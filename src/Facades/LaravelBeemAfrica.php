<?php

namespace TechLegend\LaravelBeemAfrica\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \TechLegend\LaravelBeemAfrica\Services\BeemSms sms()
 * @method static \TechLegend\LaravelBeemAfrica\Services\BeemOtp otp()
 * @method static \TechLegend\LaravelBeemAfrica\Services\BeemAirtime airtime()
 * @method static \TechLegend\LaravelBeemAfrica\Services\BeemUssd ussd()
 * @method static \TechLegend\LaravelBeemAfrica\Services\BeemVoice voice()
 * @method static \TechLegend\LaravelBeemAfrica\Services\BeemInsights insights()
 * @method static \TechLegend\LaravelBeemAfrica\Webhooks\BeemWebhookHandler webhooks()
 * @method static \TechLegend\LaravelBeemAfrica\BeemAfricaMessage message()
 * @method static \TechLegend\LaravelBeemAfrica\Data\SendSmsResponse sendSms(string $phone, string $content, ?string $sender = null)
 * @method static \TechLegend\LaravelBeemAfrica\Data\SendSmsResponse sendSmsBulk(array $phones, string $content, ?string $sender = null)
 * @method static \TechLegend\LaravelBeemAfrica\Data\SendSmsResponse sendSmsMessage(\TechLegend\LaravelBeemAfrica\BeemAfricaMessage $message)
 * @method static \TechLegend\LaravelBeemAfrica\Data\OtpResponse generateOtp(string $phoneNumber, array $options = [])
 * @method static \TechLegend\LaravelBeemAfrica\Data\OtpResponse verifyOtp(string $phoneNumber, string $code, ?string $requestId = null)
 * @method static \TechLegend\LaravelBeemAfrica\Data\AirtimeResponse sendAirtime(string $phoneNumber, float $amount, array $options = [])
 * @method static \TechLegend\LaravelBeemAfrica\Data\VoiceResponse makeCall(string $phoneNumber, string $message, array $options = [])
 * @method static \TechLegend\LaravelBeemAfrica\Data\BalanceResponse getBalance()
 *
 * @see \TechLegend\LaravelBeemAfrica\LaravelBeemAfrica
 */
class LaravelBeemAfrica extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TechLegend\LaravelBeemAfrica\LaravelBeemAfrica::class;
    }
}
