<?php

// ==================== LOAD AUTOLOAD ====================
require __DIR__ . '/../vendor/autoload.php';

// ==================== LOAD ENVIRONMENT ====================
$app = require_once __DIR__ . '/../bootstrap/app.php';

// ==================== BOOT LARAVEL ====================
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);