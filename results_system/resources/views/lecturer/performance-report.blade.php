@extends('layouts.app')
@section('title', 'Class Performance Report')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Class Performance Report</h1>
        <a href="{{ route('lecturer.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Back to Dashboard
        </a>
    </div>

    @if($session)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
        <p class="text-blue-900"><strong>Academic Session:</strong> {{ $session->name }} - Semester {{ $session->semester }}</p>
    </div>
    @endif

    @if($report->isEmpty())
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-gray-600 mb-4">No published results available for performance analysis.</p>
        <p class="text-sm text-gray-500">Results must be published before they appear in performance reports.</p>
    </div>
    @else
    <div class="grid gap-8">
        @foreach($report as $courseReport)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Course Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-white">
                <h2 class="text-2xl font-bold">{{ $courseReport['course']->code }}</h2>
                <p class="text-blue-100">{{ $courseReport['course']->title }}</p>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 p-6 border-b bg-gray-50">
                <div>
                    <div class="text-gray-500 text-sm font-medium">Total Students</div>
                    <div class="text-2xl font-bold text-blue-600">{{ $courseReport['total_students'] }}</div>
                </div>
                <div>
                    <div class="text-gray-500 text-sm font-medium">Average Mark</div>
                    <div class="text-2xl font-bold text-purple-600">{{ $courseReport['average_mark'] }}</div>
                </div>
                <div>
                    <div class="text-gray-500 text-sm font-medium">Highest Mark</div>
                    <div class="text-2xl font-bold text-green-600">{{ $courseReport['highest_mark'] }}</div>
                </div>
                <div>
                    <div class="text-gray-500 text-sm font-medium">Lowest Mark</div>
                    <div class="text-2xl font-bold text-red-600">{{ $courseReport['lowest_mark'] }}</div>
                </div>
                <div>
                    <div class="text-gray-500 text-sm font-medium">Pass Rate</div>
                    <div class="text-2xl font-bold text-orange-600">{{ $courseReport['pass_rate'] }}%</div>
                </div>
            </div>

            <!-- Grade Distribution Chart -->
            <div class="p-6">
                <h3 class="text-lg font-bold mb-6">Grade Distribution</h3>
                
                <div class="space-y-4">
                    @php
                        $grades = ['A', 'B', 'C', 'D', 'F'];
                        $colors = ['bg-green-500', 'bg-blue-500', 'bg-yellow-500', 'bg-orange-500', 'bg-red-500'];
                        $totalCount = array_sum($courseReport['grade_distribution']);
                    @endphp

                    @foreach($grades as $index => $grade)
                        @php
                            $count = $courseReport['grade_distribution'][$grade];
                            $percentage = $totalCount > 0 ? ($count / $totalCount) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="font-semibold">Grade {{ $grade }}</span>
                                <span class="text-gray-600">{{ $count }} student{{ $count !== 1 ? 's' : '' }} ({{ number_format($percentage, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $colors[$index] }} h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Performance Insights -->
            <div class="p-6 border-t bg-gray-50">
                <h3 class="text-lg font-bold mb-4">Performance Insights</h3>
                
                <div class="space-y-3 text-sm">
                    @if($courseReport['average_mark'] >= 70)
                        <div class="flex items-start gap-3 p-3 bg-green-50 rounded">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-800"><strong>Strong Performance:</strong> Average mark of {{ $courseReport['average_mark'] }} indicates above-average class performance.</span>
                        </div>
                    @elseif($courseReport['average_mark'] >= 50)
                        <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded">
                            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-yellow-800"><strong>Moderate Performance:</strong> Average mark of {{ $courseReport['average_mark'] }} suggests room for improvement.</span>
                        </div>
                    @else
                        <div class="flex items-start gap-3 p-3 bg-red-50 rounded">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-red-800"><strong>Below Average:</strong> Average mark of {{ $courseReport['average_mark'] }} indicates class needs additional support.</span>
                        </div>
                    @endif

                    @if($courseReport['pass_rate'] >= 80)
                        <div class="flex items-start gap-3 p-3 bg-green-50 rounded">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-800"><strong>High Pass Rate:</strong> {{ $courseReport['pass_rate'] }}% of students passed - excellent result!</span>
                        </div>
                    @elseif($courseReport['pass_rate'] < 50)
                        <div class="flex items-start gap-3 p-3 bg-red-50 rounded">
                            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-red-800"><strong>Low Pass Rate:</strong> Only {{ $courseReport['pass_rate'] }}% passed - consider reviewing teaching methods or assessment difficulty.</span>
                        </div>
                    @endif

                    @if($courseReport['highest_mark'] - $courseReport['lowest_mark'] > 40)
                        <div class="flex items-start gap-3 p-3 bg-blue-50 rounded">
                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm2 0h2v2h-2V8z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-blue-800"><strong>High Mark Variation:</strong> Large gap ({{ $courseReport['highest_mark'] - $courseReport['lowest_mark'] }} marks) between highest and lowest - consider providing additional support to struggling students.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
