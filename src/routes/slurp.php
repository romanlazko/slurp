<?php

use Illuminate\Support\Facades\Route;
use Romanlazko\Slurp\App\Http\Controllers\PermissionController;
use Romanlazko\Slurp\App\Http\Controllers\RoleController;
use Romanlazko\Slurp\App\Http\Controllers\UserController;

Route::middleware(['web', 'auth', 'role:super-duper-admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::resource('user', UserController::class);
    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);
});