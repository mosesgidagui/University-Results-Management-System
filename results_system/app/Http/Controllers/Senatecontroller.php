<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Result;
use App\Services\ResultService;
use Illuminate\Http\Request;

class SenateController extends Controller
{
    public function __construct(private ResultService $service) {}

    public function dashboard()
    {
        $session = AcademicSession::active();

        $results = Result::where('status', Result::STATUS_COMPILED)
            ->where('academic_session_id', $session?->id)
            ->with(['student', 'course', 'lecturer'])
            ->paginate(20);

        return view('senate.dashboard', compact('results', 'session'));
    }

    public function approve(Request $request)
    {
        $data = $request->validate([
            'ids'     => 'required|array',
            'ids.*'   => 'exists:results,id',
            'comment' => 'nullable|string|max:1000',
        ]);

        $count = $this->service->senateApprove($data['ids'], $data['comment'] ?? null);

        return back()->with('success', "{$count} result(s) approved by Senate.");
    }

    public function reject(Request $request)
    {
        $data = $request->validate([
            'ids'     => 'required|array',
            'ids.*'   => 'exists:results,id',
            'comment' => 'required|string|max:1000',
        ]);

        $count = $this->service->senateReject($data['ids'], $data['comment']);

        return back()->with('success', "{$count} result(s) returned to Registrar for correction.");
    }
}