@php $e = $expense ?? null; @endphp

<div class="form-group">
    <label>Title *</label>
    <input name="title" value="{{ old('title', $e->title ?? '') }}" required placeholder="e.g. Electricity bill">
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="form-group">
        <label>Amount *</label>
        <input type="number" step="0.01" name="amount" value="{{ old('amount', $e->amount ?? '') }}" required>
    </div>
    <div class="form-group">
        <label>Date *</label>
        <input type="date" name="expense_date" value="{{ old('expense_date', $e?->expense_date?->format('Y-m-d') ?? date('Y-m-d')) }}" required>
    </div>
</div>

<div class="form-group">
    <label>Category</label>
    <input name="category" value="{{ old('category', $e->category ?? '') }}" placeholder="Rent, Utilities, Salary, etc.">
</div>

<div class="form-group">
    <label>Notes</label>
    <textarea name="notes" rows="2">{{ old('notes', $e->notes ?? '') }}</textarea>
</div>