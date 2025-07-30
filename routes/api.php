<?php
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterUserController;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\Gate;


Route::middleware(['jwt.auth', 'admin'])->post('/register', [RegisterUserController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/debug-check', function () {
    return response()->json(['api_routes_loaded' => true]);
});

Route::middleware(['jwt.auth'])->group(function () {
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('students', StudentController::class);
});

Route::middleware('jwt.auth')->get('/teachers/{id}/students', function ($id) {
    $teacher = Teacher::find($id);

    if (!$teacher) {
        return response()->json(['message' => 'Teacher not found'], 404);
    }

    Gate::authorize('viewStudents', $teacher);

    return $teacher->students()->paginate(10);
});