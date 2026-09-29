@extends('layouts.app')
@section('title', 'New Expense')

@section('content')
<div class="card" style="max-width:600px;">
    <h2 style="margin-top:0;">New Expense</h2>
    <form method="POST" action="{{ route('expenses.store') }}">
        @csrf
        @include('expenses._form', ['expense' => null])
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('expenses.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection