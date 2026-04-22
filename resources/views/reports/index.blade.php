@extends('layouts.app')

@section('title', 'Financial Reports')

@section('page-title', 'Reports')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <span class="text-label-caps font-label-caps text-blue-600 uppercase tracking-widest mb-1 block">Analytics Dashboard</span>
            <h1 class="font-h1 text-h1 text-on-surface">Financial Reports</h1>
        </div>
        <div class="flex items-center gap-2">
            <button class="flex items-center gap-2 px-4 py-2 bg-white border border-outline-variant rounded-xl text-sm font-medium hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-sm" data-icon="ios_share">ios_share</span>
                Export PDF
            </button>
            <button class="flex items-center gap-2 px-4 py-2 bg-white border border-outline-variant rounded-xl text-sm font-medium hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-sm" data-icon="download">download</span>
                Export CSV
            </button>
            <button class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm" data-icon="filter_list">filter_list</span>
                Filters
            </button>
        </div>
    </div>
    
    <!-- Bento Grid Layout -->
    <div class="grid grid-cols-12 gap-6">
        <!-- Monthly Summary Chart -->
        <div class="col-span-12 lg:col-span-8 bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="font-label-caps text-label-caps text-gray-500 uppercase tracking-widest mb-1">Monthly Summary</h3>
                    <p class="font-h2 text-lg text-on-surface">Income vs Expenses</p>
                </div>
                <select class="text-xs border-gray-200 rounded-lg py-1 pr-8 focus:ring-blue-500">
                    <option>Last 6 Months</option>
                    <option>Last 12 Months</option>
                </select>
            </div>
            <div class="h-64 flex items-end gap-2 sm:gap-4 px-2">
                <!-- Bar Chart Representation -->
                @foreach($monthlyData as $index => $month)
                <div class="flex-1 flex flex-col justify-end gap-1">
                    <div class="flex gap-1 h-full items-end">
                        <div class="w-full bg-primary-container rounded-t-sm" style="height: {{ $month['income_percent'] }}%;"></div>
                        <div class="w-full bg-error/30 rounded-t-sm" style="height: {{ $month['expense_percent'] }}%;"></div>
                    </div>
                    <span class="text-[10px] text-center font-medium text-gray-400">{{ $month['month'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-6 flex justify-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-primary-container rounded-full"></div>
                    <span class="text-xs font-medium text-gray-600">Total Income</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-error/30 rounded-full"></div>
                    <span class="text-xs font-medium text-gray-600">Total Expenses</span>
                </div>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="bg-primary text-white p-6 rounded-xl shadow-lg relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-label-caps font-label-caps opacity-70 mb-2 uppercase tracking-widest">Net Cash Flow</h3>
                    <p class="font-display-financial text-3xl mb-1">${{ number_format($netCashFlow, 2) }}</p>
                    <p class="text-xs text-secondary-container flex items-center gap-1 font-medium">
                        <span class="material-symbols-outlined text-xs" data-icon="trending_up">trending_up</span>
                        {{ $cashFlowPercentage }}% increase from last month
                    </p>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <span class="material-symbols-outlined text-9xl" data-icon="account_balance_wallet">account_balance_wallet</span>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
                <h3 class="font-label-caps text-label-caps text-gray-500 uppercase mb-4 tracking-widest">Saving Goal Progress</h3>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-on-surface">Emergency Fund</span>
                    <span class="text-sm font-bold text-primary">{{ $savingsGoalPercentage }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5 mb-4">
                    <div class="bg-primary h-2.5 rounded-full" style="width: {{ $savingsGoalPercentage }}%"></div>
                </div>
                <p class="text-xs text-gray-500">${{ number_format($currentSavings, 0) }} of ${{ number_format($savingsGoal, 0) }} target</p>
            </div>
        </div>
        
        <!-- Category Breakdown -->
        <div class="col-span-12 md:col-span-6 bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="font-label-caps text-label-caps text-gray-500 uppercase tracking-widest mb-1">Spending</h3>
                    <p class="font-h2 text-lg text-on-surface">Category Breakdown</p>
                </div>
                <button class="p-2 hover:bg-gray-50 rounded-lg">
                    <span class="material-symbols-outlined text-gray-400" data-icon="more_vert">more_vert</span>
                </button>
            </div>
            <div class="flex items-center gap-8">
                <!-- Custom Pie Chart Representation -->
                <div class="relative w-40 h-40">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" fill="transparent" r="15.915" stroke="#F3F4F6" stroke-width="3"></circle>
                        @foreach($categoryBreakdown as $index => $category)
                        <circle cx="18" cy="18" fill="transparent" r="15.915" 
                                stroke="{{ $category['color'] }}" 
                                stroke-dasharray="{{ $category['percentage'] }} {{ 100 - $category['percentage'] }}" 
                                stroke-dashoffset="{{ $category['offset'] }}" 
                                stroke-width="3"></circle>
                        @endforeach
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-[10px] text-gray-400 font-bold uppercase">Total</span>
                        <span class="text-sm font-bold">${{ number_format($totalExpenses / 1000, 1) }}k</span>
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    @foreach($categoryBreakdown as $category)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $category['color'] }}"></div>
                            <span class="text-xs text-gray-600">{{ $category['name'] }}</span>
                        </div>
                        <span class="text-xs font-bold">{{ $category['percentage'] }}%</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Account Trends Line Chart -->
        <div class="col-span-12 md:col-span-6 bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="font-label-caps text-label-caps text-gray-500 uppercase tracking-widest mb-1">Growth</h3>
                    <p class="font-h2 text-lg text-on-surface">Account Trends</p>
                </div>
                <div class="flex gap-2">
                    <button class="px-2 py-1 text-[10px] font-bold bg-blue-50 text-blue-600 rounded">Checking</button>
                    <button class="px-2 py-1 text-[10px] font-bold text-gray-400 rounded">Savings</button>
                </div>
            </div>
            <div class="h-40 relative mt-4">
                <!-- Custom SVG Path for Line Chart -->
                <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 400 100">
                    <path d="M0,80 Q50,70 100,50 T200,60 T300,30 T400,10" fill="none" stroke="#004ccd" stroke-linecap="round" stroke-width="3"></path>
                    <path d="M0,80 Q50,70 100,50 T200,60 T300,30 T400,10 V100 H0 Z" fill="url(#gradient-blue)" opacity="0.1"></path>
                    <defs>
                        <linearGradient id="gradient-blue" x1="0%" x2="0%" y1="0%" y2="100%">
                            <stop offset="0%" stop-color="#004ccd"></stop>
                            <stop offset="100%" stop-color="#ffffff"></stop>
                        </linearGradient>
                    </defs>
                </svg>
                <div class="flex justify-between mt-4 text-[10px] font-bold text-gray-400">
                    <span>MON</span>
                    <span>TUE</span>
                    <span>WED</span>
                    <span>THU</span>
                    <span>FRI</span>
                    <span>SAT</span>
                    <span>SUN</span>
                </div>
            </div>
            <div class="mt-8 flex items-center justify-between p-3 bg-surface-container-low rounded-lg">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-blue-600" data-icon="insights">insights</span>
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 uppercase">Avg Daily Balance</p>
                        <p class="text-sm font-bold text-on-surface">${{ number_format($avgDailyBalance, 2) }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-gray-500 uppercase">Volatility</p>
                    <p class="text-sm font-bold text-error">Low</p>
                </div>
            </div>
        </div>
        
        <!-- Yearly Overview Table -->
        <div class="col-span-12 bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-label-caps text-label-caps text-gray-500 uppercase tracking-widest mb-1">Annual Performance</h3>
                    <p class="font-h2 text-lg text-on-surface">Yearly Overview Comparison</p>
                </div>
                <button class="text-sm font-semibold text-blue-600 hover:underline">View Detailed Statement</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Quarter</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Revenue</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Operating Cost</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Net Profit</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Trend</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($quarterlyData as $quarter)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-sm text-on-surface">{{ $quarter['name'] }}</td>
                            <td class="px-6 py-4 font-data-mono text-sm text-right">${{ number_format($quarter['revenue'], 2) }}</td>
                            <td class="px-6 py-4 font-data-mono text-sm text-right">${{ number_format($quarter['cost'], 2) }}</td>
                            <td class="px-6 py-4 font-data-mono text-sm text-right {{ $quarter['profit'] >= 0 ? 'text-secondary' : 'text-error' }} font-bold">
                                {{ $quarter['profit'] >= 0 ? '+' : '' }}${{ number_format($quarter['profit'], 2) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="material-symbols-outlined {{ $quarter['profit'] >= 0 ? 'text-secondary' : 'text-error' }}" data-icon="{{ $quarter['profit'] >= 0 ? 'trending_up' : 'trending_down' }}">
                                    {{ $quarter['profit'] >= 0 ? 'trending_up' : 'trending_down' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
@endsection
