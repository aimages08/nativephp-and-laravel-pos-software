<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $q = Expense::with('user')->latest('expense_date');

        if ($from = $request->get('from')) $q->whereDate('expense_date', '>=', $from);
        if ($to = $request->get('to')) $q->whereDate('expense_date', '<=', $to);
        if ($cat = $request->get('category')) $q->where('category', $cat);

        $expenses = $q->paginate(25)->withQueryString();
        $total = (clone $q)->sum('amount');
        $categories = Expense::query()->whereNotNull('category')->distinct()->pluck('category');

        return view('expenses.index', compact('expenses', 'total', 'categories'));
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        $data['user_id'] = auth()->id() ?? 1;
        Expense::create($data);
        return redirect()->route('expenses.index')->with('success', 'Expense added.');
    }

    public function edit(Expense $expense)
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        $expense->update($data);
        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }
}