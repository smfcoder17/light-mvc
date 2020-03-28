<?php

// Do not modify
use App\App;
use Core\Models\Router;
use Symfony\Component\VarDumper\VarDumper;

require "../vendor/autoload.php";
define("ROOT", dirname(__DIR__));
define("VIEWS_PATH", ROOT . '/App/Views');

/**
* a callback function to be called to configure all map routing in the website
* map all your pages here
* more details for the router on https://altorouter.com/
*/
$mapRouting = function(Router $router, string $template) {
     
     $router->map('GET', '/', function() {
          require VIEWS_PATH . '/index.php';
     }, 'home');

     $router->get('/post/[*:slug]-[i:id]', 'article');
};

App::config($mapRouting);

