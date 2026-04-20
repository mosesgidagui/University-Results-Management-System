<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'credit_units', 'department_id', 'lecturer_id'];

    public function department() { return $this->belongsTo(Department::class); }
    public function lecturer()   { return $this->belongsTo(User::class, 'lecturer_id'); }
    public function results()    { return $this->hasMany(Result::class); }
}
