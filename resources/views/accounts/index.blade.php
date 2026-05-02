@extends('layouts.app')

@section('title', 'Accounts Management')

@section('page-title', 'Accounts')

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

    <!-- Error Message (using Sweet Alert) -->
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            BawarFinTrackAlert.error('Error', '{{ session('error') }}');
        });
    </script>
    @endif
    <!-- Summary Stats (Bento Style) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-outline-variant p-6 rounded-xl flex flex-col gap-1 shadow-sm">
            <p class="font-label-caps text-label-caps text-on-surface-variant">TOTAL BALANCE</p>
            <p class="font-h1 text-h1 text-primary">{{ \App\Helpers\FormatHelper::currency($totalBalance) }}</p>
            <div class="flex items-center gap-1 mt-2 text-secondary">
                <span class="material-symbols-outlined text-sm">trending_up</span>
                <span class="text-xs font-bold">+2.4% this month</span>
            </div>
        </div>
        
        <a href="{{ route('admin.accounts.create') }}" class="bg-white border border-outline-variant p-6 rounded-xl flex flex-col justify-center items-center gap-3 shadow-sm group hover:border-primary cursor-pointer transition-all hover:bg-gray-50" onclick="console.log('Create account clicked');">
            <div class="w-12 h-12 bg-primary-container rounded-full flex items-center justify-center text-on-primary-container group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">add</span>
            </div>
            <p class="font-label-caps text-label-caps text-primary">ADD NEW ACCOUNT</p>
        </a>
        
        <a href="{{ route('admin.accounts.create') }}" class="md:col-span-2 bg-gradient-to-br from-primary to-primary-fixed-variant p-6 rounded-xl text-on-primary flex flex-col justify-between shadow-lg relative overflow-hidden hover:from-primary/90 hover:to-primary-fixed-variant/90 transition-all">
            <div class="relative z-10">
                <p class="font-label-caps text-label-caps opacity-80">QUICK TIP</p>
                <p class="font-body-md text-body-md mt-2 max-w-xs">Connecting your high-interest savings account could increase your projected annual yield by $120.</p>
            </div>
            <div class="relative z-10 mt-4 bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 rounded-lg py-2 px-4 text-sm font-semibold self-start transition-colors inline-block">
                Add Account Now
            </div>
            <span class="absolute -right-8 -bottom-8 material-symbols-outlined text-9xl opacity-10">lightbulb</span>
        </a>
    </div>
    
    <!-- Accounts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($accounts as $account)
        <div class="bg-white border border-outline-variant rounded-xl overflow-hidden shadow-sm flex flex-col">
            <div class="h-2 w-full {{ $account->balance >= 0 ? 'bg-primary' : 'bg-tertiary' }}"></div>
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg {{ $account->balance >= 0 ? 'bg-primary-container text-on-primary-container' : 'bg-tertiary-container text-on-tertiary' }} flex items-center justify-center">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">{{ $account->balance >= 0 ? 'account_balance' : 'credit_card' }}</span>
                        </div>
                        <div>
                            <p class="font-body-md font-bold text-on-surface">{{ $account->name }}</p>
                            <p class="text-xs text-on-surface-variant">Account • •••• {{ str_pad($account->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <button type="button" onclick="editAccount({{ $account->id }})" class="p-2 hover:bg-surface-container rounded-lg transition-colors text-outline">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </button>
                        <button type="button" onclick="deleteAccount({{ $account->id }})" class="p-2 hover:bg-error-container hover:text-error rounded-lg transition-colors text-outline">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </div>
                </div>
                <div class="mt-auto">
                    <p class="font-label-caps text-label-caps text-on-surface-variant">{{ $account->balance >= 0 ? 'CURRENT BALANCE' : 'OUTSTANDING DEBT' }}</p>
                    <p class="font-h2 text-h2 {{ $account->balance >= 0 ? 'text-on-surface' : 'text-tertiary' }}">
                        {{ \App\Helpers\FormatHelper::currency(abs($account->balance), null, false) }}
                    </p>
                </div>
            </div>
        </div>
        @empty
        <div class="md:col-span-3 text-center py-12">
            <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">account_balance</span>
            <p class="text-gray-500 text-lg mb-2">No accounts yet</p>
            <p class="text-gray-400">Add your first account to start tracking your finances</p>
        </div>
        @endforelse
        
        <!-- Empty State / Add Card -->
        <a href="{{ route('admin.accounts.create') }}" class="border-2 border-dashed border-outline-variant rounded-xl p-6 flex flex-col items-center justify-center gap-4 hover:border-primary hover:bg-surface-container-low cursor-pointer transition-all group hover:bg-gray-50">
            <div class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-outline group-hover:text-primary">
                <span class="material-symbols-outlined text-3xl">add</span>
            </div>
            <div class="text-center">
                <p class="font-body-md font-bold text-on-surface-variant group-hover:text-primary">Add Another Account</p>
                <p class="text-xs text-outline">Connect via Plaid or add manually</p>
            </div>
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Accounts page loaded');
    console.log('BawarFinTrackAlert available:', typeof BawarFinTrackAlert !== 'undefined');
    console.log('Swal available:', typeof Swal !== 'undefined');
});

function editAccount(id) {
    console.log('Edit account called with ID:', id);
    
    if (typeof BawarFinTrackAlert === 'undefined') {
        alert('BawarFinTrackAlert is not loaded. Please refresh the page.');
        return;
    }
    
    BawarFinTrackAlert.loading('Loading...');

    fetch(`/accounts/${id}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success || !data.account) {
            BawarFinTrackAlert.error('Error', 'Failed to load account details');
            return;
        }

        const account = data.account;
        const isAdmin = '{{ auth()->user()->email }}' === 'admin@mali.com';

        // Build form HTML based on user role
        let formHtml = `
            <div style="text-align:left">
                <label style="display:block;font-weight:600;margin-bottom:6px">Account Name</label>
                <input id="swalAccountName" class="swal2-input" style="width:100%;margin:0 0 14px 0" value="${String(account.name ?? '').replace(/"/g, '&quot;')}">
        `;

        if (isAdmin) {
            formHtml += `
                <label style="display:block;font-weight:600;margin-bottom:6px">Balance</label>
                <input id="swalAccountBalance" type="number" step="0.01" min="0" class="swal2-input" style="width:100%;margin:0" value="${account.balance ?? 0}">
            `;
        } else {
            formHtml += `
                <div style="padding:10px;background:#f5f5f5;border-radius:6px;margin-bottom:14px">
                    <div style="font-weight:600;margin-bottom:4px">Current Balance</div>
                    <div style="font-size:18px;color:#333">$${Math.round((account.balance ?? 0) * 100) / 100}</div>
                    <div style="font-size:12px;color:#666;margin-top:4px">Only administrators can modify account balances.</div>
                </div>
            `;
        }

        formHtml += `</div>`;

        Swal.fire({
            title: 'Edit Account',
            html: formHtml,
            showCancelButton: true,
            confirmButtonText: 'Save',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#004ccd',
            preConfirm: () => {
                const name = document.getElementById('swalAccountName').value.trim();
                
                if (!name) {
                    Swal.showValidationMessage('Account name is required');
                    return false;
                }

                const result = { name };
                
                if (isAdmin) {
                    const balanceValue = document.getElementById('swalAccountBalance').value;
                    const balance = parseFloat(balanceValue);
                    
                    if (Number.isNaN(balance) || balance < 0) {
                        Swal.showValidationMessage('Balance must be a valid number (0 or more)');
                        return false;
                    }
                    
                    result.balance = balance;
                } else {
                    result.balance = account.balance; // Keep existing balance for non-admins
                }

                return result;
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            BawarFinTrackAlert.loading('Saving...');

            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('name', result.value.name);
            formData.append('balance', result.value.balance);

            fetch(`/accounts/${id}`, {
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
                    BawarFinTrackAlert.success('Success!', json.message || 'Account updated successfully').then(() => {
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

                BawarFinTrackAlert.error('Error', json.message || 'Failed to update account');
            })
            .catch(() => {
                BawarFinTrackAlert.error('Error', 'Failed to update account. Please try again.');
            });
        });
    })
    .catch(() => {
        BawarFinTrackAlert.error('Error', 'Failed to load account details');
    });
}

function deleteAccount(id) {
    console.log('Delete account called with ID:', id);
    
    if (typeof BawarFinTrackAlert === 'undefined') {
        alert('BawarFinTrackAlert is not loaded. Please refresh the page.');
        return;
    }
    
    BawarFinTrackAlert.deleteConfirm('this account').then((result) => {
        if (result.isConfirmed) {
            BawarFinTrackAlert.loading('Deleting account...');
            
            fetch(`/accounts/${id}`, {
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
                    BawarFinTrackAlert.error('Error', data.message || 'Failed to delete account');
                }
            })
            .catch(error => {
                BawarFinTrackAlert.error('Error', 'Failed to delete account. Please try again.');
                console.error('Error:', error);
            });
        }
    });
}
</script>
@endpush
</main>
