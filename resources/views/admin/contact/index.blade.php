@extends('admin.master')

@section('content')
<div class="container-fluid py-4">

    {{-- Success / Error Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">

            <h5 class="fw-semibold mb-3" style="font-size: 1.1rem; color: #222;">Contact Us</h5>
            <hr class="mb-4">

            @if($contact)
                {{-- Show current contact info with edit button --}}
                <div class="mb-3">
                    <label class="form-label text-muted small">Phone Number</label>
                    <p class="mb-0 fw-medium">{{ $contact->phone_number ?? '—' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Whatsapp Number</label>
                    <p class="mb-0 fw-medium">{{ $contact->whatsapp_number ?? '—' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Messenger Link</label>
                    <p class="mb-0 fw-medium">{{ $contact->messenger_link ?? '—' }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small">Email Address</label>
                    <p class="mb-0 fw-medium">{{ $contact->email_address ?? '—' }}</p>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small">Support Image</label>
                    @if($contact->contact_image)
                        <div class="mt-1">
                            <img src="{{ asset($contact->contact_image) }}" alt="Support Image" style="max-height: 120px; border-radius: 6px; border: 1px solid #eee; padding: 4px;">
                        </div>
                    @else
                        <p class="mb-0 fw-medium text-muted">No support image uploaded</p>
                    @endif
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small">Google Map</label>
                    @if($contact->map_embed_code)
                        <div class="mt-1 rounded border overflow-hidden" style="max-width: 500px; height: 200px;">
                            {!! preg_replace('/width="[0-9]+"/', 'width="100%"', preg_replace('/height="[0-9]+"/', 'height="100%"', $contact->map_embed_code)) !!}
                        </div>
                    @else
                        <p class="mb-0 fw-medium text-muted">No map embed code configured</p>
                    @endif
                </div>

                <a href="{{ route('admin.contact.edit', $contact->id) }}"
                   class="btn btn-pink px-4">
                    Edit Contact Info
                </a>
            @else
                {{-- No record yet — show create link --}}
                <p class="text-muted">No contact information found.</p>
                <a href="{{ route('admin.contact.create') }}" class="btn btn-pink px-4">
                    Add Contact Info
                </a>
            @endif

        </div>
    </div>

</div>

<style>
    .btn-pink {
        background-color: #e8215a;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        padding: 10px 24px;
        transition: background 0.2s;
    }
    .btn-pink:hover {
        background-color: #c81a4c;
        color: #fff;
    }
</style>
@endsection
