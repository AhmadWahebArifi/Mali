@extends('layouts.app')

@section('title', 'Categories Management')

@section('page-title', 'Categories')

@section('content')
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Page Header Section -->
    <div class="mb-8">
        <h1 class="font-h1 text-h1 text-on-surface mb-2">Category Management</h1>
        <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">Organize your financial tracking by creating and managing transaction labels. Custom categories help refine your spending insights.</p>
    </div>
    
    <!-- Bento Grid Layout for Categories -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Income Section -->
        <section class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined" data-icon="trending_up">trending_up</span>
                    </div>
                    <h3 class="font-h2 text-h2 text-on-surface">Income Streams</h3>
                </div>
                <button onclick="openAddCategoryModal('income')" class="flex items-center gap-2 bg-secondary/10 text-secondary px-4 py-2 rounded-xl font-label-caps text-label-caps hover:bg-secondary/20 transition-colors">
                    <span class="material-symbols-outlined text-[18px]" data-icon="add_circle">add_circle</span>
                    Add Category
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
                @forelse ($incomeCategories as $category)
                <div class="bg-white border border-outline-variant p-4 rounded-xl flex items-center justify-between hover:shadow-sm transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-secondary-container/30 flex items-center justify-center text-on-secondary-container">
                            <span class="material-symbols-outlined" data-icon="{{ $category->icon ?? 'payments' }}">{{ $category->icon ?? 'payments' }}</span>
                        </div>
                        <div>
                            <h4 class="font-body-md font-semibold text-on-surface">{{ $category->name }}</h4>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">{{ $category->transactions_count ?? 0 }} Transactions this month</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button class="p-2 hover:bg-surface-container rounded-lg text-outline">
                            <span class="material-symbols-outlined text-[20px]" data-icon="edit">edit</span>
                        </button>
                        <button class="p-2 hover:bg-error-container/50 rounded-lg text-error">
                            <span class="material-symbols-outlined text-[20px]" data-icon="delete">delete</span>
                        </button>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2">category</span>
                    <p>No income categories yet</p>
                </div>
                @endforelse
            </div>
        </section>
        
        <!-- Expense Section -->
        <section class="space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-tertiary/10 flex items-center justify-center text-tertiary">
                        <span class="material-symbols-outlined" data-icon="trending_down">trending_down</span>
                    </div>
                    <h3 class="font-h2 text-h2 text-on-surface">Expenditures</h3>
                </div>
                <button onclick="openAddCategoryModal('expense')" class="flex items-center gap-2 bg-tertiary/10 text-tertiary px-4 py-2 rounded-xl font-label-caps text-label-caps hover:bg-tertiary/20 transition-colors">
                    <span class="material-symbols-outlined text-[18px]" data-icon="add_circle">add_circle</span>
                    Add Category
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
                @forelse ($expenseCategories as $category)
                <div class="bg-white border border-outline-variant p-4 rounded-xl flex items-center justify-between hover:shadow-sm transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-tertiary-container/10 flex items-center justify-center text-tertiary">
                            <span class="material-symbols-outlined" data-icon="{{ $category->icon ?? 'shopping_cart' }}">{{ $category->icon ?? 'shopping_cart' }}</span>
                        </div>
                        <div>
                            <h4 class="font-body-md font-semibold text-on-surface">{{ $category->name }}</h4>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">{{ $category->transactions_count ?? 0 }} Transactions this month</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button class="p-2 hover:bg-surface-container rounded-lg text-outline">
                            <span class="material-symbols-outlined text-[20px]" data-icon="edit">edit</span>
                        </button>
                        <button class="p-2 hover:bg-error-container/50 rounded-lg text-error">
                            <span class="material-symbols-outlined text-[20px]" data-icon="delete">delete</span>
                        </button>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2">category</span>
                    <p>No expense categories yet</p>
                </div>
                @endforelse
            </div>
        </section>
    </div>
    
    <!-- Summary Card / Stats Section -->
    <div class="mt-12 bg-white border border-outline-variant rounded-2xl p-6 shadow-sm overflow-hidden relative">
        <div class="absolute top-0 right-0 w-64 h-full bg-primary/5 -skew-x-12 translate-x-32"></div>
        <div class="relative z-10 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <p class="font-label-caps text-label-caps text-on-surface-variant mb-1">TOTAL CATEGORIES</p>
                <p class="text-3xl font-black text-on-surface">{{ $totalCategories }} Active</p>
            </div>
            <div>
                <p class="font-label-caps text-label-caps text-on-surface-variant mb-1">MOST USED (INC)</p>
                <p class="text-3xl font-black text-secondary">{{ $mostUsedIncome ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="font-label-caps text-label-caps text-on-surface-variant mb-1">MOST USED (EXP)</p>
                <p class="text-3xl font-black text-tertiary">{{ $mostUsedExpense ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAddCategoryModal(type) {
    // Implementation for add category modal
    console.log('Opening add category modal for type:', type);
}

function toggleMobileSidebar() {
    // Implementation for mobile sidebar toggle
    console.log('Toggling mobile sidebar');
}
</script>
@endpush
</main>
