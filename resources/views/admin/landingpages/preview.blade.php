@extends('admin.master')

@section('content')

@php $statusLabel = $landingpage->status ? 'Active' : 'Inactive'; @endphp

<style>
.preview-page{padding:24px;min-height:100vh;background:#f3f4f6;font-family:'Segoe UI',sans-serif;}
.preview-card{background:#fff;border-radius:14px;box-shadow:0 24px 64px rgba(15,23,42,.08);overflow:hidden;}
.preview-header{padding:26px 28px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
.preview-title{font-size:22px;font-weight:800;color:#111827;margin:0;}
.preview-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.meta-badge{padding:8px 12px;border-radius:999px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;}
.meta-badge.status{background:#eef2ff;color:#3730a3;}
.meta-badge.type{background:#ecfdf5;color:#166534;}
.preview-body{padding:24px 28px;display:grid;grid-template-columns:1fr 340px;gap:24px;}
.preview-section{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;}
.preview-image{width:100%;border-radius:14px;border:1px solid #e5e7eb;object-fit:cover;max-height:420px;}
.preview-text h3{margin:0 0 10px;font-size:18px;color:#111827;}
.preview-text p{line-height:1.75;color:#4b5563;margin:0 0 16px;}
.review-grid{display:grid;gap:16px;}
.review-card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;}
.review-card h4{margin:0 0 10px;font-size:15px;color:#111827;}
.review-card p{margin:0;color:#4b5563;line-height:1.7;}
.review-image{max-width:100%;border-radius:10px;margin-top:12px;border:1px solid #e5e7eb;}
.back-action{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:#fff;border:1px solid #d1d5db;border-radius:999px;color:#1f2937;text-decoration:none;font-weight:700;margin-bottom:18px;}
@media(max-width:1024px){.preview-body{grid-template-columns:1fr;}}
</style>

<div class="preview-page">
    <a href="{{ route('admin.landingpages.index') }}" class="back-action"><i class="bi bi-arrow-left"></i> Back to Landing Pages</a>
    <div class="preview-card">
        <div class="preview-header">
            <div>
                <h1 class="preview-title">{{ $landingpage->title }}</h1>
                <div class="preview-meta">
                    <span class="meta-badge status">{{ $statusLabel }}</span>
                    <span class="meta-badge type">{{ ucfirst($landingpage->media_type) }}</span>
                    @if($landingpage->product)
                        <span class="meta-badge" style="background:#fef3c7;color:#92400e;">Product: {{ Str::limit($landingpage->product->name, 24) }}</span>
                    @endif
                </div>
            </div>
            <div style="text-align:right;min-width:200px;">
                <p style="margin:0;font-size:13px;color:#6b7280;">Created at {{ $landingpage->created_at?->format('d M Y, H:i') }}</p>
                <p style="margin:0;font-size:13px;color:#6b7280;">Last updated {{ $landingpage->updated_at?->diffForHumans() }}</p>
            </div>
        </div>

        <div class="preview-body">
            <div>
                @if($landingpage->image && file_exists(public_path($landingpage->image)))
                    <img src="{{ asset($landingpage->image) }}" alt="Landing Page Image" class="preview-image">
                @else
                    <div class="preview-image" style="display:flex;align-items:center;justify-content:center;color:#9ca3af;background:#f3f4f6;min-height:320px;">No image available</div>
                @endif

                <div class="preview-section" style="margin-top:24px;">
                    <h3>Short Description</h3>
                    <p>{{ $landingpage->short_description ?: 'No short description provided.' }}</p>
                </div>

                <div class="preview-section" style="margin-top:20px;">
                    <h3>Full Description</h3>
                    <p>{!! nl2br(e($landingpage->description ?: 'No description available.')) !!}</p>
                </div>
            </div>

            <div>
                <div class="preview-section">
                    <h3>Landing Page Details</h3>
                    <p><strong>Title:</strong> {{ $landingpage->title }}</p>
                    <p><strong>Status:</strong> {{ $statusLabel }}</p>
                    <p><strong>Media Type:</strong> {{ ucfirst($landingpage->media_type) }}</p>
                    <p><strong>Product:</strong> {{ $landingpage->product->name ?? 'No product selected' }}</p>
                </div>

                <div class="preview-section" style="margin-top:20px;">
                    <h3>Reviews</h3>
                    @if(!empty($landingpage->reviews) && is_array($landingpage->reviews))
                        <div class="review-grid">
                            @foreach($landingpage->reviews as $review)
                                <div class="review-card">
                                    <h4>{{ Str::limit($review['review'] ?? 'Review text missing', 80) }}</h4>
                                    @if(!empty($review['image']) && file_exists(public_path($review['image'])))
                                        <img src="{{ asset($review['image']) }}" alt="Review image" class="review-image">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="color:#6b7280;">No reviews added yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
