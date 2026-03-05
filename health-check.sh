#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# Emare Finance — Health Check (Watchdog)
# Supervisor tarafından yönetilir. Servisleri izler ve düşerse yeniden başlatır.
# ═══════════════════════════════════════════════════════════════
set -u

BASE_DIR="/var/www/emare-finance"
CHECK_INTERVAL=30
FAIL_THRESHOLD=3
DOMAIN="emarefinance.com"
ALERT_EMAIL="admin@emarefinance.com"
LOG_PREFIX="[health-check]"

fail_count=0

log() { echo "$(date '+%Y-%m-%d %H:%M:%S') $LOG_PREFIX $1"; }

send_alert() {
    local subject="$1"
    local body="$2"
    if command -v mail &> /dev/null; then
        echo "$body" | mail -s "[Emare Finance] $subject" "$ALERT_EMAIL" 2>/dev/null || true
    fi
    # Slack webhook varsa
    if [ -n "${SLACK_WEBHOOK:-}" ]; then
        curl -s -X POST "$SLACK_WEBHOOK" \
            -H 'Content-type: application/json' \
            -d "{\"text\":\"🚨 *Emare Finance Alert*\n$subject\n$body\"}" 2>/dev/null || true
    fi
}

restart_service() {
    local service="$1"
    log "⚠️  $service yeniden başlatılıyor..."
    systemctl restart "$service" 2>/dev/null
    sleep 3
    if systemctl is-active --quiet "$service"; then
        log "✅ $service başarıyla yeniden başlatıldı"
        send_alert "$service yeniden başlatıldı" "Servis otomatik olarak kurtarıldı."
    else
        log "❌ $service başlatılamadı!"
        send_alert "KRİTİK: $service başlatılamadı!" "Manuel müdahale gerekli. Sunucu: $(hostname)"
    fi
}

log "🚀 Health check başlatıldı (aralık: ${CHECK_INTERVAL}s, eşik: ${FAIL_THRESHOLD})"

while true; do
    sleep "$CHECK_INTERVAL"

    # ─── 1. HTTP Kontrolü ───
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 5 --max-time 10 \
        "http://127.0.0.1/health" 2>/dev/null || echo "000")

    if [ "$HTTP_CODE" = "200" ]; then
        if [ "$fail_count" -gt 0 ]; then
            log "✅ Uygulama tekrar yanıt veriyor (önceki hata sayısı: $fail_count)"
            fail_count=0
        fi
    else
        fail_count=$((fail_count + 1))
        log "⚠️  HTTP yanıt kodu: $HTTP_CODE (hata: $fail_count/$FAIL_THRESHOLD)"

        if [ "$fail_count" -ge "$FAIL_THRESHOLD" ]; then
            log "🔴 Eşik aşıldı! Servisler yeniden başlatılıyor..."

            # PHP-FPM kontrol
            if ! systemctl is-active --quiet php8.2-fpm; then
                restart_service php8.2-fpm
            else
                # Reload dene
                systemctl reload php8.2-fpm 2>/dev/null || restart_service php8.2-fpm
            fi

            # Nginx kontrol
            if ! systemctl is-active --quiet nginx; then
                restart_service nginx
            else
                nginx -t 2>/dev/null && systemctl reload nginx 2>/dev/null
            fi

            fail_count=0
            send_alert "Servisler yeniden başlatıldı" "HTTP kodu: $HTTP_CODE — Otomatik kurtarma uygulandı."
        fi
    fi

    # ─── 2. Disk Kontrolü ───
    DISK_USAGE=$(df -h "$BASE_DIR" 2>/dev/null | awk 'NR==2 {print $5}' | tr -d '%')
    if [ -n "$DISK_USAGE" ] && [ "$DISK_USAGE" -gt 90 ]; then
        log "⚠️  Disk kullanımı yüksek: %${DISK_USAGE}"
        if [ "$DISK_USAGE" -gt 95 ]; then
            send_alert "KRİTİK: Disk dolu (%${DISK_USAGE})" "Acil alan temizliği gerekli!"
            # Eski logları temizle
            find "$BASE_DIR/shared/storage/logs" -name "*.log" -size +100M -exec truncate -s 10M {} \; 2>/dev/null
        fi
    fi

    # ─── 3. Supervisor Kontrolü ───
    if command -v supervisorctl &> /dev/null; then
        QUEUE_STATUS=$(supervisorctl status emare-queue:emare-queue_00 2>/dev/null | awk '{print $2}')
        if [ "$QUEUE_STATUS" != "RUNNING" ]; then
            log "⚠️  Queue worker durumu: $QUEUE_STATUS — yeniden başlatılıyor..."
            supervisorctl restart emare:* 2>/dev/null || true
        fi
    fi

    # ─── 4. PHP-FPM Process Kontrolü ───
    FPM_PROCS=$(pgrep -c php-fpm 2>/dev/null || echo "0")
    if [ "$FPM_PROCS" -lt 2 ]; then
        log "⚠️  PHP-FPM process sayısı düşük: $FPM_PROCS"
        restart_service php8.2-fpm
    fi

done
