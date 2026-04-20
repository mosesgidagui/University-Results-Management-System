@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Notification Alert -->
    @if($notification)
        <div class="mb-8 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-blue-800">📬 {{ $notification }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-800">HOD Approvals Received</h1>
            <p class="text-gray-600 mt-2">Results approved by HOD, ready to forward to Senate</p>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold text-green-600">{{ $results->total() }}</div>
            <p class="text-gray-500 text-sm">Results Approved by HOD</p>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($results->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">
                                <input type="checkbox" id="selectAll" class="rounded">
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Student</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Course</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Lecturer</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Marks</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Grade</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">HOD Comment</th>
                            <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($results as $result)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input type="checkbox" class="result-checkbox rounded" value="{{ $result->id }}">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $result->student->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $result->student->student_number }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium">{{ $result->course->code }}</div>
                                    <div class="text-sm text-gray-500">{{ $result->course->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $result->lecturer->name }}</td>
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $result->marks }}/100</td>
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
                                        ✓ {{ Str::limit($result->hod_comment ?? 'Approved', 25) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form method="POST" action="{{ route('admin.results.forward-to-senate', $result) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 text-sm bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition">
                                            Forward to Senate
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Bulk Action Bar -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3">
                <form method="POST" action="{{ route('admin.results.bulk-forward-to-senate') }}" class="flex gap-3 w-full" id="bulkForm">
                    @csrf
                    <button type="button" onclick="selectAllResults()" class="px-4 py-2 text-sm border border-gray-300 rounded hover:bg-gray-100">
                        Select All
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm bg-purple-600 text-white rounded hover:bg-purple-700 disabled:opacity-50" id="bulkSubmit" disabled>
                        Forward Selected to Senate
                    </button>
                    <div id="selectedCount" class="ml-auto text-sm text-gray-600"></div>
                </form>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $results->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-lg mb-2">No HOD approvals pending</p>
                <p class="text-sm">All results from HOD have been processed</p>
            </div>
        @endif
    </div>

    <!-- Back Link -->
    <div class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">
            ← Back to Dashboard
        </a>
    </div>
</div>

<script>
document.querySelectorAll('.result-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkUI);
});

document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.result-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
    updateBulkUI();
});

function selectAllResults() {
    document.querySelectorAll('.result-checkbox').forEach(cb => {
        cb.checked = true;
    });
    updateBulkUI();
}

function updateBulkUI() {
    const selected = Array.from(document.querySelectorAll('.result-checkbox:checked')).map(cb => cb.value);
    const form = document.getElementById('bulkForm');
    
    // Clear old inputs
    form.querySelectorAll('input[name^="result_ids"]').forEach(el => el.remove());
    
    // Add new inputs
    selected.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'result_ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.getElementById('bulkSubmit').disabled = selected.length === 0;
    document.getElementById('selectedCount').textContent = selected.length > 0 ? 
        `${selected.length} selected` : '';
}
</script>
@endsection
