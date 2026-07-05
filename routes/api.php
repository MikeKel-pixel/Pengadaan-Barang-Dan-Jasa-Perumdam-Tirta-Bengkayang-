<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "api" middleware group.
|
*/

// ==================== API PUBLIC (Tanpa Auth) ====================
Route::get('/', [ApiController::class, 'index']);
Route::get('/procurements', [ApiController::class, 'getProcurements']);
Route::get('/procurements/{id}', [ApiController::class, 'getProcurementById']);
Route::get('/vendors', [ApiController::class, 'getVendors']);
Route::get('/vendors/{id}', [ApiController::class, 'getVendorById']);
Route::get('/categories', [ApiController::class, 'getCategories']);
Route::get('/items', [ApiController::class, 'getItems']);
Route::get('/items/{id}', [ApiController::class, 'getItemById']);
Route::get('/statistics', [ApiController::class, 'getStatistics']);
Route::get('/status', [ApiController::class, 'getStatusList']);

// ==================== API AUTHENTICATION (Opsional) ====================
Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);