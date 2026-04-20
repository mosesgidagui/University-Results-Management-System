@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Review Submissions</h1>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Pending Review</div>
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

    <!-- Submitted Results -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold">Submitted Results for Review</h2>
        </div>

        @if($results->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Student</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Course</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Lecturer</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Marks</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Grade</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($results as $result)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->student->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->course->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->lecturer->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->marks ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->grade ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                        @if($result->status == 'submitted') bg-orange-100 text-orange-800
                                        @elseif($result->status == 'hod_approved') bg-green-100 text-green-800
                                        @elseif($result->status == 'hod_rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('_', ' ', $result->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($result->status == 'submitted')
                                        <form method="POST" action="{{ route('hod.results.approve', $result) }}" class="inline-block mr-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                                                Approve
                                            </button>
                                        </form>
                                        <button type="button" onclick="showRejectModal({{ $result->id }})" class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
                                            Reject
                                        </button>
                                    @else
                                        <span class="text-gray-500 text-xs">-</span>
                                    @endif
                                </td>
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
            <div class="px-6 py-12 text-center text-gray-500">
                <p class="text-lg mb-2">No submissions to review</p>
                <p class="text-sm">All results have been reviewed</p>
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-bold mb-4">Reject Result</h3>
        <form id="rejectForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection</label>
                <textarea name="comment" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentResultId = null;

    function showRejectModal(resultId) {
        currentResultId = resultId;
        const form = document.getElementById('rejectForm');
        form.action = `/hod/results/${resultId}/reject`;
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endsection
