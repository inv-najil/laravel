<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;
    /**
     * @param array $row
     * creating user and linked students from csv
     * 
     */

    public function model(array $row)
    {
        $user = User::create([
            'name' => $row['first_name'] . " " . $row["last_name"],
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
            'role' => 'student'
        ]);

        return new Student([
            'user_id' => $user->id,
            'teacher_id' => $row['teacher_id'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $user->email,
            'roll_num' => $row['roll_num'],
            'phone' => $row['phone'],
            'dob' => $row['dob'],
            'class_grade' => $row['class_grade'],
            'admission_date' => $row['admission_date'],
            'status' => $row['status']
        ]);
    }

    /**
     * Validation for each row
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email'),
            ],
            'password' => 'required|min:6',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'roll_num' => ['required', Rule::unique('students', 'roll_num')],
            'dob' => 'required|date|before:today',
            'admission_date' => 'required|date|after:dob|before_or_equal:today',
            'class_grade' => 'required',
            'status' => 'required|in:active,inactive',
            'teacher_id' => 'required|exists:teachers,id',
        ];
    }


    /**
     * Custom Validation Messages for unique email,roll
     */
    public function customValidationMessages()
    {
        return [
            'email.unique' => 'This email already exists in the system',
            'roll_num.unique' => 'This roll number already exists',
            'teacher_id.exists' => 'Teacher ID not found in teachers table',
        ];
    }
}
