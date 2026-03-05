@extends('layouts.app')
@section('title', 'Muhasebe')

@section('content')
<div class="space-y-6">

    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Muhasebe</h2>
            <p class="text-sm text-gray-500 mt-1">Tekdüzen Hesap Planı · Yevmiye · Mizan · Bilanço · Gelir Tablosu</p>
        </div>
        @if($accountPlanLoaded)
            <a href="{{ route('accounting.journal.create') }}"
               class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                <i class="fas fa-plus"></i> Yeni Fiş
            </a>
        @endif
    </div>

    @if(!$accountPlanLoaded)
    {{-- Hesap Planı yüklenmemiş --}}
    <div class="bg-amber-50 border border-amber-300 rounded-xl p-6 flex items-start gap-4">
        <i class="fas fa-triangle-exclamation text-amber-500 text-2xl mt-0.5"></i>
        <div>
            <h3 class="font-semibold text-amber-900 mb-1">Hesap Planı Henüz Yüklenmedi</h3>
            <p class="text-sm text-amber-700 mb-3">Muhasebe modülünü kullanmak için önce Türkiye Tekdüzen Hesap Planı'nı yüklemeniz gerekiyor.</p>
            <form action="{{ route('accounting.setup') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-amber-700 transition">
                    <i class="fas fa-download mr-2"></i> Hesap Planını Yükle ({{ $stats['total_accounts'] == 0 ? '~150 hesap' : $stats['total_accounts'] . ' hesap' }})
                </button>
            </form>
        </div>
    </div>
    @else

    {{-- Özet Kartlar --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_entries']) }}</div>
            <div class="text-xs text-gray-500 mt-1">Toplam Fiş</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center border-l-4 border-l-green-400">
            <div class="text-2xl font-bold text-green-600">{{ number_format($stats['posted_entries']) }}</div>
            <div class="text-xs text-gray-500 mt-1">Kesinleşen</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center border-l-4 border-l-yellow-400">
            <div class="text-2xl font-bold text-yellow-600">{{ number_format($stats['draft_entries']) }}</div>
            <div class="text-xs text-gray-500 mt-1">Taslak</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center border-l-4 border-l-indigo-400">
            <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_accounts']) }}</div>
            <div class="text-xs text-gray-500 mt-1">Aktif Hesap</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center border-l-4 border-l-blue-400">
            <div class="text-xl font-bold text-blue-600">₺{{ number_format($stats['monthly_debit'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Bu Ay Borç</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center border-l-4 border-l-purple-400">
            <div class="text-xl font-bold text-purple-600">₺{{ number_format($stats['monthly_credit'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Bu Ay Alacak</div>
        </div>
    </div>

    {{-- Hızlı Erişim --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('accounting.journal.index') }}"
           class="bg-white rounded-xl border p-5 hover:border-indigo-300 hover:shadow-sm transition group">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-indigo-600 transition">
                <i class="fas fa-book text-indigo-600 group-hover:text-white transition"></i>
            </div>
            <div class="font-semibold text-gray-800 text-sm">Yevmiye Defteri</div>
            <div class="text-xs text-gray-500 mt-0.5">Tüm muhasebe fişleri</div>
        </a>
        <a href="{{ route('accounting.trial-balance') }}"
           class="bg-white rounded-xl border p-5 hover:border-indigo-300 hover:shadow-sm transition group">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-green-600 transition">
                <i class="fas fa-scale-balanced text-green-600 group-hover:text-white transition"></i>
            </div>
            <div class="font-semibold text-gray-800 text-sm">Mizan</div>
            <div class="text-xs text-gray-500 mt-0.5">Hesap bakiyeleri</div>
        </a>
        <a href="{{ route('accounting.balance-sheet') }}"
           class="bg-white rounded-xl border p-5 hover:border-indigo-300 hover:shadow-sm transition group">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-blue-600 transition">
                <i class="fas fa-building-columns text-blue-600 group-hover:text-white transition"></i>
            </div>
            <div class="font-semibold text-gray-800 text-sm">Bilanço</div>
            <div class="text-xs text-gray-500 mt-0.5">Aktif / Pasif tablosu</div>
        </a>
        <a href="{{ route('accounting.income-statement') }}"
           class="bg-white rounded-xl border p-5 hover:border-indigo-300 hover:shadow-sm transition group">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-purple-600 transition">
                <i class="fas fa-chart-line text-purple-600 group-hover:text-white transition"></i>
            </div>
            <div class="font-semibold text-gray-800 text-sm">Gelir Tablosu</div>
            <div class="text-xs text-gray-500 mt-0.5">Kâr / Zarar analizi</div>
        </a>
        <a href="{{ route('accounting.scan') }}"
           class="bg-white rounded-xl border p-5 hover:border-indigo-300 hover:shadow-sm transition group">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-indigo-600 transition">
                <i class="fas fa-qrcode text-indigo-600 group-hover:text-white transition"></i>
            </div>
            <div class="font-semibold text-gray-800 text-sm">QR Tara</div>
            <div class="text-xs text-gray-500 mt-0.5">Fişi kamerayla tara</div>
        </a>
        <a href="{{ route('accounting.journal.camera') }}"
           class="bg-white rounded-xl border p-5 hover:border-indigo-300 hover:shadow-sm transition group">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mb-3 group-hover:bg-orange-600 transition">
                <i class="fas fa-camera text-orange-600 group-hover:text-white transition"></i>
            </div>
            <div class="font-semibold text-gray-800 text-sm">Kamera Fiş</div>
            <div class="text-xs text-gray-500 mt-0.5">Personel / gider fişi</div>
        </a>
    </div>

    {{-- Son Fişler --}}
    <div class="bg-white rounded-xl border shadow-sm">
        <div class="flex items-center justify-between px-5 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Son Fişler</h3>
            <a href="{{ route('accounting.journal.index') }}" class="text-xs text-indigo-600 hover:underline">Tümünü Gör →</a>
        </div>
        @if($recentEntries->isEmpty())
            <div class="p-10 text-center text-gray-400 text-sm">
                <i class="fas fa-inbox text-3xl mb-3 block"></i>Henüz hiç fiş yok.
                <div class="mt-2">
                    <a href="{{ route('accounting.journal.create') }}" class="text-indigo-600 hover:underline">İlk fişi oluştur →</a>
                </div>
            </div>
        @else
            <div class="divide-y">
                @foreach($recentEntries as $entry)
                <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $entry->is_posted ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $entry->is_posted ? '✓' : '~' }}
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $entry->entry_no }}</p>
                            <p class="text-xs text-gray-500">{{ $entry->description }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-700">₺{{ number_format($entry->lines->sum('debit'), 2, ',', '.') }}</p>
                        <p class="text-xs text-gray-400">{{ $entry->date->format('d.m.Y') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Hesap Planı Linki + Örnek Fişler --}}
    <div class="grid md:grid-cols-2 gap-4">
        <div class="flex items-center justify-between bg-gray-50 rounded-xl border p-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-list text-gray-400 text-lg"></i>
                <div>
                    <p class="text-sm font-medium text-gray-700">Tekdüzen Hesap Planı</p>
                    <p class="text-xs text-gray-500">{{ $stats['total_accounts'] }} aktif hesap yüklü</p>
                </div>
            </div>
            <a href="{{ route('accounting.account-plan') }}" class="text-sm text-indigo-600 hover:underline">
                Hesapları Yönet →
            </a>
        </div>
        <div class="flex items-center justify-between bg-amber-50 rounded-xl border border-amber-200 p-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-wand-magic-sparkles text-amber-500 text-lg"></i>
                <div>
                    <p class="text-sm font-medium text-amber-800">Örnek Fişler</p>
                    <p class="text-xs text-amber-600">Test ve demo için örnek yevmiye fişleri</p>
                </div>
            </div>
            <form action="{{ route('accounting.sample-entries') }}" method="POST">
                @csrf
                <button type="submit"
                        onclick="return confirm('Toplam 8 adet örnek fiş eklenecek. Devam?')"
                        class="text-sm bg-amber-500 text-white px-3 py-1.5 rounded-lg hover:bg-amber-600 transition">
                    <i class="fas fa-plus mr-1"></i> Yükle
                </button>
            </form>
        </div>
    </div>

    @endif

</div>
@endsection
