@extends('layouts.app')

@section('title', 'Yearly Comparison - Reports')

@section('page-title', 'Yearly Overview Comparison')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <span class="text-label-caps font-label-caps text-blue-600 uppercase tracking-widest mb-1 block">Historical Analysis</span>
            <h1 class="font-h1 text-h1 text-on-surface">Yearly Overview Comparison</h1>
            <p class="font-body-sm text-on-surface-variant mt-1">5-year performance comparison and trends</p>
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
    
    <!-- Best and Worst Performing Years -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Best Year -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-3xl text-success">emoji_events</span>
                <span class="text-xs font-medium px-2 py-1 bg-success text-white rounded-full">Best Year</span>
            </div>
            <h3 class="text-lg font-bold text-green-900 mb-1">{{ $bestYear['year'] }}</h3>
            <p class="text-2xl font-bold text-green-900 mb-2">
                ${{ number_format($bestYear['net'], 2) }}
            </p>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-green-700">Income</p>
                    <p class="font-semibold text-green-900">${{ number_format($bestYear['income'], 2) }}</p>
                </div>
                <div>
                    <p class="text-green-700">Expenses</p>
                    <p class="font-semibold text-green-900">${{ number_format($bestYear['expenses'], 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Worst Year -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-xl border border-red-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="material-symbols-outlined text-3xl text-error">trending_down</span>
                <span class="text-xs font-medium px-2 py-1 bg-error text-white rounded-full">Challenging Year</span>
            </div>
            <h3 class="text-lg font-bold text-red-900 mb-1">{{ $worstYear['year'] }}</h3>
            <p class="text-2xl font-bold text-red-900 mb-2">
                ${{ number_format($worstYear['net'], 2) }}
            </p>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-red-700">Income</p>
                    <p class="font-semibold text-red-900">${{ number_format($worstYear['income'], 2) }}</p>
                </div>
                <div>
                    <p class="text-red-700">Expenses</p>
                    <p class="font-semibold text-red-900">${{ number_format($worstYear['expenses'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Yearly Comparison Table -->
    <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-h2 text-lg text-on-surface">Yearly Performance</h3>
                <p class="font-body-sm text-on-surface-variant">Detailed breakdown by year</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Income</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expenses</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Cash Flow</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Transaction</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Growth</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($years as $year)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $year['year'] }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${{ number_format($year['income'], 2) }}</div>
                                @if(isset($year['income_growth']))
                                    <div class="text-xs {{ $year['income_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $year['income_growth'] > 0 ? '+' : '' }}{{ $year['income_growth'] }}%
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">${{ number_format($year['expenses'], 2) }}</div>
                                @if(isset($year['expense_growth']))
                                    <div class="text-xs {{ $year['expense_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $year['expense_growth'] > 0 ? '+' : '' }}{{ $year['expense_growth'] }}%
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium {{ $year['net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $year['net'] >= 0 ? '+' : '' }}${{ number_format($year['net'], 2) }}
                                </div>
                                @if(isset($year['net_growth']))
                                    <div class="text-xs {{ $year['net_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $year['net_growth'] > 0 ? '+' : '' }}{{ $year['net_growth'] }}%
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $year['transactions'] }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                ${{ number_format($year['avg_transaction'], 2) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if(isset($year['net_growth']))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $year['net_growth'] >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $year['net_growth'] > 0 ? '↗' : '↘' }} {{ abs($year['net_growth']) }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Base Year
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Visual Chart -->
    <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-h2 text-lg text-on-surface">Visual Trend Analysis</h3>
                <p class="font-body-sm text-on-surface-variant">5-year income and expense trends</p>
            </div>
        </div>
        
        <div class="h-64 flex items-end gap-4 px-4">
            @foreach($years as $index => $year)
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full flex gap-1 items-end" style="height: 200px;">
                        <div class="flex-1 bg-success rounded-t" style="height: {{ $year['income'] > 0 ? ($year['income'] / max(collect($years)->pluck('income')->max(), 1)) * 180 : 0 }}px;"></div>
                        <div class="flex-1 bg-error rounded-t" style="height: {{ $year['expenses'] > 0 ? ($year['expenses'] / max(collect($years)->pluck('expenses')->max(), 1)) * 180 : 0 }}px;"></div>
                    </div>
                    <div class="text-center">
                        <div class="text-xs font-medium text-gray-900">{{ $year['year'] }}</div>
                        <div class="text-xs {{ $year['net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $year['net'] >= 0 ? '+' : '' }}${{ number_format($year['net'], 0) }}
                        </div>
                    </div>
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
    
    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-xl border border-outline-variant shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-h2 text-lg text-on-surface">Quick Actions</h3>
                <p class="font-body-sm text-on-surface-variant">Generate detailed reports and analysis</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('reports.annual-performance') }}" 
               class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <span class="material-symbols-outlined text-blue-600">assessment</span>
                <div>
                    <h4 class="font-medium text-blue-900">Annual Performance</h4>
                    <p class="text-sm text-blue-700">View current year details</p>
                </div>
            </a>
            
            <a href="{{ route('reports.detailed-statement') }}" 
               class="flex items-center gap-3 p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <span class="material-symbols-outlined text-green-600">description</span>
                <div>
                    <h4 class="font-medium text-green-900">Detailed Statement</h4>
                    <p class="text-sm text-green-700">Complete financial analysis</p>
                </div>
            </a>
            
            <a href="{{ route('reports.export.pdf') }}" 
               class="flex items-center gap-3 p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <span class="material-symbols-outlined text-purple-600">picture_as_pdf</span>
                <div>
                    <h4 class="font-medium text-purple-900">Export Reports</h4>
                    <p class="text-sm text-purple-700">Download PDF or CSV</p>
                </div>
            </a>
        </div>
    </div>
</main>
@endsection
