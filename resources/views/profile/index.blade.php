@extends('layouts.app')

@section('title', 'Profile - BawarFinTrack')

@section('page-title', 'Profile')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-4xl mx-auto w-full">
    <!-- Profile Header -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-8 mb-8">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-24 h-24 rounded-full bg-gray-200 overflow-hidden">
                <img alt="User avatar" 
                     class="w-full h-full object-cover" 
                     src="https://ui-avatars.com/api/?name={{ $user->name }}&background=004ccd&color=fff&size=96" />
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="font-h1 text-h1 text-on-surface mb-2">{{ $user->name }}</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mb-4">{{ $user->email }}</p>
                <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-primary-container text-on-primary-container rounded-full">
                        <span class="material-symbols-outlined text-sm mr-1">check_circle</span>
                        Verified Account
                    </span>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium bg-surface-container-high text-on-surface-variant rounded-full">
                        {{ $user->email === 'admin@mali.com' ? 'Administrator' : 'User' }}
                    </span>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" 
               class="px-6 py-3 bg-primary text-on-primary rounded-lg hover:bg-primary-container transition-colors font-medium">
                Edit Profile
            </a>
        </div>
    </div>
    
    <!-- Account Information -->
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Personal Information -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6">
            <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">person</span>
                Personal Information
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-on-surface-variant">First Name</label>
                    <p class="mt-1 text-on-surface">{{ $user->first_name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-on-surface-variant">Last Name</label>
                    <p class="mt-1 text-on-surface">{{ $user->last_name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-on-surface-variant">Email Address</label>
                    <p class="mt-1 text-on-surface">{{ $user->email }}</p>
                </div>
            </div>
        </div>
        
        <!-- Account Statistics -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6">
            <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">analytics</span>
                Account Statistics
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-on-surface-variant">Member Since</span>
                    <span class="text-sm font-medium text-on-surface">{{ $statistics['member_since']->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-on-surface-variant">Total Transactions</span>
                    <span class="text-sm font-medium text-on-surface">{{ $statistics['total_transactions'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-on-surface-variant">Accounts Created</span>
                    <span class="text-sm font-medium text-on-surface">{{ $statistics['accounts_created'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-on-surface-variant">Categories Used</span>
                    <span class="text-sm font-medium text-on-surface">{{ $statistics['categories_used'] }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-xl border border-outline-variant shadow-sm p-6">
        <h2 class="font-h2 text-lg text-on-surface mb-6">Quick Actions</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('settings.index') }}" class="flex flex-col items-center gap-2 p-4 border border-outline-variant rounded-lg hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-2xl text-primary">settings</span>
                <span class="text-sm font-medium text-on-surface">Settings</span>
            </a>
            <a href="{{ route('profile.export') }}" class="flex flex-col items-center gap-2 p-4 border border-outline-variant rounded-lg hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-2xl text-primary">download</span>
                <span class="text-sm font-medium text-on-surface">Export Data</span>
            </a>
            <a href="{{ route('profile.security') }}" class="flex flex-col items-center gap-2 p-4 border border-outline-variant rounded-lg hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-2xl text-primary">security</span>
                <span class="text-sm font-medium text-on-surface">Security</span>
            </a>
            <a href="{{ route('profile.help') }}" class="flex flex-col items-center gap-2 p-4 border border-outline-variant rounded-lg hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-2xl text-primary">help</span>
                <span class="text-sm font-medium text-on-surface">Help</span>
            </a>
        </div>
    </div>
</main>
@endsection
