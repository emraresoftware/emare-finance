<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HardwareDriver extends Model
{
    protected $table = 'hardware_drivers';

    protected $fillable = [
        'device_type',
        'manufacturer',
        'model',
        'vendor_id',
        'product_id',
        'protocol',
        'connections',
        'features',
        'specs',
        'notes',
    ];

    protected $casts = [
        'connections' => 'array',
        'features'    => 'array',
        'specs'       => 'array',
    ];

    // ── Scope'lar ──

    public function scopeOfType($query, string $type)
    {
        return $query->where('device_type', $type);
    }

    public function scopeByManufacturer($query, string $manufacturer)
    {
        return $query->where('manufacturer', $manufacturer);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) return $query;

        return $query->where(function ($q) use ($search) {
            $q->where('manufacturer', 'like', "%{$search}%")
              ->orWhere('model', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    public function scopeWithUsb($query)
    {
        return $query->whereNotNull('vendor_id')->whereNotNull('product_id');
    }

    // ── Yardımcı Metodlar ──

    /**
     * Tam cihaz adını döner.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->manufacturer} {$this->model}";
    }

    /**
     * Cihaz türünün Türkçe etiketini döner.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->device_type) {
            'receipt_printer'  => 'Fiş Yazıcı',
            'label_printer'    => 'Etiket Yazıcı',
            'a4_printer'       => 'A4 Yazıcı',
            'barcode_scanner'  => 'Barkod Okuyucu',
            'scale'            => 'Terazi',
            'cash_drawer'      => 'Kasa Çekmecesi',
            'customer_display' => 'Müşteri Ekranı',
            default            => $this->device_type,
        };
    }

    /**
     * Cihaz türüne ait ikonu döner.
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->device_type) {
            'receipt_printer'  => 'fas fa-receipt',
            'label_printer'    => 'fas fa-tags',
            'a4_printer'       => 'fas fa-print',
            'barcode_scanner'  => 'fas fa-barcode',
            'scale'            => 'fas fa-weight-scale',
            'cash_drawer'      => 'fas fa-cash-register',
            'customer_display' => 'fas fa-tv',
            default            => 'fas fa-plug',
        };
    }

    /**
     * Cihaz türüne ait rengi döner.
     */
    public function getTypeColorAttribute(): string
    {
        return match ($this->device_type) {
            'receipt_printer'  => 'indigo',
            'label_printer'    => 'purple',
            'a4_printer'       => 'blue',
            'barcode_scanner'  => 'green',
            'scale'            => 'amber',
            'cash_drawer'      => 'emerald',
            'customer_display' => 'cyan',
            default            => 'gray',
        };
    }

    /**
     * Bağlantı türlerini okunabilir string olarak döner.
     */
    public function getConnectionLabelsAttribute(): string
    {
        $labels = [
            'usb'       => 'USB',
            'serial'    => 'Seri Port',
            'network'   => 'Ağ (TCP/IP)',
            'wifi'      => 'Wi-Fi',
            'bluetooth' => 'Bluetooth',
            'rj11'      => 'RJ11',
        ];

        return collect($this->connections ?? [])
            ->map(fn($c) => $labels[$c] ?? $c)
            ->implode(', ');
    }

    /**
     * Belirli bir spec değerini döner.
     */
    public function getSpec(string $key, $default = null)
    {
        return $this->specs[$key] ?? $default;
    }

    /**
     * Belirli bir özelliğe sahip mi kontrolü.
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }

    /**
     * Belirli bağlantı türünü destekliyor mu?
     */
    public function supportsConnection(string $connection): bool
    {
        return in_array($connection, $this->connections ?? []);
    }

    /**
     * Frontend driver config için JSON çıktısı.
     */
    public function toDriverConfig(): array
    {
        return [
            'id'           => $this->id,
            'device_type'  => $this->device_type,
            'manufacturer' => $this->manufacturer,
            'model'        => $this->model,
            'full_name'    => $this->full_name,
            'vendor_id'    => $this->vendor_id,
            'product_id'   => $this->product_id,
            'protocol'     => $this->protocol,
            'connections'  => $this->connections,
            'features'     => $this->features,
            'specs'        => $this->specs,
        ];
    }

    /**
     * Türe göre istatistik döner.
     */
    public static function getStats(): array
    {
        return static::query()
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();
    }

    /**
     * Tüm üreticileri türe göre döner.
     */
    public static function getManufacturers(?string $type = null): array
    {
        $query = static::query();
        if ($type) $query->ofType($type);

        return $query->distinct()
            ->orderBy('manufacturer')
            ->pluck('manufacturer')
            ->toArray();
    }
}
