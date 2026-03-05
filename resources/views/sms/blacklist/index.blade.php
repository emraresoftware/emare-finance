@extends('layouts.app')
@section('title', 'SMS Kara Liste')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">SMS Kara Liste</h1>
        <p class="text-sm text-gray-500 mt-1">SMS gönderilmeyecek telefon numaralarını yönetin</p>
    </div>
    <a href="{{ route('sms.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
        <i class="fas fa-arrow-left mr-2"></i> SMS Paneli
    </a>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
    </div>
@endif

{{-- Numara Ekleme Formu --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-800">Numara Ekle</h3>
        <p class="text-sm text-gray-500 mt-1">Kara listeye yeni telefon numarası ekleyin</p>
    </div>
    <form action="{{ route('sms.blacklist.store') }}" method="POST" class="p-6">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon Numarası <span class="text-red-500">*</span></label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="05XX XXX XX XX">
                @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Sebep</label>
                <input type="text" name="reason" id="reason" value="{{ old('reason') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="Engelleme sebebi (isteğe bağlı)">
                @error('reason') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex items-end">
                <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-ban mr-2"></i> Kara Listeye Ekle
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Kara Liste Tablosu --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon Numarası</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sebep</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eklenme Tarihi</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($blacklist as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-ban text-red-500 text-xs"></i>
                                </div>
                                <span class="text-sm font-mono font-medium text-gray-900">{{ $item->phone }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($item->reason)
                                <p class="text-sm text-gray-600">{{ $item->reason }}</p>
                            @else
                                <span class="text-xs text-gray-400 italic">Sebep belirtilmemiş</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm text-gray-900">{{ $item->created_at->format('d.m.Y H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $item->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <form action="{{ route('sms.blacklist.destroy', $item->id) }}" method="POST"
                                  onsubmit="return confirm('Bu numarayı kara listeden kaldırmak istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 text-gray-600 text-xs rounded-lg hover:bg-gray-50 transition"
                                        title="Kara Listeden Kaldır">
                                    <i class="fas fa-trash mr-1"></i> Kaldır
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-check-circle text-4xl mb-3 text-green-300"></i>
                                <p class="text-sm">Kara listede henüz numara bulunmuyor</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($blacklist->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $blacklist->links() }}
        </div>
    @endif
</div>
@endsection
