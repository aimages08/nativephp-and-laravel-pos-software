@extends('layouts.app')
@section('title', 'New Category')

@section('content')
<div class="card" style="max-width:600px;">
    <h2 style="margin-top:0;">New Category</h2>
    <form method="POST" action="{{ route('categories.store') }}">
        @csrf
        @include('categories._form', ['category' => null])
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('categories.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection