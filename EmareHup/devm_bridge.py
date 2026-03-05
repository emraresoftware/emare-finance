"""
DevM Bridge — Çekirdek Bileşen

DevM, Emare Hub'ın otonom yazılımcı platformudur.
Sıradan bir modül DEĞİLDİR — fabrikanın beynidir.

DevM servisleri:
  - Orchestrator  : Görev yönetimi, state machine, run lifecycle
  - Model Broker  : Çoklu AI model yönetimi, consensus
  - IDE Runner    : Cursor/VSCode ajanları ile kod üretimi

Bu köprü, Node.js tabanlı DevM'yi Python çekirdeğine bağlar.
"""

import os
import json
import subprocess
from pathlib import Path


# DevM dizini — kök dizindeki /DevM klasörü
DEVM_ROOT = Path(__file__).parent / "DevM"


class DevMBridge:
    """Emare Hub çekirdeği ↔ DevM otonom platform köprüsü"""

    def __init__(self, devm_path=None):
        self.devm_path = Path(devm_path) if devm_path else DEVM_ROOT

    # ─── Sağlık & Durum ─────────────────────────────────

    def is_node_available(self):
        """Node.js kurulu mu?"""
        try:
            result = subprocess.run(
                ["node", "--version"],
                capture_output=True, text=True, timeout=5
            )
            return result.returncode == 0
        except (FileNotFoundError, subprocess.TimeoutExpired):
            return False

    def health_check(self):
        """DevM platformunun sağlık durumu"""
        checks = {
            "node_installed": self.is_node_available(),
            "devm_path_exists": self.devm_path.exists(),
            "package_json_exists": (self.devm_path / "package.json").exists(),
            "scripts_exist": (self.devm_path / "scripts").exists(),
            "services_exist": (self.devm_path / "services").exists(),
            "context_exist": (self.devm_path / "context").exists(),
        }
        checks["healthy"] = all(checks.values())
        return checks

    def status(self):
        """DevM'nin tam durum raporu"""
        health = self.health_check()
        return {
            "name": "DevM",
            "role": "Otonom Yazılımcı Platformu (Çekirdek)",
            "healthy": health["healthy"],
            "health_details": health,
            "services": self.get_services_status(),
            "tasks": self.get_tasks()
        }

    # ─── Servisler ───────────────────────────────────────

    def get_services_status(self):
        """DevM servislerinin (orchestrator, model-broker, ide-runner) durumu"""
        service_defs = [
            {"name": "orchestrator", "path": "services/orchestrator"},
            {"name": "model-broker", "path": "services/model-broker"},
            {"name": "ide-runner", "path": "services/ide-runner"},
        ]
        services = []
        for svc in service_defs:
            svc_path = self.devm_path / svc["path"]
            services.append({
                "name": svc["name"],
                "path": svc["path"],
                "exists": svc_path.exists(),
                "has_code": any(svc_path.glob("*.js")) if svc_path.exists() else False,
            })
        return services

    # ─── Talimat Sistemi ─────────────────────────────────

    def run_talimatlar(self, apply=False):
        """DevM talimat sistemini tetikler"""
        if not self.devm_path.exists():
            return {"success": False, "error": "DevM dizini bulunamadı"}

        env = {**os.environ}
        if apply:
            env["APPLY"] = "true"

        try:
            result = subprocess.run(
                ["node", "scripts/run-talimatlar-ai.js"],
                cwd=str(self.devm_path),
                capture_output=True, text=True,
                timeout=60, env=env
            )
            return {
                "success": result.returncode == 0,
                "stdout": result.stdout,
                "stderr": result.stderr
            }
        except subprocess.TimeoutExpired:
            return {"success": False, "error": "Zaman aşımı (60s)"}
        except FileNotFoundError:
            return {"success": False, "error": "Node.js bulunamadı"}

    def start_watcher(self):
        """DevM talimat izleyicisini arka planda başlatır"""
        if not self.devm_path.exists():
            return {"success": False, "error": "DevM dizini bulunamadı"}

        try:
            process = subprocess.Popen(
                ["node", "scripts/watch-talimatlar.js"],
                cwd=str(self.devm_path),
                stdout=subprocess.PIPE, stderr=subprocess.PIPE
            )
            return {"success": True, "pid": process.pid}
        except FileNotFoundError:
            return {"success": False, "error": "Node.js bulunamadı"}

    # ─── Context Dosyaları ───────────────────────────────

    def get_tasks(self):
        """DevM TASKS.md dosyasını okur ve parse eder"""
        tasks_file = self.devm_path / "context" / "TASKS.md"
        if not tasks_file.exists():
            return {"success": False, "error": "TASKS.md bulunamadı"}

        content = tasks_file.read_text(encoding="utf-8")
        sections = {"backlog": [], "in_progress": [], "done": []}
        current = None
        for line in content.split("\n"):
            lower = line.lower().strip()
            if "backlog" in lower:
                current = "backlog"
            elif "in progress" in lower:
                current = "in_progress"
            elif "done" in lower:
                current = "done"
            elif current and line.strip().startswith("- ["):
                checked = "[x]" in line
                task_text = line.split("]", 1)[-1].strip()
                sections[current].append({"text": task_text, "done": checked})

        return {"success": True, "tasks": sections}

    def get_decisions(self):
        """DevM DECISIONS.md dosyasını okur"""
        dec_file = self.devm_path / "context" / "DECISIONS.md"
        if not dec_file.exists():
            return {"success": False, "error": "DECISIONS.md bulunamadı"}
        return {"success": True, "content": dec_file.read_text(encoding="utf-8")}

    def get_session_context(self):
        """DevM SESSION-CONTEXT.md dosyasını okur"""
        ctx_file = self.devm_path / "context" / "SESSION-CONTEXT.md"
        if not ctx_file.exists():
            return {"success": False, "error": "SESSION-CONTEXT.md bulunamadı"}
        return {"success": True, "content": ctx_file.read_text(encoding="utf-8")}

    # ─── Workspace Bilgileri ─────────────────────────────

    def get_workspaces(self):
        """DevM'nin örnek proje workspace'lerini listeler"""
        ws_base = self.devm_path / "örnek proje"
        if not ws_base.exists():
            return []
        return [
            {"name": d.name, "path": str(d), "has_talimatlar": (d / "TALIMATLAR.md").exists()}
            for d in ws_base.iterdir()
            if d.is_dir() and d.name.startswith("ws-")
        ]


# Çekirdek seviyesinde tek bir bridge instance
devm = DevMBridge()
