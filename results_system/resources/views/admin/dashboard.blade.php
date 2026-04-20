@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Administrator Dashboard</h1>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Total Students</div>
            <div class="text-3xl font-bold text-blue-600">{{ $stats['total_students'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Total Lecturers</div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['total_lecturers'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Draft Results</div>
            <div class="text-3xl font-bold text-yellow-600">{{ $stats['results_draft'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Pending Results</div>
            <div class="text-3xl font-bold text-red-600">{{ $stats['results_pending'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Admin Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Submitted Results</h2>
            <p class="text-gray-600 mb-4">Review and forward results submitted by lecturers to HOD for verification.</p>
            <a href="{{ route('admin.submitted-results') }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Manage Results
            </a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Manage Users</h2>
            <p class="text-gray-600 mb-4">Create, edit, and delete user accounts. Assign roles and permissions.</p>
            <a href="{{ route('admin.users') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Go to Users
            </a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Audit Logs</h2>
            <p class="text-gray-600 mb-4">View system activity and user actions for compliance and debugging.</p>
            <a href="{{ route('admin.audit-logs') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                View Logs
            </a>
        </div>
    </div>
</div>
@endsection
