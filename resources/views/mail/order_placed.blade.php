<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Order Placed</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .order-info {
            background-color: #f9fbfc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #2a5298;
        }
        .order-info h2 {
            margin-top: 0;
            font-size: 18px;
            color: #1e3c72;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th, .details-table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .details-table th {
            color: #666;
            font-weight: 600;
            font-size: 14px;
        }
        .total-row td {
            font-weight: 700;
            font-size: 18px;
            color: #1e3c72;
            border-bottom: none;
        }
        .footer {
            background-color: #f4f7f6;
            color: #777;
            text-align: center;
            padding: 20px;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            background-color: #e3f2fd;
            color: #1e88e5;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Order Received!</h1>
        </div>
        <div class="content">
            <p>Hello Admin,</p>
            <p>A new order has been placed on your website. Here are the details:</p>
            
            <div class="order-info">
                <h2>Order Summary <span class="badge">#{{ $invoice->invoice_number }}</span></h2>
                <p><strong>Customer:</strong> {{ $order->phone }}</p>
                <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>

            <table class="details-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item['title'] ?? $item['name'] ?? 'Product' }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>৳{{ number_format($item['price'], 2) }}</td>
                        <td>৳{{ number_format($item['price'] * $item['qty'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="3" style="text-align: right;">Subtotal:</td>
                        <td>৳{{ number_format($invoice->sub_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: right;">Shipping:</td>
                        <td>৳{{ number_format($invoice->delivery_charge, 2) }}</td>
                    </tr>
                    @if($invoice->discount > 0)
                    <tr>
                        <td colspan="3" style="text-align: right;">Discount:</td>
                        <td>-৳{{ number_format($invoice->discount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">Grand Total:</td>
                        <td>৳{{ number_format($invoice->grand_total, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div style="margin-top: 30px;">
                <p><strong>Customer Note:</strong></p>
                <p style="white-space: pre-line; background: #fdfdfd; padding: 15px; border: 1px solid #eee; border-radius: 6px;">{{ $order->note }}</p>
            </div>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
