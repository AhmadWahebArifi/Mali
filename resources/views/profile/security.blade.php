@extends('layouts.app')

@section('title', 'Security - FinTrack Pro')

@section('page-title', 'Security Settings')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-4xl mx-auto w-full">
    <!-- Security Header -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-8 mb-8">
        <div class="flex items-center gap-4 mb-6">
            <span class="material-symbols-outlined text-4xl text-primary">security</span>
            <div>
                <h1 class="font-h1 text-h1 text-on-surface mb-2">Security Settings</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">Manage your account security and privacy settings.</p>
            </div>
        </div>
    </div>
    
    <!-- Security Options -->
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Password Security -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6">
            <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">password</span>
                Password Security
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-medium text-on-surface">Change Password</h3>
                        <p class="text-sm text-on-surface-variant">Update your account password</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-primary text-on-primary rounded-lg hover:bg-primary-container transition-colors text-sm font-medium">
                        Change
                    </a>
                </div>
                <div class="border-t border-outline-variant pt-4">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-success">check_circle</span>
                        <span class="text-on-surface-variant">Account last updated: {{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Login Activity -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6">
            <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">history</span>
                Login Activity
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-medium text-on-surface">Recent Logins</h3>
                        <p class="text-sm text-on-surface-variant">View your recent login history</p>
                    </div>
                    <button onclick="showLoginActivity()" class="px-4 py-2 bg-secondary text-on-secondary rounded-lg hover:bg-secondary-container transition-colors text-sm font-medium">
                        View
                    </button>
                </div>
                <div class="border-t border-outline-variant pt-4">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-info">info</span>
                        <span class="text-on-surface-variant">Last login: {{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Two-Factor Authentication -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6">
            <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">phonelink_lock</span>
                Two-Factor Authentication
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-medium text-on-surface">2FA Status</h3>
                        <p class="text-sm text-on-surface-variant">Add an extra layer of security</p>
                    </div>
                    <button onclick="showTwoFactorInfo()" class="px-4 py-2 bg-outline text-on-outline rounded-lg hover:bg-surface-container-low transition-colors text-sm font-medium">
                        Enable
                    </button>
                </div>
                <div class="border-t border-outline-variant pt-4">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-warning">warning</span>
                        <span class="text-on-surface-variant">2FA is not enabled</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Data Privacy -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6">
            <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">privacy_tip</span>
                Data Privacy
            </h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-medium text-on-surface">Export Your Data</h3>
                        <p class="text-sm text-on-surface-variant">Download all your personal data</p>
                    </div>
                    <a href="{{ route('profile.export') }}" class="px-4 py-2 bg-secondary text-on-secondary rounded-lg hover:bg-secondary-container transition-colors text-sm font-medium">
                        Export
                    </a>
                </div>
                <div class="border-t border-outline-variant pt-4">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined text-success">check_circle</span>
                        <span class="text-on-surface-variant">Your data is encrypted and secure</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back to Profile -->
    <div class="mt-8 text-center">
        <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-outline text-on-outline rounded-lg hover:bg-surface-container-low transition-colors font-medium">
            <span class="material-symbols-outlined">arrow_back</span>
            Back to Profile
        </a>
    </div>
</main>

<!-- Login Activity Modal -->
<div id="loginActivityModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[80vh] overflow-hidden">
        <div class="p-6 border-b border-outline-variant">
            <div class="flex items-center justify-between">
                <h2 class="font-h2 text-lg text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">history</span>
                    Login Activity History
                </h2>
                <button onclick="closeLoginActivity()" class="text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <div id="loginActivityContent" class="space-y-4">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showLoginActivity() {
    const modal = document.getElementById('loginActivityModal');
    const content = document.getElementById('loginActivityContent');
    
    // Show loading state
    content.innerHTML = `
        <div class="text-center py-8">
            <span class="material-symbols-outlined animate-spin text-3xl text-primary">refresh</span>
            <p class="mt-2 text-on-surface-variant">Loading login activity...</p>
        </div>
    `;
    
    modal.classList.remove('hidden');
    
    // Fetch login activity data
    fetch('/profile/login-activity', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.activities && data.activities.length > 0) {
            content.innerHTML = `
                <div class="space-y-3">
                    ${data.activities.map(activity => `
                        <div class="border border-outline-variant rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="material-symbols-outlined text-success text-sm">login</span>
                                        <span class="font-medium text-on-surface">Successful Login</span>
                                        <span class="text-sm text-on-surface-variant">${activity.diff_for_humans}</span>
                                    </div>
                                    <div class="space-y-1 text-sm text-on-surface-variant">
                                        <p><strong>Date:</strong> ${activity.created_at}</p>
                                        <p><strong>IP Address:</strong> ${activity.ip_address}</p>
                                        <p><strong>Browser:</strong> ${activity.user_agent ? activity.user_agent.substring(0, 100) + '...' : 'Unknown'}</p>
                                        ${activity.description ? `<p><strong>Details:</strong> ${activity.description}</p>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
        } else {
            content.innerHTML = `
                <div class="text-center py-8">
                    <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">history</span>
                    <p class="text-gray-500 text-lg mb-2">No login activity found</p>
                    <p class="text-gray-400">Login activity will appear here once you start using the application.</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error fetching login activity:', error);
        content.innerHTML = `
            <div class="text-center py-8">
                <span class="material-symbols-outlined text-6xl text-error mb-4">error</span>
                <p class="text-error text-lg mb-2">Failed to load login activity</p>
                <p class="text-gray-400">Please try again later.</p>
            </div>
        `;
    });
}

function closeLoginActivity() {
    document.getElementById('loginActivityModal').classList.add('hidden');
}

function showTwoFactorInfo() {
    Swal.fire({
        title: 'Two-Factor Authentication',
        html: `
            <div class="text-left">
                <p class="mb-4">Two-Factor Authentication (2FA) adds an extra layer of security to your account.</p>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-success mt-1">check_circle</span>
                        <div>
                            <strong>Enhanced Security:</strong> Requires both your password and a verification code
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-success mt-1">check_circle</span>
                        <div>
                            <strong>Protection:</strong> Even if someone knows your password, they can't access your account
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-info mt-1">info</span>
                        <div>
                            <strong>Coming Soon:</strong> This feature will be available in the next update
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'info',
        confirmButtonColor: '#004ccd',
        confirmButtonText: 'Got it'
    });
}

// Close modal when clicking outside
document.getElementById('loginActivityModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLoginActivity();
    }
});
</script>
@endpush
@endsection
