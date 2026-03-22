<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

// Act1

Route::get('user', function () {
    return 'Hello, Im Claire';
});

Route::prefix('auth')->group(function() {
    Route::post('/register', [AuthController::class, 'register']); 
    Route::post('/login', [AuthController::class, 'login']); 
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// User routes (protected)
Route::prefix('users')->middleware('auth:sanctum')->group(function() {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::post('/', [UserController::class, 'store']);
    
    // Update & delete require permissions
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});  
Route::get('/profile', function (Request $request) { 
    return $request->user(); 
    })->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:Admin|Chairman'])->get('/management', function () {
    return "Admin or Chairman only";
});

 