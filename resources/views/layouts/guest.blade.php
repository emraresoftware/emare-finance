<!DOCTYPE html>
<html lang="tr" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Giriş') - Emare Finance</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full">

<div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    {{-- Ana Sayfa butonu --}}
    <div class="sm:mx-auto sm:w-full sm:max-w-md mb-4">
        <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Ana Sayfaya Dön
        </a>
    </div>

    {{-- Logo --}}
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center">
            <div class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-2xl">EF</span>
            </div>
        </div>
        <h2 class="mt-4 text-center text-3xl font-bold tracking-tight text-gray-900">Emare Finance</h2>
        <p class="mt-2 text-center text-sm text-gray-600">@yield('subtitle', 'İşletme yönetim platformu')</p>
    </div>

    {{-- Flash mesajlar --}}
    <div class="sm:mx-auto sm:w-full sm:max-w-md mt-4">
        @if(session('success'))
            <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        @if(session('status'))
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">
                <i class="fas fa-info-circle mr-2"></i>{{ session('status') }}
            </div>
        @endif
    </div>

    {{-- İçerik --}}
    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-xl rounded-xl sm:px-10 border border-gray-100">
            @yield('content')
        </div>
    </div>

    {{-- Alt bilgi --}}
    <div class="mt-6 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} Emare Finance. Tüm hakları saklıdır.
    </div>
</div>

</body>
</html>
