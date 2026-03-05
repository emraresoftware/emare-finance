<?php

namespace App\Http\Controllers;

use App\Models\SmsSetting;
use App\Models\SmsTemplate;
use App\Models\SmsScenario;
use App\Models\SmsLog;
use App\Models\SmsBlacklist;
use App\Models\SmsAutomationConfig;
use App\Models\SmsAutomationQueue;
use App\Models\Customer;
use App\Models\CustomerSegment;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SmsController extends Controller
{
    // ══════════════════════════════════════════════════════════════
    // SMS DASHBOARD
    // ══════════════════════════════════════════════════════════════
    public function index()
    {
        $settings = SmsSetting::first();

        $stats = [
            'total_sent'     => SmsLog::where('status', 'sent')->count() + SmsLog::where('status', 'delivered')->count(),
            'total_failed'   => SmsLog::where('status', 'failed')->count(),
            'total_pending'  => SmsLog::where('status', 'pending')->count(),
            'total_cost'     => SmsLog::whereIn('status', ['sent', 'delivered'])->sum('cost'),
            'today_sent'     => SmsLog::whereIn('status', ['sent', 'delivered'])->whereDate('created_at', today())->count(),
            'active_scenarios' => SmsScenario::where('is_active', true)->count(),
            'templates_count'  => SmsTemplate::count(),
            'balance'        => $settings?->balance ?? 0,
        ];

        $recentLogs = SmsLog::with('customer', 'scenario')
            ->latest()
            ->take(10)
            ->get();

        $dailyStats = SmsLog::selectRaw('DATE(created_at) as date, status, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        return view('sms.index', compact('settings', 'stats', 'recentLogs', 'dailyStats'));
    }

    // ══════════════════════════════════════════════════════════════
    // AYARLAR
    // ══════════════════════════════════════════════════════════════
    public function settings()
    {
        $settings = SmsSetting::first();
        $providers = SmsSetting::getProviderOptions();

        return view('sms.settings', compact('settings', 'providers'));
    }

    public function settingsUpdate(Request $request)
    {
        $request->validate([
            'provider'   => 'required|string',
            'sender_id'  => 'nullable|string|max:11',
            'api_key'    => 'nullable|string',
            'api_secret' => 'nullable|string',
            'username'   => 'nullable|string',
            'password'   => 'nullable|string',
            'api_url'    => 'nullable|url',
        ]);

        $data = $request->only(['provider', 'sender_id', 'api_key', 'api_secret', 'username', 'password', 'api_url']);
        $data['is_active'] = $request->boolean('is_active');
        $data['tenant_id'] = auth()->user()->tenant_id;

        $settings = SmsSetting::first();
        if ($settings) {
            // Boş gelenleri güncelleme (şifre alanları)
            if (empty($data['api_key'])) unset($data['api_key']);
            if (empty($data['api_secret'])) unset($data['api_secret']);
            if (empty($data['password'])) unset($data['password']);
            $settings->update($data);
        } else {
            SmsSetting::create($data);
        }

        return back()->with('success', 'SMS ayarları güncellendi.');
    }

    public function testSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10',
        ]);

        $smsService = new SmsService();
        $log = $smsService->send(
            $request->phone,
            'Bu bir test mesajıdır. Emare Finance SMS entegrasyonu başarıyla çalışıyor.',
            ['trigger_event' => 'test']
        );

        if ($log && $log->status === 'sent') {
            return back()->with('success', 'Test SMS başarıyla gönderildi!');
        }

        return back()->with('error', 'SMS gönderilemedi: ' . ($log?->error_message ?? 'Bilinmeyen hata'));
    }

    public function checkBalance()
    {
        $smsService = new SmsService();
        $balance = $smsService->checkBalance();

        if ($balance !== null) {
            return back()->with('success', "Güncel bakiye: {$balance} kredi");
        }

        return back()->with('error', 'Bakiye sorgulanamadı.');
    }

    // ══════════════════════════════════════════════════════════════
    // ŞABLONLAR
    // ══════════════════════════════════════════════════════════════
    public function templates()
    {
        $templates = SmsTemplate::withCount('scenarios')
            ->latest()
            ->paginate(15);

        $categories = SmsTemplate::getCategoryOptions();

        return view('sms.templates.index', compact('templates', 'categories'));
    }

    public function templateCreate()
    {
        $categories = SmsTemplate::getCategoryOptions();
        $variables = SmsTemplate::getAvailableVariables();

        return view('sms.templates.create', compact('categories', 'variables'));
    }

    public function templateStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:100|unique:sms_templates,code',
            'content'  => 'required|string|max:918', // 6 SMS parça
            'category' => 'required|string',
        ]);

        SmsTemplate::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name'      => $request->name,
            'code'      => $request->code,
            'content'   => $request->content,
            'category'  => $request->category,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('sms.templates.index')->with('success', 'Şablon oluşturuldu.');
    }

    public function templateEdit($id)
    {
        $template = SmsTemplate::findOrFail($id);
        $categories = SmsTemplate::getCategoryOptions();
        $variables = SmsTemplate::getAvailableVariables();

        return view('sms.templates.edit', compact('template', 'categories', 'variables'));
    }

    public function templateUpdate(Request $request, $id)
    {
        $template = SmsTemplate::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:100|unique:sms_templates,code,' . $id,
            'content'  => 'required|string|max:918',
            'category' => 'required|string',
        ]);

        $template->update([
            'name'      => $request->name,
            'code'      => $request->code,
            'content'   => $request->content,
            'category'  => $request->category,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('sms.templates.index')->with('success', 'Şablon güncellendi.');
    }

    public function templateDestroy($id)
    {
        SmsTemplate::findOrFail($id)->delete();
        return back()->with('success', 'Şablon silindi.');
    }

    // ══════════════════════════════════════════════════════════════
    // SENARYOLAR
    // ══════════════════════════════════════════════════════════════
    public function scenarios()
    {
        $scenarios = SmsScenario::with('template', 'segment')
            ->withCount('logs')
            ->latest()
            ->paginate(15);

        return view('sms.scenarios.index', compact('scenarios'));
    }

    public function scenarioCreate()
    {
        $templates = SmsTemplate::where('is_active', true)->get();
        $segments = CustomerSegment::where('is_active', true)->get();
        $triggerEvents = SmsScenario::getTriggerEvents();
        $targetTypes = SmsScenario::getTargetTypes();
        $scheduleTypes = SmsScenario::getScheduleTypes();
        $customerTypes = SmsScenario::getCustomerTypes();

        return view('sms.scenarios.create', compact(
            'templates', 'segments', 'triggerEvents',
            'targetTypes', 'scheduleTypes', 'customerTypes'
        ));
    }

    public function scenarioStore(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'trigger_event' => 'required|string',
            'template_id'   => 'required|exists:sms_templates,id',
            'target_type'   => 'required|string',
            'schedule_type' => 'required|string',
        ]);

        $conditions = [];
        if ($request->filled('min_amount')) $conditions['min_amount'] = $request->min_amount;
        if ($request->filled('max_amount')) $conditions['max_amount'] = $request->max_amount;
        if ($request->filled('inactive_days')) $conditions['inactive_days'] = $request->inactive_days;

        SmsScenario::create([
            'tenant_id'            => auth()->user()->tenant_id,
            'name'                 => $request->name,
            'trigger_event'        => $request->trigger_event,
            'template_id'          => $request->template_id,
            'target_type'          => $request->target_type,
            'customer_type_filter' => $request->customer_type_filter,
            'segment_id'           => $request->segment_id,
            'conditions'           => !empty($conditions) ? $conditions : null,
            'schedule_type'        => $request->schedule_type,
            'delay_minutes'        => $request->delay_minutes,
            'cron_expression'      => $request->cron_expression,
            'send_time'            => $request->send_time,
            'is_active'            => $request->boolean('is_active', true),
            'priority'             => $request->priority ?? 0,
        ]);

        return redirect()->route('sms.scenarios.index')->with('success', 'Senaryo oluşturuldu.');
    }

    public function scenarioEdit($id)
    {
        $scenario = SmsScenario::findOrFail($id);
        $templates = SmsTemplate::where('is_active', true)->get();
        $segments = CustomerSegment::where('is_active', true)->get();
        $triggerEvents = SmsScenario::getTriggerEvents();
        $targetTypes = SmsScenario::getTargetTypes();
        $scheduleTypes = SmsScenario::getScheduleTypes();
        $customerTypes = SmsScenario::getCustomerTypes();

        return view('sms.scenarios.edit', compact(
            'scenario', 'templates', 'segments', 'triggerEvents',
            'targetTypes', 'scheduleTypes', 'customerTypes'
        ));
    }

    public function scenarioUpdate(Request $request, $id)
    {
        $scenario = SmsScenario::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'trigger_event' => 'required|string',
            'template_id'   => 'required|exists:sms_templates,id',
            'target_type'   => 'required|string',
            'schedule_type' => 'required|string',
        ]);

        $conditions = [];
        if ($request->filled('min_amount')) $conditions['min_amount'] = $request->min_amount;
        if ($request->filled('max_amount')) $conditions['max_amount'] = $request->max_amount;
        if ($request->filled('inactive_days')) $conditions['inactive_days'] = $request->inactive_days;

        $scenario->update([
            'name'                 => $request->name,
            'trigger_event'        => $request->trigger_event,
            'template_id'          => $request->template_id,
            'target_type'          => $request->target_type,
            'customer_type_filter' => $request->customer_type_filter,
            'segment_id'           => $request->segment_id,
            'conditions'           => !empty($conditions) ? $conditions : null,
            'schedule_type'        => $request->schedule_type,
            'delay_minutes'        => $request->delay_minutes,
            'cron_expression'      => $request->cron_expression,
            'send_time'            => $request->send_time,
            'is_active'            => $request->boolean('is_active', true),
            'priority'             => $request->priority ?? 0,
        ]);

        return redirect()->route('sms.scenarios.index')->with('success', 'Senaryo güncellendi.');
    }

    public function scenarioToggle($id)
    {
        $scenario = SmsScenario::findOrFail($id);
        $scenario->update(['is_active' => !$scenario->is_active]);

        $status = $scenario->is_active ? 'aktif' : 'pasif';
        return back()->with('success', "Senaryo {$status} yapıldı.");
    }

    public function scenarioDestroy($id)
    {
        SmsScenario::findOrFail($id)->delete();
        return back()->with('success', 'Senaryo silindi.');
    }

    // ══════════════════════════════════════════════════════════════
    // LOGLAR
    // ══════════════════════════════════════════════════════════════
    public function logs(Request $request)
    {
        $query = SmsLog::with('customer', 'scenario', 'template');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $logs = $query->latest()->paginate(20)->withQueryString();
        $statusOptions = SmsLog::getStatusOptions();

        return view('sms.logs.index', compact('logs', 'statusOptions'));
    }

    // ══════════════════════════════════════════════════════════════
    // KARA LİSTE
    // ══════════════════════════════════════════════════════════════
    public function blacklist()
    {
        $blacklist = SmsBlacklist::latest()->paginate(20);
        return view('sms.blacklist.index', compact('blacklist'));
    }

    public function blacklistStore(Request $request)
    {
        $request->validate([
            'phone'  => 'required|string|min:10',
            'reason' => 'nullable|string|max:255',
        ]);

        SmsBlacklist::updateOrCreate(
            ['phone' => $request->phone],
            [
                'tenant_id' => auth()->user()->tenant_id,
                'reason'    => $request->reason,
            ]
        );

        return back()->with('success', 'Numara kara listeye eklendi.');
    }

    public function blacklistDestroy($id)
    {
        SmsBlacklist::findOrFail($id)->delete();
        return back()->with('success', 'Numara kara listeden çıkarıldı.');
    }

    // ══════════════════════════════════════════════════════════════
    // HIZLI SMS GÖNDER
    // ══════════════════════════════════════════════════════════════
    public function compose()
    {
        $templates = SmsTemplate::where('is_active', true)->get();
        $segments = CustomerSegment::where('is_active', true)->withCount('members')->get();
        $customerTypes = SmsScenario::getCustomerTypes();

        return view('sms.compose', compact('templates', 'segments', 'customerTypes'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'send_type' => 'required|in:single,segment,customer_type,all,manual',
        ]);

        $smsService = new SmsService();
        $content = $request->content;
        $templateId = $request->template_id;
        $template = null;

        if ($templateId) {
            $template = SmsTemplate::find($templateId);
        }

        $meta = ['trigger_event' => 'manual'];
        $sentCount = 0;

        switch ($request->send_type) {
            case 'single':
                $request->validate(['phone' => 'required|string|min:10']);
                if ($template) {
                    $smsService->sendWithTemplate($request->phone, $template, [], $meta);
                } else {
                    $request->validate(['content' => 'required|string']);
                    $smsService->send($request->phone, $content, $meta);
                }
                $sentCount = 1;
                break;

            case 'segment':
                $request->validate(['segment_id' => 'required|exists:customer_segments,id']);
                $segment = CustomerSegment::with('members')->find($request->segment_id);
                $customers = $segment->members;

                if ($template) {
                    $logs = $smsService->sendToCustomers($customers, $template, [], $meta);
                } else {
                    $request->validate(['content' => 'required|string']);
                    foreach ($customers as $customer) {
                        if ($customer->phone) {
                            $smsService->send($customer->phone, $content, array_merge($meta, ['customer_id' => $customer->id]));
                        }
                    }
                }
                $sentCount = $customers->count();
                break;

            case 'customer_type':
                $request->validate(['customer_type' => 'required|string']);
                $customers = Customer::where('type', $request->customer_type)
                    ->whereNotNull('phone')
                    ->where('phone', '!=', '')
                    ->get();

                if ($template) {
                    $smsService->sendToCustomers($customers, $template, [], $meta);
                } else {
                    $request->validate(['content' => 'required|string']);
                    foreach ($customers as $customer) {
                        $smsService->send($customer->phone, $content, array_merge($meta, ['customer_id' => $customer->id]));
                    }
                }
                $sentCount = $customers->count();
                break;

            case 'all':
                $customers = Customer::whereNotNull('phone')
                    ->where('phone', '!=', '')
                    ->get();

                if ($template) {
                    $smsService->sendToCustomers($customers, $template, [], $meta);
                } else {
                    $request->validate(['content' => 'required|string']);
                    foreach ($customers as $customer) {
                        $smsService->send($customer->phone, $content, array_merge($meta, ['customer_id' => $customer->id]));
                    }
                }
                $sentCount = $customers->count();
                break;

            case 'manual':
                $request->validate(['phones' => 'required|string']);
                $phones = array_filter(array_map('trim', preg_split('/[\n,;]+/', $request->phones)));

                if ($template) {
                    foreach ($phones as $phone) {
                        $smsService->sendWithTemplate($phone, $template, [], $meta);
                    }
                } else {
                    $request->validate(['content' => 'required|string']);
                    foreach ($phones as $phone) {
                        $smsService->send($phone, $content, $meta);
                    }
                }
                $sentCount = count($phones);
                break;
        }

        return redirect()->route('sms.logs.index')->with('success', "{$sentCount} adet SMS gönderim kuyruğuna eklendi.");
    }

    // ══════════════════════════════════════════════════════════════
    // OTOMASYONLAR
    // ══════════════════════════════════════════════════════════════
    public function automations()
    {
        // Varsayılan configleri oluştur (yoksa)
        $tenantId = auth()->user()->tenant_id;
        SmsAutomationConfig::seedDefaults($tenantId);

        $configs = SmsAutomationConfig::with('template')
            ->where(function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
                if (is_null($tenantId)) {
                    $q->orWhereNull('tenant_id');
                }
            })
            ->get()
            ->keyBy('automation_type');

        $automationTypes = SmsAutomationConfig::getAutomationTypes();
        $templates = SmsTemplate::where('is_active', true)->get();

        // Kuyruk istatistikleri
        $queueStats = [
            'pending'   => SmsAutomationQueue::where('status', 'pending')->count(),
            'sent'      => SmsAutomationQueue::where('status', 'sent')->count(),
            'failed'    => SmsAutomationQueue::where('status', 'failed')->count(),
            'today'     => SmsAutomationQueue::where('status', 'sent')->whereDate('sent_at', today())->count(),
        ];

        // Son 7 gün otomasyon gönderim grafiği
        $dailyAutomationStats = SmsAutomationQueue::selectRaw('DATE(sent_at) as date, COUNT(*) as count')
            ->where('status', 'sent')
            ->where('sent_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('sms.automations.index', compact('configs', 'automationTypes', 'templates', 'queueStats', 'dailyAutomationStats'));
    }

    public function automationToggle(Request $request, $type)
    {
        $tenantId = auth()->user()->tenant_id;

        $config = SmsAutomationConfig::where('automation_type', $type)
            ->where(function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
                if (is_null($tenantId)) {
                    $q->orWhereNull('tenant_id');
                }
            })
            ->firstOrFail();

        $config->update(['is_active' => !$config->is_active]);

        $status = $config->is_active ? 'aktif' : 'pasif';
        return back()->with('success', "{$config->name} otomasyonu {$status} yapıldı.");
    }

    public function automationUpdate(Request $request, $type)
    {
        $tenantId = auth()->user()->tenant_id;

        $config = SmsAutomationConfig::where('automation_type', $type)
            ->where(function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
                if (is_null($tenantId)) {
                    $q->orWhereNull('tenant_id');
                }
            })
            ->firstOrFail();

        $validatedData = $request->validate([
            'template_id'   => 'nullable|exists:sms_templates,id',
            'send_time'     => 'nullable|date_format:H:i',
            'days_before'   => 'nullable|integer|min:0|max:30',
            'days_after'    => 'nullable|integer|min:0|max:30',
            'inactive_days' => 'nullable|integer|min:1|max:365',
        ]);

        $config->update(array_filter($validatedData, fn($v) => !is_null($v)));

        return back()->with('success', "{$config->name} ayarları güncellendi.");
    }

    public function automationQueue(Request $request)
    {
        $query = SmsAutomationQueue::with('customer', 'template');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('trigger_event', $type);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        $queue = $query->latest()->paginate(20)->withQueryString();
        $statusOptions = SmsAutomationQueue::getStatusOptions();

        return view('sms.automations.queue', compact('queue', 'statusOptions'));
    }

    public function automationRunNow(Request $request, $type)
    {
        $tenantId = auth()->user()->tenant_id;

        $config = SmsAutomationConfig::where('automation_type', $type)
            ->where(function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId);
                if (is_null($tenantId)) {
                    $q->orWhereNull('tenant_id');
                }
            })
            ->firstOrFail();

        if (!$config->is_active) {
            return back()->with('error', 'Bu otomasyon pasif durumda. Önce aktif edin.');
        }

        // Artisan komutunu çalıştır
        \Artisan::call('sms:process-automations');

        return back()->with('success', 'Otomasyon işlemi başlatıldı. Sonuçları kuyruk sayfasından takip edebilirsiniz.');
    }
}
