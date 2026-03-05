# Technical Decisions Log — Emare Hub

Bu dosya tüm mimari ve operasyonel kararların tek kaynak kaydıdır.
Her karar geri dönülebilir ve izlenebilir olmalıdır.

---

## D-001 — Modüler Python Çekirdeği
- **Tarih:** 2026-02-28
- **Karar:** Emare Hub çekirdeği Python ile yazılacak. Her iş birimi bağımsız modül olarak `modules/` altında yaşayacak.
- **Gerekçe:** Hızlı prototipleme, AI kütüphaneleri ekosistemi, ekibin Python bilgisi.

## D-002 — JSON Kalıcı Kayıt (Persistence)
- **Tarih:** 2026-02-28
- **Karar:** Modül registry bilgileri `data/registry.json` dosyasında tutulacak.
- **Gerekçe:** İlk aşamada SQLite/PostgreSQL karmaşıklığı gereksiz. Modül sayısı 50'yi geçerse veritabanına geçiş değerlendirilecek.

## D-003 — DevM Entegrasyonu Sarmalama Yöntemi
- **Tarih:** 2026-02-28
- **Karar:** ~~DevM (Node.js) projesi `modules/devm/` altına yerleştirilecek ve Python `bridge.py` köprüsü ile Emare Hub'a bağlanacak.~~ **İPTAL — Bkz: D-007**
- **Gerekçe:** DevM kendi başına çalışabilir kalmalı. Sarmalama, bağımlılıkları minimumda tutar.
- **Durum:** ❌ İptal (D-007 ile değiştirildi)

## D-004 — Talimat Sistemi Python Portu
- **Tarih:** 2026-02-28
- **Karar:** DevM'nin `TALIMATLAR.md → otomatik uygulama` sistemi Python'a taşınacak. Her modülün kendi `TALIMATLAR.md` dosyası olabilecek.
- **Gerekçe:** Emare Hub Python tabanlı; Node.js bağımlılığı olmadan talimat sistemi çalışmalı.

## D-005 — Config Hiyerarşisi
- **Tarih:** 2026-02-28
- **Karar:** Gizli bilgiler `.env`'de, genel ayarlar `config.yaml`'da tutulacak. `.env` asla commit edilmeyecek.
- **Gerekçe:** Güvenlik ve yapılandırma ayrımı.

## D-006 — Manifest Standardı
- **Tarih:** 2026-02-28
- **Karar:** Her modül kök dizininde `manifest.json` dosyası bulunduracak. Bu dosya modülün kimlik kartı (isim, versiyon, runtime, bağımlılıklar, health check).
- **Gerekçe:** Heterojen modüller (Python, Node.js) arasında ortak tanımlama dili gerekli.

## D-007 — DevM Çekirdeğe Taşıma (D-003'ü İptal Eder)
- **Tarih:** 2026-02-28
- **Karar:** DevM `modules/devm/` altından çıkarılıp çekirdek katmana taşındı. `devm_bridge.py` proje kökünde yaşar, `emare_core.py` başlatılırken DevM otomatik olarak bağlanır (`hub.devm`). DevM artık bir modül olarak kaydedilmez.
- **Gerekçe:** DevM bir iş modülü değil — otonom yazılımcılar platformu, yani sistemin beyni. Modüller DevM tarafından üretilecek/yönetilecek. Beyin, kol ile aynı seviyede değil, çekirdekte olmalı.
- **Etki:** `modules/devm/` silindi, `devm_bridge.py` oluşturuldu, `emare_core.py` güncellendi, dashboard'da DevM çekirdek olarak gösteriliyor.
