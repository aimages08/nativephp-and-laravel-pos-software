@php $p = $product ?? null; @endphp

<div class="form-group">
    <label>Name *</label>
    <input name="name" value="{{ old('name', $p->name ?? '') }}" required>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="form-group">
        <label>SKU *</label>
        <input name="sku" value="{{ old('sku', $p->sku ?? '') }}" required>
    </div>
    <div class="form-group">
        <label>Barcode</label>
        <div style="display:flex;gap:6px;">
            <input name="barcode" id="barcodeInput" value="{{ old('barcode', $p->barcode ?? '') }}" placeholder="Type, paste, or scan">
            <button type="button" onclick="generateBarcode()" class="btn btn-primary" style="white-space:nowrap;">🎲 Generate</button>
        </div>
        <small style="color:#94a3b8;font-size:11px;">Tip: click the field and scan with a USB scanner.</small>
    </div>
</div>

<div class="form-group">
    <label>Barcode Type</label>
    <select name="barcode_type">
        <option value="existing" {{ old('barcode_type', $p->barcode_type ?? '') == 'existing' ? 'selected' : '' }}>Existing (from manufacturer)</option>
        <option value="generated" {{ old('barcode_type', $p->barcode_type ?? 'generated') == 'generated' ? 'selected' : '' }}>Generated (shop's own)</option>
        <option value="none" {{ old('barcode_type', $p->barcode_type ?? '') == 'none' ? 'selected' : '' }}>None (loose item)</option>
    </select>
</div>

<div class="form-group">
    <label>PLU Code <small style="color:#94a3b8;">(short code for loose items — e.g. 11, 12)</small></label>
    <input name="plu" value="{{ old('plu', $p->plu ?? '') }}" placeholder="e.g. 11" maxlength="20">
</div>


<div class="form-group">
    <label>Category</label>
    <select name="category_id">
        <option value="">— none —</option>
        @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ old('category_id', $p->category_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @endforeach
    </select>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
    <div class="form-group">
        <label>Cost</label>
        <input type="number" step="0.01" name="cost" value="{{ old('cost', $p->cost ?? 0) }}">
    </div>
    <div class="form-group">
        <label>Price *</label>
        <input type="number" step="0.01" name="price" value="{{ old('price', $p->price ?? 0) }}" required>
    </div>
    <div class="form-group">
        <label>Stock *</label>
        <input type="number" name="stock" value="{{ old('stock', $p->stock ?? 0) }}" required>
    </div>
</div>

<div class="form-group">
    <label>Expiry Date (optional)</label>
    <input type="date" name="expiry_date" value="{{ old('expiry_date', $p?->expiry_date?->format('Y-m-d') ?? '') }}">
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="form-group">
        <label>Low Stock Alert At</label>
        <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $p->low_stock_threshold ?? 5) }}">
    </div>
    <div class="form-group">
        <label>Unit</label>
        <input name="unit" value="{{ old('unit', $p->unit ?? 'pcs') }}">
    </div>
</div>

<div class="form-group">
    <label style="display:inline-flex;align-items:center;">
        <input type="checkbox" name="is_active" value="1" style="width:auto;margin-right:8px;" {{ old('is_active', $p->is_active ?? true) ? 'checked' : '' }}>
        Active
    </label>
</div>

@push('scripts')
<script>
async function generateBarcode() {
    const res = await fetch('{{ route('products.generate-barcode') }}');
    const data = await res.json();
    document.getElementById('barcodeInput').value = data.barcode;
}
</script>
@endpush