<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudBlacklist;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FraudBlacklistController extends Controller
{
    // ─── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = FraudBlacklist::with('creator')->latest();

        if ($request->filled('type')) {
            $query->byType($request->type);
        }
        if ($request->filled('search')) {
            $query->where('value', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('is_active') && $request->is_active !== '') {
            $query->where('is_active', (bool) $request->is_active);
        }

        $blacklists = $query->paginate(20)->withQueryString();
        $stats      = FraudBlacklist::getStats();
        $types      = FraudBlacklist::getTypeOptions();

        return view('admin.fraud.blacklist.index', compact('blacklists', 'stats', 'types'));
    }

    // ─── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'       => 'required|in:email,phone,ip,device,country',
            'value'      => 'required|string|max:500',
            'reason'     => 'required|string|max:500',
            'expires_at' => 'nullable|date|after:now',
        ]);

        FraudBlacklist::updateOrCreate(
            ['type' => $validated['type'], 'value' => $validated['value']],
            [
                'reason'     => $validated['reason'],
                'expires_at' => $validated['expires_at'] ?? null,
                'is_active'  => true,
                'created_by' => auth()->id(),
            ]
        );

        return back()->with('success', 'Entry added to blacklist.');
    }

    // ─── Toggle ────────────────────────────────────────────────────────────────

    public function toggle(FraudBlacklist $fraudBlacklist): RedirectResponse
    {
        $fraudBlacklist->update(['is_active' => ! $fraudBlacklist->is_active]);

        $state = $fraudBlacklist->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Blacklist entry {$state}.");
    }

    // ─── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(FraudBlacklist $fraudBlacklist): RedirectResponse
    {
        $fraudBlacklist->delete();

        return back()->with('success', 'Entry removed from blacklist.');
    }
}
