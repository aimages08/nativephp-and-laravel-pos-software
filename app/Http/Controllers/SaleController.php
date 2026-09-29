<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $q = Sale::with('customer', 'user')->latest();

        if ($s = $request->get('search')) {
            $q->where(fn($w) => $w->where('invoice_no', 'like', "%$s%")
                                 ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%$s%")));
        }

        if ($from = $request->get('from')) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $q->whereDate('created_at', '<=', $to);
        }

        $sales = $q->paginate(25)->withQueryString();
        $total = (clone $q)->sum('total');

        return view('sales.index', compact('sales', 'total'));
    }

    public function show(Sale $sale)
    {
        $sale->load('items.product', 'customer', 'user');
        return view('sales.show', compact('sale'));
    }

    public function refund(Request $request, Sale $sale)
    {
        if ($sale->status === 'refunded') {
            return back()->with('success', 'Already refunded.');
        }

        DB::transaction(function () use ($sale) {
            foreach ($sale->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'type' => 'refund',
                        'reference' => $sale->invoice_no,
                        'user_id' => auth()->id() ?? 1,
                    ]);
                }
            }
            $sale->update(['status' => 'refunded']);
        });

        return back()->with('success', 'Sale refunded. Stock restored.');
    }

    public function destroy(Sale $sale)
    {
        if ($sale->status !== 'refunded') {
            return back()->with('success', 'Refund first before deleting.');
        }
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Sale deleted.');
    }
}