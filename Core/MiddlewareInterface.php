<?php

declare(strict_types=1);

namespace Core;

/**
 * MiddlewareInterface - Contract for all middleware classes
 * 
 * Middleware classes process requests before they reach the controller
 * and/or process responses before they're sent to the client.
 * 
 * @package Core
 * @author Light-MVC
 * @version 2.0.0
 */
interface MiddlewareInterface
{
    /**
     * Handle the middleware logic
     * 
     * Must call $next() to continue the middleware chain.
     * Can modify request/response or terminate chain by not calling $next().
     * 
     * @param callable(): mixed $next Next middleware in the chain
     * @return mixed Result from the middleware chain
     */
    public function handle(callable $next): mixed;
}
