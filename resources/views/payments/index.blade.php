@extends('layouts.app')
@section('title', 'Customer Payments')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h2 style="margin:0;">Customer Payments</h2>
        <a href="{{ route('payments.create') }}" class="btn btn-primary">+ New Payment</a>
    </div>

    <form style="margin-bottom:16px;">
        <select name="customer_id" onchange="this.form.submit()" style="max-width:300px;">
            <option value="">All customers</option>
            @foreach($customers as $c)
                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
    </form>

    <div style="margin-bottom:12px;font-size:14px;color:#64748b;">
        Total: <strong style="color:#10b981;">{{ setting('currency_symbol') }} {{ number_format($total, 2) }}</strong>
    </div>

    <table>
        <thead><tr><th>Date</th><th>Customer</th><th>Method</th><th>Reference</th><th style="text-align:right;">Amount</th><th></th></tr></thead>
        <tbody>
        @forelse($payments as $p)
            <tr>
                <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
                <td><a href="{{ route('customers.show', $p->customer) }}" style="color:#3b82f6;">{{ $p->customer?->name }}</a></td>
                <td>{{ ucfirst($p->method) }}</td>
                <td>{{ $p->reference ?? '—' }}</td>
                <td style="text-align:right;">{{ setting('currency_symbol') }} {{ number_format($p->amount, 2) }}</td>
                <td style="text-align:right;">
                    <form action="{{ route('payments.destroy', $p) }}" method="POST" onsubmit="return confirm('Reverse payment?')" style="display:inline;">
                        @csrf @method('DELETE')
                        <button style="background:none;border:none;color:#ef4444;cursor:pointer;">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">No payments.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $payments->links() }}</div>
</div>
@endsection