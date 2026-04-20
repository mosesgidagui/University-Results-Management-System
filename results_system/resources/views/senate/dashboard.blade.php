@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">University Senate Dashboard</h1>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">For Review</div>
            <div class="text-3xl font-bold text-orange-600">{{ $stats['for_review'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Approved</div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-gray-500 text-sm font-medium">Published</div>
            <div class="text-3xl font-bold text-blue-600">{{ $stats['published'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Senate Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Review Results</h2>
            <p class="text-gray-600 mb-4">Review compiled examination results from the Academic Registrar.</p>
            <a href="{{ route('senate.results') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Review Results
            </a>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Publish Results</h2>
            <p class="text-gray-600 mb-4">Approve and authorize the release of results to students.</p>
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Publish Results
            </button>
        </div>
    </div>
</div>
@endsection
