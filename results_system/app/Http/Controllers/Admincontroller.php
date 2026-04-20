<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Result;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $admin = auth()->user();
        $facultyId = $admin->faculty_id;
        $session = AcademicSession::active();

        // Get faculty-specific stats (or all stats if system admin)
        $stats = [
            'total_students'    => User::where('role', User::ROLE_STUDENT)
                ->when($facultyId, function($q) use ($facultyId) {
                    $q->whereHas('department', fn($q2) => $q2->where('faculty_id', $facultyId));
                })
                ->count(),
            'total_lecturers'   => User::where('role', User::ROLE_LECTURER)
                ->when($facultyId, function($q) use ($facultyId) {
                    $q->whereHas('department', fn($q2) => $q2->where('faculty_id', $facultyId));
                })
                ->count(),
            'results_draft'     => Result::where('status', Result::STATUS_DRAFT)
                ->when($facultyId, function($q) use ($facultyId) {
                    $q->whereHas('course', fn($q2) => $q2->whereHas('department', fn($q3) => $q3->where('faculty_id', $facultyId)));
                })
                ->count(),
            'results_pending'   => Result::where('status', Result::STATUS_SUBMITTED)
                ->when($facultyId, function($q) use ($facultyId) {
                    $q->whereHas('course', fn($q2) => $q2->whereHas('department', fn($q3) => $q3->where('faculty_id', $facultyId)));
                })
                ->count(),
            'results_published' => Result::where('status', Result::STATUS_PUBLISHED)
                ->when($facultyId, function($q) use ($facultyId) {
                    $q->whereHas('course', fn($q2) => $q2->whereHas('department', fn($q3) => $q3->where('faculty_id', $facultyId)));
                })
                ->count(),
        ];

        // Get pending notifications from HOD and Senate actions
        $hodApprovals = Result::where('status', Result::STATUS_HOD_APPROVED)
            ->where('academic_session_id', $session?->id)
            ->where('hod_actioned_at', '>=', now()->subHours(2))
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereHas('course', fn($q2) => $q2->whereHas('department', fn($q3) => $q3->where('faculty_id', $facultyId)));
            })
            ->count();

        $senateApprovals = Result::where('status', Result::STATUS_SENATE_APPROVED)
            ->where('academic_session_id', $session?->id)
            ->where('senate_actioned_at', '>=', now()->subHours(2))
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereHas('course', fn($q2) => $q2->whereHas('department', fn($q3) => $q3->where('faculty_id', $facultyId)));
            })
            ->count();

        $senateRejections = Result::where('status', Result::STATUS_SENATE_REJECTED)
            ->where('academic_session_id', $session?->id)
            ->where('senate_actioned_at', '>=', now()->subHours(2))
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereHas('course', fn($q2) => $q2->whereHas('department', fn($q3) => $q3->where('faculty_id', $facultyId)));
            })
            ->count();

        return view('admin.index', compact('stats', 'session', 'hodApprovals', 'senateApprovals', 'senateRejections'));
    }

    public function users()
    {
        $users = User::with('department')->paginate(25);
        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        $departments = Department::all();
        return view('admin.create-user', compact('departments'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users',
            'role'           => 'required|in:' . implode(',', User::ROLES),
            'student_number' => 'nullable|string|max:50',
            'department_id'  => 'nullable|exists:departments,id',
            'password'       => 'required|min:8|confirmed',
        ]);

        User::create([
            ...$data,
            'password'  => $data['password'],
            'is_active' => true,
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated.');
    }

    public function auditLog()
    {
        $logs = AuditLog::with('user')->latest()->paginate(50);
        return view('admin.audit', compact('logs'));
    }

    public function sessions()
    {
        $sessions = AcademicSession::latest()->paginate(10);
        return view('admin.sessions', compact('sessions'));
    }

    public function storeSession(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'semester'   => 'required|in:1,2',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        AcademicSession::query()->update(['is_active' => false]);

        AcademicSession::create([...$data, 'is_active' => true]);

        return back()->with('success', 'Academic session created and set as active.');
    }

    // ── Results Management ─────────────────────────────────────────
    public function submittedResults(Request $request)
    {
        $admin = auth()->user();
        $facultyId = $admin->faculty_id;

        $query = Result::where('status', Result::STATUS_SUBMITTED)
            ->with(['student', 'course', 'lecturer', 'academicSession'])
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereHas('course', function($q2) use ($facultyId) {
                    $q2->whereHas('department', function($q3) use ($facultyId) {
                        $q3->where('faculty_id', $facultyId);
                    });
                });
            });

        // Filter by department if provided
        if ($request->filled('department_id')) {
            $query->whereHas('course', function($q) use ($request) {
                $q->where('department_id', $request->input('department_id'));
            });
        }

        // Filter by lecturer if provided
        if ($request->filled('lecturer_id')) {
            $query->where('lecturer_id', $request->input('lecturer_id'));
        }

        $results = $query->paginate(25);
        $departments = Department::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))->get();
        $lecturers = User::where('role', User::ROLE_LECTURER)
            ->when($facultyId, fn($q) => $q->whereHas('department', fn($q2) => $q2->where('faculty_id', $facultyId)))
            ->get();

        return view('admin.submitted-results', compact('results', 'departments', 'lecturers'));
    }

    public function forwardToHod(Result $result)
    {
        if ($result->status !== Result::STATUS_SUBMITTED) {
            return back()->with('error', 'Only submitted results can be forwarded to HOD.');
        }

        $result->update([
            'status' => Result::STATUS_HOD_APPROVED,
            'hod_actioned_at' => now(),
        ]);

        AuditLog::record('result.forwarded_to_hod', $result,
            ['status' => Result::STATUS_SUBMITTED],
            ['status' => Result::STATUS_HOD_APPROVED]
        );

        return back()->with('success', 'Result forwarded to HOD for review.');
    }

    public function forwardBulkToHod(Request $request)
    {
        $data = $request->validate([
            'result_ids' => 'required|array|min:1',
            'result_ids.*' => 'required|integer|exists:results,id'
        ]);

        $resultIds = $data['result_ids'];

        $count = Result::whereIn('id', $resultIds)
            ->where('status', Result::STATUS_SUBMITTED)
            ->update([
                'status' => Result::STATUS_HOD_APPROVED,
                'hod_actioned_at' => now(),
            ]);

        if ($count === 0) {
            return back()->with('error', 'No results were forwarded. Results must have "submitted" status.');
        }

        return back()->with('success', "{$count} result(s) forwarded to HOD for review.");
    }

    // ── Result Viewer Rights Management ────────────────────────────
    /**
     * Show students with their result access/clearance status (faculty-specific)
     */
    public function resultAccessManagement(Request $request)
    {
        $admin = auth()->user();
        $facultyId = $admin->faculty_id;
        $session = AcademicSession::active();

        $query = User::where('role', User::ROLE_STUDENT)
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereHas('results', function($q2) use ($facultyId) {
                    $q2->whereHas('course', fn($q3) => $q3->whereHas('department', fn($q4) => $q4->where('faculty_id', $facultyId)));
                }, '>=', 1);
            });

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        $students = $query->with(['financeClearance' => function($q) use ($session) {
            $q->where('academic_session_id', $session?->id);
        }])->paginate(25);

        return view('admin.result-access', compact('students', 'session'));
    }

    /**
     * Grant result viewer rights to a student (clear for results)
     * Only allowed if Senate has approved the student's results
     */
    public function grantResultAccess(Request $request, User $student)
    {
        if (!$student->isStudent()) {
            return back()->with('error', 'Only students can be granted result access.');
        }

        $admin = auth()->user();
        $facultyId = $admin->faculty_id;
        $session = AcademicSession::active();
        if (!$session) {
            return back()->with('error', 'No active academic session.');
        }

        // CRITICAL CHECK: Verify Senate has approved this student's results
        $senateApprovedCount = Result::where('student_id', $student->id)
            ->where('academic_session_id', $session->id)
            ->where('status', Result::STATUS_SENATE_APPROVED)
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereHas('course', fn($q2) => $q2->whereHas('department', fn($q3) => $q3->where('faculty_id', $facultyId)));
            })
            ->count();

        if ($senateApprovedCount === 0) {
            $message = $facultyId ? 'from this faculty. Wait for Senate approval before granting viewer rights.' : 'Wait for Senate approval before granting viewer rights.';
            return back()->with('error', 'Cannot grant access: Senate has not yet approved this student\'s results ' . $message);
        }

        $data = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $clearance = \App\Models\FinanceClearance::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_session_id' => $session->id,
            ],
            [
                'is_cleared' => true,
                'cleared_by' => auth()->id(),
                'cleared_at' => now(),
                'notes' => $data['notes'] ?? 'Result access granted after Senate approval.',
            ]
        );

        AuditLog::record('result.access_granted', $student, [], [
            'academic_session' => $session->name,
            'cleared_by' => auth()->user()->name,
            'requirement' => 'Senate approved',
        ]);

        return back()->with('success', "{$student->name} can now view results. Senate approval verified.");
    }

    /**
     * Revoke result viewer rights from a student (flag for results)
     */
    public function revokeResultAccess(Request $request, User $student)
    {
        if (!$student->isStudent()) {
            return back()->with('error', 'Only students can have result access revoked.');
        }

        $session = AcademicSession::active();
        if (!$session) {
            return back()->with('error', 'No active academic session.');
        }

        $data = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $clearance = \App\Models\FinanceClearance::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_session_id' => $session->id,
            ],
            [
                'is_cleared' => false,
                'cleared_by' => auth()->id(),
                'cleared_at' => null,
                'notes' => $data['reason'],
            ]
        );

        AuditLog::record('result.access_revoked', $student, [], [
            'reason' => $data['reason'],
            'academic_session' => $session->name,
        ]);

        return back()->with('warning', "{$student->name} can no longer view results due to pending dues. Reason: {$data['reason']}");
    }

    // ── Workflow: HOD to Senate ────────────────────────────────────
    /**
     * Show HOD approvals awaiting admin action to forward to Senate
     */
    public function hodApprovals()
    {
        $admin = auth()->user();
        $facultyId = $admin->faculty_id;
        $session = AcademicSession::active();

        $results = Result::where('status', Result::STATUS_HOD_APPROVED)
            ->where('academic_session_id', $session?->id)
            ->with(['student', 'course', 'lecturer', 'academicSession'])
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereHas('course', function($q2) use ($facultyId) {
                    $q2->whereHas('department', function($q3) use ($facultyId) {
                        $q3->where('faculty_id', $facultyId);
                    });
                });
            })
            ->orderBy('hod_actioned_at', 'desc')
            ->paginate(25);

        $count = Result::where('status', Result::STATUS_HOD_APPROVED)
            ->where('academic_session_id', $session?->id)
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereHas('course', function($q2) use ($facultyId) {
                    $q2->whereHas('department', function($q3) use ($facultyId) {
                        $q3->where('faculty_id', $facultyId);
                    });
                });
            })
            ->count();

        $notification = "HOD has approved {$count} result(s) awaiting your action to forward to Senate.";

        return view('admin.hod-approvals', compact('results', 'session', 'notification'));
    }

    /**
     * Forward HOD-approved result to Senate (via Registrar compile step)
     */
    public function forwardToSenate(Result $result)
    {
        if ($result->status !== Result::STATUS_HOD_APPROVED) {
            return back()->with('error', 'Only HOD-approved results can be forwarded to Senate.');
        }

        // Automatically compile and move toward Senate
        $result->update([
            'status' => Result::STATUS_COMPILED,
            'compiled_at' => now(),
        ]);

        AuditLog::record('result.compiled_for_senate', $result,
            ['status' => Result::STATUS_HOD_APPROVED],
            ['status' => Result::STATUS_COMPILED]
        );

        return back()->with('success', 'Result compiled and forwarded to Senate for review.');
    }

    /**
     * Bulk forward HOD-approved results to Senate
     */
    public function forwardBulkToSenate(Request $request)
    {
        $data = $request->validate([
            'result_ids' => 'required|array|min:1',
            'result_ids.*' => 'required|integer|exists:results,id'
        ]);

        $resultIds = $data['result_ids'];

        $count = Result::whereIn('id', $resultIds)
            ->where('status', Result::STATUS_HOD_APPROVED)
            ->update([
                'status' => Result::STATUS_COMPILED,
                'compiled_at' => now(),
            ]);

        if ($count === 0) {
            return back()->with('error', 'No results were forwarded. Results must have HOD-approved status.');
        }

        return back()->with('success', "{$count} result(s) compiled and forwarded to Senate for review.");
    }

    /**
     * Show Senate actions (approvals/rejections) awaiting admin attention
     */
    public function senateActions()
    {
        $admin = auth()->user();
        $facultyId = $admin->faculty_id;
        $session = AcademicSession::active();

        $approved = Result::where('status', Result::STATUS_SENATE_APPROVED)
            ->where('academic_session_id', $session?->id)
            ->with(['student', 'course', 'lecturer', 'academicSession'])
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereHas('course', function($q2) use ($facultyId) {
                    $q2->whereHas('department', function($q3) use ($facultyId) {
                        $q3->where('faculty_id', $facultyId);
                    });
                });
            })
            ->orderBy('senate_actioned_at', 'desc')
            ->get();

        $rejected = Result::where('status', Result::STATUS_SENATE_REJECTED)
            ->where('academic_session_id', $session?->id)
            ->with(['student', 'course', 'lecturer', 'academicSession'])
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereHas('course', function($q2) use ($facultyId) {
                    $q2->whereHas('department', function($q3) use ($facultyId) {
                        $q3->where('faculty_id', $facultyId);
                    });
                });
            })
            ->orderBy('senate_actioned_at', 'desc')
            ->get();

        $totalApproved = $approved->count();
        $totalRejected = $rejected->count();

        $notification = "";
        if ($totalApproved > 0) {
            $notification .= "✓ Senate has approved {$totalApproved} result(s). ";
        }
        if ($totalRejected > 0) {
            $notification .= "⚠ Senate has rejected {$totalRejected} result(s).";
        }

        return view('admin.senate-actions', compact('approved', 'rejected', 'session', 'notification', 'totalApproved', 'totalRejected'));
    }
}