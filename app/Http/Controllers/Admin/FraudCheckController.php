<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFraudCheckRequest;
use App\Models\FraudCheck;
use App\Services\FraudDetectionService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FraudCheckController extends Controller
{
    public function __construct(private readonly FraudDetectionService $service) {}

    // ─── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = FraudCheck::with(['creator', 'reviewer'])->latest();

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }
        if ($request->filled('risk_level')) {
            $query->byRiskLevel($request->risk_level);
        }
        if ($request->filled('type')) {
            $query->byType($request->type);
        }
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $checks = $query->paginate(15)->withQueryString();
        $stats  = FraudCheck::getStats();

        return view('admin.fraud.index', compact('checks', 'stats'));
    }

    // ─── Create ────────────────────────────────────────────────────────────────

    public function create(): View
    {
        $types = FraudCheck::getTypeOptions();

        return view('admin.fraud.create', compact('types'));
    }

    // ─── Store ─────────────────────────────────────────────────────────────────

    public function store(StoreFraudCheckRequest $request): RedirectResponse
    {
        $check = $this->service->analyze($request->validated());

        return redirect()
            ->route('admin.fraud.show', $check)
            ->with('success', "Check complete — {$check->check_id} | Risk: {$check->risk_score}/100");
    }

    // ─── Show ──────────────────────────────────────────────────────────────────

    public function show(FraudCheck $fraudCheck): View
    {
        $fraudCheck->load(['alerts', 'creator', 'reviewer']);

        return view('admin.fraud.show', compact('fraudCheck'));
    }

    // ─── Edit ──────────────────────────────────────────────────────────────────

    public function edit(FraudCheck $fraudCheck): View
    {
        $types = FraudCheck::getTypeOptions();

        return view('admin.fraud.edit', compact('fraudCheck', 'types'));
    }

    // ─── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, FraudCheck $fraudCheck): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,review,declined',
            'notes'  => 'nullable|string|max:2000',
        ]);

        $fraudCheck->update([
            'status'      => $validated['status'],
            'notes'       => $validated['notes'] ?? $fraudCheck->notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route('admin.fraud.show', $fraudCheck)
            ->with('success', "Decision updated to «{$fraudCheck->status}» successfully.");
    }

    // ─── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(FraudCheck $fraudCheck): RedirectResponse
    {
        $id = $fraudCheck->check_id;
        $fraudCheck->delete();

        return redirect()
            ->route('admin.fraud.index')
            ->with('success', "Fraud check {$id} deleted.");
    }

    // ─── Bulk Actions ──────────────────────────────────────────────────────────

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:approve,decline,delete',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:fraud_checks,id',
        ]);

        $checks = FraudCheck::whereIn('id', $request->ids);

        match ($request->action) {
            'approve' => $checks->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]),
            'decline' => $checks->update([
                'status'      => 'declined',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]),
            'delete'  => $checks->delete(),
        };

        return back()->with('success', 'Bulk action applied to ' . count($request->ids) . ' records.');
    }

    // ─── Export CSV ────────────────────────────────────────────────────────────

    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $checks = FraudCheck::query()
            ->when($request->status,     fn($q) => $q->byStatus($request->status))
            ->when($request->risk_level, fn($q) => $q->byRiskLevel($request->risk_level))
            ->get();

        $filename = 'fraud-checks-' . date('Y-m-d') . '.csv';

        return response()->stream(function () use ($checks) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Check ID', 'Type', 'Status', 'Risk Level', 'Risk Score',
                'Customer Name', 'Email', 'Phone', 'IP', 'Country',
                'VPN', 'Proxy', 'Tor', 'Flags', 'Created At',
            ]);

            foreach ($checks as $c) {
                fputcsv($handle, [
                    $c->check_id,
                    $c->type,
                    $c->status,
                    $c->risk_level,
                    $c->risk_score,
                    $c->customer_name,
                    $c->customer_email,
                    $c->customer_phone,
                    $c->ip_address,
                    $c->country,
                    $c->vpn_detected   ? 'Yes' : 'No',
                    $c->proxy_detected ? 'Yes' : 'No',
                    $c->tor_detected   ? 'Yes' : 'No',
                    implode(' | ', $c->flags ?? []),
                    $c->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
