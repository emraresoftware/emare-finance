import json
from pathlib import Path
from emare_core import hub

class EmareWorker:
    """Robot Kol — Modül iskelet üreticisi (v0.3)"""

    def create_module_scaffold(self, module_name, module_type="standard_module", description=""):
        # 1. Klasörü oluştur
        path = Path(f"./modules/{module_name}")
        path.mkdir(parents=True, exist_ok=True)

        # 2. __init__.py
        (path / "__init__.py").touch()

        # 3. main.py — modül giriş noktası
        with open(path / "main.py", "w", encoding="utf-8") as f:
            f.write(f'"""\n{module_name} modülü — Emare Hub tarafından üretildi\nModül Tipi: {module_type}\n"""\n\n')
            f.write(f"def run():\n")
            f.write(f"    print('🔧 {module_name} modülü çalışıyor...')\n\n")
            f.write(f"def stop():\n")
            f.write(f"    print('🛑 {module_name} modülü durduruluyor...')\n\n")
            f.write(f"def status():\n")
            f.write(f"    return {{'name': '{module_name}', 'type': '{module_type}', 'running': True}}\n\n")
            f.write(f"def health_check():\n")
            f.write(f"    return True\n")

        # 4. manifest.json — modül kimlik kartı
        manifest = {
            "name": module_name,
            "version": "0.1.0",
            "runtime": "python",
            "entry": "main.py",
            "type": module_type,
            "description": description or f"{module_name} modülü",
            "dependencies": [],
            "health_check": "python -c \"print('ok')\"",
            "created_at": hub.start_time.strftime("%Y-%m-%d"),
            "emare_hub_compatible": True
        }
        with open(path / "manifest.json", "w", encoding="utf-8") as f:
            json.dump(manifest, f, indent=4, ensure_ascii=False)

        # 5. TALIMATLAR.md — modüle özel görev listesi
        with open(path / "TALIMATLAR.md", "w", encoding="utf-8") as f:
            f.write(f"# {module_name} — Talimatlar\n\n")
            f.write(f"- [ ] Temel iş mantığını implement et\n")
            f.write(f"- [ ] Birim testlerini yaz\n")
            f.write(f"- [ ] README.md oluştur\n")

        # 6. README.md
        with open(path / "README.md", "w", encoding="utf-8") as f:
            f.write(f"# {module_name}\n\n")
            f.write(f"> {description or module_type}\n\n")
            f.write(f"## Kullanım\n\n```python\nfrom modules.{module_name}.main import run\nrun()\n```\n")

        # 7. Modülü Ana Üs'e kaydet
        hub.register_module(module_name, module_type)

        hub.log_and_print(f"🛠️  FABRİKA: '{module_name}' modülü üretildi (manifest + talimat + README)")

worker = EmareWorker()
