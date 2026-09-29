@extends('layouts.app')
@section('title', 'Edit Supplier')

@section('content')
<div class="card" style="max-width:600px;">
    <h2 style="margin-top:0;">Edit Supplier</h2>
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
        @csrf @method('PUT')
        @include('suppliers._form', ['supplier' => $supplier])
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('suppliers.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection