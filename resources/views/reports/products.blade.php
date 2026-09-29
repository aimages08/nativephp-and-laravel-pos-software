@extends('layouts.app')
@section('title', 'Top Products')

@section('content')


<div style="margin-bottom:16px;">
    <a href="{{ route('reports.index') }}" style="color:#3b82f6;text-decoration:none;">← Back to Reports</a>
</div>

<div class="card" style="margin-bottom:16px;">
    <h2 style="margin-top:0;">Top Products</h2>
    <form style="display:flex;gap:12px;align-items:end;">
        <div><label>From</label><input type="date" name="from" value="{{ $from }}"></div>
        <div><label>To</label><input type="date" name="to" value="{{ $to }}"></div>
        <button class="btn btn-primary">Filter</button>
    </form>
</div>

<div class="card">
    <table>
        <thead><tr>
            <th>#</th>
            <th>Product</th>
            <th style="text-align:right;">Qty Sold</th>
            <th style="text-align:right;">Revenue</th>
        </tr></thead>
        <tbody>
        @forelse($items as $idx => $i)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $i->product_name }}</td>
                <td style="text-align:right;">{{ $i->qty }}</td>
                <td style="text-align:right;">{{ setting('currency_symbol') }} {{ number_format($i->revenue, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:30px;">No data.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:16px;">{{ $items->links() }}</div>
@endsection