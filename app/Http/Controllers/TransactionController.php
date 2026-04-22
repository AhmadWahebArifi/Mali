<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with(['account', 'category'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(25);
            
        $accounts = Account::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $totalTransactions = Transaction::count();
        
        return view('transactions.index', compact('transactions', 'accounts', 'categories', 'totalTransactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accounts = Account::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        return view('transactions.create', compact('accounts', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:accounts,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        // Get the authenticated user (for now, we'll use the first user)
        $user = \App\Models\User::first();

        $transaction = Transaction::create([
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'category_id' => $validated['category_id'],
            'account_id' => $validated['account_id'],
            'date' => $validated['date'],
            'description' => $validated['description'] ?? '',
            'created_by' => $user->id,
        ]);

        // Check if request expects JSON (from fetch/AJAX)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst($validated['type']) . ' transaction added successfully!',
                'transaction' => $transaction
            ]);
        }
        
        // Check if the request came from dashboard
        $referer = $request->header('referer');
        $redirectToDashboard = str_contains($referer, 'dashboard');
        
        if ($redirectToDashboard) {
            return redirect()->route('dashboard')
                ->with('success', ucfirst($validated['type']) . ' transaction added successfully!');
        } else {
            return redirect()->route('transactions.index')
                ->with('success', 'Transaction added successfully!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
