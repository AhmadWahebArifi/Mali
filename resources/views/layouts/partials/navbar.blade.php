<!-- Top Navigation Bar (Mobile & Tablet) -->
<header class="sticky top-0 flex justify-between items-center px-4 md:px-6 h-16 w-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 z-40 md:pl-72">
    <div class="flex items-center gap-4">
        <button class="md:hidden p-2" onclick="toggleMobileSidebar()">
            <span class="material-symbols-outlined" data-icon="menu">menu</span>
        </button>
        <h2 class="text-lg font-bold tracking-tighter text-gray-900 dark:text-white font-h2">@yield('page-title', 'Dashboard')</h2>
    </div>
    <div class="flex items-center gap-2">
        <button class="p-2 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-full transition-colors">
            <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
        </button>
        <button class="p-2 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-full transition-colors">
            <span class="material-symbols-outlined" data-icon="help">help</span>
        </button>
        <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden ml-2">
            <img alt="User avatar" class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=004ccd&color=fff" />
        </div>
    </div>
</header>
