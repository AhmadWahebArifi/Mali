<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Account;

class ReportController extends Controller
{
    public function index()
    {
        // Calculate monthly data for charts
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
        
        // Calculate net cash flow
        $currentMonthIncome = Transaction::where('type', 'income')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');
        $currentMonthExpenses = Transaction::where('type', 'expense')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');
        $netCashFlow = $currentMonthIncome - $currentMonthExpenses;
        
        // Previous month for comparison
        $previousMonthIncome = Transaction::where('type', 'income')
            ->whereMonth('date', now()->subMonth()->month)
            ->whereYear('date', now()->subMonth()->year)
            ->sum('amount');
        $previousMonthExpenses = Transaction::where('type', 'expense')
            ->whereMonth('date', now()->subMonth()->month)
            ->whereYear('date', now()->subMonth()->year)
            ->sum('amount');
        $previousNetCashFlow = $previousMonthIncome - $previousMonthExpenses;
        
        $cashFlowPercentage = $previousNetCashFlow != 0 
            ? round((($netCashFlow - $previousNetCashFlow) / abs($previousNetCashFlow)) * 100, 1)
            : 0;
        
        // Savings goal progress
        $totalBalance = Account::sum('balance');
        $savingsGoal = 25000;
        $currentSavings = max($totalBalance, 0);
        $savingsGoalPercentage = min(round(($currentSavings / $savingsGoal) * 100), 100);
        
        // Category breakdown for expenses
        $categoryExpenses = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.type', 'expense')
            ->whereMonth('transactions.date', now()->month)
            ->whereYear('transactions.date', now()->year)
            ->selectRaw('categories.name, SUM(transactions.amount) as total')
            ->groupBy('categories.name', 'categories.id')
            ->orderBy('total', 'desc')
            ->limit(4)
            ->get();
        
        $totalExpenses = $categoryExpenses->sum('total');
        $categoryBreakdown = [];
        $colors = ['#004ccd', '#4edea3', '#ba1a1a', '#0f62fe'];
        $offset = 0;
        
        foreach ($categoryExpenses as $index => $category) {
            $percentage = $totalExpenses > 0 ? round(($category->total / $totalExpenses) * 100) : 0;
            $categoryBreakdown[] = [
                'name' => $category->name,
                'percentage' => $percentage,
                'color' => $colors[$index % count($colors)],
                'offset' => -$offset,
            ];
            $offset += $percentage;
        }
        
        // Account trends data
        $avgDailyBalance = Account::avg('balance');
        
        // Calculate real quarterly data for current year
        $quarterlyData = [];
        $currentYear = now()->year;
        for ($q = 1; $q <= 4; $q++) {
            $startMonth = ($q - 1) * 3 + 1;
            $endMonth = $startMonth + 2;
            $revenue = Transaction::where('type', 'income')
                ->whereYear('date', $currentYear)
                ->whereMonth('date', '>=', $startMonth)
                ->whereMonth('date', '<=', $endMonth)
                ->sum('amount');
            $cost = Transaction::where('type', 'expense')
                ->whereYear('date', $currentYear)
                ->whereMonth('date', '>=', $startMonth)
                ->whereMonth('date', '<=', $endMonth)
                ->sum('amount');
            if ($revenue > 0 || $cost > 0) {
                $quarterlyData[] = [
                    'name' => "Q{$q} {$currentYear}",
                    'revenue' => (float) $revenue,
                    'cost' => (float) $cost,
                    'profit' => (float) ($revenue - $cost),
                ];
            }
        }
        
        return view('reports.index', compact(
            'monthlyData',
            'netCashFlow',
            'cashFlowPercentage',
            'savingsGoalPercentage',
            'currentSavings',
            'savingsGoal',
            'categoryBreakdown',
            'totalExpenses',
            'avgDailyBalance',
            'quarterlyData'
        ));
    }
}
