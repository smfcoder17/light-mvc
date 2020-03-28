<?php

namespace App;

use Core\Models\WebPage;
use Core\Models\Router;
use Core\Database\DB;

class App
{
     private static $router;
     private $viewPath;
     private $template = "layouts/default";
     private $errorPage404 = "404";
     private $routerMapsCallable;
     private static $instance = null;
     
     const DB_NAME = "encandb-2020";
     const DB_USER = "root";
     const DB_PASSWORD = "mysql";
     const DB_HOST = "127.0.0.1";
     private static $db;

     private static $page;

     /**
      * @param callable $routerMapConfiguration - a function to be call to configure all map routing in the website
      */
     public static function config(callable $routerMapConfiguration) : void
     {
          self::getInstance()->routerMapsCallable = $routerMapConfiguration;
          self::getInstance()->configRouter();
     }

     public static function getDB() : DB {
          if (self::$db === null) {
               self::$db = new DB(self::DB_NAME, self::DB_USER, self::DB_PASSWORD, self::DB_HOST);
           }
           return self::$db;
     }

     /**
      * @return WebPage : the object representing the current view page
      */
     public static function getWebPage() : WebPage
     {
          if (self::$page === null) {
               self::$page = new WebPage();
          }
          return self::$page;
     }

     private function __construct()
     {
          $this->viewPath = ROOT . "/App/Views/";
     }

     /**
      * @return App the unique instance of the application
      */
     private static function getInstance() : App
     {
          if (self::$instance === null) {
               self::$instance = new App();
          }
          return self::$instance;
     }

     private function configRouter()
     {
          self::$router = new Router();
          call_user_func($this->routerMapsCallable, self::$router, $this->template);

          $match = self::$router->match();
          self::$page = new WebPage();
          if (is_array($match)) {
               if (is_string($match['target'])) {
                    $params = $match['params'];
                    ob_start();
                    require("{$this->viewPath}/{$match['target']}.php");
                    self::$page->content = ob_get_clean();
                    require("{$this->viewPath}/{$this->template}.php");
               }
               else if (is_callable($match['target'])) {
                    call_user_func_array($match['target'], $match['params']);
               }
          }
          else {
               ob_start();
               require("{$this->viewPath}/{$this->errorPage404}.php");
               self::$page->content = ob_get_clean();
               require("{$this->viewPath}/{$this->template}.php");
               header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
          }
     }

     public static function getRouter()
     {
          if (self::$router === null)
               self::$router = new Router();
          return self::$router;
     }
}