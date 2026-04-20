{{-- resources/views/lecturer/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add Result')
@section('content')
<div class="max-w-xl">
    <h1 class="text-xl font-semibold mb-6">Add Student Result</h1>

    <form method="POST" action="{{ route('lecturer.store') }}" class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1">Academic Session</label>
            <input type="hidden" name="academic_session_id" value="{{ $session?->id }}">
            <input type="text" value="{{ $session?->name }} — Semester {{ $session?->semester }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50" readonly>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Course</label>
            <select name="course_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                <option value="">— Select course —</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}">{{ $c->code }} — {{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Student</label>
            <select name="student_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                <option value="">— Select student —</option>
                @foreach($students as $s)
                    <option value="{{ $s->id }}">{{ $s->student_number }} — {{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Marks (0–100)</label>
            <input type="number" name="marks" min="0" max="100" step="0.01"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            <p class="text-xs text-gray-400 mt-1">Grade is auto-computed: A(80+) B(70+) C(60+) D(50+) F(&lt;50)</p>
        </div>
        <div class="flex gap-3 pt-2">
            <button class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700">Save Draft</button>
            <a href="{{ route('lecturer.index') }}" class="text-sm text-gray-500 px-4 py-2 hover:underline">Cancel</a>
        </div>
    </form>
</div>
@endsection


{{-- resources/views/hod/index.blade.php --}}
{{-- SAVE AS: resources/views/hod/index.blade.php --}}
