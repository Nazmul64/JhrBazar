@extends('admin.master')

@section('title', isset($fraudRule) ? 'Edit Rule · ' . $fraudRule->code : 'Create Fraud Rule')

<style>
:root {
    --fc-primary:#0a0e1a; --fc-card:#1a2235; --fc-border:rgba(255,255,255,0.07);
    --fc-accent:#6366f1; --fc-success:#10b981; --fc-warning:#f59e0b;
    --fc-danger:#ef4444; --fc-muted:#64748b; --fc-text:#f1f5f9;
}
body { background: var(--fc-primary); color: var(--fc-text); }

.fc-wrap { max-width: 860px; margin: 2rem auto; padding: 0 1rem; }

.fc-create-title { font-size:1.3rem; font-weight:800; letter-spacing:-.02em; }
.fc-create-title span { color: var(--fc-accent); }
.fc-create-sub { font-size:.78rem; color:var(--fc-muted); margin-top:4px; }

.fc-card { background:var(--fc-card); border:1px solid var(--fc-border); border-radius:12px; overflow:hidden; margin-bottom:1.5rem; }
.fc-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--fc-border);
    font-size:.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:var(--fc-muted);
    display: flex; align-items: center; gap: 8px;
}
.fc-card-body { padding: 1.5rem; }

.fc-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
.fc-form-grid.three { grid-template-columns: 1fr 1fr 1fr; }
.fc-form-full { grid-column: 1 / -1; }

.fc-label {
    display: block;
    font-size: .72rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .08em;
    color: var(--fc-muted); margin-bottom: 6px;
}
.fc-label span { color: var(--fc-danger); }

.fc-control {
    width: 100%;
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--fc-border);
    color: var(--fc-text);
    padding: 10px 14px;
    border-radius: 8px;
    font-size: .82rem;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.fc-control:focus { border-color: var(--fc-accent); box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
.fc-control.is-invalid { border-color: var(--fc-danger); }
.invalid-feedback { color: var(--fc-danger); font-size: .7rem; margin-top: 4px; }

.fc-hint { font-size: .68rem; color: var(--fc-muted); margin-top: 4px; }

.impact-preview {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--fc-card); border: 1px solid var(--fc-border);
    border-radius: 8px; padding: 8px 14px;
    font-size: .82rem; font-weight: 700; margin-top: 8px;
}

.fc-toggle-wrap { display: flex; align-items: center; gap: 10px; }
.fc-toggle {
    position: relative; width: 44px; height: 24px;
    background: rgba(255,255,255,.1);
    border-radius: 12px; transition: background .2s; cursor: pointer;
}
.fc-toggle input { display: none; }
.fc-toggle-knob {
    position: absolute; top: 3px; left: 3px;
    width: 18px; height: 18px;
    background: #fff; border-radius: 50%; transition: transform .2s;
}
.fc-toggle input:checked ~ .fc-toggle-knob { transform: translateX(20px); }
.fc-toggle:has(input:checked) { background: var(--fc-accent); }

.fc-submit {
    width: 100%; padding: 14px;
    background: var(--fc-accent); color: #fff; border: none;
    border-radius: 10px; font-size: .9rem; font-weight: 700;
    cursor: pointer; transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.fc-submit:hover { background: #4f46e5; box-shadow: 0 0 20px rgba(99,102,241,.4); transform: translateY(-1px); }

.btn-fc-ghost {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.05); border: 1px solid var(--fc-border);
    color: var(--fc-text); padding: 8px 14px; border-radius: 8px;
    font-size: .78rem; font-weight: 500;
    text-decoration: none; cursor: pointer; transition: all .2s;
}
.btn-fc-ghost:hover { background: rgba(255,255,255,.1); color: var(--fc-text); }

.condition-preview {
    background: rgba(99,102,241,.08);
    border: 1px solid rgba(99,102,241,.2);
    border-radius: 8px; padding: 10px 14px;
    font-family: monospace; font-size: .82rem; margin-top: 12px; color: var(--fc-muted);
}
.condition-preview .field-val { color: var(--fc-text); font-weight: 700; }
.condition-preview .op-val    { color: var(--fc-accent); }
.condition-preview .cond-val  { color: var(--fc-warning); }
</style>

@section('content')
<div class="fc-wrap">

    {{-- Header --}}
    <div style="margin-bottom:2rem">
        <div class="d-flex align-items-center gap-3 mb-3">
            <a href="{{ route('fraud.rules.index') }}" style="color:var(--fc-muted); text-decoration:none; font-size:.8rem;">
                ← Back to Rules
            </a>
        </div>
        <div class="fc-create-title">
            {{ isset($fraudRule) ? 'Edit' : 'New' }} <span>Fraud</span> Rule
        </div>
        <div class="fc-create-sub">
            {{ isset($fraudRule) ? 'Modify rule ' . $fraudRule->code : 'Define a condition that triggers a fraud score change' }}
        </div>
    </div>

    @if($errors->any())
    <div style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); color:var(--fc-danger); padding:1rem 1.25rem; border-radius:8px; margin-bottom:1.5rem; font-size:.82rem;">
        <strong><i class="fas fa-exclamation-circle me-2"></i>Please fix the following errors:</strong>
        <ul style="margin:.5rem 0 0; padding-left:1.5rem">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ isset($fraudRule) ? route('fraud.rules.update', $fraudRule) : route('fraud.rules.store') }}">
        @csrf
        @if(isset($fraudRule)) @method('PUT') @endif

        {{-- Basic Info --}}
        <div class="fc-card">
            <div class="fc-card-header">
                <i class="fas fa-info-circle" style="color:var(--fc-accent)"></i> Rule Information
            </div>
            <div class="fc-card-body">
                <div class="fc-form-grid">
                    <div class="fc-form-full">
                        <label class="fc-label">Rule Name <span>*</span></label>
                        <input type="text" name="name" class="fc-control @error('name') is-invalid @enderror"
                            placeholder="e.g. Disposable Email Detection"
                            value="{{ old('name', $fraudRule->name ?? '') }}">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="fc-label">Category <span>*</span></label>
                        <select name="category" class="fc-control @error('category') is-invalid @enderror">
                            <option value="">Select category...</option>
                            @foreach($categories as $val => $label)
                            <option value="{{ $val }}" {{ old('category', $fraudRule->category ?? '') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="fc-label">Action <span>*</span></label>
                        <select name="action" class="fc-control @error('action') is-invalid @enderror">
                            <option value="">Select action...</option>
                            @foreach($actions as $val => $label)
                            <option value="{{ $val }}" {{ old('action', $fraudRule->action ?? '') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @error('action')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="fc-form-full">
                        <label class="fc-label">Description</label>
                        <textarea name="description" class="fc-control" rows="2"
                            placeholder="What does this rule detect?">{{ old('description', $fraudRule->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Condition --}}
        <div class="fc-card">
            <div class="fc-card-header">
                <i class="fas fa-filter" style="color:var(--fc-accent)"></i> Condition
            </div>
            <div class="fc-card-body">
                <div class="fc-form-grid three">
                    <div>
                        <label class="fc-label">Field <span>*</span></label>
                        <input type="text" name="condition_field" id="cond-field"
                            class="fc-control @error('condition_field') is-invalid @enderror"
                            placeholder="e.g. email_disposable"
                            value="{{ old('condition_field', $fraudRule->condition_field ?? '') }}"
                            oninput="updatePreview()">
                        <div class="fc-hint">Available: email_disposable, vpn_detected, transaction_amount, phone_type, country...</div>
                        @error('condition_field')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="fc-label">Operator <span>*</span></label>
                        <select name="condition_operator" id="cond-op"
                            class="fc-control @error('condition_operator') is-invalid @enderror"
                            onchange="updatePreview()">
                            <option value="">Select operator...</option>
                            @foreach($operators as $val => $label)
                            <option value="{{ $val }}" {{ old('condition_operator', $fraudRule->condition_operator ?? '') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @error('condition_operator')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="fc-label">Value <span>*</span></label>
                        <input type="text" name="condition_value" id="cond-val"
                            class="fc-control @error('condition_value') is-invalid @enderror"
                            placeholder="e.g. true, 100000, voip"
                            value="{{ old('condition_value', $fraudRule->condition_value ?? '') }}"
                            oninput="updatePreview()">
                        <div class="fc-hint">For "in" operator use comma-separated values</div>
                        @error('condition_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Live Preview --}}
                <div class="condition-preview" id="cond-preview">
                    IF <span class="field-val" id="prev-field">field</span>
                    <span class="op-val" id="prev-op">operator</span>
                    <span class="cond-val" id="prev-val">"value"</span>
                </div>
            </div>
        </div>

        {{-- Score & Priority --}}
        <div class="fc-card">
            <div class="fc-card-header">
                <i class="fas fa-sliders-h" style="color:var(--fc-accent)"></i> Score & Priority
            </div>
            <div class="fc-card-body">
                <div class="fc-form-grid">
                    <div>
                        <label class="fc-label">Score Impact <span>*</span></label>
                        <input type="number" name="score_impact" id="score-impact"
                            class="fc-control @error('score_impact') is-invalid @enderror"
                            placeholder="e.g. 30" min="-100" max="100"
                            value="{{ old('score_impact', $fraudRule->score_impact ?? 0) }}"
                            oninput="updateImpact()">
                        <div class="fc-hint">Positive = increases risk. Negative = decreases risk. Range: -100 to 100</div>
                        @error('score_impact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="impact-preview" id="impact-display">
                            <i class="fas fa-arrow-up" style="color:var(--fc-danger)"></i>
                            <span id="impact-text">+0 risk score</span>
                        </div>
                    </div>

                    <div>
                        <label class="fc-label">Priority (0–100)</label>
                        <input type="number" name="priority"
                            class="fc-control @error('priority') is-invalid @enderror"
                            placeholder="0" min="0" max="100"
                            value="{{ old('priority', $fraudRule->priority ?? 50) }}">
                        <div class="fc-hint">Higher priority rules run first</div>
                        @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="margin-top:1.25rem">
                    <label class="fc-label">Rule Status</label>
                    <div class="fc-toggle-wrap">
                        <label class="fc-toggle">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $fraudRule->is_active ?? true) ? 'checked' : '' }}>
                            <div class="fc-toggle-knob"></div>
                        </label>
                        <span style="font-size:.82rem">Active — rule will be evaluated on every check</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="fc-submit">
            <i class="fas fa-save"></i>
            {{ isset($fraudRule) ? 'Update Rule' : 'Create Rule' }}
        </button>
    </form>
</div>

<script>
function updatePreview() {
    const field = document.getElementById('cond-field').value || 'field';
    const op    = document.getElementById('cond-op').value   || 'operator';
    const val   = document.getElementById('cond-val').value  || 'value';
    document.getElementById('prev-field').textContent = field;
    document.getElementById('prev-op').textContent    = op;
    document.getElementById('prev-val').textContent   = '"' + val + '"';
}

function updateImpact() {
    const val  = parseInt(document.getElementById('score-impact').value) || 0;
    const el   = document.getElementById('impact-text');
    const icon = document.querySelector('#impact-display i');
    el.textContent   = (val > 0 ? '+' : '') + val + ' risk score';
    icon.className   = val > 0 ? 'fas fa-arrow-up' : (val < 0 ? 'fas fa-arrow-down' : 'fas fa-minus');
    icon.style.color = val > 0 ? 'var(--fc-danger)' : (val < 0 ? 'var(--fc-success)' : 'var(--fc-muted)');
}

updatePreview();
updateImpact();
</script>
@endsection
