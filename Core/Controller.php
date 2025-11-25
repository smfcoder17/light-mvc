<?php

declare(strict_types=1);

namespace Core;

/**
 * Controller abstract class - Base controller for all application controllers
 * 
 * Provides route parameter access and lifecycle hooks (before/after filters).
 * All application controllers should extend this class.
 * 
 * @package Core
 * @author Light-MVC
 * @version 2.0.0
 */
abstract class Controller
{
    /** @var array<string, mixed> Route parameters extracted from URL */
    protected array $routeParams = [];

    /**
     * Controller constructor
     * 
     * @param array<string, mixed> $routeParams Parameters extracted from route
     */
    public function __construct(array $routeParams)
    {
        $this->routeParams = $routeParams;
    }

    /**
     * Magic method to handle action calls
     * Automatically appends 'Action' suffix and runs before/after filters
     * 
     * @param string $name Method name
     * @param array<mixed> $arguments Method arguments
     * @return void
     */
    public function __call(string $name, array $arguments): void
    {
        $name = $name . "Action";

        if (method_exists($this, $name)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $name], $arguments);
                $this->after();
            }
        }
    }

    /**
     * Before filter - called before action method execution
     * Override in child controllers to add logic before actions
     * Return false to prevent action execution
     * 
     * @return mixed Return false to prevent action execution
     */
    protected function before(): mixed
    {
        return true;
    }

    /**
     * After filter - called after action method execution
     * Override in child controllers to add logic after actions
     * 
     * @return void
     */
    protected function after(): void {}
}
