@extends('admin.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            {{-- Success / Error Alert --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h4 class="mb-0">Mail Configuration</h4>
                </div>

                <div class="card-body">

                    {{-- ── Send Test Mail Section ── --}}
                    <div class="mb-5">
                        <div class="bg-light p-3 rounded-top border-bottom d-flex justify-content-between align-items-center"
                             style="cursor:pointer;" id="testMailToggle">
                            <h5 class="mb-0"><i class="bi bi-send me-2"></i>Send Test Mail</h5>
                            <i class="bi bi-chevron-up" id="testMailIcon"></i>
                        </div>

                        <div class="border border-top-0 p-4 bg-white rounded-bottom" id="testMailBody">
                            <form action="{{ route('admin.mailconfiguration.send-test') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            To / Recipient Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email"
                                               name="recipient_email"
                                               class="form-control @error('recipient_email') is-invalid @enderror"
                                               placeholder="Recipient's Email"
                                               value="{{ old('recipient_email') }}"
                                               required>
                                        @error('recipient_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-semibold">
                                            Message <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="message"
                                                  class="form-control @error('message') is-invalid @enderror"
                                                  rows="3"
                                                  placeholder="Message to be sent"
                                                  required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-danger px-4">
                                        <i class="bi bi-send me-1"></i> Send Email
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- ── Mail Configuration Form ── --}}
                    <form action="{{ route('admin.mailconfiguration.update') }}" method="POST">
                        @csrf

                        <h5 class="mb-4"><i class="bi bi-gear me-2"></i>Mail Configuration</h5>

                        <div class="row g-4">

                            {{-- Left Column --}}
                            <div class="col-lg-6">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mail Mailer</label>
                                    <input type="text"
                                           name="mail_mailer"
                                           class="form-control @error('mail_mailer') is-invalid @enderror"
                                           value="{{ old('mail_mailer', $config->mail_mailer ?? 'smtp') }}">
                                    @error('mail_mailer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mail Port</label>
                                    <input type="number"
                                           name="mail_port"
                                           class="form-control @error('mail_port') is-invalid @enderror"
                                           value="{{ old('mail_port', $config->mail_port ?? 587) }}">
                                    @error('mail_port')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mail Password</label>
                                    <div class="input-group">
                                        <input type="password"
                                               name="mail_password"
                                               id="mailPassword"
                                               class="form-control @error('mail_password') is-invalid @enderror"
                                               placeholder="Leave blank to keep current password">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="bi bi-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                    @error('mail_password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Mail From Address <span class="text-danger">*</span>
                                    </label>
                                    <input type="email"
                                           name="mail_from_address"
                                           class="form-control @error('mail_from_address') is-invalid @enderror"
                                           value="{{ old('mail_from_address', $config->mail_from_address ?? '') }}"
                                           required>
                                    @error('mail_from_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Right Column --}}
                            <div class="col-lg-6">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mail Host</label>
                                    <input type="text"
                                           name="mail_host"
                                           class="form-control @error('mail_host') is-invalid @enderror"
                                           value="{{ old('mail_host', $config->mail_host ?? 'smtp.gmail.com') }}">
                                    @error('mail_host')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mail User Name</label>
                                    <input type="text"
                                           name="mail_username"
                                           class="form-control @error('mail_username') is-invalid @enderror"
                                           value="{{ old('mail_username', $config->mail_username ?? '') }}">
                                    @error('mail_username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mail Encryption</label>
                                    <select name="mail_encryption"
                                            class="form-select @error('mail_encryption') is-invalid @enderror">
                                        <option value="ssl"      {{ old('mail_encryption', $config->mail_encryption ?? 'ssl') === 'ssl'      ? 'selected' : '' }}>SSL</option>
                                        <option value="tls"      {{ old('mail_encryption', $config->mail_encryption ?? 'ssl') === 'tls'      ? 'selected' : '' }}>TLS</option>
                                        <option value="starttls" {{ old('mail_encryption', $config->mail_encryption ?? 'ssl') === 'starttls' ? 'selected' : '' }}>STARTTLS</option>
                                        <option value="none"     {{ old('mail_encryption', $config->mail_encryption ?? 'ssl') === 'none'     ? 'selected' : '' }}>None</option>
                                    </select>
                                    @error('mail_encryption')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mail From Name</label>
                                    <input type="text"
                                           name="mail_from_name"
                                           class="form-control @error('mail_from_name') is-invalid @enderror"
                                           value="{{ old('mail_from_name', $config->mail_from_name ?? '') }}">
                                    @error('mail_from_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-danger px-5">
                                <i class="bi bi-save me-1"></i> Save And Update
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // Password show/hide toggle
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input   = document.getElementById('mailPassword');
        const eyeIcon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });

    // Send Test Mail collapse toggle
    document.getElementById('testMailToggle').addEventListener('click', function () {
        const body = document.getElementById('testMailBody');
        const icon = document.getElementById('testMailIcon');
        const isHidden = body.style.display === 'none';
        body.style.display = isHidden ? 'block' : 'none';
        icon.classList.toggle('bi-chevron-up',   isHidden);
        icon.classList.toggle('bi-chevron-down', !isHidden);
    });
</script>
@endpush

@endsection
