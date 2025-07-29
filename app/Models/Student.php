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
        'dob',
        'admission_date',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
