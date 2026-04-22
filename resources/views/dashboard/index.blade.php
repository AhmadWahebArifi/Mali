@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<!-- Dashboard Content -->
<div class="p-4 md:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">
    <!-- Bento Grid - Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Balance Card -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6 flex flex-col justify-between overflow-hidden relative min-h-[240px]">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <span class="material-symbols-outlined text-[120px]" data-icon="account_balance_wallet">account_balance_wallet</span>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="font-label-caps text-gray-500 uppercase">Total Net Worth</span>
                    <span class="flex items-center gap-1 text-secondary font-semibold text-sm">
                        <span class="material-symbols-outlined text-sm" data-icon="trending_up">trending_up</span>
                        +12.4%
                    </span>
                </div>
                <h2 class="font-display-financial text-gray-900">${{ number_format($totalBalance, 2) }}</h2>
                <div class="flex gap-4 mt-6">
                    <div class="flex-1 bg-secondary/5 rounded-lg p-3 border border-secondary/10">
                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Monthly Income</p>
                        <p class="text-secondary font-bold text-lg">+${{ number_format($monthlyIncome, 2) }}</p>
                    </div>
                    <div class="flex-1 bg-tertiary/5 rounded-lg p-3 border border-tertiary/10">
                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Monthly Expenses</p>
                        <p class="text-tertiary font-bold text-lg">-${{ number_format($monthlyExpenses, 2) }}</p>
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
                        <p class="text-[11px] text-gray-400">Account</p>
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
                            <p class="text-[11px] text-gray-400">{{ $transaction->date->format('M d, Y') }} • {{ $transaction->category->name }}</p>
                        </div>
                    </div>
                    <p class="font-data-mono {{ $transaction->type === 'income' ? 'text-secondary' : 'text-tertiary' }}">
                        {{ $transaction->type === 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                    </p>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2">receipt_long</span>
                    <p>No transactions yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAddTransactionModal(type) {
    // Implementation for add transaction modal
    console.log('Opening add transaction modal for type:', type);
}

function openAddAccountModal() {
    // Implementation for add account modal
    console.log('Opening add account modal');
}
</script>
@endpush
