<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Services\NotificationService;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaction::with(['account', 'category']);
        
        // User-based filtering - non-admins can only see their own transactions
        $isAdmin = Auth::user()->email === 'admin@mali.com';
        if (!$isAdmin) {
            $query->where('created_by', Auth::id());
        }
        
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
        
        // Filter by account (only show user's accounts for non-admins)
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
        
        // Show all accounts for filtering, but transactions remain user-specific
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
        // Show all accounts to all users for transaction creation
        // Users can transact on any account, but transactions remain user-specific
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

        // Get the authenticated user
        $user = Auth::user();

        $transaction = Transaction::create([
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'category_id' => $validated['category_id'],
            'account_id' => $validated['account_id'],
            'date' => $validated['date'],
            'description' => $validated['description'] ?? '',
            'created_by' => $user->id,
        ]);

        // Log the transaction creation
        LoggingService::logTransactionCreate($transaction);

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

        // Create notifications using NotificationService
        $notificationService = new NotificationService();
        
        // Create transaction alert (respects user preferences)
        $notificationService->createTransactionAlert($transaction);
        
        // Check for low balance warning (respects user preferences)
        $notificationService->createLowBalanceAlert($account);

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
        $transaction = Transaction::findOrFail($id);
        
        // User-based authorization - non-admins can only edit their own transactions
        $isAdmin = Auth::user()->email === 'admin@mali.com';
        if (!$isAdmin && $transaction->created_by !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You can only edit your own transactions.'
                ], 403);
            }
            return redirect()->route('transactions.index')
                ->with('error', 'Unauthorized. You can only edit your own transactions.');
        }
        
        // Store old values for audit logging
        $oldValues = [
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'category_id' => $transaction->category_id,
            'account_id' => $transaction->account_id,
            'date' => $transaction->date,
            'description' => $transaction->description,
        ];
        
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'category_id' => 'required|exists:categories,id',
            'account_id' => 'required|exists:accounts,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);
        
        $transaction->update($validated);
        
        // Store new values for audit logging
        $newValues = [
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'category_id' => $transaction->category_id,
            'account_id' => $transaction->account_id,
            'date' => $transaction->date,
            'description' => $transaction->description,
        ];
        
        // Log the transaction update
        LoggingService::logTransactionUpdate($transaction, $oldValues, $newValues);
        
        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        
        // User-based authorization - non-admins can only delete their own transactions
        $isAdmin = Auth::user()->email === 'admin@mali.com';
        if (!$isAdmin && $transaction->created_by !== Auth::id()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You can only delete your own transactions.'
                ], 403);
            }
            return redirect()->route('transactions.index')
                ->with('error', 'Unauthorized. You can only delete your own transactions.');
        }
        
        // Log the transaction deletion before it's deleted
        LoggingService::logTransactionDelete($transaction);
        
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
        
        // User-based filtering - non-admins can only export their own transactions
        $isAdmin = Auth::user()->email === 'admin@mali.com';
        if (!$isAdmin) {
            $query->where('created_by', Auth::id());
        }
        
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
