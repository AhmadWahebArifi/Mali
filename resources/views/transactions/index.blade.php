@extends('layouts.app')

@section('title', 'Transaction Management')

@section('page-title', 'Transactions')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Screen Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="font-h1 text-h1 text-on-surface">Transactions</h1>
            <p class="font-body-md text-body-sm text-on-surface-variant">Review and manage your detailed financial activities.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="flex items-center gap-2 px-4 py-2 border border-outline-variant bg-white text-on-surface rounded-lg font-medium text-sm hover:bg-gray-50 transition-colors">
                <span class="material-symbols-outlined text-sm" data-icon="file_download">file_download</span>
                Export CSV
            </button>
            <a href="{{ route('transactions.create') }}" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-medium text-sm hover:bg-primary-container transition-colors shadow-sm">
                <span class="material-symbols-outlined text-sm" data-icon="add">add</span>
                New Transaction
            </a>
        </div>
    </div>
    
    <!-- Filters Bar -->
    <section class="bg-white p-4 rounded-xl border border-outline-variant mb-6 flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2 min-w-[200px]">
            <span class="material-symbols-outlined text-outline text-lg" data-icon="calendar_today">calendar_today</span>
            <select class="w-full border-none focus:ring-0 text-sm font-medium text-on-surface-variant bg-transparent cursor-pointer">
                <option>Current Month</option>
                <option>Last 30 Days</option>
                <option>Last Quarter</option>
                <option>Custom Range</option>
            </select>
        </div>
        <div class="h-6 w-px bg-outline-variant/50 hidden md:block"></div>
        <div class="flex items-center gap-2 min-w-[160px]">
            <span class="material-symbols-outlined text-outline text-lg" data-icon="account_balance_wallet">account_balance_wallet</span>
            <select class="w-full border-none focus:ring-0 text-sm font-medium text-on-surface-variant bg-transparent cursor-pointer">
                <option>All Accounts</option>
                @foreach($accounts as $account)
                <option>{{ $account->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="h-6 w-px bg-outline-variant/50 hidden md:block"></div>
        <div class="flex items-center gap-2 min-w-[160px]">
            <span class="material-symbols-outlined text-outline text-lg" data-icon="filter_list">filter_list</span>
            <select class="w-full border-none focus:ring-0 text-sm font-medium text-on-surface-variant bg-transparent cursor-pointer">
                <option>All Categories</option>
                @foreach($categories as $category)
                <option>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="ml-auto text-primary text-sm font-semibold hover:underline">Clear all filters</button>
    </section>
    
    <!-- Transactions Table Card -->
    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <!-- Bulk Actions Header (Hidden by default, shown when items selected) -->
        <div class="px-6 py-3 bg-primary-container text-white flex items-center justify-between hidden" id="bulkActions">
            <div class="flex items-center gap-4">
                <span class="font-medium text-sm">3 Transactions selected</span>
                <div class="w-px h-4 bg-white/30"></div>
                <button class="flex items-center gap-1 text-sm font-semibold hover:opacity-80">
                    <span class="material-symbols-outlined text-base" data-icon="delete">delete</span>
                    Delete
                </button>
                <button class="flex items-center gap-1 text-sm font-semibold hover:opacity-80">
                    <span class="material-symbols-outlined text-base" data-icon="drive_file_move">drive_file_move</span>
                    Change Category
                </button>
            </div>
            <button class="text-sm font-semibold opacity-80 hover:opacity-100">Cancel</button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-left border-b border-outline-variant">
                        <th class="py-4 px-6 w-10">
                            <input class="rounded border-outline-variant text-primary focus:ring-primary h-4 w-4" type="checkbox" id="selectAll">
                        </th>
                        <th class="py-4 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Date</th>
                        <th class="py-4 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Description</th>
                        <th class="py-4 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Category</th>
                        <th class="py-4 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Account</th>
                        <th class="py-4 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider text-right">Amount</th>
                        <th class="py-4 px-6 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-surface-container-low/50 transition-colors group">
                        <td class="py-4 px-6">
                            <input class="rounded border-outline-variant text-primary focus:ring-primary h-4 w-4 transaction-checkbox" type="checkbox" value="{{ $transaction->id }}">
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-data-mono text-data-mono text-on-surface">{{ $transaction->date->format('M d, Y') }}</div>
                            <div class="text-[10px] text-on-surface-variant font-medium">{{ $transaction->date->format('h:i A') }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-medium text-on-surface text-sm">{{ $transaction->description }}</div>
                            <div class="text-xs text-on-surface-variant">Transaction ID #{{ $transaction->id }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-2 py-1 {{ $transaction->category->type === 'income' ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container-highest text-on-surface-variant' }} text-[11px] font-bold rounded uppercase">
                                {{ $transaction->category->name }}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-base text-primary" data-icon="account_balance">account_balance</span>
                                <span class="text-xs font-medium text-on-surface-variant">{{ $transaction->account->name }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="font-data-mono {{ $transaction->type === 'income' ? 'text-secondary' : 'text-error' }} font-bold">
                                {{ $transaction->type === 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="p-1 text-outline opacity-0 group-hover:opacity-100 hover:text-on-surface transition-all" onclick="showTransactionMenu({{ $transaction->id }})">
                                <span class="material-symbols-outlined" data-icon="more_vert">more_vert</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center">
                            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">receipt_long</span>
                            <p class="text-gray-500 text-lg mb-2">No transactions found</p>
                            <p class="text-gray-400">Start by adding your first transaction</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-outline-variant flex items-center justify-between">
            <div class="text-xs text-on-surface-variant font-medium">
                Showing <span class="text-on-surface">1 - {{ $transactions->count() }}</span> of <span class="text-on-surface">{{ $totalTransactions }}</span> transactions
            </div>
            <div class="flex items-center gap-1">
                <button class="p-2 border border-outline-variant rounded hover:bg-surface-container-low transition-colors disabled:opacity-30" {{ $transactions->currentPage() == 1 ? 'disabled' : '' }}>
                    <span class="material-symbols-outlined text-lg leading-none" data-icon="chevron_left">chevron_left</span>
                </button>
                @for($i = 1; $i <= min(5, $transactions->lastPage()); $i++)
                <button class="w-8 h-8 flex items-center justify-center text-sm {{ $transactions->currentPage() == $i ? 'font-bold bg-primary text-white' : 'font-medium text-on-surface-variant hover:bg-surface-container-low' }} rounded">
                    {{ $i }}
                </button>
                @endfor
                @if($transactions->lastPage() > 5)
                <div class="px-2 text-on-surface-variant">...</div>
                <button class="w-8 h-8 flex items-center justify-center text-sm font-medium text-on-surface-variant hover:bg-surface-container-low rounded">
                    {{ $transactions->lastPage() }}
                </button>
                @endif
                <button class="p-2 border border-outline-variant rounded hover:bg-surface-container-low transition-colors" {{ $transactions->currentPage() == $transactions->lastPage() ? 'disabled' : '' }}>
                    <span class="material-symbols-outlined text-lg leading-none" data-icon="chevron_right">chevron_right</span>
                </button>
            </div>
        </div>
    </div>
</main>

<!-- FAB for Desktop Contextual Action -->
<div class="fixed bottom-8 right-8 hidden md:block">
    <a href="{{ route('transactions.create') }}" class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center shadow-xl shadow-primary/40 hover:scale-105 active:scale-95 transition-all group overflow-hidden">
        <span class="material-symbols-outlined text-3xl" data-icon="add">add</span>
        <div class="absolute right-full mr-4 bg-inverse-surface text-inverse-on-surface py-2 px-4 rounded-lg text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">Quick Add Transaction</div>
    </a>
</div>
@endsection

@push('scripts')
<script>
function openAddTransactionModal() {
    // Implementation for add transaction modal
    console.log('Opening add transaction modal');
}

function showTransactionMenu(id) {
    // Implementation for transaction menu
    console.log('Showing menu for transaction:', id);
}

// Select all checkbox functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.transaction-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    toggleBulkActions();
});

// Individual checkbox functionality
document.querySelectorAll('.transaction-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', toggleBulkActions);
});

function toggleBulkActions() {
    const checkedBoxes = document.querySelectorAll('.transaction-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    
    if (checkedBoxes.length > 0) {
        bulkActions.classList.remove('hidden');
        bulkActions.querySelector('span.font-medium.text-sm').textContent = `${checkedBoxes.length} Transaction${checkedBoxes.length > 1 ? 's' : ''} selected`;
    } else {
        bulkActions.classList.add('hidden');
    }
}
</script>
@endpush
