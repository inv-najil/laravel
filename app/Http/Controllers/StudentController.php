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
     * for endponit get /students
     * Returns paginated list of students
     */
    public function index()
    {
        $this->authorize('viewAny', Student::class);
        return Student::paginate(10);
    }
    /**
     * Store a newly created resource in storage.
     * for endpoint post /students to create student
     * Returns created students json 
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
     * for end point get /student/id 
     * returns the student details of spefic id
     */
    public function show(Student $student)
    {
        $this->authorize('view', $student);
        return response()->json($student);
    }
    /**
     * Update the specified resource in storage.
     * for end ponts PUT PATCH /students
     * returns updated students details
     */
    public function update(Request $request, Student $student)
    {
        $this->authorize('update', $student);

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|regex:/^[0-9]{10}$/',
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
     * for end point DELETE /student
     * deletes the student and returns message
     */
    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);
        $student->user->delete();
        return response()->json(['message' => 'Student deleted']);
    }

    /**
     * for end pont GET /student/profile
     * returns the current logged in student details
     */
    public function getProfile()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return response()->json(['message' => 'Student record not found'], 404);
        }
        $this->authorize('view', $student);
        return response()->json($student);
    }

}
