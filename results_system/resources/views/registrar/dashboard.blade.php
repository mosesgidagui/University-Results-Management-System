@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Academic Registrar Dashboard</h1>

    <!-- Faculty Statistics -->
    <div class="mb-8">
        <h2 class="text-xl font-bold mb-4">Faculty Student Enrollment</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($facultyStats as $faculty)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-gray-500 text-sm font-medium">{{ $faculty['name'] }}</div>
                <div class="text-3xl font-bold text-blue-600 mt-2">{{ $faculty['students'] }}</div>
                <div class="text-gray-400 text-xs mt-2">{{ $faculty['code'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Approved Results</div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Compiled Results</div>
            <div class="text-3xl font-bold text-blue-600">{{ $stats['compiled'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">For Senate Review</div>
            <div class="text-3xl font-bold text-orange-600">{{ $stats['for_senate'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Registrar Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Compile Results</h2>
            <p class="text-gray-600 mb-4">Compile approved results from all departments and prepare for senate review.</p>
            <a href="{{ route('registrar.results') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                View Results
            </a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Compiled Results</h2>
            <p class="text-gray-600 mb-4">View and manage compiled results ready for senate approval.</p>
            <a href="{{ route('registrar.compiled-results') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                View Compiled
            </a>
        </div>
    </div>
</div>
@endsection
