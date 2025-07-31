<?php
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterUserController;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\Gate;

//login
Route::post('/login', [AuthController::class, 'login']);

//refresh token
Route::post('/refresh', [AuthController::class, 'refresh']);

//logout
Route::post('/logout', [AuthController::class, 'logout']);

//Regsiter teacher or students admin
Route::middleware(['jwt.auth', 'admin'])->post('/register', [RegisterUserController::class, 'register']);

//just for checking
Route::get('/debug-check', function () {
    return response()->json(['api_routes_loaded' => true]);
});

//Techers and students endpoint
Route::middleware(['jwt.auth'])->group(function () {
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('students', StudentController::class);
});

//Assingned teacher
Route::middleware('jwt.auth')->get('/teachers/{id}/students', function ($id) {
    $teacher = Teacher::find($id);
    if (!$teacher) {
        return response()->json(['message' => 'Teacher not found'], 404);
    }
    Gate::authorize('viewStudents', $teacher);
    return $teacher->students()->paginate(10);
});

//student view
Route::middleware('jwt.auth')->get('/student/profile', [StudentController::class, 'getProfile']);

