@extends('layouts.app')
@section('title', 'New Purchase')

@push('styles')
<style>
    .purchase-grid { display:grid; grid-template-columns: 1fr 400px; gap:16px; }
    .search-results { max-height:300px; overflow-y:auto; margin-top:8px; }
    .search-result { padding:8px 12px; cursor:pointer; border-bottom:1px solid #f1f5f9; }
    .search-result:hover { background:#f8fafc; }
    .line-row { display:grid; grid-template-columns: 1fr 70px 80px 80px 30px; gap:8px; padding:8px; border-bottom:1px solid #f1f5f9; align-items:center; font-size:14px; }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
    @csrf

    <div class="purchase-grid">
        <div>
            <div class="card" style="margin-bottom:16px;">
                <h3 style="margin-top:0;">Products</h3>
                <input id="search" placeholder="🔍 Search product name / SKU / barcode..." autofocus>
                <div class="search-results" id="results"></div>
            </div>
        </div>

        <div>
            <div class="card" style="margin-bottom:16px;">
                <label>Supplier</label>
                <select name="supplier_id">
                    <option value="">— none —</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="card">
                <h3 style="margin-top:0;">Lines</h3>
                <div id="lines"></div>

                <div style="margin-top:16px;border-top:2px solid #e2e8f0;padding-top:12px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;"><span>Subtotal</span><span id="subtotal">0.00</span></div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span>Discount</span>
                        <input type="number" step="0.01" name="discount" id="discount" value="0" style="width:100px;text-align:right;">
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                        <span>Tax</span>
                        <input type="number" step="0.01" name="tax" id="tax" value="0" style="width:100px;text-align:right;">
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:20px;font-weight:bold;margin:12px 0;">
                        <span>Total</span><span id="total">0.00</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
                        <span>Paid</span>
                        <input type="number" step="0.01" name="paid" id="paid" value="0" style="width:120px;text-align:right;">
                    </div>

                    <button class="btn btn-success" style="width:100%;padding:12px;">Save Purchase</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
const currency = @json(setting('currency_symbol', 'Rs'));
let lines = [];

const searchEl = document.getElementById('search');
const resultsEl = document.getElementById('results');
const linesEl = document.getElementById('lines');

function money(n) { return currency + ' ' + parseFloat(n || 0).toFixed(2); }

function renderLines() {
    linesEl.innerHTML = lines.map((l, i) => `
        <div class="line-row">
            <div>
                <div style="font-weight:600;">${l.name}</div>
                <input type="hidden" name="items[${i}][product_id]" value="${l.id}">
                <input type="hidden" name="items[${i}][cost]" value="${l.cost}">
            </div>
            <input type="number" name="items[${i}][quantity]" min="1" value="${l.qty}" onchange="setQty(${i}, this.value)" style="text-align:center;">
            <div style="text-align:right;">${money(l.cost)}</div>
            <div style="text-align:right;font-weight:600;">${money(l.cost * l.qty)}</div>
            <button type="button" onclick="removeLine(${i})" style="background:none;border:none;color:#ef4444;cursor:pointer;">×</button>
        </div>
    `).join('') || '<div style="padding:20px;color:#94a3b8;text-align:center;">No lines. Search a product to add.</div>';
    updateTotals();
}

function updateTotals() {
    const sub = lines.reduce((s, l) => s + l.cost * l.qty, 0);
    const disc = parseFloat(document.getElementById('discount').value) || 0;
    const tax = parseFloat(document.getElementById('tax').value) || 0;
    document.getElementById('subtotal').textContent = money(sub);
    document.getElementById('total').textContent = money(sub - disc + tax);
}

function addProduct(p) {
    const existing = lines.find(l => l.id === p.id);
    if (existing) existing.qty++;
    else lines.push({ id: p.id, name: p.name, cost: parseFloat(p.cost), qty: 1 });
    renderLines();
}

function setQty(i, v) { lines[i].qty = Math.max(1, parseInt(v) || 1); renderLines(); }
function removeLine(i) { lines.splice(i, 1); renderLines(); }

window.setQty = setQty;
window.removeLine = removeLine;

async function search(q) {
    if (!q) { resultsEl.innerHTML = ''; return; }
    const res = await fetch('{{ route('purchases.products') }}?q=' + encodeURIComponent(q));
    const items = await res.json();
    resultsEl.innerHTML = items.map(p => `
        <div class="search-result" onclick='addProduct(${JSON.stringify(p)})'>
            <strong>${p.name}</strong> <small style="color:#94a3b8;">${p.sku}</small>
            <div style="font-size:12px;color:#64748b;">Cost: ${money(p.cost)} · Stock: ${p.stock}</div>
        </div>
    `).join('') || '<div style="padding:12px;color:#94a3b8;">No matches</div>';
}

searchEl.addEventListener('input', e => search(e.target.value));
document.getElementById('discount').addEventListener('input', updateTotals);
document.getElementById('tax').addEventListener('input', updateTotals);

renderLines();
</script>
@endpush