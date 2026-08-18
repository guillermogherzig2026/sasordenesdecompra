<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserAccessController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('obras', ProjectController::class)
        ->parameters(['obras' => 'project']);

    Route::get('/bitacora', [AuditLogController::class, 'index'])
        ->middleware('role:superadministrador,administrador-obra,supervisor')
        ->name('audit.index');

    Route::get('/usuarios-permisos', [UserAccessController::class, 'index'])
        ->middleware('role:superadministrador')
        ->name('users.access.index');

    Route::put('/usuarios-permisos/{user}/obras', [UserAccessController::class, 'updateProjectAccess'])
        ->middleware('role:superadministrador')
        ->name('users.access.update');
});
