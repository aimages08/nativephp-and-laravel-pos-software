@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h2 style="margin:0;">Suppliers</h2>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">+ New Supplier</a>
    </div>

    <form style="margin-bottom:16px;">
        <input name="search" value="{{ request('search') }}" placeholder="Search name / phone / email..." style="max-width:400px;">
    </form>

    <table>
        <thead><tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th style="text-align:right;">Balance</th>
            <th></th>
        </tr></thead>
        <tbody>
        @forelse($suppliers as $s)
            <tr>
                <td>{{ $s->name }}</td>
                <td>{{ $s->phone ?? '—' }}</td>
                <td>{{ $s->email ?? '—' }}</td>
                <td style="text-align:right;">{{ setting('currency_symbol') }} {{ number_format($s->balance, 2) }}</td>
                <td style="text-align:right;white-space:nowrap;">
                    <a href="{{ route('suppliers.edit', $s) }}" style="color:#3b82f6;">Edit</a>
                    <form action="{{ route('suppliers.destroy', $s) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete {{ $s->name }}?')">
                        @csrf @method('DELETE')
                        <button style="background:none;border:none;color:#ef4444;cursor:pointer;margin-left:8px;">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:30px;">No suppliers yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $suppliers->links() }}</div>
</div>
@endsection