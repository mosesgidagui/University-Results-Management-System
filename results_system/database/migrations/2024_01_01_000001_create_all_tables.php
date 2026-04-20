<?php

// ══════════════════════════════════════════════════════════════════
// FILE: database/migrations/2024_01_01_000001_create_faculties_table.php
// ══════════════════════════════════════════════════════════════════

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('hod_id')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('student_number')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', [
                'admin', 'lecturer', 'hod', 'finance',
                'registrar', 'senate', 'student',
            ])->default('student');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // Add FK for hod_id after users table exists
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('hod_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('academic_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);           // e.g. "2024/2025"
            $table->tinyInteger('semester');       // 1 or 2
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->tinyInteger('credit_units')->default(3);
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lecturer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->decimal('marks', 5, 2);
            $table->string('grade', 2);
            $table->decimal('grade_points', 3, 1);
            $table->string('remark', 50);
            $table->enum('status', [
                'draft', 'submitted', 'hod_approved', 'hod_rejected',
                'compiled', 'senate_approved', 'senate_rejected', 'published',
            ])->default('draft');
            $table->text('hod_comment')->nullable();
            $table->text('senate_comment')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('hod_actioned_at')->nullable();
            $table->timestamp('compiled_at')->nullable();
            $table->timestamp('senate_actioned_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // A student can only have one result per course per session
            $table->unique(['student_id', 'course_id', 'academic_session_id']);
        });

        Schema::create('finance_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_cleared')->default(false);
            $table->foreignId('cleared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cleared_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_session_id']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100);
            $table->string('auditable_type', 100)->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            // No updated_at — audit logs are immutable
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('finance_clearances');
        Schema::dropIfExists('results');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('academic_sessions');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
        
        Schema::enableForeignKeyConstraints();
    }
};
