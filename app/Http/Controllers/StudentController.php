<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Student::class);
        return Student::paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Student::class);
        $validated = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:10',
            'roll_num' => 'required|string|unique:students',
            'dob' => 'required|date',
            'admission_date' => 'required|date',
            'class_grade' => 'required|string',
            'status' => 'required|in:active,inactive',
            'teacher_id' => 'required|exists:teachers,id'
        ]);

        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'teacher_id' => $validated['teacher_id'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'roll_num' => $validated['roll_num'],
            'dob' => $validated['dob'],
            'admission_date' => $validated['admission_date'],
            'class_grade' => $validated['class_grade'],
            'status' => $validated['status'],
        ]);

        return response()->json($student, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $this->authorize('view', $student);
        return $student->load(['user', 'teacher']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $this->authorize('update', $student);

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:10',
            'class_grade' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        $student->update($validated);

        if ($request->has('first_name') || $request->has('last_name')) {
            $student->user->name = ($request->first_name ?? $student->first_name) . ' ' . ($request->last_name ?? $student->last_name);
            $student->user->save();
        }

        return response()->json($student);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);
        $student->user->delete();
        return response()->json(['message' => 'Student deleted']);
    }

    public function getProfile()
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $student = Student::where('user_id', $user->id)
            ->with(['user', 'teacher'])
            ->first();

        if (!$student) {
            return response()->json(['message' => 'Student record not found'], 404);
        }

        return response()->json($student);
    }

}
