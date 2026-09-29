@extends('layouts.app')
@section('title', 'Stock Movements')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h2 style="margin:0;">Stock Movements</h2>
        <a href="{{ route('stock.create') }}" class="btn btn-primary">+ Adjust Stock</a>
    </div>

    <form style="display:flex;gap:12px;margin-bottom:16px;align-items:end;">
        <div>
            <label>Search product</label>
            <input name="search" value="{{ request('search') }}" placeholder="Product name">
        </div>
        <div>
            <label>Type</label>


            <select name="type">
                <option value="">All</option>
                <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Sale</option>
                <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Refund</option>
                <option value="adjustment-add" {{ request('type') == 'adjustment-add' ? 'selected' : '' }}>Adjustment: Add</option>
                <option value="adjustment-remove" {{ request('type') == 'adjustment-remove' ? 'selected' : '' }}>Adjustment: Remove</option>
                <option value="adjustment-damage" {{ request('type') == 'adjustment-damage' ? 'selected' : '' }}>Adjustment: Damage</option>
                <option value="adjustment-loss" {{ request('type') == 'adjustment-loss' ? 'selected' : '' }}>Adjustment: Loss</option>
                <option value="adjustment-wastage" {{ request('type') == 'adjustment-wastage' ? 'selected' : '' }}>Adjustment: Wastage</option>
                <option value="adjustment-expired" {{ request('type') == 'adjustment-expired' ? 'selected' : '' }}>Adjustment: Expired</option>
                <option value="adjustment-supplier_return" {{ request('type') == 'adjustment-supplier_return' ? 'selected' : '' }}>Adjustment: Supplier Return</option>
                <option value="adjustment-count" {{ request('type') == 'adjustment-count' ? 'selected' : '' }}>Adjustment: Count</option>
            </select>



        </div>
        <button class="btn btn-primary">Filter</button>
    </form>

    <table>


        <thead><tr>
            <th>Date</th>
            <th>Product</th>
            <th>Type</th>
            <th style="text-align:right;">Change</th>
            <th>Reason</th>
            <th>By</th>
        </tr></thead>


        <tbody>
        @forelse($movements as $m)


          <tr>
            <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
            <td>{{ $m->product?->name ?? '—' }}</td>
            <td>{{ $m->type }}</td>
            <td style="text-align:right;font-weight:600;color:{{ $m->quantity > 0 ? '#10b981' : '#ef4444' }};">
                {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
            </td>
            <td><small style="color:#64748b;">{{ $m->reason ?? '—' }}</small></td>
            <td>{{ $m->user?->name ?? '—' }}</td>
        </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">No stock movements yet.</td></tr>
        @endforelse



        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $movements->links() }}</div>
</div>
@endsection