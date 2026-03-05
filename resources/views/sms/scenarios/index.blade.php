@extends('layouts.app')
@section('title', 'SMS Senaryoları')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">SMS Senaryoları</h1>
        <p class="text-sm text-gray-500 mt-1">Otomatik SMS gönderim senaryolarını yönetin</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('sms.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left mr-2"></i> SMS Paneli
        </a>
        <a href="{{ route('sms.scenarios.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-2"></i> Yeni Senaryo
        </a>
    </div>
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

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Senaryo Adı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tetikleyici</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hedef</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şablon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zamanlama</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Gönderim</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($scenarios as $scenario)
                    <tr class="hover:bg-gray-50 transition {{ !$scenario->is_active ? 'opacity-60' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $scenario->is_active ? 'bg-green-100' : 'bg-gray-100' }}">
                                    <i class="fas fa-robot text-sm {{ $scenario->is_active ? 'text-green-600' : 'text-gray-400' }}"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $scenario->name }}</p>
                                    @if($scenario->priority)
                                        <p class="text-xs text-gray-400">Öncelik: {{ $scenario->priority }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $scenario->trigger_event }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">
                                @switch($scenario->target_type)
                                    @case('all') Tüm Müşteriler @break
                                    @case('segment')
                                        <i class="fas fa-users text-xs text-purple-500 mr-1"></i>
                                        {{ $scenario->segment?->name ?? 'Segment' }}
                                    @break
                                    @case('customer_type') {{ $scenario->customer_type ?? 'Müşteri Tipi' }} @break
                                    @case('manual') Manuel @break
                                    @default {{ $scenario->target_type }}
                                @endswitch
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">
                                <i class="fas fa-file-alt text-xs text-gray-400 mr-1"></i>
                                {{ $scenario->template?->name ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($scenario->schedule_type)
                                @case('immediate')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Anında</span>
                                @break
                                @case('delayed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">{{ $scenario->delay_minutes }} dk gecikme</span>
                                @break
                                @case('scheduled')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $scenario->send_time }}</span>
                                @break
                                @case('recurring')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Tekrarlayan</span>
                                @break
                                @default
                                    <span class="text-xs text-gray-400">{{ $scenario->schedule_type }}</span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ number_format($scenario->logs_count ?? 0) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($scenario->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-circle text-[6px] mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-circle text-[6px] mr-1"></i> Pasif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('sms.scenarios.edit', $scenario->id) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-300 text-gray-600 text-xs rounded-lg hover:bg-gray-50 transition"
                                   title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sms.scenarios.destroy', $scenario->id) }}" method="POST"
                                      onsubmit="return confirm('Bu senaryoyu silmek istediğinize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-2.5 py-1.5 bg-white border border-red-300 text-red-600 text-xs rounded-lg hover:bg-red-50 transition"
                                            title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-robot text-4xl mb-3"></i>
                                <p class="text-sm">Henüz SMS senaryosu oluşturulmamış</p>
                                <a href="{{ route('sms.scenarios.create') }}" class="mt-3 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-plus mr-1"></i> İlk senaryoyu oluştur
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($scenarios->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $scenarios->links() }}
        </div>
    @endif
</div>
@endsection
