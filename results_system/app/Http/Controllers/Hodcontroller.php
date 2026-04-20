<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\AcademicSession;
use App\Services\ResultService;
use Illuminate\Http\Request;

class HodController extends Controller
{
    public function __construct(private ResultService $service) {}

    public function dashboard()
    {
        $deptId  = auth()->user()->department_id;
        $facultyId = auth()->user()->faculty_id;
        $session = AcademicSession::active();

        $results = Result::where('status', Result::STATUS_SUBMITTED)
            ->where('academic_session_id', $session?->id)
            ->whereHas('course', fn($q) => $q->where('department_id', $deptId))
            ->with(['student', 'course', 'lecturer'])
            ->paginate(20);

        // Get count of students affiliated to this department only
        $affiliatedStudentsCount = \App\Models\User::where('role', \App\Models\User::ROLE_STUDENT)
            ->where('department_id', $deptId)
            ->count();

        return view('hod.dashboard', compact('results', 'session', 'affiliatedStudentsCount'));
    }

    public function submissions()
    {
        $deptId  = auth()->user()->department_id;
        $session = AcademicSession::active();

        $results = Result::where('status', Result::STATUS_SUBMITTED)
            ->where('academic_session_id', $session?->id)
            ->whereHas('course', fn($q) => $q->where('department_id', $deptId))
            ->with(['student', 'course', 'lecturer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending'  => Result::where('status', Result::STATUS_SUBMITTED)->whereHas('course', fn($q) => $q->where('department_id', $deptId))->count(),
            'approved' => Result::where('status', Result::STATUS_HOD_APPROVED)->whereHas('course', fn($q) => $q->where('department_id', $deptId))->count(),
            'rejected' => Result::where('status', Result::STATUS_HOD_REJECTED)->whereHas('course', fn($q) => $q->where('department_id', $deptId))->count(),
        ];

        return view('hod.submissions', compact('results', 'session', 'stats'));
    }

    public function approve(Request $request, Result $result)
    {
        $data = $request->validate(['comment' => 'nullable|string|max:500']);
        $this->service->hodApprove($result, $data['comment'] ?? null);

        return back()->with('success', 'Result approved.');
    }

    public function reject(Request $request, Result $result)
    {
        $data = $request->validate(['comment' => 'required|string|max:500']);
        $this->service->hodReject($result, $data['comment']);

        return back()->with('success', 'Result returned to lecturer.');
    }

    public function approveBulk(Request $request)
    {
        $ids = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:results,id',
        ])['ids'];

        foreach (Result::whereIn('id', $ids)->get() as $result) {
            if ($result->canHodAction()) {
                $this->service->hodApprove($result);
            }
        }

        return back()->with('success', count($ids) . ' result(s) approved.');
    }
}