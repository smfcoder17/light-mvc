<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\MiddlewareInterface;

/**
 * CsrfMiddleware - Cross-Site Request Forgery protection
 * 
 * Validates CSRF tokens for state-changing HTTP methods (POST, PUT, DELETE).
 * Prevents CSRF attacks by ensuring requests originate from the application.
 * 
 * @package Core\Middleware
 * @author Light-MVC
 * @version 2.0.0
 */
class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * Handle CSRF token validation
     * 
     * For POST, PUT, and DELETE requests, validates that the submitted
     * CSRF token matches the token stored in the session.
     * Accepts tokens from POST data or HTTP_X_CSRF_TOKEN header.
     * 
     * @param callable(): mixed $next Next middleware in the chain
     * @return mixed Result from next middleware if token is valid
     */
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

    /**
     * Generate or retrieve CSRF token
     * 
     * Creates a new CSRF token if one doesn't exist in the session.
     * Returns the existing token if already generated.
     * Use this in forms via csrf_field() helper function.
     * 
     * @return string 64-character hexadecimal CSRF token
     */
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
