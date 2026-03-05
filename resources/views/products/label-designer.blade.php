@extends('layouts.app')
@section('title', 'Etiket Tasarla & Üret')

@section('content')
<div class="space-y-5" x-data="labelDesigner()">

    {{-- Başlık --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-bold text-gray-900"><i class="fas fa-palette mr-2 text-purple-500"></i>Etiket Tasarla & Üret</h2>
            <p class="text-sm text-gray-500 mt-0.5">Şablon seçin · Ürün ekleyin · Yazdırın</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('products.labels') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">
                <i class="fas fa-tags mr-1"></i> Basit Etiket
            </a>
            <button @click="printToDevice()" class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm hover:bg-teal-700">
                <i class="fas fa-print mr-1"></i> Yazıcıya Gönder
            </button>
            <button @click="printLabels()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-file-alt mr-1"></i> Tarayıcıdan Yazdır
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5">

        {{-- SOL PANEL --}}
        <div class="xl:col-span-4 space-y-4">

            {{-- Şablon Seçimi --}}
            <div class="bg-white rounded-xl border shadow-sm p-4">
                <h4 class="font-semibold text-sm text-gray-700 mb-3"><i class="fas fa-swatchbook text-purple-500 mr-1.5"></i>Etiket Şablonu</h4>
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="tpl in templates" :key="tpl.id">
                        <button type="button" @click="applyTemplate(tpl)"
                                class="relative rounded-xl border-2 p-2 text-left hover:border-indigo-400 transition"
                                :class="settings.template === tpl.id ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200'">
                            <div class="rounded-lg h-14 flex flex-col items-center justify-center mb-1 text-[10px] font-bold overflow-hidden px-1"
                                 :style="'background-color:' + tpl.previewBg + '; color:' + tpl.previewText">
                                <span class="truncate w-full text-center leading-tight" x-text="tpl.previewLine1"></span>
                                <span class="text-base font-black" x-text="tpl.previewLine2"></span>
                                <span class="font-normal opacity-70 text-[8px]" x-text="tpl.previewLine3"></span>
                            </div>
                            <div class="text-xs font-medium text-gray-700 text-center" x-text="tpl.label"></div>
                            <div x-show="settings.template === tpl.id"
                                 class="absolute top-1.5 right-1.5 w-4 h-4 bg-indigo-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white" style="font-size:8px"></i>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            {{-- Renk & Boyut --}}
            <div class="bg-white rounded-xl border shadow-sm p-4">
                <h4 class="font-semibold text-sm text-gray-700 mb-3"><i class="fas fa-fill-drip text-orange-500 mr-1.5"></i>Renk & Boyut</h4>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Renk Paleti</label>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-for="color in colorPalette" :key="color.bg">
                                <button @click="settings.bgColor=color.bg; settings.textColor=color.text; settings.accentColor=color.accent"
                                        class="w-8 h-8 rounded-lg border-2 transition"
                                        :class="settings.bgColor===color.bg ? 'border-gray-800 scale-110' : 'border-transparent'"
                                        :style="'background-color:'+color.bg" :title="color.name"></button>
                            </template>
                            <input type="color" x-model="settings.bgColor" class="w-8 h-8 rounded-lg border-2 border-gray-200 cursor-pointer p-0.5" title="Özel renk">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Boyut</label>
                            <select x-model="settings.size" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs">
                                <option value="tiny">Minik (25×15)</option>
                                <option value="small">Küçük (30×20)</option>
                                <option value="medium">Orta (50×30)</option>
                                <option value="large">Büyük (70×40)</option>
                                <option value="xlarge">XL (90×55)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Sütun</label>
                            <select x-model="settings.columns" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs">
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Adet / Ürün</label>
                            <input type="number" x-model="settings.quantity" min="1" max="200" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Köşe</label>
                            <select x-model="settings.radius" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs">
                                <option value="0px">Kare</option>
                                <option value="4px">Hafif</option>
                                <option value="8px">Orta</option>
                                <option value="12px">Yuvarlak</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Gösterilecek Alanlar</label>
                        <div class="grid grid-cols-2 gap-1">
                            <label class="flex items-center gap-1.5 text-xs cursor-pointer"><input type="checkbox" x-model="settings.showName" class="rounded text-indigo-600"> Ürün Adı</label>
                            <label class="flex items-center gap-1.5 text-xs cursor-pointer"><input type="checkbox" x-model="settings.showPrice" class="rounded text-indigo-600"> Fiyat</label>
                            <label class="flex items-center gap-1.5 text-xs cursor-pointer"><input type="checkbox" x-model="settings.showBarcode" class="rounded text-indigo-600"> Barkod</label>
                            <label class="flex items-center gap-1.5 text-xs cursor-pointer"><input type="checkbox" x-model="settings.showBrand" class="rounded text-indigo-600"> Marka</label>
                            <label class="flex items-center gap-1.5 text-xs cursor-pointer"><input type="checkbox" x-model="settings.showCategory" class="rounded text-indigo-600"> Kategori</label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Başlık / Alt Metin</label>
                        <input type="text" x-model="settings.headerText" placeholder="Üst metin (EMARE MARKET)" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs mb-1.5">
                        <input type="text" x-model="settings.footerText" placeholder="Alt metin (KDV dahildir)" class="w-full border border-gray-300 rounded-lg px-2 py-1.5 text-xs">
                    </div>
                </div>
            </div>

            {{-- Ürün Seçimi --}}
            <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
                    <h4 class="font-semibold text-sm text-gray-700"><i class="fas fa-box text-blue-500 mr-1.5"></i>Ürün Ekle</h4>
                    <span class="text-xs text-gray-400" x-text="selectedProducts.length + ' seçildi'"></span>
                </div>
                <div class="p-3">
                    <form method="GET" class="flex gap-1.5 mb-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ürün adı veya barkod..."
                               class="flex-1 border border-gray-300 rounded-lg px-2 py-1.5 text-xs">
                        <button class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-indigo-700"><i class="fas fa-search"></i></button>
                    </form>
                    <div class="max-h-52 overflow-y-auto divide-y divide-gray-100">
                        @forelse($products as $product)
                        <div class="px-2 py-2 hover:bg-blue-50 cursor-pointer flex items-center justify-between rounded-lg"
                             @click="addProduct({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->barcode }}', {{ $product->sale_price }}, '{{ addslashes($product->brand ?? '') }}', '{{ addslashes(optional($product->category)->name ?? '') }}')">
                            <div class="min-w-0">
                                <div class="text-xs font-medium text-gray-800 truncate">{{ $product->name }}</div>
                                <div class="text-[10px] text-gray-400">{{ $product->barcode ?? 'Barkod yok' }}</div>
                            </div>
                            <div class="text-xs font-bold text-indigo-600 ml-2 whitespace-nowrap">₺{{ number_format($product->sale_price, 2, ',', '.') }}</div>
                        </div>
                        @empty
                        <div class="py-6 text-center text-gray-400 text-xs">
                            <i class="fas fa-search text-xl mb-1 block"></i>Ürün bulmak için arama yapın
                        </div>
                        @endforelse
                    </div>
                    <div x-show="selectedProducts.length > 0" class="mt-2 border-t pt-2">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-600">Seçilenler</span>
                            <button @click="clearSelection()" class="text-xs text-red-500 hover:text-red-700"><i class="fas fa-trash mr-0.5"></i>Temizle</button>
                        </div>
                        <div class="space-y-1 max-h-32 overflow-y-auto">
                            <template x-for="(p, i) in selectedProducts" :key="p.id">
                                <div class="flex items-center gap-1.5 bg-gray-50 rounded px-2 py-1">
                                    <span class="text-[10px] flex-1 truncate font-medium text-gray-700" x-text="p.name"></span>
                                    <button @click="selectedProducts.splice(i,1)" class="text-red-400 hover:text-red-600 flex-shrink-0"><i class="fas fa-times" style="font-size:10px"></i></button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SAĞ PANEL: Önizleme --}}
        <div class="xl:col-span-8">
            <div class="bg-white rounded-xl border shadow-sm overflow-hidden sticky top-4">
                <div class="px-5 py-3.5 border-b flex items-center justify-between bg-gray-50">
                    <h4 class="font-semibold text-sm text-gray-700"><i class="fas fa-eye text-indigo-500 mr-1.5"></i>Etiket Önizleme</h4>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500" x-text="getExpandedLabels().length + ' etiket'"></span>
                        <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer">
                            <input type="checkbox" x-model="showBorder" class="rounded text-indigo-600"> Kesme çizgisi
                        </label>
                    </div>
                </div>
                <div class="p-4 bg-gray-100 min-h-80" id="print-area">
                    {{-- Boş --}}
                    <div x-show="selectedProducts.length === 0 && demo.length === 0"
                         class="flex flex-col items-center justify-center py-16 text-gray-400">
                        <i class="fas fa-tag text-5xl mb-3 text-gray-300"></i>
                        <p class="font-medium text-gray-500">Henüz Ürün Eklenmedi</p>
                        <p class="text-xs mt-1 text-center">Sol panelden ürün seçin veya demo etiketleri görüntüleyin.</p>
                        <button @click="loadDemo()" class="mt-4 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium hover:bg-indigo-200">
                            <i class="fas fa-wand-magic-sparkles mr-1"></i> Demo Etiketleri Göster
                        </button>
                    </div>
                    {{-- Grid --}}
                    <div x-show="selectedProducts.length > 0 || demo.length > 0"
                         class="grid gap-2" :style="'grid-template-columns: repeat('+settings.columns+', minmax(0,1fr))'">
                        <template x-for="(item, idx) in (selectedProducts.length > 0 ? getExpandedLabels() : demo)" :key="idx">
                            <div class="label-cell" :class="showBorder ? 'ring-1 ring-dashed ring-gray-400' : ''"
                                 :style="getLabelStyle()" title="Kaldırmak için tıkla" @click="removeItem(item.id)">

                                {{-- minimalist --}}
                                <template x-if="settings.template === 'minimal'">
                                    <div class="flex flex-col h-full justify-between p-1.5">
                                        <div x-show="settings.showName" class="text-[10px] font-semibold leading-tight" x-text="item.name" :style="'color:'+settings.textColor"></div>
                                        <div x-show="settings.showBrand && item.brand" class="text-[8px] opacity-60" x-text="item.brand" :style="'color:'+settings.textColor"></div>
                                        <div x-show="settings.showPrice" class="text-xs font-black" :style="'color:'+settings.accentColor" x-text="'₺'+fmtPrice(item.price)"></div>
                                        <div x-show="settings.showBarcode && item.barcode"><svg :class="'bc-svg bc-'+idx" :data-val="item.barcode" class="w-full"></svg></div>
                                        <div x-show="settings.footerText" class="text-[7px] opacity-50 text-center" x-text="settings.footerText" :style="'color:'+settings.textColor"></div>
                                    </div>
                                </template>

                                {{-- retail --}}
                                <template x-if="settings.template === 'retail'">
                                    <div class="flex flex-col h-full">
                                        <div x-show="settings.headerText" class="text-center text-[7px] font-bold py-0.5 px-1 truncate"
                                             :style="'background-color:'+settings.accentColor+';color:#fff'" x-text="settings.headerText"></div>
                                        <div class="flex-1 flex flex-col justify-center p-1.5">
                                            <div x-show="settings.showName" class="text-[10px] font-bold text-center leading-tight" x-text="item.name" :style="'color:'+settings.textColor"></div>
                                            <div x-show="settings.showCategory && item.category" class="text-[8px] text-center opacity-60" x-text="item.category" :style="'color:'+settings.textColor"></div>
                                            <div x-show="settings.showBrand && item.brand" class="text-[8px] text-center opacity-50 uppercase tracking-wide" x-text="item.brand" :style="'color:'+settings.textColor"></div>
                                            <div x-show="settings.showPrice" class="text-center mt-0.5">
                                                <span class="text-sm font-black" :style="'color:'+settings.accentColor" x-text="'₺'+fmtPrice(item.price)"></span>
                                            </div>
                                        </div>
                                        <div x-show="settings.showBarcode && item.barcode" class="px-1 pb-1"><svg :class="'bc-svg bc-'+idx" :data-val="item.barcode" class="w-full"></svg></div>
                                    </div>
                                </template>

                                {{-- premium --}}
                                <template x-if="settings.template === 'premium'">
                                    <div class="flex flex-col h-full" :style="'border:2px solid '+settings.accentColor">
                                        <div class="text-center py-0.5 text-[7px] font-bold tracking-widest"
                                             :style="'background-color:'+settings.accentColor+';color:'+settings.bgColor"
                                             x-text="settings.headerText || 'PREMIUM'"></div>
                                        <div class="flex-1 flex flex-col items-center justify-center p-1.5 gap-0.5">
                                            <div x-show="settings.showName" class="text-[10px] font-semibold text-center" x-text="item.name" :style="'color:'+settings.textColor"></div>
                                            <div x-show="settings.showBrand && item.brand" class="text-[8px] tracking-wider uppercase" x-text="item.brand" :style="'color:'+settings.accentColor+';opacity:0.8'"></div>
                                            <div x-show="settings.showPrice" class="text-sm font-black" :style="'color:'+settings.accentColor" x-text="'₺'+fmtPrice(item.price)"></div>
                                        </div>
                                        <div x-show="settings.showBarcode && item.barcode" class="px-1 pb-1"><svg :class="'bc-svg bc-'+idx" :data-val="item.barcode" class="w-full"></svg></div>
                                    </div>
                                </template>

                                {{-- price_tag --}}
                                <template x-if="settings.template === 'price_tag'">
                                    <div class="flex h-full">
                                        <div class="flex-1 flex flex-col justify-center p-1.5">
                                            <div x-show="settings.showName" class="text-[10px] font-bold leading-tight" x-text="item.name" :style="'color:'+settings.textColor"></div>
                                            <div x-show="settings.showBrand && item.brand" class="text-[8px] opacity-60 uppercase tracking-wide mt-0.5" x-text="item.brand" :style="'color:'+settings.textColor"></div>
                                            <div x-show="settings.showCategory && item.category" class="text-[8px] opacity-50 mt-0.5" x-text="item.category" :style="'color:'+settings.textColor"></div>
                                        </div>
                                        <div x-show="settings.showPrice" class="flex flex-col items-center justify-center px-2 min-w-[3.2rem]" :style="'background-color:'+settings.accentColor">
                                            <div class="text-[7px] font-medium text-white opacity-80">FİYAT</div>
                                            <div class="text-sm font-black text-white leading-tight" x-text="'₺'+fmtPrice(item.price)"></div>
                                        </div>
                                    </div>
                                </template>

                                {{-- gida --}}
                                <template x-if="settings.template === 'gida'">
                                    <div class="flex flex-col h-full p-1.5">
                                        <div class="flex items-center justify-between">
                                            <div class="text-[8px] font-bold uppercase tracking-wide" x-text="settings.headerText || 'GIDA'" :style="'color:'+settings.accentColor"></div>
                                            <div x-show="settings.showPrice" class="text-[10px] font-black" :style="'color:'+settings.accentColor" x-text="'₺'+fmtPrice(item.price)"></div>
                                        </div>
                                        <div class="my-0.5 border-t" :style="'border-color:'+settings.accentColor+';opacity:0.3'"></div>
                                        <div x-show="settings.showName" class="text-[10px] font-bold leading-tight" x-text="item.name" :style="'color:'+settings.textColor"></div>
                                        <div x-show="settings.showCategory && item.category" class="text-[8px] opacity-60 mt-0.5" x-text="item.category" :style="'color:'+settings.textColor"></div>
                                        <div class="mt-auto">
                                            <div x-show="settings.showBarcode && item.barcode"><svg :class="'bc-svg bc-'+idx" :data-val="item.barcode" class="w-full"></svg></div>
                                            <div x-show="settings.footerText" class="text-[7px] opacity-40 mt-0.5" x-text="settings.footerText" :style="'color:'+settings.textColor"></div>
                                        </div>
                                    </div>
                                </template>

                                {{-- modern --}}
                                <template x-if="settings.template === 'modern'">
                                    <div class="flex flex-col h-full overflow-hidden">
                                        <div class="h-1 flex-shrink-0" :style="'background-color:'+settings.accentColor"></div>
                                        <div class="flex-1 p-1.5 flex flex-col justify-between">
                                            <div>
                                                <div x-show="settings.showName" class="text-[10px] font-bold" x-text="item.name" :style="'color:'+settings.textColor"></div>
                                                <div x-show="settings.showBrand && item.brand" class="text-[8px] opacity-50 uppercase" x-text="item.brand" :style="'color:'+settings.textColor"></div>
                                            </div>
                                            <div class="flex items-end justify-between mt-1">
                                                <div x-show="settings.showBarcode && item.barcode" class="w-3/5"><svg :class="'bc-svg bc-'+idx" :data-val="item.barcode" class="w-full"></svg></div>
                                                <div x-show="settings.showPrice" class="text-right">
                                                    <div class="text-[7px] opacity-50" :style="'color:'+settings.textColor">FİYAT</div>
                                                    <div class="text-sm font-black" :style="'color:'+settings.accentColor" x-text="'₺'+fmtPrice(item.price)"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- badge --}}
                                <template x-if="settings.template === 'badge'">
                                    <div class="flex flex-col items-center justify-center h-full p-1.5 text-center gap-0.5">
                                        <div x-show="settings.headerText" class="text-[8px] font-bold uppercase tracking-widest" x-text="settings.headerText" :style="'color:'+settings.accentColor"></div>
                                        <div x-show="settings.showName" class="text-[9px] font-semibold leading-tight" x-text="item.name" :style="'color:'+settings.textColor"></div>
                                        <div x-show="settings.showBrand && item.brand" class="text-[8px] opacity-60 uppercase" x-text="item.brand" :style="'color:'+settings.textColor"></div>
                                        <div x-show="settings.showPrice" class="text-sm font-black px-3 py-0.5 rounded-full mt-0.5"
                                             :style="'background-color:'+settings.accentColor+';color:'+settings.bgColor" x-text="'₺'+fmtPrice(item.price)"></div>
                                        <div x-show="settings.footerText" class="text-[7px] opacity-50" x-text="settings.footerText" :style="'color:'+settings.textColor"></div>
                                    </div>
                                </template>

                            </div>
                        </template>
                    </div>
                </div>
                <div x-show="selectedProducts.length > 0 || demo.length > 0"
                     class="px-5 py-2.5 border-t bg-gray-50 flex items-center gap-3 text-xs text-gray-500">
                    <span><i class="fas fa-hand-pointer mr-1"></i>Etikete tıklayarak kaldırabilirsiniz</span>
                    <span class="ml-auto font-medium text-gray-700" x-text="getExpandedLabels().length + ' ya da demo etiket'"></span>
                </div>
            </div>
        </div>

    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<script>
const DEMO_PRODUCTS = [
    { id:-1, name:'Çay 500g',          barcode:'8690526085768', price:89.90,  brand:'Doğadan',  category:'İçecek' },
    { id:-2, name:'Ekmek Kepekli',     barcode:'8694700118207', price:12.50,  brand:'Öz Fırın', category:'Unlu Mamul' },
    { id:-3, name:'Süt 1L',            barcode:'8690526048879', price:34.00,  brand:'Pınar',    category:'Süt Ürünleri' },
    { id:-4, name:'Zeytinyağı 500ml',  barcode:'8690526012740', price:149.90, brand:'Tariş',    category:'Yağ' },
    { id:-5, name:'Domates 1kg',       barcode:'2000001000016', price:18.90,  brand:'',         category:'Sebze' },
    { id:-6, name:'Sigara CODE128',    barcode:'ABC-TEST-001',  price:55.00,  brand:'Testler',  category:'Tütün' },
];

const COLOR_PALETTE = [
    { name:'Beyaz',    bg:'#ffffff', text:'#1a1a1a', accent:'#4f46e5' },
    { name:'Krem',     bg:'#fefce8', text:'#1a1a1a', accent:'#ca8a04' },
    { name:'Açık Gri', bg:'#f3f4f6', text:'#111827', accent:'#374151' },
    { name:'Lacivert', bg:'#1e1b4b', text:'#e0e7ff', accent:'#818cf8' },
    { name:'Kırmızı',  bg:'#fef2f2', text:'#1a1a1a', accent:'#dc2626' },
    { name:'Yeşil',    bg:'#f0fdf4', text:'#1a1a1a', accent:'#16a34a' },
    { name:'Sarı',     bg:'#fefce8', text:'#1a1a1a', accent:'#d97706' },
    { name:'Mor',      bg:'#faf5ff', text:'#1a1a1a', accent:'#9333ea' },
    { name:'Siyah',    bg:'#0f172a', text:'#f8fafc',  accent:'#38bdf8' },
    { name:'Turuncu',  bg:'#fff7ed', text:'#1a1a1a', accent:'#ea580c' },
];

const TEMPLATES = [
    { id:'minimal',   label:'Minimalist', previewBg:'#ffffff', previewText:'#111827', previewLine1:'Ürün Adı', previewLine2:'₺34,90', previewLine3:'8690000000000',
      defaults:{ bgColor:'#ffffff', textColor:'#111827', accentColor:'#4f46e5' } },
    { id:'retail',    label:'Perakende',  previewBg:'#f0fdf4', previewText:'#166534', previewLine1:'EMARE MARKET', previewLine2:'₺59,90', previewLine3:'Kategori',
      defaults:{ bgColor:'#f0fdf4', textColor:'#166534', accentColor:'#16a34a', headerText:'EMARE MARKET' } },
    { id:'premium',   label:'Premium',    previewBg:'#1e1b4b', previewText:'#e0e7ff', previewLine1:'■ PREMIUM ■', previewLine2:'₺249', previewLine3:'MARKA',
      defaults:{ bgColor:'#1e1b4b', textColor:'#e0e7ff', accentColor:'#818cf8' } },
    { id:'price_tag', label:'Fiyat Tag',  previewBg:'#fef2f2', previewText:'#1a1a1a', previewLine1:'Ürün bilgisi', previewLine2:'₺99', previewLine3:'» FİYAT',
      defaults:{ bgColor:'#fef2f2', textColor:'#1a1a1a', accentColor:'#dc2626' } },
    { id:'gida',      label:'Gıda',       previewBg:'#f0fdf4', previewText:'#14532d', previewLine1:'GIDA ETİKETİ', previewLine2:'₺24,90', previewLine3:'Kateg.',
      defaults:{ bgColor:'#f0fdf4', textColor:'#14532d', accentColor:'#15803d', headerText:'TAZE GIDA' } },
    { id:'modern',    label:'Modern',     previewBg:'#ffffff', previewText:'#111827', previewLine1:'Ürün Adı', previewLine2:'₺79,90', previewLine3:'[===]',
      defaults:{ bgColor:'#ffffff', textColor:'#111827', accentColor:'#0ea5e9' } },
    { id:'badge',     label:'Badge/Yaka', previewBg:'#eff6ff', previewText:'#1e40af', previewLine1:'EMARE', previewLine2:'₺45,00', previewLine3:'indirim yok',
      defaults:{ bgColor:'#eff6ff', textColor:'#1e40af', accentColor:'#2563eb' } },
];

const SIZE_MAP = {
    tiny:   { w:'72px',  h:'46px'  },
    small:  { w:'92px',  h:'60px'  },
    medium: { w:'138px', h:'84px'  },
    large:  { w:'192px', h:'112px' },
    xlarge: { w:'250px', h:'152px' },
};

function labelDesigner() {
    return {
        selectedProducts: [],
        demo: [],
        showBorder: true,
        templates: TEMPLATES,
        colorPalette: COLOR_PALETTE,
        settings: {
            template:     'retail',
            size:         'medium',
            columns:      4,
            quantity:     1,
            bgColor:      '#f0fdf4',
            textColor:    '#166534',
            accentColor:  '#16a34a',
            radius:       '8px',
            showName:     true,
            showPrice:    true,
            showBarcode:  true,
            showBrand:    true,
            showCategory: false,
            headerText:   '',
            footerText:   '',
        },

        applyTemplate(tpl) {
            this.settings.template = tpl.id;
            if (tpl.defaults) {
                Object.assign(this.settings, tpl.defaults);
            }
            this.$nextTick(() => this.renderBarcodes());
        },

        addProduct(id, name, barcode, price, brand, category) {
            if (!this.selectedProducts.find(p => p.id === id)) {
                this.selectedProducts.push({ id, name, barcode, price, brand: brand || '', category: category || '' });
            }
            this.demo = [];
            this.$nextTick(() => this.renderBarcodes());
        },

        removeItem(id) {
            if (id < 0) { this.demo = this.demo.filter(p => p.id !== id); }
            else { this.selectedProducts = this.selectedProducts.filter(p => p.id !== id); }
        },

        clearSelection() { this.selectedProducts = []; this.demo = []; },

        getExpandedLabels() {
            const out = [];
            this.selectedProducts.forEach(p => {
                for (let i = 0; i < (parseInt(this.settings.quantity) || 1); i++) out.push({...p});
            });
            return out;
        },

        loadDemo() {
            this.demo = DEMO_PRODUCTS;
            this.$nextTick(() => this.renderBarcodes());
        },

        getLabelStyle() {
            const s = SIZE_MAP[this.settings.size] || SIZE_MAP.medium;
            return `background-color:${this.settings.bgColor}; width:${s.w}; height:${s.h};`
                 + `border-radius:${this.settings.radius}; overflow:hidden; cursor:pointer; box-sizing:border-box;`;
        },

        fmtPrice(v) { return parseFloat(v).toFixed(2).replace('.', ','); },

        renderBarcodes() {
            setTimeout(() => {
                document.querySelectorAll('.bc-svg').forEach(svg => {
                    const val = svg.dataset.val;
                    if (!val || !window.JsBarcode) return;
                    try {
                        JsBarcode(svg, val, {
                            format: val.length === 13 ? 'EAN13' : val.length === 8 ? 'EAN8' : 'CODE128',
                            width: 1, height: 20, fontSize: 6, margin: 1, displayValue: true,
                            lineColor: this.settings.textColor,
                            background: this.settings.bgColor,
                        });
                    } catch {
                        try { JsBarcode(svg, val, { format:'CODE128', width:1, height:18, fontSize:6, margin:1, displayValue:true }); } catch {}
                    }
                });
            }, 120);
        },

        printLabels() { window.print(); },

        async printToDevice() {
            const labels = this.getExpandedLabels().map(l => ({ name:l.name, barcode:l.barcode, price:l.price }));
            if (!labels.length) { alert('Önce ürün seçin veya demo etiketlerini kullanın.'); return; }
            try {
                await window.hw?.connectUSB('label_printer');
                await window.hw?.printLabels(labels);
            } catch(e) {
                if (e.name === 'NotFoundError') return;
                if (confirm('Etiket yazıcı bulunamadı. Tarayıcıdan yazdırmak ister misiniz?')) window.print();
            }
        },

        init() {
            this.$watch('settings', () => this.$nextTick(() => this.renderBarcodes()), { deep: true });
        }
    }
}
</script>

<style>
@media print {
    body > *                                       { display: none !important; }
    .xl\:col-span-4                                { display: none !important; }
    .xl\:col-span-8 .sticky > *:not(#print-area)  { display: none !important; }
    #print-area {
        display: block !important;
        padding: 4mm !important;
        background: white !important;
    }
    #print-area .label-cell {
        break-inside: avoid;
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>
@endsection
