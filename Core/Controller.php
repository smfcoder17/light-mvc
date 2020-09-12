<?php

namespace Core;

abstract class Controller
{
    protected $routeParams = [];

    public function __construct($routeParams)
    {
        $this->routeParams = $routeParams;
    }

    public function __call($name, $arguments)
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
    protected function before()
    {
        return true;
    }

    /**
     * After filter - called after an action methods
     */
    protected function after()
    {}
}