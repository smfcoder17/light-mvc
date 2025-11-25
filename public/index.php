<?php

declare(strict_types=1);

use App\App;

define("ROOT", dirname(__DIR__));
require_once ROOT . "/vendor/autoload.php";
require_once ROOT . "/Core/helpers.php";

$app = new App();
$app->init();

// Get request URI and method
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Dispatch the application
$app->dispatch($uri, $method);
