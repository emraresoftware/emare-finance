<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HardwareDevice extends Model
{
    protected $fillable = [
        'name', 'type', 'connection', 'protocol', 'model', 'manufacturer',
        'vendor_id', 'product_id', 'ip_address', 'port', 'serial_port',
        'baud_rate', 'mac_address', 'settings', 'is_default', 'is_active',
        'last_seen_at', 'status', 'branch_id',
    ];

    protected $casts = [
        'settings'     => 'array',
        'is_default'   => 'boolean',
        'is_active'    => 'boolean',
        'last_seen_at' => 'datetime',
        'port'         => 'integer',
        'baud_rate'    => 'integer',
    ];

    // ── İlişkiler ──

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // ── Scope'lar ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // ── Yardımcı Metotlar ──

    public function getTypeLabel(): string
    {
        $types = config('hardware.device_types', []);
        return $types[$this->type]['label'] ?? $this->type;
    }

    public function getTypeIcon(): string
    {
        $types = config('hardware.device_types', []);
        return $types[$this->type]['icon'] ?? 'fas fa-plug';
    }

    public function getTypeColor(): string
    {
        $types = config('hardware.device_types', []);
        return $types[$this->type]['color'] ?? 'gray';
    }

    public function getConnectionLabel(): string
    {
        return match($this->connection) {
            'usb'       => 'USB',
            'serial'    => 'Seri Port',
            'network'   => 'Ağ (TCP/IP)',
            'bluetooth' => 'Bluetooth',
            'printer'   => 'Yazıcı üzerinden',
            default     => $this->connection,
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'connected'    => 'Bağlı',
            'disconnected' => 'Bağlı Değil',
            'error'        => 'Hata',
            'printing'     => 'Yazdırılıyor',
            default        => $this->status,
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'connected'    => 'green',
            'disconnected' => 'gray',
            'error'        => 'red',
            'printing'     => 'blue',
            default        => 'gray',
        };
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function updateSetting(string $key, mixed $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->update(['settings' => $settings]);
    }

    public function markConnected(): void
    {
        $this->update([
            'status'       => 'connected',
            'last_seen_at' => now(),
        ]);
    }

    public function markDisconnected(): void
    {
        $this->update(['status' => 'disconnected']);
    }

    /**
     * Bu türdeki varsayılan cihazı getir.
     */
    public static function getDefault(string $type): ?self
    {
        return static::ofType($type)->active()->default()->first()
            ?? static::ofType($type)->active()->first();
    }

    /**
     * JSON olarak frontend'e gönderilecek bağlantı bilgileri.
     */
    public function toDriverConfig(): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'type'       => $this->type,
            'connection' => $this->connection,
            'protocol'   => $this->protocol,
            'config'     => array_filter([
                'vendor_id'   => $this->vendor_id,
                'product_id'  => $this->product_id,
                'ip_address'  => $this->ip_address,
                'port'        => $this->port ?? 9100,
                'serial_port' => $this->serial_port,
                'baud_rate'   => $this->baud_rate,
                'mac_address' => $this->mac_address,
            ]),
            'settings'   => $this->settings ?? [],
            'is_default' => $this->is_default,
        ];
    }
}
