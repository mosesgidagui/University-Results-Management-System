{{-- resources/views/admin/users.blade.php --}}
@extends('layouts.app')
@section('title', 'Manage Users')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Users</h1>
    <a href="{{ route('admin.users.create') }}"
        class="bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700">+ Add User</a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Reg. No.</th>
                <th class="px-4 py-3 text-center">Role</th>
                <th class="px-4 py-3 text-left">Department</th>
                <th class="px-4 py-3 text-center">Status</th>
                <th class="px-4 py-3 text-center">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
        @foreach($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $user->student_number ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    @php $roleColors = [
                        'admin'     => 'bg-purple-100 text-purple-700',
                        'lecturer'  => 'bg-teal-100 text-teal-700',
                        'hod'       => 'bg-teal-100 text-teal-700',
                        'finance'   => 'bg-amber-100 text-amber-700',
                        'registrar' => 'bg-blue-100 text-blue-700',
                        'senate'    => 'bg-indigo-100 text-indigo-700',
                        'student'   => 'bg-gray-100 text-gray-600',
                    ]; @endphp
                    <span class="px-2 py-0.5 rounded text-xs {{ $roleColors[$user->role] ?? '' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-xs text-gray-500">{{ $user->department?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    @if($user->is_active)
                        <span class="px-2 py-0.5 rounded text-xs bg-green-100 text-green-700">Active</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-700">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                        @csrf @method('PATCH')
                        <button class="text-xs text-gray-500 hover:text-red-600 hover:underline">
                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
