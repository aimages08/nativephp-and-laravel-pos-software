<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $q = StockMovement::with('product', 'user')->latest();

        if ($type = $request->get('type')) {
            $q->where('type', $type);
        }
        if ($s = $request->get('search')) {
            $q->whereHas('product', fn($p) => $p->where('name', 'like', "%$s%"));
        }

        $movements = $q->paginate(30)->withQueryString();
        return view('stock.index', compact('movements'));
    }

    public function create()
    {
        return view('stock.create');
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'product_id' => 'required|exists:products,id',
        'type' => 'required|in:add,remove,damage,loss,count,wastage,expired,supplier_return',
        'quantity' => 'required|integer|min:1',
        'reason' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
    ]);

    return DB::transaction(function () use ($data) {
        $product = Product::find($data['product_id']);

        if ($data['type'] === 'count') {
            $diff = $data['quantity'] - $product->stock;
            $product->update(['stock' => $data['quantity']]);
            $logged = $diff;
        } elseif (in_array($data['type'], ['add'])) {
            $product->increment('stock', $data['quantity']);
            $logged = $data['quantity'];
        } else {
            // remove, damage, loss, wastage, expired, supplier_return → reduce stock
            $product->decrement('stock', $data['quantity']);
            $logged = -$data['quantity'];
        }

        StockMovement::create([
            'product_id' => $product->id,
            'quantity' => $logged,
            'type' => 'adjustment-' . $data['type'],
            'reason' => $data['reason'] ?? null,
            'reference' => 'ADJ-' . date('YmdHis'),
            'notes' => $data['notes'] ?? null,
            'user_id' => auth()->id() ?? 1,
        ]);

        // Log wastage/expired as an expense (cost value)
        if (in_array($data['type'], ['wastage', 'expired', 'damage', 'loss'])) {
            \App\Models\Expense::create([
                'title' => ucfirst($data['type']) . ': ' . $product->name,
                'amount' => $product->cost * $data['quantity'],
                'category' => 'Wastage',
                'expense_date' => now()->format('Y-m-d'),
                'notes' => $data['reason'] ?? $data['notes'] ?? null,
                'user_id' => auth()->id() ?? 1,
            ]);
        }

        return redirect()->route('stock.index')->with('success', 'Stock adjusted.');
    });
}

    // AJAX product search
    public function searchProducts(Request $request)
    {
        $q = $request->get('q');
        if (strlen($q) < 1) return response()->json([]);
        return Product::active()
            ->where(fn($w) => $w->where('name', 'like', "%$q%")
                               ->orWhere('sku', 'like', "%$q%")
                               ->orWhere('barcode', 'like', "%$q%"))
            ->limit(20)
            ->get(['id', 'name', 'sku', 'stock']);
    }
}