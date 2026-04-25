<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // User-based filtering - non-admins can only see their own data
        $isAdmin = Auth::user()->email === 'admin@mali.com';
        
        // Calculate total balance from user's accounts (or all accounts for admin)
        $accountQuery = Account::query();
        if (!$isAdmin) {
            $accountQuery->where('user_id', Auth::id());
        }
        $totalBalance = $accountQuery->sum('balance');
        
        // Calculate monthly income and expenses
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
        
        // Show all accounts like transaction pages, but balance calculations remain user-specific
        $accounts = Account::orderBy('balance', 'desc')->get();
        
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
        
        return view('dashboard.index', compact(
            'totalBalance',
            'monthlyIncome',
            'monthlyExpenses',
            'accounts',
            'recentTransactions',
            'monthlyData'
        ));
    }
}
