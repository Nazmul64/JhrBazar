@extends('admin.master')

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0">Category List</h4>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-danger px-4">
        <i class="bi bi-plus-circle-fill me-1"></i> Add Category
    </a>
</div>

{{-- Success / Error Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Table Card --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="px-4 py-3 border-bottom">
            <span class="fw-semibold text-muted">Categories</span>
        </div>

        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:60px">SL</th>
                        <th style="width:200px">Thumbnail</th>
                        <th>Name</th>
                        <th class="text-center" style="width:130px">Status</th>
                        <th class="text-end pe-4" style="width:100px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $i => $category)
                    <tr class="border-bottom">
                        <td class="ps-4">{{ $i + 1 }}</td>
                        <td>
                            @if($category->thumbnail)
                                <img src="{{ asset($category->thumbnail) }}"
                                     alt="{{ $category->name }}"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center text-white"
                                     style="width:60px;height:60px;font-size:10px;">
                                    No Image
                                </div>
                            @endif
                        </td>
                        <td class="fw-medium">{{ $category->name }}</td>
                        <td class="text-center">
                            {{-- Toggle switch --}}
                            <form action="{{ route('admin.categories.toggle', $category->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                <div class="form-check form-switch d-flex justify-content-center m-0">
                                    <input class="form-check-input toggle-status"
                                           type="checkbox"
                                           role="switch"
                                           style="width:44px;height:22px;cursor:pointer;"
                                           {{ $category->is_active ? 'checked' : '' }}
                                           onchange="this.closest('form').submit()">
                                </div>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.categories.edit', $category->id) }}"
                               class="text-danger fs-5" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-grid-3x3-gap fs-1 d-block mb-2 opacity-25"></i>
                            No categories found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Pink/Red toggle switch matching screenshot */
    .form-check-input:checked {
        background-color: #e8174a !important;
        border-color:     #e8174a !important;
    }
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(232,23,74,.25) !important;
    }
</style>
@endpush
