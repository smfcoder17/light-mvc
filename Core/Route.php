<?php

declare(strict_types=1);

namespace Core;

/**
 * Route class - Represents a single route in the application
 * 
 * Manages route pattern matching, parameters extraction, middleware assignment,
 * and HTTP method validation for individual routes.
 * 
 * @package Core
 * @author Light-MVC
 * @version 2.0.0
 */
class Route
{
    /** @var string Route URL pattern (e.g., '/user/{id}') */
    private string $pattern;

    /** @var string Controller class name */
    private string $controller;

    /** @var string Controller action/method name */
    private string $action;

    /** @var array<string> Allowed HTTP methods for this route */
    private array $methods;

    /** @var array<string, mixed> Extracted route parameters */
    private array $parameters = [];

    /** @var array<string> Middleware aliases applied to this route */
    private array $middlewares = [];

    /** @var string|null Optional route name for URL generation */
    private ?string $name = null;

    /**
     * Route constructor
     * 
     * @param string $pattern URL pattern with optional parameters in curly braces
     * @param string $controller Controller class name (without namespace)
     * @param string $action Action method name (without 'Action' suffix)
     * @param array<string> $methods HTTP methods allowed for this route
     */
    public function __construct(
        string $pattern,
        string $controller,
        string $action,
        array $methods = ['GET']
    ) {
        $this->pattern = $pattern;
        $this->controller = $controller;
        $this->action = $action;
        $this->methods = array_map('strtoupper', $methods);
    }

    /**
     * Get the route URL pattern
     * 
     * @return string Route pattern
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Get the controller class name
     * 
     * @return string Controller name
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * Get the action method name
     * 
     * @return string Action name
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Get allowed HTTP methods
     * 
     * @return array<string> HTTP methods
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Get extracted route parameters
     * 
     * @return array<string, mixed> Route parameters
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Set route parameters
     * 
     * @param array<string, mixed> $parameters Route parameters
     * @return self Fluent interface
     */
    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Get applied middlewares
     * 
     * @return array<string> Middleware aliases
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Add middleware(s) to this route
     * 
     * @param string|array<string> $middleware Middleware alias or array of aliases
     * @return self Fluent interface
     */
    public function middleware(string|array $middleware): self
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        return $this;
    }

    /**
     * Set route name for URL generation
     * 
     * @param string $name Route name
     * @return self Fluent interface
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get route name
     * 
     * @return string|null Route name or null if not set
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Check if route matches the given HTTP method
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @return bool True if method is allowed
     */
    public function matchesMethod(string $method): bool
    {
        return in_array(strtoupper($method), $this->methods, true);
    }

    /**
     * Check if route matches the given URI
     * 
     * @param string $uri Request URI to match
     * @return bool True if URI matches route pattern
     */
    public function matchesUri(string $uri): bool
    {
        $regex = $this->buildRegexPattern();
        return (bool) preg_match($regex, $uri);
    }

    /**
     * Extract parameters from URI using route pattern
     * 
     * @param string $uri Request URI
     * @return array<string, string> Extracted parameters
     */
    public function extractParameters(string $uri): array
    {
        $regex = $this->buildRegexPattern();

        if (!preg_match($regex, $uri, $matches)) {
            return [];
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    /**
     * Build regex pattern from route pattern
     * Converts {param} to named capture groups
     * 
     * @return string Regex pattern
     */
    private function buildRegexPattern(): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $this->pattern);
        return '#^' . $pattern . '$#';
    }
}
