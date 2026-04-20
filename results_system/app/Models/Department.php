<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// ── Department ────────────────────────────────────────────────────
class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'faculty_id', 'hod_id'];

    public function faculty()   { return $this->belongsTo(Faculty::class); }
    public function hod()       { return $this->belongsTo(User::class, 'hod_id'); }
    public function users()     { return $this->hasMany(User::class); }
    public function courses()   { return $this->hasMany(Course::class); }
}
