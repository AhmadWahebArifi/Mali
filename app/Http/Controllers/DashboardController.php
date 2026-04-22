<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        // Calculate total balance from all accounts
        $totalBalance = Account::sum('balance');
        
        // Calculate monthly income and expenses
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $monthlyIncome = Transaction::where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
            
        $monthlyExpenses = Transaction::where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');
        
        // Get all accounts
        $accounts = Account::orderBy('balance', 'desc')->get();
        
        // Get recent transactions
        $recentTransactions = Transaction::with('category')
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
            $monthIncome = Transaction::where('type', 'income')
                ->whereMonth('date', $month->month)
                ->whereYear('date', $month->year)
                ->sum('amount');
            $monthExpenses = Transaction::where('type', 'expense')
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
