@extends('layouts.app')
@section('title', 'Alt Ürün Tanımları')

@section('content')
<div class="space-y-6">
    {{-- İstatistik Kartları --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ana Ürünler</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_parents']) }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sitemap text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Alt Ürünler</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_children']) }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-diagram-project text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Toplam İlişki</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_relations']) }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-link text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Kategori Sayısı</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['categories_with_subs']) }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-folder-tree text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtreler --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ürün adı veya barkod..." class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category_id" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-1.5 text-sm text-gray-600">
                    <input type="checkbox" name="only_parents" value="1" {{ request('only_parents') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    Sadece Ana Ürünler
                </label>
                <label class="flex items-center gap-1.5 text-sm text-gray-600">
                    <input type="checkbox" name="only_children" value="1" {{ request('only_children') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600">
                    Sadece Alt Ürünler
                </label>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-search mr-1"></i> Filtrele
            </button>
            @if(request()->hasAny(['search', 'category_id', 'only_parents', 'only_children']))
                <a href="{{ route('products.sub_products') }}" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                    <i class="fas fa-times mr-1"></i> Temizle
                </a>
            @endif
        </form>
    </div>

    {{-- Tablo --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800"><i class="fas fa-sitemap mr-2"></i>Alt Ürün Tanımları</h3>
            <span class="text-sm text-gray-500">{{ $products->total() }} kayıt</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3">Ürün Adı</th>
                        <th class="px-4 py-3">Barkod</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">İlişki</th>
                        <th class="px-4 py-3">Ana Ürün</th>
                        <th class="px-4 py-3">Alt Ürün Sayısı</th>
                        <th class="px-4 py-3 text-right">Satış Fiyatı</th>
                        <th class="px-4 py-3 text-right">Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 {{ $product->parent_id ? 'bg-blue-50/30' : '' }}">
                        <td class="px-4 py-3">
                            <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:underline font-medium">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $product->barcode ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $product->category?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($product->parent_id)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-arrow-right mr-1"></i> Alt Ürün
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-star mr-1"></i> Ana Ürün
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($product->parent)
                                <a href="{{ route('products.show', $product->parent) }}" class="text-indigo-600 hover:underline text-xs">
                                    {{ $product->parent->name }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($product->variants->count() > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $product->variants->count() }} alt ürün
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-medium">₺{{ number_format($product->sale_price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right {{ $product->stock_quantity <= 0 ? 'text-red-600' : 'text-gray-700' }}">
                            {{ number_format($product->stock_quantity, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-sitemap text-3xl mb-2"></i>
                            <p>Alt ürün tanımı bulunamadı.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="px-6 py-3 border-t bg-gray-50">
            {{ $products->links() }}
        </div>
        @endif
        <div class="px-6 py-2 border-t text-xs text-gray-400">
            Sayfa {{ $products->currentPage() }} / {{ $products->lastPage() }} — Toplam {{ $products->total() }} kayıt
        </div>
    </div>
</div>
@endsection
