@extends('admin.master')
@section('title', 'Commission Setup')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Commission & Withdraw Setup</h4>
                    <div class="page-title-right">
                        <a href="{{ route('admin.withdraws.settings') }}" class="btn btn-secondary"><i class="ri-arrow-left-line me-1"></i> Withdraw Limits</a>
                        <a href="{{ route('admin.withdraws.index') }}" class="btn btn-light ms-2">Back to Withdraw Requests</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Configure Seller Withdraw Rules</h4>
                        <form action="{{ route('admin.withdraws.commission.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Seller Withdraw Commission (%)</label>
                                <input type="number" step="0.01" name="withdraw_commission_percent" class="form-control" value="{{ old('withdraw_commission_percent', $commission->withdraw_commission_percent ?? $settings->withdraw_commission ?? 0) }}">
                                <small class="text-muted">Percentage charged on each withdraw request.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Minimum Withdraw Amount (৳)</label>
                                    <input type="number" step="0.01" name="min_withdraw_amount" class="form-control" value="{{ old('min_withdraw_amount', $commission->min_withdraw_amount ?? $settings->min_withdraw ?? 0) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Maximum Withdraw Amount (৳)</label>
                                    <input type="number" step="0.01" name="max_withdraw_amount" class="form-control" value="{{ old('max_withdraw_amount', $commission->max_withdraw_amount ?? $settings->max_withdraw ?? 0) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Withdraw Charge (Flat ৳)</label>
                                <input type="number" step="0.01" name="withdraw_charge" class="form-control" value="{{ old('withdraw_charge', $commission->withdraw_charge ?? 0) }}">
                                <small class="text-muted">Optional flat fee deducted from each withdraw payment.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Daily Withdraw Limit (৳)</label>
                                <input type="number" step="0.01" name="daily_limit" class="form-control" value="{{ old('daily_limit', $commission->daily_limit ?? '') }}">
                                <small class="text-muted">Optional limit for seller withdraws per day.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Seller Withdraw Rules</label>
                                <textarea name="seller_withdraw_rules" class="form-control" rows="4" placeholder="One rule per line">{{ old('seller_withdraw_rules', is_array($commission->seller_withdraw_rules ?? null) ? implode("\n", $commission->seller_withdraw_rules) : '') }}</textarea>
                                <small class="text-muted">Enter one rule per line: e.g. "KYC verification required", "Withdraws processed within 48 hours".</small>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="verification_required" value="1" id="verificationRequired" {{ old('verification_required', $commission->verification_required ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="verificationRequired">Verification Required for Withdraws</label>
                            </div>

                            <button type="submit" class="btn btn-primary">Save Commission Setup</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Current Commission Details</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Withdraw Commission: <strong>{{ $commission->withdraw_commission_percent ?? $settings->withdraw_commission ?? 0 }}%</strong></li>
                            <li class="list-group-item">Min Withdraw: <strong>৳{{ number_format($commission->min_withdraw_amount ?? $settings->min_withdraw ?? 0, 2) }}</strong></li>
                            <li class="list-group-item">Max Withdraw: <strong>৳{{ number_format($commission->max_withdraw_amount ?? $settings->max_withdraw ?? 0, 2) }}</strong></li>
                            <li class="list-group-item">Withdraw Charge: <strong>৳{{ number_format($commission->withdraw_charge ?? 0, 2) }}</strong></li>
                            <li class="list-group-item">Daily Limit: <strong>{{ optional($commission)->daily_limit ? '৳' . number_format(optional($commission)->daily_limit, 2) : 'None' }}</strong></li>
                            <li class="list-group-item">Verification Required: <strong>{{ (optional($commission)->verification_required ?? false) ? 'Yes' : 'No' }}</strong></li>
                        </ul>

                        @if(!empty(optional($commission)->seller_withdraw_rules))
                        <div class="mt-4">
                            <h6 class="mb-2">Withdraw Rules</h6>
                            <ul class="ps-3">
                                @foreach(optional($commission)->seller_withdraw_rules as $rule)
                                    <li>{{ $rule }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
