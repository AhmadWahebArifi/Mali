<!-- Top Navigation Bar (Mobile & Tablet) -->
<header class="sticky top-0 flex justify-between items-center px-4 md:px-6 h-16 w-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 z-40 md:pl-72">
    <div class="flex items-center gap-4">
        <button class="md:hidden p-2" onclick="toggleMobileSidebar()">
            <span class="material-symbols-outlined" data-icon="menu">menu</span>
        </button>
        <h2 class="text-lg font-bold tracking-tighter text-gray-900 dark:text-white font-h2">@yield('page-title', 'Dashboard')</h2>
    </div>
    <div class="flex items-center gap-2">
        <!-- Notifications -->
        <a href="{{ route('notifications.index') }}" class="p-2 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-full transition-colors relative">
            <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
            @if(auth()->user()->unreadNotifications()->count() > 0)
            <span class="absolute top-1 right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center">
                {{ auth()->user()->unreadNotifications()->count() > 9 ? '9+' : auth()->user()->unreadNotifications()->count() }}
            </span>
            @endif
        </a>
        
        <!-- FAQ -->
        <a href="{{ route('faq.index') }}" class="p-2 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-full transition-colors">
            <span class="material-symbols-outlined" data-icon="help">help</span>
        </a>
        
        <!-- Profile Dropdown -->
        <div class="relative">
            <button onclick="toggleProfileDropdown()" class="flex items-center gap-2 p-1 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-full transition-colors">
                <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
                    <img alt="User avatar" class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=004ccd&color=fff" />
                </div>
                <span class="material-symbols-outlined text-gray-500 text-sm">expand_more</span>
            </button>
            
            <!-- Dropdown Menu -->
            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <span class="material-symbols-outlined text-lg">settings</span>
                    Settings
                </a>
                <a href="{{ route('profile.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <span class="material-symbols-outlined text-lg">person</span>
                    Profile
                </a>
                <hr class="my-2 border-gray-200 dark:border-gray-700">
                <form method="POST" action="{{ route('logout') }}" class="px-4">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded px-2 py-1 w-full text-left">
                        <span class="material-symbols-outlined text-lg">logout</span>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
