@extends('layouts.app')
@section('title', 'Hızlı SMS Gönder')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Hızlı SMS Gönder</h1>
        <p class="text-sm text-gray-500 mt-1">Tek veya toplu SMS gönderimi yapın</p>
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

<form action="{{ route('sms.send') }}" method="POST"
      x-data="composeForm()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    @csrf

    {{-- Sol: Form --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Gönderim Tipi --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Kime Gönderilecek?</h3>
                <p class="text-sm text-gray-500 mt-1">SMS alıcılarını belirleyin</p>
            </div>
            <div class="p-6 space-y-5">
                {{-- Tip Seçimi --}}
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="send_type" value="single" x-model="send_type" class="sr-only peer">
                        <div class="border-2 border-gray-200 rounded-lg p-3 text-center transition
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300">
                            <i class="fas fa-user text-lg mb-1 text-gray-400 peer-checked:text-indigo-600"></i>
                            <p class="text-xs font-medium text-gray-700">Tek Numara</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="send_type" value="segment" x-model="send_type" class="sr-only peer">
                        <div class="border-2 border-gray-200 rounded-lg p-3 text-center transition
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300">
                            <i class="fas fa-users text-lg mb-1 text-gray-400"></i>
                            <p class="text-xs font-medium text-gray-700">Segment</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="send_type" value="customer_type" x-model="send_type" class="sr-only peer">
                        <div class="border-2 border-gray-200 rounded-lg p-3 text-center transition
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300">
                            <i class="fas fa-user-tag text-lg mb-1 text-gray-400"></i>
                            <p class="text-xs font-medium text-gray-700">Müşteri Tipi</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="send_type" value="all" x-model="send_type" class="sr-only peer">
                        <div class="border-2 border-gray-200 rounded-lg p-3 text-center transition
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300">
                            <i class="fas fa-globe text-lg mb-1 text-gray-400"></i>
                            <p class="text-xs font-medium text-gray-700">Tüm Müşteriler</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="send_type" value="manual" x-model="send_type" class="sr-only peer">
                        <div class="border-2 border-gray-200 rounded-lg p-3 text-center transition
                                    peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300">
                            <i class="fas fa-list-ol text-lg mb-1 text-gray-400"></i>
                            <p class="text-xs font-medium text-gray-700">Manuel Liste</p>
                        </div>
                    </label>
                </div>
                @error('send_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                {{-- Tek Numara --}}
                <div x-show="send_type === 'single'" x-cloak>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon Numarası <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="05XX XXX XX XX">
                    @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Segment --}}
                <div x-show="send_type === 'segment'" x-cloak>
                    <label for="segment_id" class="block text-sm font-medium text-gray-700 mb-1">Müşteri Segmenti <span class="text-red-500">*</span></label>
                    <select name="segment_id" id="segment_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Segment Seçin</option>
                        @foreach($segments as $segment)
                            <option value="{{ $segment->id }}" {{ old('segment_id') == $segment->id ? 'selected' : '' }}>
                                {{ $segment->name }} ({{ $segment->members_count ?? 0 }} üye)
                            </option>
                        @endforeach
                    </select>
                    @error('segment_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Müşteri Tipi --}}
                <div x-show="send_type === 'customer_type'" x-cloak>
                    <label for="customer_type" class="block text-sm font-medium text-gray-700 mb-1">Müşteri Tipi <span class="text-red-500">*</span></label>
                    <select name="customer_type" id="customer_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Müşteri Tipi Seçin</option>
                        @foreach($customerTypes as $ctKey => $ctLabel)
                            <option value="{{ $ctKey }}" {{ old('customer_type') == $ctKey ? 'selected' : '' }}>{{ $ctLabel }}</option>
                        @endforeach
                    </select>
                    @error('customer_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tüm Müşteriler Uyarısı --}}
                <div x-show="send_type === 'all'" x-cloak>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-red-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-medium text-red-800">Dikkat!</p>
                            <p class="text-sm text-red-700 mt-1">Bu işlem, sistemdeki tüm müşterilere SMS gönderecektir. Bu geri alınamaz bir işlemdir. Lütfen mesaj içeriğinizi dikkatlice kontrol edin.</p>
                        </div>
                    </div>
                </div>

                {{-- Manuel Liste --}}
                <div x-show="send_type === 'manual'" x-cloak>
                    <label for="phone_list" class="block text-sm font-medium text-gray-700 mb-1">Telefon Numaraları <span class="text-red-500">*</span></label>
                    <textarea name="phone_list" id="phone_list" rows="5" x-model="phoneList"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Her satıra bir numara yazın veya virgülle ayırın&#10;05XX XXX XX XX&#10;05XX XXX XX XX">{{ old('phone_list') }}</textarea>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-xs text-gray-400">Her satıra bir numara veya virgülle ayırarak yazın</p>
                        <p class="text-xs font-medium text-gray-500"><span x-text="phoneCount"></span> numara</p>
                    </div>
                    @error('phone_list') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Mesaj İçeriği --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Mesaj İçeriği</h3>
            </div>
            <div class="p-6 space-y-5">
                {{-- Şablon Seçimi --}}
                <div>
                    <label for="template_id" class="block text-sm font-medium text-gray-700 mb-1">Şablon Kullan (İsteğe Bağlı)</label>
                    <select name="template_id" id="template_id" x-model="selectedTemplate" @change="applyTemplate()"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Şablon seçmeden devam et</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" data-content="{{ $template->content }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Serbest Metin --}}
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">SMS Metni <span class="text-red-500">*</span></label>
                    <textarea name="content" id="content" rows="5" x-model="content" x-ref="contentArea"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="SMS mesajınızı yazın..."
                              maxlength="918">{{ old('content') }}</textarea>
                    @error('content') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                    <div class="flex items-center justify-between mt-2">
                        <div class="text-xs text-gray-400">
                            <span x-text="content.length"></span> / 918 karakter
                        </div>
                        <div class="text-xs font-medium" :class="smsParts > 1 ? 'text-orange-600' : 'text-green-600'">
                            <i class="fas fa-sms mr-1"></i>
                            <span x-text="smsParts"></span> SMS
                            <span class="text-gray-400 ml-1">(<span x-text="smsRemaining"></span> karakter kaldı)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-xl flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    SMS gönderimi geri alınamaz
                </p>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition"
                        @click="if(send_type === 'all' && !confirm('Tüm müşterilere SMS göndermek istediğinize emin misiniz?')) $event.preventDefault()">
                    <i class="fas fa-paper-plane mr-2"></i> SMS Gönder
                </button>
            </div>
        </div>
    </div>

    {{-- Sağ: Önizleme & Bilgi --}}
    <div class="space-y-6">
        {{-- SMS Önizleme --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">SMS Önizleme</h3>
            </div>
            <div class="p-4">
                <div class="bg-gray-900 rounded-xl p-4 min-h-[120px]">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-6 h-6 bg-indigo-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-sms text-white text-[10px]"></i>
                        </div>
                        <span class="text-xs text-gray-400">SMS Mesajı</span>
                    </div>
                    <div class="bg-gray-800 rounded-lg p-3">
                        <p class="text-sm text-gray-200 whitespace-pre-wrap break-words" x-text="content || 'Mesaj içeriği buraya gelecek...'"></p>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-[10px] text-gray-500">
                        <span x-text="content.length + ' karakter'"></span>
                        <span x-text="smsParts + ' SMS parça'"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gönderim Özeti --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Gönderim Özeti</h3>
            </div>
            <div class="p-4 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Gönderim Tipi:</span>
                    <span class="font-medium text-gray-900" x-text="sendTypeLabel"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">SMS Parça:</span>
                    <span class="font-medium text-gray-900" x-text="smsParts"></span>
                </div>
                <div class="flex items-center justify-between" x-show="send_type === 'manual'" x-cloak>
                    <span class="text-gray-500">Alıcı Sayısı:</span>
                    <span class="font-medium text-gray-900" x-text="phoneCount"></span>
                </div>
            </div>
        </div>

        {{-- Hızlı Bilgi --}}
        <div class="bg-blue-50 rounded-xl border border-blue-100 p-4">
            <h4 class="text-sm font-semibold text-blue-800 mb-2"><i class="fas fa-lightbulb mr-1"></i> Bilgilendirme</h4>
            <ul class="text-xs text-blue-700 space-y-1.5">
                <li><i class="fas fa-check text-[10px] mr-1"></i> 160 karaktere kadar 1 SMS olarak gönderilir</li>
                <li><i class="fas fa-check text-[10px] mr-1"></i> 160 karakter üstü mesajlar 153'er karakterlik parçalara bölünür</li>
                <li><i class="fas fa-check text-[10px] mr-1"></i> Türkçe karakterler (ç, ğ, ı, ö, ş, ü) ek alan kullanabilir</li>
                <li><i class="fas fa-check text-[10px] mr-1"></i> Kara listedeki numaralar otomatik atlanır</li>
            </ul>
        </div>
    </div>
</form>

@push('scripts')
<script>
function composeForm() {
    const templateContents = {
        @foreach($templates as $template)
            '{{ $template->id }}': @json($template->content),
        @endforeach
    };

    return {
        send_type: '{{ old('send_type', 'single') }}',
        selectedTemplate: '{{ old('template_id', '') }}',
        content: @json(old('content', '')),
        phoneList: @json(old('phone_list', '')),

        get smsParts() {
            const len = this.content.length;
            if (len === 0) return 0;
            if (len <= 160) return 1;
            return Math.ceil(len / 153);
        },

        get smsRemaining() {
            const len = this.content.length;
            if (len === 0) return 160;
            if (len <= 160) return 160 - len;
            const parts = Math.ceil(len / 153);
            return (parts * 153) - len;
        },

        get phoneCount() {
            if (!this.phoneList.trim()) return 0;
            return this.phoneList.split(/[\n,]+/).filter(p => p.trim().length > 0).length;
        },

        get sendTypeLabel() {
            const labels = {
                'single': 'Tek Numara',
                'segment': 'Segment',
                'customer_type': 'Müşteri Tipi',
                'all': 'Tüm Müşteriler',
                'manual': 'Manuel Liste'
            };
            return labels[this.send_type] || '-';
        },

        applyTemplate() {
            if (this.selectedTemplate && templateContents[this.selectedTemplate]) {
                this.content = templateContents[this.selectedTemplate];
            }
        }
    }
}
</script>
@endpush
@endsection
