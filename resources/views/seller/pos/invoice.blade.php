<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Invoice #{{ $invoice->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-color); 
            color: var(--text-main);
            margin: 0;
            padding: 20px;
        }

        .invoice-container {
            max-width: 850px;
            margin: 0 auto;
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.04);
            overflow: hidden;
            position: relative;
        }

        .invoice-header {
            background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%);
            padding: 60px 50px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-section h1 { font-size: 32px; font-weight: 800; margin: 0; letter-spacing: -0.5px; }
        .brand-section p { opacity: 0.8; margin: 5px 0 0; font-size: 14px; }

        .invoice-id-badge {
            background: rgba(255,255,255,0.15);
            padding: 12px 24px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            text-align: right;
        }
        .invoice-id-badge span { display: block; font-size: 12px; opacity: 0.7; text-transform: uppercase; font-weight: 700; }
        .invoice-id-badge strong { font-size: 20px; }

        .invoice-body { padding: 50px; }

        .address-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 50px; }
        .address-block h6 { font-size: 12px; font-weight: 800; color: var(--secondary-color); text-transform: uppercase; margin-bottom: 15px; letter-spacing: 1px; }
        .address-block h4 { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
        .address-block p { font-size: 14px; color: var(--text-muted); margin: 0; line-height: 1.6; }

        .info-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 24px;
            background: #f1f5f9;
            border-radius: 16px;
            margin-bottom: 40px;
        }
        .info-strip-item label { display: block; font-size: 10px; font-weight: 800; color: var(--secondary-color); text-transform: uppercase; margin-bottom: 4px; }
        .info-strip-item span { font-size: 14px; font-weight: 700; color: var(--text-main); }

        .table-wrap { margin-bottom: 40px; }
        .table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table th { 
            padding: 16px; 
            text-align: left; 
            font-size: 12px; 
            font-weight: 800; 
            color: var(--secondary-color); 
            text-transform: uppercase; 
            border-bottom: 2px solid var(--border-color);
        }
        .table td { padding: 20px 16px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }

        .product-meta { display: flex; align-items: center; gap: 15px; }
        .product-img { width: 48px; height: 48px; border-radius: 10px; object-fit: cover; background: #f1f5f9; }
        .product-name { font-weight: 700; font-size: 15px; color: var(--text-main); }
        
        /* Product Type Badge */
        .type-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .type-digital { background: #e0e7ff; color: #4338ca; }
        .type-normal { background: #f1f5f9; color: #475569; }

        .summary-section { display: flex; justify-content: space-between; align-items: flex-start; }
        .qr-section { width: 120px; text-align: center; }
        .qr-section img { width: 100%; border-radius: 12px; border: 1px solid var(--border-color); padding: 5px; }
        
        .totals-box { width: 320px; background: #f8fafc; padding: 24px; border-radius: 16px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14px; color: var(--text-muted); }
        .total-row.grand-total { 
            margin-top: 16px; 
            padding-top: 16px; 
            border-top: 2px dashed var(--border-color); 
            color: var(--primary-color); 
            font-size: 20px; 
            font-weight: 800; 
        }

        .invoice-footer {
            padding: 40px 50px;
            text-align: center;
            background: #f8fafc;
            border-top: 1px solid var(--border-color);
        }
        .invoice-footer h5 { font-weight: 800; margin-bottom: 8px; }
        .invoice-footer p { color: var(--text-muted); font-size: 14px; margin: 0; }

        .no-print-zone { padding: 20px; text-align: center; }
        .btn-print { 
            background: var(--primary-color); 
            color: white; 
            border: none; 
            padding: 12px 32px; 
            border-radius: 12px; 
            font-weight: 700; 
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        }
        .btn-print:hover { transform: translateY(-2px); box-shadow: 0 15px 30px rgba(79, 70, 229, 0.3); }

        @media print {
            body { background: white; padding: 0; }
            .invoice-container { box-shadow: none; border-radius: 0; }
            .no-print-zone { display: none; }
            .invoice-header { padding: 40px 50px; }
        }
    </style>
</head>
<body>

<div class="no-print-zone">
    <button class="btn-print" onclick="window.print()"><i class="bi bi-printer-fill me-2"></i> Print Professional Invoice</button>
</div>

<div class="invoice-container">
    {{-- Top Section --}}
    <div class="invoice-header">
        <div class="brand-section">
            <img src="{{ $shop->logo ? asset($shop->logo) : asset('assets/admin/images/default-logo.png') }}" alt="Logo" style="height: 50px; margin-bottom: 15px;">
            <h1>{{ $shop->name }}</h1>
            <p><i class="bi bi-globe me-1"></i> {{ $shop->url ?? 'www.jhrbazar.com' }}</p>
        </div>
        <div class="invoice-id-badge">
            <span>Invoice Number</span>
            <strong>#{{ $invoice->invoice_number }}</strong>
        </div>
    </div>

    <div class="invoice-body">
        {{-- Addresses --}}
        <div class="address-grid">
            <div class="address-block">
                <h6>Issued By</h6>
                <h4>{{ $shop->name }}</h4>
                <p>{{ $shop->address }}</p>
                <p><i class="bi bi-telephone-fill me-1"></i> {{ Auth::user()->phone }}</p>
                <p><i class="bi bi-envelope-fill me-1"></i> {{ Auth::user()->email }}</p>
            </div>
            <div class="address-block">
                <h6>Billed To</h6>
                <h4>{{ $invoice->customer ? ($invoice->customer->first_name . ' ' . $invoice->customer->last_name) : 'Walk-in Customer' }}</h4>
                @if($invoice->customer && $invoice->customer->user->phone)
                    <p><i class="bi bi-telephone-fill me-1"></i> {{ $invoice->customer->user->phone }}</p>
                @endif
                @if($invoice->customer && $invoice->customer->user->email)
                    <p><i class="bi bi-envelope-fill me-1"></i> {{ $invoice->customer->user->email }}</p>
                @endif
                @if($invoice->customer && $invoice->customer->address)
                    <p><i class="bi bi-geo-alt-fill me-1"></i> {{ $invoice->customer->address }}</p>
                @endif
            </div>
        </div>

        {{-- Meta Strip --}}
        <div class="info-strip">
            <div class="info-strip-item">
                <label>Date Issued</label>
                <span>{{ $invoice->created_at->format('M d, Y') }}</span>
            </div>
            <div class="info-strip-item">
                <label>Order Date</label>
                <span>{{ $invoice->order->created_at->format('M d, Y') }}</span>
            </div>
            <div class="info-strip-item">
                <label>Payment Mode</label>
                <span>{{ $invoice->payment_method_label }}</span>
            </div>
            <div class="info-strip-item">
                <label>Status</label>
                <span class="text-success">Paid</span>
            </div>
        </div>

        {{-- Products Table --}}
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Product Description</th>
                        <th class="text-center">Rate</th>
                        <th class="text-center">Qty</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <div class="product-meta">
                                @php
                                    $thumb = $item['thumbnail'] ?? $item['image'] ?? '';
                                    $name = $item['name'] ?? $item['title'] ?? 'Product';
                                @endphp
                                <img src="{{ !empty($thumb) ? asset($thumb) : asset('images/no-image.png') }}" class="product-img">
                                <div>
                                    <div class="product-name">{{ $name }}</div>
                                    <div class="type-badge {{ $item['product_type'] === 'digital' ? 'type-digital' : 'type-normal' }}">
                                        {{ $item['product_type'] === 'digital' ? 'Digital Product' : 'Regular Product' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center; font-weight: 600;">৳{{ number_format($item['price'], 2) }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ $item['qty'] }}</td>
                        <td style="text-align: right; font-weight: 700; color: var(--text-main);">৳{{ number_format($item['price'] * $item['qty'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Summary & QR --}}
        <div class="summary-section">
            <div class="qr-section">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $invoice->invoice_number }}" alt="QR">
                <p style="font-size: 10px; font-weight: 800; color: var(--secondary-color); margin-top: 8px; text-transform: uppercase;">Scan to Verify</p>
            </div>
            <div class="totals-box">
                <div class="total-row">
                    <span>Subtotal</span>
                    <strong style="color: var(--text-main);">৳{{ number_format($invoice->sub_total, 2) }}</strong>
                </div>
                <div class="total-row">
                    <span>Discount</span>
                    <strong style="color: var(--danger-color);">-৳{{ number_format($invoice->discount, 2) }}</strong>
                </div>
                <div class="total-row">
                    <span>Tax Charges</span>
                    <strong style="color: var(--text-main);">৳{{ number_format($invoice->tax_amount, 2) }}</strong>
                </div>
                <div class="total-row">
                    <span>Shipping Charges</span>
                    <strong style="color: var(--text-main);">৳{{ number_format($invoice->delivery_charge, 2) }}</strong>
                </div>
                <div class="total-row grand-total">
                    <span>Grand Total</span>
                    <span>৳{{ number_format($invoice->grand_total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="invoice-footer">
        <h5>Thank you for your business!</h5>
        <p>This is a computer generated invoice and does not require a physical signature.</p>
        <div style="margin-top: 20px; font-weight: 700; color: var(--primary-color);">{{ $shop->name }}</div>
    </div>
</div>

</body>
</html>
