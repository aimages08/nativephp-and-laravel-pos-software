<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $sale->invoice_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.4;
            padding: 10px;
            width: 300px;
            margin: 0 auto;
            color: #000;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .row { display: flex; justify-content: space-between; }
        hr { border: none; border-top: 1px dashed #000; margin: 6px 0; }
        .total { font-size: 16px; font-weight: bold; }
        .store-name { font-size: 18px; font-weight: bold; }

        .no-print { margin-top: 20px; text-align: center; }
        .no-print button, .no-print a {
            padding: 8px 16px;
            margin: 4px;
            border: none;
            border-radius: 6px;
            background: #3b82f6;
            color: #fff;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .no-print a { background: #10b981; }

        @media print {
            body { width: 100%; padding: 4px; }
            .no-print { display: none; }
            @page { size: 80mm auto; margin: 2mm; }
        }
        @media print and (max-width: 60mm) {
            @page { size: 58mm auto; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="center">
        <div class="store-name">{{ setting('store_name') }}</div>
        @if(setting('store_address'))<div>{{ setting('store_address') }}</div>@endif
        @if(setting('store_phone'))<div>{{ setting('store_phone') }}</div>@endif
    </div>

    <hr>

    <div class="row"><span>Invoice:</span><span class="bold">{{ $sale->invoice_no }}</span></div>
    <div class="row"><span>Date:</span><span>{{ $sale->created_at->format('Y-m-d H:i') }}</span></div>
    <div class="row"><span>Cashier:</span><span>{{ $sale->user?->name ?? 'N/A' }}</span></div>
    <div class="row"><span>Customer:</span><span>{{ $sale->customer?->name ?? 'Walk-in' }}</span></div>

    <hr>

    <div class="row bold">
        <span style="width:50%;">Item</span>
        <span style="width:15%;text-align:center;">Qty</span>
        <span style="width:18%;text-align:right;">Price</span>
        <span style="width:17%;text-align:right;">Total</span>
    </div>
    <hr>

    @foreach($sale->items as $i)
        <div style="margin-bottom:4px;">
            <div class="bold" style="word-break:break-word;">{{ $i->product_name }}</div>
            <div class="row" style="font-size:12px;">
                <span style="width:50%;"></span>
                <span style="width:15%;text-align:center;">{{ $i->quantity }}</span>
                <span style="width:18%;text-align:right;">{{ number_format($i->price, 2) }}</span>
                <span style="width:17%;text-align:right;">{{ number_format($i->total, 2) }}</span>
            </div>
        </div>
    @endforeach

    <hr>

    <div class="row"><span>Subtotal</span><span>{{ number_format($sale->subtotal, 2) }}</span></div>
    @if($sale->discount > 0)
        <div class="row"><span>Discount</span><span>-{{ number_format($sale->discount, 2) }}</span></div>
    @endif
    @if($sale->tax > 0)
        <div class="row"><span>Tax</span><span>{{ number_format($sale->tax, 2) }}</span></div>
    @endif
    <hr>
    <div class="row total"><span>TOTAL</span><span>{{ setting('currency_symbol') }} {{ number_format($sale->total, 2) }}</span></div>
    <div class="row"><span>Paid ({{ ucfirst($sale->payment_method) }})</span><span>{{ number_format($sale->paid, 2) }}</span></div>
    <div class="row"><span>Change</span><span>{{ number_format($sale->change_due, 2) }}</span></div>

    <hr>

    <div class="center" style="margin-top:8px;">{{ setting('receipt_footer') }}</div>
    <div class="center" style="margin-top:6px;font-size:11px;">Items: {{ $sale->items->sum('quantity') }} · Thank you!</div>

    <div class="no-print">
        <button onclick="window.print()">🖨️ Print Again</button>
        <a href="{{ route('pos.index') }}">New Sale</a>
        <a href="{{ route('sales.index') }}">Sales</a>
    </div>
</body>
</html>