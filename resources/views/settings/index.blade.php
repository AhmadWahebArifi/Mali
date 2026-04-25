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
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('settings.reset') }}" onsubmit="return confirm('Are you sure you want to reset all settings to defaults?')">
                @csrf
                <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <span class="material-symbols-outlined text-sm">refresh</span>
                    Reset to Defaults
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-green-600">check_circle</span>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')
        
        <!-- General Settings -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl text-blue-600">settings</span>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">General Settings</h2>
                        <p class="text-sm text-gray-600">Basic configuration and preferences</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                    <input type="text" name="company_name" value="{{ $currentSettings['company_name'] ?? '' }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="{{ $user->first_name }} {{ $user->last_name }}">
                    <p class="text-xs text-gray-500 mt-1">{{ $availableSettings['general']['company_name']['description'] }}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($availableSettings['general']['currency']['options'] as $option)
                                <option value="{{ $option }}" {{ ($currentSettings['currency'] ?? $availableSettings['general']['currency']['default']) == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">{{ $availableSettings['general']['currency']['description'] }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                        <select name="timezone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($availableSettings['general']['timezone']['options'] as $option)
                                <option value="{{ $option }}" {{ ($currentSettings['timezone'] ?? $availableSettings['general']['timezone']['default']) == $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">{{ $availableSettings['general']['timezone']['description'] }}</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Format</label>
                    <select name="date_format" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($availableSettings['general']['date_format']['options'] as $option)
                                <option value="{{ $option }}" {{ ($currentSettings['date_format'] ?? $availableSettings['general']['date_format']['default']) == $option ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::now()->format($option) }}
                                </option>
                            @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">{{ $availableSettings['general']['date_format']['description'] }}</p>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl text-green-600">notifications</span>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Notification Settings</h2>
                        <p class="text-sm text-gray-600">Control how you receive notifications</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                @foreach(['email_notifications', 'transaction_alerts', 'low_balance_alerts', 'monthly_reports'] as $key)
                    <div class="flex items-center justify-between py-3">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-700">{{ $availableSettings['notifications'][$key]['label'] }}</label>
                            <p class="text-xs text-gray-500 mt-1">{{ $availableSettings['notifications'][$key]['description'] }}</p>
                        </div>
                        <div class="ml-4">
                            <input type="checkbox" name="{{ $key }}" value="1" 
                                   {{ ($currentSettings[$key] ?? $availableSettings['notifications'][$key]['default']) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Appearance Settings -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl text-purple-600">palette</span>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Appearance</h2>
                        <p class="text-sm text-gray-600">Customize the look and feel</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
                    <select name="theme" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($availableSettings['appearance']['theme']['options'] as $option)
                            <option value="{{ $option }}" {{ ($currentSettings['theme'] ?? $availableSettings['appearance']['theme']['default']) == $option ? 'selected' : '' }}>
                                {{ ucfirst($option) }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">{{ $availableSettings['appearance']['theme']['description'] }}</p>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl text-red-600">security</span>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Security</h2>
                        <p class="text-sm text-gray-600">Manage your security preferences</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between py-3">
                    <div class="flex-1">
                        <label class="text-sm font-medium text-gray-700">{{ $availableSettings['security']['two_factor_auth']['label'] }}</label>
                        <p class="text-xs text-gray-500 mt-1">{{ $availableSettings['security']['two_factor_auth']['description'] }}</p>
                    </div>
                    <div class="ml-4">
                        <input type="checkbox" name="two_factor_auth" value="1" 
                               {{ ($currentSettings['two_factor_auth'] ?? $availableSettings['security']['two_factor_auth']['default']) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="flex items-center justify-between py-3">
                    <div class="flex-1">
                        <label class="text-sm font-medium text-gray-700">{{ $availableSettings['security']['login_notifications']['label'] }}</label>
                        <p class="text-xs text-gray-500 mt-1">{{ $availableSettings['security']['login_notifications']['description'] }}</p>
                    </div>
                    <div class="ml-4">
                        <input type="checkbox" name="login_notifications" value="1" 
                               {{ ($currentSettings['login_notifications'] ?? $availableSettings['security']['login_notifications']['default']) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ $availableSettings['security']['session_timeout']['label'] }}</label>
                    <input type="number" name="session_timeout" 
                           value="{{ $currentSettings['session_timeout'] ?? $availableSettings['security']['session_timeout']['default'] }}"
                           min="5" max="1440"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">{{ $availableSettings['security']['session_timeout']['description'] }}</p>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <span class="material-symbols-outlined text-sm">save</span>
                Save Changes
            </button>
        </div>
    </form>
</main>
@endsection

@push('scripts')
<script>
// Auto-save functionality
let autoSaveTimer;
const autoSaveDelay = 2000; // 2 seconds

function setupAutoSave() {
    const form = document.querySelector('form[method="POST"]');
    const inputs = form.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                autoSave();
            }, autoSaveDelay);
        });
    });
}

function autoSave() {
    const form = document.querySelector('form[method="POST"]');
    const formData = new FormData(form);
    
    // Show saving indicator
    showSavingIndicator();
    
    fetch('{{ route("settings.update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage('Settings auto-saved');
        }
    })
    .catch(error => {
        console.error('Auto-save failed:', error);
    });
}

function showSavingIndicator() {
    const indicator = document.createElement('div');
    indicator.id = 'saving-indicator';
    indicator.className = 'fixed top-4 right-4 bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-2 shadow-lg z-50';
    indicator.innerHTML = `
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-yellow-600 animate-spin">refresh</span>
            <span class="text-yellow-800">Saving...</span>
        </div>
    `;
    
    // Remove existing indicator
    const existing = document.getElementById('saving-indicator');
    if (existing) existing.remove();
    
    document.body.appendChild(indicator);
}

function showSuccessMessage(message) {
    const indicator = document.getElementById('saving-indicator');
    if (indicator) {
        indicator.className = 'fixed top-4 right-4 bg-green-50 border border-green-200 rounded-lg px-4 py-2 shadow-lg z-50';
        indicator.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-green-600">check_circle</span>
                <span class="text-green-800">${message}</span>
            </div>
        `;
        
        setTimeout(() => {
            indicator.remove();
        }, 3000);
    }
}

// Initialize auto-save when page loads
document.addEventListener('DOMContentLoaded', setupAutoSave);

// Apply theme setting
function applyTheme(theme) {
    const html = document.documentElement;
    
    // Remove all theme classes first
    html.classList.remove('light', 'dark');
    
    if (theme === 'dark') {
        html.classList.add('dark');
    } else if (theme === 'light') {
        html.classList.add('light');
    } else if (theme === 'auto') {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (prefersDark) {
            html.classList.add('dark');
        } else {
            html.classList.add('light');
        }
    }
}

// Apply current theme on page load
document.addEventListener('DOMContentLoaded', () => {
    const currentTheme = '{{ $currentSettings['theme'] ?? 'light' }}';
    applyTheme(currentTheme);
    
    // Update theme when changed
    const themeSelect = document.querySelector('select[name="theme"]');
    if (themeSelect) {
        themeSelect.addEventListener('change', (e) => {
            applyTheme(e.target.value);
        });
    }
});
</script>
@endpush
