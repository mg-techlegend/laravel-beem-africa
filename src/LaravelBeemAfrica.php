<?php

namespace TechLegend\LaravelBeemAfrica;

use TechLegend\LaravelBeemAfrica\Data\AirtimeResponse;
use TechLegend\LaravelBeemAfrica\Data\BalanceResponse;
use TechLegend\LaravelBeemAfrica\Data\InsightsResponse;
use TechLegend\LaravelBeemAfrica\Data\OtpResponse;
use TechLegend\LaravelBeemAfrica\Data\SendSmsResponse;
use TechLegend\LaravelBeemAfrica\Data\UssdResponse;
use TechLegend\LaravelBeemAfrica\Data\VoiceResponse;
use TechLegend\LaravelBeemAfrica\Services\BeemAirtime;
use TechLegend\LaravelBeemAfrica\Services\BeemInsights;
use TechLegend\LaravelBeemAfrica\Services\BeemOtp;
use TechLegend\LaravelBeemAfrica\Services\BeemSms;
use TechLegend\LaravelBeemAfrica\Services\BeemUssd;
use TechLegend\LaravelBeemAfrica\Services\BeemVoice;
use TechLegend\LaravelBeemAfrica\Webhooks\BeemWebhookHandler;

final class LaravelBeemAfrica
{
    private BeemSms $smsService;

    private BeemOtp $otpService;

    private BeemAirtime $airtimeService;

    private BeemUssd $ussdService;

    private BeemVoice $voiceService;

    private BeemInsights $insightsService;

    public function __construct(
        private readonly BeemAfricaClient $client,
        private readonly BeemWebhookHandler $webhookHandler,
    ) {
        $this->smsService = new BeemSms($this->client);
        $this->otpService = new BeemOtp($this->client);
        $this->airtimeService = new BeemAirtime($this->client);
        $this->ussdService = new BeemUssd($this->client);
        $this->voiceService = new BeemVoice($this->client);
        $this->insightsService = new BeemInsights($this->client);
    }

    // ── Service Accessors ────────────────────────────────────────────

    public function sms(): BeemSms
    {
        return $this->smsService;
    }

    public function otp(): BeemOtp
    {
        return $this->otpService;
    }

    public function airtime(): BeemAirtime
    {
        return $this->airtimeService;
    }

    public function ussd(): BeemUssd
    {
        return $this->ussdService;
    }

    public function voice(): BeemVoice
    {
        return $this->voiceService;
    }

    public function insights(): BeemInsights
    {
        return $this->insightsService;
    }

    public function webhooks(): BeemWebhookHandler
    {
        return $this->webhookHandler;
    }

    // ── Message Builder ──────────────────────────────────────────────

    public function message(): BeemAfricaMessage
    {
        return BeemAfricaMessage::make();
    }

    // ── Quick Methods ────────────────────────────────────────────────

    public function sendSms(string $phone, string $content, ?string $sender = null): SendSmsResponse
    {
        return $this->smsService->send($phone, $content, $sender);
    }

    public function sendSmsBulk(array $phones, string $content, ?string $sender = null): SendSmsResponse
    {
        return $this->smsService->sendBulk($phones, $content, $sender);
    }

    public function sendSmsMessage(BeemAfricaMessage $message): SendSmsResponse
    {
        return $this->smsService->sendMessage($message);
    }

    public function generateOtp(string $phoneNumber, array $options = []): OtpResponse
    {
        return $this->otpService->generate($phoneNumber, $options);
    }

    public function verifyOtp(string $phoneNumber, string $code, ?string $requestId = null): OtpResponse
    {
        return $this->otpService->verify($phoneNumber, $code, $requestId);
    }

    public function sendAirtime(string $phoneNumber, float $amount, array $options = []): AirtimeResponse
    {
        return $this->airtimeService->send($phoneNumber, $amount, $options);
    }

    public function makeCall(string $phoneNumber, string $message, array $options = []): VoiceResponse
    {
        return $this->voiceService->makeCall($phoneNumber, $message, $options);
    }

    public function getBalance(): BalanceResponse
    {
        return $this->insightsService->getAccountBalance();
    }
}
