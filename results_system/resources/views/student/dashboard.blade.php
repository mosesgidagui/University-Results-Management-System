@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Student Dashboard</h1>

    <!-- Student & Faculty Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Student Info Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-700">Student Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-gray-600 text-sm">Student Number</p>
                    <p class="text-lg font-bold">{{ auth()->user()->student_number }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Full Name</p>
                    <p class="text-lg font-bold">{{ auth()->user()->name }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Department</p>
                    <p class="text-lg font-bold">{{ $department?->name ?? 'Not Assigned' }}</p>
                    <p class="text-xs text-gray-500">{{ $department?->code ?? '' }}</p>
                </div>
            </div>
        </div>

        <!-- Faculty Info Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4 text-gray-700">Faculty Affiliation</h3>
            @if($faculty)
                <div class="space-y-3">
                    <div>
                        <p class="text-gray-600 text-sm">Faculty Name</p>
                        <p class="text-lg font-bold text-blue-600">{{ $faculty->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Faculty Code</p>
                        <p class="text-lg font-bold">{{ $faculty->code }}</p>
                    </div>
                    <div class="pt-2 border-t">
                        <p class="text-gray-600 text-sm">Head of Department</p>
                        <p class="text-lg font-semibold">{{ $department?->hod?->name ?? 'Not Assigned' }}</p>
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-red-600 font-semibold">Faculty Not Assigned</p>
                    <p class="text-gray-600 text-sm mt-2">Please contact the registrar to assign a faculty.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Published Results</div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['published'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Current GPA</div>
            <div class="text-3xl font-bold text-blue-600">{{ $gpa ? number_format($gpa, 2) : 'N/A' }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Finance Status</div>
            <div class="text-lg font-bold {{ $finance ? 'text-green-600' : 'text-orange-600' }}">
                {{ $finance ? 'Cleared' : 'Pending' }}
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Academic Session</div>
            <div class="text-lg font-bold text-purple-600">{{ $session?->name ?? 'N/A' }}</div>
        </div>
    </div>

    <!-- Clearance Status Details -->
    <div class="grid grid-cols-1 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Finance Clearance</h3>
            @if($finance)
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-600 rounded-full mr-2"></div>
                    <span class="text-green-600 font-semibold">Cleared</span>
                </div>
                <p class="text-gray-600 text-sm mt-2">Cleared on {{ $finance->cleared_at?->format('M d, Y') }}</p>
            @else
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-600 rounded-full mr-2"></div>
                    <span class="text-red-600 font-semibold">Pending</span>
                </div>
                <p class="text-gray-600 text-sm mt-2">Please clear outstanding fees to view your results</p>
            @endif
        </div>
    </div>

    <!-- Results Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Your Examination Results</h2>
        
        @if($cleared)
            @if($results->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-300">
                                <th class="text-left py-2 px-4">Course</th>
                                <th class="text-left py-2 px-4">Marks</th>
                                <th class="text-left py-2 px-4">Grade</th>
                                <th class="text-left py-2 px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                            <tr class="border-b border-gray-200">
                                <td class="py-3 px-4">{{ $result->course->name }}</td>
                                <td class="py-3 px-4">{{ $result->marks }}</td>
                                <td class="py-3 px-4 font-bold">{{ $result->grade }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded text-sm font-semibold bg-green-100 text-green-800">
                                        Published
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-600">No results published yet.</p>
            @endif

            <div class="mt-4">
                <a href="{{ route('student.results') }}" class="text-blue-600 hover:underline font-semibold">
                    View All Results →
                </a>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                <p class="font-semibold">Results Not Available</p>
                <p class="text-sm mt-1">Your results will be available once you have cleared finance requirements.</p>
            </div>
        @endif
    </div>
</div>
@endsection
