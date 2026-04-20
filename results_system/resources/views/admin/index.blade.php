{{-- resources/views/admin/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-xl font-semibold">System Dashboard</h1>
    @if(auth()->user()->faculty)
        <div class="text-right">
            <p class="text-sm text-gray-500">Faculty Administrator for</p>
            <p class="text-lg font-bold text-blue-600">{{ auth()->user()->faculty->name }}</p>
        </div>
    @endif
</div>

<!-- Notifications -->
@if($hodApprovals > 0 || $senateApprovals > 0 || $senateRejections > 0)
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    @if($hodApprovals > 0)
        <a href="{{ route('admin.hod-approvals') }}" class="bg-green-50 border-2 border-green-500 rounded-xl p-4 hover:shadow-lg transition group">
            <div class="flex items-center gap-3">
                <div class="text-3xl">📬</div>
                <div>
                    <p class="font-bold text-green-900">{{ $hodApprovals }} HOD Approvals</p>
                    <p class="text-sm text-green-700">Ready to forward to Senate</p>
                </div>
            </div>
        </a>
    @endif
    
    @if($senateApprovals > 0)
        <a href="{{ route('admin.senate-actions') }}" class="bg-blue-50 border-2 border-blue-500 rounded-xl p-4 hover:shadow-lg transition group">
            <div class="flex items-center gap-3">
                <div class="text-3xl">✓</div>
                <div>
                    <p class="font-bold text-blue-900">{{ $senateApprovals }} Senate Approvals</p>
                    <p class="text-sm text-blue-700">Can grant student access now</p>
                </div>
            </div>
        </a>
    @endif
    
    @if($senateRejections > 0)
        <a href="{{ route('admin.senate-actions') }}" class="bg-yellow-50 border-2 border-yellow-500 rounded-xl p-4 hover:shadow-lg transition group">
            <div class="flex items-center gap-3">
                <div class="text-3xl">⚠</div>
                <div>
                    <p class="font-bold text-yellow-900">{{ $senateRejections }} Senate Rejections</p>
                    <p class="text-sm text-yellow-700">Need correction & resubmission</p>
                </div>
            </div>
        </a>
    @endif
</div>
@endif

<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    @foreach([
        ['label' => 'Students',    'value' => $stats['total_students'],    'color' => 'blue'],
        ['label' => 'Lecturers',   'value' => $stats['total_lecturers'],   'color' => 'teal'],
        ['label' => 'Draft',       'value' => $stats['results_draft'],     'color' => 'gray'],
        ['label' => 'Pending',     'value' => $stats['results_pending'],   'color' => 'amber'],
        ['label' => 'Published',   'value' => $stats['results_published'], 'color' => 'green'],
    ] as $stat)
    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <p class="text-xs text-gray-400 uppercase mb-1">{{ $stat['label'] }}</p>
        <p class="text-2xl font-bold text-{{ $stat['color'] }}-600">{{ $stat['value'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <a href="{{ route('admin.submitted-results') }}"
        class="bg-white border border-gray-200 rounded-xl p-5 hover:border-red-300 transition group">
        <p class="font-medium group-hover:text-red-700">📝 Submitted Results</p>
        <p class="text-sm text-gray-400 mt-1">Forward to HOD</p>
    </a>
    
    <a href="{{ route('admin.hod-approvals') }}"
        class="bg-white border border-gray-200 rounded-xl p-5 hover:border-green-300 transition group">
        <p class="font-medium group-hover:text-green-700">✓ HOD Approvals</p>
        <p class="text-sm text-gray-400 mt-1">Forward to Senate</p>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <a href="{{ route('admin.senate-actions') }}"
        class="bg-white border border-gray-200 rounded-xl p-5 hover:border-purple-300 transition group">
        <p class="font-medium group-hover:text-purple-700">👥 Senate Actions</p>
        <p class="text-sm text-gray-400 mt-1">View approvals/rejections</p>
    </a>
    
    <a href="{{ route('admin.result-access') }}"
        class="bg-white border border-gray-200 rounded-xl p-5 hover:border-blue-300 transition group">
        <p class="font-medium group-hover:text-blue-700">🔓 Result Access</p>
        <p class="text-sm text-gray-400 mt-1">Grant viewer rights</p>
    </a>
</div>

@if(!auth()->user()->faculty_id)
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <a href="{{ route('admin.users') }}"
        class="bg-white border border-gray-200 rounded-xl p-5 hover:border-blue-300 transition group">
        <p class="font-medium group-hover:text-blue-700">👤 Manage Users</p>
        <p class="text-sm text-gray-400 mt-1">Create accounts</p>
    </a>
    
    <a href="{{ route('admin.sessions') }}"
        class="bg-white border border-gray-200 rounded-xl p-5 hover:border-blue-300 transition group">
        <p class="font-medium group-hover:text-blue-700">📅 Sessions</p>
        <p class="text-sm text-gray-400 mt-1">{{ $session?->name ?? 'None' }}</p>
    </a>
    
    <a href="{{ route('admin.audit-logs') }}"
        class="bg-white border border-gray-200 rounded-xl p-5 hover:border-blue-300 transition group">
        <p class="font-medium group-hover:text-blue-700">📋 Audit Log</p>
        <p class="text-sm text-gray-400 mt-1">System actions</p>
    </a>
</div>
@endif
@endsection


{{-- ════════════════════════════════════════════════════════════════ --}}
