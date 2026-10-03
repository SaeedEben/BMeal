<?php

use App\Http\Controllers\Panel\Auth\AuthController;
use App\Http\Controllers\Panel\Auth\ProfileController;
use App\Http\Controllers\Panel\Country\CountryController;
use App\Http\Controllers\Panel\Recipe\CategoryController;
use App\Http\Controllers\Panel\Recipe\TagController;
use App\Http\Controllers\Panel\User\PermissionController;
use App\Http\Controllers\Panel\User\RoleController;
use App\Http\Controllers\Panel\User\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => ['auth:sanctum', 'role:admin']], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [ProfileController::class, 'profile']);

        Route::group(['prefix' => 'users'], function () {
            // User ------------------------------------------------------------------------
            Route::get('/user/list', [UserController::class, 'list']);
            Route::post('/user/change_password/{user}', [UserController::class, 'changePassword']);
            Route::apiResource('user', UserController::class);

            // Role ------------------------------------------------------------------------
            Route::get('/roles/list', [RoleController::class, 'list']);
            Route::apiResource('role', RoleController::class);

            // Permission ------------------------------------------------------------------------
            Route::get('permissions', [PermissionController::class, 'index']);
        });

        Route::group(['prefix' => 'countries'], function () {
            // Country ------------------------------------------------------------------------
            Route::get('/country/list', [CountryController::class, 'list']);
            Route::apiResource('country', CountryController::class);
        });

        Route::group(['prefix' => 'recipes'], function () {
            // Category ----------------------------------------------
            Route::get('/category/list', [CategoryController::class, 'list']);
            Route::apiResource('category', CategoryController::class);

            // Tag ----------------------------------------------
            Route::get('/tag/list', [TagController::class, 'list']);
            Route::apiResource('tag', TagController::class);
        });
    });
});
