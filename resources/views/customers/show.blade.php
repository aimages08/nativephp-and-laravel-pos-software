@extends('layouts.app')
@section('title', $customer->name)

@section('content')
<div class="card" style="margin-bottom:16px;">
    <div style="display:flex;justify-content:space-between;align-items:start;">
        <div>
            <h2 style="margin:0;">{{ $customer->name }}</h2>
            <div style="color:#64748b;font-size:14px;margin-top:6px;">
                {{ $customer->phone ?? '—' }} · {{ $customer->email ?? '—' }}
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:13px;color:#64748b;">Outstanding Balance</div>
            <div style="font-size:28px;font-weight:bold;color:{{ $customer->balance > 0 ? '#ef4444' : '#10b981' }};">
                {{ setting('currency_symbol') }} {{ number_format($customer->balance, 2) }}
            </div>
        </div>
    </div>
    <div style="margin-top:16px;display:flex;gap:8px;">
        <a href="{{ route('payments.create', ['customer_id' => $customer->id]) }}" class="btn btn-success">💰 Record Payment</a>
        <a href="{{ route('customers.statement', $customer) }}" class="btn btn-primary" target="_blank">🖨️ Print Statement</a>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">Edit</a>
        <a href="{{ route('customers.index') }}" style="align-self:center;margin-left:12px;">← Back</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="card">
        <h3 style="margin-top:0;">Recent Purchases</h3>
        <table>
            <thead><tr><th>Invoice</th><th>Date</th><th style="text-align:right;">Total</th><th style="text-align:right;">Paid</th></tr></thead>
            <tbody>
            @forelse($customer->sales as $s)
                <tr>
                    <td><a href="{{ route('sales.show', $s) }}" style="color:#3b82f6;">{{ $s->invoice_no }}</a></td>
                    <td>{{ $s->created_at->format('Y-m-d') }}</td>
                    <td style="text-align:right;">{{ number_format($s->total, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($s->paid, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">No purchases.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Recent Payments</h3>
        <table>
            <thead><tr><th>Date</th><th>Method</th><th style="text-align:right;">Amount</th><th></th></tr></thead>
            <tbody>
            @forelse($customer->payments as $p)
                <tr>
                    <td>{{ $p->created_at->format('Y-m-d') }}</td>
                    <td>{{ ucfirst($p->method) }}</td>
                    <td style="text-align:right;">{{ number_format($p->amount, 2) }}</td>
                    <td style="text-align:right;">
                        <form action="{{ route('payments.destroy', $p) }}" method="POST" onsubmit="return confirm('Reverse this payment?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button style="background:none;border:none;color:#ef4444;cursor:pointer;">×</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">No payments.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection