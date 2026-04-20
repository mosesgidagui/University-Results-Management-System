@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Head of Department Dashboard</h1>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Affiliated Students</div>
            <div class="text-3xl font-bold text-blue-600">{{ $affiliatedStudentsCount ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Pending Submissions</div>
            <div class="text-3xl font-bold text-orange-600">{{ $stats['pending'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Approved</div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Rejected</div>
            <div class="text-3xl font-bold text-red-600">{{ $stats['rejected'] ?? 0 }}</div>
        </div>
    </div>

    <!-- HOD Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Review Submissions</h2>
        <p class="text-gray-600 mb-4">Review and approve/reject results submitted by lecturers in your department.</p>
        <a href="{{ route('hod.submissions') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Review Submissions
        </a>
    </div>
</div>
@endsection
