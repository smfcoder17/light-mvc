<?php

declare(strict_types=1);

namespace Core;

/**
 * RouteCollection class - Manages collection of routes
 * 
 * Provides methods for adding routes with different HTTP methods,
 * grouping routes with prefixes and middleware, and finding matching routes.
 * 
 * @package Core
 * @author Light-MVC
 * @version 2.0.0
 */
class RouteCollection
{
    /** @var Route[] Collection of all routes */
    private array $routes = [];

    /** @var array<string, Route> Named routes for URL generation */
    private array $namedRoutes = [];

    /** @var string Current group prefix */
    private string $prefix = '';

    /** @var array<string> Current group middlewares */
    private array $groupMiddlewares = [];

    /**
     * Add a route to the collection
     * Applies current group prefix and middlewares if in a group context
     * 
     * @param Route $route Route to add
     * @return Route Added route for chaining
     */
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

    /**
     * Register a GET route
     * 
     * @param string $pattern URL pattern
     * @param string $controller Controller name
     * @param string $action Action name
     * @return Route Created route
     */
    public function get(string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, ['GET']));
    }

    /**
     * Register a POST route
     * 
     * @param string $pattern URL pattern
     * @param string $controller Controller name
     * @param string $action Action name
     * @return Route Created route
     */
    public function post(string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, ['POST']));
    }

    /**
     * Register a PUT route
     * 
     * @param string $pattern URL pattern
     * @param string $controller Controller name
     * @param string $action Action name
     * @return Route Created route
     */
    public function put(string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, ['PUT']));
    }

    /**
     * Register a DELETE route
     * 
     * @param string $pattern URL pattern
     * @param string $controller Controller name
     * @param string $action Action name
     * @return Route Created route
     */
    public function delete(string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, ['DELETE']));
    }

    /**
     * Register a route that responds to any HTTP method
     * 
     * @param string $pattern URL pattern
     * @param string $controller Controller name
     * @param string $action Action name
     * @return Route Created route
     */
    public function any(string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, ['GET', 'POST', 'PUT', 'DELETE', 'PATCH']));
    }

    /**
     * Register a route with specific HTTP methods
     * 
     * @param array<string> $methods HTTP methods
     * @param string $pattern URL pattern
     * @param string $controller Controller name
     * @param string $action Action name
     * @return Route Created route
     */
    public function match(array $methods, string $pattern, string $controller, string $action): Route
    {
        return $this->add(new Route($pattern, $controller, $action, $methods));
    }

    /**
     * Group routes with shared attributes
     * 
     * @param array{prefix?: string, middleware?: string|array<string>} $attributes Group attributes
     * @param callable(RouteCollection): void $callback Callback receiving this collection
     * @return void
     */
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

    /**
     * Find a route that matches the URI and HTTP method
     * 
     * @param string $uri Request URI
     * @param string $method HTTP method
     * @return Route|null Matching route or null if not found
     */
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

    /**
     * Generate URL for a named route
     * 
     * @param string $name Route name
     * @param array<string, mixed> $params Route parameters
     * @return string Generated URL
     * @throws \RuntimeException If route not found or parameters missing
     */
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

    /**
     * Get all registered routes
     * 
     * @return Route[] Array of all routes
     */
    public function getAllRoutes(): array
    {
        return $this->routes;
    }
}
