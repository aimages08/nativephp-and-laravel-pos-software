<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Statement — {{ $customer->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: auto; }
        h1 { margin: 0 0 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 8px; border-bottom: 1px solid #e2e8f0; text-align: left; font-size: 14px; }
        th { background: #f8fafc; }
        .right { text-align: right; }
        .header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; }
        .controls { margin-bottom: 20px; }
        .controls button, .controls a { padding: 8px 16px; margin-right: 8px; background: #3b82f6; color: #fff; border: none; border-radius: 6px; text-decoration: none; cursor: pointer; }
        .summary { margin-top: 20px; padding: 12px; background: #f1f5f9; border-radius: 6px; }
        @media print { .controls { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="controls">
        <button onclick="window.print()">🖨️ Print</button>
        <a href="{{ route('customers.show', $customer) }}">← Back</a>
    </div>

    <div class="header">
        <div>
            <h1>{{ setting('store_name') }}</h1>
            <div>{{ setting('store_address') }}</div>
            <div>{{ setting('store_phone') }}</div>
        </div>
        <div style="text-align:right;">
            <h3 style="margin:0;">Customer Statement</h3>
            <div>{{ now()->format('Y-m-d H:i') }}</div>
        </div>
    </div>

    <div>
        <strong>{{ $customer->name }}</strong><br>
        {{ $customer->phone ?? '' }}<br>
        {{ $customer->email ?? '' }}<br>
        {{ $customer->address ?? '' }}
    </div>

    <table>
        <thead>
            <tr><th>Date</th><th>Description</th><th class="right">Debit</th><th class="right">Credit</th></tr>
        </thead>
        <tbody>
            @foreach($customer->sales->sortBy('created_at') as $s)
                <tr>
                    <td>{{ $s->created_at->format('Y-m-d') }}</td>
                    <td>Sale {{ $s->invoice_no }}</td>
                    <td class="right">{{ number_format($s->total, 2) }}</td>
                    <td class="right">—</td>
                </tr>
            @endforeach
            @foreach($customer->payments->sortBy('created_at') as $p)
                <tr>
                    <td>{{ $p->created_at->format('Y-m-d') }}</td>
                    <td>Payment ({{ ucfirst($p->method) }})</td>
                    <td class="right">—</td>
                    <td class="right">{{ number_format($p->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div style="display:flex;justify-content:space-between;">
            <span>Outstanding Balance</span>
            <strong style="font-size:18px;">{{ setting('currency_symbol') }} {{ number_format($customer->balance, 2) }}</strong>
        </div>
    </div>
</body>
</html>