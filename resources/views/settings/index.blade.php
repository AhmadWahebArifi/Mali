@extends('layouts.app')

@section('title', 'Settings')

@section('page-title', 'Settings')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="font-h1 text-h1 text-on-surface">Settings</h1>
            <p class="font-body-md text-body-sm text-on-surface-variant">Manage your account settings and preferences.</p>
        </div>
    </div>
    
    <!-- Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Settings -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-outline-variant shadow-sm">
                <div class="p-6 border-b border-outline-variant">
                    <h2 class="font-h2 text-lg text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">person</span>
                        Profile Information
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <form class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-on-surface-variant mb-2">First Name</label>
                                <input type="text" value="{{ auth()->user()->first_name }}" 
                                       class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-on-surface-variant mb-2">Last Name</label>
                                <input type="text" value="{{ auth()->user()->last_name }}" 
                                       class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-on-surface-variant mb-2">Email Address</label>
                            <input type="email" value="{{ auth()->user()->email }}" 
                                   class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-container transition-colors">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Security Settings -->
            <div class="bg-white rounded-xl border border-outline-variant shadow-sm">
                <div class="p-6 border-b border-outline-variant">
                    <h2 class="font-h2 text-lg text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">security</span>
                        Security
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-outline-variant">
                            <div>
                                <h3 class="font-medium text-on-surface">Change Password</h3>
                                <p class="text-sm text-on-surface-variant">Update your password to keep your account secure</p>
                            </div>
                            <button class="px-4 py-2 border border-outline-variant rounded-lg font-medium hover:bg-surface-container-low transition-colors">
                                Change
                            </button>
                        </div>
                        <div class="flex items-center justify-between py-3 border-b border-outline-variant">
                            <div>
                                <h3 class="font-medium text-on-surface">Two-Factor Authentication</h3>
                                <p class="text-sm text-on-surface-variant">Add an extra layer of security to your account</p>
                            </div>
                            <button class="px-4 py-2 border border-outline-variant rounded-lg font-medium hover:bg-surface-container-low transition-colors">
                                Enable
                            </button>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <div>
                                <h3 class="font-medium text-on-surface">Active Sessions</h3>
                                <p class="text-sm text-on-surface-variant">Manage your active login sessions</p>
                            </div>
                            <button class="px-4 py-2 border border-outline-variant rounded-lg font-medium hover:bg-surface-container-low transition-colors">
                                View
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notification Settings -->
            <div class="bg-white rounded-xl border border-outline-variant shadow-sm">
                <div class="p-6 border-b border-outline-variant">
                    <h2 class="font-h2 text-lg text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">notifications</span>
                        Notifications
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-on-surface">Email Notifications</h3>
                            <p class="text-sm text-on-surface-variant">Receive email updates about your account</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-on-surface">Transaction Alerts</h3>
                            <p class="text-sm text-on-surface-variant">Get notified for new transactions</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-on-surface">Monthly Reports</h3>
                            <p class="text-sm text-on-surface-variant">Receive monthly financial summaries</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Account Overview -->
            <div class="bg-white rounded-xl border border-outline-variant shadow-sm">
                <div class="p-6">
                    <h3 class="font-h2 text-lg text-on-surface mb-4">Account Overview</h3>
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-primary-container rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-3xl text-primary">person</span>
                        </div>
                        <h4 class="font-semibold text-on-surface">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h4>
                        <p class="text-sm text-on-surface-variant">{{ auth()->user()->email }}</p>
                        <div class="mt-2">
                            @if(auth()->user()->email === 'admin@mali.com')
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-container text-on-primary-container text-xs font-medium rounded-full">
                                <span class="material-symbols-outlined text-xs">admin_panel_settings</span>
                                Administrator
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-secondary-container text-on-secondary-container text-xs font-medium rounded-full">
                                <span class="material-symbols-outlined text-xs">person</span>
                                User
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Member Since</span>
                            <span class="font-medium text-on-surface">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-on-surface-variant">Account Status</span>
                            <span class="font-medium text-secondary">Active</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-outline-variant shadow-sm">
                <div class="p-6">
                    <h3 class="font-h2 text-lg text-on-surface mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button class="w-full px-4 py-2 border border-outline-variant rounded-lg font-medium hover:bg-surface-container-low transition-colors text-left flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">download</span>
                            Export Data
                        </button>
                        <button class="w-full px-4 py-2 border border-outline-variant rounded-lg font-medium hover:bg-surface-container-low transition-colors text-left flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">help</span>
                            Help Center
                        </button>
                        <button class="w-full px-4 py-2 border border-outline-variant rounded-lg font-medium hover:bg-surface-container-low transition-colors text-left flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">description</span>
                            Terms & Privacy
                        </button>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-error text-on-error rounded-lg font-medium hover:bg-error-container transition-colors text-left flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">logout</span>
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
