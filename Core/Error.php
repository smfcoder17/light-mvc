<?php

declare(strict_types=1);

namespace Core;

/**
 * Error class - Application error and exception handling
 * 
 * Provides centralized error handling with debug mode support,
 * logging capabilities, and custom error pages.
 * 
 * @package Core
 * @author Light-MVC
 * @version 2.0.0
 */
class Error
{
    /** @var array<int> HTTP status codes that have custom error pages */
    protected static array $errorsCode = [404];

    /**
     * Error handler - converts PHP errors to exceptions
     * Registered via set_error_handler() in application bootstrap
     * 
     * @param int $level Error level
     * @param string $message Error message
     * @param string $file File where error occurred
     * @param int $line Line number where error occurred
     * @return void
     * @throws \ErrorException Always throws exception if error_reporting is enabled
     */
    public static function errorHandler(int $level, string $message, string $file, int $line): void
    {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler - handles uncaught exceptions
     * Registered via set_exception_handler() in application bootstrap
     * Determines appropriate HTTP status and displays error page
     * 
     * @param \Throwable $exception The exception to handle
     * @return void
     */
    public static function exceptionHandler(\Throwable $exception): void
    {
        $code = $exception->getCode();
        if (!in_array($code, self::$errorsCode)) {
            $code = HttpStatus::INTERNAL_SERVER_ERROR->value;
        }

        $httpStatus = match ($code) {
            404 => HttpStatus::NOT_FOUND,
            500 => HttpStatus::INTERNAL_SERVER_ERROR,
            default => HttpStatus::INTERNAL_SERVER_ERROR
        };

        self::display($httpStatus, $exception);
    }

    /**
     * Display error page based on environment
     * In debug mode: shows detailed exception information
     * In production: logs error and shows custom error page
     * 
     * @param HttpStatus $status HTTP status code to send
     * @param \Throwable|null $exception Exception to display/log (optional)
     * @return void
     */
    public static function display(HttpStatus $status, ?\Throwable $exception = null): void
    {
        http_response_code($status->value);

        $isDebugMode = match ($_ENV['APP_DEBUG'] ?? 'true') {
            'true', '1', 'yes' => true,
            'false', '0', 'no' => false,
            default => true
        };

        if ($exception !== null && $isDebugMode) {
            $msg = "<h1>Fatal error</h1>";
            $msg .= "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            $msg .= "<p>Message: '" . $exception->getMessage() . "'</p>";
            $msg .= "<p>Stack Trace: <pre>" . $exception->getTraceAsString() . "</pre></p>";
            $msg .= "<p>Thrown in: '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
            echo $msg;
        } else {
            if ($exception !== null) {
                $logDir = ROOT . '/logs';
                if (!is_dir($logDir)) {
                    mkdir($logDir, 0755, true);
                }
                $logFile = $logDir . '/' . date('Y-m-d') . '.txt';
                $logMsg = sprintf(
                    "[%s] %s: %s in %s:%d\n",
                    date('Y-m-d H:i:s'),
                    get_class($exception),
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                );
                error_log($logMsg, 3, $logFile);
            }

            View::renderTemplate("Errors/{$status->value}.html");
        }
    }
}
