<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::active()->with('category');
        if ($s = $request->get('search')) {
            $q->where(fn($w) => $w->where('name', 'like', "%$s%")
                                 ->orWhere('sku', 'like', "%$s%")
                                 ->orWhere('barcode', 'like', "%$s%"));
        }
        if ($cat = $request->get('category_id')) {
            $q->where('category_id', $cat);
        }
        $products = $q->orderBy('name')->paginate(30)->withQueryString();
        $categories = Category::orderBy('name')->get();
        return view('barcodes.index', compact('products', 'categories'));
    }

    public function print(Request $request)
    {
        $ids = $request->get('ids', []);
        if (empty($ids)) {
            return back()->with('success', 'Select at least one product.');
        }

        $products = Product::whereIn('id', $ids)->get();

        // Quantities per product (from form: qty[ID] = number)
        $qty = $request->get('qty', []);
        $labels = [];

        foreach ($products as $p) {
            $count = (int) ($qty[$p->id] ?? 1);
            for ($i = 0; $i < $count; $i++) {
                $labels[] = $p;
            }
        }

        return view('barcodes.print', compact('labels'));
    }

    public function single(Product $product)
    {
        // Auto-generate barcode if none
        if (!$product->barcode) {
            $product->barcode = 'P' . str_pad($product->id, 10, '0', STR_PAD_LEFT);
            $product->save();
        }
        return view('barcodes.single', compact('product'));
    }
}