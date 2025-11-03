<?php
// strip folder prefix so Laravel sees clean URI
$_SERVER['REQUEST_URI'] = preg_replace('#^/waitinglist#', '', $_SERVER['REQUEST_URI']);

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Strip folder prefix so Laravel sees clean URI
$_SERVER['REQUEST_URI'] = preg_replace('#^/waitinglist#', '', $_SERVER['REQUEST_URI']);

// Maintenance mode check
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoload
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->handleRequest(Request::capture());