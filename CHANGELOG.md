# Changelog

All notable changes to `laravel-beem-africa` will be documented in this file.

## v3.0.0 - 2026-04-05

### Complete Rewrite

This is a ground-up rewrite with production-grade Laravel package architecture.

#### Added
- Laravel `Http` facade with Basic auth (replaces raw Guzzle)
- Typed readonly response DTOs (`SendSmsResponse`, `OtpResponse`, `AirtimeResponse`, `BalanceResponse`, `VoiceResponse`, `UssdResponse`, `InsightsResponse`)
- Proper exception hierarchy (`BeemAfricaAuthenticationException`, `BeemAfricaValidationException`, `BeemAfricaRequestException`) with API payload preservation
- Immutable fluent message builder (`BeemAfricaMessage`)
- Phone number normalization with optional country code prefixing
- Configurable HTTP retry logic with smart retry conditions (connection errors, 5xx, 429)
- Laravel Notifications channel (`BeemAfricaChannel` with `routeNotificationForBeemAfrica()`)
- Webhook handler with HMAC signature verification
- Spatie `PackageServiceProvider` with proper singleton registration
- Facade with full PHPDoc method signatures for IDE autocomplete
- 29 Pest tests covering all services, error handling, and integrations

#### Changed
- Config file renamed from `beem.php` to `beem-africa.php` with unified `base_url` (replaces per-service endpoint overrides)
- All services now use dependency injection via `BeemAfricaClient` (replaces inheritance from `BeemApiClient`)
- Notification channel method changed from `toBeem()` to `toBeemAfrica()` and routing from `routeNotificationFor('beem')` to `routeNotificationForBeemAfrica()`
- PHP `^8.4` required, Laravel `^11.0||^12.0||^13.0` supported
- Pest 4, PHPStan with Larastan for static analysis

#### Removed
- Raw Guzzle HTTP client dependency
- Array-based error responses (replaced by typed exceptions)
- Per-service endpoint env vars (use single `BEEM_BASE_URL` instead)

### Migration Guide

1. Publish the new config: `php artisan vendor:publish --tag="laravel-beem-africa-config" --force`
2. Update `.env`: rename endpoint vars to `BEEM_BASE_URL` (defaults to `https://apisms.beem.africa/v1`)
3. Update notification classes: `toBeem()` -> `toBeemAfrica()`, routing method -> `routeNotificationForBeemAfrica()`
4. Update error handling: catch typed exceptions instead of checking `$result['successful']`
5. Update response usage: access typed properties (`$response->requestId`) instead of array keys

## 2.0.0-beta - 2025-01-XX

### Major Update - Complete Beem API Integration

#### Added
- OTP Service: generate, verify, resend
- Airtime Service: send, balance, transaction history/status
- USSD Service: CRUD menu management, session data
- Voice Service: calls, status, history, cancel, available voices
- Insights Service: delivery reports, statistics, balance, message history, failed messages, error codes
- Webhook Handler: signature verification, delivery/OTP/airtime event processing
- Main Beem Facade with quick methods
- Base API Client with centralized error handling

#### Changed
- Enhanced SMS with encoding support
- Updated service provider with all services
- Reorganized configuration structure

## 1.2.1 - 2025-06-25

- Improve security, require SSL verify

## 1.2.0 - 2025-05-28

### Major Update
- Send now returns response from Beem
- Response in Array
- Better implementation of Guzzle Client
- Updated test and mockery

## 1.1.2 - 2025-05-24

- Change Beem Class from getters to public methods

## 1.1.1 - 2025-05-24

- Fix import error on BeemServiceProvider

## 1.1.0 - 2025-05-21

- Updated the name of the service provider

## 1.0.1 - 2025-05-21

- Update funding
- Update readme

## 1.0.0 - 2025-05-21

### Initial Release
- SMS support via Beem Africa
- Laravel notification channel integration
- Laravel 10, 11, and 12 compatibility
- PHP 8.4 support
- Pest-powered test suite and GitHub CI workflow
- Config publishing support
