@extends('layouts.app')

@section('title', 'Accounts Management')

@section('page-title', 'Accounts')

@section('content')
<div class="p-4 md:p-8 max-w-7xl mx-auto">
    <!-- Summary Stats (Bento Style) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-outline-variant p-6 rounded-xl flex flex-col gap-1 shadow-sm">
            <p class="font-label-caps text-label-caps text-on-surface-variant">TOTAL BALANCE</p>
            <p class="font-h1 text-h1 text-primary">${{ number_format($totalBalance, 2) }}</p>
            <div class="flex items-center gap-1 mt-2 text-secondary">
                <span class="material-symbols-outlined text-sm">trending_up</span>
                <span class="text-xs font-bold">+2.4% this month</span>
            </div>
        </div>
        
        <div class="bg-white border border-outline-variant p-6 rounded-xl flex flex-col justify-center items-center gap-3 shadow-sm group hover:border-primary cursor-pointer transition-all">
            <div class="w-12 h-12 bg-primary-container rounded-full flex items-center justify-center text-on-primary-container group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">add</span>
            </div>
            <p class="font-label-caps text-label-caps text-primary">ADD NEW ACCOUNT</p>
        </div>
        
        <div class="md:col-span-2 bg-gradient-to-br from-primary to-primary-fixed-variant p-6 rounded-xl text-on-primary flex flex-col justify-between shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <p class="font-label-caps text-label-caps opacity-80">QUICK TIP</p>
                <p class="font-body-md text-body-md mt-2 max-w-xs">Connecting your high-interest savings account could increase your projected annual yield by $120.</p>
            </div>
            <button class="relative z-10 mt-4 bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 rounded-lg py-2 px-4 text-sm font-semibold self-start transition-colors">
                Learn More
            </button>
            <span class="absolute -right-8 -bottom-8 material-symbols-outlined text-9xl opacity-10">lightbulb</span>
        </div>
    </div>
    
    <!-- Accounts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($accounts as $account)
        <div class="bg-white border border-outline-variant rounded-xl overflow-hidden shadow-sm flex flex-col">
            <div class="h-2 w-full {{ $account->balance >= 0 ? 'bg-primary' : 'bg-tertiary' }}"></div>
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg {{ $account->balance >= 0 ? 'bg-primary-container text-on-primary-container' : 'bg-tertiary-container text-on-tertiary' }} flex items-center justify-center">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">{{ $account->balance >= 0 ? 'account_balance' : 'credit_card' }}</span>
                        </div>
                        <div>
                            <p class="font-body-md font-bold text-on-surface">{{ $account->name }}</p>
                            <p class="text-xs text-on-surface-variant">Account • •••• {{ str_pad($account->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <button class="p-2 hover:bg-surface-container rounded-lg transition-colors text-outline">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </button>
                        <button class="p-2 hover:bg-error-container hover:text-error rounded-lg transition-colors text-outline">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </div>
                </div>
                <div class="mt-auto">
                    <p class="font-label-caps text-label-caps text-on-surface-variant">{{ $account->balance >= 0 ? 'CURRENT BALANCE' : 'OUTSTANDING DEBT' }}</p>
                    <p class="font-h2 text-h2 {{ $account->balance >= 0 ? 'text-on-surface' : 'text-tertiary' }}">
                        {{ $account->balance >= 0 ? '$' : '-$' }}{{ number_format(abs($account->balance), 2) }}
                    </p>
                </div>
            </div>
        </div>
        @empty
        <div class="md:col-span-3 text-center py-12">
            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">account_balance</span>
            <p class="text-gray-500 text-lg mb-2">No accounts yet</p>
            <p class="text-gray-400">Add your first account to start tracking your finances</p>
        </div>
        @endforelse
        
        <!-- Empty State / Add Card -->
        <div class="border-2 border-dashed border-outline-variant rounded-xl p-6 flex flex-col items-center justify-center gap-4 hover:border-primary hover:bg-surface-container-low cursor-pointer transition-all group">
            <div class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-outline group-hover:text-primary">
                <span class="material-symbols-outlined text-3xl">add</span>
            </div>
            <div class="text-center">
                <p class="font-body-md font-bold text-on-surface-variant group-hover:text-primary">Add Another Account</p>
                <p class="text-xs text-outline">Connect via Plaid or add manually</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAddAccountModal() {
    // Implementation for add account modal
    console.log('Opening add account modal');
}

function editAccount(id) {
    // Implementation for edit account
    console.log('Editing account:', id);
}

function deleteAccount(id) {
    // Implementation for delete account
    if (confirm('Are you sure you want to delete this account?')) {
        console.log('Deleting account:', id);
    }
}
</script>
@endpush
