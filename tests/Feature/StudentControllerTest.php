<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;
    private function asAdmin()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);
        $token = JWTAuth::fromUser($user);
        return ['Authorization' => "Bearer $token"];
    }
    /**
     * View testing
     */
    /**
     * Admin can view all the students
     */
    public function test_admin_can_view_all_students()
    {
        Student::factory()->count(5)->create();
        $response = $this->withHeaders($this->asAdmin())
            ->getJson('api/students');
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }
    /**
     * Admin can view single student details
     */
    public function test_admin_can_view_single_student()
    {
        $student = Student::factory()->create([]);
        $response = $this->withHeaders($this->asAdmin())
            ->getJson("api/students/{$student->id}");
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $student->id
            ]);
    }
    /**
     * Student can view thier own profile
     */
    public function test_student_can_view_thier_own_profile()
    {
        $studentUser = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create(['user_id' => $studentUser->id]);
        $token = JWTAuth::fromUser($studentUser);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('api/student/profile');
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $student->id
            ]);
    }
    /**
     * Delete testing
     */
    /**
     * Admin can delete all students
     */
    public function test_admin_can_delete_all()
    {
        $student = Student::factory()->create([]);
        $response = $this->withHeaders($this->asAdmin())
            ->deleteJson("api/students/{$student->id}");
        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Student deleted'
            ]);
    }
}