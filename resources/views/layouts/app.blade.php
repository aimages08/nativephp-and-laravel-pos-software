<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', setting('store_name', 'POS'))</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; margin: 0; }
        .layout { display: flex; height: 100vh; }
        .sidebar { width: 220px; background: #1e293b; color: #e2e8f0; overflow-y: auto; }
        .sidebar a { display: block; padding: 12px 20px; color: #cbd5e1; text-decoration: none; border-left: 3px solid transparent; }
        .sidebar a:hover { background: #334155; color: #fff; }
        .sidebar a.active { background: #334155; color: #fff; border-left-color: #3b82f6; }
        .sidebar .brand { padding: 20px; font-size: 18px; font-weight: bold; color: #fff; border-bottom: 1px solid #334155; }
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { height: 56px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; padding: 0 20px; justify-content: space-between; }
        .content { flex: 1; overflow-y: auto; background: #f1f5f9; padding: 20px; }
        .btn { display: inline-block; padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; text-decoration: none; }
        .btn-primary { background: #3b82f6; color: #fff; }
        .btn-primary:hover { background: #2563eb; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-success { background: #10b981; color: #fff; }
        .card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-weight: 600; }
        input, select, textarea { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; width: 100%; box-sizing: border-box; }
        label { display: block; margin-bottom: 4px; font-size: 13px; font-weight: 500; color: #334155; }
        .form-group { margin-bottom: 16px; }
        .alert-success { background: #d1fae5; color: #065f46; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">{{ setting('store_name', 'POS') }}</div>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Dashboard</a>
            <a href="{{ route('pos.index') }}" class="{{ request()->routeIs('pos.*') ? 'active' : '' }}">🛒 POS</a>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">🏷️ Categories</a>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">📦 Products</a>
            <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">👥 Customers</a>
            <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">🧾 Sales</a>
            <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">🚚 Suppliers</a>
            <a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">📥 Purchases</a>
            <a href="{{ route('expenses.index') }}" class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}">💸 Expenses</a>
            <a href="{{ route('stock.index') }}" class="{{ request()->routeIs('stock.*') ? 'active' : '' }}">📊 Stock</a>
            <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">📈 Reports</a>
            <a href="{{ route('barcodes.index') }}" class="{{ request()->routeIs('barcodes.*') ? 'active' : '' }}">🏷️ Barcodes</a>
            <a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">💰 Payments</a>
            <a href="{{ route('trash.index') }}" class="{{ request()->routeIs('trash.*') ? 'active' : '' }}">🗑️ Trash</a>
            <a href="{{ route('settings.edit') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">⚙️ Settings</a>
            <a href="{{ route('backups.index') }}" class="{{ request()->routeIs('backups.*') ? 'active' : '' }}">💾 Backups</a>
        </aside>
        <div class="main">


            <div class="topbar">
                <div style="display:flex;align-items:center;gap:12px;">
                    <button onclick="history.back()" style="background:none;border:1px solid #e2e8f0;padding:4px 10px;border-radius:6px;color:#64748b;cursor:pointer;font-size:13px;">← Back</button>
                    <span>@yield('title', 'Dashboard')</span>
                </div>
                <div style="display:flex;align-items:center;gap:12px;">
                    <span style="font-size:13px;color:#64748b;">
                        {{ auth()->user()->name ?? '' }} · {{ setting('currency_symbol', 'Rs') }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button style="background:#ef4444;color:#fff;border:none;padding:6px 14px;border-radius:6px;cursor:pointer;font-size:13px;">Logout</button>
                    </form> 
                </div>
            </div>




            <div class="content">
                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert-error">
                        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>