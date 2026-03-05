# 📦 Emare Hub — Modül Geliştirme Rehberi

> Bu belge, Emare Hub'a yeni modül eklemek isteyen geliştiriciler (Cihan, Melih, AI ajanları) için referans rehberidir.

---

## 1. Modül Nedir?

Modül, Emare Hub fabrikasına bağlanan bağımsız bir yazılım birimidir. Her modül:
- Kendi klasöründe yaşar (`modules/<modül_adı>/`)
- Kendi manifest'i, talimatları ve README'si vardır
- Ana Üs'e (`emare_core.py`) kendini kaydeder
- Diğer modüllerden bağımsız çalışabilir

## 2. Yeni Modül Oluşturma

### 2.1 Otomatik Yol (Önerilen)
```python
from factory_worker import worker

worker.create_module_scaffold(
    "modül_adı",
    module_type="standard_module",
    description="Modülün ne yaptığını açıkla"
)
```

Bu komut otomatik olarak şunları üretir:
```
modules/modül_adı/
├── __init__.py
├── main.py          # run(), stop(), status(), health_check()
├── manifest.json    # Kimlik kartı
├── TALIMATLAR.md    # Yapılacaklar listesi
└── README.md        # Açıklama
```

### 2.2 Manuel Yol
Klasörü kendin oluşturup aşağıdaki dosyaları ekleyebilirsin. Zorunlu dosyalar: `__init__.py`, `main.py`, `manifest.json`.

## 3. Manifest Standardı (`manifest.json`)

Her modülde **zorunlu** olan kimlik kartı:

```json
{
    "name": "modül_adı",
    "version": "0.1.0",
    "runtime": "python",
    "entry": "main.py",
    "type": "standard_module",
    "description": "Modülün açıklaması",
    "dependencies": ["pandas", "requests"],
    "health_check": "python -c \"print('ok')\"",
    "created_at": "2026-02-28",
    "emare_hub_compatible": true
}
```

| Alan | Zorunlu | Açıklama |
|---|---|---|
| `name` | ✅ | Modülün benzersiz adı |
| `version` | ✅ | Semantik versiyon (major.minor.patch) |
| `runtime` | ✅ | `python` veya `node` |
| `entry` | ✅ | Giriş dosyası |
| `type` | ✅ | Modül tipi (bkz: izin verilen tipler) |
| `description` | ✅ | Ne yaptığını açıklayan kısa metin |
| `dependencies` | ⬜ | Gereken paketler listesi |
| `health_check` | ⬜ | Sağlık kontrol komutu |
| `emare_hub_compatible` | ⬜ | Emare Hub uyumlu mu? |

## 4. Modül Giriş Noktası (`main.py`)

Her modül şu standart fonksiyonları sunmalıdır:

```python
def run():
    """Modülü başlatır"""
    pass

def stop():
    """Modülü durdurur"""
    pass

def status():
    """Modülün mevcut durumunu döndürür"""
    return {"name": "modül_adı", "running": True}

def health_check():
    """Sağlık kontrolü — True/False döndürür"""
    return True
```

## 5. Talimatlar Sistemi (`TALIMATLAR.md`)

Her modülde yapılacak işlerin takip listesi:

```markdown
# modül_adı — Talimatlar

- [ ] Temel iş mantığını implement et
- [ ] Birim testlerini yaz
- [ ] README.md'yi güncelle
- [x] Tamamlanmış görev örneği
```

**Talimat Runner ile tarama:**
```bash
# Tüm modüllerin talimatlarını göster
python scripts/talimat_runner.py

# Belirli modülün talimatlarını göster
python scripts/talimat_runner.py --module cagri_merkezi

# Talimatları uygula ve işaretle
python scripts/talimat_runner.py --apply
```

## 6. Modül Yaşam Döngüsü

```
    Üretim → Kayıt → Aktif ←→ Duraklatılmış → Durdurulmuş
```

**Ana Üs komutları:**
```python
from emare_core import hub

hub.register_module("ad", "tip")    # Kaydet
hub.pause_module("ad")              # Duraklat
hub.resume_module("ad")             # Devam ettir
hub.unregister_module("ad")         # Durdur
hub.get_module_status("ad")         # Durum sorgula
hub.list_active_modules()           # Aktif olanları listele
hub.print_dashboard()               # Tüm sistem paneli
```

## 7. Farklı Runtime Modülleri (Node.js)

Python dışı modüller için `bridge.py` köprüsü kullanılır:

```
modules/devm/
├── __init__.py       # Python import noktası
├── bridge.py         # Python ↔ Node.js köprüsü (subprocess)
├── manifest.json     # runtime: "node"
└── DevM/             # Orijinal Node.js projesi
```

Köprü, `subprocess.run()` ile Node script'leri çağırır ve çıktıyı Python'a döndürür.

## 8. İzin Verilen Modül Tipleri

| Tip | Açıklama |
|---|---|
| `standard_module` | Genel amaçlı |
| `analytics_module` | Veri analizi ve raporlama |
| `crm_module` | Müşteri ilişkileri yönetimi |
| `devm_platform` | Otomatik yazılım üretim platformu |
| `dashboard_module` | Web tabanlı izleme paneli |

Yeni tip eklemek için `config.yaml` → `modules.allowed_types` listesine ekleyin.

---

> **Son Güncelleme:** 28 Şubat 2026
