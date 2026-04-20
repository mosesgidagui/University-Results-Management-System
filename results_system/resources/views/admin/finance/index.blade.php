{{-- resources/views/finance/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Finance Clearance')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Finance Clearance — {{ $session?->name }} Sem {{ $session?->semester }}</h1>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Student</th>
                <th class="px-4 py-3 text-left">Reg. Number</th>
                <th class="px-4 py-3 text-center">Finance Status</th>
                <th class="px-4 py-3 text-left">Notes</th>
                <th class="px-4 py-3 text-left">Cleared By</th>
                <th class="px-4 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($students as $student)
            @php $fc = $student->financeClearance->first(); @endphp
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $student->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $student->student_number }}</td>
                <td class="px-4 py-3 text-center">
                    @if($fc?->is_cleared)
                        <span class="px-2 py-0.5 rounded text-xs bg-green-100 text-green-700 font-medium">Cleared</span>
                    @elseif($fc && !$fc->is_cleared)
                        <span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-700 font-medium">Flagged</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-500">Pending</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-xs text-gray-500">{{ $fc?->notes ?? '—' }}</td>
                <td class="px-4 py-3 text-xs text-gray-500">{{ $fc?->clearedBy?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <div class="flex justify-center gap-2">
                        <form method="POST" action="{{ route('finance.clear', $student) }}">
                            @csrf
                            <input type="hidden" name="academic_session_id" value="{{ $session?->id }}">
                            <button class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded hover:bg-green-200">Clear</button>
                        </form>
                        <button onclick="openFlag({{ $student->id }})"
                            class="text-xs bg-amber-100 text-amber-700 px-3 py-1 rounded hover:bg-amber-200">Flag</button>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No students found.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $students->links() }}</div>

{{-- Flag modal --}}
<div id="flag-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
        <h2 class="font-semibold text-lg mb-3">Flag Student — Outstanding Fees</h2>
        <form id="flag-form" method="POST">
            @csrf
            <input type="hidden" name="academic_session_id" value="{{ $session?->id }}">
            <label class="block text-sm font-medium mb-1">Notes <span class="text-red-500">*</span></label>
            <textarea name="notes" rows="3" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-4"
                placeholder="Describe outstanding balance..."></textarea>
            <div class="flex gap-3">
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-amber-700">
                    Confirm Flag
                </button>
                <button type="button" onclick="document.getElementById('flag-modal').classList.add('hidden')"
                    class="text-sm text-gray-500 px-4 py-2 hover:underline">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openFlag(id) {
    document.getElementById('flag-form').action = '/finance/' + id + '/flag';
    document.getElementById('flag-modal').classList.remove('hidden');
}
</script>
@endsection
