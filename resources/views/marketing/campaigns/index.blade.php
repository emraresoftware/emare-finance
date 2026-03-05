@extends('layouts.app')
@section('title', 'Kampanyalar')

@section('content')
{{-- Başlık --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('marketing.index') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Kampanyalar</h1>
            <p class="text-sm text-gray-500 mt-1">İndirim ve promosyon kampanyalarını yönetin</p>
        </div>
    </div>
    <a href="{{ route('marketing.campaigns.create') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 font-medium">
        <i class="fas fa-plus mr-1"></i> Yeni Kampanya
    </a>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Ara</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Kampanya adı, kupon kodu..."
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Durum</label>
            <select name="status" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Pasif</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Süresi Dolmuş</option>
                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Planlanmış</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tür</label>
            <select name="type" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="discount" {{ request('type') == 'discount' ? 'selected' : '' }}>İndirim</option>
                <option value="coupon" {{ request('type') == 'coupon' ? 'selected' : '' }}>Kupon</option>
                <option value="bogo" {{ request('type') == 'bogo' ? 'selected' : '' }}>Al-Öde</option>
                <option value="free_shipping" {{ request('type') == 'free_shipping' ? 'selected' : '' }}>Ücretsiz Kargo</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-search mr-1"></i> Filtrele
        </button>
        <a href="{{ route('marketing.campaigns.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Temizle</a>
    </form>
</div>

{{-- Kampanya Kartları --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($campaigns as $campaign)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $campaign->status_color }}-100 text-{{ $campaign->status_color }}-800">
                        {{ $campaign->status_label }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                        {{ $campaign->type_label }}
                    </span>
                </div>
                <a href="{{ route('marketing.campaigns.show', $campaign) }}" class="block">
                    <h3 class="text-lg font-semibold text-gray-800 hover:text-indigo-600 transition">{{ $campaign->name }}</h3>
                </a>
                @if($campaign->description)
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $campaign->description }}</p>
                @endif

                <div class="mt-4 flex items-center gap-4 text-sm">
                    <div class="flex items-center gap-1">
                        <i class="fas fa-tag text-indigo-500"></i>
                        <span class="font-semibold text-indigo-600">
                            @if($campaign->discount_type === 'percentage')
                                %{{ $campaign->discount_value }}
                            @else
                                ₺{{ number_format($campaign->discount_value, 2, ',', '.') }}
                            @endif
                        </span>
                    </div>
                    @if($campaign->coupon_code)
                        <div class="flex items-center gap-1">
                            <i class="fas fa-ticket text-gray-400"></i>
                            <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs font-mono">{{ $campaign->coupon_code }}</code>
                        </div>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2 text-xs text-gray-500">
                    <div>
                        <i class="fas fa-calendar-alt mr-1"></i>
                        @if($campaign->starts_at)
                            {{ $campaign->starts_at->format('d.m.Y') }}
                        @else
                            Süresiz
                        @endif
                    </div>
                    <div>
                        <i class="fas fa-calendar-check mr-1"></i>
                        @if($campaign->ends_at)
                            {{ $campaign->ends_at->format('d.m.Y') }}
                        @else
                            Süresiz
                        @endif
                    </div>
                </div>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-t flex items-center justify-between">
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <span><i class="fas fa-chart-bar mr-1"></i>{{ $campaign->usages_count ?? 0 }} kullanım</span>
                    @if($campaign->usage_limit)
                        <span>/ {{ $campaign->usage_limit }} limit</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('marketing.campaigns.toggle', $campaign) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs px-2 py-1 rounded {{ $campaign->status === 'active' ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }}">
                            {{ $campaign->status === 'active' ? 'Durdur' : 'Aktifleştir' }}
                        </button>
                    </form>
                    <a href="{{ route('marketing.campaigns.show', $campaign) }}" class="text-xs px-2 py-1 rounded bg-indigo-100 text-indigo-600 hover:bg-indigo-200">
                        Detay
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-3 bg-white rounded-xl shadow-sm border p-12 text-center text-gray-400">
            <i class="fas fa-bullhorn text-4xl mb-3"></i>
            <p class="text-lg">Henüz kampanya oluşturulmamış</p>
            <a href="{{ route('marketing.campaigns.create') }}" class="inline-block mt-4 px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                <i class="fas fa-plus mr-1"></i> İlk Kampanyayı Oluştur
            </a>
        </div>
    @endforelse
</div>

@if($campaigns->hasPages())
    <div class="mt-6 flex items-center justify-between">
        <span class="text-sm text-gray-500">Toplam {{ $campaigns->total() }} kampanya</span>
        <div>{{ $campaigns->withQueryString()->links() }}</div>
    </div>
@endif
@endsection
