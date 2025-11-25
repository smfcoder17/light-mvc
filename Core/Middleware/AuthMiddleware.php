<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
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
