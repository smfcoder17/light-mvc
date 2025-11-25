<?php

declare(strict_types=1);

namespace Core;

class Route
{
    private string $pattern;
    private string $controller;
    private string $action;
    private array $methods;
    private array $parameters = [];
    private array $middlewares = [];
    private ?string $name = null;

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

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function middleware(string|array $middleware): self
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function matchesMethod(string $method): bool
    {
        return in_array(strtoupper($method), $this->methods, true);
    }

    public function matchesUri(string $uri): bool
    {
        $regex = $this->buildRegexPattern();
        return (bool) preg_match($regex, $uri);
    }

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

    private function buildRegexPattern(): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $this->pattern);
        return '#^' . $pattern . '$#';
    }
}
