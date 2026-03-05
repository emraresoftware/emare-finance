@extends('layouts.app')
@section('title', 'Yeni Mesaj')

@section('content')
<div class="space-y-6" x-data="messageForm()">
    {{-- Başlık --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('marketing.messages.index') }}" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-lg font-bold text-gray-800">Yeni Mesaj Oluştur</h1>
                <p class="text-sm text-gray-500">Müşterilerinize SMS, e-posta veya push bildirim gönderin</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('marketing.messages.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Sol: Mesaj İçeriği --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Temel Bilgiler --}}
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-envelope text-indigo-500 mr-2"></i>Mesaj Bilgileri</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mesaj Başlığı <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required placeholder="Ör: Yılbaşı İndirim Kampanyası"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                            @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kanal <span class="text-red-500">*</span></label>
                            <select name="channel" x-model="channel" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('channel') border-red-500 @enderror">
                                <option value="">Seçin...</option>
                                <option value="sms" {{ old('channel') == 'sms' ? 'selected' : '' }}>SMS</option>
                                <option value="email" {{ old('channel') == 'email' ? 'selected' : '' }}>E-posta</option>
                                <option value="push" {{ old('channel') == 'push' ? 'selected' : '' }}>Push Bildirim</option>
                            </select>
                            @error('channel') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Zamanlama</label>
                            <select name="schedule_type" x-model="scheduleType" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="now">Hemen Gönder</option>
                                <option value="scheduled">İleri Tarihte</option>
                            </select>
                        </div>
                        <div x-show="scheduleType === 'scheduled'" x-cloak class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gönderim Zamanı</label>
                            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- Mesaj İçeriği --}}
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-pen text-indigo-500 mr-2"></i>Mesaj İçeriği</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">İçerik <span class="text-red-500">*</span></label>
                        <textarea name="content" rows="8" required placeholder="Mesaj içeriğinizi yazın..."
                                  x-model="content"
                                  class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
                        @error('content') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        <div class="mt-2 flex items-center justify-between text-xs text-gray-400">
                            <div>
                                <span x-text="content.length"></span> karakter
                                <span x-show="channel === 'sms'"> · <span x-text="Math.ceil(content.length / 160)"></span> SMS</span>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="content += ' {müşteri_adı}'" class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200">+Müşteri Adı</button>
                                <button type="button" @click="content += ' {firma_adı}'" class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200">+Firma Adı</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sağ: Hedef & Ayarlar --}}
            <div class="space-y-6">
                {{-- Hedef Kitle --}}
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-users text-indigo-500 mr-2"></i>Hedef Kitle</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Segment <span class="text-red-500">*</span></label>
                        <select name="segment_id" required class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('segment_id') border-red-500 @enderror">
                            <option value="">Segment seçin...</option>
                            @foreach($segments as $segment)
                                <option value="{{ $segment->id }}" {{ old('segment_id', request('segment_id')) == $segment->id ? 'selected' : '' }}>
                                    {{ $segment->name }} ({{ $segment->customer_count }} müşteri)
                                </option>
                            @endforeach
                        </select>
                        @error('segment_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Kampanya İlişkilendirme --}}
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-bullhorn text-indigo-500 mr-2"></i>Kampanya (İsteğe Bağlı)</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kampanya</label>
                        <select name="campaign_id" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">Kampanya seçin...</option>
                            @foreach($campaigns as $campaign)
                                <option value="{{ $campaign->id }}" {{ old('campaign_id', request('campaign_id')) == $campaign->id ? 'selected' : '' }}>
                                    {{ $campaign->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Mesajı bir kampanyayla ilişkilendirin</p>
                    </div>
                </div>

                {{-- Önizleme --}}
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-eye text-indigo-500 mr-2"></i>Önizleme</h3>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 min-h-[80px]">
                        <template x-if="channel === 'sms'">
                            <div class="bg-green-50 rounded-2xl p-4 border border-green-200 max-w-xs">
                                <p class="text-sm" x-text="content || 'Mesaj içeriği burada görünecek...'"></p>
                            </div>
                        </template>
                        <template x-if="channel === 'email'">
                            <div class="bg-white rounded-lg border p-4">
                                <div class="border-b pb-2 mb-2">
                                    <p class="text-xs text-gray-400">Konu:</p>
                                    <p class="font-medium" x-text="$refs.titleInput?.value || 'Mesaj başlığı'"></p>
                                </div>
                                <p class="text-sm whitespace-pre-line" x-text="content || 'Mesaj içeriği burada görünecek...'"></p>
                            </div>
                        </template>
                        <template x-if="channel === 'push'">
                            <div class="bg-gray-800 rounded-xl p-3 text-white max-w-xs">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-5 h-5 bg-indigo-600 rounded flex items-center justify-center">
                                        <span class="text-[10px] font-bold">EF</span>
                                    </div>
                                    <span class="text-xs text-gray-400">Emare Finance</span>
                                </div>
                                <p class="text-sm" x-text="content || 'Bildirim içeriği burada görünecek...'"></p>
                            </div>
                        </template>
                        <template x-if="!channel">
                            <p class="text-gray-400 text-center">Kanal seçerek önizlemeyi görün</p>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kaydet --}}
        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="{{ route('marketing.messages.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 font-medium">İptal</a>
            <button type="submit" name="action" value="draft" class="px-6 py-2.5 bg-gray-700 text-white rounded-lg text-sm hover:bg-gray-800 font-medium">
                <i class="fas fa-save mr-1"></i> Taslak Kaydet
            </button>
            <button type="submit" name="action" value="send" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 font-medium">
                <i class="fas fa-paper-plane mr-1"></i> Kaydet & Gönder
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function messageForm() {
    return {
        channel: '{{ old('channel', '') }}',
        scheduleType: '{{ old('schedule_type', 'now') }}',
        content: '{{ old('content', '') }}'
    }
}
</script>
@endpush
@endsection
