<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Faculty;
use App\Models\Result;
use App\Models\User;
use App\Services\ResultService;
use Illuminate\Http\Request;

class RegistrarController extends Controller
{
    public function __construct(private ResultService $service) {}

    public function dashboard()
    {
        $session = AcademicSession::active();

        $approved = Result::where('status', Result::STATUS_HOD_APPROVED)
            ->where('academic_session_id', $session?->id)
            ->with(['student', 'course', 'lecturer'])
            ->paginate(20);

        // Get faculty student statistics
        $faculties = Faculty::with('departments')->get();
        $facultyStats = $faculties->map(function ($faculty) {
            return [
                'name' => $faculty->name,
                'code' => $faculty->code,
                'students' => User::where('role', User::ROLE_STUDENT)
                    ->whereHas('department', fn($q) => $q->where('faculty_id', $faculty->id))
                    ->count(),
            ];
        });

        return view('registrar.dashboard', compact('approved', 'session', 'facultyStats'));
    }

    public function compile(Request $request)
    {
        $ids   = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:results,id',
        ])['ids'];

        $count = $this->service->compileResults($ids);

        return back()->with('success', "{$count} result(s) compiled and forwarded to Senate.");
    }

    public function compiled()
    {
        $session = AcademicSession::active();

        $results = Result::whereIn('status', [
                Result::STATUS_COMPILED,
                Result::STATUS_SENATE_REJECTED,
                Result::STATUS_SENATE_APPROVED,
            ])
            ->where('academic_session_id', $session?->id)
            ->with(['student', 'course'])
            ->paginate(20);

        return view('registrar.dashboard', compact('results', 'session'));
    }

    public function publish(Request $request)
    {
        $ids   = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:results,id',
        ])['ids'];

        $count = $this->service->publishResults($ids);

        return back()->with('success', "{$count} result(s) published to the student portal.");
    }
}