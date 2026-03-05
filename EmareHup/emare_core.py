import os
import json
import logging
from datetime import datetime
from pathlib import Path

# --- FAZ 1: LOGLAMA SİSTEMİ ---
Path("logs").mkdir(exist_ok=True)

logging.basicConfig(
    filename="logs/emare_hub.log",
    level=logging.INFO,
    format="%(asctime)s - %(levelname)s - %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S"
)

# --- FAZ 1.5: CONFIG SİSTEMİ ---
def load_config():
    """config.yaml dosyasını yükler (PyYAML yoksa varsayılan kullanır)"""
    config_path = Path("config.yaml")
    if config_path.exists():
        try:
            import yaml
            with open(config_path, "r", encoding="utf-8") as f:
                return yaml.safe_load(f)
        except ImportError:
            pass  # yaml yoksa varsayılan config kullan
    # Varsayılan config
    return {
        "hub": {"name": "Emare Hub", "version": "0.3.0"},
        "paths": {"modules_dir": "./modules", "data_dir": "./data", "logs_dir": "./logs"},
        "registry": {"file": "data/registry.json", "auto_save": True},
        "modules": {"auto_register": True, "allowed_types": ["standard_module", "crm_module", "devm_platform"]}
    }

CONFIG = load_config()


class EmareCore:
    def __init__(self):
        self.config = CONFIG
        self.registry_file = Path(self.config.get("registry", {}).get("file", "data/registry.json"))
        self.modules_dir = Path(self.config.get("paths", {}).get("modules_dir", "./modules"))
        self.registry = self._load_registry()
        self.start_time = datetime.now()
        self.version = self.config.get("hub", {}).get("version", "0.3.0")

        self.log_and_print(f"🚀 Emare Hub Ana Üs Başlatıldı (v{self.version})", "info")

        # DevM çekirdek entegrasyonu
        self._init_devm()

    def _init_devm(self):
        """DevM otonom platformunu çekirdeğe bağlar"""
        try:
            from devm_bridge import devm
            self.devm = devm
            health = devm.health_check()
            if health["healthy"]:
                self.log_and_print("🧠 DevM Otonom Platform — BAĞLI (çekirdek)", "info")
            else:
                self.log_and_print("⚠️  DevM Otonom Platform — SORUNLU", "warning")
        except ImportError:
            self.devm = None
            self.log_and_print("ℹ️  DevM bulunamadı — çekirdeğe bağlanmadı", "warning")

    def _load_registry(self):
        """Kalıcı Kayıt — Başlangıçta eski kayıtları okur"""
        Path("data").mkdir(exist_ok=True)
        if self.registry_file.exists():
            with open(self.registry_file, "r", encoding="utf-8") as f:
                return json.load(f)
        return {}

    def _save_registry(self):
        """Kalıcı Kayıt — Değişiklikleri JSON'a yazar"""
        with open(self.registry_file, "w", encoding="utf-8") as f:
            json.dump(self.registry, f, indent=4, ensure_ascii=False)

    def log_and_print(self, message, level="info"):
        """Hem ekrana basar hem log dosyasına yazar"""
        print(message)
        getattr(logging, level, logging.info)(message)

    def register_module(self, module_name, module_type):
        """Yeni modülü ana üsse bağlar (tekrar kayıt korumalı)"""
        if module_name in self.registry and self.registry[module_name]["status"] == "active":
            self.log_and_print(f"ℹ️  MODÜL ZATEN KAYITLI: [{module_name}] — atlanıyor")
            return

        self.registry[module_name] = {
            "type": module_type,
            "status": "active",
            "connected_at": datetime.now().isoformat(),
            "last_health_check": None,
            "version": self._get_module_version(module_name)
        }
        self._save_registry()
        self.log_and_print(f"✅ MODÜL BAĞLANDI: [{module_name}] — Tür: {module_type}")

    def unregister_module(self, module_name):
        """Modül Kaldırma / Devre Dışı Bırakma"""
        if module_name in self.registry:
            self.registry[module_name]["status"] = "stopped"
            self.registry[module_name]["stopped_at"] = datetime.now().isoformat()
            self._save_registry()
            self.log_and_print(f"🛑 MODÜL DURDURULDU: [{module_name}]", "warning")
        else:
            self.log_and_print(f"❌ HATA: [{module_name}] bulunamadı.", "error")

    def pause_module(self, module_name):
        """Modülü geçici olarak duraklatır"""
        if module_name in self.registry:
            self.registry[module_name]["status"] = "paused"
            self._save_registry()
            self.log_and_print(f"⏸️  MODÜL DURAKLATILDI: [{module_name}]", "warning")
        else:
            self.log_and_print(f"❌ HATA: [{module_name}] bulunamadı.", "error")

    def resume_module(self, module_name):
        """Duraklatılmış modülü yeniden aktif eder"""
        if module_name in self.registry:
            if self.registry[module_name]["status"] == "paused":
                self.registry[module_name]["status"] = "active"
                self._save_registry()
                self.log_and_print(f"▶️  MODÜL YENİDEN AKTİF: [{module_name}]")
            else:
                self.log_and_print(f"ℹ️  [{module_name}] durumu 'paused' değil, resume yapılamaz", "warning")
        else:
            self.log_and_print(f"❌ HATA: [{module_name}] bulunamadı.", "error")

    def get_module_status(self, module_name):
        """Tek modülün durumunu sorgular"""
        if module_name in self.registry:
            return self.registry[module_name]
        return None

    def list_active_modules(self):
        """Sadece aktif modülleri listeler"""
        return {
            name: info for name, info in self.registry.items()
            if info["status"] == "active"
        }

    def list_all_modules(self):
        """Tüm modülleri listeler"""
        return self.registry

    def _get_module_version(self, module_name):
        """Modülün manifest.json'ından versiyon bilgisini okur"""
        manifest_path = self.modules_dir / module_name / "manifest.json"
        if manifest_path.exists():
            try:
                with open(manifest_path, "r", encoding="utf-8") as f:
                    manifest = json.load(f)
                return manifest.get("version", "0.0.0")
            except (json.JSONDecodeError, IOError):
                pass
        return "0.0.0"

    def get_system_report(self):
        """Tüm sistemin özet raporunu döndürür"""
        total = len(self.registry)
        active = sum(1 for m in self.registry.values() if m["status"] == "active")
        stopped = sum(1 for m in self.registry.values() if m["status"] == "stopped")
        paused = sum(1 for m in self.registry.values() if m["status"] == "paused")
        uptime = datetime.now() - self.start_time

        report = {
            "hub_version": self.version,
            "started_at": self.start_time.isoformat(),
            "uptime_seconds": int(uptime.total_seconds()),
            "total_modules": total,
            "active": active,
            "stopped": stopped,
            "paused": paused,
            "modules": self.registry
        }
        return report

    def print_dashboard(self):
        """Konsola güzel formatlanmış durum paneli basar"""
        report = self.get_system_report()
        print("\n" + "=" * 55)
        print(f"  🏭 EMARE HUB — DURUM PANELİ (v{self.version})")
        print("=" * 55)

        # DevM çekirdek durumu
        if self.devm:
            health = self.devm.health_check()
            devm_icon = "🟢" if health["healthy"] else "🔴"
            print(f"  🧠 DevM Otonom Platform: {devm_icon} {'Bağlı' if health['healthy'] else 'Sorunlu'}")
            services = self.devm.get_services_status()
            for svc in services:
                s_icon = "⚙️" if svc["exists"] else "📭"
                print(f"     {s_icon}  {svc['name']}")
        else:
            print(f"  🧠 DevM Otonom Platform: ⚪ Bağlı Değil")

        print("-" * 55)
        print(f"  ⏱️  Çalışma Süresi: {report['uptime_seconds']} saniye")
        print(f"  📦 Toplam Modül: {report['total_modules']}")
        print(f"     🟢 Aktif: {report['active']}")
        print(f"     🔴 Durmuş: {report['stopped']}")
        print(f"     🟡 Duraklatılmış: {report['paused']}")
        print("-" * 55)

        status_icons = {"active": "🟢", "stopped": "🔴", "paused": "🟡", "error": "🔥"}
        for name, info in self.registry.items():
            icon = status_icons.get(info["status"], "⚪")
            version = info.get("version", "?")
            print(f"  {icon} {name:<20} | {info['type']:<18} | v{version}")

        print("=" * 55 + "\n")


# Ana üs objesini oluşturuyoruz
hub = EmareCore()
