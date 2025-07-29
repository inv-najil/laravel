<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use Hash;
use Illuminate\Http\Request;

class RegisterUserController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:user,email',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'required|in:teacher,student'
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role']
        ]);

        if ($validated['role'] === 'teacher') {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|max:10',
                'emp_id' => 'required|string|unique',
                'subject_specialization' => 'required|string',
                'date_of_joining' => 'required|date',
                'status' => 'required|in:active,inactive'
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'emp_id' => $request->emp_id,
                'subject_specialization' => $request->subject_specialization,
                'date_of_joining' => $request->date_of_joining,
                'status' => $request->status
            ]);
        }

        if ($validated['role'] === 'student') {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'teacher_id' => 'required|exists:teachers,id',
                'phone' => 'required|string|max:10',
                'roll_num' => 'required|string|unique',
                'dob' => 'required|date',
                'admission_date' => 'required|date',
                'status' => 'required|in:active,inactive'
            ]);

            Student::create([
                'user_id' => $user->id,
                'teacher_id' => $request->teacher_id,
                'email' => $user->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'roll_num' => $request->roll_num,
                'phone' => $request->phone,
                'dob' => $request->dob,
                'admission_date' => $request->admission_date,
                'status' => $request->status

            ]);
        }

        return response()->json([
            'message' => ucfirst($validated['role']) . ' registered successfully',
            'user'    => $user,
        ], 201);
    }
}
