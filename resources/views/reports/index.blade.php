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
            <form id="exportForm" method="GET" action="{{ route('reports.export.pdf') }}" class="contents">
                <input type="hidden" name="start_date" id="filter_start_date" value="{{ now()->subMonths(6)->format('Y-m-d') }}">
                <input type="hidden" name="end_date" id="filter_end_date" value="{{ now()->format('Y-m-d') }}">
                <input type="hidden" name="category_id" id="filter_category_id">
                <input type="hidden" name="account_id" id="filter_account_id">
                <input type="hidden" name="type" id="filter_type">
                <button type="submit" onclick="this.form.action='{{ route('reports.export.pdf') }}'" class="flex items-center gap-2 px-4 py-2 bg-white border border-outline-variant rounded-xl text-sm font-medium hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-sm" data-icon="ios_share">ios_share</span>
                    Export PDF
                </button>
                <button type="submit" onclick="this.form.action='{{ route('reports.export.csv') }}'" class="flex items-center gap-2 px-4 py-2 bg-white border border-outline-variant rounded-xl text-sm font-medium hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-sm" data-icon="download">download</span>
                    Export CSV
                </button>
            </form>
            <button onclick="toggleFilters()" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-xl text-sm font-medium hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm" data-icon="filter_list">filter_list</span>
                Filters
            </button>
        </div>
    </div>
    
    <!-- Filters Panel (Hidden by default) -->
    <div id="filtersPanel" class="hidden bg-white p-6 rounded-xl border border-outline-variant shadow-sm mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-h2 text-lg text-on-surface">Filter Reports</h3>
            <button onclick="toggleFilters()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" id="start_date" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ now()->subMonths(6)->format('Y-m-d') }}">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" id="end_date" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       value="{{ now()->format('Y-m-d') }}">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category_id" id="category_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Categories</option>
                    @foreach(\App\Models\Category::orderBy('name')->get() as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Account</label>
                <select name="account_id" id="account_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Accounts</option>
                    @foreach(\App\Models\Account::orderBy('name')->get() as $account)
                    <option value="{{ $account->id }}">{{ $account->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                <select name="type" id="type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button type="button" onclick="applyFilters()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Apply Filters
                </button>
                <button type="button" onclick="clearFilters()" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Clear
                </button>
            </div>
        </form>
    </div>
    
    <!-- Quick Navigation -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('reports.annual-performance') }}" 
           class="flex items-center gap-4 p-6 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200 hover:from-blue-100 hover:to-blue-200 transition-all">
            <span class="material-symbols-outlined text-3xl text-blue-600">assessment</span>
            <div>
                <h3 class="font-semibold text-blue-900 mb-1">Annual Performance</h3>
                <p class="text-sm text-blue-700">Current year detailed analysis and metrics</p>
            </div>
        </a>
        
        <a href="{{ route('reports.yearly-comparison') }}" 
           class="flex items-center gap-4 p-6 bg-gradient-to-r from-green-50 to-green-100 rounded-xl border border-green-200 hover:from-green-100 hover:to-green-200 transition-all">
            <span class="material-symbols-outlined text-3xl text-green-600">trending_up</span>
            <div>
                <h3 class="font-semibold text-green-900 mb-1">Yearly Comparison</h3>
                <p class="text-sm text-green-700">5-year performance comparison and trends</p>
            </div>
        </a>
        
        <a href="{{ route('reports.detailed-statement') }}" 
           class="flex items-center gap-4 p-6 bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl border border-purple-200 hover:from-purple-100 hover:to-purple-200 transition-all">
            <span class="material-symbols-outlined text-3xl text-purple-600">description</span>
            <div>
                <h3 class="font-semibold text-purple-900 mb-1">Detailed Statement</h3>
                <p class="text-sm text-purple-700">Complete financial statement with breakdowns</p>
            </div>
        </a>
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
            @if($totalExpenses > 0)
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
                        <span class="text-sm font-bold">${{ number_format($totalExpenses, 0) }}</span>
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    @foreach($categoryBreakdown as $category)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $category['color'] }}"></div>
                            <span class="text-xs text-gray-600">{{ $category['name'] }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold block">{{ $category['percentage'] }}%</span>
                            <span class="text-[10px] text-gray-400">${{ number_format($category['amount'], 0) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="text-center py-8 text-gray-400">
                <span class="material-symbols-outlined text-4xl mb-2">pie_chart</span>
                <p class="text-sm">No expenses this month</p>
            </div>
            @endif
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
                <!-- Dynamic SVG Line Chart from Real Transaction Data -->
                <svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 400 100">
                    <path d="{{ $pathD }}" fill="none" stroke="#004ccd" stroke-linecap="round" stroke-width="3"></path>
                    <path d="{{ $areaD }}" fill="url(#gradient-blue)" opacity="0.1"></path>
                    <defs>
                        <linearGradient id="gradient-blue" x1="0%" x2="0%" y1="0%" y2="100%">
                            <stop offset="0%" stop-color="#004ccd"></stop>
                            <stop offset="100%" stop-color="#ffffff"></stop>
                        </linearGradient>
                    </defs>
                </svg>
                <div class="flex justify-between mt-4 text-[10px] font-bold text-gray-400">
                    @foreach($weekDays as $day)
                    <span>{{ $day }}</span>
                    @endforeach
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
                <a href="{{ route('reports.detailed-statement') }}" class="text-sm font-semibold text-blue-600 hover:underline">View Detailed Statement</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Quarter</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Revenue</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Operating Cost</th>
                            <th class="px-4 py-3 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Net Profit</th>
                            <th class="px-4 py-3 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Trend</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($quarterlyData as $quarter)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-bold text-sm text-on-surface">{{ $quarter['name'] }}</td>
                            <td class="px-4 py-3 font-data-mono text-sm text-right">${{ number_format($quarter['revenue'], 2) }}</td>
                            <td class="px-4 py-3 font-data-mono text-sm text-right">${{ number_format($quarter['cost'], 2) }}</td>
                            <td class="px-4 py-3 font-data-mono text-sm text-right {{ $quarter['profit'] >= 0 ? 'text-secondary' : 'text-error' }} font-bold">
                                {{ $quarter['profit'] >= 0 ? '+' : '' }}${{ number_format($quarter['profit'], 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="material-symbols-outlined {{ $quarter['profit'] >= 0 ? 'text-secondary' : 'text-error' }}" data-icon="{{ $quarter['profit'] >= 0 ? 'trending_up' : 'trending_down' }}">
                                    {{ $quarter['profit'] >= 0 ? 'trending_up' : 'trending_down' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">
                                No quarterly data available. Add transactions to generate reports.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    panel.classList.toggle('hidden');
}

function applyFilters() {
    // Update hidden form fields for export
    document.getElementById('filter_start_date').value = document.getElementById('start_date').value;
    document.getElementById('filter_end_date').value = document.getElementById('end_date').value;
    document.getElementById('filter_category_id').value = document.getElementById('category_id').value;
    document.getElementById('filter_account_id').value = document.getElementById('account_id').value;
    document.getElementById('filter_type').value = document.getElementById('type').value;
    
    // Show loading state
    Swal.fire({
        title: 'Applying Filters...',
        text: 'Please wait',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Reload page with filters
    const params = new URLSearchParams();
    params.set('start_date', document.getElementById('start_date').value);
    params.set('end_date', document.getElementById('end_date').value);
    
    if (document.getElementById('category_id').value) {
        params.set('category_id', document.getElementById('category_id').value);
    }
    if (document.getElementById('account_id').value) {
        params.set('account_id', document.getElementById('account_id').value);
    }
    if (document.getElementById('type').value) {
        params.set('type', document.getElementById('type').value);
    }
    
    window.location.href = '{{ route("reports.index") }}?' + params.toString();
}

function clearFilters() {
    document.getElementById('start_date').value = '{{ now()->subMonths(6)->format('Y-m-d') }}';
    document.getElementById('end_date').value = '{{ now()->format('Y-m-d') }}';
    document.getElementById('category_id').value = '';
    document.getElementById('account_id').value = '';
    document.getElementById('type').value = '';
    
    applyFilters();
}

// Load filters from URL parameters on page load
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    
    if (params.has('start_date')) {
        document.getElementById('start_date').value = params.get('start_date');
        document.getElementById('filter_start_date').value = params.get('start_date');
    }
    if (params.has('end_date')) {
        document.getElementById('end_date').value = params.get('end_date');
        document.getElementById('filter_end_date').value = params.get('end_date');
    }
    if (params.has('category_id')) {
        document.getElementById('category_id').value = params.get('category_id');
        document.getElementById('filter_category_id').value = params.get('category_id');
    }
    if (params.has('account_id')) {
        document.getElementById('account_id').value = params.get('account_id');
        document.getElementById('filter_account_id').value = params.get('account_id');
    }
    if (params.has('type')) {
        document.getElementById('type').value = params.get('type');
        document.getElementById('filter_type').value = params.get('type');
    }
});
</script>
@endpush
