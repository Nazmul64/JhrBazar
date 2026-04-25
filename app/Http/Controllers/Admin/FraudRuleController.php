<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FraudRule;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FraudRuleController extends Controller
{
    /**
     * Shared view data — always pass categories/operators/actions
     * to avoid "Undefined variable $categories" errors.
     */
    private function viewData(array $extra = []): array
    {
        return array_merge([
            'categories' => FraudRule::getCategoryOptions(),
            'operators'  => FraudRule::getOperatorOptions(),
            'actions'    => FraudRule::getActionOptions(),
        ], $extra);
    }

    // ─── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = FraudRule::with('creator')->orderByDesc('priority');

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }
        if ($request->filled('is_active') && $request->is_active !== '') {
            $query->where('is_active', (bool) $request->is_active);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        $rules = $query->paginate(20)->withQueryString();
        $stats = FraudRule::getStats();

        return view('admin.fraud.rules.index',
            $this->viewData(compact('rules', 'stats'))
        );
    }

    // ─── Create ────────────────────────────────────────────────────────────────

    public function create(): View
    {
        return view('admin.fraud.rules.create', $this->viewData());
    }

    // ─── Store ─────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRule($request);

        $validated['code']       = FraudRule::generateCode();
        $validated['created_by'] = auth()->id();
        $validated['is_active']  = $request->boolean('is_active', true);

        FraudRule::create($validated);

        return redirect()
            ->route('admin.fraud.rules.index')
            ->with('success', 'Rule created successfully.');
    }

    // ─── Edit ──────────────────────────────────────────────────────────────────

    public function edit(FraudRule $fraudRule): View
    {
        return view('admin.fraud.rules.edit',
            $this->viewData(compact('fraudRule'))
        );
    }

    // ─── Update ────────────────────────────────────────────────────────────────

    public function update(Request $request, FraudRule $fraudRule): RedirectResponse
    {
        $validated = $this->validateRule($request);
        $validated['is_active'] = $request->boolean('is_active');

        $fraudRule->update($validated);

        return redirect()
            ->route('admin.fraud.rules.index')
            ->with('success', "Rule «{$fraudRule->code}» updated.");
    }

    // ─── Toggle ────────────────────────────────────────────────────────────────

    public function toggle(FraudRule $fraudRule): RedirectResponse
    {
        $fraudRule->update(['is_active' => ! $fraudRule->is_active]);

        $state = $fraudRule->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Rule {$fraudRule->code} {$state}.");
    }

    // ─── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(FraudRule $fraudRule): RedirectResponse
    {
        $code = $fraudRule->code;
        $fraudRule->delete();

        return redirect()
            ->route('admin.fraud.rules.index')
            ->with('success', "Rule {$code} deleted.");
    }

    // ─── Private: Shared Validation ────────────────────────────────────────────

    private function validateRule(Request $request): array
    {
        return $request->validate([
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string|max:1000',
            'category'           => 'required|in:identity,transaction,device,network,behavioral',
            'condition_field'    => 'required|string|max:100',
            'condition_operator' => 'required|in:equals,not_equals,contains,greater_than,less_than,in,regex,is_true,is_false',
            'condition_value'    => 'required|string|max:500',
            'action'             => 'required|in:flag,review,decline,approve',
            'score_impact'       => 'required|integer|min:-100|max:100',
            'priority'           => 'nullable|integer|min:0|max:100',
        ]);
    }
}
