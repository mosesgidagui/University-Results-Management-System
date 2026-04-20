@extends('layouts.app')
@section('title', 'Create Result')
@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Enter Student Result</h1>

    <form method="POST" action="{{ route('lecturer.results.store') }}" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="mb-4">
            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
            <select name="student_id" id="student_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('student_id') border-red-500 @enderror">
                <option value="">Select a student</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->name }} - {{ $student->student_number }}</option>
                @endforeach
            </select>
            @error('student_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
            <select name="course_id" id="course_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('course_id') border-red-500 @enderror">
                <option value="">Select a course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->title }}</option>
                @endforeach
            </select>
            @error('course_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="marks" class="block text-sm font-medium text-gray-700 mb-1">Marks (0-100)</label>
            <input type="number" name="marks" id="marks" min="0" max="100" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('marks') border-red-500 @enderror" />
            @error('marks')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <input type="hidden" name="academic_session_id" value="{{ $session?->id }}" />

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Save Result</button>
            <a href="{{ route('lecturer.dashboard') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">Cancel</a>
        </div>
    </form>
</div>
@endsection
