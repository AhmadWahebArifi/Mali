<!-- Desktop Sidebar -->
<aside class="w-64 flex-col fixed left-0 top-0 h-full py-6 px-4 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 hidden md:flex z-50">
    <div class="flex items-center gap-3 mb-10 px-2">
        <div class="w-10 h-10 bg-primary-container flex items-center justify-center rounded-xl">
            <span class="material-symbols-outlined text-white" data-icon="account_balance">account_balance</span>
        </div>
        <div>
            <h1 class="text-xl font-black text-blue-600 dark:text-blue-400 leading-tight">FinTrack</h1>
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Enterprise Finance</p>
        </div>
    </div>
    
    <nav class="flex-1 space-y-1">
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('dashboard') }}">
            <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
            Dashboard
        </a>
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('accounts.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('accounts.index') }}">
            <span class="material-symbols-outlined" data-icon="account_balance">account_balance</span>
            Accounts
        </a>
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('transactions.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('transactions.index') }}">
            <span class="material-symbols-outlined" data-icon="swap_horiz">swap_horiz</span>
            Transactions
        </a>
        <a class="flex items-center gap-3 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-r-4 border-blue-600 transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('categories.*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border-r-4 border-blue-600' : '' }}" href="{{ route('categories.index') }}">
            <span class="material-symbols-outlined" data-icon="category">category</span>
            Categories
        </a>
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('reports.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('reports.index') }}">
            <span class="material-symbols-outlined" data-icon="bar_chart">bar_chart</span>
            Reports
        </a>
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('settings.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('settings.index') }}">
            <span class="material-symbols-outlined" data-icon="settings">settings</span>
            Settings
        </a>
    </nav>
    
    <button class="mt-auto w-full bg-primary text-white py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-primary/20 hover:bg-primary-container transition-all">
        <span class="material-symbols-outlined" data-icon="add">add</span>
        Add Transaction
    </button>
</aside>
