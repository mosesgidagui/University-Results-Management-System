{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Results System') — University</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">

<nav class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
    <span class="font-semibold text-lg text-blue-700">Results Clearance System</span>
    <div class="flex items-center gap-6 text-sm">
        <span class="text-gray-500">{{ auth()->user()->name }}
            <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                {{ strtoupper(auth()->user()->role) }}
            </span>
        </span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-gray-400 hover:text-red-500">Logout</button>
        </form>
    </div>
</nav>

<main class="max-w-6xl mx-auto p-6">

    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-4 p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-sm">
            {{ session('warning') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
