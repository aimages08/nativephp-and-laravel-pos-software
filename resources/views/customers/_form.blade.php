@php $c = $customer ?? null; @endphp

<div class="form-group">
    <label>Name *</label>
    <input name="name" value="{{ old('name', $c->name ?? '') }}" required>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="form-group">
        <label>Phone</label>
        <input name="phone" value="{{ old('phone', $c->phone ?? '') }}">
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $c->email ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label>Address</label>
    <textarea name="address" rows="2">{{ old('address', $c->address ?? '') }}</textarea>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="form-group">
        <label>Opening Balance</label>
        <input type="number" step="0.01" name="balance" value="{{ old('balance', $c->balance ?? 0) }}">
    </div>
    <div class="form-group">
        <label style="display:inline-flex;align-items:center;margin-top:26px;">
            <input type="checkbox" name="is_active" value="1" style="width:auto;margin-right:8px;"
                   {{ old('is_active', $c->is_active ?? true) ? 'checked' : '' }}>
            Active
        </label>
    </div>
</div>