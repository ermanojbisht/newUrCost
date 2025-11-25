<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// DEBUG: Dump request info for root URL issues
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/ukSor/') !== false && (strpos($_SERVER['REQUEST_URI'], 'index.php') === false || $_SERVER['REQUEST_URI'] == '/ukSor/')) {
   // Only dump if it's the root path we are debugging
   if ($_SERVER['REQUEST_URI'] == '/ukSor/' || $_SERVER['REQUEST_URI'] == '/ukSor') {
       echo "<pre>";
       echo "URI: " . $_SERVER['REQUEST_URI'] . "\n";
       echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
       echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";
       echo "Path Info: " . ($_SERVER['PATH_INFO'] ?? 'NOT SET') . "\n";
       echo "</pre>";
       // Don't exit, let it continue to see if it hits Laravel
   }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
