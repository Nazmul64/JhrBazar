@extends('admin.master')
@section('content')

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold mb-0">Unit List</h4>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-muted">Units</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase text-muted small fw-bold" style="width: 80px;">SL</th>
                                    <th class="py-3 text-uppercase text-muted small fw-bold">Name</th>
                                    <th class="px-4 py-3 text-uppercase text-muted small fw-bold text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($units as $key => $unit)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3">{{ $key + 1 }}</td>
                                    <td class="py-3 fw-bold text-dark">{{ $unit->name }}</td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" role="switch" {{ $unit->is_active ? 'checked' : '' }} disabled style="cursor: not-allowed; background-color: {{ $unit->is_active ? '#f43f5e' : '' }}; border-color: {{ $unit->is_active ? '#f43f5e' : '' }}; opacity: 0.8;">
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">No units found</td>
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
