# Emare Hub — Session Context (Oturum Hafızası)

Bu dosya, farklı AI oturumlarının (Copilot, Gemini vb.) aynı bağlamı koruması için merkezi hafızadır.
Her yeni oturum başında bu dosya okunmalıdır.

---

## Vizyon

Emare Hub, tüm yazılım modüllerini tek çatı altında üreten, yöneten ve izleyen bir **yazılım fabrikası** altyapısıdır. DevM platformu ile birleşerek çoklu AI + çoklu IDE otomatik yazılım üretim kapasitesine ulaşacak.

## Mevcut Durum (v0.3)

- ✅ Ana Üs (`emare_core.py`) çalışıyor — loglama, JSON persistence, register/unregister, config, dashboard
- ✅ Robot Kol (`factory_worker.py`) çalışıyor — modül iskeleti + manifest + talimat + README üretiyor
- ✅ DevM **çekirdek katmanda** bağlı (`devm_bridge.py` → `hub.devm`) — D-007
- ✅ İki iş modülü üretildi: `cagri_merkezi` (active), `crm` (stopped)
- ✅ Config sistemi aktif (`config.yaml` + `.env`)
- ✅ Talimat sistemi Python portu (`scripts/talimat_runner.py`)
- ✅ Dokümanlar merkezileştirildi (`docs/`)
- ⏳ Gemini API entegrasyonu planlanıyor (Faz 2)

## Aktif Modüller

| Modül | Tür | Durum | Runtime | Konum |
|---|---|---|---|---|
| **DevM** | otonom_platform | 🟢 çekirdek | Node.js | `devm_bridge.py` → `DevM/` |
| cagri_merkezi | analytics_module | active | Python | `modules/cagri_merkezi/` |
| crm | crm_module | stopped | Python | `modules/crm/` |

> **Not:** DevM bir modül değil, çekirdek bileşendir. `hub.devm` ile erişilir, `modules/` altında değildir.

## Son Alınan Kararlar

Bkz: `DECISIONS.md` — D-001 ~ D-007 (D-003 iptal, D-007 ile değiştirildi)

## Ekip

- **Emre** — Proje Yöneticisi & Geliştirici
- **Cihan** — Geliştirici
- **Melih** — Geliştirici
- **AI (Gemini + Copilot)** — Robot Kol

## Ana Dokümanlar

- `docs/PROJE_GELISTIRME.md` — Yol haritası ve öneriler
- `docs/DECISIONS.md` — Teknik karar defteri
- `docs/ARCHITECTURE.md` — Sistem mimarisi
- `docs/MODULLER.md` — Modül geliştirme rehberi
- `config.yaml` — Merkezi ayarlar
- `data/registry.json` — Modül hafızası

## Çalışma Prensibi

1. Her AI oturumu önce bu dosyayı okur.
2. `DECISIONS.md` kontrol edilir.
3. `PROJE_GELISTIRME.md`'deki açık maddeler takip edilir.
4. Yapılan değişiklikler bu dosyaya ve `DECISIONS.md`'ye yansıtılır.
