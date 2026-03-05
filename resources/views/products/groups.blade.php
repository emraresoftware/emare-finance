@extends('layouts.app')
@section('title', 'Ürün Grupları')

@section('content')
{{-- Başarı Mesajı --}}
@if(session('success'))
<div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4 flex items-center gap-2 text-green-700 text-sm">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- İstatistik Kartları --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Kategori</p>
        <p class="text-xl font-bold text-gray-900">{{ number_format($totalStats['total_categories']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Aktif Kategori</p>
        <p class="text-xl font-bold text-green-600">{{ number_format($totalStats['active_categories']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Stok Değeri</p>
        <p class="text-xl font-bold text-indigo-600">₺{{ number_format($totalStats['total_stock_value'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Ort. Ürün/Kategori</p>
        <p class="text-xl font-bold text-blue-600">{{ $totalStats['avg_products_per_category'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Kategori Tablosu --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-layer-group text-indigo-500 mr-2"></i>Ürün Grupları / Kategoriler</h3>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-400">{{ $categories->count() }} grup</span>
                    @permission('products.create')
                    <button onclick="document.getElementById('createCategoryModal').showModal()" class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                        <i class="fas fa-plus mr-1"></i> Yeni Kategori
                    </button>
                    @endpermission
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grup Adı</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ürün Sayısı</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stok Değeri</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ort. Fiyat</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Durum</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($categoryStats as $i => $stat)
                            @php
                                $cat = $categories->firstWhere('id', $stat->id);
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $stat->name }}</div>
                                    @if($cat && $cat->children->count() > 0)
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            <i class="fas fa-sitemap mr-1"></i>{{ $cat->children->count() }} alt kategori
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $stat->product_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                    ₺{{ number_format($stat->stock_value, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600">
                                    ₺{{ number_format($stat->avg_price, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($cat)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cat->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $cat->is_active ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('products.index', ['category_id' => $stat->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm" title="Ürünleri Gör">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @permission('products.create')
                                        <button onclick="openEditCategory({{ $stat->id }}, '{{ addslashes($stat->name) }}', {{ $cat && $cat->parent_id ? $cat->parent_id : 'null' }}, {{ $cat ? $cat->sort_order ?? 0 : 0 }}, {{ $cat && $cat->is_active ? 'true' : 'false' }})" class="text-blue-600 hover:text-blue-800 text-sm" title="Düzenle">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('products.destroy_category', $stat->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ addslashes($stat->name) }} kategorisini silmek istediğinize emin misiniz?\n\nKategorideki {{ $stat->product_count }} ürünün kategori bağlantısı kaldırılacak.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm" title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endpermission
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fas fa-layer-group text-3xl mb-2"></i>
                                    <p>Grup bulunamadı.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sağ Panel: Dağılım Grafiği --}}
    <div class="lg:col-span-1 space-y-6">
        {{-- Kategori Dağılım Pasta Grafiği --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-chart-pie text-purple-500 mr-2"></i>Ürün Dağılımı</h3>
            @if($categoryStats->where('product_count', '>', 0)->count() > 0)
                <canvas id="categoryChart" height="250"></canvas>
            @else
                <div class="text-center text-gray-400 py-8">
                    <i class="fas fa-chart-pie text-3xl mb-2"></i>
                    <p>Veri yok</p>
                </div>
            @endif
        </div>

        {{-- Stok Değeri Dağılımı --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <h3 class="font-semibold text-gray-800 mb-4"><i class="fas fa-coins text-yellow-500 mr-2"></i>Stok Değeri Dağılımı</h3>
            <div class="space-y-3">
                @foreach($categoryStats->where('stock_value', '>', 0)->sortByDesc('stock_value')->take(10) as $stat)
                    @php
                        $percentage = $totalStats['total_stock_value'] > 0 ? ($stat->stock_value / $totalStats['total_stock_value']) * 100 : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-600 truncate max-w-[150px]">{{ $stat->name }}</span>
                            <span class="font-medium">%{{ number_format($percentage, 1) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Yeni Kategori Modal --}}
<dialog id="createCategoryModal" class="rounded-xl shadow-2xl border-0 p-0 w-full max-w-md backdrop:bg-black/50">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800"><i class="fas fa-plus-circle mr-2 text-indigo-500"></i>Yeni Kategori</h3>
            <button onclick="this.closest('dialog').close()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('products.store_category') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border" placeholder="Kategori adı">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Üst Kategori</label>
                <select name="parent_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">— Ana Kategori —</option>
                    @foreach($categories->whereNull('parent_id') as $parentCat)
                        <option value="{{ $parentCat->id }}">{{ $parentCat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sıra</label>
                    <input type="number" name="sort_order" value="0" min="0" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600">
                        <span>Aktif</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="this.closest('dialog').close()" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">İptal</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                    <i class="fas fa-check mr-1"></i> Ekle
                </button>
            </div>
        </form>
    </div>
</dialog>

{{-- Düzenle Modal --}}
<dialog id="editCategoryModal" class="rounded-xl shadow-2xl border-0 p-0 w-full max-w-md backdrop:bg-black/50">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800"><i class="fas fa-edit mr-2 text-blue-500"></i>Kategori Düzenle</h3>
            <button onclick="this.closest('dialog').close()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form id="editCategoryForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="editCatName" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Üst Kategori</label>
                <select name="parent_id" id="editCatParent" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">— Ana Kategori —</option>
                    @foreach($categories->whereNull('parent_id') as $parentCat)
                        <option value="{{ $parentCat->id }}">{{ $parentCat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sıra</label>
                    <input type="number" name="sort_order" id="editCatSort" min="0" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" id="editCatActive" value="1" class="rounded border-gray-300 text-indigo-600">
                        <span>Aktif</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="this.closest('dialog').close()" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">İptal</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                    <i class="fas fa-save mr-1"></i> Güncelle
                </button>
            </div>
        </form>
    </div>
</dialog>

<script>
function openEditCategory(id, name, parentId, sortOrder, isActive) {
    document.getElementById('editCategoryForm').action = '/urunler/gruplar/' + id;
    document.getElementById('editCatName').value = name;
    document.getElementById('editCatParent').value = parentId || '';
    document.getElementById('editCatSort').value = sortOrder;
    document.getElementById('editCatActive').checked = isActive;
    document.getElementById('editCategoryModal').showModal();
}
</script>

@if($categoryStats->where('product_count', '>', 0)->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const topCategories = {!! json_encode(
        $categoryStats->where('product_count', '>', 0)
            ->sortByDesc('product_count')
            ->take(10)
            ->values()
            ->map(fn($s) => ['name' => $s->name, 'count' => $s->product_count])
    ) !!};

    const colors = ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899', '#f43f5e', '#f97316', '#eab308', '#22c55e', '#06b6d4'];

    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: topCategories.map(c => c.name),
            datasets: [{
                data: topCategories.map(c => c.count),
                backgroundColor: colors.slice(0, topCategories.length),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 8, font: { size: 11 } } }
            }
        }
    });
});
</script>
@endif
@endsection
