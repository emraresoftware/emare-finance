#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# Emare Finance — Zero-Downtime Deploy Script
# Güncelleme sırasında uygulama KAPANMAZ. Canlı güncelleme yapar.
#
# Yapı:
#   /var/www/emare-finance/
#   ├── releases/
#   │   ├── 20260301_143000/   ← eski release
#   │   └── 20260301_150000/   ← yeni release
#   ├── current → releases/20260301_150000  (symlink)
#   ├── shared/
#   │   ├── .env
#   │   ├── storage/
#   │   └── database/
#   └── repo/                   ← bare git repo (push ile deploy)
#
# Kullanım:
#   bash deploy-zero.sh              → Normal deploy
#   bash deploy-zero.sh --rollback   → Önceki sürüme dön
#   bash deploy-zero.sh --quick      → Sadece kod güncelle (npm skip)
#   bash deploy-zero.sh --status     → Aktif release ve geçmişi göster
# ═══════════════════════════════════════════════════════════════
set -euo pipefail

# ─── YAPILANDIRMA ───
BASE_DIR="${DEPLOY_DIR:-/var/www/emare-finance}"
RELEASES_DIR="$BASE_DIR/releases"
SHARED_DIR="$BASE_DIR/shared"
CURRENT_LINK="$BASE_DIR/current"
REPO_DIR="$BASE_DIR/repo"
REPO_URL="${REPO_URL:-}"
GIT_BRANCH="${GIT_BRANCH:-main}"
KEEP_RELEASES=${KEEP_RELEASES:-5}
PHP_FPM_SERVICE="${PHP_FPM_SERVICE:-php8.2-fpm}"
RELEASE_NAME="$(date +%Y%m%d_%H%M%S)"
RELEASE_DIR="$RELEASES_DIR/$RELEASE_NAME"
LOG_FILE="$BASE_DIR/deploy.log"
SKIP_NPM=false

# ─── RENKLER ───
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

log() { echo -e "${CYAN}[$(date '+%H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE" 2>/dev/null; }
success() { echo -e "${GREEN}✅ $1${NC}" | tee -a "$LOG_FILE" 2>/dev/null; }
warn() { echo -e "${YELLOW}⚠️  $1${NC}" | tee -a "$LOG_FILE" 2>/dev/null; }
error() { echo -e "${RED}❌ $1${NC}" | tee -a "$LOG_FILE" 2>/dev/null; }

# ═══════════════════════════════════════════════════════════════
# ROLLBACK FONKSİYONU
# ═══════════════════════════════════════════════════════════════
do_rollback() {
    echo ""
    echo -e "${YELLOW}═══════════════════════════════════════════════════${NC}"
    echo -e "${YELLOW}  🔄 Emare Finance — Rollback${NC}"
    echo -e "${YELLOW}═══════════════════════════════════════════════════${NC}"
    echo ""

    if [ ! -L "$CURRENT_LINK" ]; then
        error "Aktif release bulunamadı!"
        exit 1
    fi

    CURRENT_RELEASE=$(readlink -f "$CURRENT_LINK")
    CURRENT_BASENAME=$(basename "$CURRENT_RELEASE")

    # Önceki release'i bul
    PREVIOUS=$(ls -1d "$RELEASES_DIR"/*/ 2>/dev/null | sort -r | grep -v "$CURRENT_BASENAME" | head -1)

    if [ -z "$PREVIOUS" ]; then
        error "Geri dönülecek bir release bulunamadı!"
        exit 1
    fi

    PREV_BASENAME=$(basename "$PREVIOUS")
    log "Aktif release: $CURRENT_BASENAME"
    log "Geri dönülecek: $PREV_BASENAME"

    # Symlink'i değiştir
    ln -sfn "$PREVIOUS" "$CURRENT_LINK"

    # PHP-FPM'i yeniden yükle (graceful — bağlantı kesmez)
    if command -v systemctl &> /dev/null; then
        sudo systemctl reload "$PHP_FPM_SERVICE" 2>/dev/null || true
    fi

    # OPcache temizle
    if command -v cachetool &> /dev/null; then
        cachetool opcache:reset 2>/dev/null || true
    fi

    success "Rollback tamamlandı! → $PREV_BASENAME"
    echo ""
    exit 0
}

# ═══════════════════════════════════════════════════════════════
# STATUS FONKSİYONU
# ═══════════════════════════════════════════════════════════════
do_status() {
    echo ""
    echo -e "${BLUE}═══════════════════════════════════════════════════${NC}"
    echo -e "${BLUE}  📋 Emare Finance — Deploy Durumu${NC}"
    echo -e "${BLUE}═══════════════════════════════════════════════════${NC}"
    echo ""

    if [ -L "$CURRENT_LINK" ]; then
        CURRENT_RELEASE=$(readlink -f "$CURRENT_LINK")
        echo -e "${GREEN}🟢 Aktif Release:${NC} $(basename "$CURRENT_RELEASE")"
    else
        echo -e "${RED}🔴 Aktif release bulunamadı${NC}"
    fi

    echo ""
    echo -e "${CYAN}📦 Tüm Release'ler:${NC}"
    if [ -d "$RELEASES_DIR" ]; then
        for dir in $(ls -1d "$RELEASES_DIR"/*/ 2>/dev/null | sort -r); do
            DIRNAME=$(basename "$dir")
            if [ -L "$CURRENT_LINK" ] && [ "$(readlink -f "$CURRENT_LINK")" = "$(realpath "$dir")" ]; then
                echo -e "  ${GREEN}► $DIRNAME (AKTİF)${NC}"
            else
                echo -e "  ○ $DIRNAME"
            fi
        done
    else
        echo "  (henüz release yok)"
    fi

    echo ""
    echo -e "${CYAN}💾 Disk Kullanımı:${NC}"
    du -sh "$BASE_DIR" 2>/dev/null | awk '{print "  Toplam: " $1}'
    du -sh "$RELEASES_DIR" 2>/dev/null | awk '{print "  Releases: " $1}'
    du -sh "$SHARED_DIR" 2>/dev/null | awk '{print "  Shared: " $1}'

    # Sunucu durumu
    echo ""
    echo -e "${CYAN}🌐 Servis Durumları:${NC}"
    if command -v systemctl &> /dev/null; then
        for svc in nginx "$PHP_FPM_SERVICE" supervisor; do
            STATUS=$(systemctl is-active "$svc" 2>/dev/null || echo "bilinmiyor")
            if [ "$STATUS" = "active" ]; then
                echo -e "  ${GREEN}● $svc: aktif${NC}"
            else
                echo -e "  ${RED}● $svc: $STATUS${NC}"
            fi
        done
    fi

    echo ""
    exit 0
}

# ═══════════════════════════════════════════════════════════════
# ARGÜMAN PARSE
# ═══════════════════════════════════════════════════════════════
for arg in "$@"; do
    case $arg in
        --rollback) do_rollback ;;
        --status) do_status ;;
        --quick) SKIP_NPM=true ;;
    esac
done

# ═══════════════════════════════════════════════════════════════
# ANA DEPLOY
# ═══════════════════════════════════════════════════════════════
DEPLOY_START=$(date +%s)

echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  🚀 Emare Finance — Zero-Downtime Deploy${NC}"
echo -e "${GREEN}  📁 Hedef: $BASE_DIR${NC}"
echo -e "${GREEN}  🏷️  Release: $RELEASE_NAME${NC}"
echo -e "${GREEN}  ⏰ Başlangıç: $(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo ""

# ─── 1. DİZİN YAPISI ───
log "[1/12] Dizin yapısı hazırlanıyor..."
mkdir -p "$RELEASES_DIR" "$SHARED_DIR/storage/app/public" \
    "$SHARED_DIR/storage/framework/cache/data" \
    "$SHARED_DIR/storage/framework/sessions" \
    "$SHARED_DIR/storage/framework/views" \
    "$SHARED_DIR/storage/logs" \
    "$SHARED_DIR/database"

# shared .env yoksa oluştur
if [ ! -f "$SHARED_DIR/.env" ]; then
    warn ".env bulunamadı. Lütfen $SHARED_DIR/.env dosyasını oluşturun!"
    if [ -f "$BASE_DIR/.env.production" ]; then
        cp "$BASE_DIR/.env.production" "$SHARED_DIR/.env"
        success ".env.production'dan kopyalandı"
    fi
fi

# shared database.sqlite yoksa oluştur
if [ ! -f "$SHARED_DIR/database/database.sqlite" ]; then
    touch "$SHARED_DIR/database/database.sqlite"
    log "Yeni SQLite veritabanı oluşturuldu"
fi

# ─── 2. KOD ÇEK ───
log "[2/12] Kod çekiliyor..."
if [ -d "$REPO_DIR" ]; then
    # Bare repo'dan kopyala
    git clone --depth 1 --branch "$GIT_BRANCH" "file://$REPO_DIR" "$RELEASE_DIR" 2>/dev/null || {
        # Alternatif: working copy'den rsync
        if [ -d "$BASE_DIR/.git" ]; then
            git -C "$BASE_DIR" pull origin "$GIT_BRANCH" 2>/dev/null || true
            rsync -a --exclude='.git' --exclude='node_modules' --exclude='vendor' \
                --exclude='storage' --exclude='.env' "$BASE_DIR/" "$RELEASE_DIR/"
        fi
    }
elif [ -n "$REPO_URL" ]; then
    git clone --depth 1 --branch "$GIT_BRANCH" "$REPO_URL" "$RELEASE_DIR"
elif [ -d "$BASE_DIR/.git" ]; then
    # Mevcut git repo'dan
    cd "$BASE_DIR"
    git fetch origin "$GIT_BRANCH" 2>/dev/null || true
    git archive "$GIT_BRANCH" 2>/dev/null | tar -x -C "$RELEASE_DIR" || {
        rsync -a --exclude='.git' --exclude='node_modules' --exclude='vendor' \
            --exclude='storage' --exclude='.env' \
            --exclude='releases' --exclude='shared' --exclude='current' \
            "$BASE_DIR/" "$RELEASE_DIR/"
    }
else
    # Git yok — doğrudan kopyala (geliştirme ortamı veya scp ile)
    log "Git bulunamadı. Mevcut dosyalar kopyalanıyor..."
    rsync -a --exclude='.git' --exclude='node_modules' --exclude='vendor' \
        --exclude='storage' --exclude='.env' \
        --exclude='releases' --exclude='shared' --exclude='current' \
        --exclude='repo' --exclude='deploy.log' \
        "$BASE_DIR/" "$RELEASE_DIR/"
fi

cd "$RELEASE_DIR"

# ─── 3. SHARED SYMLINK'LER ───
log "[3/12] Shared symlink'ler oluşturuluyor..."

# Storage → shared/storage
rm -rf "$RELEASE_DIR/storage"
ln -sfn "$SHARED_DIR/storage" "$RELEASE_DIR/storage"

# .env → shared/.env
rm -f "$RELEASE_DIR/.env"
ln -sfn "$SHARED_DIR/.env" "$RELEASE_DIR/.env"

# database.sqlite → shared/database/database.sqlite
if [ -f "$RELEASE_DIR/database/database.sqlite" ]; then
    rm -f "$RELEASE_DIR/database/database.sqlite"
fi
mkdir -p "$RELEASE_DIR/database"
ln -sfn "$SHARED_DIR/database/database.sqlite" "$RELEASE_DIR/database/database.sqlite"

# ─── 4. COMPOSER ───
log "[4/12] Composer bağımlılıkları yükleniyor..."
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --quiet
else
    php composer.phar install --no-dev --optimize-autoloader --no-interaction --prefer-dist --quiet 2>/dev/null || true
fi

# ─── 5. NPM BUILD ───
if [ "$SKIP_NPM" = false ] && [ -f package.json ]; then
    log "[5/12] Frontend derleniyor..."
    npm ci --production=false --silent 2>/dev/null || npm install --silent
    npm run build 2>/dev/null || true
    # node_modules temizle (disk tasarrufu)
    rm -rf node_modules
else
    log "[5/12] NPM atlanıyor (--quick modu veya package.json yok)"
fi

# ─── 6. MİGRATİON ───
log "[6/12] Migration çalıştırılıyor..."
php artisan migrate --force 2>/dev/null || true

# ─── 7. CACHE OPTİMİZASYON ───
log "[7/12] Cache optimize ediliyor..."
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true
php artisan event:cache 2>/dev/null || true

# ─── 8. STORAGE LINK ───
log "[8/12] Storage bağlantısı..."
php artisan storage:link 2>/dev/null || true

# ═══════════════════════════════════════════════════════════════
# 9. ATOMIK GEÇIŞ — SİHİR BURADA!
# Tek bir symlink değişikliği ile yeni sürüme geçiş yapılır.
# Bu işlem ~1ms sürer, kullanıcı hiçbir kesinti yaşamaz.
# ═══════════════════════════════════════════════════════════════
log "[9/12] ⚡ Atomik geçiş yapılıyor..."
ln -sfn "$RELEASE_DIR" "${CURRENT_LINK}.tmp"
mv -Tf "${CURRENT_LINK}.tmp" "$CURRENT_LINK" 2>/dev/null || {
    # macOS uyumluluğu (mv -T yok)
    rm -f "$CURRENT_LINK"
    ln -sfn "$RELEASE_DIR" "$CURRENT_LINK"
}
success "Symlink güncellendi: current → $RELEASE_NAME"

# ─── 10. PHP-FPM RELOAD (GRACEFUL) ───
log "[10/12] PHP-FPM yeniden yükleniyor (graceful)..."
if command -v systemctl &> /dev/null; then
    sudo systemctl reload "$PHP_FPM_SERVICE" 2>/dev/null || {
        warn "PHP-FPM reload başarısız — restart deneniyor..."
        sudo systemctl restart "$PHP_FPM_SERVICE" 2>/dev/null || true
    }
fi

# OPcache temizle
if command -v cachetool &> /dev/null; then
    cachetool opcache:reset 2>/dev/null || true
fi

# ─── 11. QUEUE RESTART ───
log "[11/12] Queue worker yeniden başlatılıyor..."
php artisan queue:restart 2>/dev/null || true
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart emare-queue:* 2>/dev/null || true
    sudo supervisorctl restart emare-scheduler 2>/dev/null || true
fi

# ─── 12. ESKİ RELEASE'LERİ TEMİZLE ───
log "[12/12] Eski release'ler temizleniyor (son $KEEP_RELEASES korunuyor)..."
cd "$RELEASES_DIR"
RELEASE_COUNT=$(ls -1d */ 2>/dev/null | wc -l)
if [ "$RELEASE_COUNT" -gt "$KEEP_RELEASES" ]; then
    DELETE_COUNT=$((RELEASE_COUNT - KEEP_RELEASES))
    ls -1d */ | sort | head -n "$DELETE_COUNT" | while read -r old_release; do
        log "  Siliniyor: $old_release"
        rm -rf "$RELEASES_DIR/$old_release"
    done
    success "$DELETE_COUNT eski release silindi"
fi

# ─── DEPLOY SONUCU ───
DEPLOY_END=$(date +%s)
DEPLOY_DURATION=$((DEPLOY_END - DEPLOY_START))

echo ""
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  ✅ Zero-Downtime Deploy Tamamlandı!${NC}"
echo -e "${GREEN}  🏷️  Release: $RELEASE_NAME${NC}"
echo -e "${GREEN}  ⏱️  Süre: ${DEPLOY_DURATION} saniye${NC}"
echo -e "${GREEN}  ⏰ Bitiş: $(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${GREEN}═══════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "${CYAN}Kullanışlı komutlar:${NC}"
echo "  bash deploy-zero.sh --status     → Deploy durumunu gör"
echo "  bash deploy-zero.sh --rollback   → Önceki sürüme dön"
echo "  bash deploy-zero.sh --quick      → Hızlı deploy (npm atla)"
echo ""
