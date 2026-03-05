<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Campaign;
use App\Models\CampaignUsage;
use App\Models\CustomerSegment;
use App\Models\MarketingMessage;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyPoint;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MarketingController extends Controller
{
    // ══════════════════════════════════════════════════════════════
    // DASHBOARD
    // ══════════════════════════════════════════════════════════════
    public function index()
    {
        $stats = [
            'active_campaigns' => Campaign::where('status', 'active')->count(),
            'total_quotes' => Quote::count(),
            'pending_quotes' => Quote::where('status', 'sent')->count(),
            'accepted_quotes' => Quote::where('status', 'accepted')->count(),
            'total_segments' => CustomerSegment::where('is_active', true)->count(),
            'messages_sent' => MarketingMessage::where('status', 'sent')->sum('sent_count'),
            'quote_revenue' => Quote::where('status', 'accepted')->sum('grand_total'),
            'campaign_savings' => CampaignUsage::sum('discount_applied'),
        ];

        $recentQuotes = Quote::with('customer')->latest()->take(5)->get();
        $activeCampaigns = Campaign::where('status', 'active')->latest()->take(5)->get();
        $recentMessages = MarketingMessage::latest()->take(5)->get();

        return view('marketing.index', compact('stats', 'recentQuotes', 'activeCampaigns', 'recentMessages'));
    }

    // ══════════════════════════════════════════════════════════════
    // TEKLİFLER
    // ══════════════════════════════════════════════════════════════
    public function quotes(Request $request)
    {
        $query = Quote::with('customer', 'creator');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q->where('quote_number', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%"));
        }
        if ($request->filled('date_from')) $query->where('issue_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->where('issue_date', '<=', $request->date_to);

        $quotes = $query->latest()->paginate(20)->withQueryString();
        $statusCounts = Quote::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');

        return view('marketing.quotes.index', compact('quotes', 'statusCounts'));
    }

    public function quoteCreate()
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        return view('marketing.quotes.create', compact('customers', 'products'));
    }

    public function quoteStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'valid_until' => 'required|date|after:today',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $quote = Quote::create([
            'quote_number' => Quote::generateNumber(),
            'tenant_id' => auth()->user()->tenant_id,
            'branch_id' => auth()->user()->branch_id,
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'customer_company' => $request->customer_company,
            'customer_tax_number' => $request->customer_tax_number,
            'customer_address' => $request->customer_address,
            'title' => $request->title,
            'description' => $request->description,
            'issue_date' => now(),
            'valid_until' => $request->valid_until,
            'notes' => $request->notes,
            'terms' => $request->terms,
            'created_by' => auth()->id(),
        ]);

        foreach ($request->items as $i => $item) {
            $qi = new QuoteItem([
                'product_id' => $item['product_id'] ?? null,
                'name' => $item['name'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? 'Adet',
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? 0,
                'discount_rate' => $item['discount_rate'] ?? 0,
                'sort_order' => $i,
            ]);
            $qi->calculateTotals();
            $quote->items()->save($qi);
        }

        $quote->recalculate();

        return redirect()->route('marketing.quotes.show', $quote)->with('success', 'Teklif oluşturuldu.');
    }

    public function quoteShow(Quote $quote)
    {
        $quote->load('items.product', 'customer', 'creator');
        return view('marketing.quotes.show', compact('quote'));
    }

    public function quoteSend(Quote $quote)
    {
        $quote->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
        return back()->with('success', 'Teklif gönderildi olarak işaretlendi.');
    }

    public function quoteUpdateStatus(Request $request, Quote $quote)
    {
        $request->validate(['status' => 'required|in:accepted,rejected']);
        $data = ['status' => $request->status];
        if ($request->status === 'accepted') $data['accepted_at'] = now();
        if ($request->status === 'rejected') {
            $data['rejected_at'] = now();
            $data['rejection_reason'] = $request->rejection_reason;
        }
        $quote->update($data);
        $label = $request->status === 'accepted' ? 'kabul edildi' : 'reddedildi';
        return back()->with('success', "Teklif {$label}.");
    }

    public function quoteDuplicate(Quote $quote)
    {
        $new = $quote->replicate(['quote_number', 'status', 'sent_at', 'viewed_at', 'accepted_at', 'rejected_at']);
        $new->quote_number = Quote::generateNumber();
        $new->status = 'draft';
        $new->issue_date = now();
        $new->valid_until = now()->addDays(30);
        $new->created_by = auth()->id();
        $new->save();

        foreach ($quote->items as $item) {
            $newItem = $item->replicate();
            $new->items()->save($newItem);
        }

        return redirect()->route('marketing.quotes.show', $new)->with('success', 'Teklif kopyalandı.');
    }

    // ══════════════════════════════════════════════════════════════
    // KAMPANYALAR
    // ══════════════════════════════════════════════════════════════
    public function campaigns(Request $request)
    {
        $query = Campaign::withCount('usages');

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type')) $query->where('type', $request->type);
        if ($request->filled('search')) $query->where('name', 'like', "%{$request->search}%");

        $campaigns = $query->latest()->paginate(20)->withQueryString();
        return view('marketing.campaigns.index', compact('campaigns'));
    }

    public function campaignCreate()
    {
        $segments = CustomerSegment::where('is_active', true)->get();
        $products = Product::orderBy('name')->take(200)->get();
        return view('marketing.campaigns.create', compact('segments', 'products'));
    }

    public function campaignStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:discount,bogo,bundle,loyalty_bonus,free_shipping,gift,seasonal,flash_sale',
            'starts_at' => 'required|date',
        ]);

        Campaign::create([
            'tenant_id' => auth()->user()->tenant_id,
            'branch_id' => auth()->user()->branch_id,
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'status' => $request->status ?? 'draft',
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_purchase_amount' => $request->min_purchase_amount,
            'max_discount_amount' => $request->max_discount_amount,
            'usage_limit' => $request->usage_limit,
            'per_customer_limit' => $request->per_customer_limit,
            'coupon_code' => $request->coupon_code ? strtoupper($request->coupon_code) : null,
            'target_products' => $request->target_products,
            'target_categories' => $request->target_categories,
            'target_segments' => $request->target_segments,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('marketing.campaigns.index')->with('success', 'Kampanya oluşturuldu.');
    }

    public function campaignShow(Campaign $campaign)
    {
        $campaign->load('usages.customer', 'messages');
        $dailyUsages = $campaign->usages()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(discount_applied) as total_discount')
            ->groupBy('date')->orderBy('date')->get();
        return view('marketing.campaigns.show', compact('campaign', 'dailyUsages'));
    }

    public function campaignToggle(Campaign $campaign)
    {
        $newStatus = match($campaign->status) {
            'draft' => 'active',
            'active' => 'paused',
            'paused' => 'active',
            'scheduled' => 'cancelled',
            default => $campaign->status,
        };
        $campaign->update(['status' => $newStatus]);
        return back()->with('success', "Kampanya durumu: {$campaign->status_label}");
    }

    // ══════════════════════════════════════════════════════════════
    // MÜŞTERİ SEGMENTLERİ
    // ══════════════════════════════════════════════════════════════
    public function segments(Request $request)
    {
        $segments = CustomerSegment::withCount('members')->latest()->paginate(20);
        return view('marketing.segments.index', compact('segments'));
    }

    public function segmentStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        CustomerSegment::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? '#6366f1',
            'icon' => $request->icon ?? 'fa-users',
            'type' => $request->type ?? 'manual',
            'conditions' => $request->conditions,
        ]);

        return redirect()->route('marketing.segments.index')->with('success', 'Segment oluşturuldu.');
    }

    public function segmentShow(CustomerSegment $segment)
    {
        $segment->load('members');
        $availableCustomers = Customer::whereNotIn('id', $segment->members->pluck('id'))->orderBy('name')->get();
        return view('marketing.segments.show', compact('segment', 'availableCustomers'));
    }

    public function segmentAddMembers(Request $request, CustomerSegment $segment)
    {
        $request->validate(['customer_ids' => 'required|array']);
        $segment->members()->syncWithoutDetaching($request->customer_ids);
        $segment->refreshCount();
        return back()->with('success', 'Müşteriler segmente eklendi.');
    }

    public function segmentRemoveMember(CustomerSegment $segment, Customer $customer)
    {
        $segment->members()->detach($customer->id);
        $segment->refreshCount();
        return back()->with('success', 'Müşteri segmentten çıkarıldı.');
    }

    // ══════════════════════════════════════════════════════════════
    // MESAJLAR
    // ══════════════════════════════════════════════════════════════
    public function messages(Request $request)
    {
        $query = MarketingMessage::with('segment', 'campaign');

        if ($request->filled('channel')) $query->where('channel', $request->channel);
        if ($request->filled('status')) $query->where('status', $request->status);

        $messages = $query->latest()->paginate(20)->withQueryString();
        return view('marketing.messages.index', compact('messages'));
    }

    public function messageCreate()
    {
        $segments = CustomerSegment::where('is_active', true)->get();
        $campaigns = Campaign::where('status', 'active')->get();
        return view('marketing.messages.create', compact('segments', 'campaigns'));
    }

    public function messageStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'channel' => 'required|in:email,sms,whatsapp,push',
        ]);

        $recipientCount = 0;
        if ($request->segment_id) {
            $recipientCount = CustomerSegment::find($request->segment_id)?->customer_count ?? 0;
        }

        MarketingMessage::create([
            'tenant_id' => auth()->user()->tenant_id,
            'title' => $request->title,
            'content' => $request->content,
            'channel' => $request->channel,
            'segment_id' => $request->segment_id,
            'campaign_id' => $request->campaign_id,
            'total_recipients' => $recipientCount,
            'scheduled_at' => $request->scheduled_at,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('marketing.messages.index')->with('success', 'Mesaj oluşturuldu.');
    }

    public function messageShow(MarketingMessage $message)
    {
        $message->load('segment', 'campaign', 'logs.customer');
        return view('marketing.messages.show', compact('message'));
    }

    public function messageSend(MarketingMessage $message)
    {
        $message->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_count' => $message->total_recipients,
            'delivered_count' => $message->total_recipients,
        ]);
        return back()->with('success', 'Mesaj gönderildi olarak işaretlendi.');
    }

    // ══════════════════════════════════════════════════════════════
    // SADAKAT PROGRAMI
    // ══════════════════════════════════════════════════════════════
    public function loyalty()
    {
        $program = LoyaltyProgram::first();
        $topCustomers = LoyaltyPoint::selectRaw('customer_id, SUM(CASE WHEN type="earn" THEN points ELSE 0 END) as total_earned, SUM(CASE WHEN type="redeem" THEN ABS(points) ELSE 0 END) as total_redeemed')
            ->groupBy('customer_id')
            ->orderByDesc('total_earned')
            ->with('customer')
            ->take(20)->get();
        $recentActivity = LoyaltyPoint::with('customer')->latest()->take(20)->get();
        return view('marketing.loyalty.index', compact('program', 'topCustomers', 'recentActivity'));
    }

    public function loyaltyStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'points_per_currency' => 'required|numeric|min:0',
            'currency_per_point' => 'required|numeric|min:0',
            'min_redeem_points' => 'required|integer|min:1',
        ]);

        LoyaltyProgram::updateOrCreate(
            ['tenant_id' => auth()->user()->tenant_id],
            $request->only('name', 'description', 'points_per_currency', 'currency_per_point', 'min_redeem_points', 'is_active')
        );

        return back()->with('success', 'Sadakat programı güncellendi.');
    }
}
