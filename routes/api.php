<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterUserController;


Route::middleware(['jwt.auth', 'admin'])->post('/register', [RegisterUserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/debug-check', function () {
    return response()->json(['api_routes_loaded' => true]);
});

