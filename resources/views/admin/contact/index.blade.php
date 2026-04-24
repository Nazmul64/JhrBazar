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

                <a href="{{ route('admin.contact.edit', $contact->id) }}"
                   class="btn btn-pink px-4">
                    Save And Update
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
