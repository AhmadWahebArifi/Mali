@extends('layouts.app')

@section('title', 'Add Transaction')

@section('page-title', 'Add Transaction')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <div class="max-w-2xl mx-auto">
        <!-- Back Action -->
        <a href="{{ route('transactions.index') }}" class="flex items-center gap-2 text-gray-500 hover:text-primary transition-colors mb-4 font-medium text-sm group">
            <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform" data-icon="arrow_back">arrow_back</span>
            Back to Transactions
        </a>
        
        <!-- Professional Card Container -->
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <!-- Form Header -->
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                <h1 class="font-h1 text-h1 text-on-surface">Add Transaction</h1>
                <span class="px-2 py-1 bg-surface-container text-primary text-[10px] uppercase font-bold tracking-widest rounded-full">New Entry</span>
            </div>
            
            <!-- Form Content -->
            <form action="{{ route('transactions.store') }}" method="POST" class="p-8 space-y-8">
                @csrf
                
                <!-- Type Selector (Income/Expense Toggle) -->
                <div class="space-y-2">
                    <label class="font-label-caps text-label-caps text-on-surface-variant block">TRANSACTION TYPE</label>
                    <div class="grid grid-cols-2 p-1 bg-surface-container-low rounded-xl border border-outline-variant">
                        <button type="button" onclick="setTransactionType('expense')" id="expenseBtn" class="flex items-center justify-center gap-2 py-3 rounded-lg text-sm font-semibold transition-all bg-white text-error shadow-sm">
                            <span class="material-symbols-outlined text-[20px]" data-icon="arrow_outward">arrow_outward</span>
                            Expense
                        </button>
                        <button type="button" onclick="setTransactionType('income')" id="incomeBtn" class="flex items-center justify-center gap-2 py-3 rounded-lg text-sm font-semibold text-gray-500 hover:bg-white/50 transition-all">
                            <span class="material-symbols-outlined text-[20px]" data-icon="call_received">call_received</span>
                            Income
                        </button>
                    </div>
                    <input type="hidden" name="type" id="transactionType" value="expense" required>
                </div>
                
                <!-- Amount Input -->
                <div class="space-y-2">
                    <label class="font-label-caps text-label-caps text-on-surface-variant block" for="amount">AMOUNT</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-4 font-display-financial text-on-surface-variant opacity-50">$</span>
                        <input class="w-full font-display-financial text-display-financial pl-12 pr-4 py-6 border-2 border-surface-container-high bg-surface-bright rounded-xl focus:border-primary focus:ring-0 transition-colors text-right tabular-nums" 
                               id="amount" 
                               name="amount" 
                               placeholder="0.00" 
                               step="0.01" 
                               type="number" 
                               required>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category Dropdown -->
                    <div class="space-y-2">
                        <label class="font-label-caps text-label-caps text-on-surface-variant block" for="category">CATEGORY</label>
                        <div class="relative">
                            <select class="w-full bg-surface-bright border border-outline-variant rounded-xl px-4 py-3 appearance-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-md" 
                                    id="category" 
                                    name="category_id" 
                                    required>
                                <option disabled selected value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-outline" data-icon="expand_more">expand_more</span>
                        </div>
                    </div>
                    
                    <!-- Account Dropdown -->
                    <div class="space-y-2">
                        <label class="font-label-caps text-label-caps text-on-surface-variant block" for="account">ACCOUNT</label>
                        <div class="relative">
                            <select class="w-full bg-surface-bright border border-outline-variant rounded-xl px-4 py-3 appearance-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-md" 
                                    id="account" 
                                    name="account_id" 
                                    required>
                                <option disabled selected value="">Select Account</option>
                                @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-outline" data-icon="expand_more">expand_more</span>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date Picker -->
                    <div class="space-y-2">
                        <label class="font-label-caps text-label-caps text-on-surface-variant block" for="date">TRANSACTION DATE</label>
                        <div class="relative">
                            <input class="w-full bg-surface-bright border border-outline-variant rounded-xl px-4 py-3 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-md" 
                                   id="date" 
                                   name="date" 
                                   type="date" 
                                   value="{{ now()->format('Y-m-d') }}" 
                                   required>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-outline" data-icon="calendar_today">calendar_today</span>
                        </div>
                    </div>
                    
                    <!-- Tags/Receipt (Bonus Visual Elements) -->
                    <div class="space-y-2">
                        <label class="font-label-caps text-label-caps text-on-surface-variant block">ATTACHMENT</label>
                        <button type="button" class="w-full border-2 border-dashed border-outline-variant rounded-xl py-3 flex items-center justify-center gap-2 text-sm text-outline hover:bg-surface transition-colors">
                            <span class="material-symbols-outlined text-[20px]" data-icon="upload_file">upload_file</span>
                            Add Receipt
                        </button>
                    </div>
                </div>
                
                <!-- Description Textarea -->
                <div class="space-y-2">
                    <label class="font-label-caps text-label-caps text-on-surface-variant block" for="description">DESCRIPTION</label>
                    <textarea class="w-full bg-surface-bright border border-outline-variant rounded-xl px-4 py-4 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-md" 
                              id="description" 
                              name="description" 
                              placeholder="Add a note or details about this transaction..." 
                              rows="3"></textarea>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex flex-col md:flex-row gap-4 pt-6">
                    <button type="submit" class="flex-1 bg-primary text-white py-4 rounded-xl font-semibold shadow-xl shadow-primary/20 hover:bg-on-primary-fixed-variant transition-all flex items-center justify-center gap-2 min-h-[44px]">
                        <span class="material-symbols-outlined" data-icon="check">check</span>
                        Save Transaction
                    </button>
                    <a href="{{ route('transactions.index') }}" class="flex-1 bg-surface-container text-on-surface py-4 rounded-xl font-semibold hover:bg-surface-container-highest transition-all min-h-[44px] flex items-center justify-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Footer Summary Info -->
        <div class="mt-6 p-4 bg-surface-container-low rounded-xl border border-outline-variant flex items-start gap-4">
            <span class="material-symbols-outlined text-primary p-2 bg-white rounded-lg shadow-sm" data-icon="info">info</span>
            <div>
                <p class="text-sm font-medium text-on-surface">Reporting Transparency</p>
                <p class="text-[12px] text-on-surface-variant leading-relaxed">Transactions are automatically synced with your general ledger and will be visible in your monthly Reports immediately after saving.</p>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
function setTransactionType(type) {
    const transactionType = document.getElementById('transactionType');
    const expenseBtn = document.getElementById('expenseBtn');
    const incomeBtn = document.getElementById('incomeBtn');
    
    if (type === 'expense') {
        transactionType.value = 'expense';
        expenseBtn.classList.add('bg-white', 'text-error', 'shadow-sm');
        expenseBtn.classList.remove('text-gray-500', 'hover:bg-white/50');
        incomeBtn.classList.remove('bg-white', 'text-secondary', 'shadow-sm');
        incomeBtn.classList.add('text-gray-500', 'hover:bg-white/50');
    } else {
        transactionType.value = 'income';
        incomeBtn.classList.add('bg-white', 'text-secondary', 'shadow-sm');
        incomeBtn.classList.remove('text-gray-500', 'hover:bg-white/50');
        expenseBtn.classList.remove('bg-white', 'text-error', 'shadow-sm');
        expenseBtn.classList.add('text-gray-500', 'hover:bg-white/50');
    }
}

// Auto-format amount input
document.getElementById('amount').addEventListener('input', function(e) {
    if (e.target.value < 0) {
        e.target.value = 0;
    }
});
</script>
@endpush
