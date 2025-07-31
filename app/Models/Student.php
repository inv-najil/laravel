<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'teacher_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'roll_num',
        'class_grade',
        'dob',
        'admission_date',
        'status'
    ];
    
    //student belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //student belongs to a teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
