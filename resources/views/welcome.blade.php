@extends('layouts.public')

@section('title', 'Emare Finance — Bulut POS & Finans Yönetimi')

@section('content')

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- HERO SECTION                                           -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="relative min-h-screen overflow-hidden flex items-center" style="background: #0f0a2e">
        <!-- Dark gradient background -->
        <div class="absolute inset-0 gradient-bg"></div>
        <div class="absolute inset-0 hero-pattern"></div>

        <!-- Animated blobs -->
        <div class="absolute top-20 right-10 w-96 h-96 bg-brand-500/20 blob animate-float blur-3xl"></div>
        <div class="absolute bottom-20 left-10 w-80 h-80 bg-purple-500/15 blob animate-float-delayed blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-brand-400/5 rounded-full blur-3xl"></div>

        <!-- Grid pattern overlay -->
        <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <!-- Left: Text -->
                <div class="text-center lg:text-left">
                    <!-- Badge -->
                    <div class="inline-flex items-center px-4 py-2 rounded-full glass mb-8 animate-fade-up">
                        <span class="w-2 h-2 rounded-full bg-green-400 mr-3 animate-pulse"></span>
                        <span class="text-white/80 text-sm font-medium">14 Gün Ücretsiz Deneme — Kredi Kartı Gerekmez</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-black text-white leading-tight mb-8 animate-fade-up" style="animation-delay: 0.1s">
                        İşletmenizi
                        <span class="relative inline-block">
                            <span class="relative z-10 bg-gradient-to-r from-brand-300 via-purple-300 to-pink-300 bg-clip-text text-transparent">Geleceğe</span>
                            <svg class="absolute -bottom-2 left-0 w-full" viewBox="0 0 300 12" fill="none">
                                <path d="M2 10C50 2 100 2 150 6C200 10 250 4 298 8" stroke="url(#underline-grad)" stroke-width="3" stroke-linecap="round"/>
                                <defs><linearGradient id="underline-grad"><stop stop-color="#818cf8"/><stop offset="1" stop-color="#c084fc"/></linearGradient></defs>
                            </svg>
                        </span>
                        Taşıyın
                    </h1>

                    <p class="text-lg sm:text-xl text-white/80 leading-relaxed mb-10 max-w-xl mx-auto lg:mx-0 animate-fade-up" style="animation-delay: 0.2s">
                        Satış, stok, müşteri, e-fatura, pazarlama ve daha fazlası — hepsi tek bulut platformunda. Kasadan rapora, mobilden dijital ekrana kadar her şey elinizin altında.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start animate-fade-up" style="animation-delay: 0.3s">
                        <a href="/kayit" class="group inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-white text-brand-700 font-bold text-lg shadow-2xl shadow-white/20 hover:shadow-white/30 hover:scale-105 transition-all duration-300">
                            Hemen Başla
                            <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="/giris" class="group inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-white/20 border-2 border-white/50 text-white font-semibold text-lg hover:bg-white/30 hover:border-white transition-all duration-300">
                            <i class="fas fa-sign-in-alt mr-3"></i>
                            Giriş Yap
                        </a>
                    </div>

                    <!-- Trust badges -->
                    <div class="flex items-center gap-8 mt-12 justify-center lg:justify-start animate-fade-up" style="animation-delay: 0.4s">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">%99.9</div>
                            <div class="text-xs text-white/70 mt-1">Uptime</div>
                        </div>
                        <div class="w-px h-8 bg-white/20"></div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">256-bit</div>
                            <div class="text-xs text-white/70 mt-1">SSL Şifreleme</div>
                        </div>
                        <div class="w-px h-8 bg-white/20"></div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">🇹🇷</div>
                            <div class="text-xs text-white/70 mt-1">Türkiye Sunucu</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Dashboard mockup -->
                <div class="hidden lg:block relative animate-fade-up" style="animation-delay: 0.3s">
                    <div class="relative">
                        <!-- Main dashboard card -->
                        <div class="glass rounded-3xl p-6 shadow-2xl glow shine">
                            <!-- Top bar -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                </div>
                                <div class="text-white/60 text-xs font-mono">dashboard.emarefinance.com</div>
                            </div>

                            <!-- Stats row -->
                            <div class="grid grid-cols-3 gap-3 mb-6">
                                <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                                    <div class="text-white/70 text-xs mb-1">Bugün Satış</div>
                                    <div class="text-white font-bold text-xl">₺24.850</div>
                                    <div class="text-green-400 text-xs mt-1"><i class="fas fa-arrow-up mr-1"></i>+12.5%</div>
                                </div>
                                <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                                    <div class="text-white/70 text-xs mb-1">Toplam Müşteri</div>
                                    <div class="text-white font-bold text-xl">1.847</div>
                                    <div class="text-green-400 text-xs mt-1"><i class="fas fa-arrow-up mr-1"></i>+8.3%</div>
                                </div>
                                <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                                    <div class="text-white/70 text-xs mb-1">Toplam Ürün</div>
                                    <div class="text-white font-bold text-xl">3.256</div>
                                    <div class="text-brand-300 text-xs mt-1"><i class="fas fa-cube mr-1"></i>Aktif</div>
                                </div>
                            </div>

                            <!-- Chart mockup -->
                            <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-white/80 text-sm font-medium">Haftalık Gelir</span>
                                    <span class="text-green-400 text-sm font-semibold">+23.4%</span>
                                </div>
                                <div class="flex items-end space-x-2 h-24">
                                    <div class="flex-1 bg-brand-500/30 rounded-t-lg" style="height: 45%"></div>
                                    <div class="flex-1 bg-brand-500/40 rounded-t-lg" style="height: 60%"></div>
                                    <div class="flex-1 bg-brand-500/30 rounded-t-lg" style="height: 35%"></div>
                                    <div class="flex-1 bg-brand-500/50 rounded-t-lg" style="height: 75%"></div>
                                    <div class="flex-1 bg-brand-500/40 rounded-t-lg" style="height: 55%"></div>
                                    <div class="flex-1 bg-brand-500/60 rounded-t-lg" style="height: 85%"></div>
                                    <div class="flex-1 bg-gradient-to-t from-brand-500 to-purple-400 rounded-t-lg" style="height: 100%"></div>
                                </div>
                                <div class="flex justify-between mt-2">
                                    <span class="text-white/50 text-xs">Pzt</span>
                                    <span class="text-white/50 text-xs">Sal</span>
                                    <span class="text-white/50 text-xs">Çar</span>
                                    <span class="text-white/50 text-xs">Per</span>
                                    <span class="text-white/50 text-xs">Cum</span>
                                    <span class="text-white/50 text-xs">Cts</span>
                                    <span class="text-white/60 text-xs font-medium">Paz</span>
                                </div>
                            </div>
                        </div>

                        <!-- Floating notification card -->
                        <div class="absolute -left-8 top-1/3 glass rounded-2xl p-4 shadow-xl animate-float-delayed w-56">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-green-500/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div>
                                    <p class="text-white text-sm font-semibold">Satış Tamamlandı</p>
                                    <p class="text-white/70 text-xs">₺1.250,00 — Nakit</p>
                                </div>
                            </div>
                        </div>

                        <!-- Floating POS card -->
                        <div class="absolute -right-4 bottom-16 glass rounded-2xl p-4 shadow-xl animate-float w-48">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-xl bg-brand-500/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-receipt text-brand-400"></i>
                                </div>
                                <div>
                                    <p class="text-white text-sm font-semibold">E-Fatura</p>
                                    <p class="text-white/70 text-xs">Otomatik Gönderildi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <i class="fas fa-chevron-down text-white/50 text-xl"></i>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- BRANDS / TRUST BAR                                     -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="relative py-16 bg-gray-50 border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm font-medium text-gray-400 mb-8 uppercase tracking-widest">Güvenle Kullanılan Teknolojiler</p>
            <div class="flex flex-wrap items-center justify-center gap-x-12 gap-y-6 opacity-40">
                <div class="flex items-center space-x-2 text-gray-600">
                    <i class="fab fa-laravel text-3xl"></i>
                    <span class="font-semibold">Laravel 12</span>
                </div>
                <div class="flex items-center space-x-2 text-gray-600">
                    <i class="fas fa-database text-2xl"></i>
                    <span class="font-semibold">MariaDB</span>
                </div>
                <div class="flex items-center space-x-2 text-gray-600">
                    <i class="fab fa-js text-2xl"></i>
                    <span class="font-semibold">Alpine.js</span>
                </div>
                <div class="flex items-center space-x-2 text-gray-600">
                    <i class="fab fa-css3-alt text-2xl"></i>
                    <span class="font-semibold">Tailwind CSS</span>
                </div>
                <div class="flex items-center space-x-2 text-gray-600">
                    <i class="fas fa-server text-2xl"></i>
                    <span class="font-semibold">Nginx</span>
                </div>
                <div class="flex items-center space-x-2 text-gray-600">
                    <i class="fas fa-robot text-2xl"></i>
                    <span class="font-semibold">Gemini AI</span>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- FEATURES SECTION                                       -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section id="ozellikler" class="relative py-24 lg:py-32 overflow-hidden">
        <!-- Background decorations -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-brand-100/50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-purple-100/50 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section header -->
            <div class="text-center max-w-3xl mx-auto mb-20 scroll-reveal">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-brand-50 text-brand-600 text-sm font-semibold mb-6">
                    <i class="fas fa-sparkles mr-2"></i>
                    Neden Emare Finance?
                </div>
                <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
                    Her Şey <span class="gradient-text">Tek Platformda</span>
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Kasadan buluta, mobilden ekrana — işletmenizin tüm ihtiyaçlarını karşılayan eksiksiz çözüm.
                </p>
            </div>

            <!-- Feature cards grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1: POS -->
                <div class="group card-hover scroll-reveal">
                    <div class="relative bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:border-brand-200 transition-all duration-500 h-full">
                        <div class="feature-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center mb-6 shadow-lg shadow-blue-500/25">
                            <i class="fas fa-cash-register text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Akıllı POS</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Hızlı barkod okuma, çoklu ödeme, indirim ve kampanya uygulaması. Offline çalışma desteği ile kesintisiz satış.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-medium">Barkod</span>
                            <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-medium">Çoklu Ödeme</span>
                            <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-medium">Hızlı Satış</span>
                        </div>
                    </div>
                </div>

                <!-- Feature 2: Stok -->
                <div class="group card-hover scroll-reveal" style="transition-delay: 0.1s">
                    <div class="relative bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:border-brand-200 transition-all duration-500 h-full">
                        <div class="feature-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center mb-6 shadow-lg shadow-emerald-500/25">
                            <i class="fas fa-warehouse text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Stok Yönetimi</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Anlık stok takibi, kritik stok uyarıları, sayım yönetimi ve şubeler arası transfer. Her ürünün nerede olduğunu bilin.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-medium">Anlık Takip</span>
                            <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-medium">Uyarılar</span>
                            <span class="px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-medium">Transfer</span>
                        </div>
                    </div>
                </div>

                <!-- Feature 3: Müşteri -->
                <div class="group card-hover scroll-reveal" style="transition-delay: 0.2s">
                    <div class="relative bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:border-brand-200 transition-all duration-500 h-full">
                        <div class="feature-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center mb-6 shadow-lg shadow-purple-500/25">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Müşteri & Cari</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Müşteri kartları, cari hesap takibi, veresiye yönetimi, tahsilat ve borç-alacak analizi. Her müşterinizi tanıyın.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-lg bg-purple-50 text-purple-600 text-xs font-medium">Cari Hesap</span>
                            <span class="px-3 py-1 rounded-lg bg-purple-50 text-purple-600 text-xs font-medium">Tahsilat</span>
                            <span class="px-3 py-1 rounded-lg bg-purple-50 text-purple-600 text-xs font-medium">Analiz</span>
                        </div>
                    </div>
                </div>

                <!-- Feature 4: Raporlama -->
                <div class="group card-hover scroll-reveal">
                    <div class="relative bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:border-brand-200 transition-all duration-500 h-full">
                        <div class="feature-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center mb-6 shadow-lg shadow-amber-500/25">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Gelişmiş Raporlar</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Günlük, haftalık, aylık satış raporları. Kâr analizi, ürün korelasyonu ve trend grafikleri ile veriye dayalı kararlar.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-lg bg-amber-50 text-amber-600 text-xs font-medium">Kâr Analizi</span>
                            <span class="px-3 py-1 rounded-lg bg-amber-50 text-amber-600 text-xs font-medium">Trendler</span>
                            <span class="px-3 py-1 rounded-lg bg-amber-50 text-amber-600 text-xs font-medium">Excel</span>
                        </div>
                    </div>
                </div>

                <!-- Feature 5: E-Fatura -->
                <div class="group card-hover scroll-reveal" style="transition-delay: 0.1s">
                    <div class="relative bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:border-brand-200 transition-all duration-500 h-full">
                        <div class="feature-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-red-500 to-rose-500 flex items-center justify-center mb-6 shadow-lg shadow-red-500/25">
                            <i class="fas fa-file-invoice text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">E-Fatura & E-Arşiv</h3>
                        <p class="text-gray-600 leading-relaxed">
                            GİB uyumlu e-fatura, e-arşiv, tevkifat ve istisna faturaları. Otomatik gönderim ve tekrarlayan fatura desteği.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-lg bg-red-50 text-red-600 text-xs font-medium">GİB Uyumlu</span>
                            <span class="px-3 py-1 rounded-lg bg-red-50 text-red-600 text-xs font-medium">Otomatik</span>
                            <span class="px-3 py-1 rounded-lg bg-red-50 text-red-600 text-xs font-medium">Tekrarlayan</span>
                        </div>
                    </div>
                </div>

                <!-- Feature 6: AI -->
                <div class="group card-hover scroll-reveal" style="transition-delay: 0.2s">
                    <div class="relative bg-white rounded-3xl p-8 border border-gray-100 shadow-sm hover:border-brand-200 transition-all duration-500 h-full overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-brand-100/50 to-transparent rounded-bl-full"></div>
                        <div class="feature-icon w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-500 to-purple-600 flex items-center justify-center mb-6 shadow-lg shadow-brand-500/25">
                            <i class="fas fa-robot text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">AI Asistan <span class="text-xs bg-brand-100 text-brand-700 px-2 py-0.5 rounded-full ml-2">Yeni</span></h3>
                        <p class="text-gray-600 leading-relaxed">
                            Gemini AI destekli akıllı asistan. Satış trendlerini analiz edin, öneriler alın, sorularınızı Türkçe sorun.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-lg bg-brand-50 text-brand-600 text-xs font-medium">Gemini AI</span>
                            <span class="px-3 py-1 rounded-lg bg-brand-50 text-brand-600 text-xs font-medium">Türkçe</span>
                            <span class="px-3 py-1 rounded-lg bg-brand-50 text-brand-600 text-xs font-medium">Akıllı</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- STATS SECTION                                          -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="relative py-20 bg-gradient-to-br from-brand-950 via-brand-900 to-purple-900 overflow-hidden">
        <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-brand-500/30 to-transparent"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="scroll-reveal" x-data="{ count: 0 }" x-intersect.once="let target = 264; let step = Math.ceil(target/40); let i = setInterval(() => { count += step; if (count >= target) { count = target; clearInterval(i); } }, 30);">
                    <div class="text-4xl md:text-5xl font-black text-white mb-2" x-text="count + '+'">264+</div>
                    <div class="text-brand-200 font-medium">Fonksiyon & Endpoint</div>
                </div>
                <div class="scroll-reveal" style="transition-delay: 0.1s" x-data="{ count: 0 }" x-intersect.once="let target = 59; let step = Math.ceil(target/40); let i = setInterval(() => { count += step; if (count >= target) { count = target; clearInterval(i); } }, 30);">
                    <div class="text-4xl md:text-5xl font-black text-white mb-2" x-text="count + '+'">59+</div>
                    <div class="text-brand-200 font-medium">Veri Modeli</div>
                </div>
                <div class="scroll-reveal" style="transition-delay: 0.2s" x-data="{ count: 0 }" x-intersect.once="let target = 10; let step = 1; let i = setInterval(() => { count += step; if (count >= target) { count = target; clearInterval(i); } }, 100);">
                    <div class="text-4xl md:text-5xl font-black text-white mb-2" x-text="count">10</div>
                    <div class="text-brand-200 font-medium">Güçlü Modül</div>
                </div>
                <div class="scroll-reveal" style="transition-delay: 0.3s">
                    <div class="text-4xl md:text-5xl font-black text-white mb-2">7/24</div>
                    <div class="text-brand-200 font-medium">Bulut Erişim</div>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- MODULES SECTION                                        -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section id="moduller" class="relative py-24 lg:py-32 bg-gray-50/50 overflow-hidden">
        <div class="absolute top-1/2 left-0 w-64 h-64 bg-brand-100/30 rounded-full blur-3xl -translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section header -->
            <div class="text-center max-w-3xl mx-auto mb-20 scroll-reveal">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-purple-50 text-purple-600 text-sm font-semibold mb-6">
                    <i class="fas fa-puzzle-piece mr-2"></i>
                    Modüler Yapı
                </div>
                <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
                    İhtiyacınıza Göre <span class="gradient-text">Özelleştirin</span>
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Sadece ihtiyacınız olan modülleri aktive edin. İşletmeniz büyüdükçe yeni modüller ekleyin.
                </p>
            </div>

            <!-- Modules grid -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5">
                <!-- Core POS -->
                <div class="scroll-reveal group">
                    <div class="relative bg-white rounded-2xl p-6 border border-gray-100 shadow-sm card-hover text-center h-full">
                        <div class="absolute top-3 right-3">
                            <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold uppercase">Dahil</span>
                        </div>
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-store text-blue-600 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">Temel POS</h4>
                        <p class="text-gray-500 text-xs leading-relaxed">Satış, ürün, müşteri, stok ve raporlama</p>
                    </div>
                </div>

                <!-- Hardware -->
                <div class="scroll-reveal group" style="transition-delay: 0.05s">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm card-hover text-center h-full">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-print text-gray-600 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">Donanım</h4>
                        <p class="text-gray-500 text-xs leading-relaxed">Yazıcı, barkod, kasa, terazi sürücüleri</p>
                    </div>
                </div>

                <!-- E-Invoice -->
                <div class="scroll-reveal group" style="transition-delay: 0.1s">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm card-hover text-center h-full">
                        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-invoice text-red-600 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">E-Fatura</h4>
                        <p class="text-gray-500 text-xs leading-relaxed">E-Fatura, E-Arşiv, tekrarlayan fatura</p>
                    </div>
                </div>

                <!-- Gelir Gider -->
                <div class="scroll-reveal group" style="transition-delay: 0.15s">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm card-hover text-center h-full">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-wallet text-emerald-600 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">Gelir-Gider</h4>
                        <p class="text-gray-500 text-xs leading-relaxed">Gelir, gider takibi ve mali analiz</p>
                    </div>
                </div>

                <!-- Staff -->
                <div class="scroll-reveal group" style="transition-delay: 0.2s">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm card-hover text-center h-full">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-id-badge text-amber-600 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">Personel</h4>
                        <p class="text-gray-500 text-xs leading-relaxed">Personel, giriş-çıkış, hareket takibi</p>
                    </div>
                </div>

                <!-- Marketing -->
                <div class="scroll-reveal group" style="transition-delay: 0.25s">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm card-hover text-center h-full">
                        <div class="w-12 h-12 rounded-xl bg-pink-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bullhorn text-pink-600 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">Pazarlama</h4>
                        <p class="text-gray-500 text-xs leading-relaxed">Kampanya, segment, sadakat, teklif</p>
                    </div>
                </div>

                <!-- SMS -->
                <div class="scroll-reveal group" style="transition-delay: 0.3s">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm card-hover text-center h-full">
                        <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comment-sms text-cyan-600 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">SMS</h4>
                        <p class="text-gray-500 text-xs leading-relaxed">Toplu SMS, şablon, otomatik senaryo</p>
                    </div>
                </div>

                <!-- Advanced Reports -->
                <div class="scroll-reveal group" style="transition-delay: 0.35s">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm card-hover text-center h-full">
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-pie text-indigo-600 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">İleri Raporlar</h4>
                        <p class="text-gray-500 text-xs leading-relaxed">Korelasyon, tarihsel analiz, trend</p>
                    </div>
                </div>

                <!-- API -->
                <div class="scroll-reveal group" style="transition-delay: 0.4s">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm card-hover text-center h-full">
                        <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-code text-violet-600 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">API Erişim</h4>
                        <p class="text-gray-500 text-xs leading-relaxed">REST API ile dış sistem entegrasyonu</p>
                    </div>
                </div>

                <!-- Mobile -->
                <div class="scroll-reveal group" style="transition-delay: 0.45s">
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm card-hover text-center h-full">
                        <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-mobile-screen text-teal-600 text-lg"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 mb-1">Mobil</h4>
                        <p class="text-gray-500 text-xs leading-relaxed">Mobil sipariş, barkod tarama, kamera</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- HOW IT WORKS                                           -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="relative py-24 lg:py-32 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20 scroll-reveal">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-green-50 text-green-600 text-sm font-semibold mb-6">
                    <i class="fas fa-rocket mr-2"></i>
                    3 Adımda Başlayın
                </div>
                <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
                    Dakikalar İçinde <span class="gradient-text">Hazır</span>
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-12 relative">
                <!-- Connecting line -->
                <div class="hidden md:block absolute top-20 left-1/6 right-1/6 h-0.5 bg-gradient-to-r from-brand-200 via-purple-200 to-brand-200"></div>

                <!-- Step 1 -->
                <div class="text-center scroll-reveal">
                    <div class="relative inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-600 text-white text-2xl font-bold mb-8 shadow-lg shadow-brand-500/30 z-10">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Hesap Oluşturun</h3>
                    <p class="text-gray-600 leading-relaxed">E-posta, firma adı ve şifre ile 30 saniyede kayıt olun. Otomatik olarak şubeniz ve yönetici hesabınız oluşturulur.</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center scroll-reveal" style="transition-delay: 0.15s">
                    <div class="relative inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 text-white text-2xl font-bold mb-8 shadow-lg shadow-purple-500/30 z-10">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Ürünlerinizi Ekleyin</h3>
                    <p class="text-gray-600 leading-relaxed">Barkod okutarak, Excel import ile veya tek tek — ürün kataloğunuzu hızlıca oluşturun. Kategoriler ve fiyatları belirleyin.</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center scroll-reveal" style="transition-delay: 0.3s">
                    <div class="relative inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 text-white text-2xl font-bold mb-8 shadow-lg shadow-green-500/30 z-10">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Satışa Başlayın!</h3>
                    <p class="text-gray-600 leading-relaxed">POS ekranından hızlıca satış yapın. Raporlarınız, stok hareketleriniz ve cari hesaplarınız otomatik güncellenir.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- PRICING SECTION                                        -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section id="fiyatlar" class="relative py-24 lg:py-32 bg-gray-50/50 overflow-hidden" x-data="{ annual: true }">
        <div class="absolute top-0 right-0 w-96 h-96 bg-brand-100/30 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section header -->
            <div class="text-center max-w-3xl mx-auto mb-16 scroll-reveal">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-amber-50 text-amber-600 text-sm font-semibold mb-6">
                    <i class="fas fa-tags mr-2"></i>
                    Şeffaf Fiyatlandırma
                </div>
                <h2 class="text-4xl sm:text-5xl font-bold text-gray-900 mb-6">
                    Her Bütçeye <span class="gradient-text">Uygun Planlar</span>
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed mb-10">
                    14 gün ücretsiz deneyin. İstediğiniz zaman plan değiştirin veya iptal edin.
                </p>

                <!-- Toggle -->
                <div class="inline-flex items-center bg-white rounded-2xl p-1.5 border border-gray-200 shadow-sm">
                    <button @click="annual = false"
                            :class="!annual ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/25' : 'text-gray-500 hover:text-gray-700'"
                            class="px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300">
                        Aylık
                    </button>
                    <button @click="annual = true"
                            :class="annual ? 'bg-brand-500 text-white shadow-lg shadow-brand-500/25' : 'text-gray-500 hover:text-gray-700'"
                            class="px-6 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300">
                        Yıllık <span class="ml-1 text-xs" :class="annual ? 'text-brand-200' : 'text-green-500'">%17 Tasarruf</span>
                    </button>
                </div>
            </div>

            <!-- Plans grid -->
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Starter -->
                <div class="scroll-reveal">
                    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm card-hover h-full flex flex-col">
                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Başlangıç</h3>
                            <p class="text-gray-500 text-sm">Küçük işletmeler için ideal</p>
                        </div>

                        <div class="mb-8">
                            <div class="flex items-baseline">
                                <span class="text-4xl font-black text-gray-900" x-text="annual ? '₺249' : '₺299'">₺249</span>
                                <span class="text-gray-400 ml-2">/ay</span>
                            </div>
                            <p class="text-sm text-gray-400 mt-1" x-show="annual">yıllık ödemede ₺2.990/yıl</p>
                        </div>

                        <ul class="space-y-4 mb-10 flex-1">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>1</strong> Şube</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>3</strong> Kullanıcı</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>500</strong> Ürün</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>200</strong> Müşteri</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm">Temel POS & Raporlar</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm">AI Asistan</span>
                            </li>
                        </ul>

                        <a href="/kayit" class="block w-full py-3.5 rounded-2xl border-2 border-gray-200 text-gray-700 font-semibold text-center hover:border-brand-500 hover:text-brand-600 hover:bg-brand-50 transition-all duration-300">
                            Ücretsiz Dene
                        </a>
                    </div>
                </div>

                <!-- Business (Popular) -->
                <div class="scroll-reveal" style="transition-delay: 0.1s">
                    <div class="relative bg-white rounded-3xl p-8 border-2 border-brand-500 shadow-xl shadow-brand-500/10 card-hover h-full flex flex-col">
                        <!-- Popular badge -->
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2">
                            <span class="px-5 py-1.5 rounded-full bg-gradient-to-r from-brand-500 to-purple-600 text-white text-xs font-bold shadow-lg shadow-brand-500/30">
                                <i class="fas fa-star mr-1"></i> En Popüler
                            </span>
                        </div>

                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">İşletme</h3>
                            <p class="text-gray-500 text-sm">Büyüyen işletmeler için</p>
                        </div>

                        <div class="mb-8">
                            <div class="flex items-baseline">
                                <span class="text-4xl font-black text-gray-900" x-text="annual ? '₺499' : '₺599'">₺499</span>
                                <span class="text-gray-400 ml-2">/ay</span>
                            </div>
                            <p class="text-sm text-gray-400 mt-1" x-show="annual">yıllık ödemede ₺5.990/yıl</p>
                        </div>

                        <ul class="space-y-4 mb-10 flex-1">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-brand-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>5</strong> Şube</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-brand-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>15</strong> Kullanıcı</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-brand-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>5.000</strong> Ürün</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-brand-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>2.000</strong> Müşteri</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-brand-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm">Tüm Core Modüller</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-brand-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm">E-Fatura + SMS + Pazarlama</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-brand-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm">Öncelikli Destek</span>
                            </li>
                        </ul>

                        <a href="/kayit" class="block w-full py-3.5 rounded-2xl bg-gradient-to-r from-brand-500 to-purple-600 text-white font-bold text-center shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:scale-[1.02] transition-all duration-300">
                            Ücretsiz Dene
                        </a>
                    </div>
                </div>

                <!-- Enterprise -->
                <div class="scroll-reveal" style="transition-delay: 0.2s">
                    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm card-hover h-full flex flex-col">
                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Kurumsal</h3>
                            <p class="text-gray-500 text-sm">Büyük firmalar ve zincirler</p>
                        </div>

                        <div class="mb-8">
                            <div class="flex items-baseline">
                                <span class="text-4xl font-black text-gray-900" x-text="annual ? '₺1.083' : '₺1.299'">₺1.083</span>
                                <span class="text-gray-400 ml-2">/ay</span>
                            </div>
                            <p class="text-sm text-gray-400 mt-1" x-show="annual">yıllık ödemede ₺12.990/yıl</p>
                        </div>

                        <ul class="space-y-4 mb-10 flex-1">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>Sınırsız</strong> Şube</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>Sınırsız</strong> Kullanıcı</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>Sınırsız</strong> Ürün & Müşteri</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm"><strong>Tüm</strong> Modüller Dahil</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm">API Erişimi</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm">Özel Müşteri Temsilcisi</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3 flex-shrink-0"></i>
                                <span class="text-gray-600 text-sm">SLA Garantisi</span>
                            </li>
                        </ul>

                        <a href="/kayit" class="block w-full py-3.5 rounded-2xl border-2 border-gray-200 text-gray-700 font-semibold text-center hover:border-brand-500 hover:text-brand-600 hover:bg-brand-50 transition-all duration-300">
                            Ücretsiz Dene
                        </a>
                    </div>
                </div>
            </div>

            <!-- Guarantee -->
            <div class="text-center mt-12 scroll-reveal">
                <div class="inline-flex items-center px-6 py-3 rounded-2xl bg-green-50 border border-green-100">
                    <i class="fas fa-shield-halved text-green-500 text-lg mr-3"></i>
                    <span class="text-green-700 font-medium text-sm">14 gün ücretsiz — Kredi kartı gerekmez — İstediğiniz zaman iptal</span>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- TESTIMONIALS (Social Proof)                            -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="relative py-24 lg:py-32 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 scroll-reveal">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-brand-50 text-brand-600 text-sm font-semibold mb-6">
                    <i class="fas fa-quote-left mr-2"></i>
                    Kullanıcı Yorumları
                </div>
                <h2 class="text-4xl sm:text-5xl font-bold text-gray-900">
                    Müşterilerimiz <span class="gradient-text">Ne Diyor?</span>
                </h2>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="scroll-reveal">
                    <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 h-full">
                        <div class="flex gap-1 mb-4">
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                        </div>
                        <p class="text-gray-600 leading-relaxed mb-6">"Ürünlerimizi ekledikten 10 dakika sonra ilk satışımızı yaptık. Stok takibi artık otomatik, sayım stresi bitti. Raporlar harika!"</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center mr-3">
                                <span class="text-brand-600 font-bold text-sm">AK</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm">Ahmet Kaya</div>
                                <div class="text-gray-400 text-xs">Market Sahibi, İstanbul</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="scroll-reveal" style="transition-delay: 0.1s">
                    <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 h-full">
                        <div class="flex gap-1 mb-4">
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                        </div>
                        <p class="text-gray-600 leading-relaxed mb-6">"3 şubemizi tek panelden yönetiyoruz. Veresiye takibi çok kolay, müşteri bazlı raporlar sayesinde daha bilinçli kararlar alıyoruz."</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                <span class="text-purple-600 font-bold text-sm">SY</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm">Selin Yıldız</div>
                                <div class="text-gray-400 text-xs">Restoran Zinciri Müdürü, Ankara</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="scroll-reveal" style="transition-delay: 0.2s">
                    <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 h-full">
                        <div class="flex gap-1 mb-4">
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                            <i class="fas fa-star text-amber-400"></i>
                        </div>
                        <p class="text-gray-600 leading-relaxed mb-6">"E-fatura modülü hayat kurtardı. Satış anında otomatik fatura kesiliyor. SMS ile müşterilerimize kampanya bildirimi gönderiyoruz."</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                                <span class="text-emerald-600 font-bold text-sm">MD</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm">Mehmet Demir</div>
                                <div class="text-gray-400 text-xs">Eczane Sahibi, İzmir</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- FAQ SECTION                                            -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="relative py-24 lg:py-32 bg-gray-50/50 overflow-hidden">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 scroll-reveal">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-cyan-50 text-cyan-600 text-sm font-semibold mb-6">
                    <i class="fas fa-circle-question mr-2"></i>
                    Sıkça Sorulanlar
                </div>
                <h2 class="text-4xl font-bold text-gray-900">
                    Merak <span class="gradient-text">Ettikleriniz</span>
                </h2>
            </div>

            <div class="space-y-4 scroll-reveal" x-data="{ open: null }">
                <!-- FAQ 1 -->
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <button @click="open = open === 1 ? null : 1" class="w-full flex items-center justify-between px-6 py-5 text-left">
                        <span class="font-semibold text-gray-900">Kurulum gerekiyor mu?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :class="open === 1 ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open === 1" x-collapse>
                        <div class="px-6 pb-5 text-gray-600 leading-relaxed">
                            Hayır! Emare Finance tamamen bulut tabanlıdır. Herhangi bir kurulum gerektirmez. Tarayıcınızdan veya mobil cihazınızdan anında erişebilirsiniz. Kayıt olduktan hemen sonra kullanmaya başlayabilirsiniz.
                        </div>
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <button @click="open = open === 2 ? null : 2" class="w-full flex items-center justify-between px-6 py-5 text-left">
                        <span class="font-semibold text-gray-900">Verilerim güvende mi?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :class="open === 2 ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open === 2" x-collapse>
                        <div class="px-6 pb-5 text-gray-600 leading-relaxed">
                            Kesinlikle! 256-bit SSL şifreleme, Türkiye'de bulunan sunucular ve KVKK uyumlu veri işleme politikamız ile verileriniz her zaman güvendedir. Günlük otomatik yedekleme yapılır.
                        </div>
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <button @click="open = open === 3 ? null : 3" class="w-full flex items-center justify-between px-6 py-5 text-left">
                        <span class="font-semibold text-gray-900">Hangi donanımlar destekleniyor?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :class="open === 3 ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open === 3" x-collapse>
                        <div class="px-6 pb-5 text-gray-600 leading-relaxed">
                            Fiş yazıcı (ESC/POS, Star), barkod okuyucu (USB/Bluetooth), para çekmecesi, müşteri ekranı, terazi ve kart okuyucu desteği mevcuttur. Donanım modülü ile 50+ marka ve model desteklenmektedir.
                        </div>
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <button @click="open = open === 4 ? null : 4" class="w-full flex items-center justify-between px-6 py-5 text-left">
                        <span class="font-semibold text-gray-900">Ücretsiz deneme nasıl çalışır?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :class="open === 4 ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open === 4" x-collapse>
                        <div class="px-6 pb-5 text-gray-600 leading-relaxed">
                            14 gün boyunca İşletme planının tüm özelliklerini ücretsiz deneyebilirsiniz. Kredi kartı bilgisi istenmez. Deneme süresi bittiğinde planınızı seçip devam edebilir veya hesabınızı dondurabilirsiniz.
                        </div>
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <button @click="open = open === 5 ? null : 5" class="w-full flex items-center justify-between px-6 py-5 text-left">
                        <span class="font-semibold text-gray-900">Birden fazla şubeyi yönetebilir miyim?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" :class="open === 5 ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open === 5" x-collapse>
                        <div class="px-6 pb-5 text-gray-600 leading-relaxed">
                            Evet! İşletme planında 5, Kurumsal planında sınırsız şube yönetebilirsiniz. Her şubenin stoku, personeli ve satışları ayrı takip edilir. Şubeler arası stok transferi de yapabilirsiniz.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- CTA SECTION                                            -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="relative py-24 lg:py-32 overflow-hidden" style="background: #0f0a2e">
        <div class="absolute inset-0 gradient-bg"></div>
        <div class="absolute inset-0 hero-pattern"></div>
        <div class="absolute inset-0" style="background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 30px 30px;"></div>

        <!-- Animated blobs -->
        <div class="absolute top-10 left-10 w-72 h-72 bg-brand-400/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-purple-400/10 rounded-full blur-3xl animate-float-delayed"></div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center scroll-reveal">
            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-white leading-tight mb-8">
                İşletmenizi Dönüştürmeye<br>
                <span class="bg-gradient-to-r from-brand-300 via-purple-300 to-pink-300 bg-clip-text text-transparent">Bugün Başlayın</span>
            </h2>
            <p class="text-lg sm:text-xl text-white/80 leading-relaxed mb-12 max-w-2xl mx-auto">
                14 gün ücretsiz deneyin. Kurulum yok, kredi kartı yok. Sadece kayıt olun ve hemen satışa başlayın.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/kayit" class="group inline-flex items-center justify-center px-10 py-5 rounded-2xl bg-white text-brand-700 font-bold text-lg shadow-2xl shadow-white/20 hover:shadow-white/30 hover:scale-105 transition-all duration-300">
                    <i class="fas fa-rocket mr-3"></i>
                    Ücretsiz Hesap Oluştur
                    <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="/giris" class="group inline-flex items-center justify-center px-10 py-5 rounded-2xl bg-white/20 border-2 border-white/50 text-white font-bold text-lg hover:bg-white/30 hover:border-white hover:scale-105 transition-all duration-300">
                    <i class="fas fa-sign-in-alt mr-3"></i>
                    Giriş Yap
                </a>
            </div>

            <p class="text-white/70 text-sm mt-8">
                <i class="fas fa-check mr-2"></i>Kredi kartı gerekmez
                <span class="mx-3">•</span>
                <i class="fas fa-check mr-2"></i>14 gün ücretsiz
                <span class="mx-3">•</span>
                <i class="fas fa-check mr-2"></i>İstediğin zaman iptal
            </p>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- CONTACT SECTION                                        -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section id="iletisim" class="relative py-24 lg:py-32 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-start">
                <!-- Left -->
                <div class="scroll-reveal">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-brand-50 text-brand-600 text-sm font-semibold mb-6">
                        <i class="fas fa-envelope mr-2"></i>
                        Bize Ulaşın
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">
                        Size Nasıl <span class="gradient-text">Yardımcı Olabiliriz?</span>
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-10">
                        Sorularınız, önerileriniz veya demo talepleriniz için bize ulaşın. Ekibimiz en kısa sürede yanıt verecektir.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0 mr-4">
                                <i class="fas fa-envelope text-brand-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">E-posta</h4>
                                <a href="mailto:info@emarefinance.com" class="text-brand-600 hover:text-brand-700 transition-colors">info@emarefinance.com</a>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0 mr-4">
                                <i class="fab fa-whatsapp text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">WhatsApp</h4>
                                <a href="https://wa.me/905001234567" class="text-green-600 hover:text-green-700 transition-colors">+90 500 123 45 67</a>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0 mr-4">
                                <i class="fas fa-clock text-amber-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Çalışma Saatleri</h4>
                                <p class="text-gray-600">Pazartesi - Cuma: 09:00 - 18:00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Contact form -->
                <div class="scroll-reveal" style="transition-delay: 0.2s">
                    <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                        <form class="space-y-5">
                            <div class="grid sm:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ad Soyad</label>
                                    <input type="text" placeholder="Adınız Soyadınız" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">E-posta</label>
                                    <input type="email" placeholder="ornek@firma.com" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Firma Adı</label>
                                <input type="text" placeholder="Firma adınız" class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Konu</label>
                                <select class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm text-gray-500">
                                    <option>Seçiniz...</option>
                                    <option>Demo Talebi</option>
                                    <option>Fiyat Bilgisi</option>
                                    <option>Teknik Destek</option>
                                    <option>İş Ortaklığı</option>
                                    <option>Diğer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Mesajınız</label>
                                <textarea rows="4" placeholder="Mesajınızı yazın..." class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm resize-none"></textarea>
                            </div>
                            <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-brand-500 to-purple-600 text-white font-bold shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:scale-[1.02] transition-all duration-300">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Gönder
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
