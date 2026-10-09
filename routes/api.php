<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prevfix' => 'v1'], function(){
        // Public endpoints (no authentication required)

    // Customer authentication endpoints
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot_password', [AuthController::class, 'forgetPassword']);
    Route::post('/auth/reset_password', [AuthController::class, 'resetPassword']);

    Route::post('/auth/verify', [AuthController::class, 'verifyEmail']);

});

  /**
 * Authenticated Public API Routes
 * Add routes below that require authentication but are for website/customer functionality
 * Only users with 'customer' role can access these endpoints
 */

Route::group(['prefix' => 'v1', 'middleware' => ['auth:sanctum', 'role:customer']], function () {
    // Authenticated customer routes

    // Customer authentication endpoints
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Customer profile routes
    // Route::get('/customer/profile', [CustomerController::class, 'profile']);
    // Route::put('/customer/profile', [CustomerController::class, 'updateProfile']);
    // Route::put('/customer/answer_questions', [CustomerController::class, 'answerQuestions']);

    // Customer file routes
    // Route::get('/customer/files/{file}', [FileController::class, 'show'])->name('customer.files.show');
    // Route::post('/customer/files', [FileController::class, 'store']);
    // Route::post('/customer/files/avatar', [FileController::class, 'storeAvatar']);
});
