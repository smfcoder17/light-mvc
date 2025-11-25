<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\MiddlewareInterface;

/**
 * AuthMiddleware - Authentication verification middleware
 * 
 * Ensures that a user is authenticated before accessing protected routes.
 * Redirects unauthenticated users to the login page.
 * 
 * @package Core\Middleware
 * @author Light-MVC
 * @version 2.0.0
 */
class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Handle authentication verification
     * 
     * Checks if user_id exists in session. If not authenticated,
     * redirects to /login and terminates execution.
     * 
     * @param callable(): mixed $next Next middleware in the chain
     * @return mixed Result from next middleware if authenticated
     */
    public function handle(callable $next): mixed
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        return $next();
    }
}
