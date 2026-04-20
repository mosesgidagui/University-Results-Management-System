@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Notification Alerts -->
    @if($totalApproved > 0)
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded flex items-center">
            <svg class="h-5 w-5 text-green-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-green-800">✓ Senate has APPROVED {{ $totalApproved }} result(s)</p>
        </div>
    @endif

    @if($totalRejected > 0)
        <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded flex items-center">
            <svg class="h-5 w-5 text-yellow-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-yellow-800">⚠ Senate has REJECTED {{ $totalRejected }} result(s)</p>
        </div>
    @endif

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Senate Actions Summary</h1>
        <p class="text-gray-600 mt-2">Results that Senate has reviewed and actioned</p>
    </div>

    <!-- Approved Results Section -->
    @if($approved->count() > 0)
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-green-900">✓ Approved by Senate ({{ $totalApproved }})</h2>
                    <p class="text-sm text-green-700 mt-1">These results have been verified by Senate and are ready for publication</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Student</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Course</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Grade</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Senate Comment</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Approved</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($approved as $result)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $result->student->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $result->student->student_number }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $result->course->code }}</div>
                                        <div class="text-sm text-gray-500">{{ $result->course->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
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
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded">
                                            {{ Str::limit($result->senate_comment ?? '—', 40) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $result->senate_actioned_at?->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Rejected Results Section -->
    @if($rejected->count() > 0)
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-yellow-50 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-yellow-900">⚠ Returned by Senate ({{ $totalRejected }})</h2>
                    <p class="text-sm text-yellow-700 mt-1">These results need correction or revision before resubmission</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Student</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Course</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Grade</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Senate Feedback</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Rejected</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($rejected as $result)
                                <tr class="hover:bg-red-50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $result->student->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $result->student->student_number }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ $result->course->code }}</div>
                                        <div class="text-sm text-gray-500">{{ $result->course->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
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
                                    <td class="px-6 py-4 text-sm text-red-700 font-medium">
                                        <span class="text-xs bg-red-50 px-2 py-1 rounded">
                                            {{ Str::limit($result->senate_comment ?? 'Needs revision', 50) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $result->senate_actioned_at?->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Empty State -->
    @if($approved->count() === 0 && $rejected->count() === 0)
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-lg text-gray-700 mb-2">No Senate actions yet</p>
            <p class="text-sm text-gray-500">Results awaiting Senate review will appear here once they take action</p>
        </div>
    @endif

    <!-- Info Box -->
    <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="font-semibold text-blue-900 mb-2">📋 Next Steps</h3>
        <ul class="list-disc list-inside text-sm text-blue-900 space-y-1">
            <li>✓ <strong>Approved Results</strong> - Can now grant viewer rights to students once finance is cleared</li>
            <li>⚠ <strong>Rejected Results</strong> - Return to Registrar for correction and recompilation</li>
        </ul>
    </div>

    <!-- Back Link -->
    <div class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">
            ← Back to Dashboard
        </a>
    </div>
</div>
@endsection
