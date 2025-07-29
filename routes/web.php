<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterUserController;

Route::middleware(['auth:api', 'admin'])->post('/register', [RegisterUserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


