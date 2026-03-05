# 🏭 Emare Hub — Proje Geliştirme Yol Haritası

> **Vizyon:** Emare Hub, tüm yazılım modüllerini tek bir çatı altında üreten, yöneten ve izleyen bir **yazılım fabrikası** altyapısıdır.

---

## 📌 Mevcut Durum (v0.2 — Çekirdek Güçlendirildi)

| Dosya | Görev | Durum |
|---|---|---|
| `emare_core.py` | Ana Üs — Modül kayıt merkezi (v0.2: Loglama + JSON Hafıza + Unregister) | ✅ Tamamlandı |
| `factory_worker.py` | Robot Kol — Modül iskelet üreticisi | ✅ Tamamlandı |
| `main.py` | Kontrol Paneli — Sistemi tetikleyen merkez | ✅ Tamamlandı |
| `data/registry.json` | Kalıcı modül hafızası | ✅ Çalışıyor |
| `logs/emare_hub.log` | Sistem seyir defteri | ✅ Çalışıyor |
| `modules/cagri_merkezi/` | İlk üretilen modül iskeleti | ✅ Üretildi |
| `modules/crm/` | İkinci modül iskeleti (test amaçlı) | ✅ Üretildi (stopped) |

---

## 🚀 Geliştirme Fazları

### 🔹 Faz 1 — Çekirdek Sistem Güçlendirmesi (v0.2) ✅

- [x] **Modül Yaşam Döngüsü:** `active`, `paused`, `error`, `stopped` durumları eklenmeli
- [x] **Modül Kaldırma:** `unregister_module()` fonksiyonu — modül devre dışı bırakma
- [x] **Kalıcı Kayıt (Persistence):** Registry bilgilerinin `JSON` veya `SQLite` dosyasına yazılması (şu an sadece bellekte tutuluyor, program kapanınca kaybolur)
- [x] **Loglama Sistemi:** Her işlemin tarih/saat ile `logs/` klasörüne yazılması
- [ ] **Config Dosyası:** `config.yaml` veya `config.json` ile merkezi ayar yönetimi (API anahtarları, dizin yolları vb.)

### 🔹 Faz 2 — AI Entegrasyonu (v0.3)

- [ ] **Google Gemini (Ultra) Bağlantısı:** `factory_worker.py` içine Gemini API entegrasyonu
- [ ] **Otomatik Kod Üretimi:** Modül adı ve açıklaması verildiğinde, AI'ın çalışan Python kodu üretmesi
- [ ] **Kod İnceleme (Code Review):** Üretilen kodun AI tarafından kalite kontrolden geçirilmesi
- [ ] **Prompt Şablonları:** Her modül tipi için önceden hazırlanmış prompt'lar (`prompts/` klasörü)

### 🔹 Faz 3 — Gerçek İş Modülleri (v0.4)

#### 📞 Çağrı Merkezi Analiz Modülü
- [ ] Excel/CSV dosyalarından çağrı verisi okuma
- [ ] Çağrı süresi, müşteri memnuniyeti, temsilci performansı analizi
- [ ] Otomatik rapor üretimi (PDF/HTML)
- [ ] Grafik ve görselleştirmeler (matplotlib / plotly)

#### 👥 CRM Modülü
- [ ] Müşteri veritabanı yönetimi
- [ ] Müşteri segmentasyonu
- [ ] Etkileşim geçmişi takibi
- [ ] AI destekli müşteri skoru hesaplama

#### 📊 Dashboard / Panel Modülü
- [ ] Web tabanlı canlı izleme paneli (Flask / FastAPI + HTML)
- [ ] Tüm aktif modüllerin durumunu gösteren arayüz
- [ ] Gerçek zamanlı metrikler ve grafikler
- [ ] Modül ekleme/kaldırma arayüzü

### 🔹 Faz 4 — Otomasyon ve Orkestrasyon (v0.5)

- [ ] **Zamanlayıcı (Scheduler):** Modüllerin belirli saatlerde otomatik çalışması (`APScheduler` veya `cron`)
- [ ] **Modüller Arası İletişim:** Bir modülün çıktısının diğerine girdi olarak akması (pipeline)
- [ ] **Hata Yönetimi:** Bir modül çökerse otomatik yeniden başlatma ve bildirim
- [ ] **Bildirim Sistemi:** Telegram / E-posta / WhatsApp üzerinden uyarı gönderme

### 🔹 Faz 5 — Ölçeklendirme ve Dağıtım (v1.0)

- [ ] **Docker Konteynerizasyonu:** Her modül kendi konteynerinde çalışsın
- [ ] **API Gateway:** Dış dünyadan modüllere erişim için REST API katmanı
- [ ] **Kullanıcı Yetkilendirme:** Cihan, Melih vb. kullanıcılar için rol bazlı erişim
- [ ] **CI/CD Pipeline:** Yeni modül eklendiğinde otomatik test ve deploy

---

## 🏗️ Önerilen Klasör Yapısı (Hedef)

```
EmareHub/
├── emare_core.py              # Ana Üs — Kayıt Merkezi
├── factory_worker.py          # Robot Kol — Modül Üretici
├── main.py                    # Kontrol Paneli
├── config.yaml                # Merkezi ayarlar
├── PROJE_GELISTIRME.md        # Bu dosya
│
├── modules/                   # Üretilen modüller
│   ├── cagri_merkezi/
│   │   ├── __init__.py
│   │   ├── main.py
│   │   ├── analyzer.py
│   │   └── reports/
│   ├── crm/
│   └── dashboard/
│
├── prompts/                   # AI prompt şablonları
│   ├── module_generator.txt
│   └── code_review.txt
│
├── logs/                      # Sistem logları
│   └── 2026-02-28.log
│
├── data/                      # Veri dosyaları (Excel, CSV vb.)
│
├── templates/                 # HTML/rapor şablonları
│
└── tests/                     # Birim testleri
    ├── test_core.py
    └── test_worker.py
```

---

## 💡 Teknik Fikirler ve Notlar

### 1. Neden Modüler Mimari?
Her yazılım bağımsız bir modül olarak geliştirildiğinde:
- Bir modülün hatası diğerlerini etkilemez
- Cihan ve Melih farklı modüller üzerinde paralel çalışabilir
- Yeni bir iş ihtiyacı geldiğinde sıfırdan başlamaya gerek kalmaz

### 2. AI Kod Üretim Stratejisi
Factory Worker'a Gemini bağlandığında şu akış çalışacak:
```
Kullanıcı İsteği → Prompt Şablonu → Gemini API → Kod Üretimi → Kalite Kontrol → Dosyaya Yazma → Registry'ye Kayıt
```

### 3. Veri Güvenliği
- API anahtarları asla kod içinde tutulmamalı → `.env` dosyası + `python-dotenv`
- `.gitignore` dosyası oluşturulmalı (data/, .env, __pycache__/ vb.)

### 4. Öncelik Sırası Önerim
1. ~~⭐ Config + Loglama sistemi (temeli sağlamlaştır)~~ ✅ Tamamlandı
2. ⭐ Gemini API entegrasyonu (robot kolun beynini tak)
3. ⭐ Çağrı Merkezi analiz modülü (ilk gerçek iş)
4. Dashboard (görsellik)
5. CRM + diğer modüller

---

## 🧠 Copilot Önerileri (28 Şubat 2026)

> Aşağıdakiler, mevcut kod tabanını inceledikten sonra tespit ettiğim **iyileştirme noktaları**, **eksik parçalar** ve **stratejik öneriler**.

### 🔴 Kritik — Hemen Yapılması Gerekenler

#### 1. `.gitignore` Dosyası Oluşturulmalı
Proje henüz git'e eklenmemiş. Ama eklenmeden önce hassas ve gereksiz dosyaları dışlamak şart:
```
__pycache__/
*.pyc
.env
data/
logs/
.DS_Store
```
> **Neden kritik?** API anahtarları, müşteri verileri ve loglar yanlışlıkla GitHub'a push edilirse ciddi güvenlik açığı oluşur.

#### 2. `.env` + Config Sistemi (Faz 1'den kalan son madde)
Şu an API anahtarı yok ama Faz 2'ye geçmeden önce altyapı hazır olmalı:
- `config.yaml` → genel ayarlar (modül dizini, log seviyesi, versiyon)
- `.env` → gizli bilgiler (API anahtarları)
- `python-dotenv` paketi ile yükleme

#### 3. `main.py` Her Çalıştırmada Modülleri Tekrar Kaydediyor
**Tespit:** `main.py` her çalıştığında `cagri_merkezi` ve `crm` modüllerini yeniden `register` ediyor. Ama `registry.json`'da zaten varlar. Bu, `connected_at` tarihinin her seferinde üzerine yazılması ve durdurulmuş modülün (`crm`) tekrar aktif olması demek.
**Çözüm:** `register_module()` içine "zaten kayıtlıysa atla" kontrolü eklenmeli veya `main.py` sadece ilk kurulumda scaffold çağırmalı.

### 🟡 Önemli — Kısa Vadede Yapılması Gerekenler

#### 4. `factory_worker.py` — Üretilen Modüllerin Kalitesi Artmalı
Şu an üretilen `modules/*/main.py` dosyası sadece 2 satırlık bir iskelet. Her modüle şunlar da eklenmeli:
- `config.py` → modüle özel ayarlar
- `README.md` → modülün ne yaptığını anlatan açıklama
- `requirements.txt` → modüle özel bağımlılıklar
- Standart bir `run()`, `stop()`, `status()` arayüzü (interface)

#### 5. Modül Arayüzü Standardizasyonu (Interface / Abstract Class)
Her modülün uyması gereken bir "sözleşme" tanımlanmalı:
```python
class EmareModule:
    def run(self): ...
    def stop(self): ...
    def status(self): ...
    def health_check(self): ...
```
> Bu sayede her modül aynı dilde konuşur ve Ana Üs onları aynı şekilde yönetebilir.

#### 6. `emare_core.py` — Eksik Yönetim Fonksiyonları
Mevcut fonksiyonlar: `register_module()`, `unregister_module()`
Eklenmesi gerekenler:
- `pause_module(name)` → durumu `paused` yapar (geçici durdurma)
- `resume_module(name)` → `paused` → `active` geri döndürme
- `get_module_status(name)` → tek modülün durumunu sorgulama
- `list_active_modules()` → sadece aktif olanları listeleme
- `get_system_report()` → tüm sistemin özet raporu (kaç modül aktif, kaç durdurulmuş, uptime vb.)

#### 7. Log Rotasyonu
Şu an tek bir `emare_hub.log` dosyası var. Uzun vadede bu dosya çok büyüyecek.
**Çözüm:** `RotatingFileHandler` veya günlük log dosyaları (`2026-02-28.log` formatı) kullanılmalı.

### 🟢 İyileştirme — Orta/Uzun Vadeli Fikirler

#### 8. CLI (Komut Satırı Arayüzü)
`main.py`'yi sabit kodlu komutlarla çalıştırmak yerine interaktif bir CLI:
```bash
python main.py --create modül_adı --type crm_module
python main.py --list
python main.py --stop modül_adı
python main.py --status
```
> `argparse` veya `click` kütüphanesi ile yapılabilir. Bu, Cihan ve Melih'in terminalde hızlıca işlem yapmasını sağlar.

#### 9. Modül Sağlık Kontrolü (Health Check)
Ana Üs, belirli aralıklarla (ör. her 60 saniye) tüm aktif modüllere "yaşıyor musun?" diye sormalı. Cevap gelmezse status'ü `error` olarak işaretlemeli.

#### 10. Event Sistemi (Olay Tabanlı Mimari)
Modüller birbirine doğrudan bağlı olmak yerine event'lerle haberleşmeli:
```
Çağrı Merkezi → "yeni_çağrı_tamamlandı" olayı yayınlar
CRM → bu olayı dinler ve müşteri kaydını günceller
```
> Bu, modüller arası bağımlılığı (coupling) minimuma indirir.

#### 11. Versiyon Yönetimi
Her modülün bir versiyonu olmalı (`v1.0`, `v1.1`...). Registry'ye `version` alanı eklenmeli. Böylece:
- Eski versiyona geri dönülebilir (rollback)
- Hangi modülün güncel olduğu takip edilebilir

#### 12. Test Altyapısı
Henüz hiç test yok. Faz 3'e geçmeden önce en azından:
- `tests/test_core.py` → `register`, `unregister`, `load/save registry` testleri
- `tests/test_worker.py` → scaffold oluşturma testi
- `pytest` ile otomatik test çalıştırma

#### 13. Web Dashboard Teknoloji Seçimi Önerim
Faz 3'teki Dashboard için önerim:
- **Backend:** FastAPI (hızlı, modern, async destekli)
- **Frontend:** Basit HTML + HTMX (SPA karmaşıklığı olmadan dinamik arayüz)
- **Alternatif:** Streamlit (tamamen Python ile dashboard, en hızlı prototipleme)

#### 14. Veritabanı Geçişi (JSON → SQLite)
`registry.json` şu an yeterli ama modül sayısı artınca:
- Eşzamanlı yazma sorunları çıkabilir
- Sorgu yapmak zorlaşır
`SQLite` veya ileride `PostgreSQL`'e geçiş planlanmalı.

---

## 🔗 DevM Entegrasyon Analizi (28 Şubat 2026)

> DevM projesi (`/EmareHup/DevM/`) Emare Hub'a modül olarak entegre edilecek. Aşağıda kapsamlı analiz ve uyarlama planı yer almaktadır.

### 📖 DevM Nedir? (Proje Özeti)

DevM, **çoklu AI + çoklu IDE ajanları ile otomatik yazılım geliştirme platformu** prototipidir.

**Çalışma Akışı:**
```
Kullanıcı fikir girer → project_spec.md oluşur → Çoklu AI model (Gemini/OpenAI) paralel değerlendirme →
Consensus (uzlaşı) → Kullanıcı onaylar → IDE ajanları (Cursor/VSCode) paralel kod üretir →
CI doğrulama (lint/test/build) → Deploy → Canlı URL → Geri bildirim döngüsü
```

**Mimari Bileşenler:**
| Servis | Görev |
|---|---|
| `orchestrator` (ws-1) | Ana beyin — run lifecycle, stage geçişleri, state machine |
| `model-broker` (ws-2) | AI modelleri yönetir — provider adapter, consensus giriş/çıkış |
| `ide-runner` (ws-3) | IDE ajanlarını çalıştırır — Cursor/VSCode üzerinden kod üretimi |
| `frontend-web` | Kullanıcı arayüzü (henüz iskelet) |

**Talimat Sistemi (Otomasyon Motoru):**
- `TALIMATLAR.md` dosyasına yazılan maddeler otomatik uygulanır
- `watch-talimatlar.js` → dosyayı izler, değişiklikte tetikler
- `run-talimatlar-ai.js` → `- [ ]` maddeleri bulur, uygular, `- [x]` işaretler
- `run-talimatlar.js` → bash bloklarını tespit eder ve çalıştırır
- PM2 (ecosystem.config.js) ile daemon olarak çalışabilir

**Teknoloji Yığını:** Node.js, chokidar (file watching), PM2 (process manager)

**Mevcut Durumu:** İskelet aşamasında — mimari dokümante edilmiş, örnek proje yapısı hazır, talimat sistemi çalışıyor ama henüz gerçek servisler (orchestrator state machine, model broker adapter, ide runner) kodlanmamış.

### 🔍 DevM ↔ Emare Hub Uyumluluk Analizi

#### ✅ Uyumlu Noktalar
| DevM Özelliği | Emare Hub Karşılığı | Not |
|---|---|---|
| Modüler mimari (ws-1, ws-2, ws-3) | `modules/` klasör yapısı | Doğal uyum — her ws bir modül olabilir |
| `TALIMATLAR.md` otomasyon | `factory_worker.py` | İkisi de "talimat al → uygula" mantığında |
| `context/SESSION-CONTEXT.md` | `data/registry.json` | İkisi de "hafıza" katmanı |
| `logs/` yapısı | `logs/emare_hub.log` | İkisi de log tutuyor |
| `DECISIONS.md` karar defteri | Emare Hub'da henüz yok | DevM'den alınabilir |
| `project_spec.md` → kod üretimi | `factory_worker` → scaffold üretimi | Benzer akış, DevM daha kapsamlı |

#### ⚠️ Uyumsuz / Dikkat Gereken Noktalar
| Konu | DevM | Emare Hub | Çözüm |
|---|---|---|---|
| **Dil** | Node.js (JavaScript) | Python | Köprü katmanı gerekli |
| **Proje yapısı** | workspace tabanlı (ayrı klasörler) | tek merkez + modules/ | DevM'yi tek modül olarak sarmala |
| **Talimat sistemi** | Markdown dosya tabanlı | Python fonksiyon tabanlı | Python wrapper ile talimat okuma |
| **AI entegrasyonu** | Cursor CLI + henüz API yok | Gemini API planlanıyor | Ortak API katmanı tasarla |
| **Process yönetimi** | PM2 (ecosystem.config.js) | Yok (tek seferlik çalıştırma) | Emare Hub'a process manager ekle |

### 🏗️ Entegrasyon Planı (3 Aşama)

#### Aşama 1 — DevM'yi Emare Hub Modülü Olarak Kaydet (Sarmalama)
**Hedef:** DevM'yi bozmadan, Emare Hub'ın onu tanımasını sağla.

Yapılacaklar:
- [ ] `modules/devm/` altına DevM'yi bağla (symlink veya kopyalama)
- [ ] `modules/devm/__init__.py` — Emare Hub'a kendini tanıtan Python wrapper
- [ ] `modules/devm/bridge.py` — Python ↔ Node.js köprüsü (subprocess ile Node script çağırma)
- [ ] `emare_core.py`'ye DevM'yi `"devm_platform"` tipiyle kaydet
- [ ] DevM'nin mevcut `package.json`, `scripts/`, `TALIMATLAR.md` yapısına dokunma

```python
# modules/devm/bridge.py — Örnek köprü
import subprocess
from pathlib import Path

class DevMBridge:
    def __init__(self):
        self.devm_path = Path(__file__).parent / "DevM"
    
    def run_talimatlar(self, apply=False):
        """DevM talimat sistemini Python'dan tetikler"""
        env = {"APPLY": "true"} if apply else {}
        result = subprocess.run(
            ["node", "scripts/run-talimatlar-ai.js"],
            cwd=self.devm_path,
            capture_output=True, text=True, env={**os.environ, **env}
        )
        return result.stdout
    
    def start_watcher(self):
        """DevM talimat izleyicisini arka planda başlatır"""
        subprocess.Popen(
            ["node", "scripts/watch-talimatlar.js"],
            cwd=self.devm_path
        )
    
    def get_tasks(self):
        """DevM TASKS.md dosyasını okur ve parse eder"""
        tasks_file = self.devm_path / "context" / "TASKS.md"
        if tasks_file.exists():
            return tasks_file.read_text(encoding="utf-8")
        return None
```

#### Aşama 2 — Ortak Protokol Katmanı (Standartlaştırma)
**Hedef:** DevM'nin güçlü yönlerini Emare Hub'a taşı, ortak dil oluştur.

Yapılacaklar:
- [ ] **Karar Defteri (DECISIONS.md):** Emare Hub kök dizinine ekle — DevM'deki gibi tüm mimari kararlar buraya yazılsın
- [ ] **Oturum Bağlamı (SESSION-CONTEXT.md):** Emare Hub kök dizinine ekle — AI ajanlarının hafızası
- [ ] **Talimat Sistemi Python Portu:** DevM'nin `TALIMATLAR.md` → otomatik uygulama mantığını Python'a taşı
  - `scripts/talimat_runner.py` → `.md` dosyasından `- [ ]` maddeleri oku, uygula, `- [x]` işaretle
  - Her modülün kendi `TALIMATLAR.md` dosyası olabilir
- [ ] **Ortak Event Formatı:** Hem Python hem Node modüller arasında JSON tabanlı event iletişimi
  ```json
  {
    "event": "module.registered",
    "source": "emare_core",
    "target": "devm",
    "timestamp": "2026-02-28T23:00:00Z",
    "data": { "module_name": "devm", "status": "active" }
  }
  ```
- [ ] **Modül Manifest Standardı:** Her modül (Python veya Node) kök dizininde `manifest.json` bulundursun:
  ```json
  {
    "name": "devm",
    "version": "0.1.0",
    "runtime": "node",
    "entry": "scripts/run-talimatlar-ai.js",
    "type": "devm_platform",
    "dependencies": ["chokidar"],
    "health_check": "node -e \"console.log('ok')\"",
    "description": "Çoklu AI + çoklu IDE otomatik yazılım üretim platformu"
  }
  ```

#### Aşama 3 — Derin Entegrasyon (Birleşme)
**Hedef:** DevM'nin AI orkestrasyon gücünü Emare Hub'ın modül fabrikasıyla birleştir.

Yapılacaklar:
- [ ] **DevM Orchestrator → Emare Hub factory_worker:** Yeni modül üretme emri DevM orchestrator'dan gelsin
- [ ] **DevM Model Broker → Emare Hub AI katmanı:** Gemini/OpenAI çağrıları tek merkezden yönetilsin
- [ ] **DevM IDE Runner → Emare Hub modül kodu:** Üretilen kod otomatik olarak `modules/` altına yerleşsin
- [ ] **Birleşik Dashboard:** Hem Emare Hub modüllerini hem DevM run durumlarını gösteren tek panel
- [ ] **CI/CD Entegrasyonu:** DevM'nin validation pipeline'ı (lint/test/build) Emare Hub modüllerine de uygulansın

### 📊 Hedef Mimari (DevM Entegre Sonrası)

```
EmareHub/
├── emare_core.py                  # Ana Üs — Kayıt Merkezi (Python)
├── factory_worker.py              # Robot Kol — Modül Üretici (Python)
├── main.py                        # Kontrol Paneli
├── config.yaml                    # Merkezi ayarlar
├── DECISIONS.md                   # 🆕 Karar defteri (DevM'den alındı)
├── SESSION-CONTEXT.md             # 🆕 Oturum hafızası (DevM'den alındı)
│
├── modules/
│   ├── cagri_merkezi/             # Python modülü
│   ├── crm/                       # Python modülü
│   └── devm/                      # 🆕 DevM — Node.js modülü
│       ├── manifest.json          # Modül tanım dosyası
│       ├── bridge.py              # Python ↔ Node köprüsü
│       └── DevM/                  # Orijinal DevM projesi (dokunulmadan)
│           ├── package.json
│           ├── TALIMATLAR.md
│           ├── context/
│           ├── docs/
│           ├── scripts/
│           ├── services/
│           └── örnek proje/
│
├── scripts/
│   └── talimat_runner.py          # 🆕 Python talimat motoru
│
├── data/
│   └── registry.json
├── logs/
│   └── emare_hub.log
└── tests/
```

### ⚡ DevM'den Emare Hub'a Hemen Alınabilecek Fikirler

| DevM'deki Özellik | Emare Hub'a Katkısı | Zorluk |
|---|---|---|
| `DECISIONS.md` karar defteri | Tüm mimari kararların takibi | 🟢 Kolay — dosya oluştur |
| `SESSION-CONTEXT.md` oturum hafızası | AI ajanlar arası bağlam sürekliliği | 🟢 Kolay — dosya oluştur |
| `TALIMATLAR.md` → otomatik uygulama | Modüllere "yapılacaklar listesi" özelliği | 🟡 Orta — Python portu gerekli |
| `manifest.json` modül tanımı | Standart modül kimlik kartı | 🟢 Kolay — format tanımla |
| `PROMPT-BOOTSTRAP.md` ajan kuralları | Her AI oturumunda tutarlı davranış | 🟢 Kolay — dosya oluştur |
| PM2 process management | Modüllerin daemon olarak çalışması | 🟡 Orta — supervisor entegrasyonu |
| workspace isolation (ws-1/2/3) | Modüller arası izolasyon | 🟠 Zor — mimari değişiklik |
| Hibrit API + Computer Use stratejisi | AI fallback mekanizması | 🟠 Zor — ileri seviye |

### 🎯 Önerilen Başlangıç Sırası

| # | İş | Tahmini Süre |
|---|---|---|
| 1 | `DECISIONS.md` + `SESSION-CONTEXT.md` oluştur (DevM'den ilham) | 15 dk |
| 2 | `modules/devm/manifest.json` oluştur — modül kimlik kartı | 10 dk |
| 3 | `modules/devm/bridge.py` oluştur — Python↔Node köprüsü | 30 dk |
| 4 | `emare_core.py`'ye DevM'yi kaydet + test et | 15 dk |
| 5 | Talimat sisteminin Python portunu yaz | 1-2 saat |
| 6 | Ortak event formatını tanımla | 30 dk |

---

## 📅 Zaman Çizelgesi (Tahmini)

| Faz | Hedef | Süre |
|---|---|---|
| Faz 1 | Çekirdek güçlendirme | 1-2 gün |
| Faz 2 | AI entegrasyonu | 1-2 gün |
| Faz 3 | İlk iş modülleri | 3-5 gün |
| Faz 4 | Otomasyon | 3-5 gün |
| Faz 5 | Ölçeklendirme | 1+ hafta |

---

## 🤝 Ekip Rolleri

| İsim | Rol | Odak |
|---|---|---|
| Emre | Proje Yöneticisi & Geliştirici | Genel mimari, koordinasyon |
| Cihan | Geliştirici | Modül geliştirme |
| Melih | Geliştirici | Modül geliştirme |
| AI (Gemini + Copilot) | Robot Kol | Kod üretimi, analiz, öneri |

---

> **Son Güncelleme:** 28 Şubat 2026  
> **Versiyon:** v0.2 — Çekirdek Güçlendirildi  
> **Durum:** 🟢 Aktif Geliştirmede  
> **Sonraki Hedef:** DevM entegrasyonu (Aşama 1) + Config sistemi + Gemini API (Faz 2)  
> **Aktif Modüller:** cagri_merkezi (active), crm (stopped), devm (planlanan)
