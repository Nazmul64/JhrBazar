@extends('admin.master')
@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold mb-0">Sub Category List</h4>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-muted">Sub Categories</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase text-muted small fw-bold" style="width: 80px;">SL</th>
                                    <th class="py-3 text-uppercase text-muted small fw-bold">Thumbnail</th>
                                    <th class="py-3 text-uppercase text-muted small fw-bold">Name</th>
                                    <th class="px-4 py-3 text-uppercase text-muted small fw-bold text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subcategories as $key => $sub)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">{{ $key + 1 }}</td>
                                    <td class="py-3">
                                        <div class="bg-light rounded p-1" style="width: 50px; height: 50px;">
                                            @if($sub->thumbnail)
                                                <img src="{{ asset($sub->thumbnail) }}" alt="" class="img-fluid rounded" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 fw-bold text-dark">{{ $sub->name }}</td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" role="switch" {{ $sub->is_active ? 'checked' : '' }} disabled style="cursor: not-allowed; background-color: {{ $sub->is_active ? '#f43f5e' : '' }}; border-color: {{ $sub->is_active ? '#f43f5e' : '' }}; opacity: 0.8;">
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">No subcategories found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
