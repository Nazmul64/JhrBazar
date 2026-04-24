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

            <form action="{{ route('admin.contact.store') }}" method="POST">
                @csrf

                {{-- Phone Number --}}
                <div class="mb-3">
                    <label for="phone_number" class="form-label label-custom">Phone Number</label>
                    <input
                        type="text"
                        id="phone_number"
                        name="phone_number"
                        class="form-control input-custom @error('phone_number') is-invalid @enderror"
                        placeholder="Phone Number"
                        value="{{ old('phone_number') }}"
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
                        value="{{ old('whatsapp_number') }}"
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
                        value="{{ old('messenger_link') }}"
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
                        value="{{ old('email_address') }}"
                    >
                    @error('email_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-pink px-4">
                    Save And Update
                </button>

            </form>

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
