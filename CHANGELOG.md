# Changelog

## 2.0.0 - 2025-01-XX

### Major Update - Complete Beem API Integration

#### Added
- **OTP Service**: Complete OTP generation, verification, and resend functionality
  - `generateOtp()` - Generate OTP codes with customizable length, expiry, and type
  - `verifyOtp()` - Verify OTP codes with optional request ID
  - `resendOtp()` - Resend OTP codes to the same phone number
- **Airtime Service**: Full airtime management and transaction handling
  - `sendAirtime()` - Send airtime to phone numbers
  - `getAirtimeBalance()` - Check airtime account balance
  - `getTransactionHistory()` - Retrieve airtime transaction history with filters
  - `getTransactionStatus()` - Check status of specific airtime transactions
- **USSD Service**: Complete USSD menu management
  - `createMenu()` - Create new USSD menus with custom items
  - `getMenu()` - Retrieve specific menu details
  - `listMenus()` - List all USSD menus with pagination
  - `updateMenu()` - Update existing USSD menus
  - `deleteMenu()` - Delete USSD menus
  - `getSessionData()` - Get USSD session information
- **Voice Service**: Voice call functionality
  - `makeCall()` - Initiate voice calls with customizable options
  - `getCallStatus()` - Check call status and duration
  - `getCallHistory()` - Retrieve call history with filters
  - `cancelCall()` - Cancel ongoing voice calls
  - `getAvailableVoices()` - Get list of available voice options
- **Insights Service**: Analytics and reporting
  - `getMessageDeliveryReport()` - Get detailed delivery reports
  - `getMessageStatistics()` - Retrieve message statistics with date filters
  - `getAccountBalance()` - Check account balance
  - `getMessageHistory()` - Get message history with pagination and filters
  - `getFailedMessages()` - Retrieve failed message reports
  - `getErrorCodes()` - Get list of error codes and descriptions
- **Webhook Handler**: Process incoming webhooks
  - `handle()` - Process and verify incoming webhooks
  - `processDeliveryReport()` - Handle delivery report webhooks
  - `processOtpVerification()` - Handle OTP verification webhooks
  - `processAirtimeTransaction()` - Handle airtime transaction webhooks
- **Enhanced Configuration**: Comprehensive configuration options
  - Custom API endpoints for all services
  - Default settings for each service type
  - Webhook configuration with signature verification
- **Main Beem Facade**: Unified access to all services
  - `BeemFacade` class for easy access to all services
  - Quick methods for common operations
  - Service-specific access methods
- **Base API Client**: Improved HTTP client with better error handling
  - Centralized request handling
  - Consistent error responses
  - Better logging and debugging

#### Changed
- **Enhanced SMS Service**: Improved SMS functionality
  - Added encoding support (GSM7/UCS2)
  - Better error handling and response formatting
  - Updated to use new base API client
- **Updated Service Provider**: Comprehensive service registration
  - All new services registered in container
  - Facade registration for easy access
  - Improved dependency injection support
- **Configuration Structure**: Reorganized configuration
  - Service-specific endpoint configuration
  - Default settings for each service
  - Webhook configuration options

#### Improved
- **Error Handling**: Consistent error handling across all services
- **Logging**: Enhanced logging for debugging and monitoring
- **Documentation**: Comprehensive documentation and usage examples
- **Code Quality**: Better code organization and maintainability

## 1.2.1 - 2025-06-25

* Improve security, require ssl verify now

## 1.2.0 - 2025-05-28

### Major Update

- Send now returns response from Beem
- Response in Array
- Better implementation of Guzzle Client
- Updated test and mockery

## 1.1.2 - 2025-05-24

### Bug Fix

- Change Beem Class from getters to public methods

## 1.1.1 - 2025-05-24

### Bug Fix

- Fix import error on BeemServiceProvider

## 1.1.0 - 2025-05-21

### Fix a bug

- Updated the name of the service provider

## 1.0.1 - 2025-05-21

### Update ReadMe and Funding

- Update funding
- Update read me

## 1.0.0 - 2025-05-21

### [1.0.0] - 2025-05-21

#### Added

- Initial stable release with SMS support via Beem Africa.
- Laravel notification channel integration.
- Laravel 10, 11, and 12 compatibility.
- PHP 8.4 support.
- Pest-powered test suite and GitHub CI workflow.
- Config publishing support.

#### Changed

- Cleaned up boilerplate from package skeleton.
- Removed unused migration, model, and view publishing.

#### Removed

- Placeholder usage class.
