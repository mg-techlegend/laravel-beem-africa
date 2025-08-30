<?php

namespace TechLegend\LaravelBeemAfrica;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \TechLegend\LaravelBeemAfrica\SMS\Beem sms()
 * @method static \TechLegend\LaravelBeemAfrica\OTP\BeemOtp otp()
 * @method static \TechLegend\LaravelBeemAfrica\Airtime\BeemAirtime airtime()
 * @method static \TechLegend\LaravelBeemAfrica\USSD\BeemUssd ussd()
 * @method static \TechLegend\LaravelBeemAfrica\Voice\BeemVoice voice()
 * @method static \TechLegend\LaravelBeemAfrica\Insights\BeemInsights insights()
 * @method static \TechLegend\LaravelBeemAfrica\Webhooks\BeemWebhookHandler webhooks()
 * @method static array sendSms(string $phoneNumber, string $message, string $sender = null)
 * @method static array generateOtp(string $phoneNumber, array $options = [])
 * @method static array verifyOtp(string $phoneNumber, string $code, string $requestId = null)
 * @method static array sendAirtime(string $phoneNumber, float $amount, array $options = [])
 * @method static array makeCall(string $phoneNumber, string $message, array $options = [])
 * @method static array getBalance()
 * @method static array getConfig()
 *
 * @see \TechLegend\LaravelBeemAfrica\Beem
 */
class BeemFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Beem::class;
    }
}
