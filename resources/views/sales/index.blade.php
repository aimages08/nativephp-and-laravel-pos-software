@extends('layouts.app')
@section('title', 'Sales History')

@section('content')
<div class="card">
    <h2 style="margin-top:0;">Sales History</h2>

    <form style="display:flex;gap:12px;margin-bottom:16px;align-items:end;">
        <div>
            <label>Search</label>
            <input name="search" value="{{ request('search') }}" placeholder="Invoice or customer">
        </div>
        <div>
            <label>From</label>
            <input type="date" name="from" value="{{ request('from') }}">
        </div>
        <div>
            <label>To</label>
            <input type="date" name="to" value="{{ request('to') }}">
        </div>
        <button class="btn btn-primary">Filter</button>
    </form>

    <div style="margin-bottom:12px;font-size:14px;color:#64748b;">
        Total for this filter: <strong>{{ setting('currency_symbol') }} {{ number_format($total, 2) }}</strong>
    </div>

    <table>
        <thead><tr>
            <th>Invoice</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Cashier</th>
            <th style="text-align:right;">Total</th>
            <th>Status</th>
            <th></th>
        </tr></thead>
        <tbody>
        @forelse($sales as $s)
            <tr>
                <td>{{ $s->invoice_no }}</td>
                <td>{{ $s->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $s->customer?->name ?? 'Walk-in' }}</td>
                <td>{{ $s->user?->name ?? '—' }}</td>
                <td style="text-align:right;">{{ setting('currency_symbol') }} {{ number_format($s->total, 2) }}</td>
                <td>
                    @if($s->status === 'completed') <span style="color:#10b981;">✅ Completed</span>
                    @elseif($s->status === 'refunded') <span style="color:#ef4444;">↩️ Refunded</span>
                    @else {{ $s->status }} @endif
                </td>
                <td style="text-align:right;white-space:nowrap;">
                    <a href="{{ route('sales.show', $s) }}" style="color:#3b82f6;">View</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:30px;">No sales yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $sales->links() }}</div>
</div>
@endsection