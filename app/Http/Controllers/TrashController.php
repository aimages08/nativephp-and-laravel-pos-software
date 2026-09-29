<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;


class TrashController extends Controller
{
    protected $models = [
        'products' => Product::class,
        'categories' => Category::class,
        'customers' => Customer::class,
        'suppliers' => Supplier::class,
        'sales' => Sale::class,
        'purchases' => Purchase::class,
        'payments' => \App\Models\CustomerPayment::class,
    ];

    public function index()
    {
        $data = [];
        foreach ($this->models as $key => $class) {
            $data[$key] = $class::onlyTrashed()->count();
        }
        return view('trash.index', compact('data'));
    }

    public function show(string $type)
    {
        abort_unless(isset($this->models[$type]), 404);
        $class = $this->models[$type];
        $items = $class::onlyTrashed()->latest('deleted_at')->paginate(30);
        return view('trash.show', compact('items', 'type'));
    }

    public function restore(string $type, int $id)
    {
        abort_unless(isset($this->models[$type]), 404);
        $class = $this->models[$type];
        $class::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Restored.');
    }

    public function forceDelete(string $type, int $id)
    {
        abort_unless(isset($this->models[$type]), 404);
        $class = $this->models[$type];
        $class::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Permanently deleted.');
    }

    public function empty(string $type)
    {
        abort_unless(isset($this->models[$type]), 404);
        $this->models[$type]::onlyTrashed()->forceDelete();
        return back()->with('success', 'Trash emptied.');
    }
}