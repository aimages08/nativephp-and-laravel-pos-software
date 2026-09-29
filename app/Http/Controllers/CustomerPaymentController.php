<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerPaymentController extends Controller
{
    public function index(Request $request)
    {
        $q = CustomerPayment::with('customer', 'user')->latest();
        if ($cid = $request->get('customer_id')) $q->where('customer_id', $cid);
        $payments = $q->paginate(25)->withQueryString();
        $total = (clone $q)->sum('amount');
        $customers = Customer::orderBy('name')->get();
        return view('payments.index', compact('payments', 'total', 'customers'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $selected = $request->get('customer_id');
        return view('payments.create', compact('customers', 'selected'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,bank,card,other',
            'reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data) {
            $data['user_id'] = auth()->id() ?? 1;
            CustomerPayment::create($data);
            $customer = Customer::find($data['customer_id']);
            $customer->decrement('balance', $data['amount']);
            return redirect()->route('customers.show', $customer)->with('success', 'Payment recorded.');
        });
    }

    public function destroy(CustomerPayment $payment)
    {
        DB::transaction(function () use ($payment) {
            $payment->customer->increment('balance', $payment->amount);
            $payment->delete();
        });
        return back()->with('success', 'Payment reversed.');
    }
}