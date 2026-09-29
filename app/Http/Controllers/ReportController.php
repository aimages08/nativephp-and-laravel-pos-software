<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $sales = Sale::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->where('status', '!=', 'refunded');

        $summary = [
            'total' => $sales->clone()->sum('total'),
            'count' => $sales->clone()->count(),
            'avg' => $sales->clone()->avg('total') ?? 0,
            'discount' => $sales->clone()->sum('discount'),
            'tax' => $sales->clone()->sum('tax'),
        ];

        $daily = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->where('status', '!=', 'refunded')
            ->groupBy('date')
            ->orderByDesc('date')
            ->paginate(15)
            ->withQueryString();

        return view('reports.sales', compact('from', 'to', 'summary', 'daily'));
    }

    public function profit(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $items = SaleItem::select(
                'sale_items.product_id',
                'sale_items.product_name',
                DB::raw('SUM(sale_items.quantity) as qty'),
                DB::raw('SUM(sale_items.total) as revenue'),
                DB::raw('SUM(sale_items.quantity * products.cost) as cost')
            )
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereDate('sales.created_at', '>=', $from)
            ->whereDate('sales.created_at', '<=', $to)
            ->where('sales.status', '!=', 'refunded')
            ->groupBy('sale_items.product_id', 'sale_items.product_name')
            ->orderByDesc('revenue')
            ->paginate(25)
            ->withQueryString();

        $revenue = $items->getCollection()->sum('revenue');
        $cost = $items->getCollection()->sum('cost');
        $expenses = Expense::whereDate('expense_date', '>=', $from)
            ->whereDate('expense_date', '<=', $to)
            ->sum('amount');

        // Totals from ALL pages, not just current
        $allItems = SaleItem::select(
                DB::raw('SUM(sale_items.total) as rev'),
                DB::raw('SUM(sale_items.quantity * products.cost) as cst')
            )
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereDate('sales.created_at', '>=', $from)
            ->whereDate('sales.created_at', '<=', $to)
            ->where('sales.status', '!=', 'refunded')
            ->first();

        $totalRevenue = (float) ($allItems->rev ?? 0);
        $totalCost = (float) ($allItems->cst ?? 0);

        $summary = [
            'revenue' => $totalRevenue,
            'cost' => $totalCost,
            'gross_profit' => $totalRevenue - $totalCost,
            'expenses' => $expenses,
            'net_profit' => $totalRevenue - $totalCost - $expenses,
        ];

        return view('reports.profit', compact('from', 'to', 'items', 'summary'));
    }

    public function products(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $items = SaleItem::select(
                'product_id',
                'product_name',
                DB::raw('SUM(sale_items.quantity) as qty'),
                DB::raw('SUM(sale_items.total) as revenue')
            )
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereDate('sales.created_at', '>=', $from)
            ->whereDate('sales.created_at', '<=', $to)
            ->where('sales.status', '!=', 'refunded')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('qty')
            ->paginate(25)
            ->withQueryString();

        return view('reports.products', compact('from', 'to', 'items'));
    }

    public function stock(Request $request)
    {
        $q = Product::with('category');

        if ($filter = $request->get('filter')) {
            if ($filter === 'low') $q->lowStock();
            if ($filter === 'out') $q->where('stock', '<=', 0);
            if ($filter === 'expiring') {
                $q->whereNotNull('expiry_date')
                  ->whereDate('expiry_date', '>=', now())
                  ->whereDate('expiry_date', '<=', now()->addDays(30));
            }
            if ($filter === 'expired') {
                $q->whereNotNull('expiry_date')->whereDate('expiry_date', '<', now());
            }
        }

        $totalValue = (clone $q)->get()->sum(fn($p) => $p->stock * $p->cost);
        $products = $q->orderBy('name')->paginate(30)->withQueryString();

        return view('reports.stock', compact('products', 'totalValue'));
    }
}