<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::get('/status', function () {
        return response()->json([
            'success' => true,
            'message' => 'Panel API is working',
        ]);
    })->name('status');
});
