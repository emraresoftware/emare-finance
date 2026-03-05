@extends('layouts.app')

@section('title', 'Kameradan Ürün Ekle')

@section('content')
<div x-data="cameraApp()" class="max-w-lg mx-auto px-4 py-4">
    {{-- Üst Bar --}}
    <div class="flex items-center mb-4">
        <a href="{{ route('mobile.index') }}" class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-xl mr-3">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Kameradan Ürün Ekle</h1>
    </div>

    {{-- Adım Göstergesi --}}
    <div class="flex items-center justify-center mb-6">
        <div class="flex items-center space-x-2">
            <div :class="step === 1 ? 'bg-blue-500 text-white' : 'bg-green-500 text-white'" class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">
                <template x-if="step > 1"><i class="fas fa-check text-xs"></i></template>
                <template x-if="step === 1"><span>1</span></template>
            </div>
            <div class="w-12 h-1 rounded" :class="step > 1 ? 'bg-green-500' : 'bg-gray-200'"></div>
            <div :class="step === 2 ? 'bg-blue-500 text-white' : (step > 2 ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500')" class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">
                <template x-if="step > 2"><i class="fas fa-check text-xs"></i></template>
                <template x-if="step <= 2"><span>2</span></template>
            </div>
            <div class="w-12 h-1 rounded" :class="step > 2 ? 'bg-green-500' : 'bg-gray-200'"></div>
            <div :class="step === 3 ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold">3</div>
        </div>
    </div>

    {{-- ADIM 1: Fotoğraf Çekme --}}
    <div x-show="step === 1" x-transition>
        {{-- Kamera Önizleme --}}
        <div class="relative bg-black rounded-2xl overflow-hidden mb-4" style="aspect-ratio: 4/3;">
            <video x-ref="video" autoplay playsinline class="w-full h-full object-cover" x-show="!photoTaken"></video>
            <canvas x-ref="canvas" class="w-full h-full object-cover hidden"></canvas>
            <img x-ref="preview" :src="photoPreview" class="w-full h-full object-cover" x-show="photoTaken" x-cloak>

            {{-- Kamera başlatılıyor --}}
            <div x-show="!cameraReady && !photoTaken" class="absolute inset-0 flex flex-col items-center justify-center text-white">
                <i class="fas fa-spinner fa-spin text-3xl mb-3"></i>
                <p class="text-sm">Kamera başlatılıyor...</p>
            </div>
        </div>

        {{-- Kamera Kontrolleri --}}
        <div class="flex items-center justify-center space-x-6 mb-4">
            <template x-if="!photoTaken">
                <div class="flex items-center space-x-6">
                    {{-- Galeri --}}
                    <label class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center cursor-pointer active:bg-gray-200">
                        <i class="fas fa-images text-gray-600 text-xl"></i>
                        <input type="file" accept="image/*" class="hidden" @change="selectFromGallery($event)">
                    </label>

                    {{-- Çekim Butonu --}}
                    <button @click="takePhoto()" :disabled="!cameraReady"
                        class="w-20 h-20 bg-white border-4 border-blue-500 rounded-full flex items-center justify-center active:scale-90 transition-transform disabled:opacity-50">
                        <div class="w-14 h-14 bg-blue-500 rounded-full"></div>
                    </button>

                    {{-- Kamera Değiştir --}}
                    <button @click="switchCamera()" class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center active:bg-gray-200">
                        <i class="fas fa-sync-alt text-gray-600 text-xl"></i>
                    </button>
                </div>
            </template>

            <template x-if="photoTaken">
                <div class="flex items-center space-x-6">
                    {{-- Tekrar Çek --}}
                    <button @click="retakePhoto()" class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center active:bg-red-200">
                        <i class="fas fa-redo text-red-500 text-xl"></i>
                    </button>

                    {{-- Onayla --}}
                    <button @click="confirmPhoto()" :disabled="uploading"
                        class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center active:scale-90 transition-transform text-white disabled:opacity-50">
                        <i class="fas fa-check text-3xl" x-show="!uploading"></i>
                        <i class="fas fa-spinner fa-spin text-2xl" x-show="uploading"></i>
                    </button>

                    <div class="w-14 h-14"></div>
                </div>
            </template>
        </div>

        {{-- Hata Mesajı --}}
        <div x-show="cameraError" x-cloak class="bg-red-50 border border-red-200 rounded-xl p-4 mb-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-2"></i>
                <div>
                    <p class="text-red-700 text-sm font-medium">Kamera Erişimi Başarısız</p>
                    <p class="text-red-600 text-xs mt-1">Tarayıcı ayarlarından kamera iznini açın veya galeriden fotoğraf seçin.</p>
                </div>
            </div>
        </div>

        {{-- Galeriden Seç Butonu (yedek) --}}
        <label x-show="cameraError" class="block w-full bg-blue-500 text-white text-center py-4 rounded-xl font-medium cursor-pointer active:bg-blue-600">
            <i class="fas fa-images mr-2"></i> Galeriden Fotoğraf Seç
            <input type="file" accept="image/*" class="hidden" @change="selectFromGallery($event)">
        </label>
    </div>

    {{-- ADIM 2: Ürün Bilgileri --}}
    <div x-show="step === 2" x-transition>
        <form @submit.prevent="saveProduct()">
            {{-- Fotoğraf Önizleme --}}
            <div class="flex items-center bg-gray-50 rounded-xl p-3 mb-4">
                <img :src="photoPreview" class="w-16 h-16 object-cover rounded-lg mr-3">
                <div>
                    <div class="text-sm font-medium text-gray-700">Ürün Fotoğrafı</div>
                    <div class="text-xs text-green-600"><i class="fas fa-check-circle mr-1"></i>Yüklendi</div>
                </div>
                <button type="button" @click="step = 1; retakePhoto()" class="ml-auto text-gray-400">
                    <i class="fas fa-pen text-sm"></i>
                </button>
            </div>

            {{-- Ürün Adı --}}
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ürün Adı *</label>
                <input type="text" x-model="product.name" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                    placeholder="Ürün adını girin">
            </div>

            {{-- Barkod --}}
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Barkod</label>
                <input type="text" x-model="product.barcode"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                    placeholder="Barkod numarası (opsiyonel)">
            </div>

            {{-- Kategori --}}
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select x-model="product.category_id"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base bg-white">
                    <option value="">Kategori seçin</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fiyat Satırı --}}
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alış Fiyatı</label>
                    <div class="relative">
                        <input type="number" step="0.01" x-model="product.purchase_price"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base pr-8"
                            placeholder="0.00">
                        <span class="absolute right-3 top-3 text-gray-400">₺</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satış Fiyatı *</label>
                    <div class="relative">
                        <input type="number" step="0.01" x-model="product.sale_price" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base pr-8"
                            placeholder="0.00">
                        <span class="absolute right-3 top-3 text-gray-400">₺</span>
                    </div>
                </div>
            </div>

            {{-- Stok ve KDV --}}
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Miktarı</label>
                    <input type="number" x-model="product.stock_quantity"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">KDV Oranı</label>
                    <select x-model="product.vat_rate"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base bg-white">
                        <option value="0">%0</option>
                        <option value="1">%1</option>
                        <option value="10">%10</option>
                        <option value="20">%20</option>
                    </select>
                </div>
            </div>

            {{-- Birim --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Birim</label>
                <select x-model="product.unit"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base bg-white">
                    <option value="Adet">Adet</option>
                    <option value="Kg">Kg</option>
                    <option value="Lt">Lt</option>
                    <option value="Mt">Mt</option>
                    <option value="Paket">Paket</option>
                    <option value="Kutu">Kutu</option>
                </select>
            </div>

            {{-- Kaydet Butonu --}}
            <button type="submit" :disabled="saving"
                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-4 rounded-xl font-bold text-lg shadow-md active:scale-[0.98] transition-transform disabled:opacity-50">
                <i class="fas fa-save mr-2" x-show="!saving"></i>
                <i class="fas fa-spinner fa-spin mr-2" x-show="saving"></i>
                <span x-text="saving ? 'Kaydediliyor...' : 'Ürünü Kaydet'"></span>
            </button>
        </form>
    </div>

    {{-- ADIM 3: Başarılı --}}
    <div x-show="step === 3" x-transition class="text-center py-8">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check-circle text-green-500 text-4xl"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-900 mb-2">Ürün Başarıyla Eklendi!</h2>
        <p class="text-gray-500 mb-6" x-text="product.name"></p>

        <div class="space-y-3">
            <button @click="resetForm()" class="w-full bg-blue-500 text-white py-4 rounded-xl font-bold active:bg-blue-600">
                <i class="fas fa-plus mr-2"></i> Yeni Ürün Ekle
            </button>
            <a href="{{ route('mobile.quick-order') }}" class="block w-full bg-green-500 text-white py-4 rounded-xl font-bold text-center active:bg-green-600">
                <i class="fas fa-cart-plus mr-2"></i> Sipariş Oluştur
            </a>
            <a href="{{ route('mobile.index') }}" class="block w-full bg-gray-100 text-gray-700 py-4 rounded-xl font-bold text-center active:bg-gray-200">
                <i class="fas fa-home mr-2"></i> Ana Sayfa
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cameraApp() {
    return {
        step: 1,
        cameraReady: false,
        cameraError: false,
        photoTaken: false,
        photoPreview: '',
        photoPath: '',
        uploading: false,
        saving: false,
        stream: null,
        facingMode: 'environment',
        product: {
            name: '',
            barcode: '',
            category_id: '',
            purchase_price: '',
            sale_price: '',
            stock_quantity: '0',
            vat_rate: '20',
            unit: 'Adet',
            image_path: ''
        },

        init() {
            this.startCamera();
        },

        async startCamera() {
            this.cameraError = false;
            this.cameraReady = false;
            try {
                if (this.stream) {
                    this.stream.getTracks().forEach(t => t.stop());
                }
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: this.facingMode, width: { ideal: 1280 }, height: { ideal: 960 } },
                    audio: false
                });
                this.$refs.video.srcObject = this.stream;
                this.cameraReady = true;
            } catch (e) {
                console.error('Kamera hatası:', e);
                this.cameraError = true;
            }
        },

        switchCamera() {
            this.facingMode = this.facingMode === 'environment' ? 'user' : 'environment';
            this.startCamera();
        },

        takePhoto() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            this.photoPreview = canvas.toDataURL('image/jpeg', 0.85);
            this.photoTaken = true;

            // Kamerayı durdur
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
            }
        },

        selectFromGallery(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.photoPreview = e.target.result;
                this.photoTaken = true;
                if (this.stream) {
                    this.stream.getTracks().forEach(t => t.stop());
                }
            };
            reader.readAsDataURL(file);
            this._selectedFile = file;
        },

        retakePhoto() {
            this.photoTaken = false;
            this.photoPreview = '';
            this.photoPath = '';
            this._selectedFile = null;
            this.$nextTick(() => this.startCamera());
        },

        async confirmPhoto() {
            this.uploading = true;
            try {
                let formData = new FormData();

                if (this._selectedFile) {
                    formData.append('photo', this._selectedFile);
                } else {
                    // Canvas'dan blob oluştur
                    const canvas = this.$refs.canvas;
                    const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.85));
                    formData.append('photo', blob, 'camera-photo.jpg');
                }

                const response = await fetch('{{ route("mobile.upload-photo") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    this.photoPath = data.path;
                    this.product.image_path = data.path;
                    this.step = 2;
                } else {
                    alert('Fotoğraf yüklenemedi: ' + (data.message || 'Bilinmeyen hata'));
                }
            } catch (e) {
                console.error('Yükleme hatası:', e);
                alert('Fotoğraf yüklenirken bir hata oluştu.');
            } finally {
                this.uploading = false;
            }
        },

        async saveProduct() {
            this.saving = true;
            try {
                const response = await fetch('{{ route("mobile.store-product") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.product)
                });

                const data = await response.json();
                if (data.success) {
                    this.step = 3;
                } else {
                    let msg = 'Ürün kaydedilemedi.';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join('\n');
                    }
                    alert(msg);
                }
            } catch (e) {
                console.error('Kayıt hatası:', e);
                alert('Bir hata oluştu.');
            } finally {
                this.saving = false;
            }
        },

        resetForm() {
            this.step = 1;
            this.photoTaken = false;
            this.photoPreview = '';
            this.photoPath = '';
            this._selectedFile = null;
            this.product = {
                name: '', barcode: '', category_id: '', purchase_price: '',
                sale_price: '', stock_quantity: '0', vat_rate: '20', unit: 'Adet', image_path: ''
            };
            this.$nextTick(() => this.startCamera());
        }
    }
}
</script>
@endpush
@endsection
