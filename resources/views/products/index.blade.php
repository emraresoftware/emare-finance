@extends('layouts.app')
@section('title', 'Ürünler')

@section('content')
{{-- İstatistik Kartları --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Ürün</p>
        <p class="text-xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Aktif Ürün</p>
        <p class="text-xl font-bold text-green-600">{{ number_format($stats['active']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Düşük Stok</p>
        <p class="text-xl font-bold text-orange-600">{{ number_format($stats['low_stock']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Stok Yok</p>
        <p class="text-xl font-bold text-red-600">{{ number_format($stats['out_of_stock']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Stok Değeri</p>
        <p class="text-xl font-bold text-indigo-600">₺{{ number_format($stats['total_value'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Maliyet Değeri</p>
        <p class="text-xl font-bold text-blue-600">₺{{ number_format($stats['total_cost'], 2, ',', '.') }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs text-gray-500 mb-1">Ara</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ürün adı veya barkod..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Kategori</label>
            <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Durum</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="passive" {{ request('status') === 'passive' ? 'selected' : '' }}>Pasif</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sırala</label>
            <select name="sort" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="name" {{ request('sort', 'name') === 'name' ? 'selected' : '' }}>Ada Göre</option>
                <option value="sale_price" {{ request('sort') === 'sale_price' ? 'selected' : '' }}>Fiyata Göre</option>
                <option value="stock_quantity" {{ request('sort') === 'stock_quantity' ? 'selected' : '' }}>Stoğa Göre</option>
                <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Ekleme Tarihi</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Yön</label>
            <select name="dir" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="asc" {{ request('dir', 'asc') === 'asc' ? 'selected' : '' }}>↑ Artan</option>
                <option value="desc" {{ request('dir') === 'desc' ? 'selected' : '' }}>↓ Azalan</option>
            </select>
        </div>
        <div class="flex items-center gap-3 pt-1">
            <label class="flex items-center text-sm cursor-pointer">
                <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }} class="mr-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-orange-600 text-xs font-medium">Düşük Stok</span>
            </label>
            <label class="flex items-center text-sm cursor-pointer">
                <input type="checkbox" name="out_of_stock" value="1" {{ request('out_of_stock') ? 'checked' : '' }} class="mr-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-red-600 text-xs font-medium">Stok Yok</span>
            </label>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition">
                <i class="fas fa-search mr-1"></i> Filtrele
            </button>
            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 transition">Temizle</a>
            <a href="{{ route('products.export', request()->query()) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition">
                <i class="fas fa-file-csv mr-1"></i> CSV
            </a>
            @can('products.create')
            <a href="{{ route('products.import') }}" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm hover:bg-orange-600 transition">
                <i class="fas fa-file-arrow-up mr-1"></i> Dosyadan Yükle
            </a>
            @endcan
        </div>
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barkod</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün Adı</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Alış</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Satış</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kâr %</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 {{ $product->isLowStock() ? 'bg-red-50' : '' }} {{ $product->stock_quantity <= 0 ? 'bg-orange-50' : '' }}">
                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $product->barcode ?: '-' }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                {{ $product->name }}
                            </a>
                            @if($product->variant_type)
                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-purple-100 text-purple-700">{{ $product->variant_type }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $product->category?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">₺{{ number_format($product->purchase_price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-medium">₺{{ number_format($product->sale_price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right {{ $product->isLowStock() ? 'text-red-600 font-bold' : ($product->stock_quantity <= 0 ? 'text-orange-600 font-bold' : 'text-gray-600') }}">
                            {{ $product->stock_quantity }} {{ $product->unit }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($product->profit_margin > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    %{{ $product->profit_margin }}
                                </span>
                            @elseif($product->profit_margin < 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    %{{ $product->profit_margin }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-box-open text-3xl mb-2"></i>
                            <p>Ürün bulunamadı.</p>
                            <p class="text-xs mt-1">Senkronizasyon yaparak verileri çekin veya filtreleri değiştirin.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3 border-t flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Toplam {{ $products->total() }} ürün, sayfa {{ $products->currentPage() }}/{{ $products->lastPage() }}
        </p>
        {{ $products->links() }}
    </div>
</div>
@endsection
