<!DOCTYPE html>
<html lang="tr" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Süper Admin') - Emare Finance</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full">

<div class="min-h-full" x-data="{ sidebarOpen: true }">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="fixed inset-y-0 left-0 z-50 bg-slate-900 transition-all duration-300 flex flex-col">
        {{-- Logo --}}
        <div class="flex items-center justify-between h-16 px-4 bg-slate-800">
            <a href="{{ route('super-admin.dashboard') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm"><i class="fas fa-shield-halved text-xs"></i></span>
                </div>
                <span x-show="sidebarOpen" x-cloak class="text-white font-semibold text-lg">Süper Admin</span>
            </a>
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        {{-- Menü --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('super-admin.dashboard') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('super-admin.dashboard') ? 'bg-slate-800 text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-gauge-high w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>

            <a href="{{ route('super-admin.firms.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('super-admin.firms.*') ? 'bg-slate-800 text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-building w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Firmalar</span>
                <span x-show="sidebarOpen" class="ml-auto bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full">
                    {{ \App\Models\Tenant::count() }}
                </span>
            </a>

            <a href="{{ route('super-admin.firms.create') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('super-admin.firms.create') ? 'bg-slate-800 text-white' : 'text-gray-300 hover:bg-slate-800 hover:text-white' }}">
                <i class="fas fa-plus-circle w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Yeni Firma Aç</span>
            </a>

            <div class="pt-4 mt-4 border-t border-slate-700">
                <p x-show="sidebarOpen" class="px-3 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Hızlı Erişim</p>

                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:bg-slate-800 hover:text-white">
                    <i class="fas fa-arrow-left w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Ana Panele Dön</span>
                </a>
            </div>
        </nav>

        {{-- Kullanıcı --}}
        @auth
        <div class="border-t border-slate-700 px-3 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center min-w-0">
                    <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    </div>
                    <div x-show="sidebarOpen" class="ml-3 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-red-400 truncate">Süper Admin</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white" title="Çıkış Yap">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </aside>

    {{-- Ana içerik --}}
    <div :class="sidebarOpen ? 'ml-64' : 'ml-20'" class="transition-all duration-300">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <div>
                    <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Süper Admin')</h1>
                    <p class="text-sm text-gray-500 mt-0.5">@yield('subtitle', '')</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-xs bg-red-100 text-red-800 px-2.5 py-1 rounded-full font-medium">
                        <i class="fas fa-shield-halved mr-1"></i>Süper Admin Modu
                    </span>
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ now()->locale('tr')->translatedFormat('d F Y, l') }}
                    </span>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <main class="p-6">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
