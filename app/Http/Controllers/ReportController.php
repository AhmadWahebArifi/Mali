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
        $allCategoryExpenses = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.type', 'expense')
            ->whereMonth('transactions.date', now()->month)
            ->whereYear('transactions.date', now()->year)
            ->selectRaw('categories.name, SUM(transactions.amount) as total')
            ->groupBy('categories.name', 'categories.id')
            ->orderBy('total', 'desc')
            ->get();
        
        $totalExpenses = (float) $allCategoryExpenses->sum('total');
        $categoryBreakdown = [];
        $colors = ['#004ccd', '#4edea3', '#ba1a1a', '#0f62fe', '#f59e0b'];
        $offset = 0;
        $usedPercentage = 0;
        $maxSlices = 5;
        
        foreach ($allCategoryExpenses as $index => $category) {
            if ($index >= $maxSlices) break;
            $rawPercentage = $totalExpenses > 0 ? ($category->total / $totalExpenses) * 100 : 0;
            $percentage = round($rawPercentage);
            // Ensure we don't exceed 100 with rounding
            if ($usedPercentage + $percentage > 100 && $index < $allCategoryExpenses->count() - 1) {
                $percentage = max(0, 100 - $usedPercentage);
            }
            $categoryBreakdown[] = [
                'name' => $category->name,
                'amount' => (float) $category->total,
                'percentage' => $percentage,
                'color' => $colors[$index % count($colors)],
                'offset' => -$offset,
            ];
            $offset += $rawPercentage;
            $usedPercentage += $percentage;
        }
        
        // Add "Other" slice if there are more categories
        if ($allCategoryExpenses->count() > $maxSlices) {
            $otherTotal = $allCategoryExpenses->slice($maxSlices)->sum('total');
            $otherPercentage = max(0, 100 - $usedPercentage);
            if ($otherPercentage > 0) {
                $categoryBreakdown[] = [
                    'name' => 'Other',
                    'amount' => (float) $otherTotal,
                    'percentage' => $otherPercentage,
                    'color' => '#9ca3af',
                    'offset' => -$offset,
                ];
            }
        }
        
        // Account trends data - weekly cash flow for line chart
        $avgDailyBalance = Account::avg('balance');
        
        $weekDays = [];
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayIncome = Transaction::where('type', 'income')
                ->whereDate('date', $date)
                ->sum('amount');
            $dayExpense = Transaction::where('type', 'expense')
                ->whereDate('date', $date)
                ->sum('amount');
            $weeklyData[] = (float) ($dayIncome - $dayExpense);
            $weekDays[] = $date->format('D');
        }
        
        // Normalize to 0-100 range for SVG path
        $maxFlow = max(abs(max($weeklyData)), abs(min($weeklyData)), 1);
        $svgPoints = [];
        foreach ($weeklyData as $i => $val) {
            $x = ($i / 6) * 400;
            $y = 50 - (($val / $maxFlow) * 40); // center at 50, range 40
            $svgPoints[] = [$x, $y];
        }
        
        // Build SVG path
        if (count($svgPoints) > 1) {
            $first = $svgPoints[0];
            $pathD = "M{$first[0]},{$first[1]}";
            for ($i = 1; $i < count($svgPoints); $i++) {
                $prev = $svgPoints[$i - 1];
                $curr = $svgPoints[$i];
                $cp1x = $prev[0] + ($curr[0] - $prev[0]) / 3;
                $cp1y = $prev[1];
                $cp2x = $curr[0] - ($curr[0] - $prev[0]) / 3;
                $cp2y = $curr[1];
                $pathD .= " C{$cp1x},{$cp1y} {$cp2x},{$cp2y} {$curr[0]},{$curr[1]}";
            }
            // Area fill path
            $last = $svgPoints[count($svgPoints) - 1];
            $areaD = $pathD . " V100 H{$first[0]} Z";
        } else {
            $pathD = "M0,50 L400,50";
            $areaD = "M0,50 L400,50 V100 H0 Z";
        }
        
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
            'quarterlyData',
            'weekDays',
            'pathD',
            'areaD'
        ));
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->getFilters($request);
        $transactions = $this->getFilteredTransactions($filters);
        
        $pdf = new \Dompdf\Dompdf();
        $html = view('reports.pdf', compact('transactions', 'filters'))->render();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();
        
        $filename = 'financial-report-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->stream($filename);
    }

    public function exportCsv(Request $request)
    {
        $filters = $this->getFilters($request);
        $transactions = $this->getFilteredTransactions($filters);
        
        $filename = 'financial-report-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($file, [
                'Date', 'Description', 'Category', 'Account', 'Type', 'Amount', 'Balance'
            ]);
            
            // CSV data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->date->format('Y-m-d'),
                    $transaction->description,
                    $transaction->category->name,
                    $transaction->account->name,
                    ucfirst($transaction->type),
                    number_format($transaction->amount, 2),
                    number_format($transaction->account->balance, 2)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getFilters(Request $request)
    {
        return [
            'start_date' => $request->input('start_date', now()->subMonths(6)->format('Y-m-d')),
            'end_date' => $request->input('end_date', now()->format('Y-m-d')),
            'category_id' => $request->input('category_id'),
            'account_id' => $request->input('account_id'),
            'type' => $request->input('type'),
        ];
    }

    private function getFilteredTransactions($filters)
    {
        $query = Transaction::with(['category', 'account'])
            ->whereBetween('date', [$filters['start_date'], $filters['end_date']]);

        if ($filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        if ($filters['account_id']) {
            $query->where('account_id', $filters['account_id']);
        }

        if ($filters['type']) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function annualPerformance()
    {
        $currentYear = now()->year;
        $previousYear = $currentYear - 1;
        
        // Current year data
        $currentYearIncome = Transaction::where('type', 'income')
            ->whereYear('date', $currentYear)
            ->sum('amount');
        $currentYearExpenses = Transaction::where('type', 'expense')
            ->whereYear('date', $currentYear)
            ->sum('amount');
        $currentYearNet = $currentYearIncome - $currentYearExpenses;
        
        // Previous year data
        $previousYearIncome = Transaction::where('type', 'income')
            ->whereYear('date', $previousYear)
            ->sum('amount');
        $previousYearExpenses = Transaction::where('type', 'expense')
            ->whereYear('date', $previousYear)
            ->sum('amount');
        $previousYearNet = $previousYearIncome - $previousYearExpenses;
        
        // Monthly breakdown for current year
        $monthlyBreakdown = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthIncome = Transaction::where('type', 'income')
                ->whereYear('date', $currentYear)
                ->whereMonth('date', $month)
                ->sum('amount');
            $monthExpenses = Transaction::where('type', 'expense')
                ->whereYear('date', $currentYear)
                ->whereMonth('date', $month)
                ->sum('amount');
            
            $monthlyBreakdown[] = [
                'month' => \Carbon\Carbon::createFromDate($currentYear, $month, 1)->format('M'),
                'income' => (float) $monthIncome,
                'expenses' => (float) $monthExpenses,
                'net' => (float) ($monthIncome - $monthExpenses),
            ];
        }
        
        // Performance metrics
        $incomeGrowth = $previousYearIncome > 0 ? 
            round((($currentYearIncome - $previousYearIncome) / $previousYearIncome) * 100, 2) : 0;
        $expenseGrowth = $previousYearExpenses > 0 ? 
            round((($currentYearExpenses - $previousYearExpenses) / $previousYearExpenses) * 100, 2) : 0;
        $netGrowth = $previousYearNet != 0 ? 
            round((($currentYearNet - $previousYearNet) / abs($previousYearNet)) * 100, 2) : 0;
        
        // Top categories for current year
        $topCategories = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->whereYear('transactions.date', $currentYear)
            ->selectRaw('categories.name, SUM(transactions.amount) as total, COUNT(transactions.id) as count')
            ->groupBy('categories.name', 'categories.id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        return view('reports.annual-performance', compact(
            'currentYear',
            'previousYear',
            'currentYearIncome',
            'currentYearExpenses',
            'currentYearNet',
            'previousYearIncome',
            'previousYearExpenses',
            'previousYearNet',
            'monthlyBreakdown',
            'incomeGrowth',
            'expenseGrowth',
            'netGrowth',
            'topCategories'
        ));
    }

    public function yearlyComparison()
    {
        $years = [];
        $currentYear = now()->year;
        
        // Get data for last 5 years
        for ($year = $currentYear - 4; $year <= $currentYear; $year++) {
            $yearIncome = Transaction::where('type', 'income')
                ->whereYear('date', $year)
                ->sum('amount');
            $yearExpenses = Transaction::where('type', 'expense')
                ->whereYear('date', $year)
                ->sum('amount');
            $yearNet = $yearIncome - $yearExpenses;
            $transactionCount = Transaction::whereYear('date', $year)->count();
            
            $years[] = [
                'year' => $year,
                'income' => (float) $yearIncome,
                'expenses' => (float) $yearExpenses,
                'net' => (float) $yearNet,
                'transactions' => $transactionCount,
                'avg_transaction' => $transactionCount > 0 ? round($yearIncome / $transactionCount, 2) : 0,
            ];
        }
        
        // Calculate year-over-year growth percentages
        for ($i = 1; $i < count($years); $i++) {
            $prevYear = $years[$i - 1];
            $currYear = $years[$i];
            
            $years[$i]['income_growth'] = $prevYear['income'] > 0 ? 
                round((($currYear['income'] - $prevYear['income']) / $prevYear['income']) * 100, 2) : 0;
            $years[$i]['expense_growth'] = $prevYear['expenses'] > 0 ? 
                round((($currYear['expenses'] - $prevYear['expenses']) / $prevYear['expenses']) * 100, 2) : 0;
            $years[$i]['net_growth'] = $prevYear['net'] != 0 ? 
                round((($currYear['net'] - $prevYear['net']) / abs($prevYear['net'])) * 100, 2) : 0;
        }
        
        // Best and worst performing years
        $bestYear = collect($years)->sortByDesc('net')->first();
        $worstYear = collect($years)->sortBy('net')->first();
        
        return view('reports.yearly-comparison', compact(
            'years',
            'bestYear',
            'worstYear'
        ));
    }

    public function detailedStatement(Request $request)
    {
        $filters = $this->getFilters($request);
        $transactions = $this->getFilteredTransactions($filters);
        
        // Calculate summary statistics
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpenses = $transactions->where('type', 'expense')->sum('amount');
        $netCashFlow = $totalIncome - $totalExpenses;
        
        // Group transactions by month
        $monthlyTransactions = $transactions->groupBy(function($transaction) {
            return $transaction->date->format('Y-m');
        })->sortKeys();
        
        // Category breakdown
        $categoryBreakdown = $transactions->groupBy('category.name')
            ->map(function($categoryTransactions, $categoryName) {
                $income = $categoryTransactions->where('type', 'income')->sum('amount');
                $expenses = $categoryTransactions->where('type', 'expense')->sum('amount');
                return [
                    'name' => $categoryName,
                    'income' => $income,
                    'expenses' => $expenses,
                    'net' => $income - $expenses,
                    'count' => $categoryTransactions->count(),
                ];
            })->sortByDesc('net');
        
        // Account breakdown
        $accountBreakdown = $transactions->groupBy('account.name')
            ->map(function($accountTransactions, $accountName) {
                $income = $accountTransactions->where('type', 'income')->sum('amount');
                $expenses = $accountTransactions->where('type', 'expense')->sum('amount');
                return [
                    'name' => $accountName,
                    'income' => $income,
                    'expenses' => $expenses,
                    'net' => $income - $expenses,
                    'count' => $accountTransactions->count(),
                    'balance' => $accountTransactions->last()->account->balance ?? 0,
                ];
            });
        
        return view('reports.detailed-statement', compact(
            'transactions',
            'filters',
            'totalIncome',
            'totalExpenses',
            'netCashFlow',
            'monthlyTransactions',
            'categoryBreakdown',
            'accountBreakdown'
        ));
    }
}
