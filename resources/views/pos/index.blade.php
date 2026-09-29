@extends('layouts.app')
@section('title', 'POS')

@push('styles')
<style>
    .pos { display: grid; grid-template-columns: 1fr 400px; gap: 16px; height: calc(100vh - 120px); }
    .pos-left { display: flex; flex-direction: column; }
    .search-bar { margin-bottom: 12px; }
    .search-bar input { font-size: 18px; padding: 12px 16px; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; overflow-y: auto; flex: 1; align-content: start; grid-auto-rows: min-content; }
    .product-card { background: #fff; border-radius: 8px; padding: 12px; cursor: pointer; border: 2px solid transparent; transition: all 0.1s; height: fit-content; }
    .product-card:hover { border-color: #3b82f6; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .product-card .name { font-weight: 600; margin-bottom: 4px; font-size: 14px; }
    .product-card .sku { font-size: 11px; color: #94a3b8; margin-bottom: 6px; }
    .product-card .price { font-size: 16px; font-weight: bold; color: #10b981; }
    .product-card .stock { font-size: 11px; color: #64748b; }
    .product-card.out { opacity: 0.5; }
    .cart-panel { background: #fff; border-radius: 8px; display: flex; flex-direction: column; }
    .cart-items { flex: 1; overflow-y: auto; }
    .cart-row { display: grid; grid-template-columns: 1fr 70px 80px 30px; gap: 8px; padding: 10px; border-bottom: 1px solid #f1f5f9; align-items: center; font-size: 14px; }
    .cart-row input { padding: 4px; text-align: center; }
    .cart-totals { padding: 16px; border-top: 2px solid #e2e8f0; }
    .cart-totals .row { display: flex; justify-content: space-between; margin-bottom: 6px; }
    .cart-totals .grand { font-size: 22px; font-weight: bold; color: #111; margin: 12px 0; }
    .kbd-hint { font-size: 11px; color: #94a3b8; text-align: center; padding: 6px; }
</style>
@endpush

@section('content')
<div class="pos">
    <div class="pos-left">
        <div class="search-bar">
            <input id="search" type="text" placeholder="🔍 Scan barcode or search product... (F2)" autofocus>
        </div>
        <div class="product-grid" id="grid"></div>
    </div>

    <div class="cart-panel">
        <div style="padding:12px;border-bottom:1px solid #e2e8f0;">
            <select id="customer_id">
                <option value="">Walk-in Customer</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="cart-items" id="cart"></div>

        <div class="cart-totals">
            <div class="row"><span>Subtotal</span><span id="subtotal">0.00</span></div>
            <div class="row">
                <span>Discount</span>
                <input type="number" id="discount" value="0" style="width:100px;text-align:right;">
            </div>

                       <div class="row">
                <span>Tax <small style="color:#94a3b8;">(%)</small></span>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span id="tax_display" style="font-size:13px;color:#64748b;">0.00</span>
                    <input type="number" id="tax" value="{{ setting('tax_rate', 0) }}" style="width:70px;text-align:right;">
                </div>
            </div>
            
            
                      <div class="row grand"><span>Total</span><span id="total">0.00</span></div>

            <div class="row">
                <span>Payment</span>
                <select id="payment_method" style="width:130px;">
                    <option value="cash">💵 Cash</option>
                    <option value="card">💳 Card</option>
                    <option value="credit">📒 Credit (Khata)</option>
                </select>
            </div>

            <div class="row" id="paid_row">
                <span>Paid</span>
                <input type="number" id="paid" value="0" style="width:120px;text-align:right;font-size:16px;">
            </div>
            <div class="row" id="change_row"><span>Change</span><span id="change">0.00</span></div>

            <button id="checkout" class="btn btn-success" style="width:100%;padding:14px;font-size:16px;margin-top:12px;">💳 Checkout (F9)</button>
            <div class="kbd-hint">F2: search · F9: checkout · Esc: clear cart</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const currency = @json(setting('currency_symbol', 'Rs'));
let cart = [];

const searchEl = document.getElementById('search');
const gridEl = document.getElementById('grid');
const cartEl = document.getElementById('cart');

function money(n) { return currency + ' ' + parseFloat(n || 0).toFixed(2); }

function renderCart() {
    cartEl.innerHTML = cart.map((it, i) => `
        <div class="cart-row">
            <div>
                <div style="font-weight:600;">${it.name}</div>
                <div style="font-size:11px;color:#94a3b8;">${money(it.price)}</div>
            </div>
            <input type="number" min="1" value="${it.qty}" onchange="setQty(${i}, this.value)">
            <div style="text-align:right;font-weight:600;">${money(it.price * it.qty)}</div>
            <button onclick="removeItem(${i})" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:18px;">×</button>
        </div>
    `).join('') || '<div style="padding:40px;text-align:center;color:#94a3b8;">Cart is empty<br><small>Search products to add</small></div>';
    updateTotals();
}

function updateTotals() {
    const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const taxRate = parseFloat(document.getElementById('tax').value) || 0;
    const taxAmount = subtotal * taxRate / 100;
    const total = Math.max(0, subtotal - discount + taxAmount);
    const paid = parseFloat(document.getElementById('paid').value) || 0;

    document.getElementById('subtotal').textContent = money(subtotal);
    document.getElementById('tax_display').textContent = money(taxAmount);
    document.getElementById('total').textContent = money(total);
    document.getElementById('change').textContent = money(Math.max(0, paid - total));


        // Hide paid/change when Credit
    const method = document.getElementById('payment_method').value;
    document.getElementById('paid_row').style.display = method === 'credit' ? 'none' : 'flex';
    document.getElementById('change_row').style.display = method === 'credit' ? 'none' : 'flex';

}

function addItem(p) {
    if (p.stock <= 0) { alert('Out of stock'); return; }
    const existing = cart.find(i => i.id === p.id);
    if (existing) {
        if (existing.qty >= p.stock) { alert('Not enough stock'); return; }
        existing.qty++;
    } else {
        cart.push({ id: p.id, name: p.name, price: parseFloat(p.price), qty: 1, stock: p.stock });
    }
    renderCart();
}

function setQty(i, v) {
    const q = parseInt(v) || 1;
    if (q > cart[i].stock) { alert('Not enough stock (max ' + cart[i].stock + ')'); cart[i].qty = cart[i].stock; }
    else cart[i].qty = Math.max(1, q);
    renderCart();
}

function removeItem(i) { cart.splice(i, 1); renderCart(); }
window.removeItem = removeItem;
window.setQty = setQty;

async function doSearch(q) {
    if (!q) { gridEl.innerHTML = ''; return; }
    const res = await fetch('{{ route('pos.search') }}?q=' + encodeURIComponent(q));
    const items = await res.json();
   gridEl.innerHTML = items.map(p => {
    let expiryBadge = '';
    if (p.expiry_date) {
        const exp = new Date(p.expiry_date);
        const today = new Date(); today.setHours(0,0,0,0);
        const diff = Math.floor((exp - today) / (1000 * 60 * 60 * 24));
        if (diff < 0) {
            expiryBadge = `<div style="color:#ef4444;font-weight:bold;font-size:11px;margin-top:4px;">⚠️ EXPIRED ${p.expiry_date}</div>`;
        } else if (diff <= 30) {
            expiryBadge = `<div style="color:#f59e0b;font-size:11px;margin-top:4px;">⏰ Expires in ${diff}d</div>`;
        }
    }
    return `
        <div class="product-card ${p.stock <= 0 ? 'out' : ''}" onclick='addItem(${JSON.stringify(p)})'>
            <div class="name">${p.name}</div>
            <div class="sku">${p.sku}${p.plu ? ' · PLU ' + p.plu : ''}</div>
            <div class="price">${money(p.price)}</div>
            <div class="stock">Stock: ${p.stock}</div>
            ${expiryBadge}
        </div>
    `;
}).join('') || '<div style="padding:20px;color:#94a3b8;">No products found</div>';
}

searchEl.addEventListener('input', e => doSearch(e.target.value));

searchEl.addEventListener('keydown', async e => {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    const q = searchEl.value.trim();
    if (!q) return;

    const res = await fetch('{{ route('pos.search') }}?q=' + encodeURIComponent(q));
    const items = await res.json();

    const exact = items.find(i =>
        (i.barcode && i.barcode === q) ||
        (i.sku && i.sku.toLowerCase() === q.toLowerCase()) ||
        (i.plu && i.plu === q)
    );

    if (exact) {
        addItem(exact);
        searchEl.value = '';
        gridEl.innerHTML = '';
    } else if (items.length === 1) {
        addItem(items[0]);
        searchEl.value = '';
        gridEl.innerHTML = '';
    }
});

document.getElementById('discount').addEventListener('input', updateTotals);
document.getElementById('tax').addEventListener('input', updateTotals);
document.getElementById('paid').addEventListener('input', updateTotals);
document.getElementById('payment_method').addEventListener('change', updateTotals);

document.getElementById('checkout').addEventListener('click', async () => {
    if (cart.length === 0) { alert('Cart is empty'); return; }

    const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const taxRate = parseFloat(document.getElementById('tax').value) || 0;
    const taxAmount = subtotal * taxRate / 100;
    const total = subtotal - discount + taxAmount;
    const paid = parseFloat(document.getElementById('paid').value) || 0;

    const method = document.getElementById('payment_method').value;
    const customerId = document.getElementById('customer_id').value || null;

    if (method !== 'credit' && paid < total) { alert('Paid amount is less than total'); return; }
    if (method === 'credit' && !customerId) { alert('Credit sale needs a customer selected.'); return; }

    const payload = {
        items: cart.map(i => ({ product_id: i.id, quantity: i.qty, price: i.price, discount: 0 })),
        customer_id: customerId,
        discount, tax: taxAmount, paid,
        payment_method: method,
        _token: '{{ csrf_token() }}'
    };

    const res = await fetch('{{ route('pos.checkout') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(payload)
    });

    const data = await res.json();
    if (data.ok) {
        window.location.href = '/pos/receipt/' + data.sale_id;
    } else {
        alert('Error: ' + (data.message || 'checkout failed'));
    }
});

document.addEventListener('keydown', e => {
    if (e.key === 'F2') { e.preventDefault(); searchEl.focus(); }
    if (e.key === 'F9') { e.preventDefault(); document.getElementById('checkout').click(); }
    if (e.key === 'Escape') { cart = []; renderCart(); searchEl.value = ''; gridEl.innerHTML = ''; }
});

renderCart();
</script>
@endpush