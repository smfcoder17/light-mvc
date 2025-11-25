<?php

declare(strict_types=1);

namespace Core;

class Router
{
    private RouteCollection $routes;
    private array $middlewareAliases = [];

    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->registerDefaultMiddlewares();
    }

    private function registerDefaultMiddlewares(): void
    {
        $this->middlewareAliases = [
            'auth' => Middleware\AuthMiddleware::class,
            'csrf' => Middleware\CsrfMiddleware::class,
            'rate_limit' => Middleware\RateLimitMiddleware::class,
        ];
    }

    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }

    public function dispatch(string $uri, string $method = 'GET'): void
    {
        $uri = $this->cleanUri($uri);
        $route = $this->routes->findMatch($uri, $method);

        if ($route === null) {
            throw new \Exception("No route matched.", 404);
        }

        $this->executeRoute($route);
    }

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

    private function resolveMiddleware(string $middleware): string
    {
        return $this->middlewareAliases[$middleware] ?? $middleware;
    }

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

    public function url(string $name, array $params = []): string
    {
        return $this->routes->generateUrl($name, $params);
    }
}
