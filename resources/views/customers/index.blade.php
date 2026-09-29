@extends('layouts.app')
@section('title', 'Customers')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h2 style="margin:0;">Customers</h2>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">+ New Customer</a>
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
            <th>Status</th>
            <th></th>
        </tr></thead>
        <tbody>
        @forelse($customers as $c)
            <tr>
                <td>{{ $c->name }}</td>
                <td>{{ $c->phone ?? '—' }}</td>
                <td>{{ $c->email ?? '—' }}</td>
                <td style="text-align:right;">{{ setting('currency_symbol') }} {{ number_format($c->balance, 2) }}</td>
                <td>{{ $c->is_active ? '✅' : '⛔' }}</td>
                <td style="text-align:right;white-space:nowrap;">
                    <a href="{{ route('customers.edit', $c) }}" style="color:#3b82f6;">Edit</a>
                    <form action="{{ route('customers.destroy', $c) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete {{ $c->name }}?')">
                        @csrf @method('DELETE')
                        <button style="background:none;border:none;color:#ef4444;cursor:pointer;margin-left:8px;">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">No customers yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $customers->links() }}</div>
</div>
@endsection