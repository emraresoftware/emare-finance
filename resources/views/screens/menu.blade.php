<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ekran Seçici - Emare Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important} body{-webkit-user-select:none;user-select:none;}</style>
</head>
<body class="h-full bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 overflow-hidden">

<div class="h-full flex flex-col items-center justify-center p-6" x-data>
    {{-- Logo --}}
    <div class="text-center mb-12">
        <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-indigo-500/30">
            <span class="text-white font-bold text-2xl">EF</span>
        </div>
        <h1 class="text-3xl font-bold text-white">Emare Finance</h1>
        <p class="text-indigo-300 mt-2">Çalışma ekranınızı seçin</p>
    </div>

    {{-- Ekran Kartları --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl w-full">

        {{-- POS Ekranı --}}
        <a href="{{ route('screens.pos') }}"
           class="group relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 text-center hover:bg-white/10 hover:border-indigo-400/50 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 cursor-pointer">
            <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-cash-register text-white text-2xl"></i>
            </div>
            <h3 class="text-white font-semibold text-lg">POS Ekranı</h3>
            <p class="text-indigo-300 text-sm mt-2">Hızlı satış ve kasa işlemleri</p>
            <div class="mt-4 flex items-center justify-center gap-2 text-xs text-indigo-400">
                <i class="fas fa-desktop"></i>
                <span>Dokunmatik / Masaüstü</span>
            </div>
        </a>

        {{-- Sipariş Ekranı --}}
        <a href="{{ route('screens.order') }}"
           class="group relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 text-center hover:bg-white/10 hover:border-amber-400/50 hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300 cursor-pointer">
            <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-amber-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-utensils text-white text-2xl"></i>
            </div>
            <h3 class="text-white font-semibold text-lg">Sipariş Ekranı</h3>
            <p class="text-indigo-300 text-sm mt-2">Kafe & restoran siparişleri</p>
            <div class="mt-4 flex items-center justify-center gap-2 text-xs text-amber-400">
                <i class="fas fa-hand-pointer"></i>
                <span>Dokunmatik Tablet</span>
            </div>
        </a>

        {{-- El Terminali --}}
        <a href="{{ route('screens.terminal') }}"
           class="group relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 text-center hover:bg-white/10 hover:border-cyan-400/50 hover:shadow-xl hover:shadow-cyan-500/10 transition-all duration-300 cursor-pointer">
            <div class="w-16 h-16 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-cyan-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-mobile-screen text-white text-2xl"></i>
            </div>
            <h3 class="text-white font-semibold text-lg">El Terminali</h3>
            <p class="text-indigo-300 text-sm mt-2">Stok sayım & fiyat sorgulama</p>
            <div class="mt-4 flex items-center justify-center gap-2 text-xs text-cyan-400">
                <i class="fas fa-barcode"></i>
                <span>Mobil / El Terminali</span>
            </div>
        </a>

        {{-- Yönetim Paneli --}}
        <a href="{{ route('dashboard') }}"
           class="group relative bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-8 text-center hover:bg-white/10 hover:border-purple-400/50 hover:shadow-xl hover:shadow-purple-500/10 transition-all duration-300 cursor-pointer">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-gauge-high text-white text-2xl"></i>
            </div>
            <h3 class="text-white font-semibold text-lg">Yönetim Paneli</h3>
            <p class="text-indigo-300 text-sm mt-2">Dashboard & tam yönetim</p>
            <div class="mt-4 flex items-center justify-center gap-2 text-xs text-purple-400">
                <i class="fas fa-laptop"></i>
                <span>Masaüstü / Laptop</span>
            </div>
        </a>

    </div>

    {{-- Alt Bilgi --}}
    <div class="mt-12 text-center">
        <p class="text-indigo-400/60 text-sm">
            <i class="fas fa-keyboard mr-1"></i>
            Kısayol: <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-xs">F2</kbd> POS
            <span class="mx-2">·</span>
            <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-xs">F3</kbd> Sipariş
            <span class="mx-2">·</span>
            <kbd class="px-1.5 py-0.5 bg-white/10 rounded text-xs">F4</kbd> Terminal
        </p>
    </div>
</div>

<script>
document.addEventListener('keydown', e => {
    if (e.key === 'F2') window.location.href = '{{ route("screens.pos") }}';
    if (e.key === 'F3') window.location.href = '{{ route("screens.order") }}';
    if (e.key === 'F4') window.location.href = '{{ route("screens.terminal") }}';
});
</script>

</body>
</html>
