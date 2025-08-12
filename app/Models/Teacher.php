<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;

class Teacher extends Model
{   
    use HasFactory;
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
    
    //Teacher belong to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //Teacher has many students
    public function students(){
        return $this->hasMany(Student::class);
    }

}

