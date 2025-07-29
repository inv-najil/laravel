<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        "user_id",
        "first_name",
        "last_name",
        "emp_id",
        "email",
        "phone",
        "subject_specialization",
        "date_of_joining",
        "status"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students(){
        return $this->hasMany(Student::class);
    }

}

