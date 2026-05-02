@extends('layouts.app')

@section('title', 'Register - FinTrack Pro')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-surface py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="flex items-center gap-2 justify-center">
                <span class="material-symbols-outlined text-primary text-3xl">account_balance</span>
                <span class="font-h1 text-h2 text-on-surface tracking-tighter">FinTrack Pro</span>
            </div>
            <h2 class="mt-6 text-center font-h1 text-h1 text-on-surface">Create your account</h2>
            <p class="mt-2 text-center text-sm text-on-surface-variant">
                Join 5,000+ businesses managing assets on FinTrack.
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="space-y-4">
                @if($errors->any())
                <div class="bg-error-container text-on-error-container p-4 rounded-lg">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined">error</span>
                        <div>
                            @foreach($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-on-surface-variant">First Name</label>
                        <input id="first_name" name="first_name" type="text" required 
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-outline-variant placeholder-gray-500 text-on-surface rounded-lg focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm @error('first_name') border-error @enderror"
                               value="{{ old('first_name') }}" placeholder="John">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-on-surface-variant">Last Name</label>
                        <input id="last_name" name="last_name" type="text" required 
                               class="mt-1 appearance-none relative block w-full px-3 py-2 border border-outline-variant placeholder-gray-500 text-on-surface rounded-lg focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm @error('last_name') border-error @enderror"
                               value="{{ old('last_name') }}" placeholder="Doe">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-on-surface-variant">Email</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-outline-variant placeholder-gray-500 text-on-surface rounded-lg focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm @error('email') border-error @enderror"
                           value="{{ old('email') }}" placeholder="name@company.com">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-on-surface-variant">Password</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-outline-variant placeholder-gray-500 text-on-surface rounded-lg focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm @error('password') border-error @enderror"
                           placeholder="••••••••">
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-on-surface-variant">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-2 border border-outline-variant placeholder-gray-500 text-on-surface rounded-lg focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm @error('password_confirmation') border-error @enderror"
                           placeholder="••••••••">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-primary hover:bg-primary-container focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Register Business
                </button>
            </div>
            
            <div class="text-center">
                <span class="text-sm text-on-surface-variant">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">
                        Sign in
                    </a>
                </span>
            </div>
        </form>
    </div>
</div>
@endsection