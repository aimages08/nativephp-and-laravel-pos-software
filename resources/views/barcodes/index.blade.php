@extends('layouts.app')
@section('title', 'Barcode Labels')

@section('content')
<form method="POST" action="{{ route('barcodes.print') }}">
    @csrf
    <div class="card" style="margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h2 style="margin:0;">Barcode Labels</h2>
            <button class="btn btn-primary">🖨️ Print Selected</button>
        </div>

        <div style="display:flex;gap:12px;margin-bottom:16px;">
            <input name="search" value="{{ request('search') }}" placeholder="Search product..." style="max-width:300px;">
            <select name="category_id" style="max-width:200px;">
                <option value="">All categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary">Filter</button>
        </div>

        <table>
            <thead><tr>
                <th style="width:40px;"><input type="checkbox" onclick="document.querySelectorAll('.chk').forEach(c=>c.checked=this.checked)"></th>
                <th>Product</th>
                <th>SKU</th>
                <th>Barcode</th>
                <th style="text-align:right;">Price</th>
                <th style="width:100px;">Copies</th>
                <th></th>
            </tr></thead>
            <tbody>
            @forelse($products as $p)
                <tr>
                    <td><input type="checkbox" class="chk" name="ids[]" value="{{ $p->id }}"></td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->barcode ?? '—' }}</td>
                    <td style="text-align:right;">{{ setting('currency_symbol') }} {{ number_format($p->price, 2) }}</td>
                    <td><input type="number" name="qty[{{ $p->id }}]" value="1" min="1" style="width:70px;"></td>
                    <td style="text-align:right;">
                        <a href="{{ route('barcodes.single', $p) }}" target="_blank" style="color:#3b82f6;">Print 1</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:30px;">No products.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top:16px;">{{ $products->links() }}</div>
    </div>
</form>
@endsection