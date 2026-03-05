<?php

namespace App\Http\Controllers;

use App\Models\IntegrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntegrationController extends Controller
{
    /**
     * Entegrasyon merkezi — tüm yazılım entegrasyonlarını listeler.
     */
    public function index()
    {
        $integrations = $this->getIntegrationCategories();

        // Kullanıcının mevcut başvuruları
        $myRequests = IntegrationRequest::where('user_id', Auth::id())
            ->latest()
            ->get()
            ->keyBy('integration_name');

        return view('integrations.index', compact('integrations', 'myRequests'));
    }

    /**
     * Entegrasyon başvurusu oluştur.
     */
    public function requestIntegration(Request $request)
    {
        $request->validate([
            'integration_type' => 'required|string|max:50',
            'integration_name' => 'required|string|max:100',
            'message'          => 'nullable|string|max:1000',
        ], [
            'integration_type.required' => 'Entegrasyon tipi gereklidir.',
            'integration_name.required' => 'Entegrasyon adı gereklidir.',
            'message.max'               => 'Mesaj en fazla 1000 karakter olabilir.',
        ]);

        $user = Auth::user();

        // Aynı entegrasyon için bekleyen başvuru var mı?
        $existing = IntegrationRequest::where('user_id', $user->id)
            ->where('integration_name', $request->integration_name)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('warning', $request->integration_name . ' için zaten bekleyen bir başvurunuz var.');
        }

        IntegrationRequest::create([
            'tenant_id'        => $user->tenant_id,
            'user_id'          => $user->id,
            'integration_type' => $request->integration_type,
            'integration_name' => $request->integration_name,
            'message'          => $request->message,
        ]);

        return back()->with('success', $request->integration_name . ' entegrasyon başvurunuz alındı. Admin onayından sonra bilgilendirileceksiniz.');
    }

    /**
     * Kullanıcının kendi başvurularını listeler.
     */
    public function myRequests()
    {
        $requests = IntegrationRequest::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('integrations.my-requests', compact('requests'));
    }

    /**
     * Tüm entegrasyon kategorilerini döner.
     */
    private function getIntegrationCategories(): array
    {
        return [
            'muhasebe' => [
                'title'       => 'Muhasebe & Finans',
                'description' => 'Muhasebe programları ve finansal yönetim araçlarıyla entegrasyon.',
                'icon'        => 'fa-calculator',
                'color'       => '#818cf8',
                'bg'          => '#eef2ff',
                'items'       => [
                    ['name' => 'Luca',          'logo' => '📊', 'status' => 'ready',    'desc' => 'Luca muhasebe programı entegrasyonu'],
                    ['name' => 'Paraşüt',       'logo' => '🪂', 'status' => 'ready',    'desc' => 'Paraşüt online muhasebe'],
                    ['name' => 'Logo',           'logo' => '🔷', 'status' => 'ready',    'desc' => 'Logo Tiger / Go / Netsis'],
                    ['name' => 'Mikro',          'logo' => '🟦', 'status' => 'planned',  'desc' => 'Mikro Yazılım entegrasyonu'],
                    ['name' => 'ETA',            'logo' => '🟩', 'status' => 'planned',  'desc' => 'ETA muhasebe entegrasyonu'],
                    ['name' => 'Zirve',          'logo' => '⛰️', 'status' => 'planned',  'desc' => 'Zirve muhasebe programı'],
                    ['name' => 'Nebim',          'logo' => '🌐', 'status' => 'planned',  'desc' => 'Nebim V3 / Winner entegrasyonu'],
                    ['name' => 'QuickBooks',     'logo' => '🟢', 'status' => 'planned',  'desc' => 'QuickBooks Online API'],
                    ['name' => 'Xero',           'logo' => '🔵', 'status' => 'planned',  'desc' => 'Xero bulut muhasebe'],
                ],
            ],
            'eticaret' => [
                'title'       => 'E-Ticaret Platformları',
                'description' => 'Online satış kanallarıyla otomatik ürün, stok ve sipariş senkronizasyonu.',
                'icon'        => 'fa-shopping-cart',
                'color'       => '#f472b6',
                'bg'          => '#fdf2f8',
                'items'       => [
                    ['name' => 'Trendyol',      'logo' => '🟠', 'status' => 'ready',    'desc' => 'Trendyol Marketplace API'],
                    ['name' => 'Hepsiburada',   'logo' => '🟡', 'status' => 'ready',    'desc' => 'Hepsiburada Merchant API'],
                    ['name' => 'N11',            'logo' => '🔴', 'status' => 'ready',    'desc' => 'N11 mağaza entegrasyonu'],
                    ['name' => 'Çiçeksepeti',   'logo' => '💐', 'status' => 'planned',  'desc' => 'Çiçeksepeti Marketplace'],
                    ['name' => 'Amazon TR',     'logo' => '📦', 'status' => 'planned',  'desc' => 'Amazon Seller Central'],
                    ['name' => 'Shopify',        'logo' => '🛍️', 'status' => 'ready',    'desc' => 'Shopify mağaza entegrasyonu'],
                    ['name' => 'WooCommerce',    'logo' => '🟣', 'status' => 'ready',    'desc' => 'WordPress WooCommerce API'],
                    ['name' => 'OpenCart',        'logo' => '🔵', 'status' => 'planned',  'desc' => 'OpenCart REST API'],
                    ['name' => 'Magento',         'logo' => '🟧', 'status' => 'planned',  'desc' => 'Adobe Commerce / Magento'],
                    ['name' => 'IdeaSoft',        'logo' => '💡', 'status' => 'planned',  'desc' => 'IdeaSoft altyapı entegrasyonu'],
                    ['name' => 'T-Soft',          'logo' => '🅣', 'status' => 'planned',  'desc' => 'T-Soft e-ticaret entegrasyonu'],
                ],
            ],
            'kargo' => [
                'title'       => 'Kargo & Lojistik',
                'description' => 'Kargo firmaları ve lojistik servisleriyle gönderi takip entegrasyonu.',
                'icon'        => 'fa-truck-fast',
                'color'       => '#fb923c',
                'bg'          => '#fff7ed',
                'items'       => [
                    ['name' => 'Yurtiçi Kargo',  'logo' => '🟢', 'status' => 'ready',    'desc' => 'Yurtiçi Kargo API entegrasyonu'],
                    ['name' => 'Aras Kargo',     'logo' => '🔴', 'status' => 'ready',    'desc' => 'Aras Kargo gönderi API'],
                    ['name' => 'MNG Kargo',      'logo' => '🟠', 'status' => 'ready',    'desc' => 'MNG Kargo entegrasyonu'],
                    ['name' => 'PTT Kargo',      'logo' => '🟡', 'status' => 'planned',  'desc' => 'PTT gönderi takip'],
                    ['name' => 'Sürat Kargo',    'logo' => '🔵', 'status' => 'planned',  'desc' => 'Sürat Kargo API'],
                    ['name' => 'Sendeo',          'logo' => '📮', 'status' => 'planned',  'desc' => 'Sendeo lojistik entegrasyonu'],
                    ['name' => 'Hepsijet',        'logo' => '🚀', 'status' => 'planned',  'desc' => 'Hepsijet teslimat API'],
                    ['name' => 'DHL',             'logo' => '📦', 'status' => 'planned',  'desc' => 'DHL Express API'],
                    ['name' => 'UPS',             'logo' => '🟫', 'status' => 'planned',  'desc' => 'UPS Shipping API'],
                ],
            ],
            'odeme' => [
                'title'       => 'Ödeme Sistemleri',
                'description' => 'Sanal POS, ödeme geçitleri ve dijital cüzdan entegrasyonları.',
                'icon'        => 'fa-credit-card',
                'color'       => '#4ade80',
                'bg'          => '#f0fdf4',
                'items'       => [
                    ['name' => 'iyzico',         'logo' => '💳', 'status' => 'ready',    'desc' => 'iyzico ödeme altyapısı'],
                    ['name' => 'PayTR',          'logo' => '🔵', 'status' => 'ready',    'desc' => 'PayTR sanal POS'],
                    ['name' => 'Sipay',          'logo' => '🟢', 'status' => 'ready',    'desc' => 'Sipay ödeme çözümleri'],
                    ['name' => 'Param',          'logo' => '🟠', 'status' => 'planned',  'desc' => 'Param ödeme geçidi'],
                    ['name' => 'Paynet',         'logo' => '🟣', 'status' => 'planned',  'desc' => 'Paynet ödeme sistemi'],
                    ['name' => 'Stripe',         'logo' => '🟦', 'status' => 'ready',    'desc' => 'Stripe Payments API'],
                    ['name' => 'PayPal',         'logo' => '🅿️', 'status' => 'planned',  'desc' => 'PayPal ödeme entegrasyonu'],
                    ['name' => 'Tosla',          'logo' => '🔴', 'status' => 'planned',  'desc' => 'Tosla ile ödeme'],
                ],
            ],
            'efatura' => [
                'title'       => 'E-Fatura & E-Arşiv',
                'description' => 'GİB onaylı e-fatura, e-arşiv ve e-irsaliye entegratörleri.',
                'icon'        => 'fa-file-invoice-dollar',
                'color'       => '#2dd4bf',
                'bg'          => '#f0fdfa',
                'items'       => [
                    ['name' => 'Foriba',         'logo' => '📄', 'status' => 'ready',    'desc' => 'Foriba / Sovos e-fatura'],
                    ['name' => 'QNB e-Fatura',   'logo' => '🏦', 'status' => 'ready',    'desc' => 'QNB Finansbank e-fatura'],
                    ['name' => 'Kolaysoft',      'logo' => '📋', 'status' => 'ready',    'desc' => 'Kolaysoft e-belge'],
                    ['name' => 'İzibiz',         'logo' => '✅', 'status' => 'planned',  'desc' => 'İzibiz e-fatura entegrasyonu'],
                    ['name' => 'Uyumsoft',       'logo' => '🔷', 'status' => 'planned',  'desc' => 'Uyumsoft e-dönüşüm'],
                    ['name' => 'Edm',            'logo' => '📝', 'status' => 'planned',  'desc' => 'EDM Bilişim e-fatura'],
                    ['name' => 'Logo e-Fatura',  'logo' => '🔶', 'status' => 'planned',  'desc' => 'Logo Connect e-fatura'],
                ],
            ],
            'erp' => [
                'title'       => 'ERP & İş Yönetimi',
                'description' => 'Kurumsal kaynak planlama ve iş süreçleri yönetim sistemleri.',
                'icon'        => 'fa-sitemap',
                'color'       => '#a78bfa',
                'bg'          => '#f5f3ff',
                'items'       => [
                    ['name' => 'SAP',            'logo' => '🔷', 'status' => 'planned',  'desc' => 'SAP Business One / S4HANA'],
                    ['name' => 'Oracle ERP',     'logo' => '🔴', 'status' => 'planned',  'desc' => 'Oracle ERP Cloud'],
                    ['name' => 'Microsoft Dynamics', 'logo' => '🟦', 'status' => 'planned', 'desc' => 'Dynamics 365 Business Central'],
                    ['name' => 'Canias ERP',     'logo' => '🟩', 'status' => 'planned',  'desc' => 'Canias ERP entegrasyonu'],
                    ['name' => 'Netsis',         'logo' => '🌐', 'status' => 'planned',  'desc' => 'Logo Netsis ERP'],
                    ['name' => 'Dia',            'logo' => '🔶', 'status' => 'planned',  'desc' => 'Dia yazılım entegrasyonu'],
                ],
            ],
            'crm' => [
                'title'       => 'CRM & Müşteri İlişkileri',
                'description' => 'Müşteri ilişkileri yönetimi ve pazarlama otomasyon araçları.',
                'icon'        => 'fa-people-arrows',
                'color'       => '#f43f5e',
                'bg'          => '#fff1f2',
                'items'       => [
                    ['name' => 'HubSpot',        'logo' => '🟠', 'status' => 'planned',  'desc' => 'HubSpot CRM entegrasyonu'],
                    ['name' => 'Salesforce',     'logo' => '☁️', 'status' => 'planned',  'desc' => 'Salesforce CRM API'],
                    ['name' => 'Zoho CRM',       'logo' => '🔴', 'status' => 'planned',  'desc' => 'Zoho CRM entegrasyonu'],
                    ['name' => 'Pipedrive',      'logo' => '🟢', 'status' => 'planned',  'desc' => 'Pipedrive satış yönetimi'],
                    ['name' => 'Bitrix24',       'logo' => '🔵', 'status' => 'planned',  'desc' => 'Bitrix24 iş yönetimi'],
                ],
            ],
            'iletisim' => [
                'title'       => 'İletişim & Bildirim',
                'description' => 'SMS, e-posta, WhatsApp ve push bildirim servisleri.',
                'icon'        => 'fa-comment-dots',
                'color'       => '#06b6d4',
                'bg'          => '#ecfeff',
                'items'       => [
                    ['name' => 'Netgsm',         'logo' => '📱', 'status' => 'ready',    'desc' => 'Netgsm SMS gönderimi'],
                    ['name' => 'İletimerkezi',   'logo' => '📨', 'status' => 'ready',    'desc' => 'İletimerkezi toplu SMS'],
                    ['name' => 'WhatsApp Business', 'logo' => '💬', 'status' => 'planned', 'desc' => 'WhatsApp Business API'],
                    ['name' => 'Mailjet',        'logo' => '✉️', 'status' => 'planned',  'desc' => 'Mailjet e-posta servisi'],
                    ['name' => 'SendGrid',       'logo' => '📧', 'status' => 'planned',  'desc' => 'SendGrid e-posta API'],
                    ['name' => 'Firebase FCM',   'logo' => '🔔', 'status' => 'ready',    'desc' => 'Firebase push bildirimleri'],
                    ['name' => 'OneSignal',      'logo' => '🔕', 'status' => 'planned',  'desc' => 'OneSignal bildirim servisi'],
                    ['name' => 'Telegram Bot',   'logo' => '✈️', 'status' => 'planned',  'desc' => 'Telegram Bot entegrasyonu'],
                ],
            ],
            'banka' => [
                'title'       => 'Banka & Fintech',
                'description' => 'Banka hesap hareketleri, açık bankacılık ve fintech entegrasyonları.',
                'icon'        => 'fa-university',
                'color'       => '#0ea5e9',
                'bg'          => '#f0f9ff',
                'items'       => [
                    ['name' => 'Akbank',         'logo' => '🔴', 'status' => 'planned',  'desc' => 'Akbank API Banking'],
                    ['name' => 'Garanti BBVA',   'logo' => '🟢', 'status' => 'planned',  'desc' => 'Garanti BBVA açık bankacılık'],
                    ['name' => 'Yapı Kredi',     'logo' => '🔵', 'status' => 'planned',  'desc' => 'Yapı Kredi API'],
                    ['name' => 'İş Bankası',     'logo' => '🟣', 'status' => 'planned',  'desc' => 'İş Bankası API'],
                    ['name' => 'Ziraat Bankası', 'logo' => '🟠', 'status' => 'planned',  'desc' => 'Ziraat Bankası entegrasyonu'],
                    ['name' => 'Papara',         'logo' => '🟣', 'status' => 'planned',  'desc' => 'Papara Business API'],
                    ['name' => 'Tosla',          'logo' => '💳', 'status' => 'planned',  'desc' => 'Tosla finansal API'],
                ],
            ],
            'pazar_yeri' => [
                'title'       => 'Yemek & Pazar Yeri',
                'description' => 'Yemek sipariş platformları ve online pazar yerleri.',
                'icon'        => 'fa-utensils',
                'color'       => '#ef4444',
                'bg'          => '#fef2f2',
                'items'       => [
                    ['name' => 'Yemeksepeti',    'logo' => '🍔', 'status' => 'planned',  'desc' => 'Yemeksepeti Restoran API'],
                    ['name' => 'Getir Yemek',    'logo' => '🟣', 'status' => 'planned',  'desc' => 'Getir Yemek entegrasyonu'],
                    ['name' => 'Trendyol Yemek', 'logo' => '🟠', 'status' => 'planned',  'desc' => 'Trendyol Yemek API'],
                    ['name' => 'Migros Sanal Market', 'logo' => '🟠', 'status' => 'planned', 'desc' => 'Migros market entegrasyonu'],
                    ['name' => 'Getir',          'logo' => '🟣', 'status' => 'planned',  'desc' => 'Getir market entegrasyonu'],
                ],
            ],
            'bulut' => [
                'title'       => 'Bulut & Depolama',
                'description' => 'Bulut depolama, yedekleme ve dosya yönetimi servisleri.',
                'icon'        => 'fa-cloud',
                'color'       => '#64748b',
                'bg'          => '#f8fafc',
                'items'       => [
                    ['name' => 'Google Drive',   'logo' => '📁', 'status' => 'planned',  'desc' => 'Google Drive yedekleme'],
                    ['name' => 'Dropbox',        'logo' => '📦', 'status' => 'planned',  'desc' => 'Dropbox depolama'],
                    ['name' => 'AWS S3',         'logo' => '☁️', 'status' => 'ready',    'desc' => 'Amazon S3 dosya depolama'],
                    ['name' => 'Azure Blob',     'logo' => '🔵', 'status' => 'planned',  'desc' => 'Azure Blob Storage'],
                    ['name' => 'Google Cloud',   'logo' => '🌐', 'status' => 'planned',  'desc' => 'Google Cloud Storage'],
                ],
            ],
            'analitik' => [
                'title'       => 'Analitik & Raporlama',
                'description' => 'Veri analizi, iş zekası ve raporlama araçları.',
                'icon'        => 'fa-chart-pie',
                'color'       => '#eab308',
                'bg'          => '#fefce8',
                'items'       => [
                    ['name' => 'Google Analytics', 'logo' => '📈', 'status' => 'planned', 'desc' => 'Google Analytics 4 entegrasyonu'],
                    ['name' => 'Power BI',       'logo' => '📊', 'status' => 'planned',  'desc' => 'Microsoft Power BI'],
                    ['name' => 'Metabase',       'logo' => '🔍', 'status' => 'planned',  'desc' => 'Metabase BI entegrasyonu'],
                    ['name' => 'Looker',         'logo' => '👁️', 'status' => 'planned',  'desc' => 'Looker / Data Studio'],
                ],
            ],
            'otomasyon' => [
                'title'       => 'Otomasyon & Webhook',
                'description' => 'İş akışı otomasyonu, webhook ve API bağlantı araçları.',
                'icon'        => 'fa-robot',
                'color'       => '#d946ef',
                'bg'          => '#fdf4ff',
                'items'       => [
                    ['name' => 'Zapier',         'logo' => '⚡', 'status' => 'planned',  'desc' => 'Zapier otomasyon bağlantısı'],
                    ['name' => 'Make (Integromat)', 'logo' => '🔗', 'status' => 'planned', 'desc' => 'Make otomasyon platformu'],
                    ['name' => 'n8n',            'logo' => '🔄', 'status' => 'planned',  'desc' => 'n8n açık kaynak otomasyon'],
                    ['name' => 'Webhook',        'logo' => '🪝', 'status' => 'ready',    'desc' => 'Özel webhook entegrasyonu'],
                    ['name' => 'REST API',       'logo' => '🔌', 'status' => 'ready',    'desc' => 'REST API ile özel entegrasyon'],
                ],
            ],
        ];
    }
}
