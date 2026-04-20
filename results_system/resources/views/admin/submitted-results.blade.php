@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Submitted Results Awaiting Verification</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">← Back to Dashboard</a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-600 rounded-lg p-4 mb-6">
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-600 rounded-lg p-4 mb-6">
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select name="department_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Lecturer</label>
                <select name="lecturer_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Lecturers</option>
                    @foreach($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}" {{ request('lecturer_id') == $lecturer->id ? 'selected' : '' }}>
                            {{ $lecturer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Filter
                </button>
                <a href="{{ route('admin.submitted-results') }}" class="flex-1 bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 text-center">
                    Clear
                </a>
            </div>
        </form>
    </div>

    @if($results->isEmpty())
        <div class="bg-blue-50 border-l-4 border-blue-600 rounded-lg p-6">
            <p class="text-blue-700">No submitted results awaiting verification.</p>
        </div>
    @else
        <form method="POST" action="{{ route('admin.results.bulk-forward-to-hod') }}" id="bulk-form">
            @csrf
            
            <!-- Bulk Actions -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex gap-2 items-center">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700" onclick="return validateBulkSubmit()">
                        Forward Selected to HOD
                    </button>
                    <p class="text-gray-600 text-sm">
                        <span id="selected-count">0</span> result(s) selected
                    </p>
                </div>
            </div>

            <!-- Results Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" id="select-all" class="rounded">
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Student</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Course</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Lecturer</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Marks</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Grade</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="result-checkbox rounded" name="result_ids[]" value="{{ $result->id }}">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium">{{ $result->student->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $result->student->student_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium">{{ $result->course->code }}</div>
                                    <div class="text-sm text-gray-600">{{ $result->course->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">{{ $result->lecturer->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold">{{ $result->marks }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                                        @if($result->grade === 'A') bg-green-100 text-green-800
                                        @elseif($result->grade === 'B') bg-blue-100 text-blue-800
                                        @elseif($result->grade === 'C') bg-yellow-100 text-yellow-800
                                        @elseif($result->grade === 'D') bg-orange-100 text-orange-800
                                        @else bg-red-100 text-red-800
                                        @endif
                                    ">
                                        {{ $result->grade }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.results.forward-to-hod', $result) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:underline text-sm" onclick="return confirm('Forward this result to HOD for review?')">
                                            Forward to HOD
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($results->hasPages())
                <div class="mt-6">
                    {{ $results->links() }}
                </div>
            @endif
        </form>
    @endif
</div>

<script>
function validateBulkSubmit() {
    const checked = document.querySelectorAll('input[name="result_ids[]"]:checked');
    if (checked.length === 0) {
        alert('Please select at least one result');
        return false;
    }
    return confirm('Forward ' + checked.length + ' result(s) to HOD for review?');
}

// Handle select all checkbox
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.result-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    updateSelectedCount();
});

// Update count when individual checkboxes change
document.querySelectorAll('.result-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        updateSelectedCount();
    });
});

function updateSelectedCount() {
    const selected = document.querySelectorAll('.result-checkbox:checked');
    document.getElementById('selected-count').textContent = selected.length;
}

// Initialize count on page load
updateSelectedCount();
</script>
@endsection
