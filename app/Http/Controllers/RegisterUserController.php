<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\DB;
class RegisterUserController extends Controller
{
    public function register(Request $request)
    {

        //to solve partial creation 
        DB::beginTransaction();

        try {
            Log::info('Register controller hit', ['request' => $request->all()]);

            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|confirmed|min:6',
                'role' => 'required|in:teacher,student'
            ]);

            Log::info('Validation passed', ['validated' => $validated]);

            // Validate role-specific fields BEFORE creating user
            if ($validated['role'] === 'teacher') {
                $request->validate([
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:10',
                    'emp_id' => 'required|string|unique:teachers,emp_id',
                    'subject_specialization' => 'required|string',
                    'date_of_joining' => 'required|date',
                    'status' => 'required|in:active,inactive'
                ]);
            }

            if ($validated['role'] === 'student') {
                $request->validate([
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'teacher_id' => 'required|exists:teachers,id',
                    'phone' => 'required|string|max:10',
                    'roll_num' => 'required|string|unique:students,roll_num',
                    'dob' => 'required|date',
                    'admission_date' => 'required|date',
                    'class_grade' => 'required|string',
                    'status' => 'required|in:active,inactive'
                ]);
            }

            $first_name = $request->input('first_name');
            $last_name = $request->input('last_name');

            $user = User::create([
                'name' => $first_name . ' ' . $last_name,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role']
            ]);

            if ($validated['role'] === 'teacher') {
                Teacher::create([
                    'user_id' => $user->id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $validated['email'],
                    'phone' => $request->phone,
                    'emp_id' => $request->emp_id,
                    'subject_specialization' => $request->subject_specialization,
                    'date_of_joining' => $request->date_of_joining,
                    'status' => $request->status
                ]);
            }

            if ($validated['role'] === 'student') {
                Student::create([
                    'user_id' => $user->id,
                    'teacher_id' => $request->teacher_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $validated['email'],
                    'roll_num' => $request->roll_num,
                    'phone' => $request->phone,
                    'dob' => $request->dob,
                    'admission_date' => $request->admission_date,
                    'class_grade' => $request->class_grade,
                    'status' => $request->status
                ]);
            }

            DB::commit(); // All good

            return response()->json([
                'message' => ucfirst($validated['role']) . ' registered successfully',
                'user' => $user,
            ], 201);

        } catch (Exception $e) {
            DB::rollBack(); // Rollback everything on error

            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'error' => 'Registration failed. Check logs.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
