@extends('layouts.app')

@section('title', 'User Management - Admin')

@section('page-title', 'User Management')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="font-h1 text-h1 text-on-surface">User Management</h1>
            <p class="font-body-md text-body-sm text-on-surface-variant">Approve or reject user registration requests.</p>
        </div>
    </div>
    
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            FinTrackAlert.success('Success!', '{{ session('success') }}');
        });
    </script>
    @endif
    
    <!-- Pending Users -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm mb-8">
        <div class="p-6 border-b border-outline-variant">
            <h2 class="font-h2 text-lg text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-warning">pending</span>
                Pending Approval ({{ $pendingUsers->count() }})
            </h2>
        </div>
        
        @if($pendingUsers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-container-low">
                    <tr>
                        <th class="px-6 py-4 text-left text-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Registered</th>
                        <th class="px-6 py-4 text-right text-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @foreach($pendingUsers as $user)
                    <tr class="hover:bg-surface-container-low/50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-on-surface">{{ $user->first_name }} {{ $user->last_name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-on-surface-variant">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-on-surface-variant">{{ $user->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 bg-secondary text-on-secondary text-sm font-medium rounded-lg hover:bg-secondary-container transition-colors">
                                    <span class="material-symbols-outlined text-sm">check</span>
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.users.reject', $user) }}" method="POST" class="inline ml-2">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 bg-error text-on-error text-sm font-medium rounded-lg hover:bg-error-container transition-colors">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                    Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">check_circle</span>
            <p class="text-gray-500 text-lg mb-2">No pending users</p>
            <p class="text-gray-400">All user registrations have been reviewed.</p>
        </div>
        @endif
    </div>
    
    <!-- Approved Users -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm">
        <div class="p-6 border-b border-outline-variant">
            <h2 class="font-h2 text-lg text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary">verified</span>
                Approved Users ({{ $approvedUsers->count() }})
            </h2>
        </div>
        
        @if($approvedUsers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-container-low">
                    <tr>
                        <th class="px-6 py-4 text-left text-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-left text-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Approved</th>
                        <th class="px-6 py-4 text-left text-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @foreach($approvedUsers as $user)
                    <tr class="hover:bg-surface-container-low/50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-on-surface">{{ $user->first_name }} {{ $user->last_name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-on-surface-variant">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-on-surface-variant">{{ $user->approved_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->email === 'admin@mali.com')
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-container text-on-primary-container text-xs font-medium rounded-full">
                                <span class="material-symbols-outlined text-xs">admin_panel_settings</span>
                                Admin
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-secondary-container text-on-secondary-container text-xs font-medium rounded-full">
                                <span class="material-symbols-outlined text-xs">person</span>
                                User
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">person_off</span>
            <p class="text-gray-500 text-lg mb-2">No approved users</p>
            <p class="text-gray-400">No users have been approved yet.</p>
        </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
<script>
// Handle approve user with Sweet Alert
document.querySelectorAll('form[action*="approve"]').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const result = await FinTrackAlert.confirm(
            'Approve User',
            'Are you sure you want to approve this user?',
            'Approve',
            'Cancel'
        );
        
        if (result.isConfirmed) {
            FinTrackAlert.loading('Approving...');
            form.submit();
        }
    });
});

// Handle reject user with Sweet Alert
document.querySelectorAll('form[action*="reject"]').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const result = await FinTrackAlert.deleteConfirm('this user');
        
        if (result.isConfirmed) {
            FinTrackAlert.loading('Rejecting...');
            form.submit();
        }
    });
});
</script>
@endpush
