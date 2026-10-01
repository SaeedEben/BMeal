<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\Auth\AuthController;
use App\Http\Controllers\Panel\Auth\ProfileController;
use App\Http\Controllers\Panel\User\UserController;


Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [ProfileController::class, 'profile']);
        
        // User ------------------------------------------------------------------------
        Route::get('/users/list', [UserController::class, 'list']);
        Route::post('/users/change_password/{user}', [UserController::class, 'changePassword']);
        Route::apiResource('users', UserController::class);

          // Role ------------------------------------------------------------------------
        // Route::get('/roles/list', [RoleController::class, 'list']);
        // Route::apiResource('roles', RoleController::class);

          // Permission ------------------------------------------------------------------------
        // Route::get('permissions', [PermissionController::class, 'index']);

    });
});