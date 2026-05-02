@extends('layouts.app')

@section('title', 'Categories Management')

@section('page-title', 'Categories')

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
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            BawarFinTrackAlert.error('Error', '{{ session('error') }}');
        });
    </script>
    @endif

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
                            <span class="material-symbols-outlined" data-icon="payments">payments</span>
                        </div>
                        <div>
                            <h4 class="font-body-md font-semibold text-on-surface">{{ $category->name }}</h4>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">{{ $category->transactions_count ?? 0 }} Transactions this month</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button type="button" onclick="editCategory({{ $category->id }})" class="p-2 hover:bg-surface-container rounded-lg text-outline">
                            <span class="material-symbols-outlined text-[20px]" data-icon="edit">edit</span>
                        </button>
                        <button type="button" onclick="deleteCategory({{ $category->id }})" class="p-2 hover:bg-error-container/50 rounded-lg text-error">
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
                            <span class="material-symbols-outlined" data-icon="shopping_cart">shopping_cart</span>
                        </div>
                        <div>
                            <h4 class="font-body-md font-semibold text-on-surface">{{ $category->name }}</h4>
                            <p class="font-label-caps text-label-caps text-on-surface-variant">{{ $category->transactions_count ?? 0 }} Transactions this month</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button type="button" onclick="editCategory({{ $category->id }})" class="p-2 hover:bg-surface-container rounded-lg text-outline">
                            <span class="material-symbols-outlined text-[20px]" data-icon="edit">edit</span>
                        </button>
                        <button type="button" onclick="deleteCategory({{ $category->id }})" class="p-2 hover:bg-error-container/50 rounded-lg text-error">
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
                <p class="text-3xl font-black text-secondary">{{ $mostUsedIncome ? $mostUsedIncome->name : 'N/A' }}</p>
            </div>
            <div>
                <p class="font-label-caps text-label-caps text-on-surface-variant mb-1">MOST USED (EXP)</p>
                <p class="text-3xl font-black text-tertiary">{{ $mostUsedExpense ? $mostUsedExpense->name : 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAddCategoryModal(type) {
    if (typeof BawarFinTrackAlert === 'undefined') {
        alert('BawarFinTrackAlert not loaded. Please refresh.');
        return;
    }

    Swal.fire({
        title: 'Add ' + (type === 'income' ? 'Income' : 'Expense') + ' Category',
        html: `
            <div style="text-align:left">
                <label style="display:block;font-weight:600;margin-bottom:6px">Category Name</label>
                <input id="swalCategoryName" class="swal2-input" style="width:100%;margin:0 0 14px 0" placeholder="e.g. Salary, Rent, Groceries">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Create',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#004ccd',
        preConfirm: () => {
            const name = document.getElementById('swalCategoryName').value.trim();
            if (!name) {
                Swal.showValidationMessage('Category name is required');
                return false;
            }
            return { name, type };
        }
    }).then((result) => {
        if (!result.isConfirmed) return;

        BawarFinTrackAlert.loading('Creating category...');

        const formData = new FormData();
        formData.append('name', result.value.name);
        formData.append('type', result.value.type);

        fetch('{{ route('categories.store') }}', {
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
                BawarFinTrackAlert.success('Success!', json.message || 'Category created successfully').then(() => {
                    location.reload();
                });
                return;
            }
            if (status === 422 && json.errors) {
                const firstField = Object.keys(json.errors)[0];
                const firstError = firstField ? json.errors[firstField][0] : 'Validation error';
                BawarFinTrackAlert.error('Validation Error', firstError);
                return;
            }
            BawarFinTrackAlert.error('Error', json.message || 'Failed to create category');
        })
        .catch(() => {
            BawarFinTrackAlert.error('Error', 'Failed to create category. Please try again.');
        });
    });
}

function editCategory(id) {
    if (typeof BawarFinTrackAlert === 'undefined') {
        alert('BawarFinTrackAlert not loaded. Please refresh.');
        return;
    }

    BawarFinTrackAlert.loading('Loading...');

    fetch(`/categories/${id}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success || !data.category) {
            BawarFinTrackAlert.error('Error', 'Failed to load category details');
            return;
        }

        const category = data.category;

        Swal.fire({
            title: 'Edit Category',
            html: `
                <div style="text-align:left">
                    <label style="display:block;font-weight:600;margin-bottom:6px">Category Name</label>
                    <input id="swalCategoryName" class="swal2-input" style="width:100%;margin:0 0 14px 0" value="${String(category.name ?? '').replace(/"/g, '&quot;')}">
                    <label style="display:block;font-weight:600;margin-bottom:6px">Type</label>
                    <select id="swalCategoryType" class="swal2-input" style="width:100%;margin:0">
                        <option value="income" ${category.type === 'income' ? 'selected' : ''}>Income</option>
                        <option value="expense" ${category.type === 'expense' ? 'selected' : ''}>Expense</option>
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#004ccd',
            preConfirm: () => {
                const name = document.getElementById('swalCategoryName').value.trim();
                const type = document.getElementById('swalCategoryType').value;
                if (!name) {
                    Swal.showValidationMessage('Category name is required');
                    return false;
                }
                return { name, type };
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            BawarFinTrackAlert.loading('Saving...');

            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('name', result.value.name);
            formData.append('type', result.value.type);

            fetch(`/categories/${id}`, {
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
                    BawarFinTrackAlert.success('Success!', json.message || 'Category updated successfully').then(() => {
                        location.reload();
                    });
                    return;
                }
                if (status === 422 && json.errors) {
                    const firstField = Object.keys(json.errors)[0];
                    const firstError = firstField ? json.errors[firstField][0] : 'Validation error';
                    BawarFinTrackAlert.error('Validation Error', firstError);
                    return;
                }
                BawarFinTrackAlert.error('Error', json.message || 'Failed to update category');
            })
            .catch(() => {
                BawarFinTrackAlert.error('Error', 'Failed to update category. Please try again.');
            });
        });
    })
    .catch(() => {
        BawarFinTrackAlert.error('Error', 'Failed to load category details');
    });
}

function deleteCategory(id) {
    if (typeof BawarFinTrackAlert === 'undefined') {
        alert('BawarFinTrackAlert not loaded. Please refresh.');
        return;
    }

    BawarFinTrackAlert.deleteConfirm('this category').then((result) => {
        if (result.isConfirmed) {
            BawarFinTrackAlert.loading('Deleting category...');

            fetch(`/categories/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    BawarFinTrackAlert.success('Success!', data.message).then(() => {
                        location.reload();
                    });
                } else {
                    BawarFinTrackAlert.error('Error', data.message || 'Failed to delete category');
                }
            })
            .catch(() => {
                BawarFinTrackAlert.error('Error', 'Failed to delete category. Please try again.');
            });
        }
    });
}
</script>
@endpush
</main>
