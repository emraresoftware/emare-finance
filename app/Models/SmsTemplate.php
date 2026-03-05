<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsTemplate extends Model
{
    protected $fillable = [
        'tenant_id', 'name', 'code', 'content',
        'category', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scenarios(): HasMany
    {
        return $this->hasMany(SmsScenario::class, 'template_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SmsLog::class, 'template_id');
    }

    public static function getCategoryOptions(): array
    {
        return [
            'general'   => 'Genel',
            'sales'     => 'Satış',
            'payment'   => 'Ödeme',
            'loyalty'   => 'Sadakat',
            'marketing' => 'Pazarlama',
            'reminder'  => 'Hatırlatma',
            'birthday'  => 'Doğum Günü',
        ];
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::getCategoryOptions()[$this->category] ?? $this->category;
    }

    /**
     * Şablondaki değişkenleri listele
     */
    public function getVariablesAttribute(): array
    {
        preg_match_all('/\{(\w+)\}/', $this->content, $matches);
        return $matches[1] ?? [];
    }

    /**
     * Şablon içeriğini değişkenlerle doldur
     */
    public function render(array $variables = []): string
    {
        $content = $this->content;
        foreach ($variables as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        return $content;
    }

    public static function getAvailableVariables(): array
    {
        return [
            '{musteri_adi}'    => 'Müşteri adı',
            '{firma_adi}'      => 'Firma adı',
            '{tutar}'          => 'Tutar (₺)',
            '{tarih}'          => 'Tarih',
            '{urun_adi}'       => 'Ürün adı',
            '{kampanya_adi}'   => 'Kampanya adı',
            '{indirim}'        => 'İndirim tutarı',
            '{puan}'           => 'Puan',
            '{sube_adi}'       => 'Şube adı',
            '{telefon}'        => 'Telefon',
            '{siparis_no}'     => 'Sipariş numarası',
            '{odeme_tarihi}'   => 'Ödeme tarihi',
            '{bakiye}'         => 'Bakiye',
        ];
    }
}
