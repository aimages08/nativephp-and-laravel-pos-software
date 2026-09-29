@extends('layouts.app')
@section('title', 'Profit Report')

@section('content')

<div style="margin-bottom:16px;">
    <a href="{{ route('reports.index') }}" style="color:#3b82f6;text-decoration:none;">← Back to Reports</a>
</div>

<div class="card" style="margin-bottom:16px;">
    <h2 style="margin-top:0;">Profit Report</h2>
    <form style="display:flex;gap:12px;align-items:end;">
        <div><label>From</label><input type="date" name="from" value="{{ $from }}"></div>
        <div><label>To</label><input type="date" name="to" value="{{ $to }}"></div>
        <button class="btn btn-primary">Filter</button>
        <button type="button" class="btn btn-primary" onclick="window.print()">🖨️ Print</button>
    </form>
</div>

<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-bottom:16px;">
    <div class="card"><small style="color:#64748b;">Revenue</small><div style="font-size:22px;font-weight:bold;color:#10b981;">{{ number_format($summary['revenue'], 2) }}</div></div>
    <div class="card"><small style="color:#64748b;">Cost of Goods</small><div style="font-size:22px;font-weight:bold;">{{ number_format($summary['cost'], 2) }}</div></div>
    <div class="card"><small style="color:#64748b;">Gross Profit</small><div style="font-size:22px;font-weight:bold;">{{ number_format($summary['gross_profit'], 2) }}</div></div>
    <div class="card"><small style="color:#64748b;">Expenses</small><div style="font-size:22px;font-weight:bold;color:#ef4444;">{{ number_format($summary['expenses'], 2) }}</div></div>
    <div class="card"><small style="color:#64748b;">Net Profit</small><div style="font-size:22px;font-weight:bold;color:{{ $summary['net_profit'] >= 0 ? '#10b981' : '#ef4444' }};">{{ number_format($summary['net_profit'], 2) }}</div></div>
</div>

<div class="card">
    <h3 style="margin-top:0;">Per Product</h3>
    <table>
        <thead><tr>
            <th>Product</th>
            <th style="text-align:right;">Qty</th>
            <th style="text-align:right;">Revenue</th>
            <th style="text-align:right;">Cost</th>
            <th style="text-align:right;">Profit</th>
        </tr></thead>
        <tbody>
        @forelse($items as $i)
            <tr>
                <td>{{ $i->product_name }}</td>
                <td style="text-align:right;">{{ $i->qty }}</td>
                <td style="text-align:right;">{{ number_format($i->revenue, 2) }}</td>
                <td style="text-align:right;">{{ number_format($i->cost, 2) }}</td>
                <td style="text-align:right;color:{{ ($i->revenue - $i->cost) >= 0 ? '#10b981' : '#ef4444' }};">{{ number_format($i->revenue - $i->cost, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:30px;">No sales in this range.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>


<div style="margin-top:16px;">{{ $items->links() }}</div>
@endsection