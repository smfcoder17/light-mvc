#!/bin/bash

# Setup script for Light-MVC PHP 8.2+ Migration

echo "========================================"
echo "Light-MVC PHP 8.2+ Setup"
echo "========================================"
echo ""

# Check PHP version
echo "Checking PHP version..."
PHP_VERSION=$(php -r 'echo PHP_VERSION;')
PHP_MAJOR=$(php -r 'echo PHP_MAJOR_VERSION;')
PHP_MINOR=$(php -r 'echo PHP_MINOR_VERSION;')

if [ "$PHP_MAJOR" -lt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 2 ]); then
    echo "ERROR: PHP 8.2 or higher is required!"
    echo "Current version: $PHP_VERSION"
    exit 1
fi
echo "PHP version $PHP_VERSION is compatible!"
echo ""

# Check if composer is installed
echo "Checking for Composer..."
if ! command -v composer &> /dev/null; then
    echo "ERROR: Composer is not installed or not in PATH!"
    echo "Please install Composer from https://getcomposer.org/"
    exit 1
fi
echo "Composer found!"
echo ""

# Install dependencies
echo "Installing Composer dependencies..."
composer install
if [ $? -ne 0 ]; then
    echo "ERROR: Failed to install dependencies!"
    exit 1
fi
echo "Dependencies installed successfully!"
echo ""

# Copy .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
    echo ""
    echo "IMPORTANT: Please edit .env and configure your database settings!"
    echo ""
else
    echo ".env file already exists, skipping..."
fi

# Create logs directory
if [ ! -d logs ]; then
    echo "Creating logs directory..."
    mkdir -p logs
    chmod 755 logs
    echo "Logs directory created!"
else
    echo "Logs directory already exists!"
fi
echo ""

echo "========================================"
echo "Setup Complete!"
echo "========================================"
echo ""
echo "Next steps:"
echo "1. Edit .env file with your database credentials"
echo "2. Configure your web server to point to the 'public' directory"
echo "3. Ensure mod_rewrite is enabled (Apache) or configure URL rewriting"
echo "4. Access your application via the configured URL"
echo ""
echo "For more information, see MIGRATION_GUIDE.md"
echo ""
