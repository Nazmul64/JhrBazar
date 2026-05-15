@extends('admin.master')

@section('title', 'Manage Fraud APIs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock-fill text-primary"></i> 
                        Manage Fraud APIs
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.fraud.apis.update') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th style="width: 150px;">Type</th>
                                        <th>API URL</th>
                                        <th>API Key</th>
                                        <th class="text-center" style="width: 100px;">Active</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Free API Row --}}
                                    <tr>
                                        <td class="text-center fw-bold">1</td>
                                        <td>
                                            <span class="badge bg-info px-3 py-2 text-white">free</span>
                                        </td>
                                        <td>
                                            <input type="text" name="apis[free][api_url]" class="form-control" value="{{ $freeApi->api_url }}" placeholder="Enter API URL">
                                        </td>
                                        <td>
                                            <input type="text" name="apis[free][api_key]" class="form-control" value="{{ $freeApi->api_key }}" placeholder="Enter API Key (Optional)">
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="radio" name="active_type" value="free" {{ $freeApi->is_active ? 'checked' : '' }} style="width: 20px; height: 20px; cursor: pointer;">
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Paid API Row --}}
                                    <tr>
                                        <td class="text-center fw-bold">2</td>
                                        <td>
                                            <span class="badge bg-primary px-3 py-2 text-white">Paid</span>
                                        </td>
                                        <td>
                                            <input type="text" name="apis[paid][api_url]" class="form-control" value="{{ $paidApi->api_url }}" placeholder="Enter API URL">
                                        </td>
                                        <td>
                                            <input type="text" name="apis[paid][api_key]" class="form-control" value="{{ $paidApi->api_key }}" placeholder="Enter API Key">
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="radio" name="active_type" value="paid" {{ $paidApi->is_active ? 'checked' : '' }} style="width: 20px; height: 20px; cursor: pointer;">
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success px-4 py-2 d-flex align-items-center gap-2" style="border-radius: 8px;">
                                <i class="bi bi-save-fill"></i> Save Changes
                            </button>
                        </div>
                    </form>

                    <div class="mt-5 d-flex justify-content-center gap-3">
                        <a href="https://bdcourier.com" target="_blank" class="btn btn-primary px-4 py-2 d-flex align-items-center gap-2" style="border-radius: 25px;">
                            <i class="bi bi-globe"></i> Buy API Key
                        </a>
                        <a href="#" class="btn btn-dark px-4 py-2 d-flex align-items-center gap-2" style="border-radius: 25px; background: #6b21a8; border: none;">
                            <i class="bi bi-headset"></i> Developer Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th {
        background-color: #f1f5f9;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border-top: none;
    }
    .form-control {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 1rem;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .badge {
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: 6px;
    }
</style>
@endsection
