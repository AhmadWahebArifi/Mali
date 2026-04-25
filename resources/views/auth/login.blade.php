<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>BawarFinTrack - Secure Access</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        
        /* Custom animations for logo */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { 
                box-shadow: 0 0 20px rgba(0, 76, 205, 0.3);
                border-color: rgba(0, 76, 205, 0.3);
            }
            50% { 
                box-shadow: 0 0 30px rgba(0, 76, 205, 0.5);
                border-color: rgba(0, 76, 205, 0.5);
            }
        }
        
        .logo-container {
            animation: float 6s ease-in-out infinite;
        }
        
        .logo-glow {
            animation: pulse-glow 4s ease-in-out infinite;
        }
        
        /* Gradient text animation */
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .gradient-text {
            background-size: 200% 200%;
            animation: gradient-shift 3s ease-in-out infinite;
        }
        
        /* Particle animation */
        @keyframes particle-float {
            0% { transform: translateY(0px) rotate(0deg); opacity: 0.3; }
            50% { transform: translateY(-5px) rotate(180deg); opacity: 0.8; }
            100% { transform: translateY(0px) rotate(360deg); opacity: 0.3; }
        }
        
        .particle {
            animation: particle-float 3s ease-in-out infinite;
        }
        
        .delay-75 { animation-delay: 0.75s; }
        .delay-150 { animation-delay: 1.5s; }
    </style>
</head>
<body class="bg-background font-body-md text-on-background min-h-screen selection:bg-primary-container selection:text-on-primary-container">
<main class="grid lg:grid-cols-2 min-h-screen">
    <!-- Left Side: Interactive Forms -->
    <div class="flex items-center justify-center p-4 md:p-8 lg:p-12 bg-surface">
        <div class="w-full max-w-[440px] space-y-8">
            <!-- Brand Header -->
            <div class="space-y-4">
                <div class="flex items-center justify-center">
                    <div class="relative group logo-container">
                        <!-- Logo Container with Enhanced Animation -->
                        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary/10 to-primary/5 p-6 shadow-xl ring-1 ring-primary/20 transition-all duration-300 group-hover:shadow-2xl group-hover:ring-primary/30 group-hover:scale-[1.02] logo-glow">
                            <!-- Logo Image with Enhanced Effects -->
                            <img src="{{ asset('logo.png') }}" 
                                 alt="BawarFinTrack Logo" 
                                 class="w-24 h-24 object-contain transition-transform duration-300 group-hover:scale-110"
                                 style="filter: drop-shadow(0 4px 6px rgba(0, 76, 205, 0.1));">
                        </div>
                        
                        <!-- Enhanced Floating Particles Effect -->
                        <div class="absolute -top-2 -right-2 w-4 h-4 bg-primary/30 rounded-full particle"></div>
                        <div class="absolute -bottom-1 -left-1 w-3 h-3 bg-secondary/30 rounded-full particle delay-75"></div>
                        <div class="absolute top-1/3 -right-3 w-2 h-2 bg-tertiary/30 rounded-full particle delay-150"></div>
                        <div class="absolute -top-3 left-2 w-2 h-2 bg-primary/20 rounded-full particle" style="animation-delay: 2s;"></div>
                        <div class="absolute bottom-2 right-2 w-3 h-3 bg-secondary/20 rounded-full particle" style="animation-delay: 1s;"></div>
                    </div>
                </div>
                
                <!-- Brand Name with Enhanced Typography -->
                <div class="text-center space-y-2">
                    <h1 class="font-h1 text-h1 text-on-surface tracking-tight font-bold bg-gradient-to-r from-primary via-primary/90 to-primary/80 bg-clip-text text-transparent gradient-text">
                        BawarFinTrack
                    </h1>
                    <p class="font-body-md text-on-surface-variant leading-relaxed max-w-sm mx-auto">
                        Enterprise-grade financial intelligence for modern organizations.
                    </p>
                </div>
            </div>
            
            <!-- Login Form -->
            <section class="space-y-6">
                <div class="space-y-1">
                    <h1 class="font-h1 text-h1 text-on-surface">Secure Login</h1>
                    <p class="font-body-sm text-on-surface-variant">Access your dashboard and real-time analytics.</p>
                </div>
                
                @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: '{{ session('success') }}',
                            confirmButtonColor: '#004ccd',
                            confirmButtonText: 'Got it!'
                        });
                    });
                </script>
                @endif
                
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
                
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="font-label-caps text-label-caps text-on-surface-variant uppercase" for="email">Corporate Email</label>
                        <input 
                            class="w-full h-11 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all outline-none text-md @error('email') border-error @enderror" 
                            id="email" 
                            name="email" 
                            type="email" 
                            value="{{ old('email') }}"
                            placeholder="name@company.com" 
                            required
                            autocomplete="email"
                            autofocus>
                    </div>
                    
                    <div class="space-y-1">
                        <div class="flex justify-between items-center">
                            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase" for="password">Password</label>
                            <a class="font-body-sm text-primary hover:underline" href="#">Forgot password?</a>
                        </div>
                        <input 
                            class="w-full h-11 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all outline-none text-md @error('password') border-error @enderror" 
                            id="password" 
                            name="password" 
                            type="password" 
                            placeholder="••••••••" 
                            required
                            autocomplete="current-password">
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <input 
                            class="w-4 h-4 rounded text-primary border-outline-variant focus:ring-primary-container" 
                            id="remember" 
                            name="remember" 
                            type="checkbox">
                        <label class="font-body-sm text-on-surface" for="remember">Keep me signed in for 30 days</label>
                    </div>
                    
                    <button 
                        type="submit" 
                        class="w-full h-11 bg-primary text-on-primary font-body-md font-semibold rounded-lg hover:bg-primary-container active:scale-[0.98] transition-all shadow-sm">
                        Sign In to Account
                    </button>
                </form>
                
                <div class="pt-4 border-t border-outline-variant text-center">
                    <p class="font-body-sm text-on-surface-variant">
                        New to FinTrack? 
                        <a class="font-body-sm text-primary font-semibold hover:underline" href="#register">Create an enterprise account</a>
                    </p>
                </div>
            </section>
            
            <!-- Registration Flow (Hidden by default) -->
            <section class="hidden space-y-6" id="register">
                <div class="space-y-1">
                    <h1 class="font-h1 text-h1 text-on-surface">Create Account</h1>
                    <p class="font-body-sm text-on-surface-variant">Join 5,000+ businesses managing assets on FinTrack.</p>
                </div>
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf
                    <div class="space-y-1">
                        <label class="font-label-caps text-label-caps text-on-surface-variant uppercase">Full Name</label>
                        <div class="grid grid-cols-2 gap-4">
                            <input 
                                name="first_name"
                                class="w-full h-11 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all outline-none text-md @error('first_name') border-error @enderror" 
                                placeholder="First Name" 
                                type="text"
                                value="{{ old('first_name') }}"
                                required>
                            <input 
                                name="last_name"
                                class="w-full h-11 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all outline-none text-md @error('last_name') border-error @enderror" 
                                placeholder="Last Name" 
                                type="text"
                                value="{{ old('last_name') }}"
                                required>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="font-label-caps text-label-caps text-on-surface-variant uppercase">Work Email</label>
                        <input 
                            name="email"
                            class="w-full h-11 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all outline-none text-md @error('email') border-error @enderror" 
                            placeholder="john@enterprise.com" 
                            type="email"
                            value="{{ old('email') }}"
                            required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase">Password</label>
                            <input 
                                name="password"
                                class="w-full h-11 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all outline-none text-md @error('password') border-error @enderror" 
                                placeholder="••••••••" 
                                type="password"
                                required>
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase">Confirm</label>
                            <input 
                                name="password_confirmation"
                                class="w-full h-11 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all outline-none text-md @error('password_confirmation') border-error @enderror" 
                                placeholder="••••••••" 
                                type="password"
                                required>
                        </div>
                    </div>
                    <button class="w-full h-11 bg-primary text-on-primary font-body-md font-semibold rounded-lg hover:bg-primary-container active:scale-[0.98] transition-all shadow-sm" type="submit">
                        Register Business
                    </button>
                </form>
                
                <div class="pt-4 border-t border-outline-variant text-center">
                    <p class="font-body-sm text-on-surface-variant">
                        Already have an account? 
                        <a class="font-body-sm text-primary font-semibold hover:underline" href="#login">Back to login</a>
                    </p>
                </div>
            </section>
        </div>
    </div>
    
    <!-- Right Side: Large Logo Showcase -->
    <div class="hidden lg:flex flex-col relative overflow-hidden bg-primary items-center justify-center p-8">
        <!-- Dynamic Background Effects -->
        <div class="absolute inset-0 z-0">
            <!-- Animated Gradient Orbs -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-white/3 rounded-full blur-3xl"></div>
            
            <!-- Floating Light Beams -->
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-white/10 to-transparent animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-white/10 to-transparent animate-pulse" style="animation-delay: 1s;"></div>
        </div>
        
        <!-- Large Logo Container -->
        <div class="relative z-10 flex flex-col items-center justify-center space-y-8">
            <!-- Massive Logo Display -->
            <div class="relative group">
                <!-- Outer Glow Ring -->
                <div class="absolute inset-0 w-80 h-80 rounded-full bg-white/10 blur-2xl scale-110 group-hover:scale-125 transition-transform duration-1000"></div>
                
                <!-- Middle Glow Ring -->
                <div class="absolute inset-4 w-72 h-72 rounded-full bg-white/15 blur-xl scale-105 group-hover:scale-110 transition-transform duration-1000 delay-75"></div>
                
                <!-- Inner Logo Container -->
                <div class="relative w-64 h-64 rounded-3xl bg-gradient-to-br from-white/20 to-white/5 backdrop-blur-xl border border-white/30 shadow-2xl flex items-center justify-center transition-all duration-500 group-hover:scale-105 group-hover:rotate-3">
                    <!-- Logo Image -->
                    <img src="{{ asset('logo.png') }}" 
                         alt="BawarFinTrack Logo" 
                         class="w-48 h-48 object-contain transition-all duration-500 group-hover:scale-110 group-hover:drop-shadow-2xl"
                         style="filter: drop-shadow(0 20px 40px rgba(255, 255, 255, 0.3));">
                </div>
                
                <!-- Orbiting Particles -->
                <div class="absolute inset-0 w-80 h-80">
                    <div class="absolute top-0 left-1/2 w-4 h-4 bg-white/40 rounded-full animate-spin" style="animation-duration: 20s; transform-origin: center 160px;">
                        <div class="w-full h-full bg-white rounded-full"></div>
                    </div>
                    <div class="absolute top-0 left-1/2 w-3 h-3 bg-white/30 rounded-full animate-spin" style="animation-duration: 15s; transform-origin: center 160px; animation-direction: reverse;">
                        <div class="w-full h-full bg-white rounded-full"></div>
                    </div>
                    <div class="absolute top-0 left-1/2 w-2 h-2 bg-white/50 rounded-full animate-spin" style="animation-duration: 25s; transform-origin: center 160px;">
                        <div class="w-full h-full bg-white rounded-full"></div>
                    </div>
                </div>
                
                <!-- Floating Particles -->
                <div class="absolute -top-4 -right-4 w-6 h-6 bg-white/30 rounded-full animate-bounce"></div>
                <div class="absolute -bottom-4 -left-4 w-4 h-4 bg-white/25 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
                <div class="absolute top-1/2 -right-8 w-3 h-3 bg-white/35 rounded-full animate-bounce" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/3 -left-6 w-5 h-5 bg-white/20 rounded-full animate-bounce" style="animation-delay: 1.5s;"></div>
            </div>
            
            <!-- Brand Name with Massive Typography -->
            <div class="text-center space-y-4">
                <h1 class="font-display-financial text-6xl lg:text-7xl font-bold text-white tracking-tight">
                    BawarFinTrack
                </h1>
                <p class="font-body-lg text-white/80 text-xl max-w-lg mx-auto leading-relaxed">
                    Enterprise Financial Management Platform
                </p>
            </div>
        </div>
        
        <!-- Subtle Footer -->
        <div class="absolute bottom-8 left-0 right-0 text-center z-10">
            <p class="font-body-sm text-white/60">© 2024 BawarFinTrack Enterprise</p>
        </div>
    </div>
</main>

<!-- Success Toast (Hidden by default) -->
<div class="fixed top-8 right-8 hidden z-50" id="successToast">
    <div class="bg-surface-container-highest border border-primary/20 shadow-xl rounded-xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center">
            <span class="material-symbols-outlined text-on-secondary-container" data-icon="check_circle">check_circle</span>
        </div>
        <div class="space-y-0">
            <p class="font-body-md font-bold text-on-surface">Secure Access Granted</p>
            <p class="font-body-sm text-on-surface-variant">Redirecting to dashboard...</p>
        </div>
    </div>
</div>

<script>
// Toggle between login and register forms
document.querySelectorAll('a[href="#register"]').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelector('section:not([id="register"])').classList.add('hidden');
        document.getElementById('register').classList.remove('hidden');
    });
});

// Handle "Back to login" link
document.querySelectorAll('a[href="#login"]').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('register').classList.add('hidden');
        document.querySelector('section:not([id="register"])').classList.remove('hidden');
    });
});

// Form validation and submission feedback
document.querySelector('form[method="POST"]').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    if (!email || !password) {
        e.preventDefault();
        return;
    }
    
    // Show success toast (for demo purposes)
    document.getElementById('successToast').classList.remove('hidden');
});
</script>
</body>
</html>
