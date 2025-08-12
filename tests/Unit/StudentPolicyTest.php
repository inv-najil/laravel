<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Policies\StudentPolicy;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentPolicyTest extends TestCase
{
    use RefreshDatabase;
    protected StudentPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new StudentPolicy();
    }

    /**
     * Student view Testing
     */

    /**
     * @test View admin
     */
    public function admin_can_view_all_students()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->assertTrue($this->policy->viewAny($admin));
    }
    /**
     * @test view student 
     * 
     */
    public function student_can_view_thier_own()
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($this->policy->view($user, $student));
    }
    /**
     * @test view teacher
     * 
     */
    public function teacher_can_view_thier_students()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);
        $this->assertTrue($this->policy->view($user, $student));
    }
    /**
     * @test other students cant view
     */
    public function other_students_cant_view_students()
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create();
        $this->assertFalse($this->policy->view($user, $student));
    }

    /**
     * Students create testing
     */

    /**
     * @test Only Admin  can create student
     */
    public function admin_can_create_students()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $dummyStudent = new Student();
        $this->assertTrue($this->policy->create($admin, $dummyStudent));
        $this->assertFalse($this->policy->create($teacher, $dummyStudent));
        $this->assertFalse($this->policy->create($student, $dummyStudent));
    }
    /**
     * Update policy testing
     */

    /**
     * @test admin can update all students
     */
    public function admin_can_update_all_students()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create();
        $this->assertTrue($this->policy->update($admin, $student));
    }
    /**
     * @test teacher can update assigned students
     */
    public function teacher_can_update_assigned_students()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);
        $this->assertTrue($this->policy->update($user, $student));
    }
    /**
     * @test teacher can not update unassigned students
     */
    public function teacher_can_not_update_unassigned_students()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $student = Student::factory()->create();
        $this->assertFalse($this->policy->update($user, $student));
    }
    /**
     * Student delete testing
     */
    /**
     * @test admin can delete all the students
     */
    public function admin_can_delete_all_students()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create();
        $this->assertTrue($this->policy->delete($admin, $student));
    }

    /**
     * @test teacher can delete assigned students
     */
    public function teacher_can_delete_assigned_students()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);
        $this->assertTrue($this->policy->delete($user, $student));
    }
    /**
     * @test student can not delete anyone
     */
    public function student_can_not_delete_anyone()
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create();
        $this->assertFalse($this->policy->delete($user, $student));
    }

}