<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $q = Purchase::with('supplier', 'user')->latest();
        if ($s = $request->get('search')) {
            $q->where('reference_no', 'like', "%$s%");
        }
        $purchases = $q->paginate(25)->withQueryString();
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('purchases.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data) {
            $subtotal = collect($data['items'])->sum(fn($i) => $i['cost'] * $i['quantity']);
            $total = $subtotal - ($data['discount'] ?? 0) + ($data['tax'] ?? 0);

            $purchase = Purchase::create([
                'reference_no' => $this->nextRef(),
                'supplier_id' => $data['supplier_id'] ?? null,
                'user_id' => auth()->id() ?? 1,
                'subtotal' => $subtotal,
                'discount' => $data['discount'] ?? 0,
                'tax' => $data['tax'] ?? 0,
                'total' => $total,
                'paid' => $data['paid'] ?? 0,
                'status' => 'received',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'cost' => $item['cost'],
                    'quantity' => $item['quantity'],
                    'total' => $item['cost'] * $item['quantity'],
                ]);

                $product->increment('stock', $item['quantity']);
                $product->update(['cost' => $item['cost']]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'type' => 'purchase',
                    'reference' => $purchase->reference_no,
                    'user_id' => auth()->id() ?? 1,
                ]);
            }

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase recorded. Stock updated.');
        });
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('items.product', 'supplier', 'user');
        return view('purchases.show', compact('purchase'));
    }

    protected function nextRef(): string
    {
        $prefix = 'PO-' . date('Ymd') . '-';
        $last = Purchase::where('reference_no', 'like', $prefix . '%')->latest('id')->first();
        $n = $last ? ((int) substr($last->reference_no, -4)) + 1 : 1;
        return $prefix . str_pad($n, 4, '0', STR_PAD_LEFT);
    }

    // AJAX search products
    public function searchProducts(Request $request)
    {
        $q = $request->get('q');
        if (strlen($q) < 1) return response()->json([]);
        return Product::active()
            ->where(fn($w) => $w->where('name', 'like', "%$q%")
                               ->orWhere('sku', 'like', "%$q%")
                               ->orWhere('barcode', 'like', "%$q%"))
            ->limit(20)
            ->get(['id', 'name', 'sku', 'barcode', 'cost', 'price', 'stock']);
    }
}