{{-- resources/views/admin/create-user.blade.php --}}
@extends('layouts.app')
@section('title', 'Create User')
@section('content')
<div class="max-w-xl">
    <h1 class="text-xl font-semibold mb-6">Create User Account</h1>

    <form method="POST" action="{{ route('admin.users.store') }}"
        class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Role</label>
                <select name="role" id="role-select" onchange="toggleStudentFields(this.value)"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    <option value="">— Select role —</option>
                    @foreach(\App\Models\User::ROLES as $role)
                        <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Department</label>
                <select name="department_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">— None —</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div id="student-number-field" class="col-span-2 hidden">
                <label class="block text-sm font-medium mb-1">Student Number</label>
                <input type="text" name="student_number" value="{{ old('student_number') }}"
                    placeholder="S2400001"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" required minlength="8"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700">
                Create Account
            </button>
            <a href="{{ route('admin.users') }}" class="text-sm text-gray-500 px-4 py-2 hover:underline">Cancel</a>
        </div>
    </form>
</div>

<script>
function toggleStudentFields(role) {
    document.getElementById('student-number-field').classList.toggle('hidden', role !== 'student');
}
toggleStudentFields('{{ old('role') }}');
</script>
@endsection


{{-- ════════════════════════════════════════════════════════
     resources/views/admin/sessions.blade.php
     ════════════════════════════════════════════════════════ --}}
@extends('layouts.app')
@section('title', 'Academic Sessions')
@section('content')
<h1 class="text-xl font-semibold mb-6">Academic Sessions</h1>

<div class="grid md:grid-cols-2 gap-6">
    {{-- Create form --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6">
        <h2 class="font-medium mb-4">Create New Session</h2>
        <form method="POST" action="{{ route('admin.sessions.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Session Name</label>
                <input type="text" name="name" placeholder="2025/2026" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Semester</label>
                <select name="semester" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Start Date</label>
                    <input type="date" name="start_date" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">End Date</label>
                    <input type="date" name="end_date" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <p class="text-xs text-amber-600">Creating a session will deactivate all previous sessions.</p>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 w-full">
                Create &amp; Set Active
            </button>
        </form>
    </div>

    {{-- Sessions list --}}
    <div>
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Session</th>
                        <th class="px-4 py-3 text-center">Sem</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                @foreach($sessions as $s)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $s->name }}</td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $s->semester }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($s->is_active)
                                <span class="px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">Active</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-500">Inactive</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $sessions->links() }}</div>
    </div>
</div>
@endsection


{{-- ════════════════════════════════════════════════════════
     resources/views/admin/audit.blade.php
     ════════════════════════════════════════════════════════ --}}
@extends('layouts.app')
@section('title', 'Audit Log')
@section('content')
<h1 class="text-xl font-semibold mb-6">Audit Log</h1>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Time</th>
                <th class="px-4 py-3 text-left">User</th>
                <th class="px-4 py-3 text-left">Action</th>
                <th class="px-4 py-3 text-left">Target</th>
                <th class="px-4 py-3 text-left">IP</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @forelse($logs as $log)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">
                    {{ $log->created_at->format('d M Y H:i') }}
                </td>
                <td class="px-4 py-3">{{ $log->user?->name ?? 'System' }}</td>
                <td class="px-4 py-3">
                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded">{{ $log->action }}</span>
                </td>
                <td class="px-4 py-3 text-xs text-gray-500">
                    {{ class_basename($log->auditable_type ?? '') }} #{{ $log->auditable_id }}
                </td>
                <td class="px-4 py-3 text-xs text-gray-400">{{ $log->ip_address }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No audit entries yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $logs->links() }}</div>
@endsection
