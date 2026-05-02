<!-- Desktop Sidebar -->
<aside class="w-64 flex-col fixed left-0 top-0 h-full py-6 px-4 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 hidden md:flex z-50">
    <div class="flex items-center gap-3 mb-10 px-2">
        <div class="w-10 h-10 bg-primary-container flex items-center justify-center rounded-xl">
            <span class="material-symbols-outlined text-white" data-icon="account_balance">account_balance</span>
        </div>
        <div>
            <h1 class="text-xl font-black text-blue-600 dark:text-blue-400 leading-tight">BawarFinTrack</h1>
            <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold">Enterprise Finance</p>
        </div>
    </div>
    
    <nav class="flex-1 space-y-1">
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('dashboard') }}">
            <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
            Dashboard
        </a>
        @if(auth()->user()->email === 'admin@mali.com')
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('admin.accounts.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('admin.accounts.index') }}">
            <span class="material-symbols-outlined" data-icon="account_balance">account_balance</span>
            Accounts
        </a>
        @endif
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('transactions.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('transactions.index') }}">
            <span class="material-symbols-outlined" data-icon="swap_horiz">swap_horiz</span>
            Transactions
        </a>
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('categories.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('categories.index') }}">
            <span class="material-symbols-outlined" data-icon="category">category</span>
            Categories
        </a>
        @if(auth()->check() && auth()->user()->email === 'admin@mali.com')
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('budgets.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('budgets.index') }}">
            <span class="material-symbols-outlined" data-icon="account_balance_wallet">account_balance_wallet</span>
            Budget Assignment
        </a>
        @endif
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('reports.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('reports.index') }}">
            <span class="material-symbols-outlined" data-icon="bar_chart">bar_chart</span>
            Reports
        </a>
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('settings.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('settings.index') }}">
            <span class="material-symbols-outlined" data-icon="settings">settings</span>
            Settings
        </a>
        @if(auth()->check() && auth()->user()->email === 'admin@mali.com')
        <a class="flex items-center gap-3 px-3 py-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('audit-logs.*') ? 'bg-gray-100 dark:bg-gray-800' : '' }}" href="{{ route('audit-logs.index') }}">
            <span class="material-symbols-outlined" data-icon="fact_check">fact_check</span>
            Audit Logs
        </a>
        <a class="flex items-center gap-3 px-3 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200 ease-in-out font-medium text-sm {{ request()->routeIs('admin.*') ? 'bg-red-50 dark:bg-red-900/20' : '' }}" href="{{ route('admin.users.index') }}">
            <span class="material-symbols-outlined" data-icon="admin_panel_settings">admin_panel_settings</span>
            Admin Panel
        </a>
        @endif
    </nav>
</aside>
