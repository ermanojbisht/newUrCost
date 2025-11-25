<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$request = Request::capture();

// DEBUG: Dump Laravel Request Path
if (isset($_SERVER['REQUEST_URI']) && ($_SERVER['REQUEST_URI'] == '/ukSor/' || $_SERVER['REQUEST_URI'] == '/ukSor')) {
    echo "<pre>";
    echo "Laravel Path: " . $request->path() . "\n";
    echo "Laravel URL: " . $request->url() . "\n";
    echo "Request URI: " . $request->getRequestUri() . "\n";
    echo "Base URL: " . $request->getBaseUrl() . "\n";
    echo "Base Path: " . $request->getBasePath() . "\n";
    echo "</pre>";
}

$app->handleRequest($request);
