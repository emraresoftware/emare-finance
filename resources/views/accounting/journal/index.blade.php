@extends('layouts.app')
@section('title', 'Yevmiye Defteri')

@section('content')
<div class="space-y-5">

    {{-- Başlık --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounting.dashboard') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Yevmiye Defteri</h2>
                <p class="text-sm text-gray-500">Tüm muhasebe fişleri</p>
            </div>
        </div>
        <a href="{{ route('accounting.journal.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700">
            <i class="fas fa-plus"></i> Yeni Fiş
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-circle-check text-green-500"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 flex items-center gap-2">
        <i class="fas fa-triangle-exclamation text-red-500"></i>{{ session('error') }}
    </div>
    @endif

    {{-- Filtreler --}}
    <form method="GET" class="bg-white rounded-xl border p-4 flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tür</label>
            <select name="type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                @foreach(['opening'=>'Açılış','sale'=>'Satış','purchase'=>'Alım','expense'=>'Gider','income'=>'Gelir','payroll'=>'Maaş','adjustment'=>'Düzeltme','closing'=>'Kapanış','manual'=>'Manuel'] as $v => $l)
                <option value="{{ $v }}" {{ request('type') == $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Durum</label>
            <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Kesinleşen</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Taslak</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Başlangıç</label>
            <input type="date" name="start" value="{{ request('start') }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Bitiş</label>
            <input type="date" name="end" value="{{ request('end') }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Ara</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Fiş no veya açıklama..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-52">
        </div>
        <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700">Filtrele</button>
        <a href="{{ route('accounting.journal.index') }}" class="text-sm text-gray-500 hover:text-gray-700 py-2">Temizle</a>
    </form>

    {{-- Tablo --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fiş No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tarih</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Açıklama</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tür</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Borç</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Alacak</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Durum</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-24">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($entries as $entry)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-indigo-700 font-semibold">
                        <a href="{{ route('accounting.journal.show', $entry) }}" class="hover:underline">{{ $entry->entry_no }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $entry->date->format('d.m.Y') }}</td>
                    <td class="px-4 py-3 text-gray-800 max-w-xs truncate">{{ $entry->description }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">{{ $entry->getTypeLabel() }}</span>
                    </td>
                    <td class="px-4 py-3 text-right font-mono text-xs text-gray-700 tabular-nums whitespace-nowrap">
                        {{ number_format($entry->total_debit, 2, ',', '.') }} ₺
                    </td>
                    <td class="px-4 py-3 text-right font-mono text-xs text-gray-700 tabular-nums whitespace-nowrap">
                        {{ number_format($entry->total_credit, 2, ',', '.') }} ₺
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($entry->is_posted)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700 font-medium">
                                <i class="fas fa-lock text-xs"></i> Kesinleşen
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700 font-medium">
                                <i class="fas fa-pencil text-xs"></i> Taslak
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('accounting.journal.show', $entry) }}"
                               class="text-gray-400 hover:text-indigo-600" title="Görüntüle"><i class="fas fa-eye"></i></a>
                            @if(!$entry->is_posted)
                            <form action="{{ route('accounting.journal.post', $entry) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-green-600" title="Kesinleştir"
                                        onclick="return confirm('Bu fişi kesinleştirmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')">
                                    <i class="fas fa-lock-open"></i>
                                </button>
                            </form>
                            <form action="{{ route('accounting.journal.destroy', $entry) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600" title="Sil"
                                        onclick="return confirm('Bu fişi silmek istediğinizden emin misiniz?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                        <i class="fas fa-inbox text-3xl block mb-3"></i>
                        Henüz hiç fiş yok.
                        <div class="mt-2">
                            <a href="{{ route('accounting.journal.create') }}" class="text-indigo-600 hover:underline text-sm">İlk fişi oluştur →</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Sayfalama --}}
    @if($entries->hasPages())
    <div class="flex justify-center">{{ $entries->withQueryString()->links() }}</div>
    @endif

</div>
@endsection
