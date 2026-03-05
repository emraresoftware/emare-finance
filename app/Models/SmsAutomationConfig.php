<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsAutomationConfig extends Model
{
    protected $fillable = [
        'tenant_id', 'automation_type', 'name', 'template_id',
        'is_active', 'send_time', 'days_before', 'days_after',
        'inactive_days', 'conditions', 'description', 'sent_count',
        'last_run_at',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'conditions'  => 'array',
        'last_run_at' => 'datetime',
        'send_time'   => 'datetime:H:i',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(SmsTemplate::class, 'template_id');
    }

    /**
     * Tüm otomasyon tipleri ve detayları
     */
    public static function getAutomationTypes(): array
    {
        return [
            'birthday' => [
                'name'        => 'Doğum Günü Tebriği',
                'icon'        => 'fas fa-birthday-cake',
                'color'       => 'pink',
                'description' => 'Müşterilerinizin doğum gününde otomatik kutlama SMS\'i gönderin. İndirim kuponu da ekleyebilirsiniz.',
                'category'    => 'birthday',
                'default_template' => 'Sevgili {musteri_adi}, doğum gününüz kutlu olsun! 🎂 Size özel %10 indirim kodunuz: DGUNU{yil}. {firma_adi}',
            ],
            'welcome' => [
                'name'        => 'Hoş Geldin Mesajı',
                'icon'        => 'fas fa-hand-peace',
                'color'       => 'green',
                'description' => 'Yeni müşteri kaydı oluştuğunda otomatik karşılama mesajı gönderin.',
                'category'    => 'general',
                'default_template' => 'Hoş geldiniz {musteri_adi}! {firma_adi} ailesine katıldığınız için teşekkür ederiz. Keyifli alışverişler dileriz. ✨',
            ],
            'after_sale' => [
                'name'        => 'Satış Sonrası Teşekkür',
                'icon'        => 'fas fa-shopping-bag',
                'color'       => 'blue',
                'description' => 'Her satış sonrası müşteriye otomatik teşekkür mesajı gönderin.',
                'category'    => 'sales',
                'default_template' => 'Sayın {musteri_adi}, {tutar} tutarındaki alışverişiniz için teşekkür ederiz. Yine bekleriz! {firma_adi}',
            ],
            'cargo_shipped' => [
                'name'        => 'Kargo Bilgilendirme',
                'icon'        => 'fas fa-truck',
                'color'       => 'orange',
                'description' => 'Sipariş kargoya verildiğinde müşteriye otomatik kargo takip bilgisi gönderin.',
                'category'    => 'sales',
                'default_template' => 'Sayın {musteri_adi}, {siparis_no} nolu siparişiniz kargoya verildi. Takip No: {kargo_takip_no}. Kargo Firması: {kargo_firmasi}. {firma_adi}',
            ],
            'cargo_delivered' => [
                'name'        => 'Kargo Teslim Bildirimi',
                'icon'        => 'fas fa-box-open',
                'color'       => 'emerald',
                'description' => 'Kargo teslim edildiğinde müşteriye bilgi SMS\'i gönderin.',
                'category'    => 'sales',
                'default_template' => 'Sayın {musteri_adi}, {siparis_no} nolu siparişiniz teslim edildi. Memnuniyetiniz bizim için önemli! {firma_adi}',
            ],
            'payment_reminder' => [
                'name'        => 'Ödeme Hatırlatma',
                'icon'        => 'fas fa-bell',
                'color'       => 'yellow',
                'description' => 'Vadesi yaklaşan veya geciken ödemeleri müşterilerinize otomatik hatırlatın.',
                'category'    => 'payment',
                'default_template' => 'Sayın {musteri_adi}, {tutar} tutarındaki ödemenizin vadesi {odeme_tarihi} tarihindedir. Hatırlatmak isteriz. {firma_adi}',
            ],
            'payment_received' => [
                'name'        => 'Ödeme Onayı',
                'icon'        => 'fas fa-check-double',
                'color'       => 'teal',
                'description' => 'Ödeme alındığında müşteriye otomatik onay mesajı gönderin.',
                'category'    => 'payment',
                'default_template' => 'Sayın {musteri_adi}, {tutar} tutarındaki ödemeniz başarıyla alınmıştır. Teşekkür ederiz. {firma_adi}',
            ],
            'inactivity' => [
                'name'        => 'Pasif Müşteri Hatırlatma',
                'icon'        => 'fas fa-user-clock',
                'color'       => 'red',
                'description' => 'Belirli süre alışveriş yapmayan müşterilere geri dönüş kampanyası gönderin.',
                'category'    => 'marketing',
                'default_template' => 'Sizi özledik {musteri_adi}! Uzun süredir bizi ziyaret etmediniz. Size özel %15 indirim fırsatı sizi bekliyor! {firma_adi}',
            ],
            'loyalty_milestone' => [
                'name'        => 'Sadakat Puan Bildirimi',
                'icon'        => 'fas fa-star',
                'color'       => 'amber',
                'description' => 'Müşteri belirli puan eşiğine ulaştığında tebrik ve bilgilendirme SMS\'i gönderin.',
                'category'    => 'loyalty',
                'default_template' => 'Tebrikler {musteri_adi}! 🌟 Sadakat puanınız {puan} oldu. Puanlarınızı bir sonraki alışverişinizde kullanabilirsiniz. {firma_adi}',
            ],
            'campaign_announce' => [
                'name'        => 'Kampanya Duyurusu',
                'icon'        => 'fas fa-bullhorn',
                'color'       => 'purple',
                'description' => 'Yeni kampanya başladığında tüm müşterilerinize otomatik duyuru gönderin.',
                'category'    => 'marketing',
                'default_template' => '🎉 {kampanya_adi} kampanyamız başladı! {indirim} indirim fırsatını kaçırmayın. Son tarih: {tarih}. {firma_adi}',
            ],
            'appointment_reminder' => [
                'name'        => 'Randevu Hatırlatma',
                'icon'        => 'fas fa-calendar-check',
                'color'       => 'indigo',
                'description' => 'Müşteri randevularını bir gün önceden otomatik hatırlatın.',
                'category'    => 'reminder',
                'default_template' => 'Sayın {musteri_adi}, yarın saat {randevu_saati} için randevunuz bulunmaktadır. Bekliyoruz! {firma_adi}',
            ],
            'new_year' => [
                'name'        => 'Yılbaşı / Özel Gün',
                'icon'        => 'fas fa-glass-cheers',
                'color'       => 'violet',
                'description' => 'Yılbaşı, bayram ve özel günlerde tüm müşterilerinize toplu kutlama mesajı gönderin.',
                'category'    => 'general',
                'default_template' => 'Değerli müşterimiz {musteri_adi}, yeni yılınızı en içten dileklerimizle kutlarız! Sağlık ve mutluluk dolu bir yıl geçirmenizi dileriz. 🎄 {firma_adi}',
            ],
            'invoice_created' => [
                'name'        => 'Fatura Bilgilendirme',
                'icon'        => 'fas fa-file-invoice',
                'color'       => 'slate',
                'description' => 'Yeni fatura oluşturulduğunda müşteriye otomatik bilgi gönderin.',
                'category'    => 'payment',
                'default_template' => 'Sayın {musteri_adi}, {tutar} tutarında faturanız oluşturulmuştur. Fatura No: {siparis_no}. {firma_adi}',
            ],
            'stock_alert' => [
                'name'        => 'Stok Uyarı Bildirimi',
                'icon'        => 'fas fa-boxes-stacked',
                'color'       => 'cyan',
                'description' => 'İlgilendiği ürün tekrar stoğa girdiğinde müşteriye bildirim gönderin.',
                'category'    => 'sales',
                'default_template' => 'İyi haber {musteri_adi}! Aradığınız {urun_adi} ürünü tekrar stokta. Hemen sipariş verin! {firma_adi}',
            ],
        ];
    }

    /**
     * Renk class'ını döndür
     */
    public function getColorClassesAttribute(): array
    {
        $types = self::getAutomationTypes();
        $color = $types[$this->automation_type]['color'] ?? 'gray';

        return [
            'bg'    => "bg-{$color}-100",
            'text'  => "text-{$color}-600",
            'badge' => "bg-{$color}-50 text-{$color}-700 border-{$color}-200",
            'ring'  => "ring-{$color}-500",
        ];
    }

    public function getIconAttribute(): string
    {
        $types = self::getAutomationTypes();
        return $types[$this->automation_type]['icon'] ?? 'fas fa-cog';
    }

    /**
     * Varsayılan otomasyonları oluştur
     */
    public static function seedDefaults(?int $tenantId = null): void
    {
        foreach (self::getAutomationTypes() as $type => $config) {
            // Şablon varsa bul/oluştur
            $templateCode = 'auto_' . $type;
            $template = SmsTemplate::firstOrCreate(
                ['code' => $templateCode],
                [
                    'tenant_id' => $tenantId,
                    'name'      => $config['name'] . ' Şablonu',
                    'content'   => $config['default_template'],
                    'category'  => $config['category'],
                    'is_active' => true,
                ]
            );

            self::firstOrCreate(
                ['automation_type' => $type, 'tenant_id' => $tenantId],
                [
                    'name'        => $config['name'],
                    'template_id' => $template->id,
                    'is_active'   => false,
                    'send_time'   => '10:00',
                    'description' => $config['description'],
                ]
            );
        }
    }
}
