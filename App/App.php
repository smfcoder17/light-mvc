<?php

namespace App;

use Core\Model;
use Core\Router;

define('APP_PATH', ROOT . '/App/');

class App
{
    /**
     * Represents the application router
     * @var Router
     */
    protected $router = null;
    /**
     * Immutable dotenv instance
     * @var \Dotenv\Dotenv;
     */
    protected $dotenv = null;

    /**
     * Initializing App with configurations
     */
    public function init()
    {
        $this->setupErrorsHandling();
        $this->initEntities();
        $this->setupRoutes();
        
        // Setting up the routes
    }

    protected function initEntities()
    {
        $this->router = new Router();

        // Loading environment variables
        $this->dotenv = \Dotenv\Dotenv::createImmutable(ROOT);
        $this->dotenv->load();

        Model::setDbParams(
            $_ENV['DB_HOST'],
            $_ENV['DB_PORT'],
            $_ENV['DB_NAME'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
        );
    }

    protected function setupRoutes()
    {
        $routes = require(APP_PATH .'/routes.php');
        if (is_array($routes)) {

            foreach ($routes as $route => $params) {
                $this->router->add($route, $params);
            }

        } else {
            throw new \Exception("Error while trying to load routes", 500);
        }
    }

    /**
     * Handles App errors and exceptions
     */
    protected function setupErrorsHandling()
    {
        error_reporting(E_ALL);
        set_error_handler('Core\Error::errorHandler');
        set_exception_handler('Core\Error::exceptionHandler');
    }

    /**
     * Dispatch the application to the passed url.
     * @param string $url url to dispatch the application to.
     */
    public function dispatch($url)
    {
        if ($this->router !== null) {
            $this->router->dispatch($url);
        }
    }
}