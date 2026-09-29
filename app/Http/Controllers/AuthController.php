<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        // First-run setup
        if (User::count() === 0) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@shop.com',
                'password' => 'admin@123',
            ]);

            Setting::set('store_name', 'My POS Shop');
            Setting::set('store_address', 'Shop Address');
            Setting::set('store_phone', '+92 300 0000000');
            Setting::set('currency_symbol', 'Rs');
            Setting::set('currency_code', 'PKR');
            Setting::set('tax_rate', '0');
            Setting::set('tax_included', '0');
            Setting::set('low_stock_threshold', '10');
            Setting::set('receipt_footer', 'Thank you! Visit again.');
        }

        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $creds = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($creds, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}

