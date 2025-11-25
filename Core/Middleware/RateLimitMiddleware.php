<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\MiddlewareInterface;

class RateLimitMiddleware implements MiddlewareInterface
{
    private int $maxRequests;
    private int $timeWindow;

    public function __construct(int $maxRequests = 60, int $timeWindow = 60)
    {
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
    }

    public function handle(callable $next): mixed
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $identifier = $this->getIdentifier();
        $key = 'rate_limit_' . $identifier;

        $current = $_SESSION[$key] ?? ['count' => 0, 'reset_at' => time() + $this->timeWindow];

        if (time() > $current['reset_at']) {
            $current = ['count' => 0, 'reset_at' => time() + $this->timeWindow];
        }

        $current['count']++;
        $_SESSION[$key] = $current;

        if ($current['count'] > $this->maxRequests) {
            http_response_code(429);
            header('Retry-After: ' . ($current['reset_at'] - time()));
            echo 'Too Many Requests';
            exit;
        }

        return $next();
    }

    private function getIdentifier(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
