<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('pos.index', compact('customers', 'categories'));
    }

        public function search(Request $request)
        {
            $q = $request->get('q');
            if (strlen($q) < 1) return response()->json([]);

            return Product::active()
                ->where(fn($w) => $w->where('name', 'like', "%$q%")
                                ->orWhere('sku', 'like', "%$q%")
                                ->orWhere('barcode', 'like', "%$q%")
                                ->orWhere('plu', 'like', "%$q%"))
                ->limit(20)
                ->get(['id', 'name', 'sku', 'barcode', 'plu', 'price', 'stock', 'expiry_date']);
        }



 public function checkout(Request $request)
{
    $data = $request->validate([
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.discount' => 'nullable|numeric|min:0',
        'customer_id' => 'nullable|exists:customers,id',
        'discount' => 'nullable|numeric|min:0',
        'tax' => 'nullable|numeric|min:0',
        'paid' => 'nullable|numeric|min:0',
        'payment_method' => 'required|in:cash,card,credit,split',
    ]);

    return DB::transaction(function () use ($data) {
        $subtotal = collect($data['items'])->sum(fn($i) => $i['price'] * $i['quantity'] - ($i['discount'] ?? 0));
        $total = $subtotal - ($data['discount'] ?? 0) + ($data['tax'] ?? 0);

        $isCredit = $data['payment_method'] === 'credit';
        $paid = $isCredit ? 0 : ($data['paid'] ?? 0);
        $change = max(0, $paid - $total);

        if ($isCredit && empty($data['customer_id'])) {
            return response()->json(['ok' => false, 'message' => 'Credit sale needs a customer.'], 422);
        }

        $sale = Sale::create([
            'invoice_no' => $this->nextInvoice(),
            'customer_id' => $data['customer_id'] ?? null,
            'user_id' => auth()->id() ?? 1,
            'subtotal' => $subtotal,
            'discount' => $data['discount'] ?? 0,
            'tax' => $data['tax'] ?? 0,
            'total' => $total,
            'paid' => $paid,
            'change_due' => $change,
            'payment_method' => $data['payment_method'],
            'status' => 'completed',
        ]);

        foreach ($data['items'] as $item) {
            $product = Product::find($item['product_id']);
            $lineTotal = $item['price'] * $item['quantity'] - ($item['discount'] ?? 0);

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'discount' => $item['discount'] ?? 0,
                'total' => $lineTotal,
            ]);

            $product->decrement('stock', $item['quantity']);

            StockMovement::create([
                'product_id' => $product->id,
                'quantity' => -$item['quantity'],
                'type' => 'sale',
                'reference' => $sale->invoice_no,
                'user_id' => auth()->id() ?? 1,
            ]);
        }

        if ($isCredit && $sale->customer_id) {
            $sale->customer->increment('balance', $total);
        }

        return response()->json([
            'ok' => true,
            'sale_id' => $sale->id,
            'invoice_no' => $sale->invoice_no,
            'total' => $total,
            'change' => $change,
        ]);
    });
}

    protected function nextInvoice(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
        $last = Sale::where('invoice_no', 'like', $prefix . '%')->latest('id')->first();
        $n = $last ? ((int) substr($last->invoice_no, -4)) + 1 : 1;
        return $prefix . str_pad($n, 4, '0', STR_PAD_LEFT);
    }

    public function receipt(Sale $sale)
    {
        $sale->load('items', 'customer', 'user');
        return view('pos.receipt', compact('sale'));
    }
}