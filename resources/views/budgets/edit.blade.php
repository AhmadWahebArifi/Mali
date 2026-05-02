@extends('layouts.app')

@section('title', 'Edit Budget')

@section('page-title', 'Edit Budget')

@section('content')
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="font-h1 text-h1 text-on-surface mb-2">Edit Budget</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Update budget assignment for {{ $budget->user->first_name }} {{ $budget->user->last_name }}.</p>
    </div>

    <!-- Budget Form -->
    <div class="bg-white rounded-xl border border-outline-variant p-6 max-w-2xl">
        <form action="{{ route('budgets.update', $budget) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- User Selection -->
            <div class="mb-6">
                <label for="user_id" class="block text-sm font-medium text-on-surface mb-2">
                    Assign to User <span class="text-tertiary">*</span>
                </label>
                <select id="user_id" name="user_id" required
                        class="w-full px-3 py-2 border border-outline rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent">
                    <option value="">Select a user</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $budget->user_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                    </option>
                    @endforeach
                </select>
                @error('user_id')
                <p class="mt-1 text-sm text-tertiary">{{ $message }}</p>
                @enderror
            </div>

            <!-- Budget Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-on-surface mb-2">
                    Budget Name <span class="text-tertiary">*</span>
                </label>
                <input type="text" id="name" name="name" required
                       value="{{ old('name', $budget->name) }}"
                       placeholder="e.g., Monthly Food Budget, Entertainment Allowance"
                       class="w-full px-3 py-2 border border-outline rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent">
                @error('name')
                <p class="mt-1 text-sm text-tertiary">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category Selection -->
            <div class="mb-6">
                <label for="category_id" class="block text-sm font-medium text-on-surface mb-2">
                    Category (Optional)
                </label>
                <select id="category_id" name="category_id"
                        class="w-full px-3 py-2 border border-outline rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $budget->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ $category->type }})
                    </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-on-surface-variant">
                    Leave empty to apply budget to all expense categories
                </p>
                @error('category_id')
                <p class="mt-1 text-sm text-tertiary">{{ $message }}</p>
                @enderror
            </div>

            <!-- Budget Amount -->
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-on-surface mb-2">
                    Budget Amount <span class="text-tertiary">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-on-surface-variant">
                        {{ \App\Helpers\FormatHelper::currency(0, null, false) }}
                    </span>
                    <input type="number" id="amount" name="amount" required
                           value="{{ old('amount', $budget->amount) }}"
                           step="0.01" min="0"
                           placeholder="0.00"
                           class="w-full pl-12 pr-3 py-2 border border-outline rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent">
                </div>
                @error('amount')
                <p class="mt-1 text-sm text-tertiary">{{ $message }}</p>
                @enderror
            </div>

            <!-- Budget Period -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-on-surface mb-2">
                    Budget Period <span class="text-tertiary">*</span>
                </label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="period" value="monthly" required
                               {{ old('period', $budget->period) === 'monthly' ? 'checked' : '' }}
                               class="mr-2 text-secondary focus:ring-secondary">
                        <span class="text-on-surface">Monthly (resets each month)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="period" value="yearly" required
                               {{ old('period') === 'yearly' ? 'checked' : '' }}
                               class="mr-2 text-secondary focus:ring-secondary">
                        <span class="text-on-surface">Yearly (resets each year)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="period" value="custom" required
                               {{ old('period') === 'custom' ? 'checked' : '' }}
                               class="mr-2 text-secondary focus:ring-secondary">
                        <span class="text-on-surface">Custom (specific date range)</span>
                    </label>
                </div>
                @error('period')
                <p class="mt-1 text-sm text-tertiary">{{ $message }}</p>
                @enderror
            </div>

            <!-- Custom Date Range (shown when custom period is selected) -->
            <div id="customDateRange" class="mb-6 {{ old('period', $budget->period) !== 'custom' ? 'hidden' : '' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-on-surface mb-2">
                            Start Date <span class="text-tertiary">*</span>
                        </label>
                        <input type="date" id="start_date" name="start_date"
                               value="{{ old('start_date', $budget->start_date) }}"
                               class="w-full px-3 py-2 border border-outline rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent">
                        @error('start_date')
                        <p class="mt-1 text-sm text-tertiary">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-on-surface mb-2">
                            End Date <span class="text-tertiary">*</span>
                        </label>
                        <input type="date" id="end_date" name="end_date"
                               value="{{ old('end_date', $budget->end_date) }}"
                               class="w-full px-3 py-2 border border-outline rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent">
                        @error('end_date')
                        <p class="mt-1 text-sm text-tertiary">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-on-surface mb-2">
                    Description (Optional)
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Add any notes or details about this budget..."
                          class="w-full px-3 py-2 border border-outline rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent">{{ old('description', $budget->description) }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-tertiary">{{ $message }}</p>
                @enderror
            </div>

            <!-- Budget Status -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $budget->is_active) ? 'checked' : '' }}
                           class="mr-2 text-secondary focus:ring-secondary">
                    <span class="text-on-surface">Budget is active</span>
                </label>
                <p class="mt-1 text-sm text-on-surface-variant">
                    Inactive budgets won't restrict spending
                </p>
            </div>

            <!-- Current Budget Status -->
            <div class="mb-6 p-4 bg-surface-variant/50 rounded-lg">
                <h3 class="font-semibold text-on-surface mb-2">Current Budget Status</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-on-surface-variant">Total Amount:</span>
                        <div class="font-mono">{{ \App\Helpers\FormatHelper::currency($budget->amount) }}</div>
                    </div>
                    <div>
                        <span class="text-on-surface-variant">Spent:</span>
                        <div class="font-mono {{ $budget->is_over_budget ? 'text-tertiary' : 'text-on-surface' }}">
                            {{ \App\Helpers\FormatHelper::currency($budget->spent) }}
                        </div>
                    </div>
                    <div>
                        <span class="text-on-surface-variant">Remaining:</span>
                        <div class="font-mono {{ $budget->is_over_budget ? 'text-tertiary' : ($budget->is_near_limit ? 'text-warning' : 'text-secondary') }}">
                            {{ \App\Helpers\FormatHelper::currency($budget->remaining) }}
                        </div>
                    </div>
                    <div>
                        <span class="text-on-surface-variant">Used:</span>
                        <div class="font-mono {{ $budget->is_over_budget ? 'text-tertiary' : ($budget->is_near_limit ? 'text-warning' : 'text-on-surface') }}">
                            {{ number_format($budget->percentage_used, 1) }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ route('budgets.index') }}" 
                   class="px-4 py-2 text-on-surface hover:text-on-surface/80">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-secondary text-white rounded-lg font-semibold hover:opacity-90">
                    Update Budget
                </button>
            </div>
        </form>
    </div>
</main>
@endsection

@push('scripts')
<script>
// Show/hide custom date range based on period selection
document.addEventListener('DOMContentLoaded', function() {
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
    
    periodRadios.forEach(radio => {
        radio.addEventListener('change', toggleCustomDateRange);
    });
    
    // Initial state
    toggleCustomDateRange();
    
    // Handle form submission with Sweet Alert
    const budgetForm = document.querySelector('form[action="{{ route("budgets.update", $budget) }}"]');
    if (budgetForm) {
        budgetForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (typeof BawarFinTrackAlert === 'undefined') {
                alert('BawarFinTrackAlert not loaded. Please refresh.');
                return;
            }
            
            BawarFinTrackAlert.loading('Updating budget...');
            
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
                    BawarFinTrackAlert.success('Success!', json.message || 'Budget updated successfully!').then(() => {
                        window.location.href = '{{ route("budgets.index") }}';
                    });
                } else if (status === 422 && json.errors) {
                    // Handle validation errors
                    const firstField = Object.keys(json.errors)[0];
                    const firstError = firstField ? json.errors[firstField][0] : 'Validation error';
                    BawarFinTrackAlert.error('Validation Error', firstError);
                } else {
                    BawarFinTrackAlert.error('Error', json.message || 'Failed to update budget');
                }
            })
            .catch(() => {
                BawarFinTrackAlert.error('Error', 'Failed to update budget. Please try again.');
            });
        });
    }
});
</script>
@endpush
