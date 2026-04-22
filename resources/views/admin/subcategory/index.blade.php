@extends('admin.master')

@section('content')

{{-- Page Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="fw-bold mb-0">Sub Categories</h4>
    <a href="{{ route('admin.subcategory.create') }}" class="btn btn-danger px-4">
        <i class="bi bi-plus-circle-fill me-1"></i> Add SubCategory
    </a>
</div>

{{-- Flash Message --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Table Card --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">

        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-semibold text-muted">
                All Sub Categories
                <span class="badge bg-secondary ms-1">{{ $subCategories->count() }}</span>
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width:60px;">SL</th>
                        <th style="width:100px;">Thumbnail</th>
                        <th>Category</th>
                        <th>Name</th>
                        <th class="text-center" style="width:110px;">Status</th>
                        <th class="text-end pe-4" style="width:90px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subCategories as $i => $sub)
                    <tr class="border-bottom">

                        {{-- SL --}}
                        <td class="ps-4 text-muted" style="font-size:13px;">{{ $i + 1 }}</td>

                        {{-- Thumbnail --}}
                        <td>
                            @if($sub->thumbnail)
                                <img src="{{ asset($sub->thumbnail) }}"
                                     alt="{{ $sub->name }}"
                                     style="width:52px;height:52px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                            @else
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                                     style="width:52px;height:52px;border-radius:8px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                        </td>

                        {{-- Categories (badges) --}}
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($sub->categories as $cat)
                                    <span class="badge px-2 py-1"
                                          style="background:#dbeafe;color:#1d4ed8;font-size:11.5px;font-weight:500;border-radius:4px;">
                                        {{ $cat->name }}
                                    </span>
                                @empty
                                    <span class="text-muted" style="font-size:12px;">—</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- Name --}}
                        <td class="fw-medium" style="font-size:14px;">{{ $sub->name }}</td>

                        {{-- Status Toggle --}}
                        <td class="text-center">
                            <form action="{{ route('admin.subcategory.toggle', $sub->id) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                <div class="form-check form-switch d-flex justify-content-center m-0">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           role="switch"
                                           style="width:42px;height:22px;cursor:pointer;"
                                           {{ $sub->is_active ? 'checked' : '' }}
                                           onchange="this.closest('form').submit()">
                                </div>
                            </form>
                        </td>

                        {{-- Actions --}}
                        <td class="text-end pe-4">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                {{-- Edit --}}
                                <a href="{{ route('admin.subcategory.edit', $sub->id) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   style="width:32px;height:32px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:7px;"
                                   title="Edit">
                                    <i class="bi bi-pencil-square" style="font-size:14px;"></i>
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('admin.subcategory.destroy', $sub->id) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete {{ addslashes($sub->name) }}? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            style="width:32px;height:32px;padding:0;display:flex;align-items:center;justify-content:center;border-radius:7px;"
                                            title="Delete">
                                        <i class="bi bi-trash3" style="font-size:14px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-grid-3x3-gap fs-1 d-block mb-2 opacity-25"></i>
                            No sub categories found.
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
    .form-check-input:checked {
        background-color : #e8174a !important;
        border-color     : #e8174a !important;
    }
    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(232,23,74,.2) !important;
    }
</style>
@endpush
