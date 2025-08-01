<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class TeacherController extends Controller
{
    /**
     * 
     * Display a listing of the resource.
     * for end point GET /teacher
     * Returns Paginatted view of registed teachers
     */
    public function index()
    {
        $this->authorize('viewAny', Teacher::class);
        return Teacher::paginate(10);
    }

    /**
     * Show the form for creating a new resource.
     * not used in api
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * for end point POST /teacher
     * creates a new teacher record and returns the json
     */
    public function store(Request $request)
    {
        $this->authorize('create', Teacher::class);
        $validated = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:10',
            'emp_id' => 'required|string|unique:teachers,emp_id',
            'subject_specialization' => 'required|string',
            'date_of_joining' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'],
            'emp_id' => $validated['emp_id'],
            'subject_specialization' => $validated['subject_specialization'],
            'date_of_joining' => $validated['date_of_joining'],
            'status' => $validated['status'],
        ]);

        return response()->json($teacher, 201);
    }

    /**
     * Display the specified resource.
     * for end ponit GET /teacher/id
     * returns the teacher details of specific id
     */
    public function show(Teacher $teacher)
    {
        $this->authorize('view', $teacher);
        return response()->json($teacher);
    }

    /**
     * Show the form for editing the specified resource.
     * not used for api
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * for end point PUT PATCH /teacher
     * retruns the updated teacher json
     */
    public function update(Request $request, Teacher $teacher)
    {
        $this->authorize('update', $teacher);
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:10',
            'subject_specialization' => 'sometimes|required|string',
            'date_of_joining' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        $teacher->update($validated);
        if ($request->has('first_name') || $request->has('last_name')) {
            $teacher->user->name = ($request->first_name ?? $teacher->first_name) . ' ' . ($request->last_name ?? $teacher->last_name);
            $teacher->user->save();
        }

        return response()->json($teacher, 200);
    }

    /**
     * Remove the specified resource from storage.
     * for end pont DELTE /techer
     * delets the teacher record
     */
    public function destroy(Teacher $teacher)
    {
        $this->authorize('delete', $teacher);
        $teacher->user->delete();
        return response()->json(['message' => 'teacher deleted']);
    }

    /**
     * for end pont GET /teacher/profile
     * returns the current logged in teacher details
     */
    public function getProfile()
    {
        $user = Auth::user();
        if ($user->role !== 'teacher') {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher) {
            return response()->json(['message' => 'Teacher record not found'], 404);
        }

        return response()->json($teacher, 200);
    }

}
