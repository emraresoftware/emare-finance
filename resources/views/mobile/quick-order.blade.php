@extends('layouts.app')

@section('title', 'Hızlı Sipariş')

@section('content')
<div x-data="quickOrderApp()" class="max-w-lg mx-auto px-4 py-4 pb-32">
    {{-- Üst Bar --}}
    <div class="flex items-center mb-4">
        <a href="{{ route('mobile.index') }}" class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-xl mr-3">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Hızlı Sipariş</h1>
        <div class="ml-auto flex items-center space-x-2">
            <a href="{{ route('mobile.barcode-scan') }}" class="w-10 h-10 flex items-center justify-center bg-purple-100 rounded-xl">
                <i class="fas fa-barcode text-purple-600"></i>
            </a>
        </div>
    </div>

    {{-- Arama --}}
    <div class="relative mb-4">
        <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchProducts()"
            class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base bg-white"
            placeholder="Ürün adı veya barkod ile ara...">
        <button x-show="searchQuery" @click="searchQuery = ''; searchResults = []; showSearch = false"
            class="absolute right-3 top-3 text-gray-400">
            <i class="fas fa-times-circle"></i>
        </button>
    </div>

    {{-- Arama Sonuçları --}}
    <div x-show="showSearch && searchResults.length > 0" x-transition
        class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-4 max-h-64 overflow-y-auto">
        <template x-for="product in searchResults" :key="product.id">
            <button @click="addToCart(product)" class="flex items-center w-full px-4 py-3 hover:bg-gray-50 active:bg-gray-100 border-b border-gray-50 text-left">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0 overflow-hidden">
                    <template x-if="product.image_url">
                        <img :src="product.image_url" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!product.image_url">
                        <i class="fas fa-box text-gray-400"></i>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-gray-900 text-sm truncate" x-text="product.name"></div>
                    <div class="text-xs text-gray-500" x-text="product.barcode || 'Barkod yok'"></div>
                </div>
                <div class="text-right flex-shrink-0 ml-2">
                    <div class="font-bold text-blue-600 text-sm" x-text="parseFloat(product.sale_price).toFixed(2) + ' ₺'"></div>
                    <div class="text-xs" :class="product.stock_quantity > 0 ? 'text-green-600' : 'text-red-500'"
                        x-text="'Stok: ' + product.stock_quantity"></div>
                </div>
            </button>
        </template>
    </div>

    {{-- Kategori Filtreleri --}}
    <div class="flex overflow-x-auto space-x-2 mb-4 pb-1 -mx-4 px-4 no-scrollbar">
        <button @click="filterCategory = ''; loadProducts()"
            :class="filterCategory === '' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700'"
            class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors">
            Tümü
        </button>
        @foreach($categories as $cat)
            <button @click="filterCategory = '{{ $cat->id }}'; loadProducts()"
                :class="filterCategory === '{{ $cat->id }}' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-700'"
                class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors">
                {{ $cat->name }}
            </button>
        @endforeach
    </div>

    {{-- Ürün Listesi --}}
    <div class="grid grid-cols-2 gap-3 mb-4">
        @foreach($products as $product)
            <button @click="addToCart({{ json_encode(['id' => $product->id, 'name' => $product->name, 'barcode' => $product->barcode, 'sale_price' => $product->sale_price, 'vat_rate' => $product->vat_rate, 'stock_quantity' => $product->stock_quantity, 'image_url' => $product->image_url, 'category_id' => $product->category_id]) }})"
                x-show="!filterCategory || filterCategory === '{{ $product->category_id }}'"
                class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 text-left active:scale-[0.97] transition-transform">
                <div class="w-full aspect-square bg-gray-100 rounded-lg mb-2 overflow-hidden flex items-center justify-center">
                    @if($product->image_url)
                        <img src="{{ $product->image_url }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                    @else
                        <i class="fas fa-box text-gray-300 text-3xl"></i>
                    @endif
                </div>
                <div class="font-medium text-gray-900 text-sm truncate">{{ $product->name }}</div>
                <div class="flex items-center justify-between mt-1">
                    <span class="font-bold text-blue-600">{{ number_format($product->sale_price, 2, ',', '.') }} ₺</span>
                    <span class="text-xs {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $product->stock_quantity }} {{ $product->unit ?? 'Adet' }}
                    </span>
                </div>
            </button>
        @endforeach
    </div>

    {{-- Boş ürün durumu --}}
    @if($products->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-box-open text-gray-300 text-5xl mb-3"></i>
            <p class="text-gray-500">Henüz ürün bulunmuyor.</p>
            <a href="{{ route('mobile.camera-add') }}" class="inline-block mt-3 text-blue-500 font-medium">
                <i class="fas fa-plus mr-1"></i> Ürün Ekle
            </a>
        </div>
    @endif

    {{-- Sepet Paneli (Alt Kısım) --}}
    <div x-show="cart.length > 0" x-transition
        class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-2xl z-50 rounded-t-2xl max-w-lg mx-auto"
        :class="cartOpen ? 'max-h-[80vh]' : ''">

        {{-- Sepet Başlık --}}
        <button @click="cartOpen = !cartOpen" class="w-full flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center mr-2 text-sm font-bold"
                    x-text="totalItems"></div>
                <span class="font-bold text-gray-900">Sepet</span>
            </div>
            <div class="flex items-center">
                <span class="font-bold text-green-600 mr-2" x-text="grandTotal.toFixed(2) + ' ₺'"></span>
                <i class="fas" :class="cartOpen ? 'fa-chevron-down' : 'fa-chevron-up'"></i>
            </div>
        </button>

        {{-- Sepet İçeriği --}}
        <div x-show="cartOpen" x-transition class="overflow-y-auto" style="max-height: 40vh;">
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="flex items-center px-4 py-3 border-b border-gray-50">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-900 text-sm truncate" x-text="item.name"></div>
                        <div class="text-xs text-gray-500" x-text="item.price.toFixed(2) + ' ₺/adet'"></div>
                    </div>

                    {{-- Miktar Kontrol --}}
                    <div class="flex items-center bg-gray-100 rounded-lg mx-3">
                        <button @click="decrementItem(index)" class="w-9 h-9 flex items-center justify-center text-gray-600 active:bg-gray-200 rounded-l-lg">
                            <i class="fas" :class="item.quantity === 1 ? 'fa-trash-alt text-red-500 text-xs' : 'fa-minus text-xs'"></i>
                        </button>
                        <span class="w-8 text-center font-bold text-sm" x-text="item.quantity"></span>
                        <button @click="incrementItem(index)" class="w-9 h-9 flex items-center justify-center text-gray-600 active:bg-gray-200 rounded-r-lg">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>

                    <div class="font-bold text-gray-900 text-sm w-16 text-right" x-text="(item.price * item.quantity).toFixed(2) + ' ₺'"></div>
                </div>
            </template>
        </div>

        {{-- Ödeme Bilgileri --}}
        <div x-show="cartOpen" class="px-4 py-3 bg-gray-50 border-t border-gray-100">
            {{-- Müşteri Seçimi --}}
            <div class="mb-3">
                <select x-model="selectedCustomer"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white">
                    <option value="">Müşteri seçin (opsiyonel)</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Ödeme Yöntemi --}}
            <div class="flex space-x-2 mb-3">
                <button @click="paymentMethod = 'cash'"
                    :class="paymentMethod === 'cash' ? 'bg-green-500 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-money-bill-wave mr-1"></i> Nakit
                </button>
                <button @click="paymentMethod = 'credit_card'"
                    :class="paymentMethod === 'credit_card' ? 'bg-green-500 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-credit-card mr-1"></i> Kart
                </button>
                <button @click="paymentMethod = 'mixed'"
                    :class="paymentMethod === 'mixed' ? 'bg-green-500 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-1 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-exchange-alt mr-1"></i> Karışık
                </button>
            </div>

            {{-- Toplam --}}
            <div class="flex justify-between items-center mb-3 text-sm">
                <span class="text-gray-500">Ara Toplam</span>
                <span class="font-medium" x-text="subtotal.toFixed(2) + ' ₺'"></span>
            </div>
            <div class="flex justify-between items-center mb-3 text-sm">
                <span class="text-gray-500">KDV</span>
                <span class="font-medium" x-text="vatTotal.toFixed(2) + ' ₺'"></span>
            </div>
            <div class="flex justify-between items-center mb-4 text-lg font-bold">
                <span class="text-gray-900">Toplam</span>
                <span class="text-green-600" x-text="grandTotal.toFixed(2) + ' ₺'"></span>
            </div>
        </div>

        {{-- Sipariş Oluştur Butonu --}}
        <div class="px-4 py-3 safe-area-bottom">
            <button @click="submitOrder()" :disabled="submitting"
                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-4 rounded-xl font-bold text-lg shadow-md active:scale-[0.98] transition-transform disabled:opacity-50">
                <i class="fas fa-check-circle mr-2" x-show="!submitting"></i>
                <i class="fas fa-spinner fa-spin mr-2" x-show="submitting"></i>
                <span x-text="submitting ? 'İşleniyor...' : 'Siparişi Tamamla (' + grandTotal.toFixed(2) + ' ₺)'"></span>
            </button>
        </div>
    </div>

    {{-- Sipariş Başarılı Modal --}}
    <div x-show="orderCompleted" x-transition class="fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full text-center" @click.outside="orderCompleted = false">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-500 text-4xl"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900 mb-2">Sipariş Oluşturuldu!</h2>
            <p class="text-gray-500 mb-1">Fiş No: <strong x-text="completedOrderReceipt"></strong></p>
            <p class="text-2xl font-bold text-green-600 mb-6" x-text="completedOrderTotal + ' ₺'"></p>

            <div class="space-y-3">
                <button @click="newOrder()" class="w-full bg-green-500 text-white py-3 rounded-xl font-bold active:bg-green-600">
                    <i class="fas fa-plus mr-2"></i> Yeni Sipariş
                </button>
                <a href="{{ route('mobile.index') }}" class="block w-full bg-gray-100 text-gray-700 py-3 rounded-xl font-bold text-center">
                    <i class="fas fa-home mr-2"></i> Ana Sayfa
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.safe-area-bottom { padding-bottom: env(safe-area-inset-bottom, 0); }
</style>

@push('scripts')
<script>
function quickOrderApp() {
    return {
        cart: [],
        cartOpen: false,
        searchQuery: '',
        searchResults: [],
        showSearch: false,
        filterCategory: '',
        selectedCustomer: '',
        paymentMethod: 'cash',
        submitting: false,
        orderCompleted: false,
        completedOrderReceipt: '',
        completedOrderTotal: '',

        init() {
            // Session storage'dan sepeti yükle (barkod taramadan gelen ürünler)
            const savedCart = sessionStorage.getItem('mobileCart');
            if (savedCart) {
                this.cart = JSON.parse(savedCart);
                sessionStorage.removeItem('mobileCart');
                if (this.cart.length > 0) {
                    this.cartOpen = true;
                }
            }
        },

        get totalItems() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        get vatTotal() {
            return this.cart.reduce((sum, item) => {
                const itemTotal = item.price * item.quantity;
                return sum + (itemTotal * (item.vat_rate || 0) / 100);
            }, 0);
        },

        get grandTotal() {
            return this.subtotal + this.vatTotal;
        },

        addToCart(product) {
            const existing = this.cart.find(item => item.id === product.id);
            if (existing) {
                existing.quantity += 1;
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    barcode: product.barcode,
                    price: parseFloat(product.sale_price),
                    vat_rate: parseFloat(product.vat_rate || 0),
                    quantity: 1
                });
            }
            this.showSearch = false;
            this.searchQuery = '';

            // Kısa titreşim feedback
            if (navigator.vibrate) navigator.vibrate(50);
        },

        incrementItem(index) {
            this.cart[index].quantity += 1;
        },

        decrementItem(index) {
            if (this.cart[index].quantity <= 1) {
                this.cart.splice(index, 1);
                if (this.cart.length === 0) {
                    this.cartOpen = false;
                }
            } else {
                this.cart[index].quantity -= 1;
            }
        },

        async searchProducts() {
            if (!this.searchQuery || this.searchQuery.length < 2) {
                this.showSearch = false;
                return;
            }

            try {
                const response = await fetch('{{ route("mobile.search-products") }}?q=' + encodeURIComponent(this.searchQuery), {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await response.json();
                this.searchResults = data.products || [];
                this.showSearch = true;
            } catch (e) {
                console.error('Arama hatası:', e);
            }
        },

        loadProducts() {
            // Kategori filtresi Alpine tarafında client-side yapılıyor
        },

        async submitOrder() {
            if (this.cart.length === 0 || this.submitting) return;

            this.submitting = true;
            try {
                const items = this.cart.map(item => ({
                    id: item.id,
                    qty: item.quantity,
                    price: item.price,
                    discount: 0
                }));

                const response = await fetch('{{ route("mobile.store-order") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        items: items,
                        customer_id: this.selectedCustomer || null,
                        payment_method: this.paymentMethod,
                        discount_total: 0
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.completedOrderReceipt = data.receipt_no;
                    this.completedOrderTotal = data.total;
                    this.orderCompleted = true;
                    this.cart = [];
                    this.cartOpen = false;

                    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
                } else {
                    let msg = 'Sipariş oluşturulamadı.';
                    if (data.errors) {
                        msg = Object.values(data.errors).flat().join('\n');
                    } else if (data.message) {
                        msg = data.message;
                    }
                    alert(msg);
                }
            } catch (e) {
                console.error('Sipariş hatası:', e);
                alert('Sipariş oluşturulurken hata oluştu.');
            } finally {
                this.submitting = false;
            }
        },

        newOrder() {
            this.orderCompleted = false;
            this.cart = [];
            this.cartOpen = false;
            this.selectedCustomer = '';
            this.paymentMethod = 'cash';
        }
    }
}
</script>
@endpush
@endsection
