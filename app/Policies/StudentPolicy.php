<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudentPolicy
{
    /**
     * Determine whether the user can view any models.
     * Only admin is allowd
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     * admin can view all
     * teachers can view only assinged students
     * students can only view thier own
     */
    public function view(User $user, Student $student): bool
    {
        return $user->role === 'admin' ||
            ($user->role === 'student' && $user->id === $student->user_id) ||
            ($user->role === 'teacher' && $user->id === $student->teacher->user_id);
    }

    /**
     * Determine whether the user can create models.
     * admin is only allowd to create students
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     * Admin can update
     * Teacher can only update the studnets assinged to them
     */
    public function update(User $user, Student $student): bool
    {
        return $user->role === 'admin' || ($user->role === 'teacher' && $user->id === $student->teacher->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     * admin can delete
     * teacher can only delete assinged students
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->role === 'admin' || ($user->role === 'teacher' && $user->id === $student->teacher->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Student $student): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Student $student): bool
    {
        return false;
    }
}
