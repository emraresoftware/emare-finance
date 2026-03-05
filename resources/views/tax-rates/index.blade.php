@extends('layouts.app')
@section('title', 'Vergi Oranları Yönetimi')

@section('content')
<div class="space-y-6">

    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Vergi Oranları Yönetimi</h2>
            <p class="text-sm text-gray-500">KDV, ÖTV, ÖİV ve diğer vergi oranlarını yönetin.</p>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-plus mr-1"></i> Yeni Vergi Oranı
        </button>
    </div>

    {{-- İstatistikler --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Toplam Vergi Türü</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-percent text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Aktif Oranlar</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Vergi Kodları</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['tax_codes'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tags text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500">Varsayılan KDV</p>
                    <p class="text-2xl font-bold text-indigo-600">
                        %{{ $stats['default_kdv']?->rate ?? '20' }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-star text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtreleme --}}
    <div class="bg-white rounded-xl shadow-sm border p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Vergi adı veya açıklama..."
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">Vergi Kodu</label>
                <select name="code" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    @foreach($taxCodes as $code)
                        <option value="{{ $code }}" {{ request('code') == $code ? 'selected' : '' }}>{{ $code }}</option>
                    @endforeach
                </select>
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
            <a href="{{ route('tax_rates.index') }}" class="bg-gray-50 text-gray-500 px-4 py-2 rounded-lg text-sm hover:bg-gray-100">
                <i class="fas fa-times mr-1"></i> Temizle
            </a>
        </form>
    </div>

    {{-- Gruplandırılmış Vergi Oranları --}}
    @foreach($grouped as $code => $rates)
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @php
                    $codeColors = [
                        'KDV' => 'bg-blue-500',
                        'OTV' => 'bg-orange-500',
                        'OIV' => 'bg-purple-500',
                        'DAMGA' => 'bg-yellow-500',
                        'BSMV' => 'bg-red-500',
                        'KONAKLAMA' => 'bg-teal-500',
                        'CEVRE' => 'bg-green-500',
                    ];
                    $bgColor = $codeColors[$code] ?? 'bg-gray-500';
                    $codeLabels = [
                        'KDV' => 'Katma Değer Vergisi',
                        'OTV' => 'Özel Tüketim Vergisi',
                        'OIV' => 'Özel İletişim Vergisi',
                        'DAMGA' => 'Damga Vergisi',
                        'BSMV' => 'Banka ve Sigorta Muameleleri Vergisi',
                        'KONAKLAMA' => 'Konaklama Vergisi',
                        'CEVRE' => 'Çevre Temizlik Vergisi',
                    ];
                @endphp
                <span class="w-3 h-3 rounded-full {{ $bgColor }}"></span>
                <h3 class="font-semibold text-gray-800">{{ $code }}</h3>
                <span class="text-sm text-gray-500">{{ $codeLabels[$code] ?? $code }}</span>
                <span class="bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $rates->count() }} oran</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3 text-left">Ad</th>
                        <th class="px-6 py-3 text-center">Oran</th>
                        <th class="px-6 py-3 text-center">Tip</th>
                        <th class="px-6 py-3 text-left">Açıklama</th>
                        <th class="px-6 py-3 text-center">Varsayılan</th>
                        <th class="px-6 py-3 text-center">Durum</th>
                        <th class="px-6 py-3 text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($rates as $rate)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-800">{{ $rate->name }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($rate->type === 'percentage')
                                <span class="font-semibold text-indigo-600">%{{ $rate->rate }}</span>
                            @else
                                <span class="font-semibold text-green-600">₺{{ number_format($rate->rate, 2, ',', '.') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $rate->type === 'percentage' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                {{ $rate->type === 'percentage' ? 'Yüzde' : 'Sabit' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $rate->description }}</td>
                        <td class="px-6 py-3 text-center">
                            @if($rate->is_default)
                                <i class="fas fa-star text-yellow-500"></i>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $rate->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $rate->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditModal({{ json_encode($rate) }})"
                                    class="text-blue-600 hover:text-blue-800" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('tax_rates.destroy', $rate) }}" method="POST"
                                    onsubmit="return confirm('Bu vergi oranını silmek istediğinize emin misiniz?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    @if($grouped->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
        <i class="fas fa-percent text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-600">Henüz vergi oranı tanımlanmamış</h3>
        <p class="text-sm text-gray-400 mt-1">Yeni vergi oranı ekleyerek başlayın.</p>
    </div>
    @endif
</div>

{{-- Ekleme Modal --}}
<div id="addModal" class="fixed inset-0 z-50 hidden" x-data="{ show: false }">
    <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('addModal').classList.add('hidden')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-plus-circle mr-2 text-indigo-500"></i>Yeni Vergi Oranı</h3>
                <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('tax_rates.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_submit" value="1">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vergi Adı *</label>
                        <input type="text" name="name" required placeholder="KDV %20"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vergi Kodu *</label>
                        <select name="code" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                            <option value="KDV">KDV - Katma Değer Vergisi</option>
                            <option value="OTV">ÖTV - Özel Tüketim Vergisi</option>
                            <option value="OIV">ÖİV - Özel İletişim Vergisi</option>
                            <option value="DAMGA">Damga Vergisi</option>
                            <option value="BSMV">BSMV</option>
                            <option value="KONAKLAMA">Konaklama Vergisi</option>
                            <option value="CEVRE">Çevre Temizlik Vergisi</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Oran *</label>
                        <input type="number" step="0.0001" name="rate" required placeholder="20"
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tip *</label>
                        <select name="type" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                            <option value="percentage">Yüzde (%)</option>
                            <option value="fixed">Sabit Tutar (₺)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <input type="text" name="description" placeholder="Bu vergi oranı hakkında kısa açıklama"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sıralama</label>
                    <input type="number" name="sort_order" value="0"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600">
                        <span class="text-sm text-gray-700">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-yellow-600">
                        <span class="text-sm text-gray-700">Varsayılan</span>
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
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('editModal').classList.add('hidden')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="font-semibold text-gray-800"><i class="fas fa-edit mr-2 text-blue-500"></i>Vergi Oranı Düzenle</h3>
                <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vergi Adı *</label>
                        <input type="text" name="name" id="edit_name" required
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vergi Kodu *</label>
                        <select name="code" id="edit_code" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                            <option value="KDV">KDV</option>
                            <option value="OTV">ÖTV</option>
                            <option value="OIV">ÖİV</option>
                            <option value="DAMGA">Damga</option>
                            <option value="BSMV">BSMV</option>
                            <option value="KONAKLAMA">Konaklama</option>
                            <option value="CEVRE">Çevre</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Oran *</label>
                        <input type="number" step="0.0001" name="rate" id="edit_rate" required
                            class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tip *</label>
                        <select name="type" id="edit_type" required class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                            <option value="percentage">Yüzde (%)</option>
                            <option value="fixed">Sabit Tutar (₺)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <input type="text" name="description" id="edit_description"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sıralama</label>
                    <input type="number" name="sort_order" id="edit_sort_order"
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded border-gray-300 text-indigo-600">
                        <span class="text-sm text-gray-700">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_default" id="edit_is_default" value="1" class="rounded border-gray-300 text-yellow-600">
                        <span class="text-sm text-gray-700">Varsayılan</span>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
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
function openEditModal(rate) {
    document.getElementById('editForm').action = '/vergi-oranlari/' + rate.id;
    document.getElementById('edit_name').value = rate.name;
    document.getElementById('edit_code').value = rate.code;
    document.getElementById('edit_rate').value = rate.rate;
    document.getElementById('edit_type').value = rate.type;
    document.getElementById('edit_description').value = rate.description || '';
    document.getElementById('edit_sort_order').value = rate.sort_order || 0;
    document.getElementById('edit_is_active').checked = rate.is_active;
    document.getElementById('edit_is_default').checked = rate.is_default;
    document.getElementById('editModal').classList.remove('hidden');
}
</script>
@endpush
