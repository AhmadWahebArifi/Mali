@extends('layouts.app')

@section('title', 'Detailed Statement - Reports')

@section('page-title', 'Detailed Financial Statement')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <span class="text-label-caps font-label-caps text-blue-600 uppercase tracking-widest mb-1 block">Financial Statement</span>
            <h1 class="font-h1 text-h1 text-on-surface">Detailed Financial Statement</h1>
            <p class="font-body-sm text-on-surface-variant mt-1">
                @if($filters['start_date'] && $filters['end_date'])
                    {{ \Carbon\Carbon::parse($filters['start_date'])->format('M j, Y') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('M j, Y') }}
                @else
                    Last 6 months
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.index') }}" class="flex items-center gap-2 px-4 py-2 bg-white border border-outline-variant rounded-xl text-sm font-medium hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-sm" data-icon="arrow_back">arrow_back</span>
                Back to Reports
            </a>
            <form id="exportForm" method="GET" action="{{ route('reports.export.pdf') }}" class="contents">
                <input type="hidden" name="start_date" value="{{ $filters['start_date'] }}">
                <input type="hidden" name="end_date" value="{{ $filters['end_date'] }}">
                <input type="hidden" name="category_id" value="{{ $filters['category_id'] }}">
                <input type="hidden" name="account_id" value="{{ $filters['account_id'] }}">
                <input type="hidden" name="type" value="{{ $filters['type'] }}">
                <button type="submit" onclick="this.form.action='{{ route('reports.export.pdf') }}'" class="flex items-center gap-2 px-4 py-2 bg-white border border-outline-variant rounded-xl text-sm font-medium hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-sm" data-icon="picture_as_pdf">picture_as_pdf</span>
                    Export PDF
                </button>
                <button type="submit" onclick="this.form.action='{{ route('reports.export.csv') }}'" class="flex items-center gap-2 px-4 py-2 bg-white border border-outline-variant rounded-xl text-sm font-medium hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-sm" data-icon="download">download</span>
                    Export CSV
                </button>
            </form>
            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-white border border-outline-variant rounded-xl text-sm font-medium hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-sm" data-icon="print">print</span>
                Print
            </button>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Income -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-2xl text-success">trending_up</span>
            </div>
            <h3 class="text-sm font-medium text-green-700 mb-1">Total Income</h3>
            <p class="text-2xl font-bold text-green-900">${{ number_format($totalIncome, 2) }}</p>
        </div>
        
        <!-- Total Expenses -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-xl border border-red-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-2xl text-error">trending_down</span>
            </div>
            <h3 class="text-sm font-medium text-red-700 mb-1">Total Expenses</h3>
            <p class="text-2xl font-bold text-red-900">${{ number_format($totalExpenses, 2) }}</p>
        </div>
        
        <!-- Net Cash Flow -->
        <div class="bg-gradient-to-br {{ $netCashFlow >= 0 ? 'from-blue-50 to-blue-100 border-blue-200' : 'from-orange-50 to-orange-100 border-orange-200' }} p-6 rounded-xl border shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-2xl {{ $netCashFlow >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                    {{ $netCashFlow >= 0 ? 'savings' : 'warning' }}
                </span>
            </div>
            <h3 class="text-sm font-medium {{ $netCashFlow >= 0 ? 'text-blue-700' : 'text-orange-700' }} mb-1">Net Cash Flow</h3>
            <p class="text-2xl font-bold {{ $netCashFlow >= 0 ? 'text-blue-900' : 'text-orange-900' }}">
                {{ $netCashFlow >= 0 ? '+' : '' }}${{ number_format($netCashFlow, 2) }}
            </p>
        </div>
        
        <!-- Transaction Count -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="material-symbols-outlined text-2xl text-purple-600">receipt_long</span>
            </div>
            <h3 class="text-sm font-medium text-purple-700 mb-1">Transactions</h3>
            <p class="text-2xl font-bold text-purple-900">{{ $transactions->count() }}</p>
        </div>
    </div>
    
    <!-- Category Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Categories -->
        <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-h2 text-lg text-on-surface">Category Breakdown</h3>
                    <p class="font-body-sm text-on-surface-variant">Performance by category</p>
                </div>
            </div>
            
            <div class="space-y-4">
                @forelse($categoryBreakdown as $category)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h4 class="font-medium text-on-surface">{{ $category['name'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $category['count'] }} transactions</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold {{ $category['net'] >= 0 ? 'text-success' : 'text-error' }}">
                                {{ $category['net'] >= 0 ? '+' : '' }}${{ number_format($category['net'], 2) }}
                            </p>
                            <div class="flex gap-2 text-xs text-gray-500">
                                <span>In: ${{ number_format($category['income'], 2) }}</span>
                                <span>Out: ${{ number_format($category['expenses'], 2) }}</span>
                            </div>
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
        
        <!-- Accounts -->
        <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-h2 text-lg text-on-surface">Account Performance</h3>
                    <p class="font-body-sm text-on-surface-variant">Balance and activity by account</p>
                </div>
            </div>
            
            <div class="space-y-4">
                @forelse($accountBreakdown as $account)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <h4 class="font-medium text-on-surface">{{ $account['name'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $account['count'] }} transactions</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-on-surface">
                                ${{ number_format($account['balance'], 2) }}
                            </p>
                            <p class="text-xs text-gray-500">Current Balance</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <span class="material-symbols-outlined text-4xl mb-2">account_balance</span>
                        <p>No account data available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Monthly Transactions -->
    <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-h2 text-lg text-on-surface">Monthly Transaction History</h3>
                <p class="font-body-sm text-on-surface-variant">Detailed transaction records by month</p>
            </div>
        </div>
        
        <div class="space-y-6">
            @forelse($monthlyTransactions as $month => $monthTransactions)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h4 class="font-medium text-on-surface">
                            {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                        </h4>
                        <p class="text-sm text-gray-500">{{ $monthTransactions->count() }} transactions</p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($monthTransactions as $transaction)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->date->format('M j, Y') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            {{ $transaction->description }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            {{ $transaction->category->name }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            {{ $transaction->account->name }}
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-right font-medium {{ $transaction->type == 'income' ? 'text-success' : 'text-error' }}">
                                            {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2">receipt_long</span>
                    <p>No transactions found for the selected period</p>
                </div>
            @endforelse
        </div>
    </div>
</main>
@endsection
