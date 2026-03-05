<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntegrationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntegrationRequestController extends Controller
{
    /**
     * Tüm entegrasyon başvurularını listele.
     */
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $query = IntegrationRequest::with(['user', 'tenant', 'reviewer'])
            ->forTenant($tenantId);

        // Filtreler
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('integration_type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('integration_name', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $requests = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'pending'  => IntegrationRequest::forTenant($tenantId)->pending()->count(),
            'approved' => IntegrationRequest::forTenant($tenantId)->where('status', 'approved')->count(),
            'rejected' => IntegrationRequest::forTenant($tenantId)->where('status', 'rejected')->count(),
            'total'    => IntegrationRequest::forTenant($tenantId)->count(),
        ];

        return view('admin.integration-requests.index', compact('requests', 'stats'));
    }

    /**
     * Başvuruyu onayla.
     */
    public function approve(Request $request, IntegrationRequest $integrationRequest)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        $integrationRequest->update([
            'status'      => 'approved',
            'admin_note'  => $request->admin_note,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', $integrationRequest->integration_name . ' başvurusu onaylandı.');
    }

    /**
     * Başvuruyu reddet.
     */
    public function reject(Request $request, IntegrationRequest $integrationRequest)
    {
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ], [
            'admin_note.required' => 'Red gerekçesi zorunludur.',
        ]);

        $integrationRequest->update([
            'status'      => 'rejected',
            'admin_note'  => $request->admin_note,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', $integrationRequest->integration_name . ' başvurusu reddedildi.');
    }

    /**
     * Başvuru detayı (AJAX).
     */
    public function show(IntegrationRequest $integrationRequest)
    {
        $integrationRequest->load(['user', 'reviewer']);

        return response()->json([
            'id'               => $integrationRequest->id,
            'integration_type' => $integrationRequest->integration_type,
            'integration_name' => $integrationRequest->integration_name,
            'message'          => $integrationRequest->message,
            'status'           => $integrationRequest->status,
            'status_label'     => $integrationRequest->status_label,
            'admin_note'       => $integrationRequest->admin_note,
            'user_name'        => $integrationRequest->user->name ?? '-',
            'user_email'       => $integrationRequest->user->email ?? '-',
            'reviewer_name'    => $integrationRequest->reviewer->name ?? null,
            'reviewed_at'      => $integrationRequest->reviewed_at?->format('d.m.Y H:i'),
            'created_at'       => $integrationRequest->created_at->format('d.m.Y H:i'),
        ]);
    }
}
