@extends('layouts.app')
@section('title', 'Edit Result')
@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Edit Result</h1>

    <form method="POST" action="{{ route('lecturer.results.update', $result) }}" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PATCH')

        <div class="mb-4">
            <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student</label>
            <div class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                <p class="text-gray-700">{{ $result->student->name }}</p>
                <p class="text-sm text-gray-500">{{ $result->student->student_number }}</p>
            </div>
            <input type="hidden" name="student_id" value="{{ $result->student_id }}">
            <p class="text-xs text-gray-500 mt-1">Cannot be changed after creation</p>
        </div>

        <div class="mb-4">
            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
            <div class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                <p class="text-gray-700">{{ $result->course->code }}</p>
                <p class="text-sm text-gray-500">{{ $result->course->title }}</p>
            </div>
            <input type="hidden" name="course_id" value="{{ $result->course_id }}">
            <p class="text-xs text-gray-500 mt-1">Cannot be changed after creation</p>
        </div>

        <div class="mb-4">
            <label for="marks" class="block text-sm font-medium text-gray-700 mb-1">Marks (0-100)</label>
            <input type="number" name="marks" id="marks" min="0" max="100" step="0.01" value="{{ old('marks', $result->marks) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('marks') border-red-500 @enderror" required onchange="updateGrade()" />
            @error('marks')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6 p-4 bg-gray-100 rounded-lg">
            <p class="text-sm text-gray-600 mb-2"><strong>Current Grade Breakdown:</strong></p>
            <p class="text-sm text-gray-600"><strong>Marks:</strong> {{ $result->marks }}</p>
            <p class="text-sm text-gray-600"><strong>Grade:</strong> <span class="font-bold text-lg">{{ $result->grade }}</span></p>
            <p class="text-sm text-gray-600"><strong>Grade Points:</strong> {{ $result->grade_points }}</p>
            <p class="text-sm text-gray-600"><strong>Remark:</strong> {{ $result->remark }}</p>
            <p class="text-sm text-gray-600 mt-3"><strong>Status:</strong> 
                <span class="px-2 py-1 rounded text-sm
                    @if($result->status === 'draft') bg-gray-200 text-gray-800
                    @elseif($result->status === 'submitted') bg-blue-200 text-blue-800
                    @elseif($result->status === 'hod_approved') bg-green-200 text-green-800
                    @elseif($result->status === 'hod_rejected') bg-red-200 text-red-800
                    @else bg-gray-200 text-gray-800
                    @endif">
                    {{ str_replace('_', ' ', ucfirst($result->status)) }}
                </span>
            </p>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Update Result</button>
            <a href="{{ route('lecturer.results') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">Cancel</a>
        </div>
    </form>

    <!-- Additional Actions -->
    <div class="mt-6 space-y-3">
        @if($result->canBeSubmitted())
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-blue-900 text-sm mb-3">This result is in {{ $result->status }} status. You can submit it for HOD review.</p>
                <form method="POST" action="{{ route('lecturer.results.submit', $result) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit for Review</button>
                </form>
            </div>
        @endif
        
        @if($result->status === 'draft')
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <p class="text-red-900 text-sm mb-3">This result is in draft. You can delete it if needed.</p>
                <form method="POST" action="{{ route('lecturer.results.destroy', $result) }}" style="display:inline;" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete Result</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
