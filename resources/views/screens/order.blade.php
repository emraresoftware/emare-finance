<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sipariş Ekranı - Emare Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak]{display:none!important}
        body{-webkit-user-select:none;user-select:none;overflow:hidden;}
        .order-btn{transition:all .12s;} .order-btn:active{transform:scale(0.93);}
        ::-webkit-scrollbar{width:5px;} ::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:9px;}
    </style>
</head>
<body class="h-full bg-amber-50">

<div x-data="orderApp()" class="h-full flex flex-col">

    {{-- Üst Bar --}}
    <header class="bg-white border-b border-amber-100 px-4 py-2.5 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ route('screens.menu') }}" class="w-9 h-9 bg-amber-50 hover:bg-amber-100 rounded-lg flex items-center justify-center transition">
                <i class="fas fa-th-large text-amber-600"></i>
            </a>
            <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-utensils text-white text-sm"></i>
            </div>
            <span class="font-semibold text-gray-800">Sipariş Ekranı</span>
        </div>
        <div class="flex items-center gap-4">
            {{-- Masa Seçimi --}}
            <div class="flex items-center gap-2 bg-amber-50 rounded-lg px-3 py-1.5 border border-amber-200">
                <i class="fas fa-chair text-amber-500"></i>
                <select x-model="selectedTable" class="bg-transparent border-0 text-sm font-medium text-gray-800 focus:ring-0 pr-6">
                    <option value="">Paket</option>
                    <template x-for="t in tables" :key="t">
                        <option :value="t" x-text="'Masa ' + t"></option>
                    </template>
                </select>
            </div>
            <span class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                <span x-text="currentTime"></span>
            </span>
        </div>
    </header>

    {{-- Ana İçerik --}}
    <div class="flex-1 flex overflow-hidden">

        {{-- Sol: Kategoriler --}}
        <div class="w-24 bg-white border-r border-amber-100 flex flex-col overflow-y-auto flex-shrink-0">
            <button @click="activeCategory = null"
                    :class="activeCategory === null ? 'bg-amber-500 text-white' : 'text-gray-600 hover:bg-amber-50'"
                    class="order-btn p-4 text-center border-b border-gray-50 transition">
                <i class="fas fa-border-all text-lg block mb-1"></i>
                <span class="text-[10px] font-medium leading-tight block">Tümü</span>
            </button>
            @foreach($categories as $cat)
            <button @click="activeCategory = {{ $cat->id }}"
                    :class="activeCategory === {{ $cat->id }} ? 'bg-amber-500 text-white' : 'text-gray-600 hover:bg-amber-50'"
                    class="order-btn p-4 text-center border-b border-gray-50 transition">
                <i class="fas fa-tag text-lg block mb-1"></i>
                <span class="text-[10px] font-medium leading-tight block">{{ Str::limit($cat->name, 10) }}</span>
            </button>
            @endforeach
        </div>

        {{-- Orta: Ürünler --}}
        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                @foreach($products as $product)
                <button @click="addItem({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->sale_price }})"
                        x-show="activeCategory === null || activeCategory === {{ $product->category_id ?? 'null' }}"
                        class="order-btn bg-white rounded-2xl p-4 border border-amber-100 hover:border-amber-300 hover:shadow-lg shadow-sm transition-all text-center cursor-pointer group">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-100 to-orange-100 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-105 transition-transform">
                        <i class="fas fa-mug-hot text-amber-500 text-xl"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-800 leading-tight line-clamp-2 mb-2">{{ $product->name }}</p>
                    <p class="text-base font-bold text-amber-600">@money($product->sale_price)</p>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Sağ: Sipariş Özeti --}}
        <div class="w-80 bg-white border-l border-amber-100 flex flex-col flex-shrink-0">

            {{-- Masa/Paket Başlık --}}
            <div class="px-4 py-3 bg-amber-500 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas" :class="selectedTable ? 'fa-chair' : 'fa-bag-shopping'"></i>
                        <span class="font-semibold" x-text="selectedTable ? 'Masa ' + selectedTable : 'Paket Sipariş'"></span>
                    </div>
                    <span class="text-amber-100 text-sm" x-text="order.length + ' kalem'"></span>
                </div>
            </div>

            {{-- Sipariş Kalemleri --}}
            <div class="flex-1 overflow-y-auto px-3 py-2 space-y-1.5">
                <template x-for="(item, idx) in order" :key="idx">
                    <div class="bg-amber-50 rounded-xl px-3 py-2.5 border border-amber-100">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-800 flex-1 truncate" x-text="item.name"></p>
                            <button @click="removeItem(idx)" class="text-red-400 hover:text-red-600 ml-2">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-1.5">
                            <div class="flex items-center gap-1.5">
                                <button @click="decQty(idx)" class="order-btn w-7 h-7 bg-white border border-amber-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-minus text-[10px] text-gray-500"></i>
                                </button>
                                <span class="w-8 text-center text-sm font-bold" x-text="item.qty"></span>
                                <button @click="incQty(idx)" class="order-btn w-7 h-7 bg-white border border-amber-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-plus text-[10px] text-gray-500"></i>
                                </button>
                            </div>
                            <span class="text-sm font-bold text-amber-700" x-text="formatMoney(item.price * item.qty)"></span>
                        </div>
                        {{-- Not --}}
                        <div class="mt-2">
                            <input type="text" x-model="item.note" placeholder="Not ekle..."
                                   class="w-full bg-white border border-amber-100 rounded-lg px-2 py-1 text-xs text-gray-600 placeholder:text-gray-400 focus:ring-1 focus:ring-amber-300">
                        </div>
                    </div>
                </template>
                <div x-show="order.length === 0" class="flex flex-col items-center justify-center h-full text-amber-300">
                    <i class="fas fa-clipboard-list text-5xl mb-3 opacity-50"></i>
                    <p class="text-sm text-gray-400">Henüz sipariş yok</p>
                </div>
            </div>

            {{-- Toplam --}}
            <div class="px-4 py-3 border-t border-amber-100 bg-amber-50">
                <div class="flex justify-between text-lg font-bold text-gray-900">
                    <span>Toplam</span>
                    <span class="text-amber-700" x-text="formatMoney(total)"></span>
                </div>
            </div>

            {{-- Alt Butonlar --}}
            <div class="p-3 space-y-2">
                <button @click="sendToKitchen()" :disabled="order.length === 0"
                        class="order-btn w-full py-3.5 bg-amber-500 hover:bg-amber-600 disabled:bg-gray-200 disabled:text-gray-400 text-white rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2">
                    <i class="fas fa-fire-burner"></i>
                    Mutfağa Gönder
                </button>
                <div class="grid grid-cols-3 gap-2">
                    <button @click="payOrder()" :disabled="order.length === 0"
                            class="order-btn py-2.5 bg-emerald-500 hover:bg-emerald-600 disabled:bg-gray-200 disabled:text-gray-400 text-white rounded-xl text-xs font-medium transition">
                        <i class="fas fa-check mr-1"></i> Öde
                    </button>
                    <button @click="printOrder()"
                            class="order-btn py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-medium transition">
                        <i class="fas fa-print mr-1"></i> Yazdır
                    </button>
                    <button @click="cancelOrder()"
                            class="order-btn py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-medium transition">
                        <i class="fas fa-xmark mr-1"></i> İptal
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function orderApp() {
    return {
        order: [],
        activeCategory: null,
        selectedTable: '',
        tables: Array.from({length: 20}, (_, i) => i + 1),
        currentTime: '',

        init() {
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
        },

        updateTime() {
            this.currentTime = new Date().toLocaleTimeString('tr-TR', {hour:'2-digit', minute:'2-digit'});
        },

        addItem(id, name, price) {
            const existing = this.order.find(i => i.id === id);
            if (existing) { existing.qty++; }
            else { this.order.push({ id, name, price: parseFloat(price), qty: 1, note: '' }); }
        },
        removeItem(idx) { this.order.splice(idx, 1); },
        incQty(idx) { this.order[idx].qty++; },
        decQty(idx) {
            if (this.order[idx].qty > 1) this.order[idx].qty--;
            else this.removeItem(idx);
        },

        get total() { return this.order.reduce((sum, i) => sum + (i.price * i.qty), 0); },
        formatMoney(v) { return new Intl.NumberFormat('tr-TR', {style:'currency', currency:'TRY'}).format(v); },

        sendToKitchen() {
            if (!this.order.length) return;
            const table = this.selectedTable ? `Masa ${this.selectedTable}` : 'Paket';
            alert(`${table} siparişi mutfağa gönderildi! (${this.order.length} kalem)`);
        },
        payOrder() {
            if (!this.order.length) return;
            alert(`Ödeme: ${this.formatMoney(this.total)}\nSipariş tamamlandı!`);
            this.order = [];
        },
        printOrder() { alert('Sipariş fişi yazdırılıyor...'); },
        cancelOrder() { this.order = []; },
    };
}
</script>

</body>
</html>
