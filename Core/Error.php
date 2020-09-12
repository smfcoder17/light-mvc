<?php

namespace Core;

class Error
{
    protected static $errorsCode = [
        404
    ];

    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Exception handler method
     * @param \Exception $exception the exception to be handle
     * @return void
     */
    public static function exceptionHandler($exception)
    {
        $code = $exception->getCode();
        if (!in_array($code, self::$errorsCode)) {
            $code = 500;
        }
        http_response_code($code);

        $msg = "<h1>Fatal error</h1>";
        $msg .= "<p>Uncaugt exception: '". get_class($exception) ."'</p>";
        $msg .= "<p>Message: '". $exception->getMessage() ."'</p>";
        $msg .= "<p>Stack Trace: <pre>". $exception->getTraceAsString() ."</pre></p>";
        $msg .= "<p>Thrown in: '". $exception->getFile() ."' on line ". $exception->getLine() ."</p>";

        if (isset($_ENV['APP_DEBUG']) && ($_ENV['APP_DEBUG'] === 'false') ?? false) {
            $logFile = ROOT .'/logs/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $logFile);
            error_log($msg);
            View::renderTemplate("Errors/$code.html");
        } else {
            echo $msg;
        }
    }
}