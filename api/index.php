<?php

// Verifikasi dari Vercel
if (!isset($_SERVER['VERCEL']) && !isset($_SERVER['NOW'])) {
    die('Direct access not allowed');
}

// Boot Laravel
$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);