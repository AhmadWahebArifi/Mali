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
                Pending Approval (<span id="pendingCount">{{ $pendingUsers->count() }}</span>)
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
                <tbody id="pendingUsersTable" class="divide-y divide-outline-variant">
                    @foreach($pendingUsers as $user)
                    <tr data-user-id="{{ $user->id }}" class="hover:bg-surface-container-low/50">
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
                            <button onclick="approveUser({{ $user->id }})" class="inline-flex items-center gap-1 px-3 py-1 bg-secondary text-on-secondary text-sm font-medium rounded-lg hover:bg-secondary-container transition-colors">
                                <span class="material-symbols-outlined text-sm">check</span>
                                Approve
                            </button>
                            <button onclick="rejectUser({{ $user->id }})" class="inline-flex items-center gap-1 px-3 py-1 bg-error text-on-error text-sm font-medium rounded-lg hover:bg-error-container transition-colors ml-2">
                                <span class="material-symbols-outlined text-sm">close</span>
                                Reject
                            </button>
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
                Approved Users (<span id="approvedCount">{{ $approvedUsers->count() }}</span>)
            </h2>
        </div>
        
        @if($approvedUsers->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-container-highest">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-on-surface-variant uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-on-surface-variant uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-on-surface-variant uppercase tracking-wider">Approved Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-on-surface-variant uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-on-surface-variant uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
                <tbody id="approvedUsersTable" class="divide-y divide-outline-variant">
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
                        <td class="px-6 py-4 text-right">
                            @if($user->email !== 'admin@mali.com')
                            <button onclick="deleteUser({{ $user->id }}, '{{ $user->first_name }} {{ $user->last_name }}')" class="inline-flex items-center gap-1 px-3 py-1 bg-error text-on-error text-sm font-medium rounded-lg hover:bg-error-container transition-colors">
                                <span class="material-symbols-outlined text-sm">delete</span>
                                Delete
                            </button>
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
// Dynamic user management functionality
let refreshInterval;

function refreshUsersData() {
    fetch('/admin/users/data', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        updatePendingUsersTable(data.pending);
        updateApprovedUsersTable(data.approved);
        updateCounts(data.pending_count, data.approved_count);
    })
    .catch(error => console.error('Error refreshing data:', error));
}

function updatePendingUsersTable(pendingUsers) {
    const tbody = document.getElementById('pendingUsersTable');
    
    if (pendingUsers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="p-12 text-center">
                    <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">check_circle</span>
                    <p class="text-gray-500 text-lg mb-2">No pending users</p>
                    <p class="text-gray-400">All users have been reviewed.</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = pendingUsers.map(user => `
        <tr data-user-id="${user.id}" class="hover:bg-surface-container-low/50">
            <td class="px-6 py-4">
                <div class="font-medium text-on-surface">${user.name}</div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-on-surface-variant">${user.email}</div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-on-surface-variant">${user.created_at}</div>
            </td>
            <td class="px-6 py-4 text-right">
                <button onclick="approveUser(${user.id})" class="inline-flex items-center gap-1 px-3 py-1 bg-secondary text-on-secondary text-sm font-medium rounded-lg hover:bg-secondary-container transition-colors">
                    <span class="material-symbols-outlined text-sm">check</span>
                    Approve
                </button>
                <button onclick="rejectUser(${user.id})" class="inline-flex items-center gap-1 px-3 py-1 bg-error text-on-error text-sm font-medium rounded-lg hover:bg-error-container transition-colors ml-2">
                    <span class="material-symbols-outlined text-sm">close</span>
                    Reject
                </button>
            </td>
        </tr>
    `).join('');
}

function updateApprovedUsersTable(approvedUsers) {
    const tbody = document.getElementById('approvedUsersTable');
    if (!tbody) return;
    
    if (approvedUsers.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="p-12 text-center">
                    <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">people</span>
                    <p class="text-gray-500 text-lg mb-2">No approved users</p>
                    <p class="text-gray-400">No users have been approved yet.</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = approvedUsers.map(user => `
        <tr class="hover:bg-surface-container-low/50">
            <td class="px-6 py-4">
                <div class="font-medium text-on-surface">${user.name}</div>
                ${user.is_admin ? '<span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-container text-on-primary-container text-xs font-medium rounded-full mt-1"><span class="material-symbols-outlined text-xs">admin_panel_settings</span>Administrator</span>' : ''}
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-on-surface-variant">${user.email}</div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-on-surface-variant">${user.approved_at}</div>
            </td>
            <td class="px-6 py-4 text-right">
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-success text-success text-sm font-medium rounded-lg">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    Approved
                </span>
            </td>
            <td class="px-6 py-4 text-right">
                ${!user.is_admin ? `
                <button onclick="deleteUser(${user.id}, '${user.name}')" class="inline-flex items-center gap-1 px-3 py-1 bg-error text-on-error text-sm font-medium rounded-lg hover:bg-error-container transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>
                    Delete
                </button>
                ` : ''}
            </td>
        </tr>
    `).join('');
}

function updateCounts(pendingCount, approvedCount) {
    const pendingCountElement = document.getElementById('pendingCount');
    const approvedCountElement = document.getElementById('approvedCount');
    
    if (pendingCountElement) {
        pendingCountElement.textContent = pendingCount;
    }
    if (approvedCountElement) {
        approvedCountElement.textContent = approvedCount;
    }
}

function approveUser(userId) {
    Swal.fire({
        title: 'Approve User?',
        text: 'This user will be able to log in to their account.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Approve'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Approving...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/admin/users/${userId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#004ccd'
                    });
                    refreshUsersData();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to approve user',
                        confirmButtonColor: '#004ccd'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to approve user. Please try again.',
                    confirmButtonColor: '#004ccd'
                });
                console.error('Error:', error);
            });
        }
    });
}

function rejectUser(userId) {
    Swal.fire({
        title: 'Reject User?',
        text: 'This user will be permanently removed from the system.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Reject'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Rejecting...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/admin/users/${userId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#004ccd'
                    });
                    refreshUsersData();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to reject user',
                        confirmButtonColor: '#004ccd'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to reject user. Please try again.',
                    confirmButtonColor: '#004ccd'
                });
                console.error('Error:', error);
            });
        }
    });
}

function deleteUser(userId, userName) {
    Swal.fire({
        title: 'Delete User?',
        html: `Are you sure you want to delete <strong>${userName}</strong>?<br><br>This action cannot be undone and will permanently remove the user from the system.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        confirmButtonColor: '#004ccd'
                    });
                    refreshUsersData();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to delete user',
                        confirmButtonColor: '#004ccd'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to delete user. Please try again.',
                    confirmButtonColor: '#004ccd'
                });
                console.error('Error:', error);
            });
        }
    });
}

// Start real-time updates when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initial load
    refreshUsersData();
    
    // Refresh every 10 seconds
    refreshInterval = setInterval(refreshUsersData, 10000);
    
    // Clean up interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
});

document.querySelectorAll('.swal2-confirm').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const form = this.closest('form');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                FinTrackAlert.loading('Rejecting...');
                form.submit();
            }
        });
    });
});
</script>
@endpush
