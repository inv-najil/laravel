<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\User;
use Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class TeacherImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;
    /**
     * @param array $row
     *
     */
    public function model(array $row)
    {

        $user = User::create([
            'name' => $row['first_name'] . " " . $row['last_name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
            'role' => 'teacher'
        ]);
        return new Teacher([
            'user_id' => $user->id,
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'email' => $user->email,
            'phone' => $row['phone'],
            'emp_id' => $row['emp_id'],
            'subject_specialization' => $row['subject_specialization'],
            'date_of_joining' => $row['date_of_joining'],
            'status' => $row['status']
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
            ],
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'emp_id' => [
                'required',
                Rule::unique('teachers', 'emp_id')
            ],
            'subject_specialization' => 'required|string',
            'date_of_joining' => 'required|date|before_or_equal:today',
            'status' => 'required|in:active,inactive'
        ];
    }

    public function customValidationMessages()
    {
        return [
            'email.unique' => 'This email already exists',
            'emp_id.unique' => 'This Emp number already exists',
        ];
    }
}
