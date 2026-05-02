@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Success Message (using Sweet Alert) -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            BawarFinTrackAlert.success('Success!', '{{ session('success') }}');
        });
    </script>
    @endif
    
    <!-- Bento Grid - Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Balance Card -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-outline-variant p-6 flex flex-col justify-between overflow-hidden relative min-h-[240px] shadow-sm">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <span class="material-symbols-outlined text-[120px]" data-icon="account_balance_wallet">account_balance_wallet</span>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="font-label-caps text-label-caps text-on-surface-variant uppercase">Total Net Worth</span>
                    <span class="flex items-center gap-1 text-secondary font-semibold text-sm">
                        <span class="material-symbols-outlined text-sm" data-icon="trending_up">trending_up</span>
                        +12.4%
                    </span>
                </div>
                <h2 class="font-display-financial text-on-surface">{{ \App\Helpers\FormatHelper::currency($totalNetWorth) }}</h2>
                <div class="flex gap-4 mt-6">
                    <div class="flex-1 bg-secondary/5 rounded-lg p-3 border border-secondary/10">
                        <p class="text-[10px] uppercase font-bold text-on-surface-variant mb-1">Monthly Income</p>
                        <p class="text-secondary font-bold text-lg">+{{ \App\Helpers\FormatHelper::currency($monthlyIncome) }}</p>
                    </div>
                    <div class="flex-1 bg-tertiary/5 rounded-lg p-3 border border-tertiary/10">
                        <p class="text-[10px] uppercase font-bold text-on-surface-variant mb-1">Monthly Expenses</p>
                        <p class="text-tertiary font-bold text-lg">-{{ \App\Helpers\FormatHelper::currency($monthlyExpenses) }}</p>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex gap-3">
                <button onclick="openAddTransactionModal('income')" class="flex-1 py-2.5 bg-secondary text-white rounded-lg font-semibold text-sm flex items-center justify-center gap-2 hover:opacity-90">
                    <span class="material-symbols-outlined text-sm" data-icon="arrow_upward">arrow_upward</span>
                    Add Income
                </button>
                <button onclick="openAddTransactionModal('expense')" class="flex-1 py-2.5 bg-tertiary text-white rounded-lg font-semibold text-sm flex items-center justify-center gap-2 hover:opacity-90">
                    <span class="material-symbols-outlined text-sm" data-icon="arrow_downward">arrow_downward</span>
                    Add Expense
                </button>
                <button class="flex items-center justify-center w-12 h-10 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50">
                    <span class="material-symbols-outlined" data-icon="sync_alt">sync_alt</span>
                </button>
            </div>
        </div>
        
        <!-- Account Cards Stack -->
        <div class="flex flex-col gap-4">
            @forelse($accounts as $account)
            <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center justify-between group hover:border-primary/30 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined" data-icon="account_balance">account_balance</span>
                    </div>
                    <div>
                        <p class="font-body-sm font-semibold text-gray-900">{{ $account->name }}</p>
                        <p class="text-[11px] text-gray-400">Shared by All Users</p>
                    </div>
                </div>
                <p class="font-data-mono text-gray-900 text-right">
                    {{ $account->balance >= 0 ? '$' : '-$' }}{{ number_format(abs($account->balance), 2) }}
                </p>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <span class="material-symbols-outlined text-4xl mb-2">account_balance</span>
                <p>No accounts yet</p>
            </div>
            @endforelse
            
            <button onclick="openAddAccountModal()" class="w-full py-3 border-2 border-dashed border-gray-200 rounded-xl text-gray-400 font-semibold text-sm flex items-center justify-center gap-2 hover:border-gray-300 hover:text-gray-500">
                <span class="material-symbols-outlined" data-icon="add">add</span>
                Link New Account
            </button>
        </div>
    </div>

    <!-- Total Budget Section -->
    @if(isset($totalBudgetAmount))
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6 mb-6">
        @if($totalBudgetAmount > 0)
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-h2 text-blue-900 mb-2">Budget Overview</h3>
                <p class="text-sm text-blue-700">Your assigned budgets and spending status</p>
            </div>
            <div class="flex items-center gap-2">
                @if($overBudgetBudgets->count() > 0)
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                    {{ $overBudgetBudgets->count() }} Over Budget
                </span>
                @endif
                @if($nearLimitBudgets->count() > 0)
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                    {{ $nearLimitBudgets->count() }} Near Limit
                </span>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg p-4 border border-blue-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-blue-600 font-medium">Total Budget</span>
                    <span class="material-symbols-outlined text-blue-500 text-sm">account_balance</span>
                </div>
                <div class="text-2xl font-bold text-blue-900">{{ \App\Helpers\FormatHelper::currency($totalBudgetAmount) }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-blue-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-blue-600 font-medium">Total Spent</span>
                    <span class="material-symbols-outlined text-blue-500 text-sm">shopping_cart</span>
                </div>
                <div class="text-2xl font-bold text-blue-900">{{ \App\Helpers\FormatHelper::currency($totalBudgetSpent) }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-blue-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-blue-600 font-medium">Current Balance</span>
                    <span class="material-symbols-outlined text-blue-500 text-sm">savings</span>
                </div>
                <div class="text-2xl font-bold {{ $totalBudgetBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ \App\Helpers\FormatHelper::currency($totalBudgetBalance) }}
                </div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-blue-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-blue-600 font-medium">Usage</span>
                    <span class="material-symbols-outlined text-blue-500 text-sm">pie_chart</span>
                </div>
                <div class="text-2xl font-bold text-blue-900">{{ round($budgetUsagePercentage, 1) }}%</div>
            </div>
        </div>
        
        <!-- Overall Budget Progress -->
        <div class="bg-white rounded-lg p-4 border border-blue-100">
            <div class="flex justify-between text-sm text-blue-700 mb-2">
                <span>Overall Budget Usage</span>
                <span>{{ round($budgetUsagePercentage, 1) }}% used</span>
            </div>
            <div class="w-full bg-blue-200 rounded-full h-3 mb-2">
                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                     style="width: {{ min(100, $budgetUsagePercentage) }}%"></div>
            </div>
            @if($budgetUsagePercentage >= 100)
            <p class="text-xs text-red-600">⚠️ You have exceeded your total budget!</p>
            @elseif($budgetUsagePercentage >= 80)
            <p class="text-xs text-yellow-600">⚠️ You are approaching your budget limit</p>
            @else
            <p class="text-xs text-green-600">✓ You are within your budget limits</p>
            @endif
        </div>
        @else
        <div class="text-center py-8">
            <span class="material-symbols-outlined text-4xl text-blue-300 mb-4">account_balance</span>
            <h3 class="font-h2 text-blue-900 mb-2">No Budgets Assigned</h3>
            <p class="text-sm text-blue-700 mb-6">You don't have any budgets assigned yet. Contact your admin to get budget limits set up.</p>
            @if(auth()->user()->email === 'admin@mali.com')
            <a href="{{ route('budgets.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                <span class="material-symbols-outlined" data-icon="add">add</span>
                Assign Budget
            </a>
            @endif
        </div>
        @endif
    </div>
    @endif

    <!-- Budget Overview Section -->
    @php
        $userBudgets = \App\Models\Budget::where('user_id', auth()->id())
            ->where('is_active', true)
            ->with('category')
            ->get();
    @endphp
    @if($userBudgets->count() > 0)
    <div class="mb-8">
        <h2 class="font-h2 text-gray-900 mb-4">Budget Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($userBudgets as $budget)
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900">{{ $budget->name }}</h3>
                    @if($budget->category)
                    <span class="text-xs px-2 py-1 rounded-full 
                        @if($budget->category->type === 'income') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ $budget->category->name }}
                    </span>
                    @else
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-800">
                        All Categories
                    </span>
                    @endif
                </div>
                
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Budget:</span>
                        <span class="font-medium">{{ \App\Helpers\FormatHelper::currency($budget->amount) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Spent:</span>
                        <span class="font-medium {{ $budget->is_over_budget ? 'text-red-600' : 'text-gray-900' }}">
                            {{ \App\Helpers\FormatHelper::currency($budget->spent) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Remaining:</span>
                        <span class="font-medium {{ $budget->is_over_budget ? 'text-red-600' : ($budget->is_near_limit ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ \App\Helpers\FormatHelper::currency($budget->remaining) }}
                        </span>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-3">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>{{ round($budget->percentage_used, 1) }}% used</span>
                        <span>{{ ucfirst($budget->period) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-300
                            @if($budget->is_over_budget) bg-red-600
                            @elseif($budget->is_near_limit) bg-yellow-600
                            @else bg-green-600 @endif"
                            style="width: {{ min(100, $budget->percentage_used) }}%">
                        </div>
                    </div>
                </div>
                
                @if($budget->is_over_budget)
                <div class="mt-3 text-xs text-red-600 bg-red-50 rounded p-2">
                    ⚠️ Budget exceeded by {{ \App\Helpers\FormatHelper::currency(abs($budget->remaining)) }}
                </div>
                @elseif($budget->is_near_limit)
                <div class="mt-3 text-xs text-yellow-600 bg-yellow-50 rounded p-2">
                    ⚠️ {{ round(100 - $budget->percentage_used, 1) }}% remaining
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- Bento Grid - Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <!-- Activity Chart -->
        <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="font-h2 text-gray-900">Monthly Analysis</h3>
                    <p class="text-sm text-gray-500">Income vs Expenses trends</p>
                </div>
                <select class="bg-gray-50 border border-gray-200 rounded-lg text-xs font-semibold px-3 py-1.5 focus:ring-primary">
                    <option>Last 6 Months</option>
                    <option>Year to Date</option>
                </select>
            </div>
            
            <!-- Simplified Visual Representation of a Chart -->
            <div class="h-64 flex items-end gap-3 md:gap-6 relative group">
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                    <div class="border-b border-gray-100 w-full h-0"></div>
                    <div class="border-b border-gray-100 w-full h-0"></div>
                    <div class="border-b border-gray-100 w-full h-0"></div>
                    <div class="border-b border-gray-100 w-full h-0"></div>
                </div>
                
                <!-- Bar Pairs -->
                @foreach($monthlyData as $index => $month)
                <div class="flex-1 flex flex-col justify-end gap-1 {{ $index === 3 ? 'group/bar' : '' }}">
                    <div class="h-[{{ $month['income_percent'] }}%] {{ $index === 3 ? 'bg-secondary/80 ring-2 ring-secondary ring-offset-2' : 'bg-secondary/20' }} rounded-t-sm w-full"></div>
                    <div class="h-[{{ $month['expense_percent'] }}%] {{ $index === 3 ? 'bg-tertiary/80' : 'bg-tertiary/20' }} rounded-t-sm w-full"></div>
                    <p class="text-[10px] text-center mt-2 {{ $index === 3 ? 'text-gray-900 font-bold' : 'text-gray-400' }}">{{ $month['month'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 flex flex-col">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-h2 text-gray-900">Recent Transactions</h3>
                <a href="{{ route('transactions.index') }}" class="text-primary font-semibold text-sm hover:underline">View All</a>
            </div>
            <div class="flex-1 overflow-y-auto max-h-[380px] divide-y divide-gray-50 px-6">
                @forelse($recentTransactions as $transaction)
                <div class="py-4 flex items-center justify-between hover:bg-gray-50 transition-colors cursor-pointer rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 {{ $transaction->category->type === 'income' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }} rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined" data-icon="{{ $transaction->category->icon ?? 'receipt_long' }}">
                                {{ $transaction->category->icon ?? 'receipt_long' }}
                            </span>
                        </div>
                        <div>
                            <p class="font-body-sm font-semibold text-gray-900">{{ $transaction->description }}</p>
                            <p class="text-[11px] text-gray-400">{{ \App\Helpers\FormatHelper::date($transaction->date, 'M d, Y') }} • {{ $transaction->category->name }}</p>
                        </div>
                    </div>
                    <p class="font-data-mono {{ $transaction->type === 'income' ? 'text-secondary' : 'text-tertiary' }}">
                        {{ $transaction->type === 'income' ? '+' : '-' }}{{ \App\Helpers\FormatHelper::currency($transaction->amount) }}
                    </p>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2">add_circle</span>
                    <p class="font-medium mb-2">Start tracking your finances</p>
                    <p class="text-sm">Add your first income or expense transaction to get started</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Add Transaction Modal -->
<div id="addTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 relative">
        <div class="flex items-center justify-between mb-6">
            <h2 id="modalTitle" class="font-h1 text-h1 text-on-surface">Add Transaction</h2>
            <button onclick="closeAddTransactionModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <form id="transactionForm" action="{{ route('transactions.store') }}" method="POST">
            @csrf
            <input type="hidden" id="transaction_type" name="type" value="">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-2">Description</label>
                    <input type="text" name="description" required 
                           class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="Enter description">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-2">Amount</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required 
                           class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           placeholder="0.00">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-2">Account</label>
                    <select name="account_id" required 
                            class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-2">Category</label>
                    <select name="category_id" required 
                            class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Select Category</option>
                        @foreach(App\Models\Category::all() as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-on-surface-variant mb-2">Date</label>
                    <input type="date" name="date" required 
                           class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                           value="{{ now()->format('Y-m-d') }}">
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeAddTransactionModal()" 
                        class="flex-1 h-11 border border-outline-variant text-on-surface font-body-md font-semibold rounded-lg hover:bg-surface-container-low transition-all">
                    Cancel
                </button>
                <button type="submit" id="submitButton"
                        class="flex-1 h-11 bg-primary text-on-primary font-body-md font-semibold rounded-lg hover:bg-primary-container active:scale-[0.98] transition-all shadow-sm">
                    Add Transaction
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAddTransactionModal(type) {
    const modal = document.getElementById('addTransactionModal');
    const typeInput = document.getElementById('transaction_type');
    const modalTitle = document.getElementById('modalTitle');
    const submitButton = document.getElementById('submitButton');
    
    typeInput.value = type;
    
    if (type === 'income') {
        modalTitle.textContent = 'Add Income';
        submitButton.className = 'w-full h-11 bg-secondary text-on-secondary font-body-md font-semibold rounded-lg hover:bg-secondary-container active:scale-[0.98] transition-all shadow-sm';
        submitButton.textContent = 'Add Income';
    } else {
        modalTitle.textContent = 'Add Expense';
        submitButton.className = 'w-full h-11 bg-tertiary text-on-tertiary font-body-md font-semibold rounded-lg hover:bg-tertiary-container active:scale-[0.98] transition-all shadow-sm';
        submitButton.textContent = 'Add Expense';
    }
    
    modal.classList.remove('hidden');
}

function closeAddTransactionModal() {
    const modal = document.getElementById('addTransactionModal');
    modal.classList.add('hidden');
    document.getElementById('transactionForm').reset();
}

// Handle form submission with success feedback
document.getElementById('transactionForm').addEventListener('submit', function(e) {
    const submitButton = document.getElementById('submitButton');
    const originalText = submitButton.textContent;
    const type = document.getElementById('transaction_type').value;
    const amount = document.querySelector('input[name="amount"]').value;
    
    // Show loading state
    submitButton.disabled = true;
    submitButton.textContent = 'Adding...';
    
    // Let the form submit normally to avoid CSRF issues
    // The success message will be shown on page reload via session flash
});
</script>
@endpush
