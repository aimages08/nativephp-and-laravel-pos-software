<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Sale::whereDate('created_at', today())
            ->where('status', '!=', 'refunded')
            ->sum('total');
        $todayCount = Sale::whereDate('created_at', today())
            ->where('status', '!=', 'refunded')
            ->count();

        $monthSales = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', '!=', 'refunded')
            ->sum('total');

        $chart = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('D');
            $chart[] = Sale::whereDate('created_at', $date)
                ->where('status', '!=', 'refunded')
                ->sum('total');
        }

        $lowStockCount = Product::lowStock()->active()->count();
        $expiringCount = Product::active()
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->count();
        $expiredCount = Product::active()
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now())
            ->count();

        $recent = Sale::with('customer', 'user')->latest()->take(8)->get();

        $topProducts = DB::table('sale_items')
            ->select('product_name', DB::raw('SUM(sale_items.quantity) as qty'), DB::raw('SUM(sale_items.total) as revenue'))
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereMonth('sales.created_at', now()->month)
            ->whereYear('sales.created_at', now()->year)
            ->where('sales.status', '!=', 'refunded')
            ->groupBy('product_name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        $monthExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $monthCost = DB::table('sale_items')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereMonth('sales.created_at', now()->month)
            ->whereYear('sales.created_at', now()->year)
            ->where('sales.status', '!=', 'refunded')
            ->sum(DB::raw('sale_items.quantity * products.cost'));

        $monthProfit = $monthSales - $monthCost - $monthExpenses;

        return view('dashboard', compact(
            'todaySales', 'todayCount', 'monthSales',
            'chart', 'chartLabels',
            'lowStockCount', 'expiringCount', 'expiredCount',
            'recent', 'topProducts',
            'monthExpenses', 'monthCost', 'monthProfit'
        ));
    }
}