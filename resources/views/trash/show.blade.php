@extends('layouts.app')
@section('title', 'Trash — ' . ucfirst($type))

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <a href="{{ route('trash.index') }}" style="color:#3b82f6;">← Back to Trash</a>
    <form action="{{ route('trash.empty', $type) }}" method="POST" onsubmit="return confirm('Permanently delete ALL items in this trash?')">
        @csrf @method('DELETE')
        <button class="btn btn-danger">🗑️ Empty Trash</button>
    </form>
</div>

<div class="card">
    <h2>{{ ucfirst($type) }} — Deleted Items</h2>
    <table>
        <thead><tr><th>Name / Detail</th><th>Deleted</th><th></th></tr></thead>
        <tbody>
        @forelse($items as $item)
            <tr>
                <td>{{ $item->name ?? $item->title ?? $item->invoice_no ?? $item->reference_no ?? ('#' . $item->id) }}</td>
                <td>{{ $item->deleted_at->format('Y-m-d H:i') }}</td>
                <td style="text-align:right;white-space:nowrap;">
                    <form action="{{ route('trash.restore', [$type, $item->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        <button class="btn btn-success">Restore</button>
                    </form>
                    <form action="{{ route('trash.force-delete', [$type, $item->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Permanently delete?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger">Delete Forever</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:30px;">Trash is empty.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div style="margin-top:16px;">{{ $items->links() }}</div>
</div>
@endsection