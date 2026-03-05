#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# Emare Finance — Geliştirme Sunucusu Başlatıcı
# ═══════════════════════════════════════════════════════════════
set -e

cd "$(dirname "$0")"

echo "🚀 Emare Finance başlatılıyor..."
echo ""

# 1) Port temizliği
if lsof -ti :8000 > /dev/null 2>&1; then
    echo "⚠️  Port 8000 meşgul — temizleniyor..."
    lsof -ti :8000 | xargs kill -9 2>/dev/null || true
    sleep 1
fi

# 2) .env kontrolü
if [ ! -f .env ]; then
    echo "📋 .env dosyası oluşturuluyor..."
    cp .env.example .env
    php artisan key:generate
fi

# 3) Veritabanı kontrolü
if [ ! -f database/database.sqlite ]; then
    echo "🗄️  Veritabanı oluşturuluyor..."
    touch database/database.sqlite
fi

# 4) Migration
echo "🔄 Migration kontrolü..."
php artisan migrate --force 2>/dev/null || true

# 5) Demo kullanıcı kontrolü
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null)
if [ "$USER_COUNT" = "0" ]; then
    echo "👤 Demo kullanıcılar oluşturuluyor..."
    php artisan db:seed --class=DemoUserSeeder --force 2>/dev/null || true
fi

# 6) Cache temizlik
echo "🧹 Cache temizleniyor..."
php artisan config:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

echo ""
echo "═══════════════════════════════════════════════════"
echo "  ✅ Emare Finance hazır!"
echo "  🌐 http://127.0.0.1:8000"
echo "  👤 admin@emarefinance.com / password"
echo "  👤 kasiyer@emarefinance.com / password"
echo "═══════════════════════════════════════════════════"
echo ""

# 7) Sunucuyu başlat
php artisan serve --host=127.0.0.1 --port=8000
