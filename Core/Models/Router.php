<?php
namespace Core\Models;

use AltoRouter;

class Router
{
     /**
      * @var AltoRouter
      */
     private $router;

     public function __construct()
     {
          $this->router = new AltoRouter();
     }

     public function get(string $url, string $view, ?string $name = null): self
     {
          $this->router->map('GET', $url, $view, $name);
          return $this;
     }

     public function post(string $url, string $view, ?string $name = null): self
     {
          $this->router->map('POST', $url, $view, $name);
          return $this;
     }

     public function getPost(string $url, string $view, ?string $name = null): self
     {
          $this->router->map('POST|GET', $url, $view, $name);
          return $this;
     }

     public function url(string $name, $params = [])
     {
          return $this->router->generate($name, $params);
     }

     public function map($method, $route, $target, $name = null)
     {
          $this->router->map($method, $route, $target, $name);
     }

     public function match($requestUrl = null, $requestMethod = null)
     {
          return $this->router->match($requestUrl, $requestMethod);
     }
}