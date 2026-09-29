<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = Customer::query();
        if ($s = $request->get('search')) {
            $q->where(fn($w) => $w->where('name', 'like', "%$s%")
                                 ->orWhere('phone', 'like', "%$s%")
                                 ->orWhere('email', 'like', "%$s%"));
        }
        $customers = $q->orderBy('name')->paginate(20)->withQueryString();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'balance' => 'nullable|numeric',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        Customer::create($data);
        return redirect()->route('customers.index')->with('success', 'Customer created.');
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'sales' => fn($q) => $q->latest()->limit(50),
            'payments' => fn($q) => $q->latest()->limit(50),
        ]);
        return view('customers.show', compact('customer'));
    }

    public function statement(Customer $customer)
    {
        $customer->load(['sales', 'payments']);
        return view('customers.statement', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'balance' => 'nullable|numeric',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $customer->update($data);
        return redirect()->route('customers.index')->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->sales()->exists() || $customer->balance > 0) {
            return back()->withErrors(['customer' => 'Cannot delete: customer has sales or outstanding balance.']);
        }
        $customer->delete();
        return back()->with('success', 'Customer moved to trash.');
    }
}