@extends('layouts.app')

@section('title', 'Add Funds to Budget Pool')

@section('page-title', 'Add Funds to Budget Pool')

@section('content')
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="font-h1 text-h1 text-on-surface mb-2">Add Funds to Budget Pool</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Add funds to the admin budget pool to allocate to users.</p>
    </div>

    <!-- Current Pool Status -->
    <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold text-blue-900 mb-4">Current Budget Pool Status</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg p-4 border border-blue-100">
                <div class="text-sm text-blue-600 mb-1">Total Budget</div>
                <div class="text-xl font-bold text-blue-900">{{ \App\Helpers\FormatHelper::currency($adminPool->total_budget) }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-blue-100">
                <div class="text-sm text-blue-600 mb-1">Total Allocated</div>
                <div class="text-xl font-bold text-blue-900">{{ \App\Helpers\FormatHelper::currency($adminPool->total_allocated) }}</div>
            </div>
            <div class="bg-white rounded-lg p-4 border border-blue-100">
                <div class="text-sm text-blue-600 mb-1">Available Funds</div>
                <div class="text-xl font-bold {{ $adminPool->available_funds > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ \App\Helpers\FormatHelper::currency($adminPool->available_funds) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add Funds Form -->
    <div class="bg-white rounded-xl border border-outline-variant p-6 max-w-2xl">
        <form action="{{ route('budgets.add-funds.store') }}" method="POST">
            @csrf
            
            <!-- Amount -->
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Amount to Add <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">
                        ؋
                    </span>
                    <input type="number" id="amount" name="amount" required
                           step="0.01" min="0.01"
                           placeholder="0.00"
                           class="w-full pl-8 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900 placeholder-gray-400">
                </div>
                <p class="mt-1 text-xs text-gray-500">Enter the amount to add to the admin budget pool</p>
                @error('amount')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description (Optional)
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Add a note about this fund addition..."
                          class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900 placeholder-gray-400">{{ old('description') }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account Selection -->
            <div class="mb-6">
                <label for="account_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Target Account <span class="text-red-500">*</span>
                </label>
                <select id="account_id" name="account_id" required
                        class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900">
                    <option value="">Select an account</option>
                    @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                        {{ $account->name }} (Current Balance: {{ \App\Helpers\FormatHelper::currency($account->balance) }})
                    </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Select the account that will receive the added funds</p>
                @error('account_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('budgets.index') }}" 
                   class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors duration-200 shadow-sm">
                    Add Funds
                </button>
            </div>
        </form>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission with Sweet Alert
    const addFundsForm = document.querySelector('form[action="{{ route("budgets.add-funds.store") }}"]');
    if (addFundsForm) {
        addFundsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (typeof BawarFinTrackAlert === 'undefined') {
                alert('BawarFinTrackAlert not loaded. Please refresh.');
                return;
            }
            
            BawarFinTrackAlert.loading('Adding funds...');
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                const json = await response.json().catch(() => ({}));
                return { ok: response.ok, status: response.status, json };
            })
            .then(({ ok, status, json }) => {
                if (ok && json.success) {
                    BawarFinTrackAlert.success('Success!', json.message || 'Funds added successfully!').then(() => {
                        window.location.href = '{{ route("budgets.index") }}';
                    });
                } else if (status === 422 && json.errors) {
                    // Handle validation errors
                    const firstField = Object.keys(json.errors)[0];
                    const firstError = firstField ? json.errors[firstField][0] : 'Validation error';
                    BawarFinTrackAlert.error('Validation Error', firstError);
                } else {
                    BawarFinTrackAlert.error('Error', json.message || 'Failed to add funds');
                }
            })
            .catch(() => {
                BawarFinTrackAlert.error('Error', 'Failed to add funds. Please try again.');
            });
        });
    }
});
</script>
@endpush
