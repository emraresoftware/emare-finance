<!DOCTYPE html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Emare Finance — Bulut tabanlı POS ve finans yönetim platformu. Satış, stok, müşteri, e-fatura ve daha fazlası tek çatı altında.">
    <meta name="keywords" content="pos yazılımı, satış noktası, stok yönetimi, e-fatura, bulut pos, finans yönetimi, cari hesap">
    <meta name="author" content="Emare Finance">
    <title>@yield('title', 'Emare Finance — Bulut POS & Finans Yönetimi')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>💰</text></svg>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                            950: '#1e1b4b',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 2s infinite',
                        'float-slow': 'float 8s ease-in-out 1s infinite',
                        'gradient': 'gradient 8s ease infinite',
                        'fade-up': 'fadeUp 0.6s ease-out forwards',
                        'slide-right': 'slideRight 0.6s ease-out forwards',
                        'counter': 'counter 2s ease-out forwards',
                        'pulse-soft': 'pulseSoft 3s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        gradient: {
                            '0%, 100%': { backgroundPosition: '0% 50%' },
                            '50%': { backgroundPosition: '100% 50%' },
                        },
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideRight: {
                            '0%': { opacity: '0', transform: 'translateX(-30px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        },
                        pulseSoft: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.7' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js + Collapse Plugin -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        [x-cloak] { display: none !important; }

        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .glass-white {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .gradient-text {
            background: linear-gradient(135deg, #4f46e5, #7c3aed, #6d28d9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .gradient-bg {
            background: linear-gradient(-45deg, #0f0a2e, #1e1b4b, #1e1b4b, #312e81);
            background-size: 400% 400%;
            animation: gradient 8s ease infinite;
        }

        .hero-pattern {
            background-image:
                radial-gradient(at 80% 20%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 20% 80%, rgba(139, 92, 246, 0.1) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(167, 139, 250, 0.05) 0px, transparent 50%);
        }

        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(99, 102, 241, 0.15);
        }

        .glow {
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.3);
        }

        .shine {
            position: relative;
            overflow: hidden;
        }
        .shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                transparent 40%,
                rgba(255, 255, 255, 0.08) 50%,
                transparent 60%
            );
            animation: shine 4s ease-in-out infinite;
        }
        @keyframes shine {
            0%, 100% { transform: translateX(-50%) translateY(-50%) rotate(30deg); }
            50% { transform: translateX(50%) translateY(50%) rotate(30deg); }
        }

        .blob {
            border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%;
            animation: morph 8s ease-in-out infinite;
        }
        @keyframes morph {
            0%, 100% { border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%; }
            33% { border-radius: 70% 30% 46% 54% / 30% 29% 71% 70%; }
            66% { border-radius: 30% 70% 70% 30% / 58% 42% 58% 42%; }
        }

        .scroll-reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .scroll-reveal.revealed {
            opacity: 1;
            transform: translateY(0);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #1e1b4b; }
        ::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6366f1; }

        .feature-icon {
            transition: all 0.3s ease;
        }
        .group:hover .feature-icon {
            transform: scale(1.1) rotate(-5deg);
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800 bg-white" x-data="{ mobileMenu: false }">

    <!-- ═══ NAVBAR ═══ -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 bg-white/95 backdrop-blur-xl shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-purple-600 flex items-center justify-center shadow-lg shadow-brand-500/30 group-hover:shadow-brand-500/50 transition-all duration-300">
                        <span class="text-white font-bold text-lg">EF</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">
                        Emare <span class="gradient-text">Finance</span>
                    </span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="#ozellikler" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-brand-600 transition-all duration-300 hover:bg-brand-500/10">Özellikler</a>
                    <a href="#moduller" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-brand-600 transition-all duration-300 hover:bg-brand-500/10">Modüller</a>
                    <a href="#fiyatlar" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-brand-600 transition-all duration-300 hover:bg-brand-500/10">Fiyatlar</a>
                    <a href="#iletisim" class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-brand-600 transition-all duration-300 hover:bg-brand-500/10">İletişim</a>
                </div>

                <!-- CTA Buttons -->
                <div class="hidden md:flex items-center space-x-3">
                    <a href="/giris" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-brand-700 border-2 border-brand-200 hover:border-brand-500 hover:bg-brand-50 transition-all duration-300">
                        <i class="fas fa-sign-in-alt mr-1.5"></i> Giriş Yap
                    </a>
                    <a href="/kayit" class="px-6 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-brand-500 to-purple-600 text-white shadow-lg shadow-brand-500/30 hover:shadow-brand-500/50 hover:scale-105 transition-all duration-300">
                        Ücretsiz Dene
                    </a>
                </div>

                <!-- Mobile menu button -->
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-gray-700">
                    <i class="fas" :class="mobileMenu ? 'fa-xmark' : 'fa-bars'" style="font-size: 1.25rem"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden bg-white/95 backdrop-blur-xl border-t border-gray-100 shadow-xl">
            <div class="px-4 py-4 space-y-1">
                <a href="#ozellikler" @click="mobileMenu = false" class="block px-4 py-3 rounded-xl text-gray-700 hover:bg-brand-50 hover:text-brand-600 font-medium transition-all">Özellikler</a>
                <a href="#moduller" @click="mobileMenu = false" class="block px-4 py-3 rounded-xl text-gray-700 hover:bg-brand-50 hover:text-brand-600 font-medium transition-all">Modüller</a>
                <a href="#fiyatlar" @click="mobileMenu = false" class="block px-4 py-3 rounded-xl text-gray-700 hover:bg-brand-50 hover:text-brand-600 font-medium transition-all">Fiyatlar</a>
                <a href="#iletisim" @click="mobileMenu = false" class="block px-4 py-3 rounded-xl text-gray-700 hover:bg-brand-50 hover:text-brand-600 font-medium transition-all">İletişim</a>
                <div class="pt-3 border-t border-gray-100 space-y-2">
                    <a href="/giris" class="block px-4 py-3 rounded-xl text-center text-brand-700 border-2 border-brand-200 hover:bg-brand-50 font-semibold transition-all"><i class="fas fa-sign-in-alt mr-1.5"></i> Giriş Yap</a>
                    <a href="/kayit" class="block px-4 py-3 rounded-xl text-center text-white bg-gradient-to-r from-brand-500 to-purple-600 font-semibold shadow-lg transition-all">Ücretsiz Dene</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    @yield('content')

    <!-- ═══ FOOTER ═══ -->
    <footer class="relative bg-gray-950 text-gray-300 overflow-hidden">
        <!-- Decorative top border -->
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-brand-500/50 to-transparent"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <!-- Brand -->
                <div class="lg:col-span-1">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white font-bold text-lg">EF</span>
                        </div>
                        <span class="text-xl font-bold text-white">Emare Finance</span>
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed mb-6">
                        İşletmenizi geleceğe taşıyın. Bulut tabanlı POS ve finans yönetimi ile tüm iş süreçlerinizi tek platformdan yönetin.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-lg bg-white/10 hover:bg-brand-500/20 flex items-center justify-center text-gray-300 hover:text-brand-400 transition-all duration-300">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-white/10 hover:bg-brand-500/20 flex items-center justify-center text-gray-300 hover:text-brand-400 transition-all duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-white/10 hover:bg-brand-500/20 flex items-center justify-center text-gray-300 hover:text-brand-400 transition-all duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-white/10 hover:bg-brand-500/20 flex items-center justify-center text-gray-300 hover:text-brand-400 transition-all duration-300">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Ürün -->
                <div>
                    <h4 class="text-white font-semibold mb-6">Ürün</h4>
                    <ul class="space-y-3">
                        <li><a href="#ozellikler" class="text-sm hover:text-brand-400 transition-colors">Özellikler</a></li>
                        <li><a href="#moduller" class="text-sm hover:text-brand-400 transition-colors">Modüller</a></li>
                        <li><a href="#fiyatlar" class="text-sm hover:text-brand-400 transition-colors">Fiyatlandırma</a></li>
                        <li><a href="#" class="text-sm hover:text-brand-400 transition-colors">API Dokümantasyonu</a></li>
                        <li><a href="#" class="text-sm hover:text-brand-400 transition-colors">Güncellemeler</a></li>
                    </ul>
                </div>

                <!-- Şirket -->
                <div>
                    <h4 class="text-white font-semibold mb-6">Şirket</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm hover:text-brand-400 transition-colors">Hakkımızda</a></li>
                        <li><a href="#iletisim" class="text-sm hover:text-brand-400 transition-colors">İletişim</a></li>
                        <li><a href="#" class="text-sm hover:text-brand-400 transition-colors">Kariyer</a></li>
                        <li><a href="#" class="text-sm hover:text-brand-400 transition-colors">Blog</a></li>
                        <li><a href="#" class="text-sm hover:text-brand-400 transition-colors">Basın</a></li>
                    </ul>
                </div>

                <!-- Destek -->
                <div>
                    <h4 class="text-white font-semibold mb-6">Destek</h4>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm hover:text-brand-400 transition-colors">Yardım Merkezi</a></li>
                        <li><a href="#" class="text-sm hover:text-brand-400 transition-colors">Eğitim Videoları</a></li>
                        <li><a href="#" class="text-sm hover:text-brand-400 transition-colors">Gizlilik Politikası</a></li>
                        <li><a href="#" class="text-sm hover:text-brand-400 transition-colors">Kullanım Şartları</a></li>
                        <li><a href="#" class="text-sm hover:text-brand-400 transition-colors">KVKK</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} Emare Finance. Tüm hakları saklıdır.</p>
                <div class="flex items-center space-x-6">
                    <span class="flex items-center text-sm text-gray-400">
                        <i class="fas fa-shield-halved text-green-500 mr-2"></i>
                        256-bit SSL
                    </span>
                    <span class="flex items-center text-sm text-gray-400">
                        <i class="fas fa-lock text-green-500 mr-2"></i>
                        KVKK Uyumlu
                    </span>
                    <span class="flex items-center text-sm text-gray-400">
                        <i class="fas fa-server text-green-500 mr-2"></i>
                        Türkiye Sunucu
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll reveal script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));
        });
    </script>

    @yield('scripts')
</body>
</html>
