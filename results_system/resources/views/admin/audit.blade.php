@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold">Audit Logs</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:underline">← Back to Dashboard</a>
    </div>

    <!-- Audit Logs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Date</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">User</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Action</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Type</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">
                            {{ $log->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium">{{ $log->user?->name ?? 'System' }}</div>
                            <div class="text-xs text-gray-600">{{ $log->user?->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                {{ ucwords(str_replace('.', ' ', $log->action)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $log->auditable_type }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($log->new_values)
                                <span class="text-gray-700">
                                    {{ json_encode($log->new_values) }}
                                </span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No audit logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
