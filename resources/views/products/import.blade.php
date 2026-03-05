@extends('layouts.app')
@section('title', 'Dosyadan Ürün Yükleme')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Başlık --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Dosyadan Ürün Yükle</h2>
            <p class="text-sm text-gray-500 mt-0.5">CSV veya Excel dosyasından toplu ürün aktarın</p>
        </div>
    </div>

    {{-- Hatalar --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                <div class="text-sm text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if(session('import_errors') && count(session('import_errors')) > 0)
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-orange-700 mb-2">
                <i class="fas fa-triangle-exclamation mr-1"></i> Bazı satırlarda hata oluştu:
            </p>
            <ul class="text-xs text-orange-700 space-y-1 list-disc list-inside">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Şablon İndir --}}
    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 flex items-center justify-between gap-4">
        <div>
            <p class="font-semibold text-indigo-900 text-sm">1. Adım: Şablonu İndir</p>
            <p class="text-xs text-indigo-700 mt-1">
                Örnek CSV şablonunu indirip ürünlerinizi girin. Excel'de düzenleyip tekrar CSV olarak kaydedin.
            </p>
            <p class="text-xs text-indigo-600 mt-2 font-mono bg-indigo-100 rounded px-2 py-1 inline-block">
                barkod ; urun_adi ; kategori_adi ; birim ; alis_fiyati ; satis_fiyati ; kdv_orani ; stok_miktari ; kritik_stok ; aciklama
            </p>
        </div>
        <a href="{{ route('products.import.template') }}"
           class="flex-shrink-0 inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
            <i class="fas fa-download"></i>
            Şablon İndir
        </a>
    </div>

    {{-- Yükleme Formu --}}
    <div class="bg-white rounded-xl border p-6 shadow-sm">
        <p class="font-semibold text-gray-800 mb-5 text-sm">2. Adım: Dosyayı Yükle</p>

        <form action="{{ route('products.import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5"
              x-data="{ fileName: '', dragging: false }"
              @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
              @drop.prevent="dragging = false; let f = $event.dataTransfer.files[0]; if(f){ fileName = f.name; $refs.fileInput.files = $event.dataTransfer.files; }">
            @csrf

            {{-- Dosya sürükle-bırak alanı --}}
            <div :class="dragging ? 'border-indigo-400 bg-indigo-50' : 'border-gray-300 bg-gray-50'"
                 class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-colors"
                 @click="$refs.fileInput.click()">
                <i class="fas fa-file-arrow-up text-3xl text-gray-400 mb-3 block"></i>
                <p class="text-sm text-gray-600" x-text="fileName || 'CSV veya Excel dosyası sürükleyin ya da tıklayın'"></p>
                <p class="text-xs text-gray-400 mt-1">.csv, .xlsx, .xls — Maks. 10 MB</p>
                <input type="file" name="file" id="file" x-ref="fileInput" accept=".csv,.xlsx,.xls,.txt" class="hidden"
                       @change="fileName = $event.target.files[0]?.name || ''">
            </div>

            {{-- Seçenekler --}}
            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Seçenekler</p>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="update_existing" value="1"
                           class="mt-0.5 w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <span class="text-sm font-medium text-gray-700">Mevcut ürünleri güncelle</span>
                        <p class="text-xs text-gray-500 mt-0.5">Aynı barkoda sahip ürünler güncellenir. İşaretlenmezse bu ürünler atlanır.</p>
                    </div>
                </label>
            </div>

            {{-- Sütun Açıklamaları --}}
            <details class="bg-gray-50 rounded-lg border">
                <summary class="px-4 py-3 text-sm font-medium text-gray-700 cursor-pointer select-none">
                    <i class="fas fa-circle-info mr-2 text-gray-400"></i>Sütun açıklamaları
                </summary>
                <div class="px-4 pb-4">
                    <table class="w-full text-xs mt-2">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="pb-2 font-semibold">Sütun</th>
                                <th class="pb-2 font-semibold">Zorunlu</th>
                                <th class="pb-2 font-semibold">Açıklama</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600">
                            <tr><td class="py-1.5 font-mono">urun_adi</td><td><span class="text-red-600 font-bold">Evet</span></td><td>Ürün adı</td></tr>
                            <tr><td class="py-1.5 font-mono">barkod</td><td>Hayır</td><td>Barkod numarası (aynı barkod varsa güncelleme için kullanılır)</td></tr>
                            <tr><td class="py-1.5 font-mono">kategori_adi</td><td>Hayır</td><td>Kategori adı — yoksa otomatik oluşturulur</td></tr>
                            <tr><td class="py-1.5 font-mono">birim</td><td>Hayır</td><td>adet, kg, lt, m... (varsayılan: adet)</td></tr>
                            <tr><td class="py-1.5 font-mono">alis_fiyati</td><td>Hayır</td><td>Alış fiyatı (ondalık için nokta veya virgül)</td></tr>
                            <tr><td class="py-1.5 font-mono">satis_fiyati</td><td>Hayır</td><td>Satış fiyatı</td></tr>
                            <tr><td class="py-1.5 font-mono">kdv_orani</td><td>Hayır</td><td>KDV % (0, 1, 10, 20 — varsayılan: 20)</td></tr>
                            <tr><td class="py-1.5 font-mono">stok_miktari</td><td>Hayır</td><td>Mevcut stok miktarı</td></tr>
                            <tr><td class="py-1.5 font-mono">kritik_stok</td><td>Hayır</td><td>Kritik stok uyarı eşiği (varsayılan: 5)</td></tr>
                            <tr><td class="py-1.5 font-mono">aciklama</td><td>Hayır</td><td>Ürün açıklaması</td></tr>
                        </tbody>
                    </table>
                    <p class="text-xs text-gray-400 mt-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        CSV ayırıcı olarak <strong>noktalı virgül (;)</strong> veya <strong>virgül (,)</strong> kullanabilirsiniz. Türkçe karakter için UTF-8 ile kaydedin.
                    </p>
                </div>
            </details>

            <button type="submit"
                    class="w-full bg-indigo-600 text-white py-3 rounded-lg font-medium hover:bg-indigo-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-upload"></i>
                Ürünleri Yükle
            </button>
        </form>
    </div>

</div>
@endsection
