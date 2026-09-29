@extends('layouts.app')
@section('title', 'Trash')

@section('content')
<h2>🗑️ Recycle Bin</h2>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
    @foreach($data as $type => $count)
        <a href="{{ route('trash.show', $type) }}" class="card" style="text-decoration:none;color:#111;">
            <div style="font-size:14px;color:#64748b;">{{ ucfirst($type) }}</div>
            <div style="font-size:28px;font-weight:bold;">{{ $count }}</div>
        </a>
    @endforeach
</div>
@endsection