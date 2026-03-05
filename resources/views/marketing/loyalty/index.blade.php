@extends('layouts.app')
@section('title', 'Sadakat Programı')

@section('content')
<div class="space-y-6">
    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('marketing.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Sadakat Programı</h1>
                <p class="text-sm text-gray-500 mt-1">Müşteri puan sistemi ve ödül yönetimi</p>
            </div>
        </div>
    </div>

    {{-- Program Ayarları --}}
    <div class="bg-white rounded-xl shadow-sm border p-6" x-data="{ editing: {{ $program ? 'false' : 'true' }} }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-md font-semibold text-gray-800">
                <i class="fas fa-star text-yellow-500 mr-2"></i>Program Ayarları
            </h3>
            @if($program)
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $program->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        <i class="fas fa-{{ $program->is_active ? 'check-circle' : 'pause-circle' }} mr-1"></i>
                        {{ $program->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                    <button @click="editing = !editing" class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">
                        <i class="fas fa-edit mr-1"></i> Düzenle
                    </button>
                </div>
            @endif
        </div>

        @if($program && !$program->is_active)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 text-sm text-yellow-700">
                <i class="fas fa-exclamation-triangle mr-1"></i> Sadakat programı şu anda pasif durumda. Müşteriler puan kazanamaz ve harcayamaz.
            </div>
        @endif

        {{-- Program Bilgileri (Görüntüleme) --}}
        <div x-show="!editing" x-cloak>
            @if($program)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-4 border border-yellow-200">
                        <p class="text-xs text-yellow-600 font-medium mb-1">Program Adı</p>
                        <p class="text-lg font-bold text-gray-800">{{ $program->name }}</p>
                        @if($program->description)
                            <p class="text-xs text-gray-500 mt-1">{{ $program->description }}</p>
                        @endif
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200">
                        <p class="text-xs text-blue-600 font-medium mb-1">Puan Kazanma</p>
                        <p class="text-lg font-bold text-gray-800">{{ $program->points_per_currency }} puan</p>
                        <p class="text-xs text-gray-500">Her ₺1 harcamada</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200">
                        <p class="text-xs text-green-600 font-medium mb-1">Puan Harcama</p>
                        <p class="text-lg font-bold text-gray-800">₺{{ number_format($program->currency_per_point, 2, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">Her 1 puan değeri</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-200">
                        <p class="text-xs text-purple-600 font-medium mb-1">Min. Harcama</p>
                        <p class="text-lg font-bold text-gray-800">{{ number_format($program->min_redeem_points) }} puan</p>
                        <p class="text-xs text-gray-500">Minimum harcama puanı</p>
                    </div>
                </div>
            @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-star text-4xl mb-3"></i>
                    <p class="text-lg">Henüz sadakat programı oluşturulmamış</p>
                    <p class="text-sm mt-1">Aşağıdaki formu doldurun</p>
                </div>
            @endif
        </div>

        {{-- Program Formu (Düzenleme) --}}
        <form method="POST" action="{{ route('marketing.loyalty.store') }}" x-show="editing" x-cloak x-transition>
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program Adı <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $program->name ?? '') }}" required placeholder="Ör: Emare Puan Sistemi"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                    <textarea name="description" rows="2" placeholder="Program hakkında kısa açıklama..."
                              class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('description', $program->description ?? '') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">₺1 = Kaç Puan <span class="text-red-500">*</span></label>
                    <input type="number" name="points_per_currency" value="{{ old('points_per_currency', $program->points_per_currency ?? 1) }}" required min="0.01" step="0.01"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('points_per_currency') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Her ₺1 harcamada kazanılacak puan</p>
                    @error('points_per_currency') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">1 Puan = ₺? <span class="text-red-500">*</span></label>
                    <input type="number" name="currency_per_point" value="{{ old('currency_per_point', $program->currency_per_point ?? 0.01) }}" required min="0.001" step="0.001"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('currency_per_point') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Her puanın TL karşılığı</p>
                    @error('currency_per_point') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Harcama Puanı <span class="text-red-500">*</span></label>
                    <input type="number" name="min_redeem_points" value="{{ old('min_redeem_points', $program->min_redeem_points ?? 100) }}" required min="1" step="1"
                           class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('min_redeem_points') border-red-500 @enderror">
                    <p class="text-xs text-gray-400 mt-1">Müşterinin puan harcamak için gereken minimum puan</p>
                    @error('min_redeem_points') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                    <select name="is_active" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="1" {{ old('is_active', $program->is_active ?? 1) ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $program->is_active ?? 1) ? '' : 'selected' }}>Pasif</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 bg-yellow-600 text-white rounded-lg text-sm hover:bg-yellow-700 font-medium">
                    <i class="fas fa-save mr-1"></i> {{ $program ? 'Güncelle' : 'Oluştur' }}
                </button>
                @if($program)
                    <button type="button" @click="editing = false" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 font-medium">İptal</button>
                @endif
            </div>
        </form>
    </div>

    @if($program)
        {{-- En Çok Puan Toplayan Müşteriler --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="p-6 pb-0">
                    <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-trophy text-yellow-500 mr-2"></i>En Çok Puan Toplayan Müşteriler</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam Puan</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">TL Değeri</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($topCustomers as $i => $topCustomer)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        @if($i < 3)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                                {{ $i == 0 ? 'bg-yellow-100 text-yellow-700' : ($i == 1 ? 'bg-gray-200 text-gray-600' : 'bg-orange-100 text-orange-700') }}">
                                                {{ $i + 1 }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">{{ $i + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $topCustomer->customer->name ?? 'Bilinmeyen' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="font-semibold text-yellow-600">{{ number_format($topCustomer->total_points) }}</span>
                                        <span class="text-xs text-gray-400 ml-1">puan</span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-green-600 font-medium">
                                        ₺{{ number_format($topCustomer->total_points * ($program->currency_per_point ?? 0), 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fas fa-trophy text-3xl mb-2"></i><p>Henüz puan kazanan müşteri yok</p>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Son Puan Hareketleri --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="p-6 pb-0">
                    <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-exchange-alt text-blue-500 mr-2"></i>Son Puan Hareketleri</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tür</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Puan</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Bakiye</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tarih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($recentActivity as $activity)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-gray-800">{{ $activity->customer->name ?? 'Bilinmeyen' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $activity->type === 'earn' ? 'bg-green-100 text-green-700' : ($activity->type === 'redeem' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                                            {{ $activity->type_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold {{ $activity->points > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $activity->points > 0 ? '+' : '' }}{{ number_format($activity->points) }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-600">{{ number_format($activity->balance_after) }}</td>
                                    <td class="px-4 py-3 text-right text-gray-500 text-xs">{{ $activity->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fas fa-exchange-alt text-3xl mb-2"></i><p>Henüz puan hareketi yok</p>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
