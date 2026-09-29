@extends('layouts.app')
@section('title', 'Edit Customer')

@section('content')
<div class="card" style="max-width:600px;">
    <h2 style="margin-top:0;">Edit Customer</h2>
    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf @method('PUT')
        @include('customers._form', ['customer' => $customer])
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('customers.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection