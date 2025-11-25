# Migration Guide - PHP 8.2+ Modernization

## Changes Summary

This project has been successfully migrated to PHP 8.2+ with modern coding standards and best practices.

## Critical Changes Implemented

### 1. PHP 8.2+ Requirement ✅

-   Updated `composer.json` to require PHP >= 8.2
-   Updated Twig to version 3.0
-   Updated phpdotenv to version 5.6

### 2. Strict Types ✅

-   Added `declare(strict_types=1);` to all PHP files
-   All class properties now have explicit type declarations
-   All method parameters have type hints
-   All return types are explicitly declared

### 3. Modern PHP Features ✅

#### Enums

Created `Core\HttpStatus` enum for HTTP status codes:

```php
HttpStatus::OK->value          // 200
HttpStatus::NOT_FOUND->value   // 404
HttpStatus::INTERNAL_SERVER_ERROR->value // 500
```

The enum includes helper methods:

-   `isSuccess()` - Check if status is 2xx
-   `isRedirect()` - Check if status is 3xx
-   `isClientError()` - Check if status is 4xx
-   `isServerError()` - Check if status is 5xx
-   `isError()` - Check if status is 4xx or 5xx

#### Match Expressions

Replaced conditional logic with `match` expressions in `Error.php`:

```php
$httpStatus = match($code) {
    HttpStatus::NOT_FOUND->value => HttpStatus::NOT_FOUND,
    HttpStatus::INTERNAL_SERVER_ERROR->value => HttpStatus::INTERNAL_SERVER_ERROR,
    default => HttpStatus::INTERNAL_SERVER_ERROR
};
```

### 4. Environment Configuration ✅

#### .env.example File

Created template with required environment variables:

```env
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost

DB_HOST=localhost
DB_PORT=3306
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASSWORD=your_database_password

MAIL_FROM=contact@example.com
MAIL_FROM_NAME="Your App Name"
```

#### Updated .gitignore

Enhanced to ignore:

-   Environment files (.env, .env.local, etc.)
-   Vendor directory
-   Logs and cache
-   IDE files (.vscode, .idea)
-   OS files (.DS_Store, Thumbs.db)

### 5. Security Improvements ✅

-   Removed hardcoded credentials from all files
-   All database parameters now loaded from `.env`
-   Added proper null coalescing operators for environment variables
-   Enhanced XSS protection with `ENT_QUOTES` in `htmlspecialchars()`
-   Changed database charset to `utf8mb4` for full Unicode support
-   Added `PDO::ATTR_DEFAULT_FETCH_MODE` for consistency

## Files Modified

### Core Classes

-   ✅ `Core/Controller.php` - Added strict types, typed properties, return types
-   ✅ `Core/Router.php` - Full modernization with strict types
-   ✅ `Core/Model.php` - Typed properties, PDO type hints
-   ✅ `Core/View.php` - Strict types and return types
-   ✅ `Core/Error.php` - Match expressions, enum usage
-   ✅ `Core/Utility.php` - Strict types throughout
-   ✅ `Core/HttpStatus.php` - NEW: HTTP status enum

### App Classes

-   ✅ `App/App.php` - Full modernization
-   ✅ `App/routes.php` - Added strict types declaration
-   ✅ `App/Controllers/Home.php` - Modernized with strict types
-   ✅ `App/Controllers/Admin/Test.php` - Modernized
-   ✅ `App/Models/Home.php` - Typed properties and return types
-   ✅ `App/Models/Admin/Test.php` - Template structure

### Configuration & Setup

-   ✅ `composer.json` - PHP 8.2+ requirement
-   ✅ `.env.example` - Environment template
-   ✅ `.gitignore` - Enhanced exclusions
-   ✅ `public/index.php` - Strict types and null safety

## Next Steps

### 1. Install Dependencies

```bash
composer install
```

### 2. Configure Environment

```bash
# Copy the example environment file
copy .env.example .env

# Edit .env with your actual configuration
# Set database credentials, app settings, etc.
```

### 3. Create Required Directories

```bash
mkdir logs
```

### 4. Test the Application

-   Ensure PHP 8.2+ is installed
-   Run the application and verify all endpoints work
-   Check error handling in both debug and production modes

## Breaking Changes

### Type Safety

All methods now enforce strict types. Code calling these methods must:

-   Pass correct types (no more implicit conversions)
-   Handle return types properly
-   Use null coalescing for optional parameters

### Database

-   Charset changed from `utf8` to `utf8mb4`
-   All queries now default to `PDO::FETCH_ASSOC`

### Error Handling

-   `Error::exceptionHandler()` now accepts `\Throwable` instead of `\Exception`
-   HTTP status codes now use `HttpStatus` enum values

## Benefits

1. **Type Safety** - Catch errors at compile time instead of runtime
2. **Better IDE Support** - Full autocomplete and type checking
3. **Modern Standards** - Following current PHP best practices
4. **Security** - No hardcoded credentials, proper environment management
5. **Maintainability** - Clear type declarations make code easier to understand
6. **Performance** - Strict types can improve performance slightly

## Compatibility

-   **Minimum PHP Version**: 8.2
-   **Recommended PHP Version**: 8.3+
-   **Dependencies**: All updated to latest compatible versions

## Troubleshooting

### Type Errors

If you encounter type errors, ensure:

-   All function arguments match expected types
-   Return statements return correct types
-   Null values are properly handled with `?Type` or `Type|null`

### Environment Variables

If environment variables aren't loading:

-   Verify `.env` file exists in project root
-   Check file permissions
-   Ensure phpdotenv is installed via composer

### Database Connection

If database connection fails:

-   Verify `.env` database credentials
-   Ensure MySQL/MariaDB supports utf8mb4
-   Check database user permissions
