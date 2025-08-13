<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Tymon\JWTAuth\Payload;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;
    private function asAdmin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($admin);
        return ['Authorization' => "Bearer $token"];
    }
    /**
     * Only admin can register users
     */
    /**
     * Admin can register teacher
     */
    public function test_register_teacher_successfully()
    {
        $payload = [
            'email' => 'teacher@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'teacher',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'emp_id' => 'EMP001',
            'subject_specialization' => 'Math',
            'date_of_joining' => now()->toDateString(),
            'status' => 'active',
        ];

        $response = $this->withHeaders($this->asAdmin())
            ->postJson('api/register', $payload);
        $response->assertStatus(201)
            ->assertJson(['message' => 'Teacher registered successfully']);
    }

    /**
     * Admin can Register Student
     */
    public function test_register_student_successfully()
    {
        $teacherUser = User::factory()->create(['role' => 'student']);
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $payload = [
            'email' => 'student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'teacher_id' => $teacher->id,
            'phone' => '9876543210',
            'roll_num' => 'R001',
            'dob' => now()->subYears(10)->toDateString(),
            'admission_date' => now()->subYears(5)->toDateString(),
            'class_grade' => '5A',
            'status' => 'active'
        ];
        $response = $this->withHeaders($this->asAdmin())
            ->postJson('api/register', $payload);
        $response->assertStatus(201)
            ->assertJson(['message' => 'Student registered successfully']);
    }
    /**
     * Registeration fails with missing required fields
     */
    public function test_register_fail_missing_required()
    {
        $response = $this->withHeaders($this->asAdmin())
            ->postJson('api/register', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password', 'role']);
    }
    /**
     * Registeration fails if the dob is in future
     */
    public function test_register_fails_invalid_dob()
    {
        $teacherUser = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $payload = [
            'email' => 'future@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
            'first_name' => 'Future',
            'last_name' => 'Person',
            'teacher_id' => $teacher->id,
            'phone' => '9876543210',
            'roll_num' => 'R100',
            'dob' => now()->addDay()->toDateString(),
            'admission_date' => now()->toDateString(),
            'class_grade' => '10B',
            'status' => 'active'
        ];
        $response = $this->withHeaders($this->asAdmin())
            ->postJson('api/register', $payload);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['dob']);
    }
    /**
     * Registeration fails if admission date is before dob
     */
    public function test_register_fails_invalid_admission_date()
    {
        $teacherUser = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        $payload = [
            'email' => 'wrongdates@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
            'first_name' => 'Wrong',
            'last_name' => 'Dates',
            'teacher_id' => $teacher->id,
            'phone' => '9876543210',
            'roll_num' => 'R101',
            'dob' => now()->subYears(5)->toDateString(),
            'admission_date' => now()->subYears(6)->toDateString(),
            'class_grade' => '10C',
            'status' => 'active'
        ];
        $response = $this->withHeaders($this->asAdmin())
            ->postJson('api/register', $payload);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['admission_date']);
    }
    /**
     * Registeraion fails for invalid joining date
     */
    public function test_register_fails_invalid_joining_date()
    {
        $payload = [
            'email' => 'teacher@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'teacher',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'emp_id' => 'EMP001',
            'subject_specialization' => 'Math',
            'date_of_joining' => now()->addYears(6)->toDateString(),
            'status' => 'active',
        ];
        $response = $this->withHeaders($this->asAdmin())
            ->postJson('api/register', $payload);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date_of_joining']);
    }
    /**
     * Registeration Fails for Duplicate roll num
     */
    public function test_register_fails_duplicate_roll_num()
    {
        $teacherUser = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id]);
        Student::factory()->create([
            'roll_num' => 'R200',
            'teacher_id' => $teacher->id
        ]);
        $payload = [
            'email' => 'dup@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
            'first_name' => 'Dup',
            'last_name' => 'LICATE',
            'teacher_id' => $teacher->id,
            'phone' => '9876543210',
            'roll_num' => 'R200',
            'dob' => now()->subYears(10)->toDateString(),
            'admission_date' => now()->subYears(5)->toDateString(),
            'class_grade' => '6A',
            'status' => 'active'
        ];
        $response = $this->withHeaders($this->asAdmin())
            ->postJson('api/register', $payload);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['roll_num']);
    }
    /**
     * Registration fails for duplicate emp id
     */
    public function test_register_fails_duplicate_emp_id()
    {
        Teacher::factory()->create([
            'emp_id' => 'EMP999'
        ]);
        $payload = [
            'email' => 'dupemp@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'teacher',
            'first_name' => 'Dup',
            'last_name' => 'Emp',
            'phone' => '1234567890',
            'emp_id' => 'EMP999',
            'subject_specialization' => 'Science',
            'date_of_joining' => now()->toDateString(),
            'status' => 'active',
        ];
        $response = $this->withHeaders($this->asAdmin())
            ->postJson('api/register', $payload);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['emp_id']);
    }
    /**
     * Registeration fails for duplicate email
     */
    public function test_register_fails_duplicate_email()
    {
        User::factory()->create([
            'role' => 'teacher',
            'email' => 'teacher@example.com'
        ]);
        $payload = [
            'email' => 'teacher@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'teacher',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'emp_id' => 'EMP001',
            'subject_specialization' => 'Math',
            'date_of_joining' => now()->toDateString(),
            'status' => 'active',
        ];
        $response = $this->withHeaders($this->asAdmin())
            ->postJson('api/register', $payload);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

}