<?php

declare(strict_types=1);

namespace Core\Middleware;

use Core\MiddlewareInterface;

/**
 * RateLimitMiddleware - Request rate limiting
 * 
 * Throttles requests from the same client to prevent abuse.
 * Uses session storage to track request counts per IP address.
 * 
 * @package Core\Middleware
 * @author Light-MVC
 * @version 2.0.0
 */
class RateLimitMiddleware implements MiddlewareInterface
{
    /** @var int Maximum number of requests allowed within time window */
    private int $maxRequests;
    
    /** @var int Time window in seconds */
    private int $timeWindow;

    /**
     * RateLimitMiddleware constructor
     * 
     * @param int $maxRequests Maximum requests allowed (default: 60)
     * @param int $timeWindow Time window in seconds (default: 60)
     */
    public function __construct(int $maxRequests = 60, int $timeWindow = 60)
    {
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
    }

    /**
     * Handle rate limiting logic
     * 
     * Tracks request count per client (identified by IP address).
     * Returns 429 Too Many Requests if limit exceeded.
     * Automatically resets counter after time window expires.
     * 
     * @param callable(): mixed $next Next middleware in the chain
     * @return mixed Result from next middleware if rate limit not exceeded
     */
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

    /**
     * Get client identifier for rate limiting
     * Uses IP address to identify clients
     * 
     * @return string Client identifier (IP address or 'unknown')
     */
    private function getIdentifier(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}
