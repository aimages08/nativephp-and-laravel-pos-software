@extends('layouts.app')
@section('title', 'Purchases')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h2 style="margin:0;">Purchase Orders</h2>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ New Purchase</a>
    </div>

    <table>
        <thead><tr>
            <th>Ref</th>
            <th>Date</th>
            <th>Supplier</th>
            <th style="text-align:right;">Total</th>
            <th>Status</th>
            <th></th>
        </tr></thead>
        <tbody>
        @forelse($purchases as $p)
            <tr>
                <td>{{ $p->reference_no }}</td>
                <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $p->supplier?->name ?? '—' }}</td>
                <td style="text-align:right;">{{ setting('currency_symbol') }} {{ number_format($p->total, 2) }}</td>
                <td>{{ ucfirst($p->status) }}</td>
                <td style="text-align:right;">
                    <a href="{{ route('purchases.show', $p) }}" style="color:#3b82f6;">View</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">No purchases yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $purchases->links() }}</div>
</div>
@endsection