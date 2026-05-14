@extends('admin.master')

@section('content')

<style>
    :root {
        --primary: #e91e8c;
        --primary-hover: #c4166f;
        --border-radius: 8px;
        --input-border: #dee2e6;
        --section-bg: #ffffff;
        --page-bg: #f4f6f9;
        --label-color: #444;
        --section-title: #333;
    }

    .support-page-wrapper {
        padding: 25px;
        background: var(--page-bg);
        min-height: 100vh;
    }

    .support-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 25px;
    }
    .support-header h4 {
        font-size: 20px;
        font-weight: 700;
        color: var(--section-title);
        margin: 0;
    }

    .support-section {
        background: var(--section-bg);
        border-radius: var(--border-radius);
        padding: 30px;
        box-shadow: 0 2px 12px rgba(0,0,0,.05);
        max-width: 800px;
    }

    .support-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--label-color);
        margin-bottom: 8px;
        display: block;
    }

    .support-input {
        width: 100%;
        padding: 12px 15px;
        font-size: 14px;
        border: 1px solid var(--input-border);
        border-radius: var(--border-radius);
        color: #333;
        transition: all .2s;
        outline: none;
        margin-bottom: 20px;
    }
    .support-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(233,30,140,.1);
    }

    .btn-save {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 12px 35px;
        border-radius: var(--border-radius);
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
    }
    .btn-save:hover { background: var(--primary-hover); }

    .support-alert {
        padding: 15px 20px;
        border-radius: var(--border-radius);
        margin-bottom: 20px;
        font-size: 14px;
    }
    .support-alert.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
</style>

<div class="support-page-wrapper">
    <div class="support-header">
        <i class="bi bi-headset" style="font-size: 24px; color: var(--primary);"></i>
        <h4>Admin Support Settings</h4>
    </div>

    @if(session('success'))
        <div class="support-alert success">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="support-section">
        <form action="{{ route('admin.support.update') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-12">
                    <label class="support-label">Messenger URL</label>
                    <input type="text" name="messenger_url" class="support-input" 
                           placeholder="https://m.me/yourpage" 
                           value="{{ old('messenger_url', $data->messenger_url ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="support-label">WhatsApp Number</label>
                    <input type="text" name="whatsapp_number" class="support-input" 
                           placeholder="01700000000" 
                           value="{{ old('whatsapp_number', $data->whatsapp_number ?? '') }}">
                </div>

                <div class="col-md-6">
                    <label class="support-label">Phone Number (Call Us)</label>
                    <input type="text" name="phone_number" class="support-input" 
                           placeholder="01700000000" 
                           value="{{ old('phone_number', $data->phone_number ?? '') }}">
                </div>

                <div class="col-md-12 mt-3">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-save me-2"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
