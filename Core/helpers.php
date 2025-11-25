<?php

declare(strict_types=1);

/**
 * Helper functions for routing and views
 */

if (!function_exists('route')) {
    /**
     * Generate a URL for a named route
     * 
     * @param string $name Route name
     * @param array $params Route parameters
     * @return string Generated URL
     */
    function route(string $name, array $params = []): string
    {
        global $app;
        
        if (!isset($app)) {
            throw new \RuntimeException('Application instance not found');
        }
        
        return $app->getRouter()->url($name, $params);
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate a hidden CSRF token field for forms
     * 
     * @return string HTML input field with CSRF token
     */
    function csrf_field(): string
    {
        $token = \Core\Middleware\CsrfMiddleware::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get the CSRF token value
     * 
     * @return string CSRF token
     */
    function csrf_token(): string
    {
        return \Core\Middleware\CsrfMiddleware::generateToken();
    }
}

if (!function_exists('old')) {
    /**
     * Retrieve old input value
     * 
     * @param string $key Input name
     * @param mixed $default Default value if not found
     * @return mixed Old input value
     */
    function old(string $key, mixed $default = ''): mixed
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['_old_input'][$key] ?? $default;
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL
     * 
     * @param string $url URL to redirect to
     * @param int $code HTTP status code
     * @return never
     */
    function redirect(string $url, int $code = 302): never
    {
        header("Location: $url", true, $code);
        exit;
    }
}

if (!function_exists('back')) {
    /**
     * Redirect back to the previous page
     * 
     * @param string $default Default URL if referer not found
     * @return never
     */
    function back(string $default = '/'): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? $default;
        redirect($referer);
    }
}
