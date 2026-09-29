@extends('layouts.app')
@section('title', 'Categories')

@section('content')
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <form style="margin-bottom:16px;">
                <input name="search" value="{{ request('search') }}" placeholder="Search categories..."
                    style="max-width:400px;">
            </form>
            <h2 style="margin:0;">Categories</h2>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">+ New Category</a>

        </div>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th style="text-align:right;">Products</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $c)
                    <tr>
                        <td>{{ $c->name }}</td>
                        <td style="color:#64748b;">{{ $c->description ?? '—' }}</td>
                        <td style="text-align:right;">{{ $c->products_count }}</td>
                        <td>{{ $c->is_active ? '✅ Active' : '⛔ Inactive' }}</td>
                        <td style="text-align:right;white-space:nowrap;">
                            <a href="{{ route('categories.edit', $c) }}" style="color:#3b82f6;">Edit</a>
                            <form action="{{ route('categories.destroy', $c) }}" method="POST" style="display:inline;"
                                onsubmit="return confirm('Delete {{ $c->name }}?')">
                                @csrf @method('DELETE')
                                <button
                                    style="background:none;border:none;color:#ef4444;cursor:pointer;margin-left:8px;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;color:#94a3b8;padding:30px;">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:16px;">{{ $categories->links() }}</div>
    </div>
@endsection
