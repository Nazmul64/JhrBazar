<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudAlert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FraudAlertController extends Controller
{
    // ─── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = FraudAlert::with(['fraudCheck', 'assignee'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('severity')) {
            $query->bySeverity($request->severity);
        }

        $alerts    = $query->paginate(20)->withQueryString();
        $stats     = FraudAlert::getStats();
        $statuses  = FraudAlert::getStatusOptions();
        $severities = FraudAlert::getSeverityOptions();

        return view('admin.fraud.alerts.index', compact('alerts', 'stats', 'statuses', 'severities'));
    }

    // ─── Show ──────────────────────────────────────────────────────────────────

    public function show(FraudAlert $fraudAlert): View
    {
        $fraudAlert->load(['fraudCheck', 'assignee']);
        $users     = User::orderBy('name')->pluck('name', 'id');
        $statuses  = FraudAlert::getStatusOptions();

        return view('admin.fraud.alerts.show', compact('fraudAlert', 'users', 'statuses'));
    }

    // ─── Resolve ───────────────────────────────────────────────────────────────

    public function resolve(Request $request, FraudAlert $fraudAlert): RedirectResponse
    {
        $validated = $request->validate([
            'status'          => 'required|in:resolved,false_positive,investigating',
            'resolution_note' => 'nullable|string|max:2000',
        ]);

        $fraudAlert->update([
            'status'          => $validated['status'],
            'resolution_note' => $validated['resolution_note'] ?? null,
            'resolved_at'     => in_array($validated['status'], ['resolved', 'false_positive']) ? now() : null,
            'assigned_to'     => auth()->id(),
        ]);

        return back()->with('success', 'Alert updated successfully.');
    }

    // ─── Assign ────────────────────────────────────────────────────────────────

    public function assign(Request $request, FraudAlert $fraudAlert): RedirectResponse
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $fraudAlert->update(['assigned_to' => $request->user_id]);

        return back()->with('success', 'Alert assigned.');
    }
}
