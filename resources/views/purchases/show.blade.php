@extends('layouts.app')
@section('title', 'Purchase ' . $purchase->reference_no)

@section('content')
<div style="max-width:800px;">
    <div class="card" style="margin-bottom:16px;">
        <h2 style="margin:0;">{{ $purchase->reference_no }}</h2>
        <div style="color:#64748b;font-size:14px;">{{ $purchase->created_at->format('Y-m-d H:i') }}</div>
        <div style="margin-top:12px;font-size:14px;">
            <strong>Supplier:</strong> {{ $purchase->supplier?->name ?? '—' }}<br>
            <strong>By:</strong> {{ $purchase->user?->name ?? '—' }}
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <table>
            <thead><tr>
                <th>Product</th>
                <th style="text-align:right;">Cost</th>
                <th style="text-align:right;">Qty</th>
                <th style="text-align:right;">Total</th>
            </tr></thead>
            <tbody>
            @foreach($purchase->items as $i)
                <tr>
                    <td>{{ $i->product_name }}</td>
                    <td style="text-align:right;">{{ number_format($i->cost, 2) }}</td>
                    <td style="text-align:right;">{{ $i->quantity }}</td>
                    <td style="text-align:right;">{{ number_format($i->total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="margin-top:16px;border-top:1px solid #e2e8f0;padding-top:12px;">
            <div style="display:flex;justify-content:space-between;"><span>Subtotal</span><span>{{ number_format($purchase->subtotal, 2) }}</span></div>
            <div style="display:flex;justify-content:space-between;"><span>Discount</span><span>-{{ number_format($purchase->discount, 2) }}</span></div>
            <div style="display:flex;justify-content:space-between;"><span>Tax</span><span>{{ number_format($purchase->tax, 2) }}</span></div>
            <div style="display:flex;justify-content:space-between;font-weight:bold;font-size:18px;margin-top:8px;">
                <span>TOTAL</span><span>{{ setting('currency_symbol') }} {{ number_format($purchase->total, 2) }}</span>
            </div>
        </div>
    </div>

    <a href="{{ route('purchases.index') }}">← Back to Purchases</a>
</div>
@endsection