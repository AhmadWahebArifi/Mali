<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\NotificationService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Log the account creation
        LoggingService::logAccountCreate($account);

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
        
        // Check if user is admin for balance changes
        $isAdmin = Auth::user()->email === 'admin@mali.com';
        
        // Store old values for audit logging
        $oldValues = [
            'name' => $account->name,
            'balance' => $account->balance,
        ];

        // Different validation rules based on user role
        $validationRules = [
            'name' => 'required|string|max:255|unique:accounts,name,' . $account->id,
        ];
        
        // Only admin can change balance
        if ($isAdmin) {
            $validationRules['balance'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($validationRules);

        // Update account - only admin can change balance
        $updateData = [
            'name' => $validated['name'],
        ];
        
        if ($isAdmin) {
            $updateData['balance'] = $validated['balance'];
        }

        $account->update($updateData);

        // Store new values for audit logging
        $newValues = [
            'name' => $account->name,
            'balance' => $account->balance,
        ];

        // Log the account update
        LoggingService::logAccountUpdate($account, $oldValues, $newValues);

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
        
        // Log the account deletion before deletion
        LoggingService::logAccountDelete($account);
        
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
