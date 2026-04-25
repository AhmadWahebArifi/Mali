<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Notification;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['account', 'category']);
        
        // Filter by date range
        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'current_month':
                    $query->whereMonth('date', now()->month)
                          ->whereYear('date', now()->year);
                    break;
                case 'last_30_days':
                    $query->where('date', '>=', now()->subDays(30));
                    break;
                case 'last_quarter':
                    $query->where('date', '>=', now()->subMonths(3));
                    break;
            }
        }
        
        // Filter by account
        if ($request->filled('account_id') && $request->account_id != 'all') {
            $query->where('account_id', $request->account_id);
        }
        
        // Filter by category
        if ($request->filled('category_id') && $request->category_id != 'all') {
            $query->where('category_id', $request->category_id);
        }
        
        $transactions = $query->orderBy('date', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->paginate(25);
            
        $accounts = Account::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $totalTransactions = $query->count();
        
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

        // Update account balance
        $account = Account::find($validated['account_id']);
        if ($account) {
            if ($validated['type'] === 'income') {
                $account->balance += $validated['amount'];
            } else {
                $account->balance -= $validated['amount'];
            }
            $account->save();
        }

        // Create notification for the transaction
        $title = ucfirst($validated['type']) . ' Transaction Added';
        $message = ucfirst($validated['type']) . ' of $' . number_format($validated['amount'], 2) . ' has been added to ' . $account->name . ' account';
        $type = $validated['type'] === 'income' ? 'success' : 'info';
        
        Notification::createForUser(
            $user->id,
            $title,
            $message,
            $type,
            [
                'transaction_id' => $transaction->id,
                'account_id' => $account->id,
                'amount' => $validated['amount'],
                'type' => $validated['type']
            ]
        );

        // Check for low balance warning
        if ($account->balance < 100 && $validated['type'] === 'expense') {
            Notification::createForUser(
                $user->id,
                'Account Balance Low',
                'Your ' . $account->name . ' account balance is below $100',
                'warning',
                [
                    'account_id' => $account->id,
                    'balance' => $account->balance
                ]
            );
        }

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
        $transaction = Transaction::findOrFail($id);
        
        // Update account balance (reverse the transaction)
        $account = Account::find($transaction->account_id);
        if ($account) {
            if ($transaction->type === 'income') {
                $account->balance -= $transaction->amount;
            } else {
                $account->balance += $transaction->amount;
            }
            $account->save();
        }
        
        $transaction->delete();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully!'
            ]);
        }
        
        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully!');
    }

    /**
     * Export transactions to CSV
     */
    public function exportCsv(Request $request)
    {
        $query = Transaction::with(['account', 'category']);
        
        // Apply same filters as index method
        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'current_month':
                    $query->whereMonth('date', now()->month)
                          ->whereYear('date', now()->year);
                    break;
                case 'last_30_days':
                    $query->where('date', '>=', now()->subDays(30));
                    break;
                case 'last_quarter':
                    $query->where('date', '>=', now()->subMonths(3));
                    break;
            }
        }
        
        if ($request->filled('account_id') && $request->account_id != 'all') {
            $query->where('account_id', $request->account_id);
        }
        
        if ($request->filled('category_id') && $request->category_id != 'all') {
            $query->where('category_id', $request->category_id);
        }
        
        $transactions = $query->orderBy('date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($file, ['Date', 'Description', 'Category', 'Type', 'Account', 'Amount']);
            
            // CSV data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->date->format('Y-m-d'),
                    $transaction->description,
                    $transaction->category->name,
                    ucfirst($transaction->type),
                    $transaction->account->name,
                    $transaction->amount
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
