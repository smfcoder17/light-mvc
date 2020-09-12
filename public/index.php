<?php

use App\App;

define("ROOT", dirname(__DIR__));
require_once ROOT . "/vendor/autoload.php";

$app = new App();
$app->init();
$app->dispatch('/' . $_SERVER['QUERY_STRING']);
