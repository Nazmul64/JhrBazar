@extends('admin.master')

@section('content')
<div class="container-fluid py-4">

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">

            <h5 class="fw-semibold mb-3" style="font-size: 1.1rem; color: #222;">Contact Us</h5>
            <hr class="mb-4">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.contact.update', $contact->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Phone Number --}}
                <div class="mb-3">
                    <label for="phone_number" class="form-label label-custom">Phone Number</label>
                    <input
                        type="text"
                        id="phone_number"
                        name="phone_number"
                        class="form-control input-custom @error('phone_number') is-invalid @enderror"
                        placeholder="Phone Number"
                        value="{{ old('phone_number', $contact->phone_number) }}"
                    >
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Whatsapp Number --}}
                <div class="mb-3">
                    <label for="whatsapp_number" class="form-label label-custom">Whatsapp Number</label>
                    <input
                        type="text"
                        id="whatsapp_number"
                        name="whatsapp_number"
                        class="form-control input-custom @error('whatsapp_number') is-invalid @enderror"
                        placeholder="Whatsapp Number"
                        value="{{ old('whatsapp_number', $contact->whatsapp_number) }}"
                    >
                    @error('whatsapp_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Messenger Link --}}
                <div class="mb-3">
                    <label for="messenger_link" class="form-label label-custom">Messenger Link</label>
                    <input
                        type="text"
                        id="messenger_link"
                        name="messenger_link"
                        class="form-control input-custom @error('messenger_link') is-invalid @enderror"
                        placeholder="Messenger link"
                        value="{{ old('messenger_link', $contact->messenger_link) }}"
                    >
                    @error('messenger_link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email Address --}}
                <div class="mb-4">
                    <label for="email_address" class="form-label label-custom">Email Address</label>
                    <input
                        type="email"
                        id="email_address"
                        name="email_address"
                        class="form-control input-custom @error('email_address') is-invalid @enderror"
                        placeholder="Email Address"
                        value="{{ old('email_address', $contact->email_address) }}"
                    >
                    @error('email_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Support Image --}}
                <div class="mb-3">
                    <label for="contact_image" class="form-label label-custom">Support Image</label>
                    <input
                        type="file"
                        id="contact_image"
                        name="contact_image"
                        class="form-control input-custom @error('contact_image') is-invalid @enderror"
                        accept="image/*"
                        onchange="previewContactImage(this)"
                    >
                    @error('contact_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="mt-2" id="contact_image_preview_container" style="{{ $contact->contact_image ? '' : 'display:none;' }}">
                        <img id="contact_image_preview" src="{{ $contact->contact_image ? asset($contact->contact_image) : '#' }}" alt="Preview" style="max-height: 150px; border-radius: 6px; border: 1px solid #d9d9d9; padding: 4px;">
                    </div>
                </div>

                {{-- Google Map Embed Code --}}
                <div class="mb-4">
                    <label for="map_embed_code" class="form-label label-custom">Google Map Embed Code (iframe)</label>
                    <textarea
                        id="map_embed_code"
                        name="map_embed_code"
                        rows="4"
                        class="form-control input-custom @error('map_embed_code') is-invalid @enderror"
                        placeholder="Paste Google Map iframe code here"
                    >{{ old('map_embed_code', $contact->map_embed_code) }}</textarea>
                    @error('map_embed_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-pink px-4">
                    Save And Update
                </button>

            </form>

            <script>
                function previewContactImage(input) {
                    const preview = document.getElementById('contact_image_preview');
                    const container = document.getElementById('contact_image_preview_container');
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            container.style.display = 'block';
                        }
                        reader.readAsDataURL(input.files[0]);
                    } else {
                        @if($contact->contact_image)
                            preview.src = "{{ asset($contact->contact_image) }}";
                            container.style.display = 'block';
                        @else
                            preview.src = '#';
                            container.style.display = 'none';
                        @endif
                    }
                }
            </script>

        </div>
    </div>

</div>


<style>
    .label-custom {
        font-size: 0.875rem;
        color: #333;
        font-weight: 500;
        margin-bottom: 6px;
    }
    .input-custom {
        border: 1px solid #d9d9d9;
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 0.9rem;
        color: #555;
        background-color: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .input-custom:focus {
        border-color: #e8215a;
        box-shadow: 0 0 0 3px rgba(232, 33, 90, 0.1);
        outline: none;
    }
    .input-custom::placeholder {
        color: #aaa;
    }
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
