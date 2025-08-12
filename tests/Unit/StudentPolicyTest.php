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
     *  View admin
     */
    public function test_admin_can_view_all_students()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->assertTrue($this->policy->viewAny($admin));
    }
    /**
     *  view student 
     * 
     */
    public function test_student_can_view_thier_own()
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($this->policy->view($user, $student));
    }
    /**
     *  view teacher
     * 
     */
    public function test_teacher_can_view_thier_students()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);
        $this->assertTrue($this->policy->view($user, $student));
    }
    /**
     *  other students cant view
     */
    public function test_other_students_cant_view_students()
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create();
        $this->assertFalse($this->policy->view($user, $student));
    }

    /**
     * Students create testing
     */

    /**
     *  Only Admin  can create student
     */
    public function test_admin_can_create_students()
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
     *  admin can update all students
     */
    public function test_admin_can_update_all_students()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create();
        $this->assertTrue($this->policy->update($admin, $student));
    }
    /**
     *  teacher can update assigned students
     */
    public function test_teacher_can_update_assigned_students()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);
        $this->assertTrue($this->policy->update($user, $student));
    }
    /**
     *  teacher can not update unassigned students
     */
    public function test_teacher_can_not_update_unassigned_students()
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
     *  admin can delete all the students
     */
    public function test_admin_can_delete_all_students()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = Student::factory()->create();
        $this->assertTrue($this->policy->delete($admin, $student));
    }

    /**
     *  teacher can delete assigned students
     */
    public function test_teacher_can_delete_assigned_students()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);
        $this->assertTrue($this->policy->delete($user, $student));
    }
    /**
     *  student can not delete anyone
     */
    public function test_student_can_not_delete_anyone()
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create();
        $this->assertFalse($this->policy->delete($user, $student));
    }

}