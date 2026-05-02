@extends('layouts.app')

@section('title', 'Help - FinTrack Pro')

@section('page-title', 'Help & Support')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-4xl mx-auto w-full">
    <!-- Help Header -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-8 mb-8">
        <div class="flex items-center gap-4 mb-6">
            <span class="material-symbols-outlined text-4xl text-primary">help</span>
            <div>
                <h1 class="font-h1 text-h1 text-on-surface mb-2">Help & Support</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">Find answers to common questions and get support.</p>
            </div>
        </div>
    </div>
    
    <!-- Help Categories -->
    <div class="grid md:grid-cols-2 gap-8 mb-8">
        <!-- Getting Started -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6">
            <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">rocket_launch</span>
                Getting Started
            </h2>
            <div class="space-y-4">
                <div class="border-l-4 border-primary pl-4">
                    <h3 class="font-medium text-on-surface mb-1">Creating Your First Account</h3>
                    <p class="text-sm text-on-surface-variant">Learn how to set up your first financial account and start tracking transactions.</p>
                </div>
                <div class="border-l-4 border-secondary pl-4">
                    <h3 class="font-medium text-on-surface mb-1">Adding Transactions</h3>
                    <p class="text-sm text-on-surface-variant">Step-by-step guide to recording income and expenses.</p>
                </div>
                <div class="border-l-4 border-tertiary pl-4">
                    <h3 class="font-medium text-on-surface mb-1">Understanding Categories</h3>
                    <p class="text-sm text-on-surface-variant">How to organize your transactions with categories.</p>
                </div>
            </div>
        </div>
        
        <!-- Features -->
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6">
            <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">stars</span>
                Features
            </h2>
            <div class="space-y-4">
                <div class="border-l-4 border-success pl-4">
                    <h3 class="font-medium text-on-surface mb-1">Reports & Analytics</h3>
                    <p class="text-sm text-on-surface-variant">Generate detailed reports and analyze your financial data.</p>
                </div>
                <div class="border-l-4 border-warning pl-4">
                    <h3 class="font-medium text-on-surface mb-1">Budget Tracking</h3>
                    <p class="text-sm text-on-surface-variant">Set budgets and monitor your spending across categories.</p>
                </div>
                <div class="border-l-4 border-error pl-4">
                    <h3 class="font-medium text-on-surface mb-1">Data Export</h3>
                    <p class="text-sm text-on-surface-variant">Export your financial data in various formats.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Common Issues -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6 mb-8">
        <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">troubleshoot</span>
            Common Issues & Solutions
        </h2>
        <div class="space-y-6">
            <div class="border border-outline-variant rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-warning mt-1">help_outline</span>
                    <div class="flex-1">
                        <h3 class="font-medium text-on-surface mb-2">How do I reset my password?</h3>
                        <p class="text-sm text-on-surface-variant mb-3">Click on "Edit Profile" and update your password in the security section.</p>
                        <a href="{{ route('profile.edit') }}" class="text-sm text-primary hover:underline">Go to Profile Settings →</a>
                    </div>
                </div>
            </div>
            
            <div class="border border-outline-variant rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-info mt-1">help_outline</span>
                    <div class="flex-1">
                        <h3 class="font-medium text-on-surface mb-2">Can I export my data?</h3>
                        <p class="text-sm text-on-surface-variant mb-3">Yes! Use the Export Data feature in your profile to download all your financial information.</p>
                        <a href="{{ route('profile.export') }}" class="text-sm text-primary hover:underline">Export Data →</a>
                    </div>
                </div>
            </div>
            
            <div class="border border-outline-variant rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-success mt-1">help_outline</span>
                    <div class="flex-1">
                        <h3 class="font-medium text-on-surface mb-2">How do I delete an account?</h3>
                        <p class="text-sm text-on-surface-variant mb-3">Navigate to the accounts section and use the delete option for any account you wish to remove.</p>
                        <span class="text-sm text-on-surface-variant">Note: This action cannot be undone.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contact Support -->
    <div class="bg-white rounded-xl border border-outline-variant shadow-sm p-6 mb-8">
        <h2 class="font-h2 text-lg text-on-surface mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">contact_support</span>
            Contact Support
        </h2>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center">
                <span class="material-symbols-outlined text-3xl text-primary mb-3">email</span>
                <h3 class="font-medium text-on-surface mb-2">Email Support</h3>
                <p class="text-sm text-on-surface-variant">Get help via email</p>
                <a href="mailto:support@mali.com" class="text-sm text-primary hover:underline">support@mali.com</a>
            </div>
            <div class="text-center">
                <span class="material-symbols-outlined text-3xl text-primary mb-3">chat</span>
                <h3 class="font-medium text-on-surface mb-2">Live Chat</h3>
                <p class="text-sm text-on-surface-variant">Chat with our team</p>
                <button class="text-sm text-primary hover:underline">Start Chat</button>
            </div>
            <div class="text-center">
                <span class="material-symbols-outlined text-3xl text-primary mb-3">forum</span>
                <h3 class="font-medium text-on-surface mb-2">FAQ</h3>
                <p class="text-sm text-on-surface-variant">Browse common questions</p>
                <a href="{{ route('faq.index') }}" class="text-sm text-primary hover:underline">View FAQ</a>
            </div>
        </div>
    </div>
    
    <!-- Back to Profile -->
    <div class="text-center">
        <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-outline text-on-outline rounded-lg hover:bg-surface-container-low transition-colors font-medium">
            <span class="material-symbols-outlined">arrow_back</span>
            Back to Profile
        </a>
    </div>
</main>
@endsection
