<?php

declare(strict_types=1);

namespace Core;

class RouteCollection
{
    /** @var Route[] */
    private array $routes = [];
    
    /** @var array<string, Route> */
    private array $namedRoutes = [];
    
    private string $prefix = '';
    private array $groupMiddlewares = [];

    public function add(Route $route): Route
    {
        if ($this->prefix !== '') {
            $pattern = rtrim($this->prefix, '/') . '/' . ltrim($route->getPattern(), '/');
            $route = new Route(
                $pattern,
                $route->getController(),
                $route->getAction(),
                $route->getMethods()
            );
        }

        if (!empty($this->groupMiddlewares)) {
            $route->middleware($this->groupMiddlewares);
        }

        $this->routes[] = $route;

        if ($route->getName() !== null) {
            $this->namedRoutes[$route->getName()] = $route;
        }

        return $route;
    }

    public function get(string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, ['GET']));
    }

    public function post(string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, ['POST']));
    }

    public function put(string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, ['PUT']));
    }

    public function delete(string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, ['DELETE']));
    }

    public function any(string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, ['GET', 'POST', 'PUT', 'DELETE', 'PATCH']));
    }

    public function match(array $methods, string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, $methods));
    }

    public function group(array $attributes, callable $callback): void
    {
        $previousPrefix = $this->prefix;
        $previousMiddlewares = $this->groupMiddlewares;

        if (isset($attributes['prefix'])) {
            $this->prefix = rtrim($previousPrefix, '/') . '/' . trim($attributes['prefix'], '/');
        }

        if (isset($attributes['middleware'])) {
            $middlewares = is_array($attributes['middleware']) ? $attributes['middleware'] : [$attributes['middleware']];
            $this->groupMiddlewares = array_merge($previousMiddlewares, $middlewares);
        }

        $callback($this);

        $this->prefix = $previousPrefix;
        $this->groupMiddlewares = $previousMiddlewares;
    }

    public function findMatch(string $uri, string $method): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->matchesMethod($method) && $route->matchesUri($uri)) {
                $parameters = $route->extractParameters($uri);
                $route->setParameters($parameters);
                return $route;
            }
        }

        return null;
    }

    public function generateUrl(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new \RuntimeException("Route '{$name}' not found");
        }

        $route = $this->namedRoutes[$name];
        $url = $route->getPattern();

        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', (string) $value, $url);
        }

        if (preg_match('/\{[^}]+\}/', $url)) {
            throw new \RuntimeException("Missing required parameters for route '{$name}'");
        }

        return $url;
    }

    /** @return Route[] */
    public function getAllRoutes(): array
    {
        return $this->routes;
    }
}
