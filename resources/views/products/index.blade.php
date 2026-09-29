@extends('layouts.app')
@section('title', 'Products')

@section('content')
    <div class="card">


        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <h2 style="margin:0;">Products</h2>
    <div style="display:flex;gap:8px;">
        <form method="POST" action="{{ route('products.bulk-generate-barcodes') }}" onsubmit="return confirm('Generate barcodes for all products without one?')">
            @csrf
            <button class="btn btn-primary" style="background:#f59e0b;">🎲 Generate Missing Barcodes</button>
        </form>
        <a href="{{ route('products.create') }}" class="btn btn-primary">+ New Product</a>
    </div>
</div>

        <form style="margin-bottom:16px;">
            <input name="search" value="{{ request('search') }}" placeholder="Search name / SKU / barcode..."
                style="max-width:400px;">
        </form>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th style="text-align:right;">Price</th>
                    <th style="text-align:right;">Stock</th>
                    <th>Expiry</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->sku }}</td>
                        <td>{{ $p->category?->name ?? '—' }}</td>
                        <td style="text-align:right;">{{ setting('currency_symbol') }} {{ number_format($p->price, 2) }}
                        </td>
                        <td style="text-align:right;{{ $p->is_low_stock ? 'color:#ef4444;font-weight:bold;' : '' }}">
                            {{ $p->stock }}</td>
                        <td>
                            @if ($p->expiry_date)
                                @if ($p->is_expired)
                                    <span style="color:#ef4444;font-weight:bold;">⚠️
                                        {{ $p->expiry_date->format('Y-m-d') }}</span>
                                @elseif($p->is_expiring_soon)
                                    <span style="color:#f59e0b;">⏰ {{ $p->expiry_date->format('Y-m-d') }}</span>
                                @else
                                    {{ $p->expiry_date->format('Y-m-d') }}
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td style="text-align:right;white-space:nowrap;">
                            <a href="{{ route('products.edit', $p) }}" style="color:#3b82f6;">Edit</a>
                            <form action="{{ route('products.destroy', $p) }}" method="POST" style="display:inline;"
                                onsubmit="return confirm('Delete {{ $p->name }}?')">
                                @csrf @method('DELETE')
                                <button
                                    style="background:none;border:none;color:#ef4444;cursor:pointer;margin-left:8px;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">No products yet. Add one →
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:16px;">{{ $products->links() }}</div>
    </div>
@endsection
