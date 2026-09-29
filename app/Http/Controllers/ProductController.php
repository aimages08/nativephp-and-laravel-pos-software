<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::with('category')->latest();

        if ($s = $request->get('search')) {
            $q->where(fn ($w) => $w->where('name', 'like', "%$s%")
                ->orWhere('sku', 'like', "%$s%")
                ->orWhere('barcode', 'like', "%$s%"));
        }

        $products = $q->paginate(20)->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['expiry_date'] = $request->filled('expiry_date') ? $request->input('expiry_date') : null;
        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product->id);
        $data['is_active'] = $request->boolean('is_active');
        $data['expiry_date'] = $request->filled('expiry_date') ? $request->input('expiry_date') : null;
        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Product deleted.');
    }

    protected function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku'.($id ? ",$id" : ''),
            'barcode' => 'nullable|string|max:100|unique:products,barcode'.($id ? ",$id" : ''),
            'category_id' => 'nullable|exists:categories,id',
            'cost' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
            'expiry_date' => 'nullable|date',
            'barcode_type' => 'nullable|in:existing,generated,none',
            'plu' => 'nullable|string|max:20|unique:products,plu' . ($id ? ",$id" : ''),
        ]);
    }

    public function generateBarcode()
    {
        return response()->json(['barcode' => Product::generateBarcode()]);
    }

    public function bulkGenerateBarcodes()
    {
        $products = Product::whereNull('barcode')->orWhere('barcode', '')->get();
        $count = 0;
        foreach ($products as $p) {
            $p->update([
                'barcode' => Product::generateBarcode(),
                'barcode_type' => 'generated',
            ]);
            $count++;
        }

        return back()->with('success', "$count barcodes generated.");
    }
}
