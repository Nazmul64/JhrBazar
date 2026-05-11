@extends('admin.master')

@section('content')

<style>
    .ml-page-wrapper { padding: 20px; background: #f4f6f9; min-height: 100vh; }
    .ml-section { background: #fff; border-radius: 8px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,.07); margin-bottom: 20px; }
    .ml-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px; }
    .ml-header h4 { font-size: 18px; font-weight: 700; color: #333; margin: 0; }
    .gs-label { font-size: 13px; font-weight: 500; color: #444; margin-bottom: 6px; display: block; }
    .gs-input { width: 100%; padding: 9px 13px; font-size: 13px; border: 1px solid #dee2e6; border-radius: 6px; outline: none; }
    .btn-save { background: #e91e8c; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background .2s; }
    .btn-save:hover { background: #c4166f; }
    .logo-card { border: 1px solid #eee; border-radius: 8px; padding: 15px; position: relative; text-align: center; background: #fff; transition: transform 0.2s; }
    .logo-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .logo-img { max-width: 100%; height: 60px; object-fit: contain; margin-bottom: 10px; }
    .btn-delete { position: absolute; top: 10px; right: 10px; color: #ff4d4d; border: none; background: none; font-size: 16px; cursor: pointer; }
</style>

<div class="ml-page-wrapper">
    <div class="ml-header">
        <h4>Membership Logos (e-CAB, BASIS, DBID etc.)</h4>
        <a href="{{ route('admin.generalsettings.index') }}" class="btn-save" style="background:#6c757d;">Back to Settings</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        {{-- Add New Logo --}}
        <div class="col-md-4">
            <div class="ml-section">
                <h6 class="mb-4">Upload Logos</h6>
                <form action="{{ route('admin.membership_logos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="gs-label">Common Name (Optional)</label>
                        <input type="text" name="name" class="gs-input" placeholder="e.g. Partners">
                    </div>
                    <div class="mb-3">
                        <label class="gs-label">Select Logo Images</label>
                        <input type="file" name="images[]" class="form-control" required accept="image/*" multiple>
                        <small class="text-muted">You can select multiple images at once.</small>
                    </div>
                    <button type="submit" class="btn-save w-100">Upload All Logos</button>
                </form>
            </div>
        </div>

        {{-- Logo List --}}
        <div class="col-md-8">
            <div class="ml-section">
                <h6 class="mb-4">All Active Logos</h6>
                <div class="row g-3">
                    @forelse($logos as $logo)
                        <div class="col-md-4 col-sm-6">
                            <div class="logo-card">
                                <form action="{{ route('admin.membership_logos.destroy', $logo->id) }}" method="POST" onsubmit="return confirm('Delete this logo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete"><i class="fas fa-trash"></i></button>
                                </form>
                                <img src="{{ asset($logo->image) }}" class="logo-img" alt="{{ $logo->name }}">
                                <div class="small fw-bold text-muted">{{ $logo->name ?: 'Logo' }}</div>
                                <div class="mt-2">
                                    <form action="{{ route('admin.membership_logos.toggle', $logo->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $logo->is_active ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $logo->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted">
                            No logos found. Upload your first membership logo.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
