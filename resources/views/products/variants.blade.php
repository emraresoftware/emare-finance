@extends('layouts.app')
@section('title', 'Ürün Varyantları')

@section('content')
{{-- İstatistik Kartları --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Varyantlı Ürün</p>
        <p class="text-xl font-bold text-purple-600">{{ number_format($stats['total_variants']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Ana Ürün (Varyant Sahibi)</p>
        <p class="text-xl font-bold text-indigo-600">{{ number_format($stats['parent_products']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Varyant Tipi Çeşidi</p>
        <p class="text-xl font-bold text-blue-600">{{ number_format($stats['variant_types']) }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm border p-4 mb-6">
    <form method="GET" class="flex items-end gap-4 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Arama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ürün adı, barkod, varyant tipi..."
                   class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border focus:ring-2 focus:ring-indigo-500">
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
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">
            <i class="fas fa-search mr-1"></i> Ara
        </button>
        <a href="{{ route('products.variants') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 transition">Temizle</a>
    </form>
</div>

{{-- Varyant Tablosu --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <h3 class="font-semibold text-gray-800"><i class="fas fa-clone text-purple-500 mr-2"></i>Varyantlı Ürünler</h3>
        <span class="text-xs text-gray-400">{{ $variants->total() }} ürün</span>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ürün Adı</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barkod</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Varyant Tipi</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ana Ürün</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Fiyat</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Alt Varyant</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($variants as $i => $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $variants->firstItem() + $i }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('products.show', $product) }}" class="text-sm font-medium text-indigo-600 hover:underline">{{ $product->name }}</a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 font-mono text-xs">{{ $product->barcode ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $product->category?->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($product->variant_type)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $product->variant_type }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($product->parent)
                                <a href="{{ route('products.show', $product->parent) }}" class="text-indigo-600 hover:underline text-xs">{{ $product->parent->name }}</a>
                            @else
                                <span class="text-gray-400 text-xs">Ana ürün</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-medium">₺{{ number_format($product->sale_price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-right {{ $product->stock_quantity <= 0 ? 'text-red-600' : 'text-gray-600' }}">
                            {{ $product->stock_quantity ?? 0 }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            @if($product->variants->count() > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $product->variants->count() }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-400">
                            <i class="fas fa-clone text-4xl mb-3"></i>
                            <p class="text-lg">Varyantlı ürün bulunamadı.</p>
                            <p class="text-sm mt-1">Ürünlerde varyant tipi tanımlandığında burada listelenecektir.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($variants->hasPages())
    <div class="px-4 py-3 border-t">
        {{ $variants->links() }}
    </div>
    @endif
</div>
@endsection
