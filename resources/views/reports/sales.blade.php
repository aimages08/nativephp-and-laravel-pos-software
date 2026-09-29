@extends('layouts.app')
@section('title', 'Sales Report')

@section('content')

<div style="margin-bottom:16px;">
    <a href="{{ route('reports.index') }}" style="color:#3b82f6;text-decoration:none;">← Back to Reports</a>
</div>

<div class="card" style="margin-bottom:16px;">
    <h2 style="margin-top:0;">Sales Report</h2>
    <form style="display:flex;gap:12px;align-items:end;">
        <div><label>From</label><input type="date" name="from" value="{{ $from }}"></div>
        <div><label>To</label><input type="date" name="to" value="{{ $to }}"></div>
        <button class="btn btn-primary">Filter</button>
        <button type="button" class="btn btn-primary" onclick="window.print()">🖨️ Print</button>
    </form>
</div>

<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:16px;">
    <div class="card"><small style="color:#64748b;">Total Sales</small><div style="font-size:24px;font-weight:bold;">{{ setting('currency_symbol') }} {{ number_format($summary['total'], 2) }}</div></div>
    <div class="card"><small style="color:#64748b;">Transactions</small><div style="font-size:24px;font-weight:bold;">{{ $summary['count'] }}</div></div>
    <div class="card"><small style="color:#64748b;">Average Sale</small><div style="font-size:24px;font-weight:bold;">{{ number_format($summary['avg'], 2) }}</div></div>
    <div class="card"><small style="color:#64748b;">Discounts</small><div style="font-size:24px;font-weight:bold;">{{ number_format($summary['discount'], 2) }}</div></div>
    <div class="card"><small style="color:#64748b;">Tax</small><div style="font-size:24px;font-weight:bold;">{{ number_format($summary['tax'], 2) }}</div></div>
</div>

<div class="card">
    <h3 style="margin-top:0;">Daily Breakdown</h3>
    <table>
        <thead><tr><th>Date</th><th style="text-align:right;">Transactions</th><th style="text-align:right;">Total</th></tr></thead>
        <tbody>
        @forelse($daily as $d)
            <tr>
                <td>{{ $d->date }}</td>
                <td style="text-align:right;">{{ $d->count }}</td>
                <td style="text-align:right;">{{ setting('currency_symbol') }} {{ number_format($d->total, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:30px;">No sales in this range.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $daily->links() }}</div>
@endsection