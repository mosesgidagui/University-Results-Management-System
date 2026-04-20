<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinanceClearance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_session_id',
        'is_cleared',
        'cleared_by',
        'cleared_at',
        'notes',
    ];

    protected $casts = [
        'is_cleared' => 'boolean',
        'cleared_at' => 'datetime',
    ];

    public function student()         { return $this->belongsTo(User::class, 'student_id'); }
    public function academicSession() { return $this->belongsTo(AcademicSession::class); }
    public function clearedBy()       { return $this->belongsTo(User::class, 'cleared_by'); }
}
