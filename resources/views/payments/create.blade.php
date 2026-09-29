@extends('layouts.app')
@section('title', 'New Payment')

@section('content')
<div class="card" style="max-width:600px;">
    <h2 style="margin-top:0;">Record Customer Payment</h2>
    <form method="POST" action="{{ route('payments.store') }}">
        @csrf

        <div class="form-group">
            <label>Customer *</label>
            <select name="customer_id" required>
                <option value="">Select customer</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id', $selected) == $c->id ? 'selected' : '' }}>
                        {{ $c->name }} — Balance: {{ number_format($c->balance, 2) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Amount *</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required>
        </div>

        <div class="form-group">
            <label>Method</label>
            <select name="method">
                <option value="cash">Cash</option>
                <option value="bank">Bank transfer</option>
                <option value="card">Card</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label>Reference</label>
            <input name="reference" value="{{ old('reference') }}" placeholder="Cheque no, transaction ID">
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" rows="2">{{ old('notes') }}</textarea>
        </div>

        <button class="btn btn-primary">Save Payment</button>
        <a href="{{ route('payments.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection