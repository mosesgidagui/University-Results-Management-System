@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            @if($faculty)
                <p class="text-lg text-gray-600 font-medium">{{ $faculty->name }}</p>
            @endif
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="text-gray-500 text-sm font-medium">Draft Results</div>
            <div class="text-4xl font-bold text-blue-600 mt-2">{{ $stats['draft_results'] }}</div>
            <p class="text-xs text-gray-500 mt-2">Awaiting submission</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="text-gray-500 text-sm font-medium">Submitted Results</div>
            <div class="text-4xl font-bold text-yellow-600 mt-2">{{ $stats['submitted_results'] }}</div>
            <p class="text-xs text-gray-500 mt-2">Awaiting admin verification</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold">Upload Marks</h2>
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
            </div>
            <p class="text-gray-600 text-sm mb-4">Upload student marks for your courses</p>
            <a href="{{ route('lecturer.results.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                Add Result
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold">My Results</h2>
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="text-gray-600 text-sm mb-4">View and manage all results</p>
            <a href="{{ route('lecturer.results') }}" class="inline-block bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 text-sm">
                View Results
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold">Performance</h2>
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <p class="text-gray-600 text-sm mb-4">View class performance reports</p>
            <a href="{{ route('lecturer.performance-report') }}" class="inline-block bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 text-sm">
                View Reports
            </a>
        </div>
    </div>

    <!-- Active Session Info -->
    @if($session)
    <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-blue-900">Active Academic Session</h3>
                <p class="mt-2 text-sm text-blue-700">
                    <strong>{{ $session->name }}</strong> - Semester {{ $session->semester }}
                </p>
                <p class="text-sm text-blue-700">
                    Start: {{ $session->start_date->format('M d, Y') }} | End: {{ $session->end_date->format('M d, Y') }}
                </p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-yellow-50 border-l-4 border-yellow-600 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M7.08 6.06A9 9 0 1 0 20.94 19.94"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-yellow-900">No Active Session</h3>
                <p class="mt-2 text-sm text-yellow-700">
                    There is currently no active academic session. Contact the administrator.
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
