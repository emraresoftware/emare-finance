<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>El Terminali - Emare Finance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak]{display:none!important}
        body{-webkit-user-select:none;user-select:none;}
        .term-btn{transition:all .12s;} .term-btn:active{transform:scale(0.92);}
        input::-webkit-outer-spin-button,input::-webkit-inner-spin-button{-webkit-appearance:none;margin:0;}
    </style>
</head>
<body class="h-full bg-gradient-to-b from-slate-900 to-cyan-950">

<div x-data="terminalApp()" x-cloak class="h-full flex flex-col max-w-md mx-auto">

    {{-- Üst Bar --}}
    <header class="px-4 py-3 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="{{ route('screens.menu') }}" class="w-9 h-9 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition">
                <i class="fas fa-th-large text-cyan-300 text-sm"></i>
            </a>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-cyan-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-mobile-screen text-white text-sm"></i>
                </div>
                <span class="font-semibold text-white">El Terminali</span>
            </div>
        </div>
        <div class="flex items-center gap-2 text-cyan-300 text-sm">
            <i class="fas fa-wifi"></i>
            <i class="fas fa-battery-three-quarters"></i>
            <span x-text="currentTime"></span>
        </div>
    </header>

    {{-- Mod Seçimi --}}
    <div class="px-4 mb-4 flex gap-2">
        <button @click="mode = 'scan'" :class="mode === 'scan' ? 'bg-cyan-500 text-white' : 'bg-white/10 text-cyan-200'"
                class="term-btn flex-1 py-2.5 rounded-xl text-sm font-medium transition">
            <i class="fas fa-barcode mr-1.5"></i> Ürün Sorgula
        </button>
        <button @click="mode = 'count'" :class="mode === 'count' ? 'bg-cyan-500 text-white' : 'bg-white/10 text-cyan-200'"
                class="term-btn flex-1 py-2.5 rounded-xl text-sm font-medium transition">
            <i class="fas fa-boxes-stacked mr-1.5"></i> Stok Sayım
        </button>
        <button @click="mode = 'price'" :class="mode === 'price' ? 'bg-cyan-500 text-white' : 'bg-white/10 text-cyan-200'"
                class="term-btn flex-1 py-2.5 rounded-xl text-sm font-medium transition">
            <i class="fas fa-tag mr-1.5"></i> Fiyat
        </button>
    </div>

    {{-- Barkod Girişi --}}
    <div class="px-4 mb-4">
        <div class="relative">
            <input type="text" x-model="barcode" @keydown.enter="lookupBarcode()"
                   x-ref="barcodeInput"
                   placeholder="Barkod oku veya yaz..."
                   class="w-full bg-white/10 border border-cyan-500/30 rounded-2xl px-5 py-4 text-lg text-white placeholder:text-cyan-300/50 focus:ring-2 focus:ring-cyan-400 focus:border-transparent font-mono">
            <button @click="lookupBarcode()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-cyan-500 hover:bg-cyan-400 rounded-xl flex items-center justify-center transition">
                <i class="fas fa-search text-white"></i>
            </button>
        </div>
    </div>

    {{-- Büyük Tarama Butonu --}}
    <div x-show="!product && mode !== 'count'" class="px-4 flex-1 flex items-center justify-center">
        <button @click="$refs.barcodeInput.focus()" class="term-btn w-56 h-56 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-3xl flex flex-col items-center justify-center shadow-2xl shadow-cyan-500/30">
            <i class="fas fa-barcode text-6xl text-white mb-4"></i>
            <span class="text-white text-lg font-semibold">Barkod Tara</span>
            <span class="text-cyan-200 text-sm mt-1">veya elle girin</span>
        </button>
    </div>

    {{-- ÜRÜN SORGULA MODU --}}
    <div x-show="product && mode === 'scan'" class="px-4 flex-1 overflow-y-auto" x-cloak>
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-5 border border-cyan-500/20 mb-4">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-cube text-3xl text-white"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-white" x-text="product.name"></h3>
                    <p class="text-sm text-cyan-300 mt-0.5" x-text="'Barkod: ' + product.barcode"></p>
                    <p class="text-sm text-cyan-300/70 mt-0.5" x-text="'Kategori: ' + (product.category || '-')"></p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-5">
                <div class="bg-white/10 rounded-xl p-3 text-center">
                    <p class="text-[10px] text-cyan-300 uppercase tracking-wider mb-1">Satış Fiyatı</p>
                    <p class="text-xl font-bold text-emerald-400" x-text="formatMoney(product.sale_price)"></p>
                </div>
                <div class="bg-white/10 rounded-xl p-3 text-center">
                    <p class="text-[10px] text-cyan-300 uppercase tracking-wider mb-1">Alış Fiyatı</p>
                    <p class="text-xl font-bold text-amber-400" x-text="formatMoney(product.purchase_price)"></p>
                </div>
                <div class="bg-white/10 rounded-xl p-3 text-center">
                    <p class="text-[10px] text-cyan-300 uppercase tracking-wider mb-1">Stok</p>
                    <p class="text-xl font-bold" :class="product.stock > 0 ? 'text-green-400' : 'text-red-400'" x-text="product.stock + ' ' + (product.unit || 'ad')"></p>
                </div>
                <div class="bg-white/10 rounded-xl p-3 text-center">
                    <p class="text-[10px] text-cyan-300 uppercase tracking-wider mb-1">KDV</p>
                    <p class="text-xl font-bold text-cyan-300" x-text="'%' + product.tax_rate"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- STOK SAYIM MODU --}}
    <div x-show="mode === 'count'" class="px-4 flex-1 overflow-y-auto" x-cloak>
        {{-- Sayım Listesi --}}
        <div class="space-y-2 mb-4">
            <template x-for="(item, idx) in countList" :key="idx">
                <div class="bg-white/10 rounded-xl px-4 py-3 border border-cyan-500/20 flex items-center justify-between">
                    <div class="flex-1 min-w-0 mr-3">
                        <p class="text-sm font-medium text-white truncate" x-text="item.name"></p>
                        <p class="text-xs text-cyan-300/70" x-text="item.barcode"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="item.count > 0 && item.count--" class="term-btn w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-minus text-xs text-cyan-300"></i>
                        </button>
                        <input type="number" x-model.number="item.count" class="w-16 bg-white/10 border border-cyan-500/30 rounded-lg text-center text-white text-sm py-1.5 focus:ring-1 focus:ring-cyan-400">
                        <button @click="item.count++" class="term-btn w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center">
                            <i class="fas fa-plus text-xs text-cyan-300"></i>
                        </button>
                        <button @click="countList.splice(idx, 1)" class="term-btn w-8 h-8 bg-red-500/20 rounded-lg flex items-center justify-center ml-1">
                            <i class="fas fa-trash text-xs text-red-400"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="countList.length === 0" class="flex flex-col items-center justify-center py-16 text-cyan-400/40">
            <i class="fas fa-clipboard-list text-5xl mb-3"></i>
            <p class="text-sm text-cyan-300/50">Barkod tarayarak ürün ekleyin</p>
        </div>

        {{-- Sayım Özeti --}}
        <div x-show="countList.length > 0" class="bg-white/10 rounded-xl p-4 border border-cyan-500/20 mb-4">
            <div class="flex justify-between text-sm text-cyan-300 mb-2">
                <span>Toplam Ürün Çeşidi:</span>
                <span class="font-bold text-white" x-text="countList.length"></span>
            </div>
            <div class="flex justify-between text-sm text-cyan-300">
                <span>Toplam Adet:</span>
                <span class="font-bold text-white" x-text="countList.reduce((s,i) => s + i.count, 0)"></span>
            </div>
        </div>
    </div>

    {{-- FİYAT MODU --}}
    <div x-show="product && mode === 'price'" class="px-4 flex-1 flex items-center justify-center" x-cloak>
        <div class="text-center">
            <p class="text-cyan-300 text-sm mb-2" x-text="product.name"></p>
            <p class="text-6xl font-black text-white" x-text="formatMoney(product.sale_price)"></p>
            <div class="mt-4 inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-2">
                <span class="w-2.5 h-2.5 rounded-full" :class="product.stock > 0 ? 'bg-green-400' : 'bg-red-400'"></span>
                <span class="text-sm text-cyan-200" x-text="product.stock > 0 ? 'Stokta var (' + product.stock + ')' : 'Stokta yok'"></span>
            </div>
        </div>
    </div>

    {{-- Alt Bar --}}
    <div class="px-4 py-3 flex gap-2 flex-shrink-0">
        <template x-if="mode === 'count'">
            <button @click="submitCount()" :disabled="countList.length === 0"
                    class="term-btn flex-1 py-3.5 bg-emerald-500 hover:bg-emerald-400 disabled:bg-white/10 disabled:text-white/30 text-white rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2">
                <i class="fas fa-cloud-arrow-up"></i> Sayımı Kaydet
            </button>
        </template>
        <button @click="resetProduct()" class="term-btn py-3.5 px-5 bg-white/10 hover:bg-white/15 text-cyan-300 rounded-xl text-sm font-medium transition">
            <i class="fas fa-rotate-left"></i>
        </button>
        <a href="{{ route('dashboard') }}" class="term-btn py-3.5 px-5 bg-white/10 hover:bg-white/15 text-cyan-300 rounded-xl text-sm font-medium transition flex items-center">
            <i class="fas fa-right-from-bracket"></i>
        </a>
    </div>

</div>

<script>
function terminalApp() {
    return {
        mode: 'scan',
        barcode: '',
        product: null,
        countList: [],
        currentTime: '',

        // Demo ürün havuzu
        demoProducts: [
            { id: 1, barcode: '8690000000001', name: 'Coca Cola 330ml', category: 'İçecek', sale_price: 35.00, purchase_price: 25.00, stock: 120, unit: 'ad', tax_rate: 10 },
            { id: 2, barcode: '8690000000002', name: 'Simit', category: 'Fırın', sale_price: 15.00, purchase_price: 8.00, stock: 45, unit: 'ad', tax_rate: 1 },
            { id: 3, barcode: '8690000000003', name: 'Çay Bardak', category: 'İçecek', sale_price: 20.00, purchase_price: 5.00, stock: 0, unit: 'ad', tax_rate: 10 },
            { id: 4, barcode: '8690000000004', name: 'Tost (Kaşarlı)', category: 'Yiyecek', sale_price: 65.00, purchase_price: 35.00, stock: 30, unit: 'ad', tax_rate: 10 },
            { id: 5, barcode: '8690000000005', name: 'Su 500ml', category: 'İçecek', sale_price: 10.00, purchase_price: 4.00, stock: 200, unit: 'ad', tax_rate: 1 },
        ],

        init() {
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
            this.$nextTick(() => this.$refs.barcodeInput.focus());
        },

        updateTime() {
            this.currentTime = new Date().toLocaleTimeString('tr-TR', {hour:'2-digit', minute:'2-digit'});
        },

        lookupBarcode() {
            if (!this.barcode.trim()) return;

            // Demo: barkodla eşleştir veya rastgele ürün göster
            let found = this.demoProducts.find(p => p.barcode === this.barcode.trim());
            if (!found) {
                // Herhangi bir giriş için demo ürün
                const idx = Math.abs(this.hashCode(this.barcode)) % this.demoProducts.length;
                found = { ...this.demoProducts[idx], barcode: this.barcode.trim() };
            }

            this.product = found;

            if (this.mode === 'count') {
                const existing = this.countList.find(i => i.barcode === found.barcode);
                if (existing) { existing.count++; }
                else { this.countList.push({ ...found, count: 1 }); }
            }

            this.barcode = '';
            this.$refs.barcodeInput.focus();
        },

        hashCode(str) {
            let hash = 0;
            for (let i = 0; i < str.length; i++) {
                hash = ((hash << 5) - hash) + str.charCodeAt(i);
                hash |= 0;
            }
            return hash;
        },

        resetProduct() {
            this.product = null;
            this.barcode = '';
            this.$refs.barcodeInput.focus();
        },

        submitCount() {
            alert(`${this.countList.length} ürün, ${this.countList.reduce((s,i)=>s+i.count,0)} adet sayım kaydedildi!`);
            this.countList = [];
        },

        formatMoney(v) {
            return new Intl.NumberFormat('tr-TR', {style:'currency', currency:'TRY'}).format(v);
        },
    };
}
</script>

</body>
</html>
