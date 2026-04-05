<?php

namespace TechLegend\LaravelBeemAfrica;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use TechLegend\LaravelBeemAfrica\Exceptions\BeemAfricaAuthenticationException;
use TechLegend\LaravelBeemAfrica\Exceptions\BeemAfricaRequestException;
use TechLegend\LaravelBeemAfrica\Exceptions\BeemAfricaValidationException;
use Throwable;

final class BeemAfricaClient
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $secretKey,
        private readonly string $baseUrl,
        private readonly float $timeout,
        private readonly float $connectTimeout,
        private readonly int $httpRetryAttempts,
        private readonly int $httpRetryDelayMs,
        private readonly ?string $defaultSenderName,
        private readonly PhoneNumberNormalizer $normalizer,
        private readonly array $defaults,
    ) {}

    /**
     * @param  array<string, mixed>  $config
     */
    public static function fromConfig(array $config): self
    {
        $apiKey = $config['api_key'] ?? '';
        if (! is_string($apiKey) || $apiKey === '') {
            throw new InvalidArgumentException('[Beem Africa] Missing API key. Set BEEM_API_KEY in your environment and ensure config is published (beem-africa.api_key).');
        }

        $secretKey = $config['secret_key'] ?? '';
        if (! is_string($secretKey) || $secretKey === '') {
            throw new InvalidArgumentException('[Beem Africa] Missing secret key. Set BEEM_SECRET_KEY in your environment and ensure config is published (beem-africa.secret_key).');
        }

        $rawBase = $config['base_url'] ?? 'https://apisms.beem.africa/v1';
        $baseUrl = rtrim(is_string($rawBase) ? $rawBase : 'https://apisms.beem.africa/v1', '/');

        $timeout = is_numeric($config['timeout'] ?? null) ? (float) $config['timeout'] : 30.0;
        $connectTimeout = is_numeric($config['connect_timeout'] ?? null) ? (float) $config['connect_timeout'] : 10.0;

        $sender = $config['sender_name'] ?? null;
        $defaultSenderName = is_string($sender) && trim($sender) !== '' ? trim($sender) : null;

        $cc = $config['default_country_calling_code'] ?? null;
        $countryCode = is_string($cc) && $cc !== '' ? $cc : null;

        $retryAttempts = isset($config['http_retry_attempts']) && is_numeric($config['http_retry_attempts'])
            ? max(1, (int) $config['http_retry_attempts'])
            : 1;
        $retryDelayMs = isset($config['http_retry_delay_ms']) && is_numeric($config['http_retry_delay_ms'])
            ? max(0, (int) $config['http_retry_delay_ms'])
            : 250;

        $defaults = is_array($config['defaults'] ?? null) ? $config['defaults'] : [];

        return new self(
            apiKey: $apiKey,
            secretKey: $secretKey,
            baseUrl: $baseUrl,
            timeout: $timeout,
            connectTimeout: $connectTimeout,
            httpRetryAttempts: $retryAttempts,
            httpRetryDelayMs: $retryDelayMs,
            defaultSenderName: $defaultSenderName,
            normalizer: new PhoneNumberNormalizer($countryCode),
            defaults: $defaults,
        );
    }

    public function getDefaultSenderName(): ?string
    {
        return $this->defaultSenderName;
    }

    public function getNormalizer(): PhoneNumberNormalizer
    {
        return $this->normalizer;
    }

    public function getDefault(string $service, string $key, mixed $default = null): mixed
    {
        return $this->defaults[$service][$key] ?? $default;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function post(string $path, array $payload = []): Response
    {
        return $this->pendingRequest()->post($path, $payload);
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public function get(string $path, array $query = []): Response
    {
        $url = $path;
        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return $this->pendingRequest()->get($url);
    }

    public function put(string $path, array $payload = []): Response
    {
        return $this->pendingRequest()->put($path, $payload);
    }

    public function delete(string $path): Response
    {
        return $this->pendingRequest()->delete($path);
    }

    /**
     * @param  callable(array<string, mixed>, int): object  $factory
     */
    public function handleJsonResponse(Response $response, callable $factory): object
    {
        /** @var array<string, mixed>|null $decoded */
        $decoded = $response->json();

        if (! is_array($decoded)) {
            throw new BeemAfricaRequestException(
                sprintf('[Beem Africa] Expected JSON from the API but the response was not valid JSON (HTTP %d).', $response->status()),
                ['http_status' => $response->status(), 'body' => $response->body()],
            );
        }

        $httpStatus = $response->status();

        if ($response->successful()) {
            return $factory($decoded, $httpStatus);
        }

        $rawMessage = $decoded['message'] ?? null;
        $errorMessage = is_string($rawMessage) && $rawMessage !== ''
            ? $rawMessage
            : sprintf('[Beem Africa] Request failed (HTTP %d).', $httpStatus);

        throw $this->mapFailure($httpStatus, $errorMessage, $decoded);
    }

    /**
     * @param  array<string, mixed>  $decoded
     */
    private function mapFailure(int $httpStatus, string $errorMessage, array $decoded): BeemAfricaAuthenticationException|BeemAfricaValidationException|BeemAfricaRequestException
    {
        if ($httpStatus === 401 || $httpStatus === 403) {
            return new BeemAfricaAuthenticationException($errorMessage, $decoded);
        }

        if (in_array($httpStatus, [400, 422], true)) {
            return new BeemAfricaValidationException($errorMessage, $decoded);
        }

        return new BeemAfricaRequestException($errorMessage, $decoded);
    }

    private function pendingRequest(): PendingRequest
    {
        $pending = Http::baseUrl($this->baseUrl)
            ->withBasicAuth($this->apiKey, $this->secretKey)
            ->acceptJson()
            ->asJson()
            ->timeout($this->timeout)
            ->connectTimeout($this->connectTimeout);

        if ($this->httpRetryAttempts > 1) {
            $pending->retry(
                $this->httpRetryAttempts,
                $this->httpRetryDelayMs,
                fn (?Throwable $exception, PendingRequest $request): bool => $this->shouldRetryHttpAttempt($exception),
                throw: false,
            );
        }

        return $pending;
    }

    private function shouldRetryHttpAttempt(?Throwable $exception): bool
    {
        if ($exception === null) {
            return false;
        }

        if ($exception instanceof ConnectionException) {
            return true;
        }

        if ($exception instanceof RequestException && $exception->response !== null) {
            $status = $exception->response->status();

            return in_array($status, [408, 425, 429, 500, 502, 503, 504], true);
        }

        return false;
    }
}
