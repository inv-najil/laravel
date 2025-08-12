<?php

namespace Tests\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Policies\TeacherPolicy;

class TeacherPolicyTest extends TestCase
{
    use RefreshDatabase;
    protected TeacherPolicy $policy;

    public function setUp(): void
    {
        parent::setUp();
        $this->policy = new TeacherPolicy();
    }

    /**
     * Teacher view permission testing
     */

    /**
     *  Admin can view all the teachers
     */
    public function test_admin_can_view_all_teachers()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->assertTrue($this->policy->viewAny($user));
    }
    /**
     *  Teacher can view their own
     */
    public function test_teacher_can_view_thier_own()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($this->policy->view($user, $teacher));
    }
    /**
     *  Student cant view teacher details
     */
    public function test_student_can_not_view_teachers()
    {
        $user = User::factory()->create(['role' => 'student']);
        $teacher = Teacher::factory()->create();
        $this->assertFalse($this->policy->view($user, $teacher));
    }
    /**
     *  Teacher cannot view other teachers
     */
    public function test_teacher_can_not_view_other_teachers()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create();
        $this->assertFalse($this->policy->view($user, $teacher));
    }

    /**
     * Teacher create permission test
     */

    /**
     *  Admin Can Create Teachers
     */
    public function test_admin_can_create_teacher()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $teacher = Teacher::factory()->create();
        $this->assertTrue($this->policy->create($user, $teacher));
    }
    /**
     *  teacher can not create a new teacher
     */
    public function test_teacher_cannot_create_teacher()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create();
        $this->assertFalse($this->policy->create($user, $teacher));
    }
    /**
     *  student can not create a new teacher
     */
    public function test_students_cannot_create_teacher()
    {
        $user = User::factory()->create(['role' => 'student']);
        $teacher = Teacher::factory()->create();
        $this->assertFalse($this->policy->create($user, $teacher));
    }
    /**
     * Teacher Update permission checking
     */
    /**
     *  admin can update teachers
     */
    public function test_admin_can_update_teachers()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $teacher = Teacher::factory()->create();
        $this->assertTrue($this->policy->update($user, $teacher));
    }
    /**
     *  teacher cant update teacher
     */
    public function test_teacher_cannot_update_teacher()
    {
        $user = User::factory()->create(['role' => 'teacher']);
        $teacher = Teacher::factory()->create();
        $this->assertFalse($this->policy->update($user, $teacher));
    }
    /**
     *  student cant update teacher
     */
    public function test_student_cannot_update_teacher()
    {
        $user = User::factory()->create(['role' => 'student']);
        $teacher = Teacher::factory()->create();
        $this->assertFalse($this->policy->update($user, $teacher));
    }
    /**
     * Teacher delete testing
     */
    /**
     * Admin can delete all the teachers
     */
    public function test_admin_can_delete_teacher()
    {
        $user = User::factory()->create(['role'=>'admin']);
        $teacher = Teacher::factory()->create();
        $this->assertTrue($this->policy->delete($user,$teacher));
    }
    /**
     * Teacher cant delete teacher
     */
    public function test_teacher_cannot_delete_teacher()
    {
        $user = User::factory()->create(['role'=>'teacher']);
        $teacher = Teacher::factory()->create();
        $this->assertFalse($this->policy->delete($user,$teacher));
    }
    /**
     * student cant delete teacher
     */
    public function test_student_can_not_delete_teacher()
    {
        $user = User::factory()->create(['role'=>'student']);
        $teacher = Teacher::factory()->create();
        $this->assertFalse($this->policy->delete($user,$teacher));
    }
}