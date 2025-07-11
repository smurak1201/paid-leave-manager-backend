<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
// if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
if (file_exists($maintenance = __DIR__ . '/paid-leave-manager/backend/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
// require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/paid-leave-manager/backend/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
// $app = require_once __DIR__ . '/../bootstrap/app.php';
$app = require_once __DIR__ . '/paid-leave-manager/backend/bootstrap/app.php';

$app->handleRequest(Request::capture());
