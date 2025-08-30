<?php

namespace TechLegend\LaravelBeemAfrica;

use TechLegend\LaravelBeemAfrica\Airtime\BeemAirtime;
use TechLegend\LaravelBeemAfrica\Insights\BeemInsights;
use TechLegend\LaravelBeemAfrica\OTP\BeemOtp;
use TechLegend\LaravelBeemAfrica\SMS\Beem as BeemSms;
use TechLegend\LaravelBeemAfrica\USSD\BeemUssd;
use TechLegend\LaravelBeemAfrica\Voice\BeemVoice;
use TechLegend\LaravelBeemAfrica\Webhooks\BeemWebhookHandler;

class Beem
{
    protected array $config;
    protected BeemSms $sms;
    protected BeemOtp $otp;
    protected BeemAirtime $airtime;
    protected BeemUssd $ussd;
    protected BeemVoice $voice;
    protected BeemInsights $insights;
    protected BeemWebhookHandler $webhookHandler;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->initializeServices();
    }

    protected function initializeServices(): void
    {
        $this->sms = new BeemSms($this->config);
        $this->otp = new BeemOtp($this->config);
        $this->airtime = new BeemAirtime($this->config);
        $this->ussd = new BeemUssd($this->config);
        $this->voice = new BeemVoice($this->config);
        $this->insights = new BeemInsights($this->config);
        
        $webhookSecret = $this->config['webhooks']['secret'] ?? '';
        $this->webhookHandler = new BeemWebhookHandler($webhookSecret);
    }

    /**
     * Get SMS service
     */
    public function sms(): BeemSms
    {
        return $this->sms;
    }

    /**
     * Get OTP service
     */
    public function otp(): BeemOtp
    {
        return $this->otp;
    }

    /**
     * Get Airtime service
     */
    public function airtime(): BeemAirtime
    {
        return $this->airtime;
    }

    /**
     * Get USSD service
     */
    public function ussd(): BeemUssd
    {
        return $this->ussd;
    }

    /**
     * Get Voice service
     */
    public function voice(): BeemVoice
    {
        return $this->voice;
    }

    /**
     * Get Insights service
     */
    public function insights(): BeemInsights
    {
        return $this->insights;
    }

    /**
     * Get Webhook Handler
     */
    public function webhooks(): BeemWebhookHandler
    {
        return $this->webhookHandler;
    }

    /**
     * Quick SMS send method
     */
    public function sendSms(string $phoneNumber, string $message, string $sender = null): array
    {
        $smsMessage = new \TechLegend\LaravelBeemAfrica\SMS\BeemMessage($message);
        if ($sender) {
            $smsMessage->sender($sender);
        }

        $recipients = [
            [
                'recipient_id' => 0,
                'dest_addr' => $phoneNumber,
            ],
        ];

        return $this->sms->sendMessage($smsMessage, $recipients);
    }

    /**
     * Quick OTP generation method
     */
    public function generateOtp(string $phoneNumber, array $options = []): array
    {
        return $this->otp->generateOtp($phoneNumber, $options);
    }

    /**
     * Quick OTP verification method
     */
    public function verifyOtp(string $phoneNumber, string $code, string $requestId = null): array
    {
        return $this->otp->verifyOtp($phoneNumber, $code, $requestId);
    }

    /**
     * Quick airtime send method
     */
    public function sendAirtime(string $phoneNumber, float $amount, array $options = []): array
    {
        return $this->airtime->sendAirtime($phoneNumber, $amount, $options);
    }

    /**
     * Quick voice call method
     */
    public function makeCall(string $phoneNumber, string $message, array $options = []): array
    {
        return $this->voice->makeCall($phoneNumber, $message, $options);
    }

    /**
     * Get account balance
     */
    public function getBalance(): array
    {
        return $this->insights->getAccountBalance();
    }

    /**
     * Get configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}
