@extends('layouts.app')
@section('title', 'SMS Şablonu Düzenle')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Şablonu Düzenle</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $template->name }}</p>
    </div>
    <a href="{{ route('sms.templates.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
        <i class="fas fa-arrow-left mr-2"></i> Şablonlara Dön
    </a>
</div>

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
    </div>
@endif

<form action="{{ route('sms.templates.update', $template->id) }}" method="POST"
      x-data="templateForm()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    @csrf
    @method('PUT')

    {{-- Sol: Form --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Şablon Bilgileri</h3>
            </div>
            <div class="p-6 space-y-5">
                {{-- Şablon Adı --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Şablon Adı <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" x-model="name"
                           value="{{ old('name', $template->name) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Örn: Hoş Geldiniz Mesajı">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kod --}}
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Şablon Kodu <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" x-model="code"
                           value="{{ old('code', $template->code) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="hos-geldiniz-mesaji">
                    @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category" id="category"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Kategori Seçin</option>
                        @foreach($categories as $catKey => $catLabel)
                            <option value="{{ $catKey }}" {{ old('category', $template->category) == $catKey ? 'selected' : '' }}>{{ $catLabel }}</option>
                        @endforeach
                    </select>
                    @error('category') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- İçerik --}}
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Mesaj İçeriği <span class="text-red-500">*</span></label>
                    <textarea name="content" id="content" rows="6" x-model="content" x-ref="contentArea"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="SMS mesaj içeriğini yazın..."
                              maxlength="918">{{ old('content', $template->content) }}</textarea>
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

                {{-- Aktif --}}
                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                               {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                    <span class="text-sm font-medium text-gray-700">Aktif</span>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 rounded-b-xl flex justify-end gap-3">
                <a href="{{ route('sms.templates.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    İptal
                </a>
                <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-2"></i> Değişiklikleri Kaydet
                </button>
            </div>
        </div>
    </div>

    {{-- Sağ: Değişkenler & Önizleme --}}
    <div class="space-y-6">
        {{-- Kullanılabilir Değişkenler --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-800">Kullanılabilir Değişkenler</h3>
                <p class="text-xs text-gray-400 mt-1">Tıklayarak mesaja ekleyin</p>
            </div>
            <div class="p-4 space-y-2 max-h-96 overflow-y-auto">
                @foreach($variables as $var => $desc)
                    <button type="button"
                            @click="insertVariable('{{ $var }}')"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-left hover:bg-indigo-50 transition group">
                        <div>
                            <code class="text-xs font-mono text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded">{{ $var }}</code>
                            <p class="text-xs text-gray-500 mt-1">{{ $desc }}</p>
                        </div>
                        <i class="fas fa-plus-circle text-gray-300 group-hover:text-indigo-500 transition"></i>
                    </button>
                @endforeach
            </div>
        </div>

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
    </div>
</form>

@push('scripts')
<script>
function templateForm() {
    return {
        name: @json(old('name', $template->name)),
        code: @json(old('code', $template->code)),
        content: @json(old('content', $template->content)),

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

        insertVariable(variable) {
            const ta = this.$refs.contentArea;
            const start = ta.selectionStart;
            const end = ta.selectionEnd;
            const text = this.content;
            this.content = text.substring(0, start) + variable + text.substring(end);
            this.$nextTick(() => {
                ta.focus();
                ta.selectionStart = ta.selectionEnd = start + variable.length;
            });
        }
    }
}
</script>
@endpush
@endsection
