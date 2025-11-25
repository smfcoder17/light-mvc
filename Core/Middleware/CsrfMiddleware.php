<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\MiddlewareInterface;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(callable $next): mixed
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            $sessionToken = $_SESSION['csrf_token'] ?? '';

            if (!hash_equals($sessionToken, $token)) {
                http_response_code(403);
                echo 'CSRF token mismatch';
                exit;
            }
        }

        return $next();
    }

    public static function generateToken(): string
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}
