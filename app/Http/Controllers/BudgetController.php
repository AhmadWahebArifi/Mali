<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\User;
use App\Models\Category;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->email !== 'admin@mali.com') {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        })->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $isAdmin = Auth::user()->email === 'admin@mali.com';
        
        if ($isAdmin) {
            $budgets = Budget::with(['user', 'category'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            $budgets = Budget::with(['user', 'category'])
                ->where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        // Always fetch the latest AdminBudgetPool for the view
        $adminPool = $isAdmin ? \App\Models\AdminBudgetPool::getCurrent() : null;

        return view('budgets.index', compact('budgets', 'adminPool'));
    }

    public function create()
    {
        $users = User::where('is_approved', true)
                    ->get();
        $categories = Category::orderBy('name')->get();
        $accounts = Account::orderBy('name')->get();
        
        return view('budgets.create', compact('users', 'categories', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'account_id' => 'nullable|exists:accounts,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'period' => 'required|in:monthly,yearly,custom',
            'start_date' => 'required_if:period,custom|nullable|date',
            'end_date' => 'required_if:period,custom|nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string'
        ]);

        $adminUserId = Auth::id();
        $isAllocatingToOtherUser = (int) $request->user_id !== (int) $adminUserId;

        // Check admin budget pool availability
        $adminBudgetPool = \App\Models\AdminBudgetPool::getCurrent();

        if (!$adminBudgetPool->canAllocate($request->amount)) {
            $errorMessage = 'Insufficient funds in admin budget pool. Available: ' . 
                           \App\Helpers\FormatHelper::currency($adminBudgetPool->available_funds) . 
                           ', Required: ' . \App\Helpers\FormatHelper::currency($request->amount);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => $errorMessage]);
        }

        // Find or create user's target account FIRST (before creating budget)
        $targetAccount = null;
        if ($request->filled('account_id')) {
            $targetAccount = Account::find($request->account_id);
            if ($targetAccount && $targetAccount->user_id === null) {
                $targetAccount->user_id = $request->user_id;
                $targetAccount->save();
            }
            if ($targetAccount && (int) $targetAccount->user_id !== (int) $request->user_id) {
                $targetAccount = null;
            }
        }

        if (!$targetAccount) {
            $targetAccount = Account::firstOrCreate(
                ['user_id' => $request->user_id, 'name' => 'Cash on Hand'],
                ['balance' => 0]
            );
        }

        // Create the budget with the CORRECT account_id
        $budget = Budget::create([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'account_id' => $targetAccount->id, // Use the validated target account ID
            'name' => $request->name,
            'amount' => $request->amount,
            'period' => $request->period,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description
        ]);

        // Allocate from admin budget pool
        $adminBudgetPool->allocateBudget($request->amount, "Budget allocated to {$budget->user->first_name} {$budget->user->last_name}: {$budget->name}");

        if ($isAllocatingToOtherUser) {
            // AdminBudgetPool is the source of funds for user allocations
            // Credit the allocated amount to the user's account so their net worth increases
            $targetAccount->balance += $request->amount;
            $targetAccount->save();
        }

        // Update spent amount (skip for now to avoid timeout)
        // $budget->updateSpentAmount();

        // Check if request expects JSON (from fetch/AJAX)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Budget assigned successfully!',
                'budget' => $budget,
                'admin_pool' => [
                    'total_budget' => $adminBudgetPool->total_budget,
                    'total_allocated' => $adminBudgetPool->total_allocated,
                    'available_funds' => $adminBudgetPool->available_funds
                ]
            ]);
        }

        return redirect()->route('budgets.index')
            ->with('success', 'Budget assigned successfully!');
    }

    public function edit(Budget $budget)
    {
        $users = User::where('is_approved', true)->get();
        $categories = Category::orderBy('name')->get();
        
        return view('budgets.edit', compact('budget', 'users', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'period' => 'required|in:monthly,yearly,custom',
            'start_date' => 'required_if:period,custom|nullable|date',
            'end_date' => 'required_if:period,custom|nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string'
        ]);

        $budget->update([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'amount' => $request->amount,
            'period' => $request->period,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description
        ]);

        // Check if request expects JSON (from fetch/AJAX)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Budget updated successfully!',
                'budget' => $budget
            ]);
        }

        return redirect()->route('budgets.index')
            ->with('success', 'Budget updated successfully!');
    }

    public function destroy(Budget $budget)
    {
        // Return funds to admin budget pool
        $adminBudgetPool = \App\Models\AdminBudgetPool::getCurrent();
        $adminBudgetPool->returnBudget($budget->amount);

        $budget->delete();

        // Check if request expects JSON (from AJAX)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Budget deleted successfully! Funds returned to admin pool.'
            ]);
        }

        return redirect()->route('budgets.index')
            ->with('success', 'Budget deleted successfully! Funds returned to admin pool.');
    }

    public function toggleStatus(Budget $budget)
    {
        $budget->is_active = !$budget->is_active;
        $budget->save();

        return response()->json([
            'success' => true,
            'is_active' => $budget->is_active,
            'message' => $budget->is_active ? 'Budget activated!' : 'Budget deactivated!'
        ]);
    }

    public function updateSpent(Budget $budget)
    {
        // Spent is now calculated dynamically; just return the current values
        return response()->json([
            'success' => true,
            'spent' => $budget->spent,
            'remaining' => $budget->remaining,
            'percentage_used' => $budget->percentage_used,
            'is_over_budget' => $budget->is_over_budget,
            'is_near_limit' => $budget->is_near_limit
        ]);
    }

    /**
     * Show form to add funds to admin budget pool
     */
    public function showAddFunds()
    {
        $adminPool = \App\Models\AdminBudgetPool::getCurrent();
        $accounts = Account::orderBy('name')->get();
        
        return view('budgets.add-funds', compact('adminPool', 'accounts'));
    }

    /**
     * Add funds to admin budget pool
     */
    public function addFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'description' => 'nullable|string'
        ]);

        try {
            // Get the target account
            $account = Account::findOrFail($request->account_id);
            
            // Add funds to the account
            $account->balance += $request->amount;
            $account->save();

            // Add funds to admin budget pool
            $adminPool = \App\Models\AdminBudgetPool::getCurrent();
            $adminPool->addFunds($request->amount);

            // Check if request expects JSON (from fetch/AJAX)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Funds added successfully to admin budget pool!',
                    'admin_pool' => [
                        'total_budget' => $adminPool->total_budget,
                        'total_allocated' => $adminPool->total_allocated,
                        'available_funds' => $adminPool->available_funds
                    ]
                ]);
            }

            return redirect()->route('budgets.index')
                ->with('success', 'Funds added successfully to admin budget pool!');
        } catch (\Exception $e) {
            \Log::error('Failed to add funds to admin budget pool: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'amount' => $request->amount
            ]);
            
            // Check if request expects JSON (from fetch/AJAX)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add funds: ' . $e->getMessage()
                ], 400);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => 'Failed to add funds: ' . $e->getMessage()]);
        }
    }
}
