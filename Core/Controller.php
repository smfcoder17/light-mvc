<?php

declare(strict_types=1);

namespace Core;

abstract class Controller
{
    protected array $routeParams = [];

    public function __construct(array $routeParams)
    {
        $this->routeParams = $routeParams;
    }

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
     * Before filter - called before an action methods
     */
    protected function before(): mixed
    {
        return true;
    }

    /**
     * After filter - called after an action methods
     */
    protected function after(): void {}
}
