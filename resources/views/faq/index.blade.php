@extends('layouts.app')

@section('title', 'FAQ - FinTrack Pro')

@section('page-title', 'Frequently Asked Questions')

@section('content')
<!-- Main Content -->
<main class="flex-1 p-4 md:p-8 max-w-4xl mx-auto w-full">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="font-h1 text-h1 text-on-surface mb-4">Help Center</h1>
        <p class="font-body-md text-body-lg text-on-surface-variant max-w-2xl mx-auto">
            Find answers to common questions about using FinTrack Pro to manage your finances effectively.
        </p>
    </div>
    
    <!-- Search Bar -->
    <div class="mb-8">
        <div class="relative max-w-2xl mx-auto">
            <input type="text" 
                   placeholder="Search for answers..." 
                   class="w-full px-4 py-3 pl-12 pr-4 text-base border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            <span class="material-symbols-outlined absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                search
            </span>
        </div>
    </div>
    
    <!-- FAQ Categories -->
    <div class="space-y-8">
        @foreach($faqs->groupBy('category') as $category => $items)
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-surface-container-low border-b border-outline-variant">
                <h2 class="font-h2 text-lg text-on-surface">{{ $category }}</h2>
            </div>
            <div class="divide-y divide-outline-variant">
                @foreach($items as $index => $faq)
                <div class="faq-item">
                    <button onclick="toggleFAQ({{ $category . '_' . $index }})" 
                            class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-surface-container-low/50 transition-colors">
                        <span class="font-medium text-on-surface pr-4">{{ $faq['question'] }}</span>
                        <span class="material-symbols-outlined text-gray-400 flex-shrink-0 transition-transform" id="icon-{{ $category . '_' . $index }}">
                            expand_more
                        </span>
                    </button>
                    <div id="faq-{{ $category . '_' . $index }}" class="hidden px-6 pb-4">
                        <p class="text-sm text-on-surface-variant leading-relaxed">
                            {{ $faq['answer'] }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Contact Support -->
    <div class="mt-12 text-center bg-surface-container-low rounded-xl p-8">
        <span class="material-symbols-outlined text-4xl text-primary mb-4">support_agent</span>
        <h3 class="font-h2 text-lg text-on-surface mb-2">Still need help?</h3>
        <p class="text-on-surface-variant mb-6">
            Can't find what you're looking for? Our support team is here to help.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button class="px-6 py-3 bg-primary text-on-primary rounded-lg hover:bg-primary-container transition-colors font-medium">
                Contact Support
            </button>
            <button class="px-6 py-3 border border-outline-variant text-on-surface rounded-lg hover:bg-surface-container-low transition-colors font-medium">
                View Documentation
            </button>
        </div>
    </div>
</main>

@push('scripts')
<script>
function toggleFAQ(id) {
    const content = document.getElementById('faq-' + id);
    const icon = document.getElementById('icon-' + id);
    
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}
</script>
@push('scripts')
<style>
.rotate-180 {
    transform: rotate(180deg);
}
</style>
@endsection
