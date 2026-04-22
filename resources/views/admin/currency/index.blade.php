{{-- resources/views/admin/currency/index.blade.php --}}
@extends('admin.master')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap');

    .currency-wrap * { font-family: 'DM Sans', sans-serif; }

    .page-fade-in {
        animation: pageFadeIn 0.4s ease both;
    }
    @keyframes pageFadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .currency-table tbody tr {
        transition: background 0.15s ease, box-shadow 0.15s ease;
    }
    .currency-table tbody tr:hover {
        background: #fdf2f7;
    }

    .badge-default {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #fff0f5;
        color: #e91e63;
        border: 1px solid #fce4ec;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
        letter-spacing: 0.3px;
    }
    .badge-usd {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f3f4f6;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        font-size: 11px;
        font-weight: 500;
        padding: 2px 8px;
        border-radius: 20px;
    }

    .rate-value {
        font-weight: 600;
        color: #111827;
        font-variant-numeric: tabular-nums;
    }

    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #e91e63, #c2185b);
        color: #fff;
        font-weight: 600;
        font-size: 13.5px;
        padding: 9px 18px;
        border-radius: 10px;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(233,30,99,0.35);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .btn-add:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(233,30,99,0.45);
    }
    .btn-add:active {
        transform: translateY(0);
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: background 0.15s, transform 0.12s;
        text-decoration: none;
    }
    .action-btn:hover { transform: scale(1.08); }
    .action-edit  { background: #eff6ff; color: #3b82f6; }
    .action-edit:hover { background: #dbeafe; }
    .action-delete { background: #fff1f2; color: #f43f5e; }
    .action-delete:hover { background: #ffe4e6; }

    .flash-success {
        background: linear-gradient(90deg, #ecfdf5, #d1fae5);
        border-left: 4px solid #10b981;
        color: #065f46;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 13.5px;
        font-weight: 500;
        animation: pageFadeIn 0.3s ease;
    }
    .flash-error {
        background: linear-gradient(90deg, #fff1f2, #ffe4e6);
        border-left: 4px solid #f43f5e;
        color: #9f1239;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 13.5px;
        font-weight: 500;
        animation: pageFadeIn 0.3s ease;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }
    .empty-state svg {
        margin: 0 auto 12px;
        opacity: 0.35;
    }
    .empty-state p { font-size: 14px; font-weight: 500; }

    .table-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .stats-bar {
        background: linear-gradient(135deg, #fdf2f7 0%, #fff5f8 100%);
        border-bottom: 1px solid #fce4ec;
        padding: 14px 24px;
        display: flex;
        align-items: center;
        gap: 24px;
    }
    .stat-item { font-size: 12.5px; color: #6b7280; font-weight: 500; }
    .stat-item strong { color: #111827; font-weight: 700; margin-right: 4px; }
</style>

<div class="currency-wrap page-fade-in" style="padding: 28px 32px; min-height: 100vh; background: #f8fafc;">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <div>
            <h1 style="font-size:22px; font-weight:700; color:#111827; margin:0 0 4px;">
                Currencies
            </h1>
            <p style="font-size:13px; color:#9ca3af; margin:0; font-weight:400;">
                Manage your store's supported currencies
            </p>
        </div>
        <a href="{{ route('admin.currencies.create') }}" class="btn-add">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5"
                 viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Currency
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="flash-success" style="margin-bottom:20px;">
            <svg style="display:inline;margin-right:6px;vertical-align:-3px;" width="15" height="15"
                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flash-error" style="margin-bottom:20px;">
            <svg style="display:inline;margin-right:6px;vertical-align:-3px;" width="15" height="15"
                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Table Card --}}
    <div class="table-card">

        {{-- Stats Bar --}}
        <div class="stats-bar">
            <div class="stat-item">
                <strong>{{ $currencies->count() }}</strong> Total Currencies
            </div>
            <div style="width:1px;height:14px;background:#fce4ec;"></div>
            <div class="stat-item">
                <strong>{{ $currencies->where('is_default', true)->first()?->name ?? 'N/A' }}</strong>
                Default Currency
            </div>
        </div>

        <table class="currency-table" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#fafafa; border-bottom:1px solid #f1f5f9;">
                    <th style="padding:13px 24px; font-size:11.5px; font-weight:700; text-transform:uppercase;
                               letter-spacing:0.7px; color:#9ca3af; text-align:left;">#</th>
                    <th style="padding:13px 24px; font-size:11.5px; font-weight:700; text-transform:uppercase;
                               letter-spacing:0.7px; color:#9ca3af; text-align:left;">Name</th>
                    <th style="padding:13px 24px; font-size:11.5px; font-weight:700; text-transform:uppercase;
                               letter-spacing:0.7px; color:#9ca3af; text-align:left;">Symbol</th>
                    <th style="padding:13px 24px; font-size:11.5px; font-weight:700; text-transform:uppercase;
                               letter-spacing:0.7px; color:#9ca3af; text-align:left;">Rate</th>
                    <th style="padding:13px 24px; font-size:11.5px; font-weight:700; text-transform:uppercase;
                               letter-spacing:0.7px; color:#9ca3af; text-align:left;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($currencies as $i => $currency)
                <tr style="border-bottom:1px solid #f9fafb;">
                    <td style="padding:14px 24px; color:#cbd5e1; font-size:13px; font-weight:600;">
                        {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                    </td>
                    <td style="padding:14px 24px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:36px; height:36px; border-radius:10px;
                                        background:linear-gradient(135deg,#fce4ec,#ffd7e8);
                                        display:flex; align-items:center; justify-content:center;
                                        font-size:13px; font-weight:700; color:#e91e63; flex-shrink:0;">
                                {{ substr($currency->name, 0, 1) }}
                            </div>
                            <span style="font-size:14px; font-weight:600; color:#111827;">
                                {{ $currency->name }}
                            </span>
                        </div>
                    </td>
                    <td style="padding:14px 24px;">
                        <span style="display:inline-flex; align-items:center; justify-content:center;
                                     min-width:36px; height:36px; border-radius:8px;
                                     background:#f8fafc; border:1px solid #e2e8f0;
                                     font-size:16px; font-weight:600; color:#374151; padding:0 10px;">
                            {{ $currency->symbol }}
                        </span>
                    </td>
                    <td style="padding:14px 24px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span class="rate-value" style="font-size:14px;">
                                {{ number_format($currency->rate, 2) }}
                            </span>
                            @if($currency->is_default)
                                <span class="badge-default">
                                    <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="4"/>
                                    </svg>
                                    Default
                                </span>
                            @else
                                <span class="badge-usd">From USD</span>
                            @endif
                        </div>
                    </td>
                    <td style="padding:14px 24px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <a href="{{ route('admin.currencies.edit', $currency->id) }}"
                               class="action-btn action-edit" title="Edit">
                                <svg width="15" height="15" fill="none" stroke="currentColor"
                                     stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                             m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @if(!$currency->is_default)
                            <form action="{{ route('admin.currencies.destroy', $currency->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete {{ $currency->name }}?')"
                                  style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn action-delete" title="Delete">
                                    <svg width="15" height="15" fill="none" stroke="currentColor"
                                         stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                                 a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                                 m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <svg width="48" height="48" fill="none" stroke="#9ca3af" stroke-width="1.5"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2
                                         m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1
                                         c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>No currencies found. Add your first currency to get started.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
