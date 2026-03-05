#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# Emare Finance — Sunucu İlk Kurulum Scripti (Zero-Downtime)
# Ubuntu 22.04/24.04 için hazırlanmıştır
# Kullanım: sudo bash server-setup.sh
#
# Yapı:
#   /var/www/emare-finance/
#   ├── releases/          ← her deploy yeni klasör
#   ├── current → releases/xxx  (symlink)
#   ├── shared/            ← .env, storage, database
#   ├── repo/              ← bare git repo (push deploy)
#   ├── deploy-zero.sh
#   ├── health-check.sh
#   └── deploy.log
# ═══════════════════════════════════════════════════════════════
set -e

if [ "$EUID" -ne 0 ]; then
    echo "❌ Bu script root olarak çalıştırılmalıdır: sudo bash server-setup.sh"
    exit 1
fi

DOMAIN="${1:-emarefinance.com}"
APP_DIR="/var/www/emare-finance"
APP_USER="www-data"
PHP_VERSION="8.2"

echo ""
echo "═══════════════════════════════════════════════════"
echo "  🚀 Emare Finance — Sunucu Kurulumu (Zero-Downtime)"
echo "  📁 Dizin: $APP_DIR"
echo "  🌐 Domain: $DOMAIN"
echo "═══════════════════════════════════════════════════"
echo ""

# ─── 1. Sistem güncelleme ───
echo "📦 [1/11] Sistem güncelleniyor..."
apt update && apt upgrade -y

# ─── 2. Gerekli paketler ───
echo "📦 [2/11] Paketler yükleniyor..."
apt install -y \
    nginx \
    php${PHP_VERSION}-fpm php${PHP_VERSION}-cli php${PHP_VERSION}-common \
    php${PHP_VERSION}-mysql php${PHP_VERSION}-sqlite3 php${PHP_VERSION}-pgsql \
    php${PHP_VERSION}-mbstring php${PHP_VERSION}-xml php${PHP_VERSION}-curl \
    php${PHP_VERSION}-zip php${PHP_VERSION}-gd php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-intl php${PHP_VERSION}-readline php${PHP_VERSION}-tokenizer \
    php${PHP_VERSION}-opcache \
    supervisor \
    git unzip curl rsync \
    certbot python3-certbot-nginx \
    logrotate

# ─── 3. Composer yükleme ───
echo "📦 [3/11] Composer yükleniyor..."
if ! command -v composer &> /dev/null; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

# ─── 4. Node.js yükleme ───
echo "📦 [4/11] Node.js yükleniyor..."
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt install -y nodejs
fi

# ─── 5. Dizin yapısını oluştur ───
echo "📁 [5/11] Zero-downtime dizin yapısı oluşturuluyor..."
mkdir -p "$APP_DIR/releases"
mkdir -p "$APP_DIR/shared/storage/app/public"
mkdir -p "$APP_DIR/shared/storage/framework/cache/data"
mkdir -p "$APP_DIR/shared/storage/framework/sessions"
mkdir -p "$APP_DIR/shared/storage/framework/views"
mkdir -p "$APP_DIR/shared/storage/logs"
mkdir -p "$APP_DIR/shared/database"
mkdir -p /var/log/php-fpm

# Shared database
touch "$APP_DIR/shared/database/database.sqlite"

# İzinler
chown -R $APP_USER:$APP_USER "$APP_DIR/shared"
chmod -R 775 "$APP_DIR/shared/storage"

# ─── 6. Git Bare Repo (push deploy) ───
echo "📦 [6/11] Git bare repo oluşturuluyor..."
if [ ! -d "$APP_DIR/repo" ]; then
    git init --bare "$APP_DIR/repo"

    # Post-receive hook kopyala
    if [ -f "$APP_DIR/git-hooks/post-receive" ]; then
        cp "$APP_DIR/git-hooks/post-receive" "$APP_DIR/repo/hooks/post-receive"
    else
        cat > "$APP_DIR/repo/hooks/post-receive" << 'HOOK'
#!/bin/bash
BASE_DIR="/var/www/emare-finance"
while read oldrev newrev refname; do
    BRANCH=$(echo "$refname" | sed 's|refs/heads/||')
    if [ "$BRANCH" = "main" ]; then
        echo "🚀 main branch push — deploy başlatılıyor..."
        nohup bash "$BASE_DIR/deploy-zero.sh" >> "$BASE_DIR/deploy.log" 2>&1 &
        echo "✅ Deploy başlatıldı (PID: $!)"
    fi
done
HOOK
    fi
    chmod +x "$APP_DIR/repo/hooks/post-receive"
    echo "  ✅ Bare repo: $APP_DIR/repo"
    echo "  📌 Yerel: git remote add production ssh://root@SUNUCU_IP$APP_DIR/repo"
fi

# ─── 7. PHP-FPM yapılandırması ───
echo "⚙️  [7/11] PHP-FPM yapılandırılıyor..."
if [ -f "$APP_DIR/php-fpm.conf" ]; then
    cp "$APP_DIR/php-fpm.conf" "/etc/php/${PHP_VERSION}/fpm/pool.d/emare.conf"
else
    cat > "/etc/php/${PHP_VERSION}/fpm/pool.d/emare.conf" << FPMEOF
[emare]
user = www-data
group = www-data
listen = /var/run/php/php${PHP_VERSION}-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
pm = dynamic
pm.max_children = 20
pm.start_servers = 5
pm.min_spare_servers = 2
pm.max_spare_servers = 8
pm.max_requests = 1000
request_terminate_timeout = 300
php_admin_value[realpath_cache_size] = 0
php_admin_value[realpath_cache_ttl] = 0
php_admin_value[opcache.revalidate_freq] = 0
php_admin_value[opcache.validate_timestamps] = 1
php_admin_value[upload_max_filesize] = 50M
php_admin_value[post_max_size] = 50M
php_admin_value[memory_limit] = 256M
FPMEOF
fi
systemctl restart php${PHP_VERSION}-fpm

# ─── 8. Nginx yapılandırması ───
echo "🌐 [8/11] Nginx yapılandırılıyor..."
if [ -f "$APP_DIR/nginx.conf" ]; then
    cp "$APP_DIR/nginx.conf" "/etc/nginx/sites-available/$DOMAIN"
else
    echo "⚠️  nginx.conf bulunamadı. Lütfen manuel kopyalayın."
fi
ln -sf "/etc/nginx/sites-available/$DOMAIN" "/etc/nginx/sites-enabled/$DOMAIN"
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl restart nginx

# ─── 9. SSL sertifikası ───
echo "🔒 [9/11] SSL sertifikası alınıyor..."
certbot --nginx -d "$DOMAIN" -d "www.$DOMAIN" --non-interactive --agree-tos \
    --email "admin@$DOMAIN" || echo "⚠️  SSL sonra yapılandırılabilir: certbot --nginx -d $DOMAIN"

# ─── 10. Supervisor yapılandırması ───
echo "⚙️  [10/11] Supervisor yapılandırılıyor..."
if [ -f "$APP_DIR/supervisor.conf" ]; then
    cp "$APP_DIR/supervisor.conf" "/etc/supervisor/conf.d/emare-finance.conf"
fi
supervisorctl reread
supervisorctl update

# ─── 11. Logrotate ───
echo "📋 [11/11] Logrotate yapılandırılıyor..."
cat > "/etc/logrotate.d/emare-finance" << 'LOGEOF'
/var/www/emare-finance/shared/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0664 www-data www-data
    sharedscripts
    postrotate
        /usr/bin/supervisorctl restart emare:* > /dev/null 2>&1 || true
    endscript
}
LOGEOF

# ─── Scriptleri kopyala ───
for script in deploy-zero.sh health-check.sh keep-alive.sh; do
    if [ -f "$APP_DIR/$script" ]; then
        chmod +x "$APP_DIR/$script"
    fi
done

# ─── .env kontrolü ───
if [ ! -f "$APP_DIR/shared/.env" ]; then
    if [ -f "$APP_DIR/.env.production" ]; then
        cp "$APP_DIR/.env.production" "$APP_DIR/shared/.env"
        echo "📋 .env.production → shared/.env kopyalandı"
        echo "⚠️  Lütfen $APP_DIR/shared/.env dosyasını düzenleyin!"
    else
        echo "⚠️  Lütfen $APP_DIR/shared/.env dosyasını oluşturun!"
    fi
fi

# ─── Özet ───
echo ""
echo "═══════════════════════════════════════════════════════════"
echo "  ✅ Sunucu Kurulumu Tamamlandı! (Zero-Downtime)"
echo ""
echo "  📁 Uygulama:  $APP_DIR"
echo "  🌐 Domain:    $DOMAIN"
echo "  📦 Releases:  $APP_DIR/releases/"
echo "  🔗 Current:   $APP_DIR/current → (aktif release)"
echo "  📂 Shared:    $APP_DIR/shared/ (.env, storage, db)"
echo "  📨 Git Repo:  $APP_DIR/repo/"
echo ""
echo "  📝 Sonraki Adımlar:"
echo "  ─────────────────────────────────────────────"
echo "  1. .env dosyasını düzenleyin:"
echo "     nano $APP_DIR/shared/.env"
echo ""
echo "  2. İlk deploy'u çalıştırın:"
echo "     cd $APP_DIR && bash deploy-zero.sh"
echo ""
echo "  3. Git push deploy ayarlayın (yerel makinede):"
echo "     git remote add production ssh://root@SUNUCU_IP$APP_DIR/repo"
echo "     git push production main"
echo ""
echo "  4. GitHub Webhook ayarlayın:"
echo "     URL: https://$DOMAIN/deploy/webhook"
echo "     Secret: .env'deki DEPLOY_WEBHOOK_SECRET"
echo "     Events: Push"
echo ""
echo "  Kullanışlı Komutlar:"
echo "  ─────────────────────────────────────────────"
echo "  bash deploy-zero.sh              → Yeni deploy"
echo "  bash deploy-zero.sh --rollback   → Geri al"
echo "  bash deploy-zero.sh --status     → Durum"
echo "  bash deploy-zero.sh --quick      → Hızlı deploy"
echo "  supervisorctl status emare:*     → Servis durumu"
echo "  tail -f $APP_DIR/deploy.log      → Deploy logu"
echo "═══════════════════════════════════════════════════════════"
echo ""
