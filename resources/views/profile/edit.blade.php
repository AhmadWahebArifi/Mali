@extends('layouts.app')

@section('title', 'Edit Profile - FinTrack Pro')

@section('page-title', 'Edit Profile')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-4xl mx-auto w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="font-h1 text-h1 text-on-surface mb-2">Edit Profile</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">
            Update your personal information and password settings.
        </p>
    </div>
    
    <!-- Profile Form -->
    <form method="POST" action="{{ route('profile.update') }}" class="space-y-8">
        @csrf
        @method('PUT')
        
        <!-- Personal Information -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6">
            <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">person</span>
                Personal Information
            </h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-on-surface-variant mb-2">
                        First Name
                    </label>
                    <input type="text" 
                           id="first_name" 
                           name="first_name" 
                           value="{{ old('first_name', $user->first_name) }}"
                           required
                           class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('first_name')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="last_name" class="block text-sm font-medium text-on-surface-variant mb-2">
                        Last Name
                    </label>
                    <input type="text" 
                           id="last_name" 
                           name="last_name" 
                           value="{{ old('last_name', $user->last_name) }}"
                           required
                           class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('last_name')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6">
                <label for="email" class="block text-sm font-medium text-on-surface-variant mb-2">
                    Email Address
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', $user->email) }}"
                       required
                       class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                @error('email')
                <p class="mt-1 text-sm text-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Password Settings -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6">
            <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">lock</span>
                Password Settings
            </h2>
            <p class="text-sm text-on-surface-variant mb-6">
                Leave blank if you don't want to change your password.
            </p>
            
            <div class="space-y-6">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-on-surface-variant mb-2">
                        Current Password
                    </label>
                    <input type="password" 
                           id="current_password" 
                           name="current_password"
                           class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    @error('current_password')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-on-surface-variant mb-2">
                            New Password
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password"
                               minlength="8"
                               class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        @error('password')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-on-surface-variant mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation"
                               minlength="8"
                               class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        @error('password_confirmation')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="flex flex-col sm:flex-row gap-4 justify-end">
            <a href="{{ route('profile.index') }}" 
               class="px-6 py-3 border border-outline-variant text-on-surface rounded-lg hover:bg-surface-container-low transition-colors font-medium text-center">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-primary text-on-primary rounded-lg hover:bg-primary-container transition-colors font-medium">
                Save Changes
            </button>
        </div>
    </form>
</main>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof BawarFinTrackAlert !== 'undefined') {
            BawarFinTrackAlert.success('Success!', '{{ session('success') }}');
        } else {
            alert('{{ session('success') }}');
        }
    });
</script>
@endif
@endsection
