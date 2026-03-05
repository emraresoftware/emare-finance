<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Ekranı - Emare Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3/dist/JsBarcode.all.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak]{display:none!important}
        body{-webkit-user-select:none;user-select:none;overflow:hidden;}
        .pos-btn{transition:all .15s;} .pos-btn:active{transform:scale(0.95);}
        .product-card:active{transform:scale(0.96);}
        ::-webkit-scrollbar{width:6px;} ::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:9px;}
    </style>
</head>
<body class="h-full bg-gray-100">

<div x-data="posApp()" class="h-full flex flex-col">

    {{-- Üst Bar --}}
    <header class="bg-white border-b border-gray-200 px-4 py-2 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ route('screens.menu') }}" class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition">
                <i class="fas fa-th-large text-gray-600"></i>
            </a>
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-xs">EF</span>
            </div>
            <span class="font-semibold text-gray-800">POS Ekranı</span>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" x-model="search" @keydown.enter="searchBarcode()"
                       placeholder="Barkod oku veya ürün ara..."
                       class="pl-9 pr-4 py-2 w-72 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400"
                       x-ref="searchInput">
            </div>
            <span class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                <span x-text="currentTime"></span>
            </span>
            <a href="{{ route('dashboard') }}" class="text-xs text-gray-400 hover:text-gray-600">
                <i class="fas fa-arrow-right-from-bracket"></i>
            </a>
        </div>
    </header>

    {{-- Ana İçerik --}}
    <div class="flex-1 flex overflow-hidden">

        {{-- Sol: Ürünler --}}
        <div class="flex-1 flex flex-col bg-gray-50">

            {{-- Kategori Tabları --}}
            <div class="flex items-center gap-1 px-4 py-2 bg-white border-b overflow-x-auto flex-shrink-0">
                <button @click="activeCategory = null" :class="activeCategory === null ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="pos-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                    Tümü
                </button>
                @foreach($categories as $cat)
                <button @click="activeCategory = {{ $cat->id }}"
                        :class="activeCategory === {{ $cat->id }} ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="pos-btn px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>

            {{-- Ürün Grid --}}
            <div class="flex-1 overflow-y-auto p-3">
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-2">
                    @foreach($products as $product)
                    <button @click="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->sale_price }}, '{{ $product->barcode }}')"
                            x-show="(activeCategory === null || activeCategory === {{ $product->category_id ?? 'null' }}) && matchSearch('{{ strtolower(addslashes($product->name)) }}', '{{ $product->barcode }}')"
                            class="product-card bg-white rounded-xl p-3 border border-gray-100 hover:border-indigo-200 hover:shadow-md transition-all text-center cursor-pointer">
                        <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-cube text-indigo-400"></i>
                        </div>
                        <p class="text-xs font-medium text-gray-800 leading-tight line-clamp-2 mb-1">{{ $product->name }}</p>
                        <p class="text-sm font-bold text-indigo-600">@money($product->sale_price)</p>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sağ: Sepet --}}
        <div class="w-96 bg-white border-l border-gray-200 flex flex-col flex-shrink-0">

            {{-- Müşteri Seçimi --}}
            <div class="px-4 py-3 border-b bg-gray-50">
                <select x-model="selectedCustomer" class="w-full rounded-lg border-gray-200 text-sm py-2">
                    <option value="">Genel Müşteri</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Sepet Öğeleri --}}
            <div class="flex-1 overflow-y-auto px-3 py-2 space-y-1">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2 group">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate" x-text="item.name"></p>
                            <p class="text-xs text-gray-500">
                                <span x-text="formatMoney(item.price)"></span> × <span x-text="item.qty"></span>
                            </p>
                        </div>
                        <div class="flex items-center gap-1">
                            <button @click="decreaseQty(index)" class="pos-btn w-7 h-7 bg-gray-200 hover:bg-red-100 rounded-md flex items-center justify-center text-xs">
                                <i class="fas fa-minus text-gray-600"></i>
                            </button>
                            <span class="w-8 text-center text-sm font-semibold" x-text="item.qty"></span>
                            <button @click="increaseQty(index)" class="pos-btn w-7 h-7 bg-gray-200 hover:bg-green-100 rounded-md flex items-center justify-center text-xs">
                                <i class="fas fa-plus text-gray-600"></i>
                            </button>
                        </div>
                        <span class="text-sm font-bold text-gray-800 w-20 text-right" x-text="formatMoney(item.price * item.qty)"></span>
                        <button @click="removeFromCart(index)" class="pos-btn w-7 h-7 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-md flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </template>
                <div x-show="cart.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400">
                    <i class="fas fa-shopping-basket text-4xl mb-3 opacity-50"></i>
                    <p class="text-sm">Sepet boş</p>
                    <p class="text-xs mt-1">Ürüne tıklayarak ekleyin</p>
                </div>
            </div>

            {{-- İndirim --}}
            <div class="px-4 py-2 border-t bg-gray-50" x-show="cart.length > 0">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 w-16">İndirim:</span>
                    <input type="number" x-model.number="discount" min="0" step="0.01"
                           class="flex-1 rounded-lg border-gray-200 text-sm py-1.5 text-right" placeholder="0,00">
                    <select x-model="discountType" class="rounded-lg border-gray-200 text-xs py-1.5 w-16">
                        <option value="tl">₺</option>
                        <option value="pct">%</option>
                    </select>
                </div>
            </div>

            {{-- Toplam --}}
            <div class="px-4 py-3 border-t bg-indigo-50">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Ara Toplam</span>
                    <span x-text="formatMoney(subtotal)"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 mb-1" x-show="discountAmount > 0">
                    <span>İndirim</span>
                    <span class="text-red-500" x-text="'-' + formatMoney(discountAmount)"></span>
                </div>
                <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-indigo-200">
                    <span>TOPLAM</span>
                    <span class="text-indigo-700" x-text="formatMoney(grandTotal)"></span>
                </div>
            </div>

            {{-- Hızlı Ödeme Butonları --}}
            <div class="p-3 border-t bg-white grid grid-cols-3 gap-2">
                <button @click="quickPay('cash')" :disabled="cart.length === 0"
                        class="pos-btn py-3 bg-emerald-500 hover:bg-emerald-600 disabled:bg-gray-200 disabled:text-gray-400 text-white rounded-xl font-medium text-sm transition">
                    <i class="fas fa-money-bill-wave block text-lg mb-1"></i> Nakit
                </button>
                <button @click="quickPay('credit_card')" :disabled="cart.length === 0"
                        class="pos-btn py-3 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-200 disabled:text-gray-400 text-white rounded-xl font-medium text-sm transition">
                    <i class="fas fa-credit-card block text-lg mb-1"></i> Kart
                </button>
                <button @click="openPaymentModal()" :disabled="cart.length === 0"
                        class="pos-btn py-3 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-200 disabled:text-gray-400 text-white rounded-xl font-medium text-sm transition">
                    <i class="fas fa-wallet block text-lg mb-1"></i> Diğer
                </button>
            </div>

            {{-- Alt Araçlar --}}
            <div class="p-3 pt-0 grid grid-cols-4 gap-1.5">
                <button @click="clearCart()" class="pos-btn py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-medium transition">
                    <i class="fas fa-trash-can mr-1"></i> Temizle
                </button>
                <button @click="holdSale()" class="pos-btn py-2 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg text-xs font-medium transition">
                    <i class="fas fa-pause mr-1"></i> Beklet
                </button>
                <button @click="printReceipt()" class="pos-btn py-2 bg-gray-50 text-gray-600 hover:bg-gray-100 rounded-lg text-xs font-medium transition">
                    <i class="fas fa-print mr-1"></i> Yazdır
                </button>
                <button @click="openDrawer()" class="pos-btn py-2 bg-gray-50 text-gray-600 hover:bg-gray-100 rounded-lg text-xs font-medium transition">
                    <i class="fas fa-cash-register mr-1"></i> Çekmece
                </button>
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════ ÖDEME MODAL ═══════════════════ --}}
<div x-show="paymentModal" x-cloak
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm"
     @keydown.escape.window="paymentModal = false">

    <div class="bg-white rounded-2xl shadow-2xl w-[640px] max-h-[90vh] overflow-hidden" @click.away="paymentModal = false">

        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-white">Ödeme Yöntemi Seçin</h3>
                <p class="text-sm text-indigo-200">Toplam: <span class="font-bold text-white" x-text="formatMoney(grandTotal)"></span></p>
            </div>
            <button @click="paymentModal = false" class="w-9 h-9 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center text-white transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Ödeme Yöntemleri Grid --}}
        <div class="p-6">
            <div class="grid grid-cols-4 gap-3">

                {{-- Nakit --}}
                <button @click="confirmPayment('cash')"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg"
                        :class="selectedPaymentMethod === 'cash' ? 'border-emerald-500 bg-emerald-50 shadow-emerald-100' : 'border-gray-200 hover:border-emerald-300'">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-2">
                        <i class="fas fa-money-bill-wave text-emerald-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Nakit</span>
                </button>

                {{-- Kredi Kartı --}}
                <button @click="confirmPayment('credit_card')"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg"
                        :class="selectedPaymentMethod === 'credit_card' ? 'border-blue-500 bg-blue-50 shadow-blue-100' : 'border-gray-200 hover:border-blue-300'">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mb-2">
                        <i class="fas fa-credit-card text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Kredi Kartı</span>
                </button>

                {{-- Veresiye --}}
                <button @click="confirmPayment('veresiye')"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg"
                        :class="selectedPaymentMethod === 'veresiye' ? 'border-amber-500 bg-amber-50 shadow-amber-100' : 'border-gray-200 hover:border-amber-300'">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mb-2">
                        <i class="fas fa-handshake text-amber-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Veresiye</span>
                </button>

                {{-- Havale --}}
                <button @click="confirmPayment('havale')"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg"
                        :class="selectedPaymentMethod === 'havale' ? 'border-cyan-500 bg-cyan-50 shadow-cyan-100' : 'border-gray-200 hover:border-cyan-300'">
                    <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center mb-2">
                        <i class="fas fa-building-columns text-cyan-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Havale</span>
                </button>

                {{-- EFT --}}
                <button @click="confirmPayment('eft')"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg"
                        :class="selectedPaymentMethod === 'eft' ? 'border-sky-500 bg-sky-50 shadow-sky-100' : 'border-gray-200 hover:border-sky-300'">
                    <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center mb-2">
                        <i class="fas fa-right-left text-sky-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">EFT</span>
                </button>

                {{-- Sanal POS --}}
                <button @click="confirmPayment('sanal_pos')"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg"
                        :class="selectedPaymentMethod === 'sanal_pos' ? 'border-violet-500 bg-violet-50 shadow-violet-100' : 'border-gray-200 hover:border-violet-300'">
                    <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center mb-2">
                        <i class="fas fa-globe text-violet-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Sanal POS</span>
                </button>

                {{-- POS --}}
                <button @click="confirmPayment('pos')"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg"
                        :class="selectedPaymentMethod === 'pos' ? 'border-indigo-500 bg-indigo-50 shadow-indigo-100' : 'border-gray-200 hover:border-indigo-300'">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center mb-2">
                        <i class="fas fa-cash-register text-indigo-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">POS</span>
                </button>

                {{-- Yemek Kartı --}}
                <button @click="confirmPayment('yemek_karti')"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg"
                        :class="selectedPaymentMethod === 'yemek_karti' ? 'border-orange-500 bg-orange-50 shadow-orange-100' : 'border-gray-200 hover:border-orange-300'">
                    <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center mb-2">
                        <i class="fas fa-utensils text-orange-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Yemek Kartı</span>
                </button>

                {{-- Setcard --}}
                <button @click="confirmPayment('setcard')"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg"
                        :class="selectedPaymentMethod === 'setcard' ? 'border-rose-500 bg-rose-50 shadow-rose-100' : 'border-gray-200 hover:border-rose-300'">
                    <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center mb-2">
                        <i class="fas fa-id-card text-rose-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Setcard</span>
                </button>

                {{-- Pluspay --}}
                <button @click="confirmPayment('pluspay')"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg"
                        :class="selectedPaymentMethod === 'pluspay' ? 'border-teal-500 bg-teal-50 shadow-teal-100' : 'border-gray-200 hover:border-teal-300'">
                    <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center mb-2">
                        <i class="fas fa-plus-circle text-teal-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Pluspay</span>
                </button>

                {{-- Çoklu / Karışık --}}
                <button @click="openSplitPayment()"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg border-gray-200 hover:border-purple-300">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center mb-2">
                        <i class="fas fa-layer-group text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Çoklu Ödeme</span>
                </button>

                {{-- Diğer --}}
                <button @click="confirmPayment('other')"
                        class="pos-btn flex flex-col items-center p-4 rounded-xl border-2 transition-all hover:shadow-lg border-gray-200 hover:border-gray-400">
                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center mb-2">
                        <i class="fas fa-ellipsis text-gray-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-gray-700">Diğer</span>
                </button>

            </div>
        </div>

        {{-- Çoklu Ödeme Bölümü --}}
        <div x-show="splitPaymentOpen" x-transition class="px-6 pb-4 border-t">
            <h4 class="text-sm font-bold text-gray-800 mt-4 mb-3"><i class="fas fa-layer-group mr-1.5 text-purple-500"></i> Çoklu Ödeme</h4>
            <div class="space-y-2 max-h-40 overflow-y-auto">
                <template x-for="(split, idx) in splitPayments" :key="idx">
                    <div class="flex items-center gap-2">
                        <select x-model="split.method" class="flex-1 rounded-lg border-gray-200 text-xs py-2">
                            <option value="cash">Nakit</option>
                            <option value="credit_card">Kredi Kartı</option>
                            <option value="veresiye">Veresiye</option>
                            <option value="havale">Havale</option>
                            <option value="eft">EFT</option>
                            <option value="sanal_pos">Sanal POS</option>
                            <option value="pos">POS</option>
                            <option value="yemek_karti">Yemek Kartı</option>
                            <option value="setcard">Setcard</option>
                            <option value="pluspay">Pluspay</option>
                        </select>
                        <input type="number" x-model.number="split.amount" min="0" step="0.01"
                               class="w-32 rounded-lg border-gray-200 text-sm py-2 text-right" placeholder="₺ Tutar">
                        <button @click="splitPayments.splice(idx, 1)" class="w-8 h-8 text-red-400 hover:text-red-600 rounded flex items-center justify-center" x-show="splitPayments.length > 1">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </template>
            </div>
            <div class="flex items-center justify-between mt-3">
                <button @click="splitPayments.push({method:'cash', amount:0})" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-plus mr-1"></i> Satır Ekle
                </button>
                <div class="text-xs text-gray-500">
                    Kalan: <span class="font-bold" :class="splitRemaining < 0 ? 'text-red-600' : (splitRemaining === 0 ? 'text-green-600' : 'text-amber-600')" x-text="formatMoney(splitRemaining)"></span>
                </div>
            </div>
            <button @click="completeSplitPayment()" :disabled="Math.abs(splitRemaining) > 0.01"
                    class="w-full mt-3 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:from-purple-700 hover:to-indigo-700 transition-all">
                <i class="fas fa-check mr-1.5"></i> Çoklu Ödemeyi Tamamla
            </button>
        </div>

        {{-- Nakit Ödeme Detay --}}
        <div x-show="selectedPaymentMethod === 'cash' && !splitPaymentOpen" x-transition class="px-6 pb-5 border-t">
            <h4 class="text-sm font-bold text-gray-800 mt-4 mb-3"><i class="fas fa-money-bill-wave mr-1.5 text-emerald-500"></i> Nakit Ödeme</h4>
            <div class="flex items-center gap-3 mb-3">
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">Alınan Tutar</label>
                    <input type="number" x-model.number="cashReceived" min="0" step="0.01" x-ref="cashInput"
                           class="w-full rounded-lg border-gray-200 text-lg py-2.5 text-right font-bold focus:ring-2 focus:ring-emerald-400" placeholder="0,00">
                </div>
                <div class="flex-1">
                    <label class="block text-xs text-gray-500 mb-1">Para Üstü</label>
                    <div class="w-full rounded-lg bg-gray-50 border border-gray-200 text-lg py-2.5 text-right font-bold px-3"
                         :class="cashChange >= 0 ? 'text-emerald-600' : 'text-red-600'"
                         x-text="formatMoney(cashChange)"></div>
                </div>
            </div>
            {{-- Hızlı tutar butonları --}}
            <div class="grid grid-cols-5 gap-2 mb-3">
                <template x-for="amt in quickCashAmounts" :key="amt">
                    <button @click="cashReceived = amt" class="pos-btn py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-semibold border border-emerald-200 transition"
                            x-text="'₺' + amt"></button>
                </template>
            </div>
            <button @click="completeCashPayment()" :disabled="cashReceived < grandTotal"
                    class="w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed transition">
                <i class="fas fa-check mr-1.5"></i> Ödemeyi Tamamla
            </button>
        </div>

    </div>
</div>

<script src="/js/hardware-drivers.js"></script>
<script>
function posApp() {
    return {
        cart: [],
        search: '',
        activeCategory: null,
        selectedCustomer: '',
        discount: 0,
        discountType: 'tl',
        currentTime: '',

        // Ödeme Modal
        paymentModal: false,
        selectedPaymentMethod: null,
        splitPaymentOpen: false,
        splitPayments: [{method:'cash', amount:0}, {method:'credit_card', amount:0}],
        cashReceived: 0,

        // Ödeme yöntemi etiketleri
        paymentLabels: {
            cash: 'Nakit', credit_card: 'Kredi Kartı', veresiye: 'Veresiye',
            havale: 'Havale', eft: 'EFT', sanal_pos: 'Sanal POS',
            pos: 'POS', yemek_karti: 'Yemek Kartı', setcard: 'Setcard',
            pluspay: 'Pluspay', other: 'Diğer', mixed: 'Çoklu Ödeme',
        },

        init() {
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
            this.$refs.searchInput?.focus();

            // Klavye kısayolları
            document.addEventListener('keydown', e => {
                if (e.key === 'F1') { e.preventDefault(); this.$refs.searchInput?.focus(); }
                if (e.key === 'F5') { e.preventDefault(); this.quickPay('cash'); }
                if (e.key === 'F6') { e.preventDefault(); this.quickPay('credit_card'); }
                if (e.key === 'F7') { e.preventDefault(); this.openPaymentModal(); }
                if (e.key === 'Escape') {
                    if (this.paymentModal) { this.paymentModal = false; }
                    else { this.search = ''; }
                }
                if (e.key === 'Delete' && e.ctrlKey) { this.clearCart(); }
            });
        },

        updateTime() {
            this.currentTime = new Date().toLocaleTimeString('tr-TR', {hour:'2-digit', minute:'2-digit'});
        },

        matchSearch(name, barcode) {
            if (!this.search) return true;
            const q = this.search.toLowerCase();
            return name.includes(q) || (barcode && barcode.includes(q));
        },

        searchBarcode() {
            const q = this.search.trim().toLowerCase();
            if (!q) return;
            const btn = document.querySelector(`[data-barcode="${q}"]`);
            if (btn) btn.click();
            this.search = '';
        },

        addToCart(id, name, price, barcode) {
            const existing = this.cart.find(i => i.id === id);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({ id, name, price: parseFloat(price), barcode, qty: 1 });
            }
        },

        removeFromCart(index) { this.cart.splice(index, 1); },
        increaseQty(index) { this.cart[index].qty++; },
        decreaseQty(index) {
            if (this.cart[index].qty > 1) this.cart[index].qty--;
            else this.removeFromCart(index);
        },
        clearCart() { this.cart = []; this.discount = 0; },

        get subtotal() { return this.cart.reduce((sum, i) => sum + (i.price * i.qty), 0); },
        get discountAmount() {
            if (this.discountType === 'pct') return this.subtotal * (this.discount / 100);
            return parseFloat(this.discount) || 0;
        },
        get grandTotal() { return Math.max(0, this.subtotal - this.discountAmount); },

        formatMoney(val) { return new Intl.NumberFormat('tr-TR', {style:'currency', currency:'TRY'}).format(val); },

        // ═══ Ödeme Yöntemleri ═══

        // Hızlı ödeme (nakit / kart — direkt modal yok)
        quickPay(method) {
            if (this.cart.length === 0) return;
            if (method === 'cash') {
                this.selectedPaymentMethod = 'cash';
                this.cashReceived = 0;
                this.splitPaymentOpen = false;
                this.paymentModal = true;
                this.$nextTick(() => this.$refs.cashInput?.focus());
            } else {
                this.completeSale(method);
            }
        },

        // Ödeme modal aç
        openPaymentModal() {
            if (this.cart.length === 0) return;
            this.paymentModal = true;
            this.selectedPaymentMethod = null;
            this.splitPaymentOpen = false;
            this.cashReceived = 0;
        },

        // Ödeme yöntemi seç (modaldan)
        confirmPayment(method) {
            this.selectedPaymentMethod = method;
            this.splitPaymentOpen = false;

            if (method === 'cash') {
                this.cashReceived = 0;
                this.$nextTick(() => this.$refs.cashInput?.focus());
            } else if (method === 'veresiye' && !this.selectedCustomer) {
                alert('Veresiye ödeme için lütfen bir müşteri seçin!');
                this.paymentModal = false;
                return;
            } else {
                this.completeSale(method);
            }
        },

        // Nakit ödeme tamamla
        get cashChange() {
            return (this.cashReceived || 0) - this.grandTotal;
        },

        get quickCashAmounts() {
            const total = this.grandTotal;
            const amounts = [10, 20, 50, 100, 200, 500];
            // Toplama yakın yuvarlak tutarlar ekle
            const rounded = Math.ceil(total / 10) * 10;
            const set = new Set([...amounts.filter(a => a >= total), rounded, rounded + 10]);
            return [...set].sort((a, b) => a - b).slice(0, 5);
        },

        completeCashPayment() {
            if (this.cashReceived < this.grandTotal) return;
            const change = this.cashChange;
            this.completeSale('cash', { received: this.cashReceived, change });
        },

        // Çoklu ödeme
        openSplitPayment() {
            this.splitPaymentOpen = true;
            this.selectedPaymentMethod = null;
            this.splitPayments = [
                { method: 'cash', amount: 0 },
                { method: 'credit_card', amount: 0 },
            ];
        },

        get splitRemaining() {
            const paid = this.splitPayments.reduce((sum, s) => sum + (parseFloat(s.amount) || 0), 0);
            return this.grandTotal - paid;
        },

        completeSplitPayment() {
            if (Math.abs(this.splitRemaining) > 0.01) return;
            const details = this.splitPayments.filter(s => s.amount > 0).map(s => ({
                method: s.method,
                label: this.paymentLabels[s.method] || s.method,
                amount: s.amount,
            }));
            this.completeSale('mixed', { splits: details });
        },

        // Satışı tamamla
        completeSale(method, extra = {}) {
            const label = this.paymentLabels[method] || method;
            let msg = `✅ Satış Tamamlandı!\n\nÖdeme: ${label}\nTutar: ${this.formatMoney(this.grandTotal)}`;

            if (extra.received) {
                msg += `\nAlınan: ${this.formatMoney(extra.received)}`;
                msg += `\nPara Üstü: ${this.formatMoney(extra.change)}`;
            }
            if (extra.splits) {
                msg += '\n\n— Ödeme Detayı —';
                extra.splits.forEach(s => {
                    msg += `\n${s.label}: ${this.formatMoney(s.amount)}`;
                });
            }
            if (this.selectedCustomer) {
                msg += `\nMüşteri: #${this.selectedCustomer}`;
            }

            alert(msg);
            this.paymentModal = false;
            this.clearCart();
        },

        holdSale() { if (this.cart.length) alert('Satış beklemeye alındı.'); },
        printReceipt() { alert('Fiş yazdırılıyor...'); },
        openDrawer() { alert('Para çekmecesi açılıyor...'); },
    };
}
</script>

</body>
</html>
