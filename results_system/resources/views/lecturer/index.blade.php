@extends('layouts.app')
@section('title', 'My Results')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">My Results</h1>
        <div class="flex gap-2">
            <a href="{{ route('lecturer.results.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Add Result
            </a>
            <a href="{{ route('lecturer.performance-report') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                Performance Report
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('lecturer.results') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-200">
                <input type="text" name="search" placeholder="Search by student name or number" value="{{ request('search') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div class="flex-1 min-w-200">
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Status</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="submitted" @selected(request('status') === 'submitted')>Submitted</option>
                    <option value="hod_approved" @selected(request('status') === 'hod_approved')>HOD Approved</option>
                    <option value="hod_rejected" @selected(request('status') === 'hod_rejected')>HOD Rejected</option>
                    <option value="compiled" @selected(request('status') === 'compiled')>Compiled</option>
                    <option value="senate_approved" @selected(request('status') === 'senate_approved')>Senate Approved</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                </select>
            </div>
            <div class="flex-1 min-w-200">
                <select name="course_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(request('course_id') == $course->id)>
                            {{ $course->code }} - {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Filter
            </button>
        </form>
    </div>

    <!-- Bulk Actions -->
    @if($results->count() > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="POST" action="{{ route('lecturer.results.bulk-submit') }}" id="bulkForm">
            @csrf
            <div class="flex justify-between items-center mb-4">
                <label class="flex items-center">
                    <input type="checkbox" id="selectAll" class="mr-2" onchange="toggleSelectAll(this)">
                    <span class="font-medium">Select All</span>
                </label>
                <div class="flex gap-2">
                    <button type="button" onclick="if(confirm('Delete selected results?')) document.getElementById('bulkDeleteForm').submit()" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700" id="deleteBtn" disabled>
                        Delete Selected
                    </button>
                    <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700" id="submitBtn" disabled>
                        Submit Selected
                    </button>
                </div>
            </div>

            <!-- Results Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold">
                                <input type="checkbox" class="result-checkbox" onchange="updateBulkButtons()">
                            </th>
                            <th class="px-4 py-2 text-left font-semibold">Student</th>
                            <th class="px-4 py-2 text-left font-semibold">Course</th>
                            <th class="px-4 py-2 text-center font-semibold">Marks</th>
                            <th class="px-4 py-2 text-center font-semibold">Grade</th>
                            <th class="px-4 py-2 text-left font-semibold">Status</th>
                            <th class="px-4 py-2 text-center font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">
                                @if($result->canBeSubmitted())
                                    <input type="checkbox" name="ids[]" value="{{ $result->id }}" class="result-checkbox" onchange="updateBulkButtons()">
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $result->student->name }}</div>
                                <div class="text-sm text-gray-500">{{ $result->student->student_number }}</div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="font-medium">{{ $result->course->code }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($result->course->title, 40) }}</div>
                            </td>
                            <td class="px-4 py-2 text-center font-semibold">{{ $result->marks }}</td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold
                                    @if($result->grade === 'A') bg-green-100 text-green-800
                                    @elseif($result->grade === 'B') bg-blue-100 text-blue-800
                                    @elseif($result->grade === 'C') bg-yellow-100 text-yellow-800
                                    @elseif($result->grade === 'D') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $result->grade }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-sm
                                    @if($result->status === 'draft') bg-gray-100 text-gray-800
                                    @elseif($result->status === 'submitted') bg-blue-100 text-blue-800
                                    @elseif($result->status === 'hod_approved') bg-green-100 text-green-800
                                    @elseif($result->status === 'hod_rejected') bg-red-100 text-red-800
                                    @elseif($result->status === 'published') bg-purple-100 text-purple-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ str_replace('_', ' ', ucfirst($result->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex justify-center gap-2">
                                    @if($result->canBeSubmitted())
                                        <a href="{{ route('lecturer.results.edit', $result) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                        <form method="POST" action="{{ route('lecturer.results.submit', $result) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800">Submit</button>
                                        </form>
                                    @endif
                                    @if($result->status === 'draft')
                                        <form method="POST" action="{{ route('lecturer.results.destroy', $result) }}" style="display:inline;" onsubmit="return confirm('Delete this result?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('lecturer.results.edit', $result) }}" class="text-gray-600 hover:text-gray-800">View</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $results->links() }}
        </form>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-gray-600 mb-4">No results found.</p>
        <a href="{{ route('lecturer.results.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Add Your First Result
        </a>
    </div>
    @endif
</div>

<!-- Bulk Delete Form (hidden) -->
<form method="POST" id="bulkDeleteForm" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
function toggleSelectAll(checkbox) {
    document.querySelectorAll('.result-checkbox').forEach(cb => {
        if (cb.id !== 'selectAll') {
            cb.checked = checkbox.checked;
        }
    });
    updateBulkButtons();
}

function updateBulkButtons() {
    const checked = document.querySelectorAll('input[name="ids[]"]:checked').length;
    document.getElementById('submitBtn').disabled = checked === 0;
    document.getElementById('deleteBtn').disabled = checked === 0;
}

// Update bulk delete form
document.getElementById('bulkDeleteForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('input[name="ids[]"]:checked');
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        this.appendChild(input);
    });
});
</script>
@endsection
