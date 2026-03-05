# 🏗️ Emare Hub — Sistem Mimarisi

> Bu belge Emare Hub'ın genel mimarisini, bileşenler arası ilişkileri ve veri akışını tanımlar.

---

## 1. Genel Bakış

```
┌──────────────────────────────────────────────────────────────┐
│                    EMARE HUB (v0.3)                          │
│                                                              │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │                  ÇEKİRDEK KATMAN                        │ │
│  │                                                         │ │
│  │  ┌─────────────┐  ┌──────────────┐  ┌──────────────┐   │ │
│  │  │ emare_core  │  │factory_worker│  │   main.py    │   │ │
│  │  │  (Ana Üs)   │  │ (Robot Kol)  │  │(Kontrol Pan.)│   │ │
│  │  └──────┬──────┘  └──────────────┘  └──────────────┘   │ │
│  │         │                                               │ │
│  │         ▼                                               │ │
│  │  ┌──────────────────────────────────────────┐           │ │
│  │  │ 🧠 devm_bridge.py (Çekirdek Köprü)      │           │ │
│  │  │    DevMBridge — Python ↔ Node.js         │           │ │
│  │  │    health_check, status, talimatlar      │           │ │
│  │  └──────────────┬───────────────────────────┘           │ │
│  │                 │ subprocess                             │ │
│  │                 ▼                                        │ │
│  │  ┌──────────────────────────────────────────┐           │ │
│  │  │ 🤖 DevM/ (Otonom Yazılımcılar Platformu)│           │ │
│  │  │    ⚙️ orchestrator                       │           │ │
│  │  │    ⚙️ model-broker                       │           │ │
│  │  │    ⚙️ ide-runner                         │           │ │
│  │  └──────────────────────────────────────────┘           │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                              │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │                   modules/ (İş Modülleri)               │ │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐              │ │
│  │  │ cagri_   │  │   crm    │  │  ...     │              │ │
│  │  │ merkezi  │  │          │  │ (yeni)   │              │ │
│  │  │ (Python) │  │ (Python) │  │          │              │ │
│  │  └──────────┘  └──────────┘  └──────────┘              │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                              │
│  ┌────────────┐  ┌────────────┐  ┌────────────────────┐      │
│  │   data/    │  │   logs/    │  │     scripts/       │      │
│  │registry.json  │emare_hub.log  │talimat_runner.py   │      │
│  └────────────┘  └────────────┘  └────────────────────┘      │
│                                                              │
│  ┌────────────┐  ┌────────────┐  ┌────────────────────┐      │
│  │config.yaml │  │   .env     │  │      docs/         │      │
│  │  (ayarlar) │  │ (gizli)    │  │  (dokümanlar)      │      │
│  └────────────┘  └────────────┘  └────────────────────┘      │
└──────────────────────────────────────────────────────────────┘
```

## 2. Çekirdek Bileşenler

### 2.1 Ana Üs (`emare_core.py`)
Fabrikanın beyni. Tüm modülleri kaydeder, yönetir ve izler.

**Sorumluluklar:**
- Modül kayıt/kaldırma/duraklatma/devam ettirme
- Registry'yi JSON'a kalıcı olarak yazma/okuma
- Loglama (dosya + konsol)
- Config yönetimi (`config.yaml` okuma)
- Sistem raporu ve dashboard üretimi
- Modül manifest'lerinden versiyon okuma
- DevM çekirdek bağlantısı (`devm_bridge.py` üzerinden)

**Arayüz:**
```python
hub.register_module(name, type)     # Modül kaydet
hub.unregister_module(name)         # Modül durdur
hub.pause_module(name)              # Geçici duraklat
hub.resume_module(name)             # Yeniden aktif et
hub.get_module_status(name)         # Tek modül durumu
hub.list_active_modules()           # Aktif modüller
hub.get_system_report()             # Sistem raporu (dict)
hub.print_dashboard()               # Konsol paneli
```

### 2.2 Robot Kol (`factory_worker.py`)
Yeni modüllerin iskeletini üreten fabrika kolu.

**Ürettiği Dosyalar:**
```
modules/<modül_adı>/
├── __init__.py         # Python paket tanımı
├── main.py             # Giriş noktası (run, stop, status, health_check)
├── manifest.json       # Modül kimlik kartı
├── TALIMATLAR.md       # Modüle özel görev listesi
└── README.md           # Modül açıklaması
```

### 2.3 DevM Çekirdek Köprüsü (`devm_bridge.py`)
DevM otonom yazılımcılar platformunu Emare Hub çekirdeğine bağlayan köprü.

> **ÖNEMLİ:** DevM bir modül değildir — sistemin beynidir. Bu yüzden `modules/` altında değil,
> çekirdek katmanda (`devm_bridge.py`) yaşar. (Bkz: D-007)

**Sorumluluklar:**
- DevM sağlık kontrolü (Node.js, dizin, servisler, talimatlar, context, workspaces)
- Servis durumu sorgulama (orchestrator, model-broker, ide-runner)
- Talimat sistemi çalıştırma (`scripts/run-talimatlar.js`, `run-talimatlar-ai.js`)
- Watcher başlatma (`scripts/watch-talimatlar.js`)
- DevM görev, karar ve workspace bilgisi sunma

**Arayüz:**
```python
devm.health_check()          # Sağlık raporu (dict)
devm.status()                # Genel durum özeti
devm.get_services_status()   # 3 servisin durumu
devm.run_talimatlar(apply)   # Talimat sistemi çalıştır
devm.start_watcher()         # Talimat dosyası izleyicisi
devm.get_tasks()             # TASKS.md verisi
devm.get_decisions()         # DECISIONS.md verisi
devm.get_workspaces()        # workspace listesi
```

### 2.4 Kontrol Paneli (`main.py`)
Sistemi tetikleyen, test eden ve izleyen giriş noktası.

## 3. Modül Mimarisi

### 3.1 Modül Yaşam Döngüsü
```
  oluşturma         kayıt            duraklat          devam
  ─────────► [new] ──────► [active] ──────► [paused] ──────►  [active]
                              │                                   │
                              │ durdur                            │ durdur
                              ▼                                   ▼
                          [stopped]                           [stopped]
```

**Durumlar:**
| Durum | Açıklama |
|---|---|
| `active` | Modül çalışıyor, sorgulara cevap veriyor |
| `paused` | Geçici olarak durdurulmuş, resume ile geri dönebilir |
| `stopped` | Tamamen durdurulmuş |
| `error` | Hata durumunda (henüz otomatik geçiş yok) |

### 3.2 Manifest Standardı (`manifest.json`)
Her modül bu dosyayı kök dizininde bulundurmalıdır:
```json
{
    "name": "modül_adı",
    "version": "0.1.0",
    "runtime": "python | node",
    "entry": "main.py",
    "type": "standard_module | crm_module | devm_platform",
    "description": "Modülün açıklaması",
    "dependencies": [],
    "health_check": "python -c \"print('ok')\"",
    "emare_hub_compatible": true
}
```

### 3.3 Modül Tipleri
| Tip | Açıklama | Runtime |
|---|---|---|
| `standard_module` | Genel amaçlı Python modülü | Python |
| `analytics_module` | Veri analizi ve raporlama | Python |
| `crm_module` | Müşteri ilişkileri yönetimi | Python |
| `devm_platform` | Otomatik yazılım üretim platformu | Node.js |
| `dashboard_module` | Web tabanlı izleme paneli | Python |

## 4. Veri Akışı

### 4.1 Modül Üretim Akışı
```
Kullanıcı İsteği
       │
       ▼
  main.py (Kontrol Paneli)
       │
       ▼
  factory_worker.py (Robot Kol)
       │
       ├── modules/<ad>/ klasörü oluştur
       ├── main.py, manifest.json, TALIMATLAR.md, README.md üret
       │
       ▼
  emare_core.py (Ana Üs)
       │
       ├── registry'ye kaydet
       ├── registry.json'a yaz
       └── log dosyasına yaz
```

### 4.2 DevM Köprü Akışı (Çekirdek Seviyesi)
```
  Python (Emare Hub Çekirdek)       Node.js (DevM)
       │                                 │
  emare_core.py                          │
       │ self.devm                       │
       ▼                                 │
  devm_bridge.py ──── subprocess ──────► scripts/run-talimatlar-ai.js
       │                                 │
       ◄──── stdout/stderr ──────────────┘
```

## 5. Dosya Yapısı (Güncel)

```
EmareHub/
├── emare_core.py          # Ana Üs (v0.3)
├── factory_worker.py      # Robot Kol
├── devm_bridge.py         # 🧠 DevM Çekirdek Köprüsü
├── main.py                # Kontrol Paneli
├── config.yaml            # Merkezi ayarlar
├── .env                   # Gizli bilgiler (Git dışı)
├── .gitignore             # Git dışlama kuralları
├── README.md              # Proje tanıtımı
│
├── DevM/                  # 🤖 Otonom Yazılımcılar Platformu (Node.js)
│   ├── scripts/           #    Talimat & watcher araçları
│   ├── services/          #    orchestrator, model-broker, ide-runner
│   ├── context/           #    TASKS.md, DECISIONS.md, SESSION-CONTEXT.md
│   └── örnek proje/       #    Workspace örnekleri
│
├── docs/                  # 📚 Dokümanlar
│   ├── INDEX.md
│   ├── ARCHITECTURE.md    # Bu dosya
│   ├── PROJE_GELISTIRME.md
│   ├── DECISIONS.md
│   ├── SESSION-CONTEXT.md
│   └── MODULLER.md
│
├── modules/               # 📦 İş Modülleri
│   ├── cagri_merkezi/
│   └── crm/
│
├── scripts/               # 🤖 Otomasyon
│   └── talimat_runner.py
│
├── data/                  # 💾 Kalıcı veri
│   └── registry.json
│
└── logs/                  # 📒 Loglar
    └── emare_hub.log
```

## 6. Teknoloji Yığını

| Katman | Teknoloji |
|---|---|
| Çekirdek | Python 3.x |
| Konfigürasyon | YAML + .env |
| Kalıcı Veri | JSON (ileride SQLite) |
| Loglama | Python `logging` modülü |
| DevM Entegrasyonu | Node.js + çekirdek-seviye `devm_bridge.py` köprüsü |
| AI (planlanan) | Google Gemini API |
| Dashboard (planlanan) | FastAPI + HTMX veya Streamlit |

---

> **Son Güncelleme:** 28 Şubat 2026
