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

        http_response_code($httpStatus->value);

        $msg = "<h1>Fatal error</h1>";
        $msg .= "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
        $msg .= "<p>Message: '" . $exception->getMessage() . "'</p>";
        $msg .= "<p>Stack Trace: <pre>" . $exception->getTraceAsString() . "</pre></p>";
        $msg .= "<p>Thrown in: '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";

        $isDebugMode = match ($_ENV['APP_DEBUG'] ?? 'true') {
            'true', '1', 'yes' => true,
            'false', '0', 'no' => false,
            default => true
        };

        if (!$isDebugMode) {
            $logDir = ROOT . '/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . '/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $logFile);
            error_log($msg);
            View::renderTemplate("Errors/{$httpStatus->value}.html");
        } else {
            echo $msg;
        }
    }
}
