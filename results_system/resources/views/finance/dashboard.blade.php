@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Finance Department Dashboard</h1>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Total Students</div>
            <div class="text-3xl font-bold text-blue-600">{{ $stats['total_students'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Cleared</div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['cleared'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Pending Fees</div>
            <div class="text-3xl font-bold text-red-600">{{ $stats['pending'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Finance Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Manage Clearances</h2>
        <p class="text-gray-600 mb-4">Verify tuition payment status and clear students with no outstanding balances.</p>
        <a href="{{ route('finance.clearances') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Manage Clearances
        </a>
    </div>
</div>
@endsection
