<?php

namespace App\Services;

use App\Models\SmsSetting;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Models\SmsBlacklist;
use App\Models\Customer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected ?SmsSetting $settings;

    public function __construct(?SmsSetting $settings = null)
    {
        $this->settings = $settings ?? SmsSetting::where('is_active', true)->first();
    }

    /**
     * Tek bir numaraya SMS gönder
     */
    public function send(string $phone, string $content, array $meta = []): ?SmsLog
    {
        if (!$this->settings || !$this->settings->is_active) {
            Log::warning('SMS ayarları aktif değil.');
            return null;
        }

        // Kara listede mi kontrol et
        if (SmsBlacklist::where('phone', $this->normalizePhone($phone))->exists()) {
            Log::info("SMS gönderilmedi - Kara listede: {$phone}");
            return null;
        }

        $normalizedPhone = $this->normalizePhone($phone);

        $log = SmsLog::create([
            'tenant_id'     => $this->settings->tenant_id,
            'phone'         => $normalizedPhone,
            'content'       => $content,
            'status'        => 'pending',
            'trigger_event' => $meta['trigger_event'] ?? 'manual',
            'scenario_id'   => $meta['scenario_id'] ?? null,
            'template_id'   => $meta['template_id'] ?? null,
            'customer_id'   => $meta['customer_id'] ?? null,
            'meta'          => $meta,
        ]);

        try {
            $result = $this->sendViaProvider($normalizedPhone, $content);

            $log->update([
                'status'              => $result['success'] ? 'sent' : 'failed',
                'provider_message_id' => $result['message_id'] ?? null,
                'error_message'       => $result['error'] ?? null,
                'cost'                => $result['cost'] ?? 0,
                'sent_at'             => $result['success'] ? now() : null,
            ]);
        } catch (\Exception $e) {
            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error("SMS gönderim hatası: {$e->getMessage()}", ['phone' => $phone]);
        }

        return $log;
    }

    /**
     * Şablon kullanarak gönder
     */
    public function sendWithTemplate(string $phone, SmsTemplate $template, array $variables = [], array $meta = []): ?SmsLog
    {
        $content = $template->render($variables);
        $meta['template_id'] = $template->id;
        return $this->send($phone, $content, $meta);
    }

    /**
     * Toplu SMS gönder
     */
    public function sendBulk(array $recipients, string $content, array $meta = []): array
    {
        $logs = [];
        foreach ($recipients as $recipient) {
            $phone = is_array($recipient) ? $recipient['phone'] : $recipient;
            $customerMeta = is_array($recipient) ? array_merge($meta, $recipient) : $meta;
            $logs[] = $this->send($phone, $content, $customerMeta);
        }
        return array_filter($logs);
    }

    /**
     * Müşteri listesine şablonla gönder
     */
    public function sendToCustomers($customers, SmsTemplate $template, array $extraVars = [], array $meta = []): array
    {
        $logs = [];
        foreach ($customers as $customer) {
            if (empty($customer->phone)) continue;

            $variables = array_merge([
                'musteri_adi' => $customer->name ?? '',
                'telefon'     => $customer->phone ?? '',
            ], $extraVars);

            $customerMeta = array_merge($meta, ['customer_id' => $customer->id]);
            $logs[] = $this->sendWithTemplate($customer->phone, $template, $variables, $customerMeta);
        }
        return array_filter($logs);
    }

    /**
     * Sağlayıcı üzerinden gönderim
     */
    protected function sendViaProvider(string $phone, string $content): array
    {
        return match ($this->settings->provider) {
            'netgsm'        => $this->sendNetGSM($phone, $content),
            'iletimerkezi'  => $this->sendIletiMerkezi($phone, $content),
            'twilio'        => $this->sendTwilio($phone, $content),
            'mutlucell'     => $this->sendMutlucell($phone, $content),
            default         => $this->sendCustom($phone, $content),
        };
    }

    protected function sendNetGSM(string $phone, string $content): array
    {
        try {
            $response = Http::get('https://api.netgsm.com.tr/sms/send/get', [
                'usercode' => $this->settings->username,
                'password' => $this->settings->api_key,
                'gsmno'    => $phone,
                'message'  => $content,
                'msgheader' => $this->settings->sender_id,
            ]);

            $body = $response->body();
            $parts = explode(' ', $body);
            $code = $parts[0] ?? '';

            // NetGSM başarı kodları: 00, 01, 02
            if (in_array($code, ['00', '01', '02'])) {
                return [
                    'success'    => true,
                    'message_id' => $parts[1] ?? null,
                    'cost'       => 0.035,
                ];
            }

            return [
                'success' => false,
                'error'   => "NetGSM Hata Kodu: {$code} - {$body}",
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function sendIletiMerkezi(string $phone, string $content): array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://api.iletimerkezi.com/v1/send-sms/get/', [
                'request' => [
                    'authentication' => [
                        'key'  => $this->settings->api_key,
                        'hash' => $this->settings->api_secret,
                    ],
                    'order' => [
                        'sender'  => $this->settings->sender_id,
                        'message' => [
                            'text'       => $content,
                            'receipents' => [
                                'number' => [$phone],
                            ],
                        ],
                    ],
                ],
            ]);

            $data = $response->json();
            $statusCode = $data['response']['status']['code'] ?? '';

            if ($statusCode == '200') {
                return [
                    'success'    => true,
                    'message_id' => $data['response']['order']['id'] ?? null,
                    'cost'       => 0.035,
                ];
            }

            return [
                'success' => false,
                'error'   => $data['response']['status']['message'] ?? 'Bilinmeyen hata',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function sendTwilio(string $phone, string $content): array
    {
        try {
            $sid = $this->settings->username;
            $token = $this->settings->api_key;

            $response = Http::withBasicAuth($sid, $token)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'To'   => '+90' . ltrim($phone, '0'),
                    'From' => $this->settings->sender_id,
                    'Body' => $content,
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['sid'])) {
                return [
                    'success'    => true,
                    'message_id' => $data['sid'],
                    'cost'       => $data['price'] ?? 0.05,
                ];
            }

            return [
                'success' => false,
                'error'   => $data['message'] ?? 'Twilio hatası',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function sendMutlucell(string $phone, string $content): array
    {
        try {
            $response = Http::post('https://smsgw.mutlucell.com/smsgw-ws/sndblkex', [
                'username' => $this->settings->username,
                'password' => $this->settings->api_key,
                'sender'   => $this->settings->sender_id,
                'phone'    => $phone,
                'message'  => $content,
            ]);

            $body = $response->body();
            if (str_contains($body, 'success') || is_numeric($body) && $body > 0) {
                return [
                    'success'    => true,
                    'message_id' => trim($body),
                    'cost'       => 0.035,
                ];
            }

            return ['success' => false, 'error' => "Mutlucell: {$body}"];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function sendCustom(string $phone, string $content): array
    {
        if (!$this->settings->api_url) {
            return ['success' => false, 'error' => 'Özel API URL tanımlı değil'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->settings->api_key,
                'Content-Type'  => 'application/json',
            ])->post($this->settings->api_url, [
                'phone'   => $phone,
                'message' => $content,
                'sender'  => $this->settings->sender_id,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success'    => true,
                    'message_id' => $data['id'] ?? null,
                    'cost'       => $data['cost'] ?? 0,
                ];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Telefon numarasını normalize et (Türkiye)
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '90') && strlen($phone) === 12) {
            $phone = '0' . substr($phone, 2);
        }

        if (str_starts_with($phone, '+90')) {
            $phone = '0' . substr($phone, 3);
        }

        if (!str_starts_with($phone, '0') && strlen($phone) === 10) {
            $phone = '0' . $phone;
        }

        return $phone;
    }

    /**
     * Bakiye sorgula
     */
    public function checkBalance(): ?float
    {
        if (!$this->settings) return null;

        return match ($this->settings->provider) {
            'netgsm' => $this->checkNetGSMBalance(),
            default  => $this->settings->balance,
        };
    }

    protected function checkNetGSMBalance(): ?float
    {
        try {
            $response = Http::get('https://api.netgsm.com.tr/balance/list/get', [
                'usercode' => $this->settings->username,
                'password' => $this->settings->api_key,
                'stession' => 1,
            ]);

            $body = $response->body();
            if (is_numeric(trim($body))) {
                $balance = (float) trim($body);
                $this->settings->update(['balance' => $balance]);
                return $balance;
            }

            return $this->settings->balance;
        } catch (\Exception $e) {
            return $this->settings->balance;
        }
    }
}
