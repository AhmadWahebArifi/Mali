@extends('layouts.app')

@section('title', 'Assign New Budget')

@section('page-title', 'Assign New Budget')

@section('content')
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="font-h1 text-h1 text-on-surface mb-2">Assign New Budget</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Create a budget assignment for a user to control their spending.</p>
    </div>

    <!-- Budget Form -->
    <div class="bg-white rounded-xl border border-outline-variant p-6 max-w-2xl">
        <form action="{{ route('budgets.store') }}" method="POST">
            @csrf
            
            <!-- User Selection -->
            <div class="mb-6">
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Assign to User <span class="text-red-500">*</span>
                </label>
                <select id="user_id" name="user_id" required
                        class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900">
                    <option value="">Select a user</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                    </option>
                    @endforeach
                </select>
                @error('user_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Budget Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Budget Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                       value="{{ old('name') }}"
                       placeholder="e.g., Monthly Food Budget, Entertainment Allowance"
                       class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900 placeholder-gray-400">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category Selection -->
            <div class="mb-6">
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Category (Optional)
                </label>
                <select id="category_id" name="category_id"
                        class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ $category->type }})
                    </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Leave empty to apply budget to all expense categories
                </p>
                @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account Selection -->
            <div class="mb-6">
                <label for="account_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Account (Optional)
                </label>
                <select id="account_id" name="account_id"
                        class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                        {{ $account->name }} - {{ $account->user ? $account->user->first_name : 'Unknown' }} (Balance: {{ \App\Helpers\FormatHelper::currency($account->balance) }})
                    </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    Leave empty to apply budget to all accounts
                </p>
                @error('account_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Budget Amount -->
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Budget Amount <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">
                        ؋
                    </span>
                    <input type="number" id="amount" name="amount" required
                           value="{{ old('amount') }}"
                           step="0.01" min="0"
                           placeholder="0.00"
                           class="w-full pl-8 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900 placeholder-gray-400">
                </div>
                <p class="mt-1 text-xs text-gray-500">Enter the budget amount in AFN</p>
                @error('amount')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Budget Period -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Budget Period <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="period" value="monthly" required
                               {{ old('period', 'monthly') === 'monthly' ? 'checked' : '' }}
                               class="mr-3 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-900">Monthly (resets each month)</span>
                    </label>
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="period" value="yearly" required
                               {{ old('period') === 'yearly' ? 'checked' : '' }}
                               class="mr-3 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-900">Yearly (resets each year)</span>
                    </label>
                    <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="period" value="custom" required
                               {{ old('period') === 'custom' ? 'checked' : '' }}
                               class="mr-3 text-blue-600 focus:ring-blue-500">
                        <span class="text-gray-900">Custom (specific date range)</span>
                    </label>
                </div>
                @error('period')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Custom Date Range (shown when custom period is selected) -->
            <div id="customDateRange" class="hidden mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="start_date" name="start_date"
                               value="{{ old('start_date') }}"
                               class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900">
                        @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="end_date" name="end_date"
                               value="{{ old('end_date') }}"
                               class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900">
                        @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description (Optional)
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Add any notes or details about this budget..."
                          class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 text-gray-900 placeholder-gray-400">{{ old('description') }}</textarea>
                @error('description')
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
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                    Assign Budget
                </button>
            </div>
        </form>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userSelect = document.getElementById('user_id');
    const accountSelect = document.getElementById('account_id');
    
    // Store original accounts data
    const originalAccounts = Array.from(accountSelect.options).slice(1); // Skip first "All Accounts" option
    
    userSelect.addEventListener('change', function() {
        const selectedUserId = this.value;
        
        // Clear current options (except first one)
        while (accountSelect.options.length > 1) {
            accountSelect.remove(1);
        }
        
        if (selectedUserId === '') {
            // If no user selected, show all accounts
            originalAccounts.forEach(option => {
                accountSelect.add(option.cloneNode(true));
            });
        } else {
            // Filter accounts for selected user - show standard account types
            const standardAccounts = [
                { name: 'Cash on Hand', text: 'Cash on Hand (Balance: $0.00)' },
                { name: 'HesabPay', text: 'HesabPay (Balance: $0.00)' }
            ];
            
            standardAccounts.forEach(account => {
                const option = document.createElement('option');
                option.value = ''; // Let BudgetController handle account creation
                option.textContent = account.text;
                accountSelect.add(option);
            });
        }
    });
    
    // Show/hide custom date range based on period selection
    const periodRadios = document.querySelectorAll('input[name="period"]');
    const customDateRange = document.getElementById('customDateRange');
    
    function toggleCustomDateRange() {
        const selectedPeriod = document.querySelector('input[name="period"]:checked').value;
        if (selectedPeriod === 'custom') {
            customDateRange.classList.remove('hidden');
        } else {
            customDateRange.classList.add('hidden');
        }
    }
    
    // Add event listeners to period radios
    periodRadios.forEach(radio => {
        radio.addEventListener('change', toggleCustomDateRange);
    });
    
    // Initialize state
    toggleCustomDateRange();
});
</script>
@endpush
