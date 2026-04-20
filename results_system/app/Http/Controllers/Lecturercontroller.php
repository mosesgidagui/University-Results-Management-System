<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Course;
use App\Models\AcademicSession;
use App\Models\User;
use App\Services\ResultService;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    public function __construct(private ResultService $service) {}

    public function dashboard()
    {
        $session = AcademicSession::active();
        $faculty = auth()->user()->department?->faculty;
        
        $stats = [
            'draft_results' => Result::where('lecturer_id', auth()->id())
                ->where('academic_session_id', $session?->id)
                ->where('status', Result::STATUS_DRAFT)
                ->count(),
            'submitted_results' => Result::where('lecturer_id', auth()->id())
                ->where('academic_session_id', $session?->id)
                ->where('status', Result::STATUS_SUBMITTED)
                ->count(),
        ];

        return view('lecturer.dashboard', compact('stats', 'session', 'faculty'));
    }

    public function index(Request $request)
    {
        $session = AcademicSession::active();
        $query = Result::where('lecturer_id', auth()->id())
            ->where('academic_session_id', $session?->id)
            ->with(['student', 'course']);

        // Search by student name or number
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }

        $results = $query->latest()->paginate(20);
        $courses = Course::where('lecturer_id', auth()->id())->get();

        return view('lecturer.index', compact('results', 'courses', 'session'));
    }

    public function create()
    {
        $courses  = Course::where('lecturer_id', auth()->id())->get();
        $students = User::where('role', User::ROLE_STUDENT)->orderBy('name')->get();
        $session  = AcademicSession::active();

        return view('lecturer.create', compact('courses', 'students', 'session'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id'          => 'required|exists:users,id',
            'course_id'           => 'required|exists:courses,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'marks'               => 'required|numeric|min:0|max:100',
        ]);

        $gradeInfo = Result::computeGrade((float) $data['marks']);

        Result::create([
            ...$data,
            'lecturer_id'  => auth()->id(),
            'grade'        => $gradeInfo['grade'],
            'grade_points' => $gradeInfo['points'],
            'remark'       => $gradeInfo['remark'],
            'status'       => Result::STATUS_DRAFT,
        ]);

        return redirect()->route('lecturer.index')
            ->with('success', 'Result saved as draft.');
    }

    public function edit(Result $result)
    {
        $this->authorize('update', $result);
        $courses  = Course::where('lecturer_id', auth()->id())->get();
        $students = User::where('role', User::ROLE_STUDENT)->orderBy('name')->get();

        return view('lecturer.edit', compact('result', 'courses', 'students'));
    }

    public function update(Request $request, Result $result)
    {
        $this->authorize('update', $result);

        $data = $request->validate([
            'marks' => 'required|numeric|min:0|max:100',
        ]);

        $gradeInfo = Result::computeGrade((float) $data['marks']);

        $result->update([
            'marks'        => $data['marks'],
            'grade'        => $gradeInfo['grade'],
            'grade_points' => $gradeInfo['points'],
            'remark'       => $gradeInfo['remark'],
        ]);

        return redirect()->route('lecturer.index')
            ->with('success', 'Result updated.');
    }

    public function submit(Result $result)
    {
        $this->authorize('submit', $result);
        $this->service->submitResult($result);

        return back()->with('success', 'Result submitted for HOD review.');
    }

    public function submitBulk(Request $request)
    {
        $ids = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'exists:results,id',
        ])['ids'];

        $results = Result::whereIn('id', $ids)
            ->where('lecturer_id', auth()->id())
            ->get();

        foreach ($results as $result) {
            if ($result->canBeSubmitted()) {
                $this->service->submitResult($result);
            }
        }

        return back()->with('success', count($ids) . ' result(s) submitted.');
    }

    public function destroy(Result $result)
    {
        $this->authorize('delete', $result);
        $result->delete();

        return back()->with('success', 'Result deleted successfully.');
    }

    public function performanceReport()
    {
        $session = AcademicSession::active();
        $courses = Course::where('lecturer_id', auth()->id())->get();

        $report = [];
        foreach ($courses as $course) {
            $results = Result::where('course_id', $course->id)
                ->where('academic_session_id', $session?->id)
                ->published()
                ->get();

            if ($results->isEmpty()) {
                continue;
            }

            $totalMarks = $results->sum('marks');
            $count = $results->count();
            $avg = $count > 0 ? $totalMarks / $count : 0;

            $gradeDistribution = [
                'A' => $results->where('grade', 'A')->count(),
                'B' => $results->where('grade', 'B')->count(),
                'C' => $results->where('grade', 'C')->count(),
                'D' => $results->where('grade', 'D')->count(),
                'F' => $results->where('grade', 'F')->count(),
            ];

            $report[] = [
                'course' => $course,
                'total_students' => $count,
                'average_mark' => round($avg, 2),
                'highest_mark' => $results->max('marks'),
                'lowest_mark' => $results->min('marks'),
                'pass_rate' => round(($results->where('marks', '>=', 50)->count() / $count) * 100, 1),
                'grade_distribution' => $gradeDistribution,
            ];
        }

        return view('lecturer.performance-report', compact('report', 'session'));
    }
}