@extends('admin.master')
@section('title', 'Product Export')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Bulk Product Exports</h4>
                    <div class="page-title-right">
                        <a href="#" class="text-primary fw-medium">Get Instructions <i class="ri-information-line"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <i class="ri-download-2-line fs-4 text-primary me-2"></i>
                            <h5 class="card-title mb-0">Export Products</h5>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger border-0 shadow-sm mb-4">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('seller.import-export.product-export.submit') }}" method="POST">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted small fw-bold">Type</label>
                                        <select name="export_type" class="form-select border-light shadow-none" style="background-color: #f8fafc;">
                                            <option value="all">All Products</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 text-end">
                                        <button type="reset" class="btn btn-secondary px-4 me-2 border-0" style="background-color: #64748b;">Reset</button>
                                        <button type="submit" class="btn btn-danger px-4 border-0" style="background-color: #f43f5e;">
                                            <i class="ri-download-cloud-line me-1"></i> Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
