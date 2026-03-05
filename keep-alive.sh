#!/bin/bash
# ══════════════════════════════════════════════════════════════
# Emare Finance — Keep Alive (Watchdog) Script
# Sunucuyu sürekli ayakta tutar. Düşerse otomatik yeniden başlatır.
# Kullanım: nohup ./keep-alive.sh &
# ══════════════════════════════════════════════════════════════

APP_DIR="$(cd "$(dirname "$0")" && pwd)"
PORT=8000
HOST=127.0.0.1
LOG_FILE="$APP_DIR/storage/logs/server.log"
CHECK_INTERVAL=5  # saniye

echo "🚀 Emare Finance Keep-Alive başlatıldı — $(date)"
echo "   Kontrol aralığı: ${CHECK_INTERVAL}s | Port: $PORT"

start_server() {
    echo "$(date) — Sunucu başlatılıyor..."
    cd "$APP_DIR"
    nohup php artisan serve --host=$HOST --port=$PORT >> "$LOG_FILE" 2>&1 &
    SERVER_PID=$!
    echo "$(date) — Sunucu PID: $SERVER_PID"
    sleep 2
}

# İlk kontrol — zaten çalışıyor mu?
if lsof -ti :$PORT > /dev/null 2>&1; then
    echo "$(date) — Sunucu zaten çalışıyor (PID: $(lsof -ti :$PORT))"
else
    start_server
fi

# Sonsuz döngü — sürekli kontrol
while true; do
    sleep $CHECK_INTERVAL

    # HTTP kontrolü
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 3 http://$HOST:$PORT/giris 2>/dev/null)

    if [ "$HTTP_CODE" != "200" ] && [ "$HTTP_CODE" != "302" ]; then
        echo "$(date) — ⚠️  Sunucu yanıt vermiyor (HTTP: $HTTP_CODE). Yeniden başlatılıyor..."

        # Eski süreçleri temizle
        lsof -ti :$PORT | xargs kill -9 2>/dev/null
        sleep 1

        start_server

        # Tekrar kontrol
        sleep 2
        HTTP_CHECK=$(curl -s -o /dev/null -w "%{http_code}" --connect-timeout 3 http://$HOST:$PORT/giris 2>/dev/null)
        if [ "$HTTP_CHECK" = "200" ] || [ "$HTTP_CHECK" = "302" ]; then
            echo "$(date) — ✅ Sunucu başarıyla yeniden başlatıldı"
        else
            echo "$(date) — ❌ Sunucu başlatılamadı! HTTP: $HTTP_CHECK"
        fi
    fi
done
