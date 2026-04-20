<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\FinanceClearance;
use App\Models\Result;
use App\Services\ResultService;

class StudentController extends Controller
{
    public function __construct(private ResultService $service) {}

    public function dashboard()
    {
        $session = AcademicSession::active();
        $student = auth()->user();
        $cleared = $this->service->studentIsCleared($student->id, $session?->id);

        $results = $cleared
            ? Result::forStudent($student->id)
                ->where('academic_session_id', $session?->id)
                ->published()
                ->with('course')
                ->get()
            : collect();

        $gpa = $cleared ? $this->service->calculateGPA($student->id, $session->id) : null;

        $finance = FinanceClearance::where('student_id', $student->id)
            ->where('academic_session_id', $session?->id)
            ->first();

        // Get faculty information
        $faculty = $student->department?->faculty;
        $department = $student->department;

        // Calculate stats
        $stats = [
            'published' => $results->count(),
            'clearance' => $finance?->cleared_at ? 'Cleared' : 'Pending',
        ];

        return view('student.dashboard', compact('results', 'gpa', 'cleared', 'finance', 'session', 'faculty', 'department', 'stats'));
    }

    public function results()
    {
        $session = AcademicSession::active();
        $student = auth()->user();
        $cleared = $this->service->studentIsCleared($student->id, $session?->id);

        $results = $cleared
            ? Result::forStudent($student->id)
                ->where('academic_session_id', $session?->id)
                ->published()
                ->with('course')
                ->orderBy('created_at', 'desc')
                ->paginate(20)
            : collect();

        $gpa = $cleared ? $this->service->calculateGPA($student->id, $session->id) : null;

        return view('student.results', compact('results', 'gpa', 'cleared', 'session'));
    }
}