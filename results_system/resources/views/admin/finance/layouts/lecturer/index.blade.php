{{-- resources/views/lecturer/index.blade.php --}}
@extends('layouts.app')
@section('title', 'My Results')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">My Results — {{ $session?->name }} Sem {{ $session?->semester }}</h1>
    <a href="{{ route('lecturer.create') }}" class="bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700">+ Add Result</a>
</div>

<form method="POST" action="{{ route('lecturer.submit-bulk') }}">
@csrf
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left"><input type="checkbox" id="chk-all"></th>
                <th class="px-4 py-3 text-left">Student</th>
                <th class="px-4 py-3 text-left">Course</th>
                <th class="px-4 py-3 text-center">Marks</th>
                <th class="px-4 py-3 text-center">Grade</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-left">Comment</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($results as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    @if($r->canBeSubmitted())
                        <input type="checkbox" name="ids[]" value="{{ $r->id }}" class="row-chk">
                    @endif
                </td>
                <td class="px-4 py-3 font-medium">{{ $r->student->name }}<br>
                    <span class="text-gray-400 text-xs">{{ $r->student->student_number }}</span></td>
                <td class="px-4 py-3">{{ $r->course->code }}<br>
                    <span class="text-gray-400 text-xs">{{ $r->course->name }}</span></td>
                <td class="px-4 py-3 text-center font-mono">{{ $r->marks }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded font-semibold text-xs
                        {{ $r->grade === 'F' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $r->grade }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    @php
                        $colors = [
                            'draft'        => 'bg-gray-100 text-gray-600',
                            'submitted'    => 'bg-blue-100 text-blue-700',
                            'hod_approved' => 'bg-teal-100 text-teal-700',
                            'hod_rejected' => 'bg-red-100 text-red-700',
                            'compiled'     => 'bg-purple-100 text-purple-700',
                            'senate_approved' => 'bg-indigo-100 text-indigo-700',
                            'published'    => 'bg-green-100 text-green-700',
                        ];
                    @endphp
                    <span class="px-2 py-0.5 rounded text-xs {{ $colors[$r->status] ?? 'bg-gray-100' }}">
                        {{ str_replace('_', ' ', $r->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-xs text-red-600">{{ $r->hod_comment }}</td>
                <td class="px-4 py-3 flex gap-2">
                    @if($r->canBeSubmitted())
                        <form method="POST" action="{{ route('lecturer.submit', $r) }}">@csrf
                            <button class="text-xs text-blue-600 hover:underline">Submit</button>
                        </form>
                        <a href="{{ route('lecturer.edit', $r) }}" class="text-xs text-gray-500 hover:underline">Edit</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No results yet. Add your first result.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 flex items-center gap-3">
    <button type="submit" class="bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700">
        Submit Selected
    </button>
    <span class="text-xs text-gray-400">Select draft or rejected results above</span>
</div>
</form>

<div class="mt-4">{{ $results->links() }}</div>

<script>
document.getElementById('chk-all').addEventListener('change', function() {
    document.querySelectorAll('.row-chk').forEach(c => c.checked = this.checked);
});
</script>
@endsection
