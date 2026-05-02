<!DOCTYPE html>
<html class="{{ $userSettings['theme'] ?? 'light' }}" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BawarFinTrack') - BawarFinTrack</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('icon.svg') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon.svg') }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-tertiary": "#ffffff",
                        "surface-dim": "#d3daea",
                        "on-tertiary-container": "#fff1ef",
                        "on-primary-container": "#f3f3ff",
                        "on-secondary": "#ffffff",
                        "on-tertiary-fixed-variant": "#930013",
                        "tertiary-container": "#d22f32",
                        "surface-container-highest": "#dce2f3",
                        "outline-variant": "#c3c6d8",
                        "on-surface-variant": "#424656",
                        "secondary-fixed": "#6ffbbe",
                        "on-tertiary-fixed": "#410004",
                        "on-primary-fixed": "#00174c",
                        "surface-container-low": "#f0f3ff",
                        "outline": "#737687",
                        "on-error": "#ffffff",
                        "surface": "#f9f9ff",
                        "background": "#f9f9ff",
                        "primary-container": "#0f62fe",
                        "primary": "#004ccd",
                        "error": "#ba1a1a",
                        "secondary-fixed-dim": "#4edea3",
                        "tertiary-fixed-dim": "#ffb3ad",
                        "surface-tint": "#0052dd",
                        "on-secondary-fixed": "#002113",
                        "on-surface": "#151c27",
                        "on-primary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "on-error-container": "#93000a",
                        "surface-container-high": "#e2e8f8",
                        "on-secondary-fixed-variant": "#005236",
                        "secondary-container": "#6cf8bb",
                        "error-container": "#ffdad6",
                        "on-primary-fixed-variant": "#003da9",
                        "on-secondary-container": "#00714d",
                        "secondary": "#006c49",
                        "surface-bright": "#f9f9ff",
                        "inverse-on-surface": "#ebf1ff",
                        "primary-fixed-dim": "#b4c5ff",
                        "inverse-primary": "#b4c5ff",
                        "primary-fixed": "#dbe1ff",
                        "surface-container": "#e7eefe",
                        "inverse-surface": "#2a313d",
                        "on-background": "#151c27",
                        "tertiary": "#af0f1d",
                        "surface-variant": "#dce2f3",
                        "tertiary-fixed": "#ffdad7"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "spacing": {
                        "xs": "4px",
                        "sm": "8px",
                        "gutter": "16px",
                        "md": "16px",
                        "xl": "32px",
                        "xxl": "48px",
                        "container-margin": "16px",
                        "lg": "24px",
                        "base": "4px",
                        "touch-target": "44px"
                    },
                    "fontFamily": {
                        "body-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "h2": ["Inter"],
                        "body-lg": ["Inter"],
                        "h1": ["Inter"],
                        "data-mono": ["Inter"],
                        "label-caps": ["Inter"],
                        "display-financial": ["Inter"]
                    },
                    "fontSize": {
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "h2": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "h1": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "data-mono": ["14px", {"lineHeight": "20px", "fontWeight": "500"}],
                        "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "display-financial": ["40px", {"lineHeight": "48px", "letterSpacing": "-0.02em", "fontWeight": "700"}]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
        body {
            background-color: #f9f9ff;
        }
    </style>
    @stack('styles')
</head>
<body class="font-body-md text-on-background">
    <!-- Desktop Sidebar -->
    @include('layouts.partials.sidebar')
    
    <!-- Top Navigation Bar (Mobile & Tablet) -->
    @include('layouts.partials.navbar')
    
    <!-- Main Canvas -->
    <main class="md:pl-64 min-h-screen">
        @yield('content')
    </main>
    
    <!-- Bottom Navigation Bar (Mobile only) -->
    @include('layouts.partials.bottom-nav')
    
    <script src="{{ asset('js/sweet-alert.js') }}"></script>
    <script>
        // Check if BawarFinTrackAlert is loaded
        if (typeof BawarFinTrackAlert === 'undefined') {
            console.error('BawarFinTrackAlert is not loaded');
        }
        
        // Profile dropdown toggle
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function closeDropdown(e) {
                if (!e.target.closest('.relative')) {
                    dropdown.classList.add('hidden');
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
