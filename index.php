<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';  // Caminho do autoload foi ajustado para a raiz

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__.'/bootstrap/app.php'; // Caminho do app.php foi ajustado para a raiz

// Handle the incoming request
$request = Request::capture();
$app->handle($request);
