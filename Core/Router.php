<?php

declare(strict_types=1);

namespace Core;

/**
 * Router class - Main routing dispatcher
 * 
 * Handles route dispatching, middleware execution, and controller invocation.
 * Manages route collection and middleware aliases.
 * 
 * @package Core
 * @author Light-MVC
 * @version 2.0.0
 */
class Router
{
    /** @var RouteCollection Collection of all application routes */
    private RouteCollection $routes;
    
    /** @var array<string, class-string> Middleware alias to class mapping */
    private array $middlewareAliases = [];

    /**
     * Router constructor
     * Initializes route collection and registers default middleware
     */
    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->registerDefaultMiddlewares();
    }

    /**
     * Register default middleware aliases
     * Maps short names to fully qualified middleware class names
     * 
     * @return void
     */
    private function registerDefaultMiddlewares(): void
    {
        $this->middlewareAliases = [
            'auth' => Middleware\AuthMiddleware::class,
            'csrf' => Middleware\CsrfMiddleware::class,
            'rate_limit' => Middleware\RateLimitMiddleware::class,
        ];
    }

    /**
     * Get the route collection
     * 
     * @return RouteCollection Route collection instance
     */
    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }

    /**
     * Dispatch request to matching route
     * 
     * @param string $uri Request URI
     * @param string $method HTTP method (default: GET)
     * @return void
     * @throws \Exception If no route matches
     */
    public function dispatch(string $uri, string $method = 'GET'): void
    {
        $uri = $this->cleanUri($uri);
        $route = $this->routes->findMatch($uri, $method);

        if ($route === null) {
            throw new \Exception("No route matched.", 404);
        }

        $this->executeRoute($route);
    }

    /**
     * Execute a matched route
     * Instantiates controller and invokes action through middleware chain
     * 
     * @param Route $route Matched route
     * @return void
     * @throws \Exception If controller or action not found
     */
    private function executeRoute(Route $route): void
    {
        $controller = 'App\\Controllers\\' . $route->getController();
        $action = $route->getAction() . 'Action';

        if (!class_exists($controller)) {
            throw new \Exception("Controller class $controller not found");
        }

        $controllerInstance = new $controller($route->getParameters());

        if (!method_exists($controllerInstance, $action)) {
            throw new \Exception("Method $action not found in controller $controller");
        }

        $this->runMiddlewares($route->getMiddlewares(), function () use ($controllerInstance, $action): void {
            $controllerInstance->$action();
        });
    }

    /**
     * Run middleware chain
     * Builds an onion-like middleware stack and executes it
     * 
     * @param array<string> $middlewares Middleware aliases
     * @param callable(): void $finalAction Final action to execute
     * @return void
     */
    private function runMiddlewares(array $middlewares, callable $finalAction): void
    {
        $chain = $finalAction;

        foreach (array_reverse($middlewares) as $middleware) {
            $middlewareClass = $this->resolveMiddleware($middleware);
            $middlewareInstance = new $middlewareClass();

            $chain = fn() => $middlewareInstance->handle($chain);
        }

        $chain();
    }

    /**
     * Resolve middleware alias to fully qualified class name
     * 
     * @param string $middleware Middleware alias or class name
     * @return class-string Fully qualified middleware class name
     */
    private function resolveMiddleware(string $middleware): string
    {
        return $this->middlewareAliases[$middleware] ?? $middleware;
    }

    /**
     * Clean and normalize URI
     * Removes query string and normalizes slashes
     * 
     * @param string $uri Raw URI
     * @return string Cleaned URI
     */
    private function cleanUri(string $uri): string
    {
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        // Parse URL and get path
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        return '/' . trim($path, '/');
    }

    /**
     * Generate URL for named route
     * 
     * @param string $name Route name
     * @param array<string, mixed> $params Route parameters
     * @return string Generated URL
     */
    public function url(string $name, array $params = []): string
    {
        return $this->routes->generateUrl($name, $params);
    }
}
