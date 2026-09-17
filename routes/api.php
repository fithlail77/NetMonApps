<?php

use App\Http\Controllers\Api\AlertApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceApiController;
use App\Http\Controllers\Api\MetricApiController;
use App\Http\Controllers\Api\TopologyApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Devices
    Route::get('/devices', [DeviceApiController::class, 'index']);
    Route::get('/devices/{device}', [DeviceApiController::class, 'show']);
    Route::get('/devices/{device}/metrics', [DeviceApiController::class, 'metrics']);

    // Alerts
    Route::get('/alerts', [AlertApiController::class, 'index']);
    Route::post('/alerts/{alert}/acknowledge', [AlertApiController::class, 'acknowledge']);
    Route::post('/alerts/{alert}/resolve', [AlertApiController::class, 'resolve']);

    // Topology
    Route::get('/topology', [TopologyApiController::class, 'index']);

    // Metrics
    Route::get('/metrics', [MetricApiController::class, 'index']);
});
