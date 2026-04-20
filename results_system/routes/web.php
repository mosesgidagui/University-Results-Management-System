<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\HodController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\RegistrarController;
use App\Http\Controllers\SenateController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// ── Authenticated routes ──────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Role-based dashboards
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        return match($user->role) {
            \App\Models\User::ROLE_ADMIN    => redirect()->route('admin.dashboard'),
            \App\Models\User::ROLE_LECTURER => redirect()->route('lecturer.dashboard'),
            \App\Models\User::ROLE_HOD      => redirect()->route('hod.dashboard'),
            \App\Models\User::ROLE_FINANCE  => redirect()->route('finance.dashboard'),
            \App\Models\User::ROLE_REGISTRAR => redirect()->route('registrar.dashboard'),
            \App\Models\User::ROLE_SENATE   => redirect()->route('senate.dashboard'),
            \App\Models\User::ROLE_STUDENT  => redirect()->route('student.dashboard'),
            default => redirect()->route('login'),
        };
    })->name('dashboard');

    // ── ADMINISTRATOR ROUTES ──────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/sessions', [AdminController::class, 'sessions'])->name('sessions');
        Route::post('/sessions', [AdminController::class, 'storeSession'])->name('sessions.store');
        Route::get('/audit-logs', [AdminController::class, 'auditLog'])->name('audit-logs');
        Route::get('/submitted-results', [AdminController::class, 'submittedResults'])->name('submitted-results');
        Route::post('/results/{result}/forward-to-hod', [AdminController::class, 'forwardToHod'])->name('results.forward-to-hod');
        Route::post('/results/bulk-forward-to-hod', [AdminController::class, 'forwardBulkToHod'])->name('results.bulk-forward-to-hod');
        Route::get('/hod-approvals', [AdminController::class, 'hodApprovals'])->name('hod-approvals');
        Route::post('/results/{result}/forward-to-senate', [AdminController::class, 'forwardToSenate'])->name('results.forward-to-senate');
        Route::post('/results/bulk-forward-to-senate', [AdminController::class, 'forwardBulkToSenate'])->name('results.bulk-forward-to-senate');
        Route::get('/senate-actions', [AdminController::class, 'senateActions'])->name('senate-actions');
        Route::get('/result-access', [AdminController::class, 'resultAccessManagement'])->name('result-access');
        Route::post('/students/{student}/grant-result-access', [AdminController::class, 'grantResultAccess'])->name('students.grant-result-access');
        Route::post('/students/{student}/revoke-result-access', [AdminController::class, 'revokeResultAccess'])->name('students.revoke-result-access');
    });

    // ── LECTURER ROUTES ───────────────────────────────────────────
    Route::middleware('role:lecturer')->prefix('lecturer')->name('lecturer.')->group(function () {
        Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('dashboard');
        Route::get('/results', [LecturerController::class, 'index'])->name('results');
        Route::get('/results/create', [LecturerController::class, 'create'])->name('results.create');
        Route::post('/results', [LecturerController::class, 'store'])->name('results.store');
        Route::get('/results/{result}/edit', [LecturerController::class, 'edit'])->name('results.edit');
        Route::patch('/results/{result}', [LecturerController::class, 'update'])->name('results.update');
        Route::post('/results/{result}/submit', [LecturerController::class, 'submit'])->name('results.submit');
        Route::post('/results/bulk-submit', [LecturerController::class, 'submitBulk'])->name('results.bulk-submit');
        Route::delete('/results/{result}', [LecturerController::class, 'destroy'])->name('results.destroy');
        Route::get('/performance-report', [LecturerController::class, 'performanceReport'])->name('performance-report');
    });

    // ── HEAD OF DEPARTMENT ROUTES ─────────────────────────────────
    Route::middleware('role:hod')->prefix('hod')->name('hod.')->group(function () {
        Route::get('/dashboard', [HodController::class, 'dashboard'])->name('dashboard');        Route::get('/submissions', [HodController::class, 'submissions'])->name('submissions');        Route::patch('/results/{result}/approve', [HodController::class, 'approve'])->name('results.approve');
        Route::patch('/results/{result}/reject', [HodController::class, 'reject'])->name('results.reject');
    });

    // ── FINANCE ROUTES ────────────────────────────────────────────
    Route::middleware('role:finance')->prefix('finance')->name('finance.')->group(function () {
        Route::get('/dashboard', [FinanceController::class, 'dashboard'])->name('dashboard');
        Route::post('/students/{student}/clear', [FinanceController::class, 'clear'])->name('students.clear');
        Route::post('/students/{student}/flag', [FinanceController::class, 'flag'])->name('students.flag');
    });


    // ── ACADEMIC REGISTRAR ROUTES ─────────────────────────────────
    Route::middleware('role:registrar')->prefix('registrar')->name('registrar.')->group(function () {
        Route::get('/dashboard', [RegistrarController::class, 'dashboard'])->name('dashboard');
        Route::post('/results/compile', [RegistrarController::class, 'compile'])->name('results.compile');
        Route::get('/compiled-results', [RegistrarController::class, 'compiled'])->name('compiled-results');
        Route::post('/results/publish', [RegistrarController::class, 'publish'])->name('results.publish');
    });

    // ── SENATE ROUTES ─────────────────────────────────────────────
    Route::middleware('role:senate')->prefix('senate')->name('senate.')->group(function () {
        Route::get('/dashboard', [SenateController::class, 'dashboard'])->name('dashboard');
        Route::post('/results/approve', [SenateController::class, 'approve'])->name('results.approve');
        Route::post('/results/reject', [SenateController::class, 'reject'])->name('results.reject');
    });

    // ── STUDENT ROUTES ────────────────────────────────────────────
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');        Route::get('/results', [StudentController::class, 'results'])->name('results');    });
});

require __DIR__.'/auth.php';
