<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\MetricController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SlaController;
use App\Http\Controllers\TopologyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/devices', [DashboardController::class, 'deviceList'])->name('dashboard.devices');

    // Devices
    Route::resource('devices', DeviceController::class);

    // Alerts
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('/alerts/{alert}/acknowledge', [AlertController::class, 'acknowledge'])->name('alerts.acknowledge');
    Route::post('/alerts/{alert}/resolve', [AlertController::class, 'resolve'])->name('alerts.resolve');

    // Topology
    Route::get('/topology', [TopologyController::class, 'index'])->name('topology.index');
    Route::get('/topology/data', [TopologyController::class, 'data'])->name('topology.data');
    Route::post('/topology/node', [TopologyController::class, 'storeNode'])->name('topology.node.store');
    Route::post('/topology/edge', [TopologyController::class, 'storeEdge'])->name('topology.edge.store');
    Route::put('/topology/node/{node}', [TopologyController::class, 'updateNode'])->name('topology.node.update');
    Route::delete('/topology/node/{node}', [TopologyController::class, 'destroyNode'])->name('topology.node.destroy');
    Route::delete('/topology/edge/{edge}', [TopologyController::class, 'destroyEdge'])->name('topology.edge.destroy');

    // Metrics
    Route::get('/metrics', [MetricController::class, 'index'])->name('metrics.index');
    Route::get('/metrics/api/data', [MetricController::class, 'apiData'])->name('metrics.api.data');

    // SLA Reports
    Route::get('/sla', [SlaController::class, 'index'])->name('sla.index');
    Route::get('/sla/export', [SlaController::class, 'export'])->name('sla.export');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/email/test', [SettingController::class, 'testEmail'])->name('settings.email.test');
    Route::post('/settings/telegram/test', [SettingController::class, 'testTelegram'])->name('settings.telegram.test');

    // Users (admin only)
    Route::resource('users', UserController::class)->middleware('role:admin');
});
