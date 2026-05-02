@extends('layouts.app')

@section('title', 'Annual Performance - Reports')

@section('page-title', 'Annual Performance')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <span class="text-label-caps font-label-caps text-blue-600 uppercase tracking-widest mb-1 block">Financial Analysis</span>
            <h1 class="font-h1 text-h1 text-on-surface">Annual Performance {{ $currentYear }}</h1>
            <p class="font-body-sm text-on-surface-variant mt-1">Year-over-year comparison and performance metrics</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.index') }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-outline-variant rounded-xl text-sm font-medium hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-sm" data-icon="arrow_back">arrow_back</span>
                Back to Reports
            </a>
            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-white border border-outline-variant rounded-xl text-sm font-medium hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-sm" data-icon="print">print</span>
                Print
            </button>
        </div>
    </div>
    
    <!-- Year Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Current Year Income -->
        <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-3xl text-success">trending_up</span>
                <span class="text-xs font-medium px-2 py-1 bg-success/10 text-success rounded-full">
                    {{ $incomeGrowth > 0 ? '+' : '' }}{{ $incomeGrowth }}%
                </span>
            </div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Total Income</h3>
            <p class="text-2xl font-bold text-on-surface">${{ number_format($currentYearIncome, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $previousYear }}: ${{ number_format($previousYearIncome, 2) }}</p>
        </div>
        
        <!-- Current Year Expenses -->
        <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-3xl text-error">trending_down</span>
                <span class="text-xs font-medium px-2 py-1 bg-error/10 text-error rounded-full">
                    {{ $expenseGrowth > 0 ? '+' : '' }}{{ $expenseGrowth }}%
                </span>
            </div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Total Expenses</h3>
            <p class="text-2xl font-bold text-on-surface">${{ number_format($currentYearExpenses, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ $previousYear }}: ${{ number_format($previousYearExpenses, 2) }}</p>
        </div>
        
        <!-- Net Cash Flow -->
        <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-3xl {{ $currentYearNet >= 0 ? 'text-success' : 'text-error' }}">
                    {{ $currentYearNet >= 0 ? 'savings' : 'warning' }}
                </span>
                <span class="text-xs font-medium px-2 py-1 {{ $netGrowth >= 0 ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }} rounded-full">
                    {{ $netGrowth > 0 ? '+' : '' }}{{ $netGrowth }}%
                </span>
            </div>
            <h3 class="text-sm font-medium text-gray-500 mb-1">Net Cash Flow</h3>
            <p class="text-2xl font-bold text-on-surface">
                {{ $currentYearNet >= 0 ? '+' : '' }}${{ number_format($currentYearNet, 2) }}
            </p>
            <p class="text-xs text-gray-500 mt-2">{{ $previousYear }}: {{ $previousYearNet >= 0 ? '+' : '' }}${{ number_format($previousYearNet, 2) }}</p>
        </div>
    </div>
    
    <!-- Monthly Breakdown Chart -->
    <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-h2 text-lg text-on-surface">Monthly Breakdown</h3>
                <p class="font-body-sm text-on-surface-variant">Income vs expenses by month</p>
            </div>
        </div>
        
        <div class="h-64 flex items-end gap-2 px-2">
            @foreach($monthlyBreakdown as $index => $month)
                <div class="flex-1 flex flex-col justify-end gap-1">
                    <div class="flex gap-1 h-full items-end">
                        <div class="flex-1 bg-success rounded-t" style="height: {{ $month['income'] > 0 ? max($month['income'] / max(collect($monthlyBreakdown)->pluck('income')->max(), 1) * 100, 5) : 0 }}%"></div>
                        <div class="flex-1 bg-error rounded-t" style="height: {{ $month['expenses'] > 0 ? max($month['expenses'] / max(collect($monthlyBreakdown)->pluck('expenses')->max(), 1) * 100, 5) : 0 }}%"></div>
                    </div>
                    <div class="text-xs text-center text-gray-500 font-medium">{{ $month['month'] }}</div>
                </div>
            @endforeach
        </div>
        
        <div class="flex items-center justify-center gap-6 mt-4">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-success rounded"></div>
                <span class="text-xs text-gray-600">Income</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-error rounded"></div>
                <span class="text-xs text-gray-600">Expenses</span>
            </div>
        </div>
    </div>
    
    <!-- Top Categories and Detailed Statement -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Categories -->
        <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-h2 text-lg text-on-surface">Top Categories</h3>
                    <p class="font-body-sm text-on-surface-variant">Highest performing categories this year</p>
                </div>
            </div>
            
            <div class="space-y-4">
                @forelse($topCategories as $category)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <h4 class="font-medium text-on-surface">{{ $category->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $category->count }} transactions</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-on-surface">${{ number_format($category->total, 2) }}</p>
                            <p class="text-xs text-gray-500">Total</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <span class="material-symbols-outlined text-4xl mb-2">category</span>
                        <p>No category data available</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Detailed Statement Button -->
        <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-h2 text-lg text-on-surface">Detailed Analysis</h3>
                    <p class="font-body-sm text-on-surface-variant">Comprehensive financial statement</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="font-medium text-blue-900 mb-2">Complete Financial Statement</h4>
                    <p class="text-sm text-blue-700 mb-4">
                        View detailed breakdown of all transactions, category analysis, and account performance for the selected period.
                    </p>
                    <a href="{{ route('reports.detailed-statement') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <span class="material-symbols-outlined text-sm">description</span>
                        View Detailed Statement
                    </a>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Avg Monthly Income</p>
                        <p class="font-bold text-on-surface">
                            ${{ number_format($currentYearIncome / 12, 2) }}
                        </p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Avg Monthly Expenses</p>
                        <p class="font-bold text-on-surface">
                            ${{ number_format($currentYearExpenses / 12, 2) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
