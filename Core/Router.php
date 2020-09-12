<?php
namespace Core;

class Router
{

    protected $routes;
    protected $params;

    public function __construct()
    {
        
    }

    /**
     * Add a route to this Router
     * @param string $route The route url
     * @param array $params Parameters (controller, action, etc)
     */
    public function add($route, $params = [])
    {
        // Convert the route to a regular expression
        $route = preg_replace('/\//', '\\/', $route);

        // Convert varaibles to regex
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert varaibles with regex to regex
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Adding regex delimiters & case insensitive flag
        $route = '/^' . $route . '$/i';
        $this->routes[$route] = $params;
    }

    /**
     * Get all routes from this Router
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Match the url to the routes in this Router and set $this->params
     * if a route is found.
     * 
     * @param string $url The route url to match with
     * @return boolean true if a match is found, false otherwise.
     */
    public function match($url)
    {
        // $regex = "/^\/(?P<controller>[a-z-]+)\/(?P<action>[a-z-]+)$/";

        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match)
                {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
    
                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    /**
     * Dispatch the application to the passed url.
     * @param string $url url to dispatch the application to.
     */
    public function dispatch($url)
    {
        $url = $this->removeQueryStringVar($url);
        
        if ($this->match($url)) {
            $controller = Utility::toStudlyCaps($this->params['controller']);
            $controller = $this->getControllerNamespace() . $controller;

            if (class_exists($controller)) {
                $controllerObj = new $controller($this->params);
                $action = Utility::toCamelCase($this->params['action']);

                if (preg_match('/action$/i', $action) == 0) {
                    $controllerObj->$action();
                } else {
                    throw new \Exception("Method $action (in controller $controller) cannot be called directly. remove 'Action' in method");
                }
            } else {
                throw new \Exception("Controller class $controller not found");
            }
        } else {
            throw new \Exception("No route matched.", 404);
        }
    }

    /**
     * @return array parameters associated/passed with the url
     */
    public function getParams()
    {
        return $this->params;
    }

    protected function removeQueryStringVar($url, $limit = 2)
    {
        if ($url != '') {
            $parts = explode('&', $url, $limit);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }

            return $url;
        }
    }

    protected function getControllerNamespace()
    {
        $namespace = "App\Controllers\\";
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . "\\";
        }

        return $namespace;
    }
}