@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Your Examination Results</h1>

    <!-- Student Info -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-gray-600 text-sm">Student Number</p>
                <p class="text-xl font-bold">{{ auth()->user()->student_number }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Academic Session</p>
                <p class="text-xl font-bold">{{ $session?->name ?? 'Not Active' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">GPA</p>
                <p class="text-xl font-bold">{{ $gpa ? number_format($gpa, 2) : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold">Published Results</h2>
        </div>

        @if($results->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Course Code</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Course Name</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Credit Units</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Marks</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Grade</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Points</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Remark</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($results as $result)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->course->code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->course->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->course->credit_units }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ $result->marks }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                        @if($result->grade == 'A') bg-green-100 text-green-800
                                        @elseif($result->grade == 'B') bg-green-100 text-green-800
                                        @elseif($result->grade == 'C') bg-blue-100 text-blue-800
                                        @elseif($result->grade == 'D') bg-yellow-100 text-yellow-800
                                        @elseif($result->grade == 'F') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $result->grade }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->grade_points }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->remark }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $results->links() }}
            </div>
        @else
            @if($cleared)
                <div class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-lg text-gray-700 mb-2">No results published yet</p>
                    <p class="text-sm text-gray-500">Results will appear here once they are published by the administration</p>
                </div>
            @else
                <!-- Access Denied Alert -->
                <div class="px-6 py-6 bg-red-50 border-l-4 border-red-500">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-red-800">Unable to View Results</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p class="mb-2">Your account has <strong>outstanding financial dues</strong> that must be cleared before you can view your examination results.</p>
                                <p class="mb-4">The system automatically verified with the Finance Department and found pending obligations on your account.</p>
                                
                                <div class="mt-4 p-4 bg-red-100 rounded-lg border border-red-300">
                                    <p class="font-semibold mb-2">📋 What you need to do:</p>
                                    <ol class="list-decimal list-inside space-y-1 text-sm">
                                        <li>Contact the Finance Department to review your account</li>
                                        <li>Clear all outstanding fees and dues</li>
                                        <li>Request the Finance Officer to update your clearance status</li>
                                        <li>Your results will be accessible immediately after clearance</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-900">
                        <strong>Finance Department Contact:</strong> Please visit the Finance Office or email your inquiry for assistance with clearing your outstanding dues.
                    </p>
                </div>
            @endif
        @endif
    </div>

    <!-- Back to Dashboard -->
    <div class="mt-6">
        <a href="{{ route('student.dashboard') }}" class="text-blue-600 hover:underline">
            ← Back to Dashboard
        </a>
    </div>
</div>
@endsection
