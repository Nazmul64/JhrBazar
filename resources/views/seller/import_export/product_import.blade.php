@extends('admin.master')
@section('title', 'Product Import')
@section('content')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Bulk Product Imports</h4>
                    <div class="page-title-right">
                        <a href="{{ route('seller.import-export.product-template') }}" class="text-primary fw-medium">Download Template <i class="ri-file-download-line"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <i class="ri-upload-2-line fs-4 text-primary me-2"></i>
                            <h5 class="card-title mb-0">Import Products (CSV)</h5>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success border-0 shadow-sm mb-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('seller.import-export.product-import.submit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Select CSV File</label>
                                <div class="p-4 border border-dashed text-center" style="border-radius: 10px; background-color: #f8fafc;">
                                    <input type="file" name="csv_file" class="form-control mb-2" required>
                                    <p class="text-muted small mb-0">Max file size: 2MB. Format: .csv</p>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="reset" class="btn btn-secondary px-4 me-2 border-0" style="background-color: #64748b;">Reset</button>
                                <button type="submit" class="btn btn-primary px-4 border-0" style="background-color: #4f46e5;">
                                    <i class="ri-upload-cloud-2-line me-1"></i> Start Import
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; background-color: #eff6ff;">
                    <div class="card-body p-4">
                        <h5 class="card-title text-primary mb-3"><i class="ri-lightbulb-line me-1"></i> Instructions</h5>
                        <ul class="text-muted small ps-3">
                            <li class="mb-2">Download the CSV template before starting.</li>
                            <li class="mb-2">Ensure all required columns (Name, SKU, Selling Price) are filled.</li>
                            <li class="mb-2">Don't change the column headers in the template.</li>
                            <li class="mb-2">Buying Price and Selling Price should be numeric.</li>
                            <li>Stock quantity should be a whole number.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
