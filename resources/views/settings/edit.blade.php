@extends('layouts.app')
@section('title', 'Settings')

@section('content')
<div style="max-width:800px;">

    
   

    {{-- Store settings --}}
    <div class="card" style="margin-bottom:16px;">
        <h2 style="margin-top:0;">⚙️ Store Settings</h2>

        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PUT')

            @foreach([
                'store_name' => 'Store Name',
                'store_address' => 'Store Address',
                'store_phone' => 'Store Phone',
                'currency_symbol' => 'Currency Symbol',
                'currency_code' => 'Currency Code',
                'tax_rate' => 'Tax Rate (%)',
                'low_stock_threshold' => 'Low Stock Threshold',
                'receipt_footer' => 'Receipt Footer',
            ] as $key => $label)
                <div class="form-group">
                    <label>{{ $label }}</label>
                    <input name="{{ $key }}" value="{{ old($key, $settings[$key] ?? '') }}">
                </div>
            @endforeach

            <div class="form-group">
                <label style="display:inline-flex;align-items:center;">
                    <input type="checkbox" name="tax_included" value="1" style="width:auto;margin-right:8px;"
                           {{ old('tax_included', $settings['tax_included'] ?? false) ? 'checked' : '' }}>
                    Tax included in prices
                </label>
            </div>

            <button class="btn btn-primary">Save Settings</button>
        </form>
    </div>

    {{-- Change login --}}
    <div class="card">
        <h2 style="margin-top:0;">🔐 Change Login</h2>
        <p style="color:#64748b;font-size:14px;margin-bottom:16px;">Update your email or password. Current password is required to save.</p>

        <form method="POST" action="{{ route('settings.login') }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
            </div>

            <div class="form-group">
                <label>Current Password *</label>
                <input type="password" name="current_password" required>
                @error('current_password') <div style="color:#ef4444;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label>New Password <small style="color:#94a3b8;">(leave blank to keep)</small></label>
                    <input type="password" name="new_password">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="new_password_confirmation">
                </div>
            </div>

            <button class="btn btn-primary">Update Login</button>
        </form>
    </div>

</div>


        <script>
            document.querySelectorAll('input[type="password"]').forEach(function(input) {
                const wrapper = document.createElement('div');
                wrapper.style.cssText = 'position:relative;';
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = '👁️';
                btn.style.cssText = 'position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:16px;';
                btn.onclick = function() {
                    input.type = input.type === 'password' ? 'text' : 'password';
                    btn.textContent = input.type === 'password' ? '👁️' : '🙈';
                };
                wrapper.appendChild(btn);
            });
        </script>

    </div>
@endsection


