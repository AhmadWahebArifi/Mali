<!-- Bottom Navigation Bar (Mobile only) -->
<nav class="fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-safe pt-2 h-16 bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg border-t border-gray-200 dark:border-gray-800 md:hidden">
    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 scale-95 active:scale-90 transition-transform {{ request()->routeIs('dashboard') ? 'text-blue-600 dark:text-blue-400' : '' }}">
        <span class="material-symbols-outlined" data-icon="home">home</span>
        <span class="text-[10px] font-semibold uppercase tracking-wider">Home</span>
    </a>
    @if(auth()->user()->email === 'admin@mali.com')
    <a href="{{ route('admin.accounts.index') }}" class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 scale-95 active:scale-90 transition-transform {{ request()->routeIs('admin.accounts.*') ? 'text-blue-600 dark:text-blue-400' : '' }}">
        <span class="material-symbols-outlined text-lg" data-icon="account_balance">account_balance</span>
        <span class="text-xs mt-1">Accounts</span>
    </a>
    @endif
    <button onclick="openAddTransactionModal()" class="flex flex-col items-center justify-center text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/30 rounded-xl p-1 scale-95 active:scale-90 transition-transform">
        <span class="material-symbols-outlined" data-icon="add_circle">add_circle</span>
        <span class="text-[10px] font-semibold uppercase tracking-wider">Add</span>
    </button>
    <a href="{{ route('transactions.index') }}" class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 scale-95 active:scale-90 transition-transform {{ request()->routeIs('transactions.*') ? 'text-blue-600 dark:text-blue-400' : '' }}">
        <span class="material-symbols-outlined" data-icon="receipt_long">receipt_long</span>
        <span class="text-[10px] font-semibold uppercase tracking-wider">Log</span>
    </a>
    <a href="{{ route('reports.index') }}" class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 scale-95 active:scale-90 transition-transform {{ request()->routeIs('reports.*') ? 'text-blue-600 dark:text-blue-400' : '' }}">
        <span class="material-symbols-outlined" data-icon="analytics">analytics</span>
        <span class="text-[10px] font-semibold uppercase tracking-wider">Reports</span>
    </a>
</nav>
