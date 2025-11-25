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
    protected Router $router;

    /**
     * Immutable dotenv instance
     */
    protected Dotenv $dotenv;

    /**
     * Initializing App with configurations
     */
    public function init(): void
    {
        // Start session for middleware support
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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
        $routeLoader = require(APP_PATH . '/routes.php');

        if (is_callable($routeLoader)) {
            $routeLoader($this->router);
        } else {
            throw new \Exception("Routes file must return a callable", 500);
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
     * @param string $method HTTP method
     */
    public function dispatch(string $url, string $method = 'GET'): void
    {
        $this->router->dispatch($url, $method);
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}
