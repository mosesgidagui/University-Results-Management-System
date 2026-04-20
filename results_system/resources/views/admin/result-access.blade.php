@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-800">Result Access Management</h1>
            <p class="text-gray-600 mt-2">Grant or revoke student viewer rights for results</p>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold text-blue-600">{{ $session?->name ?? 'No Session' }}</div>
            <p class="text-gray-500 text-sm">Active Academic Session</p>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" class="flex gap-4">
            <input type="text" name="search" placeholder="Search by name, email, or student number..." 
                value="{{ request('search') }}"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Search
            </button>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Student</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Student #</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Access Status</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Cleared Date</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Reason</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($students as $student)
                    @php
                        $clearance = $student->financeClearance?->first();
                        $isCleared = $clearance?->is_cleared ?? false;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $student->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $student->email }}</td>
                        <td class="px-6 py-4 text-gray-500 text-sm">{{ $student->student_number ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($isCleared)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Cleared
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Blocked
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-500">
                            {{ $clearance?->cleared_at?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                                {{ Str::limit($clearance?->notes ?? 'No notes', 30) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center space-x-2">
                            @if($isCleared)
                                <!-- Revoke Access Button -->
                                <button 
                                    type="button"
                                    class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 transition"
                                    onclick="openRevokeModal({{ $student->id }}, '{{ $student->name }}')">
                                    Revoke Access
                                </button>
                            @else
                                <!-- Grant Access Button -->
                                <button 
                                    type="button"
                                    class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200 transition"
                                    onclick="openGrantModal({{ $student->id }}, '{{ $student->name }}')">
                                    Grant Access
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            No students found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $students->links() }}
    </div>
</div>

<!-- Grant Access Modal -->
<div id="grantModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Grant Result Access</h2>
        
        <form id="grantForm" method="POST">
            @csrf
            <div class="mb-4">
                <p class="text-gray-600 mb-4">
                    Student: <span id="grantStudentName" class="font-bold text-blue-600"></span>
                </p>
                <p class="text-sm text-gray-500 mb-4">
                    This student will be able to view published results once their finance clearance is verified.
                </p>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="3" placeholder="e.g., Fees paid in full"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeGrantModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Grant Access
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Revoke Access Modal -->
<div id="revokeModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Revoke Result Access</h2>
        
        <form id="revokeForm" method="POST">
            @csrf
            <div class="mb-4">
                <p class="text-gray-600 mb-4">
                    Student: <span id="revokeStudentName" class="font-bold text-red-600"></span>
                </p>
                <p class="text-sm text-gray-500 mb-4">
                    This student will no longer be able to view published results.
                </p>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Revocation <span class="text-red-600">*</span></label>
                <textarea name="reason" rows="3" placeholder="e.g., Outstanding fees of $500, Tuition not paid"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeRevokeModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Revoke Access
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openGrantModal(studentId, studentName) {
    document.getElementById('grantStudentName').textContent = studentName;
    document.getElementById('grantForm').action = `/admin/students/${studentId}/grant-result-access`;
    document.getElementById('grantModal').classList.remove('hidden');
}

function closeGrantModal() {
    document.getElementById('grantModal').classList.add('hidden');
}

function openRevokeModal(studentId, studentName) {
    document.getElementById('revokeStudentName').textContent = studentName;
    document.getElementById('revokeForm').action = `/admin/students/${studentId}/revoke-result-access`;
    document.getElementById('revokeModal').classList.remove('hidden');
}

function closeRevokeModal() {
    document.getElementById('revokeModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('grantModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeGrantModal();
});

document.getElementById('revokeModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeRevokeModal();
});
</script>
@endsection
