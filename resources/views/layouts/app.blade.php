<!DOCTYPE html>
<html lang="tr" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Emare Finance</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
    <!-- Heroicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- JsBarcode — Gerçek barkod üretimi -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3/dist/JsBarcode.all.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full">

<div class="min-h-full" x-data="{ sidebarOpen: true }">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="fixed inset-y-0 left-0 z-50 bg-gray-900 transition-all duration-300 flex flex-col">
        {{-- Logo --}}
        <div class="flex items-center justify-between h-16 px-4 bg-gray-800">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">EF</span>
                </div>
                <span x-show="sidebarOpen" x-cloak class="text-white font-semibold text-lg">Emare Finance</span>
            </a>
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        {{-- Menü --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            {{-- Dashboard --}}
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-chart-line w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Dashboard</span>
            </a>

            {{-- ─── Raporlar (Açılır) ─── --}}
            @permission('reports.view')
            <div x-data="{ open: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fas fa-chart-bar w-5 mr-3 text-center"></i>
                        <span x-show="sidebarOpen">Raporlar</span>
                    </div>
                    <i x-show="sidebarOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs"></i>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('reports.daily') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('reports.daily') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Günlük Rapor</a>
                    <a href="{{ route('reports.historical') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('reports.historical') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Tarihsel Rapor</a>
                    <a href="{{ route('reports.products') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('reports.products') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Ürünsel Rapor</a>
                    <a href="{{ route('reports.groups') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('reports.groups') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Grupsal Rapor</a>
                    <a href="{{ route('reports.correlation') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('reports.correlation') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Ürün Korelasyon Raporu</a>
                    <a href="{{ route('reports.stock_movement') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('reports.stock_movement') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Stok Hareket Rapor</a>
                    <a href="{{ route('reports.staff_movement') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('reports.staff_movement') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Personel Hareket Raporu</a>
                </div>
            </div>
            @endpermission

            {{-- ─── Müşteriler (Açılır) ─── --}}
            @permission('customers.view')
            <div x-data="{ open: {{ request()->routeIs('customers.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('customers.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fas fa-user-tie w-5 mr-3 text-center"></i>
                        <span x-show="sidebarOpen">Müşteriler</span>
                    </div>
                    <i x-show="sidebarOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs"></i>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('customers.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('customers.index') && !request('has_debt') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Müşteri Listesi</a>
                    @permission('customers.create')
                    <a href="{{ route('customers.create') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('customers.create') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">
                        <i class="fas fa-plus mr-1 text-xs"></i> Yeni Müşteri
                    </a>
                    @endpermission
                    <a href="{{ route('customers.index', ['has_debt' => 1]) }}" class="block px-3 py-1.5 rounded text-sm {{ request('has_debt') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Borçlu Cariler</a>
                </div>
            </div>
            @endpermission

            {{-- ─── Ürünler (Açılır) ─── --}}
            @permission('products.view')
            <div x-data="{ open: {{ request()->routeIs('products.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('products.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fas fa-boxes-stacked w-5 mr-3 text-center"></i>
                        <span x-show="sidebarOpen">Ürünler</span>
                    </div>
                    <i x-show="sidebarOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs"></i>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('products.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('products.index') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Ürün Listesi</a>
                    <a href="{{ route('products.create') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('products.create') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Ürün Ekle & Güncelle</a>
                    <a href="{{ route('products.create_variant') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('products.create_variant') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Varyantlı Ürün Ekle</a>
                    <a href="{{ route('products.groups') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('products.groups') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Ürün Grupları</a>
                    <a href="{{ route('products.sub_products') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('products.sub_products') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Alt Ürün Tanımları</a>
                    <a href="{{ route('products.variants') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('products.variants') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Ürün Varyantları</a>
                    <a href="{{ route('products.refunds') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('products.refunds') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Ürün İadesi Al</a>
                    <a href="{{ route('products.refund_requests') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('products.refund_requests') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">İade Talepleri</a>
                    <a href="{{ route('products.labels') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('products.labels') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Ürün Etiketi Üret</a>
                    <a href="{{ route('products.label_designer') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('products.label_designer') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Etiket Tasarla & Üret</a>
                    <a href="{{ route('products.scale_barcode') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('products.scale_barcode') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Barkodlu Terazi Çıktısı</a>
                </div>
            </div>
            @endpermission

            {{-- ─── Faturalar (Birleşik Açılır) ─── --}}
            <div x-data="{ open: {{ request()->routeIs('faturalar.*') || request()->routeIs('invoices.*') || request()->routeIs('einvoices.*') || request()->routeIs('recurring_invoices.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('faturalar.*') || request()->routeIs('invoices.*') || request()->routeIs('einvoices.*') || request()->routeIs('recurring_invoices.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fas fa-file-invoice-dollar w-5 mr-3 text-center"></i>
                        <span x-show="sidebarOpen">Faturalar</span>
                    </div>
                    <i x-show="sidebarOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs"></i>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('faturalar.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('faturalar.index') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}"><i class="fas fa-tachometer-alt w-4 mr-2"></i>Fatura Paneli</a>
                    <a href="{{ route('faturalar.create') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('faturalar.create') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}"><i class="fas fa-plus-circle w-4 mr-2"></i>Yeni Fatura Kes</a>
                    <a href="{{ route('faturalar.outgoing') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('faturalar.outgoing') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}"><i class="fas fa-file-export w-4 mr-2"></i>Giden Faturalar</a>
                    <a href="{{ route('faturalar.incoming') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('faturalar.incoming') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}"><i class="fas fa-file-import w-4 mr-2"></i>Gelen Faturalar</a>
                    <a href="{{ route('faturalar.waybills') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('faturalar.waybills') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}"><i class="fas fa-truck w-4 mr-2"></i>İrsaliyeler</a>
                    <a href="{{ route('faturalar.earsiv') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('faturalar.earsiv') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}"><i class="fas fa-file-archive w-4 mr-2"></i>e-Arşiv Faturalar</a>
                    <a href="{{ route('recurring_invoices.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('recurring_invoices.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}"><i class="fas fa-redo w-4 mr-2"></i>Tekrarlayan Faturalar</a>
                    <a href="{{ route('einvoices.settings') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('einvoices.settings') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}"><i class="fas fa-cog w-4 mr-2"></i>E-Fatura Ayarları</a>
                </div>
            </div>

            {{-- ─── Firmalar ─── --}}
            <a href="{{ route('firms.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('firms.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-building w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Firmalar</span>
            </a>


            {{-- ─── Vergi & Hizmet Yönetimi (Açılır) ─── --}}
            <div x-data="{ open: {{ request()->routeIs('tax_rates.*') || request()->routeIs('service_categories.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('tax_rates.*') || request()->routeIs('service_categories.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fas fa-percent w-5 mr-3 text-center"></i>
                        <span x-show="sidebarOpen">Vergi & Hizmet</span>
                    </div>
                    <i x-show="sidebarOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs"></i>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('tax_rates.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('tax_rates.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Vergi Oranları</a>
                    <a href="{{ route('service_categories.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('service_categories.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Hizmet Kategorileri</a>
                </div>
            </div>

            {{-- ─── Stok (Açılır) ─── --}}
            <div x-data="{ open: {{ request()->routeIs('stock.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('stock.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fas fa-clipboard-check w-5 mr-3 text-center"></i>
                        <span x-show="sidebarOpen">Stok</span>
                    </div>
                    <i x-show="sidebarOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs"></i>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('stock.movements') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('stock.movements') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Stok Hareketleri</a>
                    <a href="{{ route('stock.counts') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('stock.counts') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Stok Sayımı</a>
                </div>
            </div>

            {{-- ─── Gelir / Giderler (Açılır) ─── --}}
            @module('income_expense')
            <div x-data="{ open: {{ request()->routeIs('income_expense.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('income_expense.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fas fa-exchange-alt w-5 mr-3 text-center"></i>
                        <span x-show="sidebarOpen">Gelir / Giderler</span>
                    </div>
                    <i x-show="sidebarOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs"></i>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('income_expense.incomes') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('income_expense.incomes') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Gelirler</a>
                    <a href="{{ route('income_expense.expenses') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('income_expense.expenses') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Giderler</a>
                    <a href="{{ route('income_expense.types') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('income_expense.types') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Gelir / Gider Türleri</a>
                </div>
            </div>
            @endmodule

            {{-- ─── Personeller (Açılır) ─── --}}
            @module('staff')
            <div x-data="{ open: {{ request()->routeIs('staff.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('staff.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fas fa-users-gear w-5 mr-3 text-center"></i>
                        <span x-show="sidebarOpen">Personeller</span>
                    </div>
                    <i x-show="sidebarOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs"></i>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('staff.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('staff.index') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Personel Listesi</a>
                    <a href="{{ route('staff.motions') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('staff.motions') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Personel Hareketleri</a>
                </div>
            </div>
            @endmodule

            {{-- ─── Pazarlama (Açılır) ─── --}}
            {{-- ─── SMS Yönetimi (Açılır) ─── --}}
            @module('sms')
            <div x-data="{ open: {{ request()->routeIs('sms.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sms.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fas fa-sms w-5 mr-3 text-center"></i>
                        <span x-show="sidebarOpen">SMS Yönetimi</span>
                    </div>
                    <i x-show="sidebarOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs"></i>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('sms.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('sms.index') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Dashboard</a>
                    <a href="{{ route('sms.compose') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('sms.compose') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">SMS Gönder</a>
                    <a href="{{ route('sms.templates.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('sms.templates.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Şablonlar</a>
                    <a href="{{ route('sms.scenarios.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('sms.scenarios.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Senaryolar</a>
                    <a href="{{ route('sms.automations.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('sms.automations.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">
                        <i class="fas fa-robot mr-1 text-xs"></i>Otomasyonlar
                    </a>
                    <a href="{{ route('sms.logs.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('sms.logs.index') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Gönderim Logları</a>
                    <a href="{{ route('sms.blacklist.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('sms.blacklist.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Kara Liste</a>
                    <a href="{{ route('sms.settings') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('sms.settings') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Ayarlar</a>
                </div>
            </div>
            @endmodule

            @module('marketing')
            <div x-data="{ open: {{ request()->routeIs('marketing.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('marketing.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <div class="flex items-center">
                        <i class="fas fa-bullhorn w-5 mr-3 text-center"></i>
                        <span x-show="sidebarOpen">Pazarlama</span>
                    </div>
                    <i x-show="sidebarOpen" :class="open ? 'fa-chevron-down' : 'fa-chevron-right'" class="fas text-xs"></i>
                </button>
                <div x-show="open && sidebarOpen" x-cloak class="ml-8 mt-1 space-y-1">
                    <a href="{{ route('marketing.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('marketing.index') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Dashboard</a>
                    <a href="{{ route('marketing.quotes.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('marketing.quotes.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Teklifler</a>
                    <a href="{{ route('marketing.campaigns.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('marketing.campaigns.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Kampanyalar</a>
                    <a href="{{ route('marketing.segments.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('marketing.segments.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Müşteri Segmentleri</a>
                    <a href="{{ route('marketing.messages.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('marketing.messages.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Mesajlar</a>
                    <a href="{{ route('marketing.loyalty.index') }}" class="block px-3 py-1.5 rounded text-sm {{ request()->routeIs('marketing.loyalty.*') ? 'text-white bg-gray-700' : 'text-gray-400 hover:text-white' }}">Sadakat Programı</a>
                </div>
            </div>
            @endmodule

            {{-- ─── Görevler ─── --}}
            <a href="{{ route('tasks.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('tasks.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-list-check w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Görevler</span>
                <span x-show="sidebarOpen" class="ml-auto bg-green-500 text-white text-xs px-1.5 py-0.5 rounded-full">Yeni!</span>
            </a>

            {{-- ─── Ödeme Tipleri ─── --}}
            <a href="{{ route('payment_types.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('payment_types.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-credit-card w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Ödeme Tipleri</span>
            </a>

            {{-- ─── Satışlar ─── --}}
            <a href="{{ route('sales.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sales.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-receipt w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Satışlar</span>
            </a>

            {{-- ─── Dijital Ekran ─── --}}
            <a href="{{ route('signage.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('signage.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-tv w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Dijital Ekran</span>
                <span x-show="sidebarOpen" class="ml-auto bg-violet-500 text-white text-xs px-1.5 py-0.5 rounded-full">Yeni!</span>
            </a>

            {{-- ─── Entegrasyonlar ─── --}}
            <a href="{{ route('integrations.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('integrations.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-puzzle-piece w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Entegrasyonlar</span>
                <span x-show="sidebarOpen" class="ml-auto bg-purple-500 text-white text-xs px-1.5 py-0.5 rounded-full">95+</span>
            </a>

            {{-- ─── Ekranlar ─── --}}
            <a href="{{ route('screens.menu') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('screens.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-desktop w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Ekranlar</span>
                <span x-show="sidebarOpen" class="ml-auto bg-amber-500 text-white text-xs px-1.5 py-0.5 rounded-full">POS</span>
            </a>

            {{-- ─── Donanım ─── --}}
            @module('hardware')
            <a href="{{ route('hardware.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('hardware.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-plug w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Donanım</span>
                <span x-show="sidebarOpen" class="ml-auto bg-cyan-500 text-white text-xs px-1.5 py-0.5 rounded-full">Yeni!</span>
            </a>
            @endmodule

            {{-- ─── Mobil İşlemler ─── --}}
            <a href="{{ route('mobile.index') }}"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('mobile.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <i class="fas fa-mobile-alt w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">Mobil İşlemler</span>
                <span x-show="sidebarOpen" class="ml-auto bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-xs px-1.5 py-0.5 rounded-full">Yeni</span>
            </a>

            {{-- ─── AI Sohbet ─── --}}
            <a href="#" @click.prevent="$dispatch('open-chat-widget')"
               class="flex items-center px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white">
                <i class="fas fa-robot w-5 mr-3 text-center"></i>
                <span x-show="sidebarOpen">AI Sohbet</span>
                <span x-show="sidebarOpen" class="ml-auto bg-gradient-to-r from-blue-500 to-purple-500 text-white text-xs px-1.5 py-0.5 rounded-full">AI</span>
            </a>

            {{-- ─── Muhasebe ─── --}}
            <div class="pt-4 mt-4 border-t border-gray-700">
                <p x-show="sidebarOpen" class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Muhasebe</p>
                <a href="{{ route('accounting.dashboard') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fas fa-calculator w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Muhasebe</span>
                </a>
                <a href="{{ route('accounting.account-plan') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.account-plan') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fas fa-list-ol w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Hesap Planı</span>
                </a>
                <a href="{{ route('accounting.journal.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.journal.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fas fa-book w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Yevmiye</span>
                </a>
                <a href="{{ route('accounting.trial-balance') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.trial-balance') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fas fa-scale-balanced w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Mizan</span>
                </a>
                <a href="{{ route('accounting.balance-sheet') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.balance-sheet') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fas fa-building-columns w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Bilanço</span>
                </a>
                <a href="{{ route('accounting.income-statement') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('accounting.income-statement') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fas fa-chart-line w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Gelir Tablosu</span>
                </a>
            </div>

            {{-- ─── Yönetim Paneli (Admin) ─── --}}
            @role('admin')
            <div class="pt-4 mt-4 border-t border-gray-700">
                <p x-show="sidebarOpen" class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Yönetim</p>
                <a href="{{ route('admin.modules.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.modules.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fas fa-puzzle-piece w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Modüller</span>
                </a>
                <a href="{{ route('admin.roles.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.roles.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fas fa-user-shield w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Roller & İzinler</span>
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fas fa-users-cog w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Kullanıcılar</span>
                </a>
                <a href="{{ route('admin.integration-requests.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('admin.integration-requests.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fas fa-inbox w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Entegrasyon Başvuruları</span>
                    @php $pendingCount = \App\Models\IntegrationRequest::forTenant(auth()->user()->tenant_id ?? 0)->pending()->count(); @endphp
                    @if($pendingCount > 0)
                        <span x-show="sidebarOpen" class="ml-auto bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full animate-pulse">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('feedback.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('feedback.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    <i class="fas fa-comment-dots w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Geri Bildirimler</span>
                    @php $openFeedback = \App\Models\FeedbackMessage::where('status', 'open')->count(); @endphp
                    @if($openFeedback > 0)
                        <span x-show="sidebarOpen" class="ml-auto bg-amber-500 text-white text-xs px-1.5 py-0.5 rounded-full">{{ $openFeedback }}</span>
                    @endif
                </a>
            </div>
            @endrole

            {{-- ─── Süper Admin Panel ─── --}}
            @superadmin
            <div class="pt-4 mt-4 border-t border-red-700">
                <p x-show="sidebarOpen" class="px-3 mb-2 text-xs font-semibold text-red-400 uppercase tracking-wider">Süper Admin</p>
                <a href="{{ route('super-admin.dashboard') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('super-admin.dashboard') ? 'bg-red-800 text-white' : 'text-red-300 hover:bg-red-900 hover:text-white' }}">
                    <i class="fas fa-shield-halved w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Süper Admin Paneli</span>
                </a>
                <a href="{{ route('super-admin.feedback.index') }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('super-admin.feedback.*') ? 'bg-red-800 text-white' : 'text-red-300 hover:bg-red-900 hover:text-white' }}">
                    <i class="fas fa-comment-dots w-5 mr-3 text-center"></i>
                    <span x-show="sidebarOpen">Geri Bildirimler
                        @php $openCount = \App\Models\FeedbackMessage::where('status','open')->count(); @endphp
                        @if($openCount > 0)
                            <span class="ml-1 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $openCount }}</span>
                        @endif
                    </span>
                </a>
            </div>
            @endsuperadmin

        </nav>

        {{-- Kullanıcı bilgisi & Çıkış --}}
        @auth
        <div class="border-t border-gray-700 px-3 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center min-w-0">
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    </div>
                    <div x-show="sidebarOpen" class="ml-3 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->primaryRole?->name ?? 'Kullanıcı' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white" title="Çıkış Yap">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </aside>

    {{-- Ana içerik --}}
    <div :class="sidebarOpen ? 'ml-64' : 'ml-20'" class="transition-all duration-300">
        {{-- Üst bar --}}
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between px-6 py-4">
                <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                <div class="flex items-center space-x-4">
                    @auth
                        @if(auth()->user()->tenant?->isOnTrial())
                            <span class="text-xs bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full font-medium">
                                <i class="fas fa-clock mr-1"></i>Deneme: {{ auth()->user()->tenant->trial_ends_at?->diffForHumans() }}
                            </span>
                        @endif
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-building mr-1"></i>{{ auth()->user()->branch?->name ?? 'Şube Yok' }}
                        </span>
                    @endauth
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-calendar mr-1"></i>
                        {{ now()->locale('tr')->translatedFormat('d F Y, l') }}
                    </span>
                </div>
            </div>
        </header>

        {{-- Flash mesajlar --}}
        @if(session('success'))
            <div class="mx-6 mt-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        {{-- İçerik --}}
        <main class="p-6">
            @yield('content')
        </main>
    </div>
</div>

@auth
    @include('partials.chat-widget')
    @include('partials.feedback-widget')
@endauth

@stack('scripts')
<script src="/js/hardware-drivers.js"></script>
<script>
    // Global hardware manager — tüm sayfalarda erişilebilir
    window.hw = new HardwareManager();
    // Barkod okuyucu otomatik dinleme (klavye wedge modu)
    window.hw.startBarcodeListener(function(barcode) {
        // Aktif arama input'u varsa barkodu yaz
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.value = barcode;
            searchInput.dispatchEvent(new Event('input', { bubbles: true }));
            // Formu otomatik gönder
            const form = searchInput.closest('form');
            if (form) form.submit();
        }
    });
</script>
</body>
</html>
