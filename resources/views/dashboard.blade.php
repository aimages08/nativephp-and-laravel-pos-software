@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 16px; }
    .stat { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .stat small { color: #64748b; font-size: 13px; display: block; margin-bottom: 6px; }
    .stat .val { font-size: 24px; font-weight: bold; }
    .chart-wrap { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 16px; }
    .chart-bars { display: flex; align-items: flex-end; gap: 12px; height: 200px; margin-top: 16px; }
    .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px; }
    .bar { width: 100%; background: linear-gradient(180deg,#3b82f6,#2563eb); border-radius: 4px 4px 0 0; min-height: 4px; }
    .bar-label { font-size: 12px; color: #64748b; }
    .bar-val { font-size: 11px; color: #94a3b8; }
    .row2 { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px; }
    .alert-box { background: #fff; padding: 16px; border-radius: 8px; display: flex; gap: 12px; align-items: center; }
    .alert-box.red { border-left: 4px solid #ef4444; }
    .alert-box.orange { border-left: 4px solid #f59e0b; }
</style>
@endpush

@section('content')
<h2 style="margin-top:0;">Dashboard</h2>

{{-- Alerts --}}
@if($expiredCount > 0 || $expiringCount > 0 || $lowStockCount > 0)
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px;">
    @if($lowStockCount > 0)
        <div class="alert-box red">
            <div style="font-size:24px;">📉</div>
            <div>
                <div style="font-weight:600;">{{ $lowStockCount }} low stock items</div>
                <a href="{{ route('reports.stock') }}?filter=low" style="font-size:13px;color:#3b82f6;">View report →</a>
            </div>
        </div>
    @endif
    @if($expiringCount > 0)
        <div class="alert-box orange">
            <div style="font-size:24px;">⏰</div>
            <div>
                <div style="font-weight:600;">{{ $expiringCount }} expiring soon</div>
                <a href="{{ route('reports.stock') }}?filter=expiring" style="font-size:13px;color:#3b82f6;">View report →</a>
            </div>
        </div>
    @endif
    @if($expiredCount > 0)
        <div class="alert-box red">
            <div style="font-size:24px;">⚠️</div>
            <div>
                <div style="font-weight:600;">{{ $expiredCount }} expired items</div>
                <a href="{{ route('reports.stock') }}?filter=expired" style="font-size:13px;color:#3b82f6;">View report →</a>
            </div>
        </div>
    @endif
</div>
@endif

{{-- Stats --}}
<div class="stat-grid">
    <div class="stat">
        <small>Today's Sales</small>
        <div class="val">{{ setting('currency_symbol') }} {{ number_format($todaySales, 2) }}</div>
    </div>
    <div class="stat">
        <small>Transactions Today</small>
        <div class="val">{{ $todayCount }}</div>
    </div>
    <div class="stat">
        <small>This Month</small>
        <div class="val">{{ setting('currency_symbol') }} {{ number_format($monthSales, 2) }}</div>
    </div>
    <div class="stat">
        <small>Month Profit</small>
        <div class="val" style="color:{{ $monthProfit >= 0 ? '#10b981' : '#ef4444' }};">{{ setting('currency_symbol') }} {{ number_format($monthProfit, 2) }}</div>
    </div>
</div>

{{-- Chart --}}
<div class="chart-wrap">
    <h3 style="margin-top:0;">Last 7 Days Sales</h3>
    @php $max = max($chart) ?: 1; @endphp
    <div class="chart-bars">
        @foreach($chart as $i => $val)
            <div class="bar-col">
                <div class="bar-val">{{ number_format($val, 0) }}</div>
                <div class="bar" style="height: {{ max(4, ($val / $max) * 160) }}px;"></div>
                <div class="bar-label">{{ $chartLabels[$i] }}</div>
            </div>
        @endforeach
    </div>
</div>

<div class="row2">
    {{-- Recent Sales --}}
    <div class="chart-wrap">
        <h3 style="margin-top:0;">Recent Sales</h3>
        <table>
            <thead><tr><th>Invoice</th><th>Customer</th><th style="text-align:right;">Total</th><th>Time</th></tr></thead>
            <tbody>
            @forelse($recent as $s)
                <tr>
                    <td><a href="{{ route('sales.show', $s) }}" style="color:#3b82f6;">{{ $s->invoice_no }}</a></td>
                    <td>{{ $s->customer?->name ?? 'Walk-in' }}</td>
                    <td style="text-align:right;">{{ setting('currency_symbol') }} {{ number_format($s->total, 2) }}</td>
                    <td>{{ $s->created_at->format('H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">No sales yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Top Products --}}
    <div class="chart-wrap">
        <h3 style="margin-top:0;">Top Products (month)</h3>
        @forelse($topProducts as $t)
            <div style="padding:8px 0;border-bottom:1px solid #f1f5f9;">
                <div style="font-weight:500;">{{ $t->product_name }}</div>
                <div style="font-size:12px;color:#64748b;">{{ $t->qty }} sold · {{ setting('currency_symbol') }} {{ number_format($t->revenue, 2) }}</div>
            </div>
        @empty
            <div style="color:#94a3b8;">No data</div>
        @endforelse
    </div>
</div>

{{-- Quick Actions --}}
<div style="display:flex;gap:12px;">
    <a href="{{ route('pos.index') }}" class="btn btn-primary">🛒 Open POS</a>
    <a href="{{ route('products.create') }}" class="btn btn-success">+ Add Product</a>
    <a href="{{ route('reports.index') }}" class="btn btn-primary">📈 Reports</a>
</div>
@endsection