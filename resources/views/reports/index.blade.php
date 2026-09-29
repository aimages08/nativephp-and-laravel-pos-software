@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<h2>Reports</h2>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
    <a href="{{ route('reports.sales') }}" class="card" style="text-decoration:none;color:#111;cursor:pointer;">
        <h3 style="margin-top:0;">📈 Sales Report</h3>
        <p style="color:#64748b;">Daily and monthly sales summary</p>
    </a>

    <a href="{{ route('reports.profit') }}" class="card" style="text-decoration:none;color:#111;cursor:pointer;">
        <h3 style="margin-top:0;">💰 Profit Report</h3>
        <p style="color:#64748b;">Revenue, cost, expenses, net profit</p>
    </a>

    <a href="{{ route('reports.products') }}" class="card" style="text-decoration:none;color:#111;cursor:pointer;">
        <h3 style="margin-top:0;">📦 Top Products</h3>
        <p style="color:#64748b;">Best sellers by quantity</p>
    </a>

    <a href="{{ route('reports.stock') }}" class="card" style="text-decoration:none;color:#111;cursor:pointer;">
        <h3 style="margin-top:0;">📊 Stock Report</h3>
        <p style="color:#64748b;">Current stock, low stock, expiring soon</p>
    </a>
</div>
@endsection