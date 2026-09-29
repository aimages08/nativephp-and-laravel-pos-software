@extends('layouts.app')
@section('title', 'Edit Product')

@section('content')
<div class="card" style="max-width:700px;">
    <h2 style="margin-top:0;">Edit Product</h2>
    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf @method('PUT')
        @include('products._form', ['product' => $product])
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('products.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection