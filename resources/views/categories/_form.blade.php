@php $c = $category ?? null; @endphp

<div class="form-group">
    <label>Name *</label>
    <input name="name" value="{{ old('name', $c->name ?? '') }}" required>
</div>

<div class="form-group">
    <label>Description</label>
    <textarea name="description" rows="3">{{ old('description', $c->description ?? '') }}</textarea>
</div>

<div class="form-group">
    <label style="display:inline-flex;align-items:center;">
        <input type="checkbox" name="is_active" value="1" style="width:auto;margin-right:8px;"
               {{ old('is_active', $c->is_active ?? true) ? 'checked' : '' }}>
        Active
    </label>
</div>