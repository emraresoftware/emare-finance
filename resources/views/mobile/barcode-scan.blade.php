@extends('layouts.app')

@section('title', 'Barkod Tara')

@section('content')
<div x-data="barcodeApp()" class="max-w-lg mx-auto px-4 py-4">
    {{-- Üst Bar --}}
    <div class="flex items-center mb-4">
        <a href="{{ route('mobile.index') }}" class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-xl mr-3">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Barkod Tara</h1>
    </div>

    {{-- Kamera ile Barkod Tarama --}}
    <div class="relative bg-black rounded-2xl overflow-hidden mb-4" style="aspect-ratio: 16/9;" x-show="!productFound">
        <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>

        {{-- Tarama Çerçevesi --}}
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-64 h-32 border-2 border-white/70 rounded-lg relative">
                <div class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-blue-400 rounded-tl-lg"></div>
                <div class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-blue-400 rounded-tr-lg"></div>
                <div class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-blue-400 rounded-bl-lg"></div>
                <div class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-blue-400 rounded-br-lg"></div>
                {{-- Tarama çizgisi animasyonu --}}
                <div class="absolute top-0 left-2 right-2 h-0.5 bg-red-500 animate-scan-line"></div>
            </div>
        </div>

        {{-- Kamera durumu --}}
        <div x-show="!cameraReady" class="absolute inset-0 flex items-center justify-center bg-black/50">
            <div class="text-white text-center">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p class="text-sm">Kamera başlatılıyor...</p>
            </div>
        </div>
    </div>

    {{-- Manuel Barkod Girişi --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-4" x-show="!productFound">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            <i class="fas fa-keyboard mr-1"></i> Manuel Barkod Girişi
        </label>
        <div class="flex space-x-2">
            <input type="text" x-model="barcodeInput" @keyup.enter="searchBarcode()"
                class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base"
                placeholder="Barkod numarasını girin" inputmode="numeric">
            <button @click="searchBarcode()" :disabled="searching || !barcodeInput"
                class="px-6 py-3 bg-purple-500 text-white rounded-xl font-medium active:bg-purple-600 disabled:opacity-50">
                <i class="fas fa-search" x-show="!searching"></i>
                <i class="fas fa-spinner fa-spin" x-show="searching"></i>
            </button>
        </div>
    </div>

    {{-- Arama Sonucu: Ürün Bulundu --}}
    <div x-show="productFound && foundProduct" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
        <div class="bg-green-50 px-4 py-3 border-b border-green-100">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <span class="font-medium text-green-700">Ürün Bulundu</span>
                <button @click="resetSearch()" class="ml-auto text-green-600 text-sm font-medium">
                    <i class="fas fa-redo mr-1"></i> Yeni Tarama
                </button>
            </div>
        </div>

        <div class="p-4">
            {{-- Ürün Bilgileri --}}
            <div class="flex items-start mb-4">
                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mr-3 flex-shrink-0 overflow-hidden">
                    <template x-if="foundProduct.image_url">
                        <img :src="foundProduct.image_url" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!foundProduct.image_url">
                        <i class="fas fa-box text-gray-400 text-2xl"></i>
                    </template>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900" x-text="foundProduct.name"></h3>
                    <div class="text-sm text-gray-500 mt-1">
                        <span x-text="'Barkod: ' + foundProduct.barcode"></span>
                    </div>
                    <div class="flex items-center mt-2 space-x-3">
                        <span class="text-lg font-bold text-blue-600" x-text="parseFloat(foundProduct.sale_price).toFixed(2) + ' ₺'"></span>
                        <span class="text-sm px-2 py-0.5 rounded-full"
                            :class="foundProduct.stock_quantity > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                            x-text="'Stok: ' + foundProduct.stock_quantity"></span>
                    </div>
                </div>
            </div>

            {{-- İşlem Butonları --}}
            <div class="grid grid-cols-2 gap-3">
                <a :href="'/mobil/urun/' + foundProduct.id" class="flex items-center justify-center bg-gray-100 text-gray-700 py-3 rounded-xl font-medium active:bg-gray-200">
                    <i class="fas fa-eye mr-2"></i> Detay
                </a>
                <button @click="addToOrder(foundProduct)" class="flex items-center justify-center bg-green-500 text-white py-3 rounded-xl font-medium active:bg-green-600">
                    <i class="fas fa-cart-plus mr-2"></i> Siparişe Ekle
                </button>
            </div>
        </div>
    </div>

    {{-- Arama Sonucu: Ürün Bulunamadı --}}
    <div x-show="productFound && !foundProduct" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-4">
        <div class="bg-yellow-50 px-4 py-3 border-b border-yellow-100">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>
                <span class="font-medium text-yellow-700">Ürün Bulunamadı</span>
                <button @click="resetSearch()" class="ml-auto text-yellow-600 text-sm font-medium">
                    <i class="fas fa-redo mr-1"></i> Yeni Tarama
                </button>
            </div>
        </div>

        <div class="p-4 text-center">
            <p class="text-gray-600 mb-1">Barkod: <strong x-text="lastSearchedBarcode"></strong></p>
            <p class="text-gray-500 text-sm mb-4">Bu barkoda sahip ürün bulunamadı.</p>

            <a :href="'{{ route("mobile.camera-add") }}'" class="block w-full bg-blue-500 text-white py-3 rounded-xl font-medium text-center active:bg-blue-600">
                <i class="fas fa-plus mr-2"></i> Yeni Ürün Olarak Ekle
            </a>
        </div>
    </div>

    {{-- Son Taranan Ürünler --}}
    <div x-show="recentScans.length > 0" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h3 class="font-medium text-gray-700">
                <i class="fas fa-history mr-1"></i> Son Tarananlar
            </h3>
        </div>
        <div class="divide-y divide-gray-50">
            <template x-for="scan in recentScans" :key="scan.barcode + scan.time">
                <div class="flex items-center px-4 py-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                        <i class="fas fa-barcode text-gray-400"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-900 truncate" x-text="scan.name || scan.barcode"></div>
                        <div class="text-xs text-gray-500" x-text="scan.time"></div>
                    </div>
                    <template x-if="scan.found">
                        <span class="text-sm font-bold text-blue-600" x-text="scan.price + ' ₺'"></span>
                    </template>
                    <template x-if="!scan.found">
                        <span class="text-xs text-red-500 font-medium">Bulunamadı</span>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<style>
@keyframes scan-line {
    0%, 100% { top: 0; }
    50% { top: calc(100% - 2px); }
}
.animate-scan-line {
    animation: scan-line 2s ease-in-out infinite;
}
</style>

@push('scripts')
<script>
function barcodeApp() {
    return {
        cameraReady: false,
        barcodeInput: '',
        searching: false,
        productFound: false,
        foundProduct: null,
        lastSearchedBarcode: '',
        recentScans: [],
        stream: null,

        init() {
            this.startCamera();
        },

        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } },
                    audio: false
                });
                this.$refs.video.srcObject = this.stream;
                this.cameraReady = true;
            } catch (e) {
                console.warn('Kamera erişilemedi:', e);
            }
        },

        async searchBarcode() {
            if (!this.barcodeInput || this.searching) return;

            this.searching = true;
            this.lastSearchedBarcode = this.barcodeInput;

            try {
                const response = await fetch('{{ route("mobile.barcode-search") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ barcode: this.barcodeInput })
                });

                const data = await response.json();

                if (data.found) {
                    this.foundProduct = data.product;
                    this.recentScans.unshift({
                        barcode: this.barcodeInput,
                        name: data.product.name,
                        price: parseFloat(data.product.sale_price).toFixed(2),
                        found: true,
                        time: new Date().toLocaleTimeString('tr-TR')
                    });
                } else {
                    this.foundProduct = null;
                    this.recentScans.unshift({
                        barcode: this.barcodeInput,
                        name: null,
                        price: null,
                        found: false,
                        time: new Date().toLocaleTimeString('tr-TR')
                    });
                }

                this.productFound = true;

                // Stop camera when product found
                if (this.stream) {
                    this.stream.getTracks().forEach(t => t.stop());
                }
            } catch (e) {
                console.error('Arama hatası:', e);
                alert('Barkod araması sırasında hata oluştu.');
            } finally {
                this.searching = false;
            }
        },

        resetSearch() {
            this.productFound = false;
            this.foundProduct = null;
            this.barcodeInput = '';
            this.$nextTick(() => this.startCamera());
        },

        addToOrder(product) {
            // Ürünü session storage'a ekle ve sipariş sayfasına yönlendir
            let cart = JSON.parse(sessionStorage.getItem('mobileCart') || '[]');
            const existing = cart.find(item => item.id === product.id);
            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    barcode: product.barcode,
                    price: parseFloat(product.sale_price),
                    vat_rate: parseFloat(product.vat_rate || 0),
                    quantity: 1
                });
            }
            sessionStorage.setItem('mobileCart', JSON.stringify(cart));
            window.location.href = '{{ route("mobile.quick-order") }}';
        }
    }
}
</script>
@endpush
@endsection
