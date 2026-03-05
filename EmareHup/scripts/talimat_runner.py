"""
Emare Hub — Talimat Runner (Python Portu)

DevM'nin TALIMATLAR.md otomatik uygulama sisteminin Python implementasyonu.
Her modülün kendi TALIMATLAR.md dosyası olabilir ve bu script onları işler.

Kullanım:
    python scripts/talimat_runner.py                        # Tüm modüllerin talimatlarını listele
    python scripts/talimat_runner.py --module devm          # Belirli modülün talimatları
    python scripts/talimat_runner.py --apply                # Talimatları uygula (işaretle)
    python scripts/talimat_runner.py --file TALIMATLAR.md   # Belirli dosyayı işle
"""

import re
import sys
import argparse
import subprocess
from pathlib import Path
from datetime import datetime


class TalimatRunner:
    """TALIMATLAR.md dosyalarından görevleri okur, uygular ve işaretler"""

    def __init__(self, base_path=None):
        self.base_path = Path(base_path) if base_path else Path(__file__).parent.parent
        self.log_lines = []

    def _log(self, message, level="INFO"):
        """Loglama"""
        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        line = f"[{timestamp}] [{level}] {message}"
        self.log_lines.append(line)
        print(line)

    def find_talimat_files(self):
        """Proje genelinde tüm TALIMATLAR.md dosyalarını bulur"""
        files = []

        # Ana dizindeki TALIMATLAR.md
        root_talimat = self.base_path / "TALIMATLAR.md"
        if root_talimat.exists():
            files.append({"path": root_talimat, "scope": "root"})

        # Modüllerdeki TALIMATLAR.md
        modules_dir = self.base_path / "modules"
        if modules_dir.exists():
            for module_dir in modules_dir.iterdir():
                if module_dir.is_dir():
                    talimat = module_dir / "TALIMATLAR.md"
                    if talimat.exists():
                        files.append({
                            "path": talimat,
                            "scope": f"module:{module_dir.name}"
                        })

        return files

    def parse_tasks(self, file_path):
        """TALIMATLAR.md dosyasından görevleri parse eder"""
        content = Path(file_path).read_text(encoding="utf-8")
        lines = content.split("\n")

        tasks = []
        for i, line in enumerate(lines):
            # İşaretlenmemiş maddeler: - [ ] ...
            unchecked = re.match(r'^(\s*)-\s*\[\s*\]\s*(.*)', line)
            if unchecked:
                tasks.append({
                    "line_number": i,
                    "text": unchecked.group(2).strip(),
                    "done": False,
                    "indent": len(unchecked.group(1)),
                    "original": line
                })

            # İşaretlenmiş maddeler: - [x] ...
            checked = re.match(r'^(\s*)-\s*\[x\]\s*(.*)', line, re.IGNORECASE)
            if checked:
                tasks.append({
                    "line_number": i,
                    "text": checked.group(2).strip(),
                    "done": True,
                    "indent": len(checked.group(1)),
                    "original": line
                })

        return tasks, lines

    def find_bash_blocks(self, lines, task_line):
        """Bir görevin altındaki bash kod bloklarını bulur"""
        blocks = []
        in_block = False
        block_content = []
        is_bash = False

        # Görev satırından sonraki 10 satırı tara
        for i in range(task_line + 1, min(task_line + 15, len(lines))):
            line = lines[i]

            if line.strip().startswith("```"):
                if not in_block:
                    in_block = True
                    is_bash = "bash" in line.lower() or "sh" in line.lower()
                    block_content = []
                else:
                    if is_bash and block_content:
                        blocks.append("\n".join(block_content))
                    in_block = False
                    is_bash = False
                    block_content = []
            elif in_block:
                block_content.append(line)

            # Yeni görev satırı görürsek dur
            if re.match(r'^\s*-\s*\[', line) and i != task_line:
                break

        return blocks

    def mark_done(self, file_path, line_number):
        """Bir görevi tamamlandı olarak işaretler"""
        content = Path(file_path).read_text(encoding="utf-8")
        lines = content.split("\n")

        if line_number < len(lines):
            lines[line_number] = re.sub(
                r'\[\s*\]',
                '[x]',
                lines[line_number],
                count=1
            )
            Path(file_path).write_text("\n".join(lines), encoding="utf-8")
            return True
        return False

    def execute_bash(self, command, cwd=None):
        """Bash komutunu çalıştırır"""
        try:
            result = subprocess.run(
                command,
                shell=True,
                cwd=cwd or str(self.base_path),
                capture_output=True, text=True,
                timeout=120
            )
            return {
                "success": result.returncode == 0,
                "stdout": result.stdout,
                "stderr": result.stderr,
                "exit_code": result.returncode
            }
        except subprocess.TimeoutExpired:
            return {"success": False, "error": "Zaman aşımı (120s)"}

    def run(self, file_path=None, apply=False, module=None):
        """Ana çalıştırma fonksiyonu"""
        self._log("🚀 Talimat Runner başlatıldı")

        # Dosya belirle
        if file_path:
            files = [{"path": Path(file_path), "scope": "manual"}]
        elif module:
            module_talimat = self.base_path / "modules" / module / "TALIMATLAR.md"
            if module_talimat.exists():
                files = [{"path": module_talimat, "scope": f"module:{module}"}]
            else:
                self._log(f"❌ {module} modülünde TALIMATLAR.md bulunamadı", "ERROR")
                return
        else:
            files = self.find_talimat_files()

        if not files:
            self._log("📭 Hiçbir TALIMATLAR.md dosyası bulunamadı")
            return

        total_pending = 0
        total_done = 0

        for f in files:
            self._log(f"\n📄 İşleniyor: {f['path']} ({f['scope']})")
            tasks, lines = self.parse_tasks(f["path"])

            pending = [t for t in tasks if not t["done"]]
            done = [t for t in tasks if t["done"]]
            total_pending += len(pending)
            total_done += len(done)

            if not pending:
                self._log(f"  ✅ Tüm görevler tamamlanmış ({len(done)} tamamlanan)")
                continue

            self._log(f"  📋 {len(pending)} bekleyen / {len(done)} tamamlanan görev")

            for task in pending:
                self._log(f"  ⏳ [{task['line_number']+1}] {task['text']}")

                # Bash blokları var mı?
                bash_blocks = self.find_bash_blocks(lines, task["line_number"])
                if bash_blocks:
                    for block in bash_blocks:
                        self._log(f"    📦 Bash blok bulundu: {block[:60]}...")
                        if apply:
                            result = self.execute_bash(block)
                            if result["success"]:
                                self._log(f"    ✅ Bash çalıştırıldı")
                            else:
                                self._log(f"    ❌ Bash hatası: {result.get('stderr', result.get('error', ''))[:100]}", "ERROR")

                if apply:
                    self.mark_done(f["path"], task["line_number"])
                    self._log(f"  ✔️  İşaretlendi: {task['text']}")

        self._log(f"\n📊 Özet: {total_pending} bekleyen, {total_done} tamamlanan")
        if not apply and total_pending > 0:
            self._log("💡 Uygulamak için --apply parametresini ekleyin")

        # Log dosyasına yaz
        log_dir = self.base_path / "logs"
        log_dir.mkdir(exist_ok=True)
        log_file = log_dir / "talimat_runner.log"
        with open(log_file, "a", encoding="utf-8") as lf:
            lf.write("\n".join(self.log_lines) + "\n---\n")


def main():
    parser = argparse.ArgumentParser(description="Emare Hub Talimat Runner")
    parser.add_argument("--file", "-f", help="İşlenecek TALIMATLAR.md dosya yolu")
    parser.add_argument("--module", "-m", help="Belirli modülün talimatlarını işle")
    parser.add_argument("--apply", "-a", action="store_true", help="Talimatları uygula ve işaretle")
    args = parser.parse_args()

    runner = TalimatRunner()
    runner.run(file_path=args.file, apply=args.apply, module=args.module)


if __name__ == "__main__":
    main()
