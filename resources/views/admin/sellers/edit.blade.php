@extends('admin.master')
@section('content')

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-pencil-square me-2"></i> Edit Seller: {{ $seller->name }}</h5>
                    <a href="{{ route('admin.sellers.approvals') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.sellers.update', $seller->id) }}" method="POST">
                        @csrf
                        
                        {{-- Basic Information --}}
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">1. Basic Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $seller->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $seller->last_name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $seller->email) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $seller->phone) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">New Password (Leave blank to keep current)</label>
                                    <input type="password" name="password" class="form-control" placeholder="Enter new password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>

                        {{-- Business Information --}}
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">2. Business Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Business Type</label>
                                    <select name="business_type" class="form-select">
                                        <option value="Individual" {{ (old('business_type', $seller->shop->business_type ?? '') == 'Individual') ? 'selected' : '' }}>Individual</option>
                                        <option value="Business" {{ (old('business_type', $seller->shop->business_type ?? '') == 'Business') ? 'selected' : '' }}>Business</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Business Name</label>
                                    <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $seller->shop->business_name ?? '') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Business Address</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address', $seller->shop->address ?? '') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">City</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $seller->shop->city ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $seller->shop->postal_code ?? '') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Store Information --}}
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">3. Store Information</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Store Name</label>
                                    <input type="text" name="shop_name" class="form-control" value="{{ old('shop_name', $seller->shop->name ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Store URL</label>
                                    <input type="text" name="store_url" class="form-control" value="{{ old('store_url', $seller->shop->url ?? '') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Store Description</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description', $seller->shop->description ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Bank Details --}}
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">4. Documents & Bank Details</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Bank Name</label>
                                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $seller->bank_name) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Branch Name</label>
                                    <input type="text" name="bank_branch" class="form-control" value="{{ old('bank_branch', $seller->bank_branch) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Account Number</label>
                                    <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $seller->bank_account_number) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Account Holder Name</label>
                                    <input type="text" name="bank_account_holder" class="form-control" value="{{ old('bank_account_holder', $seller->bank_account_holder) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-danger px-5 py-2 fw-bold">
                                <i class="bi bi-save me-2"></i> Update Seller Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
