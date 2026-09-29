@extends('layouts.app')
@section('title', 'Sale ' . $sale->invoice_no)

@section('content')
<div style="max-width:800px;">
    <div class="card" style="margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h2 style="margin:0;">{{ $sale->invoice_no }}</h2>
                <div style="color:#64748b;font-size:14px;">{{ $sale->created_at->format('Y-m-d H:i') }}</div>
            </div>
            <div>
                @if($sale->status === 'completed')
                    <span style="background:#d1fae5;color:#065f46;padding:6px 12px;border-radius:6px;">Completed</span>
                @elseif($sale->status === 'refunded')
                    <span style="background:#fee2e2;color:#991b1b;padding:6px 12px;border-radius:6px;">Refunded</span>
                @endif
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:14px;">
            <div><strong>Customer:</strong> {{ $sale->customer?->name ?? 'Walk-in' }}</div>
            <div><strong>Cashier:</strong> {{ $sale->user?->name ?? '—' }}</div>
            <div><strong>Payment:</strong> {{ ucfirst($sale->payment_method) }}</div>
            <div><strong>Paid:</strong> {{ setting('currency_symbol') }} {{ number_format($sale->paid, 2) }}</div>
        </div>
    </div>

    <div class="card" style="margin-bottom:16px;">
        <table>
            <thead><tr>
                <th>Product</th>
                <th style="text-align:right;">Price</th>
                <th style="text-align:right;">Qty</th>
                <th style="text-align:right;">Total</th>
            </tr></thead>
            <tbody>
            @foreach($sale->items as $i)
                <tr>
                    <td>{{ $i->product_name }}</td>
                    <td style="text-align:right;">{{ number_format($i->price, 2) }}</td>
                    <td style="text-align:right;">{{ $i->quantity }}</td>
                    <td style="text-align:right;">{{ number_format($i->total, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="margin-top:16px;border-top:1px solid #e2e8f0;padding-top:12px;">
            <div style="display:flex;justify-content:space-between;"><span>Subtotal</span><span>{{ number_format($sale->subtotal, 2) }}</span></div>
            <div style="display:flex;justify-content:space-between;"><span>Discount</span><span>-{{ number_format($sale->discount, 2) }}</span></div>
            <div style="display:flex;justify-content:space-between;"><span>Tax</span><span>{{ number_format($sale->tax, 2) }}</span></div>
            <div style="display:flex;justify-content:space-between;font-weight:bold;font-size:18px;margin-top:8px;">
                <span>TOTAL</span><span>{{ setting('currency_symbol') }} {{ number_format($sale->total, 2) }}</span>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:8px;">
        <a href="{{ route('pos.receipt', $sale) }}" class="btn btn-primary" target="_blank">🖨️ Print Receipt</a>

        @if($sale->status === 'completed')
            <form action="{{ route('sales.refund', $sale) }}" method="POST" onsubmit="return confirm('Refund this sale and restore stock?')">
                @csrf
                <button class="btn btn-danger">↩️ Refund</button>
            </form>
        @elseif($sale->status === 'refunded')
            <form action="{{ route('sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Delete this refunded sale permanently?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger">🗑️ Delete</button>
            </form>
        @endif

        <a href="{{ route('sales.index') }}" style="align-self:center;margin-left:12px;">← Back</a>
    </div>
</div>
@endsection