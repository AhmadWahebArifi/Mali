@extends('layouts.app')

@section('title', 'Budget Management')

@section('page-title', 'Budget Management')

@section('content')
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- SweetAlert Session Messages -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            BawarFinTrackAlert.success('Success!', '{{ session('success') }}');
        });
    </script>
    @endif

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="font-h1 text-h1 text-on-surface mb-2">Budget Management</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Assign and manage budgets for users to control spending.</p>
    </div>

    <!-- Admin Budget Pool Status (Admin Only) -->
    @if(auth()->user()->email === 'admin@mali.com')
    @php
        $adminPool = \App\Models\AdminBudgetPool::getCurrent();
    @endphp
    <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
        <h2 class="text-lg font-semibold text-blue-900 mb-4">Admin Budget Pool Status</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
            <div class="bg-white rounded-lg p-4 border border-blue-100">
                <div class="text-sm text-blue-600 mb-1">Allocation Status</div>
                <div class="text-xl font-bold text-blue-900">{{ round($adminPool->allocation_percentage, 1) }}%</div>
            </div>
        </div>
        
        <!-- Allocation Progress Bar -->
        <div class="mt-4">
            <div class="flex justify-between text-sm text-blue-700 mb-2">
                <span>Allocation Progress</span>
                <span>{{ round($adminPool->allocation_percentage, 1) }}% used</span>
            </div>
            <div class="w-full bg-blue-200 rounded-full h-3">
                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                     style="width: {{ min(100, $adminPool->allocation_percentage) }}%"></div>
            </div>
        </div>
        
        @if($adminPool->available_funds <= 0)
        <div class="mt-4 p-3 bg-red-100 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800">⚠️ No available funds in admin budget pool. Add funds to create new budgets.</p>
        </div>
        @endif
    </div>
    @endif

    <!-- Action Buttons -->
    @if(auth()->user()->email === 'admin@mali.com')
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('budgets.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
            <span class="material-symbols-outlined" data-icon="add">add</span>
            Assign New Budget
        </a>
        <a href="{{ route('budgets.add-funds') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition-colors">
            <span class="material-symbols-outlined" data-icon="add">add</span>
            Add Funds to Pool
        </a>
    </div>
    @endif

    <!-- Budgets Table -->
    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-surface-variant">
                    <tr>
                        <th class="text-left p-4 font-semibold text-on-surface-variant">Budget Name</th>
                        <th class="text-left p-4 font-semibold text-on-surface-variant">User</th>
                        <th class="text-left p-4 font-semibold text-on-surface-variant">Category</th>
                        <th class="text-left p-4 font-semibold text-on-surface-variant">Period</th>
                        <th class="text-right p-4 font-semibold text-on-surface-variant">Amount</th>
                        <th class="text-right p-4 font-semibold text-on-surface-variant">Spent</th>
                        <th class="text-right p-4 font-semibold text-on-surface-variant">Remaining</th>
                        <th class="text-center p-4 font-semibold text-on-surface-variant">Status</th>
                        <th class="text-center p-4 font-semibold text-on-surface-variant">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgets as $budget)
                    <tr class="border-t border-outline-variant hover:bg-surface-variant/50">
                        <td class="p-4">
                            <div>
                                <div class="font-semibold text-on-surface">{{ $budget->name }}</div>
                                @if($budget->description)
                                <div class="text-sm text-on-surface-variant">{{ $budget->description }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="text-on-surface">{{ $budget->user->first_name }} {{ $budget->user->last_name }}</div>
                            <div class="text-sm text-on-surface-variant">{{ $budget->user->email }}</div>
                        </td>
                        <td class="p-4">
                            @if($budget->category)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                    @if($budget->category->type === 'income') bg-secondary/10 text-secondary
                                    @else bg-tertiary/10 text-tertiary @endif">
                                    {{ $budget->category->name }}
                                </span>
                            @else
                                <span class="text-on-surface-variant">All Categories</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-outline/10 text-on-surface-variant">
                                {{ ucfirst($budget->period) }}
                            </span>
                        </td>
                        <td class="p-4 text-right font-mono">{{ \App\Helpers\FormatHelper::currency($budget->amount) }}</td>
                        <td class="p-4 text-right font-mono">
                            <span class="@if($budget->is_over_budget) text-tertiary @else text-on-surface @endif">
                                {{ \App\Helpers\FormatHelper::currency($budget->spent) }}
                            </span>
                        </td>
                        <td class="p-4 text-right font-mono">
                            <span class="@if($budget->is_over_budget) text-tertiary @elseif($budget->is_near_limit) text-warning @else text-secondary @endif">
                                {{ \App\Helpers\FormatHelper::currency($budget->remaining) }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <button onclick="toggleBudgetStatus({{ $budget->id }})" 
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                        @if($budget->is_active) bg-secondary @else bg-outline @endif">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform
                                        @if($budget->is_active) translate-x-6 @else translate-x-1 @endif"></span>
                                </button>
                                <span class="text-xs text-on-surface-variant">
                                    {{ $budget->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center justify-center gap-2">
                                @if(auth()->user()->email === 'admin@mali.com')
                                <a href="{{ route('budgets.edit', $budget) }}" 
                                   class="text-secondary hover:text-secondary/80 p-1">
                                    <span class="material-symbols-outlined" data-icon="edit">edit</span>
                                </a>
                                <button onclick="deleteBudget({{ $budget->id }})" 
                                        class="text-tertiary hover:text-tertiary/80 p-1">
                                    <span class="material-symbols-outlined" data-icon="delete">delete</span>
                                </button>
                                @endif
                                <button onclick="updateBudgetSpent({{ $budget->id }})" 
                                        class="text-primary hover:text-primary/80 p-1">
                                    <span class="material-symbols-outlined" data-icon="refresh">refresh</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-on-surface-variant">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-symbols-outlined text-4xl" data-icon="account_balance_wallet">account_balance_wallet</span>
                                <span>No budgets assigned yet.</span>
                                @if(auth()->user()->email === 'admin@mali.com')
                                <a href="{{ route('budgets.create') }}" class="text-secondary hover:underline">
                                    Assign your first budget
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($budgets->hasPages())
        <div class="border-t border-outline-variant p-4">
            {{ $budgets->links() }}
        </div>
        @endif
    </div>
</main>
@endsection

@push('scripts')
<script>
function toggleBudgetStatus(budgetId) {
    fetch(`/budgets/${budgetId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            BawarFinTrackAlert.success('Success!', data.message);
            location.reload();
        }
    })
    .catch(error => {
        BawarFinTrackAlert.error('Error', 'Failed to toggle budget status');
    });
}

function deleteBudget(budgetId) {
    BawarFinTrackAlert.deleteConfirm('this budget').then((result) => {
        if (result.isConfirmed) {
            BawarFinTrackAlert.loading('Deleting budget...');
            
            fetch(`/budgets/${budgetId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    BawarFinTrackAlert.success('Success!', 'Budget deleted successfully').then(() => {
                        location.reload();
                    });
                }
            })
            .catch(error => {
                BawarFinTrackAlert.error('Error', 'Failed to delete budget');
            });
        }
    });
}

function updateBudgetSpent(budgetId) {
    fetch(`/budgets/${budgetId}/update-spent`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            BawarFinTrackAlert.success('Success!', 'Budget spent amount updated');
            location.reload();
        }
    })
    .catch(error => {
        BawarFinTrackAlert.error('Error', 'Failed to update budget spent amount');
    });
}
</script>
@endpush
