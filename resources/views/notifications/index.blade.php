@extends('layouts.app')

@section('title', 'Notifications - FinTrack Pro')

@section('page-title', 'Notifications')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="font-h1 text-h1 text-on-surface">Notifications</h1>
            <p class="font-body-md text-body-sm text-on-surface-variant">Stay updated with your financial activities.</p>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                Mark all as read
            </button>
            <button class="px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                Clear all
            </button>
        </div>
    </div>
    
    <!-- Notifications List -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm divide-y divide-outline-variant">
        @forelse($notifications as $notification)
        <div class="p-6 hover:bg-surface-container-low/50 transition-colors {{ !$notification['read'] ? 'bg-blue-50/30' : '' }}">
            <div class="flex items-start gap-4">
                <!-- Notification Icon -->
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                    @if($notification['type'] === 'success') bg-green-100 text-green-600
                    @elseif($notification['type'] === 'warning') bg-yellow-100 text-yellow-600
                    @elseif($notification['type'] === 'error') bg-red-100 text-red-600
                    @else bg-blue-100 text-blue-600 @endif">
                    <span class="material-symbols-outlined text-lg">
                        @if($notification['type'] === 'success') check_circle
                        @elseif($notification['type'] === 'warning') warning
                        @elseif($notification['type'] === 'error') error
                        @else info @endif
                    </span>
                </div>
                
                <!-- Notification Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1">
                            <h3 class="font-semibold text-on-surface {{ !$notification['read'] ? 'font-bold' : '' }}">
                                {{ $notification['title'] }}
                            </h3>
                            <p class="mt-1 text-sm text-on-surface-variant">
                                {{ $notification['message'] }}
                            </p>
                            <p class="mt-2 text-xs text-on-surface-variant">
                                {{ $notification['time'] }}
                            </p>
                        </div>
                        @if(!$notification['read'])
                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                            New
                        </span>
                        @endif
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-2">
                    @if(!$notification['read'])
                    <button class="p-1 text-gray-400 hover:text-gray-600 transition-colors" title="Mark as read">
                        <span class="material-symbols-outlined text-lg">done</span>
                    </button>
                    @endif
                    <button class="p-1 text-gray-400 hover:text-gray-600 transition-colors" title="Delete">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">notifications_off</span>
            <p class="text-gray-500 text-lg mb-2">No notifications</p>
            <p class="text-gray-400">You're all caught up! Check back later for updates.</p>
        </div>
        @endforelse
    </div>
</main>
@endsection
