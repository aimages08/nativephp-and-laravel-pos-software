@extends('layouts.app')
@section('title', 'Expenses')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h2 style="margin:0;">Expenses</h2>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">+ New Expense</a>
    </div>

    <form style="display:flex;gap:12px;margin-bottom:16px;align-items:end;">
        <div>
            <label>From</label>
            <input type="date" name="from" value="{{ request('from') }}">
        </div>
        <div>
            <label>To</label>
            <input type="date" name="to" value="{{ request('to') }}">
        </div>
        <div>
            <label>Category</label>
            <select name="category">
                <option value="">All</option>
                @foreach($categories as $c)
                    <option value="{{ $c }}" {{ request('category') == $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary">Filter</button>
    </form>

    <div style="margin-bottom:12px;font-size:14px;color:#64748b;">
        Total for this filter: <strong style="color:#ef4444;">{{ setting('currency_symbol') }} {{ number_format($total, 2) }}</strong>
    </div>

    <table>
        <thead><tr>
            <th>Date</th>
            <th>Title</th>
            <th>Category</th>
            <th>By</th>
            <th style="text-align:right;">Amount</th>
            <th></th>
        </tr></thead>
        <tbody>
        @forelse($expenses as $e)
            <tr>
                <td>{{ $e->expense_date->format('Y-m-d') }}</td>
                <td>{{ $e->title }}</td>
                <td>{{ $e->category ?? '—' }}</td>
                <td>{{ $e->user?->name ?? '—' }}</td>
                <td style="text-align:right;">{{ setting('currency_symbol') }} {{ number_format($e->amount, 2) }}</td>
                <td style="text-align:right;white-space:nowrap;">
                    <a href="{{ route('expenses.edit', $e) }}" style="color:#3b82f6;">Edit</a>
                    <form action="{{ route('expenses.destroy', $e) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button style="background:none;border:none;color:#ef4444;cursor:pointer;margin-left:8px;">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">No expenses yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $expenses->links() }}</div>
</div>
@endsection