<?php

declare(strict_types=1);

namespace App;

use Core\Model;
use Core\Router;
use Dotenv\Dotenv;

define('APP_PATH', ROOT . '/App/');

class App
{
    /**
     * Represents the application router
     */
    protected ?Router $router = null;

    /**
     * Immutable dotenv instance
     */
    protected ?Dotenv $dotenv = null;

    /**
     * Initializing App with configurations
     */
    public function init(): void
    {
        $this->setupErrorsHandling();
        $this->initEntities();
        $this->setupRoutes();
    }

    protected function initEntities(): void
    {
        $this->router = new Router();

        // Loading environment variables
        $this->dotenv = Dotenv::createImmutable(ROOT);
        $this->dotenv->load();

        Model::setDbParams(
            $_ENV['DB_HOST'] ?? '',
            $_ENV['DB_PORT'] ?? '3306',
            $_ENV['DB_NAME'] ?? '',
            $_ENV['DB_USER'] ?? '',
            $_ENV['DB_PASSWORD'] ?? ''
        );
    }

    protected function setupRoutes(): void
    {
        $routes = require(APP_PATH . '/routes.php');
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
    protected function setupErrorsHandling(): void
    {
        error_reporting(E_ALL);
        set_error_handler('Core\Error::errorHandler');
        set_exception_handler('Core\Error::exceptionHandler');
    }

    /**
     * Dispatch the application to the passed url.
     * @param string $url url to dispatch the application to.
     */
    public function dispatch(string $url): void
    {
        if ($this->router !== null) {
            $this->router->dispatch($url);
        }
    }
}
