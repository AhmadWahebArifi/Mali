@extends('layouts.app')

@section('title', 'Edit Account')

@section('page-title', 'Edit Account')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
    <!-- Success Message (using Sweet Alert) -->
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            FinTrackAlert.success('Success!', '{{ session('success') }}');
        });
    </script>
    @endif

    <!-- Error Message (using Sweet Alert) -->
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            FinTrackAlert.error('Error', '{{ session('error') }}');
        });
    </script>
    @endif

    <!-- Screen Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="font-h1 text-h1 text-on-surface">Edit Account</h1>
            <p class="font-body-md text-body-sm text-on-surface-variant">Update your account details.</p>
        </div>
        <a href="{{ route('admin.accounts.index') }}" class="flex items-center gap-2 px-4 py-2 border border-outline-variant bg-white text-on-surface rounded-lg font-medium text-sm hover:bg-gray-50 transition-colors">
            <span class="material-symbols-outlined text-sm" data-icon="arrow_back">arrow_back</span>
            Back to Accounts
        </a>
    </div>
    
    <!-- Edit Account Form -->
    <div class="bg-white rounded-xl border border-outline-variant p-6 md:p-8 max-w-2xl">
        <form id="editAccountForm" method="POST" action="{{ route('admin.accounts.update', $account->id) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Account Name -->
                <div>
                    <label for="name" class="block font-label-caps text-label-caps text-on-surface-variant mb-2">Account Name</label>
                    <input type="text" id="name" name="name" required value="{{ old('name', $account->name) }}"
                           class="w-full px-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    @error('name')
                        <p class="mt-1 text-sm text-tertiary">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Balance (Admin Only) -->
                @if(auth()->user()->email === 'admin@mali.com')
                <div>
                    <label for="balance" class="block font-label-caps text-label-caps text-on-surface-variant mb-2">Balance</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-on-surface-variant font-medium">$</span>
                        <input type="number" id="balance" name="balance" required step="0.01" min="0" value="{{ old('balance', $account->balance) }}"
                               class="w-full pl-8 pr-4 py-3 border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                    </div>
                    @error('balance')
                        <p class="mt-1 text-sm text-tertiary">{{ $message }}</p>
                    @enderror
                </div>
                @else
                <!-- Balance Display (Read-only for non-admins) -->
                <div>
                    <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2">Current Balance</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-on-surface-variant font-medium">$</span>
                        <input type="text" value="{{ number_format($account->balance, 2) }}" readonly
                               class="w-full pl-8 pr-4 py-3 border border-outline-variant rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed">
                        <p class="mt-2 text-sm text-on-surface-variant">
                            <span class="material-symbols-outlined text-sm align-middle">info</span>
                            Only administrators can modify account balances. Contact your admin to make changes.
                        </p>
                    </div>
                </div>
                @endif

                <!-- Submit Button -->
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-primary text-white py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-primary/20 hover:bg-primary-container transition-all">
                        <span class="material-symbols-outlined" data-icon="save">save</span>
                        Save Changes
                    </button>
                    <a href="{{ route('admin.accounts.index') }}" class="px-6 py-3 border border-outline-variant bg-white text-on-surface rounded-xl font-bold hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.getElementById('editAccountForm').addEventListener('submit', function(e) {
    e.preventDefault();

    FinTrackAlert.loading('Saving...');

    const formData = new FormData(this);

    fetch('{{ route('admin.accounts.update', $account->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        return { ok: response.ok, status: response.status, data };
    })
    .then(({ ok, status, data }) => {
        if (ok && data.success) {
            FinTrackAlert.success('Success!', data.message).then(() => {
                window.location.href = '{{ route('admin.accounts.index') }}';
            });
            return;
        }

        if (status === 422 && data.errors) {
            const firstErrorField = Object.keys(data.errors)[0];
            const firstError = firstErrorField ? data.errors[firstErrorField][0] : 'Validation error';
            FinTrackAlert.error('Validation Error', firstError);
            return;
        }

        FinTrackAlert.error('Error', data.message || 'Failed to update account');
    })
    .catch(error => {
        FinTrackAlert.error('Error', 'Failed to update account. Please try again.');
        console.error('Error:', error);
    });
});
</script>
@endpush
