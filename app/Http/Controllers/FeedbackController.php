<?php

namespace App\Http\Controllers;

use App\Models\FeedbackMessage;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Admin: Tüm geri bildirimler listesi
     */
    public function index(Request $request)
    {
        // Süper admin tüm mesajları, normal admin sadece kendi tenant'ını görür
        $query = FeedbackMessage::with('user', 'repliedByUser')->latest();

        if (!auth()->user()->is_super_admin && auth()->user()->tenant_id) {
            $query->whereHas('user', fn($q) => $q->where('tenant_id', auth()->user()->tenant_id));
        }

        // Filtreler
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('message', 'like', "%{$s}%")
                  ->orWhere('page_url', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"));
            });
        }

        $messages = $query->paginate(25)->withQueryString();

        // İstatistikler
        $stats = [
            'total'       => FeedbackMessage::count(),
            'open'        => FeedbackMessage::where('status', 'open')->count(),
            'in_progress' => FeedbackMessage::where('status', 'in_progress')->count(),
            'resolved'    => FeedbackMessage::where('status', 'resolved')->count(),
            'bugs'        => FeedbackMessage::where('category', 'bug')->unresolved()->count(),
            'today'       => FeedbackMessage::whereDate('created_at', today())->count(),
        ];

        return view('feedback.index', compact('messages', 'stats'));
    }

    /**
     * Widget'tan geri bildirim kaydet (AJAX)
     */
    public function store(Request $request)
    {
        $request->validate([
            'message'  => 'required|string|min:3|max:2000',
            'category' => 'in:bug,suggestion,question,other',
            'priority' => 'in:low,normal,high,critical',
            'page_url' => 'nullable|string|max:500',
        ]);

        $feedback = FeedbackMessage::create([
            'user_id'  => auth()->id(),
            'message'  => $request->message,
            'category' => $request->category ?? 'bug',
            'priority' => $request->priority ?? 'normal',
            'page_url' => $request->page_url ?? url()->previous(),
            'status'   => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Geri bildiriminiz alındı. Teşekkür ederiz!',
            'feedback' => [
                'id'             => $feedback->id,
                'message'        => $feedback->message,
                'category'       => $feedback->category,
                'category_label' => $feedback->category_label,
                'category_icon'  => $feedback->category_icon,
                'status'         => $feedback->status,
                'created_at'     => $feedback->created_at->format('d.m.Y H:i'),
            ],
        ]);
    }

    /**
     * Kullanıcının kendi geri bildirimleri
     * - AJAX isteğinde JSON döner (widget için)
     * - Normal GET isteğinde blade view döner
     */
    public function myFeedback(Request $request)
    {
        $messages = FeedbackMessage::where('user_id', auth()->id())
            ->latest()
            ->take(50)
            ->get();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'messages' => $messages->map(fn($m) => [
                    'id'             => $m->id,
                    'message'        => $m->message,
                    'category'       => $m->category,
                    'category_label' => $m->category_label,
                    'category_icon'  => $m->category_icon,
                    'category_color' => $m->category_color,
                    'status'         => $m->status,
                    'status_label'   => $m->status_label,
                    'admin_reply'    => $m->admin_reply,
                    'replied_at'     => $m->replied_at?->format('d.m.Y H:i'),
                    'created_at'     => $m->created_at->format('d.m.Y H:i'),
                ]),
            ]);
        }

        return view('feedback.my-feedback', compact('messages'));
    }

    /**
     * Admin: Durum güncelle
     */
    public function updateStatus(Request $request, FeedbackMessage $feedback)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $feedback->update(['status' => $request->status]);

        return back()->with('success', 'Durum güncellendi: ' . $feedback->status_label);
    }

    /**
     * Admin: Yanıt ver
     */
    public function reply(Request $request, FeedbackMessage $feedback)
    {
        $request->validate([
            'admin_reply' => 'required|string|min:2|max:2000',
        ]);

        $feedback->update([
            'admin_reply' => $request->admin_reply,
            'replied_by'  => auth()->id(),
            'replied_at'  => now(),
            'status'      => $feedback->status === 'open' ? 'in_progress' : $feedback->status,
        ]);

        return back()->with('success', 'Yanıt gönderildi.');
    }
}
