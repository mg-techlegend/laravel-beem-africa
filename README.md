# Laravel Beem Africa

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mg-techlegend/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/mg-techlegend/laravel-beem-africa)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mg-techlegend/laravel-beem-africa/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mg-techlegend/laravel-beem-africa/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mg-techlegend/laravel-beem-africa/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mg-techlegend/laravel-beem-africa/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mg-techlegend/laravel-beem-africa.svg?style=flat-square)](https://packagist.org/packages/mg-techlegend/laravel-beem-africa)

**A Laravel package to send SMS via Beem Africa using Laravel Notifications.**

This package provides a simple, elegant way to send SMS messages using [Beem Africa](https://beem.africa)'s API. It integrates seamlessly with Laravel's native notification system, allowing you to send SMS just like Mail or Slack notifications. Currently, the package supports SMS functionality, with more Beem services (such as OTPs, voice, USSD, and messaging insights) to be added soon.

---

## 📦 Installation

You can install the package via Composer:

```bash
composer require mg-techlegend/laravel-beem-africa
````

Then, publish the config file:

```bash
php artisan vendor:publish --tag="laravel-beem-africa-config"
```

Update your `.env` file with your Beem credentials:

```env
BEEM_API_KEY=your-api-key
BEEM_SECRET_KEY=your-secret
BEEM_SENDER_NAME=your-sender-name
```

---

## 🛠 Configuration

The config file `config/beem.php` will contain:

```php
return [
    'api_key' => env('BEEM_API_KEY'),
    'secret_key' => env('BEEM_SECRET_KEY'),
    'sender_name' => env('BEEM_SENDER_NAME'),
];
```

---

## 🚀 Usage

You can send SMS via Laravel’s notification system like this:

### Create a notification:

```php
use Illuminate\Notifications\Notification;
use TechLegend\LaravelBeemAfrica\SMS\BeemMessage;

class SendSmsNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['beem'];
    }

    public function toBeem($notifiable): BeemMessage
    {
        return new BeemMessage('Your order has been shipped!');
    }
}
```

### Use in your model:

Make sure your `Notifiable` model has a `routeNotificationForBeem()` method that returns the recipient’s phone number in array.

```php
public function routeNotificationForBeem(): array
{
    return array($this->phone_number);
}
```

### Send the notification:

```php
$user->notify(new SendSmsNotification());
```

---

## ✅ Testing

```bash
composer test
```

Tests are written using [Pest](https://pestphp.com/) and run automatically via GitHub Actions on every push.

---

## ⛏ Roadmap

* [x] SMS Notifications
* [ ] OTP Support
* [ ] USSD Integration
* [ ] Message Insights
* [ ] Webhooks Support
* [ ] Voice Notifications

---

## 📄 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

---

## 🔒 Security

If you discover any security issues, please review [our security policy](../../security/policy) to report them.

---

## 👨‍💻 Credits

* [Thomson Maguru](https://github.com/tomsgad)
* [TechLegend](https://github.com/mg-techlegend)
* [All Contributors](../../contributors)

---

## 📜 License

The MIT License (MIT). Please see the [LICENSE](LICENSE.md) file for more information.
