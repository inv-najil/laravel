<?php

namespace App\Http\Controllers;

use App\Imports\TeacherImport;
use App\Models\Teacher;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;


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
            'phone' => 'sometimes|required|regex:/^[0-9]{10}$/',
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
        $teacher = Teacher::where('user_id', $user->id)->first();
        if (!$teacher) {
            return response()->json(['message' => 'Teacher record not found'], 404);
        }
        $this->authorize('view', $teacher);

        return response()->json($teacher, 200);
    }

    /**
     * For export Teacher details as csv
     * Returns a CSV file
     */
    public function exportCSV()
    {
        $this->authorize('viewAny', Teacher::class);

        $filename = "teachers.csv";

        $teachers = Teacher::with('user')->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-disposition" => "attachment; filename=$filename"
        ];

        $columns = [
            "Id",
            "First_name",
            "Last_name",
            "Email",
            "Phone",
            "EMP_num",
            "Subject",
            "Date_of_Joining"
        ];

        $callback = function () use ($columns, $teachers) {
            $file = fopen("php://output", "w");
            fputcsv($file, $columns);
            foreach ($teachers as $teacher) {
                fputcsv($file, [
                    $teacher->id,
                    $teacher->first_name,
                    $teacher->last_name,
                    $teacher->user->email ?? '',
                    $teacher->phone,
                    $teacher->emp_id,
                    $teacher->subject_specialization,
                    $teacher->date_of_joining
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function importCSV(Request $request){
          $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
          ]);

          $import = new TeacherImport;

          Excel::import($import,$request->file('file'));

           return response()->json([
            'message' => 'Teachers import completed',
            'failures' => $import->failures(), 
        ]);
    }
}

