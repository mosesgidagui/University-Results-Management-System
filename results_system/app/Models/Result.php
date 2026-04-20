<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
    use HasFactory, SoftDeletes;

    // ── Status constants ──────────────────────────────────────────
    const STATUS_DRAFT            = 'draft';
    const STATUS_SUBMITTED        = 'submitted';
    const STATUS_HOD_APPROVED     = 'hod_approved';
    const STATUS_HOD_REJECTED     = 'hod_rejected';
    const STATUS_COMPILED         = 'compiled';
    const STATUS_SENATE_APPROVED  = 'senate_approved';
    const STATUS_SENATE_REJECTED  = 'senate_rejected';
    const STATUS_PUBLISHED        = 'published';

    const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SUBMITTED,
        self::STATUS_HOD_APPROVED,
        self::STATUS_HOD_REJECTED,
        self::STATUS_COMPILED,
        self::STATUS_SENATE_APPROVED,
        self::STATUS_SENATE_REJECTED,
        self::STATUS_PUBLISHED,
    ];

    // Grade boundaries
    const GRADE_BOUNDARIES = [
        ['min' => 80, 'max' => 100, 'grade' => 'A',  'points' => 5.0, 'remark' => 'Distinction'],
        ['min' => 70, 'max' => 79,  'grade' => 'B',  'points' => 4.0, 'remark' => 'Credit'],
        ['min' => 60, 'max' => 69,  'grade' => 'C',  'points' => 3.0, 'remark' => 'Merit'],
        ['min' => 50, 'max' => 59,  'grade' => 'D',  'points' => 2.0, 'remark' => 'Pass'],
        ['min' => 0,  'max' => 49,  'grade' => 'F',  'points' => 0.0, 'remark' => 'Fail'],
    ];

    protected $fillable = [
        'student_id',
        'lecturer_id',
        'course_id',
        'academic_session_id',
        'marks',
        'grade',
        'grade_points',
        'remark',
        'status',
        'hod_comment',
        'senate_comment',
        'submitted_at',
        'hod_actioned_at',
        'compiled_at',
        'senate_actioned_at',
        'published_at',
    ];

    protected $casts = [
        'marks'              => 'decimal:2',
        'grade_points'       => 'decimal:1',
        'submitted_at'       => 'datetime',
        'hod_actioned_at'    => 'datetime',
        'compiled_at'        => 'datetime',
        'senate_actioned_at' => 'datetime',
        'published_at'       => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────
    public function student()      { return $this->belongsTo(User::class, 'student_id'); }
    public function lecturer()     { return $this->belongsTo(User::class, 'lecturer_id'); }
    public function course()       { return $this->belongsTo(Course::class); }
    public function academicSession() { return $this->belongsTo(AcademicSession::class); }

    // ── Grade computation ─────────────────────────────────────────
    public static function computeGrade(float $marks): array
    {
        foreach (self::GRADE_BOUNDARIES as $boundary) {
            if ($marks >= $boundary['min'] && $marks <= $boundary['max']) {
                return $boundary;
            }
        }
        return ['grade' => 'F', 'points' => 0.0, 'remark' => 'Fail'];
    }

    // ── Status helpers ────────────────────────────────────────────
    public function canBeSubmitted(): bool
    {
        return $this->status === self::STATUS_DRAFT
            || $this->status === self::STATUS_HOD_REJECTED;
    }

    public function canHodAction(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function canBeCompiled(): bool
    {
        return $this->status === self::STATUS_HOD_APPROVED;
    }

    public function canSenateAction(): bool
    {
        return $this->status === self::STATUS_COMPILED;
    }

    public function canBePublished(): bool
    {
        return $this->status === self::STATUS_SENATE_APPROVED;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    // ── Scopes ───────────────────────────────────────────────────
    public function scopePublished($query)       { return $query->where('status', self::STATUS_PUBLISHED); }
    public function scopeSubmitted($query)       { return $query->where('status', self::STATUS_SUBMITTED); }
    public function scopeHodApproved($query)    { return $query->where('status', self::STATUS_HOD_APPROVED); }
    public function scopeCompiled($query)        { return $query->where('status', self::STATUS_COMPILED); }
    public function scopeForStudent($query, $id) { return $query->where('student_id', $id); }
}
