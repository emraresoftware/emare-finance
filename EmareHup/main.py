# ============================================
# Emare Hub — Ana Kontrol Paneli (v0.3)
# ============================================
from emare_core import hub
from factory_worker import worker


def setup_modules():
    """İlk kurulum — temel modülleri oluştur"""
    worker.create_module_scaffold(
        "cagri_merkezi",
        module_type="analytics_module",
        description="Çağrı merkezi veri analizi ve raporlama"
    )
    worker.create_module_scaffold(
        "crm",
        module_type="crm_module",
        description="Müşteri ilişkileri yönetimi"
    )


def test_devm_core():
    """DevM çekirdek entegrasyonunu test et"""
    print("\n--- 🧠 DevM Çekirdek Testi ---")
    if not hub.devm:
        print("  ⚠️  DevM çekirdeğe bağlı değil")
        return

    health = hub.devm.health_check()
    print(f"  Node.js kurulu: {'✅' if health['node_installed'] else '❌'}")
    print(f"  DevM dizini: {'✅' if health['devm_path_exists'] else '❌'}")
    print(f"  Servisler: {'✅' if health['services_exist'] else '❌'}")
    print(f"  Genel Sağlık: {'🟢 Sağlıklı' if health['healthy'] else '🔴 Sorunlu'}")

    services = hub.devm.get_services_status()
    if services:
        print(f"\n  DevM Servisleri:")
        for svc in services:
            icon = "⚙️" if svc["exists"] else "📭"
            code = " (kod var)" if svc.get("has_code") else ""
            print(f"    {icon}  {svc['name']}{code}")

    tasks = hub.devm.get_tasks()
    if tasks.get("success"):
        t = tasks["tasks"]
        print(f"\n  DevM Görevleri: {len(t['backlog'])} backlog, {len(t['done'])} tamamlanan")

    workspaces = hub.devm.get_workspaces()
    if workspaces:
        print(f"\n  DevM Workspace'leri:")
        for ws in workspaces:
            t_icon = "📋" if ws["has_talimatlar"] else "  "
            print(f"    📂 {ws['name']} {t_icon}")


def test_lifecycle():
    """Modül yaşam döngüsünü test et"""
    print("\n--- 🔄 Yaşam Döngüsü Testi ---")
    hub.pause_module("crm")
    print(f"  CRM durumu: {hub.get_module_status('crm')['status']}")
    hub.resume_module("crm")
    print(f"  CRM durumu (resume): {hub.get_module_status('crm')['status']}")
    hub.unregister_module("crm")


def main():
    setup_modules()
    test_devm_core()
    test_lifecycle()

    print("\n--- 📊 Sistem Raporu ---")
    report = hub.get_system_report()
    print(f"  Aktif: {report['active']} | Durmuş: {report['stopped']} | Duraklatılmış: {report['paused']}")

    hub.print_dashboard()


if __name__ == "__main__":
    main()
