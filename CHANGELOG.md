# Changelog

All notable changes to `laravel-beem-africa` will be documented in this file.

## v1.0.0 - 2026-04-05

### Added
- SMS messaging: single and bulk send with fluent message builder
- OTP service: generate, verify, and resend one-time passwords
- Airtime: send airtime, check balance, transaction history
- USSD: create, update, list, and delete USSD menus
- Voice: make calls, check status, cancel, list available voices
- Insights: delivery reports, message statistics, account balance, message history
- Webhook handler with HMAC signature verification
- Laravel Notifications channel integration (`BeemAfricaChannel`)
- Typed readonly response DTOs for all API responses
- Proper exception hierarchy (Authentication, Validation, Request)
- Phone number normalization with optional country code prefixing
- HTTP retry logic with configurable attempts and delay
- Laravel Http facade with Basic auth (no raw Guzzle)
- Comprehensive Pest test suite
