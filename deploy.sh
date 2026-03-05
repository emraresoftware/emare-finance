#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# Emare Finance — Deploy Script (Uyumluluk Katmanı)
# Bu script deploy-zero.sh'e yönlendirir.
# Eski deploy.sh kullanımına alışkın olanlar için geriye uyumlu.
# ═══════════════════════════════════════════════════════════════

APP_DIR="$(cd "$(dirname "$0")" && pwd)"

# Zero-downtime script varsa onu kullan
if [ -f "$APP_DIR/deploy-zero.sh" ]; then
    echo "🔄 deploy-zero.sh kullanılıyor (zero-downtime deploy)..."
    exec bash "$APP_DIR/deploy-zero.sh" "$@"
fi

# Fallback: basit deploy (development veya zero-downtime yoksa)
set -e
cd "$APP_DIR"

echo ""
echo "🚀 Emare Finance Deploy başlıyor (basit mod)..."
echo "📁 Dizin: $APP_DIR"
echo "⏰ Tarih: $(date '+%Y-%m-%d %H:%M:%S')"
echo ""

# 1. Maintenance modu
echo "🔧 [1/10] Bakım modu aktif ediliyor..."
php artisan down --retry=60 --refresh=5 2>/dev/null || true

# 2. Git pull
echo "📥 [2/10] Kod güncelleniyor..."
if [ -d .git ]; then
    git pull origin main 2>/dev/null || git pull origin master 2>/dev/null || echo "⚠️  Git pull atlandı"
else
    echo "⚠️  Git repo değil, atlanıyor"
fi

# 3. Composer
echo "📦 [3/10] Composer bağımlılıkları..."
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# 4. NPM build
echo "🎨 [4/10] Frontend derleniyor..."
if [ -f package.json ]; then
    npm ci --production=false 2>/dev/null || npm install
    npm run build
fi

# 5. Migration
echo "🗄️  [5/10] Migration çalıştırılıyor..."
php artisan migrate --force

# 6. Cache optimizasyonu
echo "⚡ [6/10] Cache optimize ediliyor..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 7. Storage link
echo "🔗 [7/10] Storage bağlantısı..."
php artisan storage:link 2>/dev/null || true

# 8. Queue restart
echo "🔄 [8/10] Queue worker yeniden başlatılıyor..."
php artisan queue:restart 2>/dev/null || true

# 9. Permissions
echo "🔐 [9/10] Dosya izinleri ayarlanıyor..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 10. PHP-FPM Reload (graceful)
echo "🔄 [10/10] PHP-FPM yeniden yükleniyor..."
if command -v systemctl &> /dev/null; then
    sudo systemctl reload php8.2-fpm 2>/dev/null || true
fi

# Bakım modunu kaldır
echo "🌐 Bakım modu kaldırılıyor..."
php artisan up

echo ""
echo "═══════════════════════════════════════════════════"
echo "  ✅ Deploy tamamlandı!"
echo "  ⏰ $(date '+%Y-%m-%d %H:%M:%S')"
echo "  💡 Zero-downtime deploy için: bash deploy-zero.sh"
echo "═══════════════════════════════════════════════════"
echo ""
