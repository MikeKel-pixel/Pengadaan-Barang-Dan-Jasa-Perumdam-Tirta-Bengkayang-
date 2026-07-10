<?php

// Load autoload
require __DIR__ . '/../vendor/autoload.php';

// Load environment
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot Laravel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);