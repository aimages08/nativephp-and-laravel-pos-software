@extends('layouts.app')
@section('title', 'Stock Report')

@section('content')

<div style="margin-bottom:16px;">
    <a href="{{ route('reports.index') }}" style="color:#3b82f6;text-decoration:none;">← Back to Reports</a>
</div>


<div class="card" style="margin-bottom:16px;">
    <h2 style="margin-top:0;">Stock Report</h2>
    <form style="display:flex;gap:8px;">
        <button class="btn btn-primary" name="filter" value="">All</button>
        <button class="btn btn-primary" name="filter" value="low">Low Stock</button>
        <button class="btn btn-primary" name="filter" value="out">Out of Stock</button>
        <button class="btn btn-primary" name="filter" value="expiring">Expiring in 30 days</button>
        <button class="btn btn-primary" name="filter" value="expired">Expired</button>
    </form>
    <div style="margin-top:12px;color:#64748b;">
        Total stock value (cost): <strong>{{ setting('currency_symbol') }} {{ number_format($totalValue, 2) }}</strong>
    </div>
</div>

<div class="card">
    <table>
        <thead><tr>
            <th>Product</th>
            <th>SKU</th>
            <th style="text-align:right;">Stock</th>
            <th style="text-align:right;">Cost</th>
            <th style="text-align:right;">Price</th>
            <th style="text-align:right;">Value</th>
            <th>Expiry</th>
        </tr></thead>
        <tbody>
        @forelse($products as $p)
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->sku }}</td>
                <td style="text-align:right;{{ $p->is_low_stock ? 'color:#ef4444;font-weight:bold;' : '' }}">{{ $p->stock }}</td>
                <td style="text-align:right;">{{ number_format($p->cost, 2) }}</td>
                <td style="text-align:right;">{{ number_format($p->price, 2) }}</td>
                <td style="text-align:right;">{{ number_format($p->stock * $p->cost, 2) }}</td>
                <td>
                    @if($p->expiry_date)
                        @if($p->is_expired)<span style="color:#ef4444;font-weight:bold;">⚠️ {{ $p->expiry_date->format('Y-m-d') }}</span>
                        @elseif($p->is_expiring_soon)<span style="color:#f59e0b;">⏰ {{ $p->expiry_date->format('Y-m-d') }}</span>
                        @else{{ $p->expiry_date->format('Y-m-d') }}@endif
                    @else — @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:30px;">No products match.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>


<div style="margin-top:16px;">{{ $products->links() }}</div>
@endsection