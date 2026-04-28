<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Models\Budget;
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
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
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

        // Check budget limits for all transactions (income and expense)
        $this->checkAndUpdateBudgets($user->id, $validated['category_id'], $validated['amount'], $validated['date'], $validated['type']);

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
        
        // Update budgets for old transaction values (remove old impact)
        $this->checkAndUpdateBudgets($transaction->user_id, $oldValues['category_id'], $oldValues['amount'], $oldValues['date'], $oldValues['type']);
        
        // Update budgets for new transaction values (add new impact)
        $this->checkAndUpdateBudgets($transaction->user_id, $newValues['category_id'], $newValues['amount'], $newValues['date'], $newValues['type']);
        
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
        
        // Update budgets before deleting the transaction
        $this->checkAndUpdateBudgets($transaction->user_id, $transaction->category_id, $transaction->amount, $transaction->date, $transaction->type);
        
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
        // Check if user is admin
        if (Auth::user()->email !== 'admin@mali.com') {
            abort(403, 'Unauthorized. Only admin users can export transactions.');
        }

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

    /**
     * Import transactions from CSV file
     */
    public function import(Request $request)
    {
        // Debug: Log the request
        \Log::info('Import request received', [
            'user' => Auth::user() ? Auth::user()->email : 'not authenticated',
            'has_file' => $request->hasFile('csv_file'),
            'all_request_data' => $request->all()
        ]);

        // Check if user is admin
        if (Auth::user()->email !== 'admin@mali.com') {
            \Log::warning('Non-admin user attempted import', ['user' => Auth::user()->email]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admin users can import transactions.'
            ], 403);
        }

        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->errors()['csv_file'] ?? [])
            ], 422);
        }

        try {
            $file = $request->file('csv_file');
            if (!$file) {
                \Log::error('No file uploaded');
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
            }

            \Log::info('File details', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'path' => $file->getPathname()
            ]);

            $filePath = $file->getPathname();
            
            // Read file content to check for BOM and handle encoding
            $fileContent = file_get_contents($filePath);
            if ($fileContent === false) {
                \Log::error('Unable to read file content');
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to read the uploaded file'
                ], 400);
            }

            // Remove BOM if present
            if (substr($fileContent, 0, 3) === "\xEF\xBB\xBF") {
                $fileContent = substr($fileContent, 3);
                \Log::info('BOM detected and removed');
            }

            // Write back to temp file without BOM
            $tempPath = tempnam(sys_get_temp_dir(), 'csv_import_');
            file_put_contents($tempPath, $fileContent);
            
            // Open and read the CSV file
            $handle = fopen($tempPath, 'r');
            if (!$handle) {
                \Log::error('Unable to open file', ['path' => $tempPath]);
                unlink($tempPath);
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to read the uploaded file'
                ], 400);
            }

            \Log::info('File opened successfully for parsing');

            // Read header row
            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty or invalid format'
                ], 400);
            }

            // Normalize header keys (lowercase, trim spaces)
            $header = array_map(function($value) {
                return strtolower(trim($value));
            }, $header);

            // Required columns
            $requiredColumns = ['date', 'description', 'amount', 'type', 'category'];
            $missingColumns = array_diff($requiredColumns, $header);
            
            if (!empty($missingColumns)) {
                fclose($handle);
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required columns: ' . implode(', ', $missingColumns)
                ], 400);
            }

            // Get user info
            $user = Auth::user();
            $isAdmin = $user->email === 'admin@mali.com';
            
            // Get available categories and accounts for the user
            if ($isAdmin) {
                $categories = Category::all()->keyBy('name');
                $accounts = Account::all()->keyBy('name');
            } else {
                $categories = Category::all()->keyBy('name');
                $accounts = Account::where('user_id', $user->id)->get()->keyBy('name');
            }

            $importedCount = 0;
            $errors = [];
            $rowNumber = 2; // Start after header

            // Process each row
            while (($row = fgetcsv($handle)) !== false) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        $rowNumber++;
                        continue;
                    }

                    \Log::info("Processing row {$rowNumber}", [
                        'row_data' => $row,
                        'row_count' => count($row),
                        'header_count' => count($header)
                    ]);

                    // Check if row has more columns than header (trailing data)
                    if (count($row) > count($header)) {
                        $extraColumns = array_slice($row, count($header));
                        \Log::warning("Row {$rowNumber} has extra columns", [
                            'extra_columns' => $extraColumns,
                            'expected_columns' => count($header),
                            'actual_columns' => count($row)
                        ]);
                        // Trim the row to match header count
                        $row = array_slice($row, 0, count($header));
                    }

                    // Map row data to column names
                    $rowData = [];
                    foreach ($header as $index => $columnName) {
                        $rowData[$columnName] = isset($row[$index]) ? trim($row[$index]) : '';
                    }

                    \Log::info("Mapped row data for row {$rowNumber}", ['mapped_data' => $rowData]);

                    // Validate required fields
                    if (empty($rowData['date']) || empty($rowData['description']) || 
                        empty($rowData['amount']) || empty($rowData['type']) || empty($rowData['category'])) {
                        $errors[] = "Row {$rowNumber}: Missing required data";
                        \Log::warning("Row {$rowNumber} missing required data", ['row_data' => $rowData]);
                        $rowNumber++;
                        continue;
                    }

                    // Validate date - try multiple formats
                    $date = null;
                    $dateFormats = ['Y-m-d', 'm/d/Y', 'd/m/Y', 'm-d-Y', 'd-m-Y'];
                    
                    foreach ($dateFormats as $format) {
                        try {
                            $date = \Carbon\Carbon::createFromFormat($format, $rowData['date']);
                            if ($date) break;
                        } catch (\Exception $e) {
                            // Continue to next format
                        }
                    }
                    
                    if (!$date) {
                        $errors[] = "Row {$rowNumber}: Invalid date format (use YYYY-MM-DD, MM/DD/YYYY, or DD/MM/YYYY)";
                        $rowNumber++;
                        continue;
                    }

                    // Validate amount
                    $amount = floatval($rowData['amount']);
                    if ($amount <= 0) {
                        $errors[] = "Row {$rowNumber}: Amount must be greater than 0";
                        $rowNumber++;
                        continue;
                    }

                    // Validate type
                    $type = strtolower($rowData['type']);
                    if (!in_array($type, ['income', 'expense'])) {
                        $errors[] = "Row {$rowNumber}: Type must be 'income' or 'expense'";
                        $rowNumber++;
                        continue;
                    }

                    // Find category
                    $categoryName = trim($rowData['category']);
                    $category = $categories->get($categoryName);
                    
                    if (!$category) {
                        // Create new category if it doesn't exist
                        $category = Category::create([
                            'name' => $categoryName,
                            'type' => $type,
                            'icon' => $type === 'income' ? 'trending_up' : 'trending_down',
                            'color' => $type === 'income' ? '#10b981' : '#ef4444',
                        ]);
                        $categories->put($categoryName, $category);
                    }

                    // Find account (optional)
                    $account = null;
                    if (!empty($rowData['account'])) {
                        $accountName = trim($rowData['account']);
                        $account = $accounts->get($accountName);
                        
                        if (!$account && !$isAdmin) {
                            // Create new account for non-admin users if it doesn't exist
                            $account = Account::create([
                                'name' => $accountName,
                                'balance' => 0,
                                'user_id' => $user->id,
                            ]);
                            $accounts->put($accountName, $account);
                        }
                    }

                    // If no account specified and user has accounts, use the first one
                    if (!$account && !$isAdmin) {
                        $account = $accounts->first();
                    }

                    // If still no account, skip this row
                    if (!$account) {
                        $errors[] = "Row {$rowNumber}: No valid account found and no account specified";
                        $rowNumber++;
                        continue;
                    }

                    // Create transaction
                    Transaction::create([
                        'date' => $date,
                        'description' => $rowData['description'],
                        'amount' => $amount,
                        'type' => $type,
                        'category_id' => $category->id,
                        'account_id' => $account->id,
                        'created_by' => $user->id,
                    ]);

                    // Update account balance
                    if ($type === 'income') {
                        $account->balance += $amount;
                    } else {
                        $account->balance -= $amount;
                    }
                    $account->save();

                    $importedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
                
                $rowNumber++;
            }

            fclose($handle);
            unlink($tempPath);

            // Log the import activity
            LoggingService::logTransactionImport($user->id, $importedCount, count($errors));

            // Return response
            if ($importedCount > 0) {
                $message = "Successfully imported {$importedCount} transactions";
                if (!empty($errors)) {
                    $message .= ". " . count($errors) . " rows had errors and were skipped.";
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'imported_count' => $importedCount,
                    'errors' => array_slice($errors, 0, 10) // Return first 10 errors
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No transactions were imported. Please check your CSV format.',
                    'errors' => array_slice($errors, 0, 10)
                ], 400);
            }

        } catch (\Exception $e) {
            \Log::error('CSV Import Error: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Clean up temp file if it exists
            if (isset($tempPath) && file_exists($tempPath)) {
                unlink($tempPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during import: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check and update budgets for expense transactions
     */
    private function checkAndUpdateBudgets($userId, $categoryId, $amount, $transactionDate, $transactionType = 'expense')
    {
        // Get active budgets for this user
        $budgets = Budget::where('user_id', $userId)
            ->where('is_active', true)
            ->where(function ($query) use ($categoryId) {
                $query->whereNull('category_id')
                      ->orWhere('category_id', $categoryId);
            })
            ->get();

        foreach ($budgets as $budget) {
            // Update spent amount for this budget (now tracks both income and expenses)
            $budget->updateSpentAmount();

            // Check budget status and create notifications
            if ($budget->is_over_budget) {
                $this->createBudgetNotification($userId, $budget, 'exceeded');
            } elseif ($budget->is_near_limit) {
                $this->createBudgetNotification($userId, $budget, 'near_limit');
            }
            
            // For income transactions, create positive balance notification
            if ($transactionType === 'income' && $budget->current_balance > 0) {
                $this->createBudgetNotification($userId, $budget, 'positive_balance');
            }
        }
    }

    /**
     * Create budget-related notifications
     */
    private function createBudgetNotification($userId, $budget, $type)
    {
        $title = '';
        $message = '';
        $icon = '';

        switch ($type) {
            case 'exceeded':
                $title = 'Budget Exceeded';
                $message = "Your budget '{$budget->name}' has been exceeded. Spent: {$budget->spent}, Budget: {$budget->amount}";
                $icon = 'warning';
                break;
            case 'near_limit':
                $title = 'Budget Near Limit';
                $message = "Your budget '{$budget->name}' is near its limit. Used: " . round($budget->percentage_used, 1) . "%";
                $icon = 'info';
                break;
        }

        // Create notification using NotificationService
        $notificationService = new NotificationService();
        $notificationService->createNotification([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'type' => 'budget'
        ]);
    }
}
