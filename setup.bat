@echo off
REM Setup script for Light-MVC PHP 8.2+ Migration

echo ========================================
echo Light-MVC PHP 8.2+ Setup
echo ========================================
echo.

REM Check PHP version
echo Checking PHP version...
php -v | findstr /R "PHP 8\.\([2-9]\|[1-9][0-9]\)"
if errorlevel 1 (
    echo ERROR: PHP 8.2 or higher is required!
    echo Current version:
    php -v
    pause
    exit /b 1
)
echo PHP version is compatible!
echo.

REM Check if composer is installed
echo Checking for Composer...
where composer >nul 2>nul
if errorlevel 1 (
    echo ERROR: Composer is not installed or not in PATH!
    echo Please install Composer from https://getcomposer.org/
    pause
    exit /b 1
)
echo Composer found!
echo.

REM Install dependencies
echo Installing Composer dependencies...
composer install
if errorlevel 1 (
    echo ERROR: Failed to install dependencies!
    pause
    exit /b 1
)
echo Dependencies installed successfully!
echo.

REM Copy .env file if it doesn't exist
if not exist .env (
    echo Creating .env file from .env.example...
    copy .env.example .env
    echo.
    echo IMPORTANT: Please edit .env and configure your database settings!
    echo.
) else (
    echo .env file already exists, skipping...
)

REM Create logs directory
if not exist logs (
    echo Creating logs directory...
    mkdir logs
    echo Logs directory created!
) else (
    echo Logs directory already exists!
)
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Edit .env file with your database credentials
echo 2. Configure your web server to point to the 'public' directory
echo 3. Ensure mod_rewrite is enabled (Apache) or configure URL rewriting
echo 4. Access your application via the configured URL
echo.
echo For more information, see MIGRATION_GUIDE.md
echo.
pause
