{{-- resources/views/hod/index.blade.php --}}
@extends('layouts.app')
@section('title', 'HOD Review')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Results Awaiting Review — {{ $session?->name }} Sem {{ $session?->semester }}</h1>
</div>

<form method="POST" action="{{ route('hod.approve-bulk') }}">
@csrf
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3"><input type="checkbox" id="chk-all"></th>
                <th class="px-4 py-3 text-left">Student</th>
                <th class="px-4 py-3 text-left">Course</th>
                <th class="px-4 py-3 text-left">Lecturer</th>
                <th class="px-4 py-3 text-center">Marks</th>
                <th class="px-4 py-3 text-center">Grade</th>
                <th class="px-4 py-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($results as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3"><input type="checkbox" name="ids[]" value="{{ $r->id }}" class="row-chk"></td>
                <td class="px-4 py-3 font-medium">{{ $r->student->name }}<br>
                    <span class="text-gray-400 text-xs">{{ $r->student->student_number }}</span></td>
                <td class="px-4 py-3">{{ $r->course->code }}<br>
                    <span class="text-gray-400 text-xs">{{ $r->course->name }}</span></td>
                <td class="px-4 py-3 text-gray-600">{{ $r->lecturer->name }}</td>
                <td class="px-4 py-3 text-center font-mono">{{ $r->marks }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded font-semibold text-xs
                        {{ $r->grade === 'F' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $r->grade }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        {{-- Approve single --}}
                        <form method="POST" action="{{ route('hod.approve', $r) }}">
                            @csrf
                            <button class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200">Approve</button>
                        </form>

                        {{-- Reject single with comment --}}
                        <button onclick="openReject({{ $r->id }})"
                            class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200">Reject</button>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No results awaiting review.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    <button type="submit" class="bg-green-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-green-700">
        Approve Selected
    </button>
</div>
</form>

<div class="mt-4">{{ $results->links() }}</div>

{{-- Reject modal --}}
<div id="reject-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
        <h2 class="font-semibold text-lg mb-3">Reject Result</h2>
        <form id="reject-form" method="POST">
            @csrf
            <label class="block text-sm font-medium mb-1">Reason / Comment <span class="text-red-500">*</span></label>
            <textarea name="comment" rows="3" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-4"
                placeholder="Explain what needs to be corrected..."></textarea>
            <div class="flex gap-3">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
                    Confirm Reject
                </button>
                <button type="button" onclick="closeReject()"
                    class="text-sm text-gray-500 px-4 py-2 hover:underline">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('chk-all').addEventListener('change', function() {
    document.querySelectorAll('.row-chk').forEach(c => c.checked = this.checked);
});
function openReject(id) {
    document.getElementById('reject-form').action = '/hod/' + id + '/reject';
    document.getElementById('reject-modal').classList.remove('hidden');
}
function closeReject() {
    document.getElementById('reject-modal').classList.add('hidden');
}
</script>
@endsection
