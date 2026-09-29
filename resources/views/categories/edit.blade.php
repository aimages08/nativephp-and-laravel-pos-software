@extends('layouts.app')
@section('title', 'Edit Category')

@section('content')
<div class="card" style="max-width:600px;">
    <h2 style="margin-top:0;">Edit Category</h2>
    <form method="POST" action="{{ route('categories.update', $category) }}">
        @csrf @method('PUT')
        @include('categories._form', ['category' => $category])
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('categories.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection