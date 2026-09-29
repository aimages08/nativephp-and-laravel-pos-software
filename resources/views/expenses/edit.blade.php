@extends('layouts.app')
@section('title', 'Edit Expense')

@section('content')
<div class="card" style="max-width:600px;">
    <h2 style="margin-top:0;">Edit Expense</h2>
    <form method="POST" action="{{ route('expenses.update', $expense) }}">
        @csrf @method('PUT')
        @include('expenses._form', ['expense' => $expense])
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('expenses.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection