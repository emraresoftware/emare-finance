@extends('layouts.app')
@section('title', 'Hizmet Kategorileri')

@section('content')
<div class="space-y-6">

    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Hizmet Kategorileri</h2>
            <p class="text-sm text-gray-500">Hizmet tipi ürün ve fatura kategorilerini yönetin.</p>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-plus mr-1"></i> Yeni Kategori
        </button>
    </div>

    {{-- İstatistikler --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Toplam Kategori</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Aktif</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Ürün İçeren</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['with_products'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-box text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Tekrarlayan Fatura</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['with_recurring'] }}</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sync text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtreleme --}}
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Kategori adı..."
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div class="w-32">
                <label class="block text-xs text-gray-500 mb-1">Durum</label>
                <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="passive" {{ request('status') == 'passive' ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                <i class="fas fa-search mr-1"></i> Filtrele
            </button>
        </form>
    </div>

    {{-- Kategori Listesi --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Renk</th>
                        <th class="px-6 py-3 text-left">Kategori Adı</th>
                        <th class="px-6 py-3 text-left">Üst Kategori</th>
                        <th class="px-6 py-3 text-left">Açıklama</th>
                        <th class="px-6 py-3 text-center">Ürünler</th>
                        <th class="px-6 py-3 text-center">T. Faturalar</th>
                        <th class="px-6 py-3 text-center">Durum</th>
                        <th class="px-6 py-3 text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <div class="w-6 h-6 rounded-full border-2 border-white shadow" style="background-color: {{ $cat->color ?? '#6366f1' }}"></div>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                @if($cat->icon)
                                    <i class="fas fa-{{ $cat->icon }} text-gray-400"></i>
                                @endif
                                <span class="font-medium text-gray-800">{{ $cat->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $cat->parent?->name ?? '-' }}</td>
                        <td class="px-6 py-3 text-gray-500 text-xs">{{ Str::limit($cat->description, 50) }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $cat->products_count }}</span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="bg-orange-100 text-orange-700 text-xs px-2 py-0.5 rounded-full">{{ $cat->recurring_invoices_count }}</span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $cat->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $cat->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditCategoryModal({{ json_encode($cat) }})"
                                    class="text-blue-600 hover:text-blue-800" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('service_categories.destroy', $cat) }}" method="POST"
                                    onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-layer-group text-3xl mb-3 block"></i>
                            Henüz hizmet kategorisi eklenmemiş.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Ekleme Modal --}}
<div id="addModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('addModal').classList.add('hidden')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-plus-circle mr-2 text-indigo-500"></i>Yeni Hizmet Kategorisi</h3>
                <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('service_categories.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_submit" value="1">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı *</label>
                    <input type="text" name="name" required placeholder="Danışmanlık Hizmeti"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <textarea name="description" rows="2" placeholder="Kategori açıklaması..."
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Üst Kategori</label>
                        <select name="parent_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                            <option value="">Ana Kategori</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sıralama</label>
                        <input type="number" name="sort_order" value="0"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Renk</label>
                        <input type="color" name="color" value="#6366f1"
                            class="w-full h-10 rounded-lg border cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">İkon (FA)</label>
                        <input type="text" name="icon" placeholder="briefcase" value=""
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600">
                        <span class="text-sm text-gray-700">Aktif</span>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')"
                        class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">İptal</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                        <i class="fas fa-save mr-1"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Düzenleme Modal --}}
<div id="editCatModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('editCatModal').classList.add('hidden')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-edit mr-2 text-blue-500"></i>Kategori Düzenle</h3>
                <button onclick="document.getElementById('editCatModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editCatForm" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Adı *</label>
                    <input type="text" name="name" id="ec_name" required
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <textarea name="description" id="ec_description" rows="2"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Üst Kategori</label>
                        <select name="parent_id" id="ec_parent_id" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                            <option value="">Ana Kategori</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sıralama</label>
                        <input type="number" name="sort_order" id="ec_sort_order"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Renk</label>
                        <input type="color" name="color" id="ec_color"
                            class="w-full h-10 rounded-lg border cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">İkon</label>
                        <input type="text" name="icon" id="ec_icon"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="ec_is_active" value="1" class="rounded border-gray-300 text-indigo-600">
                        <span class="text-sm text-gray-700">Aktif</span>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="document.getElementById('editCatModal').classList.add('hidden')"
                        class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">İptal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                        <i class="fas fa-save mr-1"></i> Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditCategoryModal(cat) {
    document.getElementById('editCatForm').action = '/hizmet-kategorileri/' + cat.id;
    document.getElementById('ec_name').value = cat.name;
    document.getElementById('ec_description').value = cat.description || '';
    document.getElementById('ec_parent_id').value = cat.parent_id || '';
    document.getElementById('ec_sort_order').value = cat.sort_order || 0;
    document.getElementById('ec_color').value = cat.color || '#6366f1';
    document.getElementById('ec_icon').value = cat.icon || '';
    document.getElementById('ec_is_active').checked = cat.is_active;
    document.getElementById('editCatModal').classList.remove('hidden');
}
</script>
@endpush
