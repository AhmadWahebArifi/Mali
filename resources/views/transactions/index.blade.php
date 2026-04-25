@extends('layouts.app')

@section('title', 'Transaction Management')

@section('page-title', 'Transactions')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Success Message (using Sweet Alert) -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            BawarFinTrackAlert.success('Success!', '{{ session('success') }}');
        });
    </script>
    @endif
    <!-- Screen Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="font-h1 text-h1 text-on-surface">Transactions</h1>
            <p class="font-body-md text-body-sm text-on-surface-variant">Review and manage your detailed financial activities.</p>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()->email === 'admin@mali.com')
            <button onclick="openImportModal()" class="flex items-center gap-2 px-4 py-2 border border-outline-variant bg-white text-on-surface rounded-lg font-medium text-sm hover:bg-gray-50 transition-colors">
                <span class="material-symbols-outlined text-sm" data-icon="file_upload">file_upload</span>
                Import CSV
            </button>
            <a href="{{ route('transactions.export.csv', request()->query()) }}" class="flex items-center gap-2 px-4 py-2 border border-outline-variant bg-white text-on-surface rounded-lg font-medium text-sm hover:bg-gray-50 transition-colors">
                <span class="material-symbols-outlined text-sm" data-icon="file_download">file_download</span>
                Export CSV
            </a>
            @endif
            <a href="{{ route('transactions.create') }}" class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg font-medium text-sm hover:bg-primary-container transition-colors shadow-sm">
                <span class="material-symbols-outlined text-sm" data-icon="add">add</span>
                New Transaction
            </a>
        </div>
    </div>
    
    <!-- Filters Bar -->
    <form method="GET" action="{{ route('transactions.index') }}" class="bg-white p-4 rounded-xl border border-outline-variant mb-6 flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2 min-w-[200px]">
            <span class="material-symbols-outlined text-outline text-lg" data-icon="calendar_today">calendar_today</span>
            <select name="date_filter" class="w-full border-none focus:ring-0 text-sm font-medium text-on-surface-variant bg-transparent cursor-pointer" onchange="this.form.submit()">
                <option value="">All Time</option>
                <option value="current_month" {{ request('date_filter') == 'current_month' ? 'selected' : '' }}>Current Month</option>
                <option value="last_30_days" {{ request('date_filter') == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="last_quarter" {{ request('date_filter') == 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
            </select>
        </div>
        <div class="h-6 w-px bg-outline-variant/50 hidden md:block"></div>
        <div class="flex items-center gap-2 min-w-[160px]">
            <span class="material-symbols-outlined text-outline text-lg" data-icon="account_balance_wallet">account_balance_wallet</span>
            <select name="account_id" class="w-full border-none focus:ring-0 text-sm font-medium text-on-surface-variant bg-transparent cursor-pointer" onchange="this.form.submit()">
                <option value="all">All Accounts</option>
                @foreach($accounts as $account)
                <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="h-6 w-px bg-outline-variant/50 hidden md:block"></div>
        <div class="flex items-center gap-2 min-w-[160px]">
            <span class="material-symbols-outlined text-outline text-lg" data-icon="filter_list">filter_list</span>
            <select name="category_id" class="w-full border-none focus:ring-0 text-sm font-medium text-on-surface-variant bg-transparent cursor-pointer" onchange="this.form.submit()">
                <option value="all">All Categories</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" onclick="window.location.href='{{ route('transactions.index') }}'" class="ml-auto text-primary text-sm font-semibold hover:underline">Clear all filters</button>
    </form>
    
    <!-- Transactions Table Card -->
    <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
        <!-- Bulk Actions Header (Hidden by default, shown when items selected) -->
        <div class="px-6 py-3 bg-primary-container text-white flex items-center justify-between hidden" id="bulkActions">
            <div class="flex items-center gap-4">
                <span class="font-medium text-sm">3 Transactions selected</span>
                <div class="w-px h-4 bg-white/30"></div>
                <button class="flex items-center gap-1 text-sm font-semibold hover:opacity-80">
                    <span class="material-symbols-outlined text-base" data-icon="delete">delete</span>
                    Delete
                </button>
                <button class="flex items-center gap-1 text-sm font-semibold hover:opacity-80">
                    <span class="material-symbols-outlined text-base" data-icon="drive_file_move">drive_file_move</span>
                    Change Category
                </button>
            </div>
            <button class="text-sm font-semibold opacity-80 hover:opacity-100">Cancel</button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-left border-b border-outline-variant">
                        <th class="py-4 px-6 w-10">
                            <input class="rounded border-outline-variant text-primary focus:ring-primary h-4 w-4" type="checkbox" id="selectAll">
                        </th>
                        <th class="py-4 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Date</th>
                        <th class="py-4 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Description</th>
                        <th class="py-4 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Category</th>
                        <th class="py-4 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">Account</th>
                        <th class="py-4 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider text-right">Amount</th>
                        <th class="py-4 px-6 w-10"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($transactions as $transaction)
                    <tr class="hover:bg-surface-container-low/50 transition-colors group">
                        <td class="py-4 px-6">
                            <input class="rounded border-outline-variant text-primary focus:ring-primary h-4 w-4 transaction-checkbox" type="checkbox" value="{{ $transaction->id }}">
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-data-mono text-data-mono text-on-surface">{{ \App\Helpers\FormatHelper::date($transaction->date, 'M d, Y') }}</div>
                            <div class="text-[10px] text-on-surface-variant font-medium">{{ \App\Helpers\FormatHelper::time($transaction->created_at, 'h:i A') }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-medium text-on-surface text-sm">{{ $transaction->description }}</div>
                            <div class="text-xs text-on-surface-variant">Transaction ID #{{ $transaction->id }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-2 py-1 {{ $transaction->category->type === 'income' ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container-highest text-on-surface-variant' }} text-[11px] font-bold rounded uppercase">
                                {{ $transaction->category->name }}
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-base text-primary" data-icon="account_balance">account_balance</span>
                                <span class="text-xs font-medium text-on-surface-variant">{{ $transaction->account->name }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="font-data-mono {{ $transaction->type === 'income' ? 'text-secondary' : 'text-error' }} font-bold">
                                {{ $transaction->type === 'income' ? '+' : '-' }}{{ \App\Helpers\FormatHelper::currency($transaction->amount) }}
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="p-1 text-outline opacity-0 group-hover:opacity-100 hover:text-on-surface transition-all" onclick="deleteTransaction({{ $transaction->id }})">
                                <span class="material-symbols-outlined" data-icon="delete">delete</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center">
                            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">receipt_long</span>
                            <p class="text-gray-500 text-lg mb-2">No transactions found</p>
                            <p class="text-gray-400">Start by adding your first transaction</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-outline-variant flex items-center justify-between">
            <div class="text-xs text-on-surface-variant font-medium">
                Showing <span class="text-on-surface">{{ $transactions->firstItem() }} - {{ $transactions->lastItem() }}</span> of <span class="text-on-surface">{{ $totalTransactions }}</span> transactions
            </div>
            <div class="flex items-center gap-1">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</main>

<!-- FAB for Desktop Contextual Action -->
<div class="fixed bottom-8 right-8 hidden md:block">
    <a href="{{ route('transactions.create') }}" class="w-16 h-16 bg-primary text-white rounded-full flex items-center justify-center shadow-xl shadow-primary/40 hover:scale-105 active:scale-95 transition-all group overflow-hidden">
        <span class="material-symbols-outlined text-3xl" data-icon="add">add</span>
        <div class="absolute right-full mr-4 bg-inverse-surface text-inverse-on-surface py-2 px-4 rounded-lg text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">Quick Add Transaction</div>
    </a>
</div>
@endsection

@push('scripts')
<script>
function deleteTransaction(id) {
    BawarFinTrackAlert.deleteConfirm('this transaction').then((result) => {
        if (result.isConfirmed) {
            BawarFinTrackAlert.loading('Deleting...');
            
            fetch(`/transactions/${id}`, {
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
                    BawarFinTrackAlert.error('Error', data.message || 'Failed to delete transaction');
                }
            })
            .catch(error => {
                BawarFinTrackAlert.error('Error', 'Failed to delete transaction. Please try again.');
                console.error('Error:', error);
            });
        }
    });
}

// Select all checkbox functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.transaction-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    toggleBulkActions();
});

// Individual checkbox functionality
document.querySelectorAll('.transaction-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', toggleBulkActions);
});

function toggleBulkActions() {
    const checkedBoxes = document.querySelectorAll('.transaction-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    
    if (checkedBoxes.length > 0) {
        bulkActions.classList.remove('hidden');
        bulkActions.querySelector('span.font-medium.text-sm').textContent = `${checkedBoxes.length} Transaction${checkedBoxes.length > 1 ? 's' : ''} selected`;
    } else {
        bulkActions.classList.add('hidden');
    }
}

// Import Modal Functions
function openImportModal() {
    const modal = document.getElementById('importModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImportModal() {
    const modal = document.getElementById('importModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('importForm').reset();
    document.getElementById('fileInfo').classList.add('hidden');
}

function handleFileSelect(event) {
    const file = event.target.files[0];
    const fileInfo = document.getElementById('fileInfo');
    
    if (file) {
        if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
            BawarFinTrackAlert.error('Invalid File', 'Please select a CSV file');
            event.target.value = '';
            return;
        }
        
        fileInfo.classList.remove('hidden');
        fileInfo.innerHTML = `
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span class="material-symbols-outlined text-sm">description</span>
                <span>${file.name} (${(file.size / 1024).toFixed(2)} KB)</span>
            </div>
        `;
    } else {
        fileInfo.classList.add('hidden');
    }
}

function handleImportSubmit(event) {
    event.preventDefault();
    
    console.log('Import submit started');
    
    const formData = new FormData(event.target);
    const file = formData.get('csv_file');
    
    console.log('File selected:', file ? file.name : 'No file');
    
    if (!file) {
        BawarFinTrackAlert.error('No File', 'Please select a CSV file to import');
        return;
    }
    
    BawarFinTrackAlert.loading('Importing transactions...');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const importUrl = '{{ route('transactions.import') }}';
    
    console.log('Import URL:', importUrl);
    console.log('CSRF Token:', csrfToken ? 'Present' : 'Missing');
    
    fetch(importUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.log('Error response text:', text);
                throw new Error(`HTTP ${response.status}: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            BawarFinTrackAlert.success('Success!', data.message || 'Transactions imported successfully').then(() => {
                closeImportModal();
                window.location.reload();
            });
        } else {
            BawarFinTrackAlert.error('Import Failed', data.message || 'Failed to import transactions');
        }
    })
    .catch(error => {
        console.error('Import error:', error);
        BawarFinTrackAlert.error('Import Error', 'An error occurred while importing transactions: ' + error.message);
    });
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('importModal');
    if (event.target === modal) {
        closeImportModal();
    }
});
</script>

<!-- Import CSV Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary-container rounded-lg flex items-center justify-center text-on-primary-container">
                        <span class="material-symbols-outlined">file_upload</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Import Transactions</h3>
                        <p class="text-sm text-gray-600">Upload a CSV file to import transactions</p>
                    </div>
                </div>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>
        
        <form id="importForm" onsubmit="handleImportSubmit(event)" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">CSV File</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-gray-400 transition-colors">
                    <input type="file" name="csv_file" accept=".csv" onchange="handleFileSelect(event)" class="hidden" id="csvFileInput">
                    <label for="csvFileInput" class="cursor-pointer">
                        <span class="material-symbols-outlined text-3xl text-gray-400 mb-2 block">cloud_upload</span>
                        <span class="text-sm text-gray-600">Click to upload CSV file</span>
                        <span class="text-xs text-gray-500 block mt-1">or drag and drop</span>
                    </label>
                </div>
                <div id="fileInfo" class="mt-2 hidden"></div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <h4 class="text-sm font-medium text-blue-900 mb-2">CSV Format Requirements:</h4>
                <ul class="text-xs text-blue-800 space-y-1">
                    <li>• Date (YYYY-MM-DD format)</li>
                    <li>• Description</li>
                    <li>• Amount (numeric)</li>
                    <li>• Type (income/expense)</li>
                    <li>• Category Name</li>
                    <li>• Account Name (optional)</li>
                </ul>
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeImportModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-container transition-colors font-medium">
                    Import Transactions
                </button>
            </div>
        </form>
    </div>
</div>
@endpush
