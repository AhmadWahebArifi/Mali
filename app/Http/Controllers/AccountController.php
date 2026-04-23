<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Account::orderBy('balance', 'desc')->get();
        $totalBalance = Account::sum('balance');
        
        return view('accounts.index', compact('accounts', 'totalBalance'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:accounts,name',
            'balance' => 'required|numeric|min:0',
        ]);

        $account = Account::create([
            'name' => $validated['name'],
            'balance' => $validated['balance'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account created successfully!',
                'account' => $account
            ]);
        }

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $account = Account::findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'account' => $account,
            ]);
        }

        return redirect()->route('accounts.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $account = Account::findOrFail($id);

        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $account = Account::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:accounts,name,' . $account->id,
            'balance' => 'required|numeric|min:0',
        ]);

        $account->update([
            'name' => $validated['name'],
            'balance' => $validated['balance'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account updated successfully!',
                'account' => $account->fresh(),
            ]);
        }

        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $account = Account::findOrFail($id);
        
        // Check if account has transactions
        $transactionCount = $account->transactions()->count();
        if ($transactionCount > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete account with {$transactionCount} transactions. Please delete or reassign transactions first."
                ]);
            }
            return redirect()->route('accounts.index')
                ->with('error', "Cannot delete account with {$transactionCount} transactions. Please delete or reassign transactions first.");
        }
        
        $account->delete();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully!'
            ]);
        }
        
        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully!');
    }
}
