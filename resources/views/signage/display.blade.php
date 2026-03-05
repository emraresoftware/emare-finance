<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emare Digital Signage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body{margin:0;padding:0;overflow:hidden;cursor:none;}
        @keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        @keyframes pulse-slow{0%,100%{opacity:1}50%{opacity:.7}}
        @keyframes ticker{0%{transform:translateX(100%)}100%{transform:translateX(-100%)}}
        .anim-slide-up{animation:slideUp .6s ease}
        .anim-fade{animation:fadeIn .8s ease}
        .anim-ticker{animation:ticker 30s linear infinite}
        .anim-pulse-slow{animation:pulse-slow 3s ease-in-out infinite}
    </style>
</head>
<body class="h-full" x-data="signageDisplay()">

{{-- ═══════════════════════════════════════ --}}
{{-- MENÜ PANOSU --}}
{{-- ═══════════════════════════════════════ --}}
<div x-show="template === 'menu-board'" class="h-full flex flex-col bg-gradient-to-br from-amber-950 via-amber-900 to-yellow-900">
    {{-- Üst banner --}}
    <div class="bg-black/30 px-8 py-4 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-utensils text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-white">MENÜ</h1>
                <p class="text-amber-300 text-sm">Taze • Lezzetli • Her Gün</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-4xl font-bold text-amber-300" x-text="currentTime"></p>
            <p class="text-amber-400/60 text-sm" x-text="currentDate"></p>
        </div>
    </div>

    {{-- İçerik grid --}}
    <div class="flex-1 p-6 overflow-hidden">
        <div class="grid grid-cols-3 gap-6 h-full">
            @foreach($categories->take(3) as $idx => $cat)
            <div class="anim-slide-up" style="animation-delay: {{ $idx * 0.15 }}s">
                <h2 class="text-xl font-bold text-amber-300 mb-3 pb-2 border-b border-amber-700/50 flex items-center gap-2">
                    <i class="fas fa-fire text-orange-400"></i>
                    {{ strtoupper($cat->name) }}
                </h2>
                <div class="space-y-2">
                    @foreach($products->where('category_id', $cat->id)->take(8) as $product)
                    <div class="flex items-center justify-between text-white py-1.5">
                        <span class="text-base">{{ $product->name }}</span>
                        <span class="text-lg font-bold text-amber-300">{{ number_format($product->sale_price, 2, ',', '.') }} ₺</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Alt kampanya bandı --}}
    <div class="bg-amber-500 px-6 py-3 flex-shrink-0 overflow-hidden">
        <div class="anim-ticker whitespace-nowrap text-amber-950 font-bold text-lg">
            🔥 Günün Fırsatı: Tüm Sıcak İçeceklerde %20 İndirim &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
            ⭐ Yeni Lezzetlerimizi Denediniz mi? &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
            🎉 Sadakat Kartınızla Her 10. Siparişte 1 Hediye! &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
            📱 Mobil Sipariş ile Kuyruğa Girmeden Alın!
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════ --}}
{{-- FİYAT LİSTESİ --}}
{{-- ═══════════════════════════════════════ --}}
<div x-show="template === 'price-list'" class="h-full flex flex-col bg-gradient-to-br from-slate-50 to-emerald-50">
    <div class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-tags text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Güncel Fiyat Listesi</h1>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-400" x-text="currentDate"></span>
            <span class="text-xl font-bold text-emerald-600" x-text="currentTime"></span>
        </div>
    </div>
    <div class="flex-1 overflow-hidden p-6">
        <div class="grid grid-cols-4 gap-4 h-full auto-rows-min">
            @foreach($products->take(24) as $idx => $product)
            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-sm flex items-center gap-3 anim-slide-up" style="animation-delay:{{ $idx * 0.04 }}s">
                <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-cube text-emerald-400 text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $product->name }}</p>
                    <p class="text-xs text-gray-400">{{ $product->barcode ?? 'Barkod Yok' }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-lg font-bold text-emerald-600">{{ number_format($product->sale_price, 2, ',', '.') }} ₺</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="bg-emerald-600 text-white text-center py-2 text-sm flex-shrink-0">
        Fiyatlarımıza KDV dahildir • Emare Finance ile güncellenmektedir
    </div>
</div>


{{-- ═══════════════════════════════════════ --}}
{{-- KAMPANYA & DUYURU --}}
{{-- ═══════════════════════════════════════ --}}
<div x-show="template === 'promo'" class="h-full relative bg-black" x-data="promoSlider()">
    {{-- Slaytlar --}}
    <template x-for="(slide, idx) in slides" :key="idx">
        <div x-show="current === idx" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
             class="absolute inset-0 flex items-center justify-center" :class="slide.bg">
            <div class="text-center max-w-3xl px-8">
                <div class="text-8xl mb-6" x-text="slide.emoji"></div>
                <h2 class="text-5xl font-black text-white mb-4 leading-tight" x-text="slide.title"></h2>
                <p class="text-xl text-white/80" x-text="slide.desc"></p>
                <div x-show="slide.badge" class="mt-6 inline-block bg-white/20 backdrop-blur-sm rounded-full px-6 py-3">
                    <span class="text-white text-2xl font-bold" x-text="slide.badge"></span>
                </div>
            </div>
        </div>
    </template>
    {{-- İlerleme noktaları --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2">
        <template x-for="(slide, idx) in slides" :key="idx">
            <div class="w-3 h-3 rounded-full transition-all" :class="current === idx ? 'bg-white scale-125' : 'bg-white/30'"></div>
        </template>
    </div>
    {{-- Logo --}}
    <div class="absolute top-6 right-8 text-white/30 text-sm">
        <i class="fas fa-circle-notch anim-pulse-slow mr-1"></i> Emare Finance
    </div>
</div>


{{-- ═══════════════════════════════════════ --}}
{{-- SIRA / ÇAĞRI EKRANI --}}
{{-- ═══════════════════════════════════════ --}}
<div x-show="template === 'queue'" class="h-full flex flex-col bg-gradient-to-br from-blue-950 to-slate-900" x-data="queueDisplay()">
    <div class="bg-blue-900/50 px-8 py-5 flex items-center justify-between flex-shrink-0 border-b border-blue-800/40">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-list-ol text-white text-xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">Sıra Takip Ekranı</h1>
        </div>
        <div class="text-5xl font-bold text-blue-300" x-text="currentTime"></div>
    </div>

    <div class="flex-1 flex overflow-hidden">
        {{-- Aktif çağrı --}}
        <div class="flex-1 flex items-center justify-center">
            <div class="text-center">
                <p class="text-blue-400 text-lg mb-2 uppercase tracking-widest">Şu An Çağrılan</p>
                <div class="text-[12rem] font-black text-white leading-none anim-pulse-slow" x-text="activeNumber">-</div>
                <p class="text-3xl text-blue-300 mt-4" x-text="activeCounter">-</p>
            </div>
        </div>
        {{-- Bekleyenler --}}
        <div class="w-96 bg-blue-900/30 border-l border-blue-800/40 p-6 flex flex-col">
            <h3 class="text-blue-400 text-sm font-semibold uppercase tracking-wider mb-4">Sıradaki Numaralar</h3>
            <div class="flex-1 space-y-3 overflow-y-auto">
                <template x-for="(item, idx) in waitingList" :key="idx">
                    <div class="bg-blue-800/30 rounded-xl px-5 py-4 flex items-center justify-between border border-blue-700/30">
                        <span class="text-3xl font-bold text-white" x-text="item.number"></span>
                        <span class="text-blue-400 text-sm" x-text="item.counter"></span>
                    </div>
                </template>
            </div>
            <div class="mt-4 pt-4 border-t border-blue-800/40 text-center">
                <p class="text-blue-400/60 text-sm">Bekleyen: <span class="text-white font-bold" x-text="waitingList.length"></span></p>
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════ --}}
{{-- KARŞILAMA EKRANI --}}
{{-- ═══════════════════════════════════════ --}}
<div x-show="template === 'welcome'" class="h-full flex items-center justify-center bg-gradient-to-br from-violet-950 via-purple-900 to-fuchsia-900 relative overflow-hidden">
    {{-- Dekoratif daireler --}}
    <div class="absolute w-96 h-96 bg-purple-500/10 rounded-full -top-20 -left-20"></div>
    <div class="absolute w-80 h-80 bg-fuchsia-500/10 rounded-full -bottom-16 -right-16"></div>
    <div class="absolute w-64 h-64 bg-violet-400/10 rounded-full top-1/2 left-1/4"></div>

    <div class="text-center relative z-10 anim-fade">
        <div class="w-28 h-28 bg-white/10 backdrop-blur-lg rounded-3xl flex items-center justify-center mx-auto mb-8 border border-white/20">
            <i class="fas fa-building text-5xl text-white/90"></i>
        </div>
        <h1 class="text-6xl font-black text-white mb-4">Hoş Geldiniz</h1>
        <p class="text-2xl text-purple-200 mb-8">Emare Finance İşletmesine</p>
        <div class="flex items-center justify-center gap-8 text-white/60">
            <div class="text-center">
                <p class="text-5xl font-bold text-white" x-text="currentTime"></p>
                <p class="text-sm mt-1" x-text="currentDate"></p>
            </div>
        </div>
        <div class="mt-12 flex items-center justify-center gap-6 text-white/30 text-sm">
            <span><i class="fas fa-wifi mr-1"></i> Wi-Fi: EMARE-GUEST</span>
            <span>•</span>
            <span><i class="fas fa-phone mr-1"></i> 0212 555 00 00</span>
            <span>•</span>
            <span><i class="fas fa-globe mr-1"></i> emare.com.tr</span>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════ --}}
{{-- İŞLETME DASHBOARD TV --}}
{{-- ═══════════════════════════════════════ --}}
<div x-show="template === 'dashboard-tv'" class="h-full flex flex-col bg-gradient-to-br from-gray-950 to-cyan-950">
    <div class="bg-black/30 px-8 py-3 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
            <i class="fas fa-chart-line text-cyan-400 text-xl"></i>
            <span class="text-white font-semibold text-lg">İşletme Dashboard</span>
        </div>
        <div class="flex items-center gap-6 text-sm">
            <span class="text-cyan-300" x-text="currentDate"></span>
            <span class="text-2xl font-bold text-white" x-text="currentTime"></span>
        </div>
    </div>
    <div class="flex-1 p-6 grid grid-cols-4 grid-rows-3 gap-4">
        {{-- KPI Kartları --}}
        <div class="bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-cyan-500/20 flex flex-col justify-center">
            <p class="text-cyan-400 text-xs uppercase tracking-wider mb-1">Günlük Satış</p>
            <p class="text-4xl font-black text-white">₺12.450</p>
            <p class="text-green-400 text-sm mt-1"><i class="fas fa-arrow-up"></i> %12 artış</p>
        </div>
        <div class="bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-cyan-500/20 flex flex-col justify-center">
            <p class="text-cyan-400 text-xs uppercase tracking-wider mb-1">İşlem Sayısı</p>
            <p class="text-4xl font-black text-white">87</p>
            <p class="text-green-400 text-sm mt-1"><i class="fas fa-arrow-up"></i> %8 artış</p>
        </div>
        <div class="bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-cyan-500/20 flex flex-col justify-center">
            <p class="text-cyan-400 text-xs uppercase tracking-wider mb-1">Ortalama Sepet</p>
            <p class="text-4xl font-black text-white">₺143</p>
            <p class="text-amber-400 text-sm mt-1"><i class="fas fa-minus"></i> Sabit</p>
        </div>
        <div class="bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-cyan-500/20 flex flex-col justify-center">
            <p class="text-cyan-400 text-xs uppercase tracking-wider mb-1">Aktif Personel</p>
            <p class="text-4xl font-black text-white">5</p>
            <p class="text-cyan-300/50 text-sm mt-1">/ 8 toplam</p>
        </div>
        {{-- Grafik alanı --}}
        <div class="col-span-2 row-span-2 bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-cyan-500/20">
            <p class="text-cyan-400 text-xs uppercase tracking-wider mb-4">Saatlik Satış Grafiği</p>
            <div class="h-full flex items-end gap-2 pb-8">
                <template x-for="(v, i) in [30,45,65,80,55,90,100,85,70,60,45,75]" :key="i">
                    <div class="flex-1 bg-gradient-to-t from-cyan-500 to-cyan-400 rounded-t-lg transition-all" :style="'height:' + v + '%'"></div>
                </template>
            </div>
        </div>
        {{-- En çok satanlar --}}
        <div class="col-span-2 row-span-2 bg-white/5 backdrop-blur-sm rounded-xl p-5 border border-cyan-500/20">
            <p class="text-cyan-400 text-xs uppercase tracking-wider mb-4">En Çok Satanlar (Bugün)</p>
            <div class="space-y-3">
                @foreach($products->take(6) as $idx => $p)
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 bg-cyan-500/20 rounded-full flex items-center justify-center text-xs text-cyan-300 font-bold">{{ $idx + 1 }}</span>
                    <span class="flex-1 text-white text-sm truncate">{{ $p->name }}</span>
                    <span class="text-cyan-300 text-sm font-bold">{{ rand(5,25) }} ad</span>
                    <span class="text-white text-sm">{{ number_format($p->sale_price * rand(5,25), 0, ',', '.') }} ₺</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════ --}}
{{-- SOSYAL MEDYA DUVARI --}}
{{-- ═══════════════════════════════════════ --}}
<div x-show="template === 'social-wall'" class="h-full flex flex-col bg-gradient-to-br from-pink-950 to-purple-950">
    <div class="bg-black/30 px-8 py-4 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
            <i class="fas fa-hashtag text-pink-400 text-2xl"></i>
            <span class="text-white font-bold text-xl">#EmareLezzetleri</span>
        </div>
        <div class="flex items-center gap-4 text-sm text-pink-300/60">
            <span><i class="fab fa-instagram mr-1"></i> @emare</span>
            <span><i class="fab fa-twitter mr-1"></i> @emare</span>
        </div>
    </div>
    <div class="flex-1 p-6 grid grid-cols-4 gap-4 auto-rows-fr overflow-hidden">
        <template x-for="i in 8" :key="i">
            <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-pink-500/20 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-pink-500/30 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-pink-300 text-xs"></i>
                        </div>
                        <span class="text-white text-sm font-medium" x-text="'@kullanici' + i"></span>
                    </div>
                    <p class="text-white/70 text-sm" x-text="reviews[i-1]"></p>
                </div>
                <div class="flex items-center gap-3 mt-3 text-xs text-pink-300/50">
                    <span><i class="fas fa-heart mr-1"></i> <span x-text="Math.floor(Math.random()*200)+10"></span></span>
                    <span><i class="fas fa-comment mr-1"></i> <span x-text="Math.floor(Math.random()*30)+1"></span></span>
                </div>
            </div>
        </template>
    </div>
</div>


{{-- ═══════════════════════════════════════ --}}
{{-- YÖNLENDİRME EKRANI --}}
{{-- ═══════════════════════════════════════ --}}
<div x-show="template === 'wayfinding'" class="h-full flex flex-col bg-gradient-to-br from-teal-950 to-slate-900">
    <div class="bg-teal-900/50 px-8 py-4 flex items-center justify-between flex-shrink-0 border-b border-teal-800/40">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-map-signs text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Yönlendirme</h1>
        </div>
        <span class="text-teal-300 text-xl font-bold" x-text="currentTime"></span>
    </div>
    <div class="flex-1 p-8 flex gap-8">
        <div class="flex-1 grid grid-cols-2 gap-4">
            <template x-for="(area, idx) in wayfindingAreas" :key="idx">
                <div class="bg-white/5 backdrop-blur-sm rounded-2xl border border-teal-500/20 p-6 flex items-center gap-4 hover:bg-white/10 transition">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0" :class="area.bg">
                        <i class="fas text-2xl text-white" :class="area.icon"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white" x-text="area.name"></h3>
                        <p class="text-teal-300/70 text-sm" x-text="area.floor"></p>
                    </div>
                    <div class="ml-auto text-teal-400">
                        <i class="fas fa-arrow-right text-xl"></i>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

@if(isset($preview) && $preview)
{{-- Önizleme kontrolü --}}
<div class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-black/80 backdrop-blur-lg rounded-full px-6 py-3 flex items-center gap-4 z-50" style="cursor:default">
    <span class="text-white/60 text-sm">Önizleme Modu</span>
    <a href="{{ route('signage.index') }}" class="text-purple-400 hover:text-purple-300 text-sm font-medium" style="cursor:pointer">
        <i class="fas fa-arrow-left mr-1"></i> Yönetime Dön
    </a>
</div>
@endif

<script>
function signageDisplay() {
    return {
        template: '{{ $template }}',
        currentTime: '',
        currentDate: '',

        reviews: [
            'Harika bir mekan, çok beğendik! 😍',
            'Kahveleri muhteşem, kesinlikle tekrar geleceğim ☕',
            'Servis çok hızlı ve personel çok ilgili 👍',
            'Atmosferi çok güzel, rahat bir ortam 🎵',
            'Yemekleri çok taze ve lezzetli 🍽️',
            'Her geldiğimizde memnun ayrılıyoruz ⭐',
            'Fiyat/performans olarak çok iyi 💯',
            'Çocuklara özel menü çok düşünceli 🧒',
        ],

        wayfindingAreas: [
            { name: 'Ana Giriş', floor: 'Zemin Kat', icon: 'fa-door-open', bg: 'bg-teal-500' },
            { name: 'Restoran', floor: '1. Kat', icon: 'fa-utensils', bg: 'bg-amber-500' },
            { name: 'Toplantı Odaları', floor: '2. Kat', icon: 'fa-users', bg: 'bg-blue-500' },
            { name: 'Mağaza', floor: 'Zemin Kat', icon: 'fa-shopping-bag', bg: 'bg-emerald-500' },
            { name: 'Otopark', floor: 'Bodrum', icon: 'fa-car', bg: 'bg-slate-500' },
            { name: 'Tuvaletler', floor: 'Her Kat', icon: 'fa-restroom', bg: 'bg-violet-500' },
            { name: 'Danışma', floor: 'Zemin Kat', icon: 'fa-circle-info', bg: 'bg-cyan-500' },
            { name: 'Acil Çıkış', floor: 'Her Kat', icon: 'fa-person-running', bg: 'bg-red-500' },
        ],

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);
        },

        updateClock() {
            const now = new Date();
            this.currentTime = now.toLocaleTimeString('tr-TR', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
            this.currentDate = now.toLocaleDateString('tr-TR', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
        },
    };
}

function promoSlider() {
    return {
        current: 0,
        slides: [
            { title: 'Yeni Sezon Menümüz Hazır!', desc: 'Birbirinden lezzetli yeni tatlar sizi bekliyor.', emoji: '🍽️', bg: 'bg-gradient-to-br from-orange-600 to-red-700', badge: null },
            { title: '%30 İndirim Fırsatı!', desc: 'Tüm sıcak içeceklerde hafta sonu kampanyası.', emoji: '☕', bg: 'bg-gradient-to-br from-emerald-600 to-teal-700', badge: '%30 İNDİRİM' },
            { title: 'Sadakat Programı', desc: 'Her alışverişte puan kazanın, hediyelerle buluşun.', emoji: '⭐', bg: 'bg-gradient-to-br from-violet-600 to-purple-700', badge: '2X PUAN' },
            { title: 'Mobil Sipariş Açıldı!', desc: 'Telefonunuzdan sipariş verin, hazır gelsin.', emoji: '📱', bg: 'bg-gradient-to-br from-blue-600 to-indigo-700', badge: 'ÜCRETSİZ KARGO' },
        ],
        init() {
            setInterval(() => { this.current = (this.current + 1) % this.slides.length; }, 6000);
        }
    };
}

function queueDisplay() {
    return {
        activeNumber: 'A-042',
        activeCounter: 'Gişe 1',
        waitingList: [
            { number: 'A-043', counter: 'Gişe 2' },
            { number: 'A-044', counter: 'Gişe 3' },
            { number: 'B-012', counter: 'Gişe 1' },
            { number: 'B-013', counter: 'Gişe 2' },
            { number: 'C-005', counter: 'Gişe 1' },
        ],
        init() {
            // Demo: Her 10 sn'de sıra ilerletme
            setInterval(() => {
                if (this.waitingList.length > 0) {
                    const next = this.waitingList.shift();
                    this.activeNumber = next.number;
                    this.activeCounter = next.counter;
                    // Yeni numara ekle
                    const prefix = ['A','B','C'][Math.floor(Math.random()*3)];
                    const num = String(Math.floor(Math.random()*90)+10).padStart(3,'0');
                    this.waitingList.push({ number: prefix + '-' + num, counter: 'Gişe ' + (Math.floor(Math.random()*3)+1) });
                }
            }, 10000);
        }
    };
}
</script>

</body>
</html>
