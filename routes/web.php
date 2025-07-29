<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post("/api/login", [AuthController::class, 'login']);
