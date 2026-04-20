<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\AuditLog;
use App\Models\FinanceClearance;
use App\Models\User;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function dashboard()
    {
        $session  = AcademicSession::active();
        $students = User::where('role', User::ROLE_STUDENT)
            ->with(['financeClearance' => fn($q) => $q->where('academic_session_id', $session?->id)])
            ->paginate(20);

        return view('finance.dashboard', compact('students', 'session'));
    }

    public function clear(Request $request, User $student)
    {
        $data = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'notes'               => 'nullable|string|max:500',
        ]);

        FinanceClearance::updateOrCreate(
            [
                'student_id'          => $student->id,
                'academic_session_id' => $data['academic_session_id'],
            ],
            [
                'is_cleared' => true,
                'cleared_by' => auth()->id(),
                'cleared_at' => now(),
                'notes'      => $data['notes'] ?? null,
            ]
        );

        AuditLog::record('finance.cleared', $student);

        return back()->with('success', "{$student->name} cleared for finance.");
    }

    public function flag(Request $request, User $student)
    {
        $data = $request->validate([
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'notes'               => 'required|string|max:500',
        ]);

        FinanceClearance::updateOrCreate(
            [
                'student_id'          => $student->id,
                'academic_session_id' => $data['academic_session_id'],
            ],
            [
                'is_cleared' => false,
                'cleared_by' => auth()->id(),
                'cleared_at' => null,
                'notes'      => $data['notes'],
            ]
        );

        return back()->with('warning', "{$student->name} flagged with outstanding fees.");
    }
}