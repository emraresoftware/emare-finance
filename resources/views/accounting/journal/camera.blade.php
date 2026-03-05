@extends('layouts.app')
@section('title', 'Kamera ile Fiş Giriş')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="cameraReceipt()">

    {{-- Başlık --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('accounting.journal.index') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Kamera ile Fiş Giriş</h2>
            <p class="text-sm text-gray-500">Personel / gider fişini fotoğraflayın, muhasebe fişi otomatik hazırlanır</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- ADIM 1: Fotoğraf Çek --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-3 flex items-center gap-2">
            <i class="fas fa-camera text-white text-lg"></i>
            <h3 class="font-semibold text-white">Adım 1 — Fişi Fotoğrafla</h3>
        </div>
        <div class="p-5">
            {{-- Kamera/Galeri seçici --}}
            <div class="flex gap-3 mb-4">
                <button type="button" @click="openCamera('camera')"
                        class="flex-1 flex flex-col items-center gap-2 border-2 border-dashed 
                               border-indigo-300 rounded-xl p-4 hover:bg-indigo-50 transition"
                        :class="captureMode === 'camera' ? 'border-indigo-500 bg-indigo-50' : ''">
                    <i class="fas fa-camera text-2xl text-indigo-600"></i>
                    <span class="text-sm font-medium text-indigo-700">Kamera ile Çek</span>
                    <span class="text-xs text-gray-400">Gerçek zamanlı kamera</span>
                </button>
                <button type="button" @click="openCamera('file')"
                        class="flex-1 flex flex-col items-center gap-2 border-2 border-dashed
                               border-purple-300 rounded-xl p-4 hover:bg-purple-50 transition"
                        :class="captureMode === 'file' ? 'border-purple-500 bg-purple-50' : ''">
                    <i class="fas fa-image text-2xl text-purple-600"></i>
                    <span class="text-sm font-medium text-purple-700">Galeriden Seç</span>
                    <span class="text-xs text-gray-400">Mevcut fotoğraf</span>
                </button>
            </div>

            {{-- Canlı kamera görünümü --}}
            <div x-show="captureMode === 'camera' && !photoData" class="relative">
                <video id="cameraStream" autoplay playsinline
                       class="w-full rounded-xl border-2 border-indigo-200 bg-black"
                       style="max-height:360px; object-fit:cover"></video>
                <button type="button" @click="capturePhoto"
                        class="absolute bottom-4 left-1/2 -translate-x-1/2 w-16 h-16 bg-white rounded-full shadow-xl border-4 border-indigo-500 flex items-center justify-center hover:scale-105 transition">
                    <i class="fas fa-circle text-indigo-600 text-2xl"></i>
                </button>
            </div>

            {{-- Dosya input --}}
            <input type="file" id="fileInput" accept="image/*" class="hidden"
                   @change="handleFile($event)">

            {{-- Fotoğraf önizleme --}}
            <div x-show="photoData" class="relative">
                <img :src="photoData" class="w-full rounded-xl border-2 border-green-300 object-contain" style="max-height:360px">
                <button type="button" @click="clearPhoto"
                        class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-700 shadow">
                    <i class="fas fa-times text-sm"></i>
                </button>
                <div class="absolute bottom-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded-full">
                    <i class="fas fa-check mr-1"></i> Fotoğraf hazır
                </div>
            </div>

            {{-- Boş durum --}}
            <div x-show="!captureMode && !photoData"
                 class="border-2 border-dashed border-gray-200 rounded-xl p-10 text-center text-gray-400">
                <i class="fas fa-receipt text-4xl block mb-3"></i>
                <p class="text-sm">Fişi fotoğraflamak için yukarıdan seçin</p>
            </div>
        </div>
    </div>

    {{-- ADIM 2: Fiş Bilgileri --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <div class="bg-orange-500 px-5 py-3 flex items-center gap-2">
            <i class="fas fa-pen-to-square text-white"></i>
            <h3 class="font-semibold text-white">Adım 2 — Fiş Bilgilerini Girin</h3>
        </div>
        <form action="{{ route('accounting.journal.camera.store') }}" method="POST" enctype="multipart/form-data" id="cameraForm">
            @csrf
            {{-- Fotoğraf gizli input --}}
            <input type="hidden" name="photo_data" :value="photoData">

            <div class="p-5 space-y-4">
                {{-- Hızlı Şablon Butonları --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Hızlı Şablon</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="applyTemplate('expense_cash')"
                                class="px-3 py-1.5 bg-orange-100 text-orange-700 rounded-lg text-xs font-medium hover:bg-orange-200">
                            <i class="fas fa-receipt mr-1"></i> Nakit Gider
                        </button>
                        <button type="button" @click="applyTemplate('expense_card')"
                                class="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium hover:bg-blue-200">
                            <i class="fas fa-credit-card mr-1"></i> Kart Gider
                        </button>
                        <button type="button" @click="applyTemplate('personel')"
                                class="px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg text-xs font-medium hover:bg-purple-200">
                            <i class="fas fa-user mr-1"></i> Personel Fişi
                        </button>
                        <button type="button" @click="applyTemplate('purchase')"
                                class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-medium hover:bg-green-200">
                            <i class="fas fa-shopping-cart mr-1"></i> Alım Faturası
                        </button>
                    </div>
                </div>

                {{-- Temel Bilgiler --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Tarih *</label>
                        <input type="date" name="date" :value="form.date" @change="form.date = $event.target.value"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Fiş Türü</label>
                        <select name="type" x-model="form.type"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="expense">Gider</option>
                            <option value="purchase">Alım</option>
                            <option value="payroll">Maaş/Personel</option>
                            <option value="manual">Manuel</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Açıklama *</label>
                    <input type="text" name="description" x-model="form.description" required
                           placeholder="örn: Personel yol parası fişi — Ahmet Koç"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tutar (₺) *</label>
                    <input type="number" step="0.01" min="0" x-model="form.amount" @input="updateLinesFromAmount"
                           placeholder="0.00"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-right focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            {{-- Satırlar (otomatik şablondan doldurulur) --}}
            <div class="border-t px-5 py-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-semibold text-gray-700">Muhasebe Satırları</h4>
                    <span class="text-xs px-2 py-1 rounded-full"
                          :class="isBalanced ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                        <span x-show="isBalanced"><i class="fas fa-check mr-1"></i> Dengede</span>
                        <span x-show="!isBalanced"><i class="fas fa-exclamation mr-1"></i> Dengede değil</span>
                    </span>
                </div>
                <div class="space-y-2">
                    <template x-for="(line, i) in lines" :key="i">
                        <div class="flex gap-2 items-center">
                            <input type="text" :name="'lines[' + i + '][account_code]'" x-model="line.code"
                                   placeholder="Kod" class="w-24 border border-gray-200 rounded px-2 py-1.5 text-xs font-mono">
                            <input type="text" :value="getAccountName(line.code)" readonly
                                   class="flex-1 bg-gray-50 border border-gray-200 rounded px-2 py-1.5 text-xs text-gray-500">
                            <input type="number" step="0.01" :name="'lines[' + i + '][debit]'" x-model="line.debit"
                                   placeholder="Borç" class="w-24 border border-gray-200 rounded px-2 py-1.5 text-xs text-right">
                            <input type="number" step="0.01" :name="'lines[' + i + '][credit]'" x-model="line.credit"
                                   placeholder="Alacak" class="w-24 border border-gray-200 rounded px-2 py-1.5 text-xs text-right">
                        </div>
                    </template>
                </div>
            </div>

            {{-- Fotoğraf upload (form submit anında) --}}
            <input type="file" name="receipt_photo" id="photoFile" class="hidden">

            {{-- Submit --}}
            <div class="px-5 pb-5 flex gap-2">
                <button type="submit" name="post_entry" value="0"
                        class="flex-1 py-3 bg-yellow-500 text-white rounded-xl font-medium hover:bg-yellow-600">
                    <i class="fas fa-floppy-disk mr-2"></i> Taslak Kaydet
                </button>
                <button type="submit" name="post_entry" value="1"
                        :disabled="!isBalanced"
                        class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 disabled:opacity-50">
                    <i class="fas fa-lock mr-2"></i> Kaydet & Kesinleştir
                </button>
            </div>
        </form>
    </div>

</div>

<script>
const accountList = @json($accounts->map(fn($a) => ['code' => $a->code, 'name' => $a->name]));

const TEMPLATES = {
    expense_cash: {
        type: 'expense', desc: 'Nakit Gider Fişi',
        lines: [
            { code: '770', debit: 0, credit: 0 },
            { code: '100', debit: 0, credit: 0 },
        ]
    },
    expense_card: {
        type: 'expense', desc: 'Kart ile Gider Fişi',
        lines: [
            { code: '770', debit: 0, credit: 0 },
            { code: '102', debit: 0, credit: 0 },
        ]
    },
    personel: {
        type: 'payroll', desc: 'Personel Masraf Fişi',
        lines: [
            { code: '720', debit: 0, credit: 0 },
            { code: '100', debit: 0, credit: 0 },
        ]
    },
    purchase: {
        type: 'purchase', desc: 'Mal/Hizmet Alım Faturası',
        lines: [
            { code: '153', debit: 0, credit: 0 },
            { code: '191', debit: 0, credit: 0 },
            { code: '320', debit: 0, credit: 0 },
        ]
    },
};

function cameraReceipt() {
    return {
        captureMode: null,
        photoData: null,
        form: { date: new Date().toISOString().split('T')[0], type: 'expense', description: '', amount: '' },
        lines: [{ code: '770', debit: 0, credit: 0 }, { code: '100', debit: 0, credit: 0 }],
        stream: null,

        get isBalanced() {
            const d = this.lines.reduce((s, l) => s + (parseFloat(l.debit) || 0), 0);
            const c = this.lines.reduce((s, l) => s + (parseFloat(l.credit) || 0), 0);
            return d > 0 && Math.abs(d - c) < 0.01;
        },

        async openCamera(mode) {
            this.captureMode = mode;
            if (mode === 'file') {
                document.getElementById('fileInput').click();
                return;
            }
            // Kamera akışını başlat
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                await this.$nextTick();
                const video = document.getElementById('cameraStream');
                if (video) { video.srcObject = this.stream; }
            } catch (e) {
                alert('Kamera erişimi reddedildi. Lütfen izin verin.');
                this.captureMode = null;
            }
        },

        capturePhoto() {
            const video = document.getElementById('cameraStream');
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            this.photoData = canvas.toDataURL('image/jpeg', 0.85);
            if (this.stream) { this.stream.getTracks().forEach(t => t.stop()); this.stream = null; }

            // Blob'u file input'a aktar
            canvas.toBlob(blob => {
                const file = new File([blob], "receipt.jpg", { type: "image/jpeg" });
                const dt = new DataTransfer();
                dt.items.add(file);
                document.getElementById('photoFile').files = dt.files;
            }, 'image/jpeg', 0.85);
        },

        handleFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = ev => { this.photoData = ev.target.result; };
            reader.readAsDataURL(file);
            // Aynı dosyayı form input'a aktar
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('photoFile').files = dt.files;
        },

        clearPhoto() {
            this.photoData = null;
            this.captureMode = null;
            if (this.stream) { this.stream.getTracks().forEach(t => t.stop()); this.stream = null; }
        },

        applyTemplate(key) {
            const tpl = TEMPLATES[key];
            if (!tpl) return;
            this.form.type = tpl.type;
            this.form.description = tpl.desc;
            this.lines = tpl.lines.map(l => ({ ...l }));
            this.updateLinesFromAmount();
        },

        updateLinesFromAmount() {
            const amt = parseFloat(this.form.amount) || 0;
            if (amt <= 0 || this.lines.length < 2) return;
            const tpl = TEMPLATES[Object.keys(TEMPLATES).find(k => TEMPLATES[k].type === this.form.type)] || TEMPLATES.expense_cash;

            if (this.form.type === 'purchase' && this.lines.length >= 3) {
                const net = Math.round(amt / 1.20 * 100) / 100;
                const vat = Math.round((amt - net) * 100) / 100;
                this.lines[0].debit = net;
                this.lines[1].debit = vat;
                this.lines[2].credit = amt;
            } else {
                this.lines[0].debit = amt;
                this.lines[this.lines.length - 1].credit = amt;
            }
        },

        getAccountName(code) {
            return accountList.find(a => a.code === code)?.name || (code ? '⚠ Hesap bulunamadı' : '');
        }
    }
}
</script>
@endsection
