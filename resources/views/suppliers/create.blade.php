@extends('layouts.app')
@section('title', 'New Supplier')

@section('content')
<div class="card" style="max-width:600px;">
    <h2 style="margin-top:0;">New Supplier</h2>
    <form method="POST" action="{{ route('suppliers.store') }}">
        @csrf
        @include('suppliers._form', ['supplier' => null])
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('suppliers.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection