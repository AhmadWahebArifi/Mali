<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // User-based filtering - non-admins can only see their own data
        $isAdmin = Auth::user()->email === 'admin@mali.com';
        
        // Calculate total balance from the logged-in user's accounts
        // (Admin dashboard should not be inflated by other users' account balances.)
        $accountQuery = Account::query()->where('user_id', Auth::id());
        $totalBalance = $accountQuery->sum('balance');
        
        // Get budget data for the logged-in user only
        $budgets = Budget::with('category', 'user')
            ->where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();

                
        // Calculate totals
        $totalBalance = $accountQuery->sum('balance'); // Actual cash on hand
        $totalBudgetAmount = $budgets->sum('amount');
        $totalBudgetBalance = $budgets->sum('current_balance'); // Now uses dynamic accessor
        
        // Total Net Worth should be actual cash (account balances) 
        // Budget allocations are not actual cash, they're spending limits
        $totalNetWorth = $totalBalance;
        
        // Debug logging
        \Log::info('Dashboard Debug', [
            'totalBalance' => $totalBalance,
            'totalBudgetAmount' => $totalBudgetAmount,
            'totalBudgetBalance' => $totalBudgetBalance,
            'totalNetWorth' => $totalNetWorth,
            'budgets_count' => $budgets->count()
        ]);
        
        // Calculate monthly income and expenses (from transactions)
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $transactionQuery = Transaction::query();
        if (!$isAdmin) {
            $transactionQuery->where('created_by', Auth::id());
        }
        
        $monthlyIncome = $transactionQuery->where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
            
        $monthlyExpenses = $transactionQuery->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
        
        // Get accounts for display (logged-in user's accounts only)
        $accounts = Account::where('user_id', Auth::id())
            ->orderBy('balance', 'desc')
            ->get();
        
        // Get recent transactions (user's transactions or all for admin)
        $recentTransactionQuery = Transaction::with('category');
        if (!$isAdmin) {
            $recentTransactionQuery->where('created_by', Auth::id());
        }
        $recentTransactions = $recentTransactionQuery
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Generate monthly data for chart (last 6 months)
        $monthlyData = [];
        $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN'];
        $monthIndex = 0;
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            
            $monthlyTransactionQuery = Transaction::query();
            if (!$isAdmin) {
                $monthlyTransactionQuery->where('created_by', Auth::id());
            }
            
            $monthIncome = $monthlyTransactionQuery->where('type', 'income')
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->sum('amount');
            $monthExpenses = $monthlyTransactionQuery->where('type', 'expense')
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->sum('amount');
            
            $maxAmount = max($monthIncome, $monthExpenses, 1);
            $monthlyData[] = [
                'month' => $months[$monthIndex],
                'income_percent' => round(($monthIncome / $maxAmount) * 80),
                'expense_percent' => round(($monthExpenses / $maxAmount) * 80),
            ];
            $monthIndex++;
        }
        
        // Calculate remaining budget totals
        $totalBudgetSpent = $budgets->sum('spent');
        $totalBudgetRemaining = $totalBudgetAmount - $totalBudgetSpent;
        $budgetUsagePercentage = $totalBudgetAmount > 0 ? ($totalBudgetSpent / $totalBudgetAmount) * 100 : 0;
        
        // Get budgets that are over limit or near limit
        $overBudgetBudgets = $budgets->filter(function($budget) {
            return $budget->is_over_budget;
        });
        $nearLimitBudgets = $budgets->filter(function($budget) {
            return $budget->is_near_limit && !$budget->is_over_budget;
        });
        
        return view('dashboard.index', compact(
            'totalBalance',
            'totalNetWorth',
            'monthlyIncome',
            'monthlyExpenses',
            'accounts',
            'recentTransactions',
            'monthlyData',
            'budgets',
            'totalBudgetAmount',
            'totalBudgetSpent',
            'totalBudgetBalance',
            'totalBudgetRemaining',
            'budgetUsagePercentage',
            'overBudgetBudgets',
            'nearLimitBudgets'
        ));
    }
}
