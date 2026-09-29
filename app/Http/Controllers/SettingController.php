<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $keys = [
            'store_name', 'store_address', 'store_phone',
            'currency_symbol', 'currency_code',
            'tax_rate', 'tax_included', 'low_stock_threshold',
            'receipt_footer',
        ];

        $settings = [];
        foreach ($keys as $k) {
            $settings[$k] = Setting::get($k, '');
        }

        return view('settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'nullable|string',
            'store_phone' => 'nullable|string|max:50',
            'currency_symbol' => 'required|string|max:10',
            'currency_code' => 'required|string|max:10',
            'tax_rate' => 'required|numeric|min:0',
            'tax_included' => 'nullable|boolean',
            'low_stock_threshold' => 'required|integer|min:0',
            'receipt_footer' => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        return back()->with('success', 'Settings saved.');
    }

    public function updateLogin(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
            'current_password' => 'required|string',
            'new_password' => 'nullable|string|min:4|confirmed',
        ]);

        if (! \Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->email = $data['email'];
        if (! empty($data['new_password'])) {
            $user->password = $data['new_password'];   // ← hashed cast handles this
        }
        $user->save();

        return back()->with('success', 'Login updated.');
    }
}
