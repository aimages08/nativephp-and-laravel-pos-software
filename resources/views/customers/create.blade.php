@extends('layouts.app')
@section('title', 'New Customer')

@section('content')
<div class="card" style="max-width:600px;">
    <h2 style="margin-top:0;">New Customer</h2>
    <form method="POST" action="{{ route('customers.store') }}">
        @csrf
        @include('customers._form', ['customer' => null])
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('customers.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection