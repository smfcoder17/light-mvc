<?php

declare(strict_types=1);

namespace Core;

class Error
{
    protected static array $errorsCode = [404];

    public static function errorHandler(int $level, string $message, string $file, int $line): void
    {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler method
     * @param \Throwable $exception the exception to be handle
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
     * Display error page
     * @param HttpStatus $status HTTP status enum
     * @param \Throwable|null $exception Optional exception for debug info
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
