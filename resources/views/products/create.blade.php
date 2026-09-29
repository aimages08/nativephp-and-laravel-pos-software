@extends('layouts.app')
@section('title', 'New Product')

@section('content')
<div class="card" style="max-width:700px;">
    <h2 style="margin-top:0;">New Product</h2>
    <form method="POST" action="{{ route('products.store') }}">
        @csrf
        @include('products._form', ['product' => null])
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('products.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection