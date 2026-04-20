<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ROLE_ADMIN      = 'admin';
    const ROLE_LECTURER   = 'lecturer';
    const ROLE_HOD        = 'hod';
    const ROLE_FINANCE    = 'finance';

    const ROLE_REGISTRAR  = 'registrar';
    const ROLE_SENATE     = 'senate';
    const ROLE_STUDENT    = 'student';

    const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_LECTURER,
        self::ROLE_HOD,
        self::ROLE_FINANCE,
        self::ROLE_REGISTRAR,
        self::ROLE_SENATE,
        self::ROLE_STUDENT,
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_number',
        'department_id',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_active'         => 'boolean',
    ];

    // ── Role helpers ──────────────────────────────────────────────
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin():      bool { return $this->hasRole(self::ROLE_ADMIN); }
    public function isLecturer():   bool { return $this->hasRole(self::ROLE_LECTURER); }
    public function isHod():        bool { return $this->hasRole(self::ROLE_HOD); }
    public function isFinance():    bool { return $this->hasRole(self::ROLE_FINANCE); }
    public function isRegistrar():  bool { return $this->hasRole(self::ROLE_REGISTRAR); }
    public function isSenate():     bool { return $this->hasRole(self::ROLE_SENATE); }
    public function isStudent():    bool { return $this->hasRole(self::ROLE_STUDENT); }

    // ── Relationships ─────────────────────────────────────────────
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'student_id');
    }

    public function uploadedResults()
    {
        return $this->hasMany(Result::class, 'lecturer_id');
    }

    public function financeClearance()
    {
        return $this->hasMany(FinanceClearance::class, 'student_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
