@extends('layouts.app')
@section('title', 'Hesap Planı')

@section('content')
<div class="space-y-5" x-data="accountPlan()">

    {{-- Başlık --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounting.dashboard') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Hesap Planı</h2>
                <p class="text-sm text-gray-500">Tekdüzen Hesap Planı (THP)</p>
            </div>
        </div>
        <button @click="showAdd = !showAdd"
                class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700">
            <i class="fas fa-plus"></i> Yeni Hesap
        </button>
    </div>

    {{-- Yeni Hesap Formu --}}
    <div x-show="showAdd" x-cloak class="bg-indigo-50 border border-indigo-200 rounded-xl p-5">
        <h3 class="font-semibold text-indigo-900 mb-4">Yeni Hesap Ekle</h3>
        <form action="{{ route('accounting.account-plan.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Hesap Kodu *</label>
                    <input type="text" name="code" required placeholder="örn: 100.01"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Hesap Adı *</label>
                    <input type="text" name="name" required placeholder="Hesap adı"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tür *</label>
                    <select name="type" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="asset">Varlık (Aktif)</option>
                        <option value="liability">Yükümlülük</option>
                        <option value="equity">Özkaynak</option>
                        <option value="revenue">Gelir</option>
                        <option value="cost">Satış Maliyeti</option>
                        <option value="expense">Gider</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Normal Bakiye</label>
                    <select name="normal_balance" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="debit">Borç (Debit)</option>
                        <option value="credit">Alacak (Credit)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Üst Hesap Kodu</label>
                    <input type="text" name="parent_code" placeholder="örn: 100"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="flex items-end pb-0.5">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded">
                        Aktif
                    </label>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-indigo-700">Kaydet</button>
                <button type="button" @click="showAdd = false" class="bg-white text-gray-700 border px-4 py-2 rounded-lg text-sm hover:bg-gray-50">İptal</button>
            </div>
        </form>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-circle-check text-green-500"></i>{{ session('success') }}
    </div>
    @endif

    {{-- Filtreler --}}
    <div class="bg-white rounded-xl border p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tür</label>
            <select id="filterType" x-model="filterType"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="asset">Varlık</option>
                <option value="liability">Yükümlülük</option>
                <option value="equity">Özkaynak</option>
                <option value="revenue">Gelir</option>
                <option value="cost">Maliyet</option>
                <option value="expense">Gider</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Arama</label>
            <input type="text" x-model="search" placeholder="Kod veya ad..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-48">
        </div>
        <div class="ml-auto text-sm text-gray-500" x-text="filtered.length + ' hesap'"></div>
    </div>

    {{-- Tablo --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-28">Kod</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Hesap Adı</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-28">Tür</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-24">Bakiye Yönü</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-20">Seviye</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-16">Durum</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="row in filtered" :key="row.code">
                    <tr class="hover:bg-gray-50"
                        :class="{ 'bg-gray-50 font-semibold': row.level == 1 }">
                        <td class="px-4 py-2 text-gray-700 font-mono text-xs font-semibold" x-text="row.code"></td>
                        <td class="px-4 py-2 text-gray-800" :style="'padding-left: ' + ((row.level - 1) * 14 + 16) + 'px'">
                            <span x-text="row.name"></span>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                                  :class="typeClass(row.type)"
                                  x-text="typeLabel(row.type)"></span>
                        </td>
                        <td class="px-4 py-2 text-center text-xs text-gray-600" x-text="row.normal_balance === 'debit' ? 'Borç' : 'Alacak'"></td>
                        <td class="px-4 py-2 text-center text-xs text-gray-500" x-text="row.level"></td>
                        <td class="px-4 py-2 text-center">
                            <span class="inline-block w-2 h-2 rounded-full"
                                  :class="row.is_active ? 'bg-green-400' : 'bg-gray-300'"></span>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

</div>

<script>
function accountPlan() {
    return {
        showAdd: false,
        search: '',
        filterType: '',
        accounts: @json($accounts),
        get filtered() {
            return this.accounts.filter(a => {
                const matchType = !this.filterType || a.type === this.filterType;
                const q = this.search.toLowerCase();
                const matchSearch = !q || a.code.toLowerCase().includes(q) || a.name.toLowerCase().includes(q);
                return matchType && matchSearch;
            });
        },
        typeLabel(type) {
            return { asset:'Varlık', liability:'Yükümlülük', equity:'Özkaynak',
                     revenue:'Gelir', cost:'Maliyet', expense:'Gider' }[type] || type;
        },
        typeClass(type) {
            return { asset:'bg-blue-50 text-blue-700', liability:'bg-red-50 text-red-700',
                     equity:'bg-green-50 text-green-700', revenue:'bg-emerald-50 text-emerald-700',
                     cost:'bg-orange-50 text-orange-700', expense:'bg-yellow-50 text-yellow-700' }[type] || 'bg-gray-50 text-gray-600';
        }
    }
}
</script>
@endsection
