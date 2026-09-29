@extends('layouts.app')
@section('title', 'Adjust Stock')

@push('styles')
<style>
    .search-results { max-height:300px; overflow-y:auto; margin-top:8px; border:1px solid #e2e8f0; border-radius:6px; }
    .search-result { padding:10px 12px; cursor:pointer; border-bottom:1px solid #f1f5f9; }
    .search-result:hover { background:#f8fafc; }
    .selected-product { background:#eff6ff; border:1px solid #3b82f6; padding:12px; border-radius:6px; }
</style>
@endpush

@section('content')
<div class="card" style="max-width:600px;">
    <h2 style="margin-top:0;">Adjust Stock</h2>

    <form method="POST" action="{{ route('stock.store') }}">
        @csrf

        <div class="form-group">
            <label>Product *</label>
            <input id="search" placeholder="🔍 Search product name / SKU / barcode..." autocomplete="off">
            <input type="hidden" name="product_id" id="product_id" required>
            <div id="results" class="search-results" style="display:none;"></div>
            <div id="selected" class="selected-product" style="display:none;margin-top:8px;"></div>
        </div>

       <div class="form-group">
    <label>Adjustment Type *</label>
    <select name="type" required id="typeSelect">
        <option value="add">➕ Add stock (found, returned from customer)</option>
        <option value="remove">➖ Remove stock (generic)</option>
        <option value="damage">💥 Damage (broken)</option>
        <option value="loss">🔍 Loss / Theft</option>
        <option value="wastage">🗑️ Wastage (spoilage)</option>
        <option value="expired">⏰ Expired (past expiry date)</option>
        <option value="supplier_return">🚚 Return to supplier</option>
        <option value="count">🔢 Set to count (physical count)</option>
    </select>
</div>

    <div class="form-group">
        <label>Reason (optional)</label>
        <input name="reason" placeholder="e.g. Dropped, expired batch, damaged box">
    </div>

        <div class="form-group">
            <label>Quantity *</label>
            <input type="number" name="quantity" min="1" value="1" required>
        </div>

        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" rows="2" placeholder="Reason, reference, etc."></textarea>
        </div>

        <button class="btn btn-primary">Apply Adjustment</button>
        <a href="{{ route('stock.index') }}" style="margin-left:12px;">Cancel</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
const searchEl = document.getElementById('search');
const resultsEl = document.getElementById('results');
const selectedEl = document.getElementById('selected');
const productIdEl = document.getElementById('product_id');

async function search(q) {
    if (!q) { resultsEl.style.display = 'none'; return; }
    const res = await fetch('{{ route('stock.products') }}?q=' + encodeURIComponent(q));
    const items = await res.json();
    if (items.length === 0) { resultsEl.style.display = 'none'; return; }
    resultsEl.style.display = 'block';
    resultsEl.innerHTML = items.map(p => `
        <div class="search-result" data-id="${p.id}" data-name="${p.name}" data-stock="${p.stock}" data-sku="${p.sku}">
            <strong>${p.name}</strong> <small style="color:#94a3b8;">${p.sku}</small>
            <div style="font-size:12px;color:#64748b;">Current stock: ${p.stock}</div>
        </div>
    `).join('');
}

searchEl.addEventListener('input', e => search(e.target.value));

resultsEl.addEventListener('click', e => {
    const row = e.target.closest('.search-result');
    if (!row) return;
    productIdEl.value = row.dataset.id;
    selectedEl.style.display = 'block';
    selectedEl.innerHTML = `<strong>${row.dataset.name}</strong><br><small>SKU: ${row.dataset.sku} · Current stock: ${row.dataset.stock}</small>`;
    searchEl.value = row.dataset.name;
    resultsEl.style.display = 'none';
});
</script>
@endpush