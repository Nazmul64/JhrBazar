@extends('admin.master')
@section('title', 'Withdraw Settings')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Withdraw Settings</h4>
                    <div class="page-title-right">
                        <a href="{{ route('admin.withdraws.commission.index') }}" class="btn btn-info me-2"><i class="ri-settings-3-line me-1"></i> Commission Setup</a>
                        <a href="{{ route('admin.withdraws.index') }}" class="btn btn-secondary"><i class="ri-arrow-left-line me-1"></i> Back to Requests</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Set Withdraw Limits</h4>
                        <form action="{{ route('admin.withdraws.settings.update') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Minimum Withdraw Amount (৳)</label>
                                <input type="number" step="0.01" name="min_withdraw" class="form-control" value="{{ $settings->min_withdraw ?? 100 }}" required>
                                <small class="text-muted">Minimum amount a seller can request.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Maximum Withdraw Amount (৳)</label>
                                <input type="number" step="0.01" name="max_withdraw" class="form-control" value="{{ $settings->max_withdraw ?? 10000 }}" required>
                                <small class="text-muted">Maximum amount a seller can request per transaction.</small>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">Update Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
