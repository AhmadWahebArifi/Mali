@extends('layouts.app')

@section('title', 'Settings - BawarFinTrack')

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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @php
                                $currencies = \App\Helpers\FormatHelper::getAvailableCurrencies();
                            @endphp
                            @foreach($currencies as $currency)
                                <option value="{{ $currency['code'] }}" {{ $settings->currency == $currency['code'] ? 'selected' : '' }}>
                                    {{ $currency['symbol'] }} {{ $currency['name'] }} ({{ $currency['code'] }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Default currency for financial reports</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                        <select name="timezone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @php
                                $timezones = \App\Helpers\FormatHelper::getAvailableTimezones();
                            @endphp
                            @foreach($timezones as $value => $label)
                                <option value="{{ $value }}" {{ $settings->timezone == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Your timezone for date/time display</p>
                    </div>
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
            // Update currency symbols throughout the page if currency changed
            if (data.currency) {
                updateCurrencySymbols(data.currency);
            }
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

function updateCurrencySymbols(newCurrency) {
    // This function would update currency symbols throughout the application
    // For now, we'll just reload the page to show the changes
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Initialize auto-save when page loads
document.addEventListener('DOMContentLoaded', setupAutoSave);
</script>
@endpush
