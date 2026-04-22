<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>FinTrack Pro - Secure Access</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
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
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-background font-body-md text-on-background min-h-screen selection:bg-primary-container selection:text-on-primary-container">
<main class="grid lg:grid-cols-2 min-h-screen">
    <!-- Left Side: Interactive Forms -->
    <div class="flex items-center justify-center p-4 md:p-8 lg:p-12 bg-surface">
        <div class="w-full max-w-[440px] space-y-8">
            <!-- Brand Header -->
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-3xl" data-icon="account_balance">account_balance</span>
                    <span class="font-h1 text-h2 text-on-surface tracking-tighter">FinTrack Pro</span>
                </div>
                <p class="font-body-md text-on-surface-variant">Enterprise-grade financial intelligence for modern organizations.</p>
            </div>
            
            <!-- Login Form -->
            <section class="space-y-6">
                <div class="space-y-1">
                    <h1 class="font-h1 text-h1 text-on-surface">Secure Login</h1>
                    <p class="font-body-sm text-on-surface-variant">Access your dashboard and real-time analytics.</p>
                </div>
                
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
                <form class="space-y-4">
                    <div class="space-y-1">
                        <label class="font-label-caps text-label-caps text-on-surface-variant uppercase">Full Name</label>
                        <input class="w-full h-11 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all outline-none text-md" placeholder="John Doe" type="text"/>
                    </div>
                    <div class="space-y-1">
                        <label class="font-label-caps text-label-caps text-on-surface-variant uppercase">Work Email</label>
                        <input class="w-full h-11 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all outline-none text-md" placeholder="john@enterprise.com" type="email"/>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase">Password</label>
                            <input class="w-full h-11 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all outline-none text-md" placeholder="••••••••" type="password"/>
                        </div>
                        <div class="space-y-1">
                            <label class="font-label-caps text-label-caps text-on-surface-variant uppercase">Confirm</label>
                            <input class="w-full h-11 px-4 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary-container bg-surface-container-lowest transition-all outline-none text-md" placeholder="••••••••" type="password"/>
                        </div>
                    </div>
                    <button class="w-full h-11 bg-primary text-on-primary font-body-md font-semibold rounded-lg hover:bg-primary-container active:scale-[0.98] transition-all shadow-sm" type="submit">
                        Register Business
                    </button>
                </form>
            </section>
        </div>
    </div>
    
    <!-- Right Side: Brand Showcase -->
    <div class="hidden lg:flex flex-col relative overflow-hidden bg-primary p-8 justify-between">
        <!-- Background Decorative Element -->
        <div class="absolute inset-0 z-0 opacity-10">
            <div class="absolute top-[-10%] right-[-10%] w-[600px] h-[600px] rounded-full bg-surface-container-lowest blur-3xl"></div>
            <div class="absolute bottom-[-20%] left-[-20%] w-[800px] h-[800px] rounded-full bg-secondary-container blur-3xl"></div>
        </div>
        
        <div class="relative z-10 space-y-4">
            <div class="inline-flex items-center gap-2 bg-on-primary/10 backdrop-blur-md px-4 py-2 rounded-full border border-on-primary/20">
                <span class="material-symbols-outlined text-on-primary text-sm" data-icon="verified_user">verified_user</span>
                <span class="font-label-caps text-on-primary text-[10px]">SOC2 TYPE II COMPLIANT</span>
            </div>
            <h2 class="font-h1 text-[48px] leading-tight text-on-primary tracking-tighter">Unified control over every transaction.</h2>
            <p class="font-body-lg text-primary-fixed max-w-md">Real-time visibility into your operational expenses, automated bookkeeping, and predictive cash flow forecasting.</p>
        </div>
        
        <!-- Bento Card Grid Preview -->
        <div class="relative z-10 grid grid-cols-2 gap-4 mt-12">
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-xl flex flex-col gap-2">
                <span class="material-symbols-outlined text-secondary-fixed text-3xl" data-icon="trending_up">trending_up</span>
                <div class="space-y-1">
                    <p class="font-label-caps text-primary-fixed/80">NET REVENUE</p>
                    <p class="font-display-financial text-h1 text-white">$142,850.00</p>
                </div>
            </div>
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-xl flex flex-col gap-2">
                <span class="material-symbols-outlined text-on-tertiary-fixed text-3xl" data-icon="pie_chart">pie_chart</span>
                <div class="space-y-1">
                    <p class="font-label-caps text-primary-fixed/80">EXPENSE RATIO</p>
                    <p class="font-display-financial text-h1 text-white">24.5%</p>
                </div>
            </div>
            <div class="col-span-2 bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-white/30">
                        <img alt="Executive User" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAfugcF9daFFyki9Aaeh3oCHmbYoHy3c_L97MV_3Slvp08sQIFKHucaSehe0gRSEPcOoHn-2CYIoETAOlWxLxiApfTzQOETKwMiUvgNkS8N8iz45HSKXzUpH-TgGzXeeAz7wX8kLKdR-zgk3-7WmF37A8g6CRJxIgPbRuKIaUmD2kxuPuziOd3mv4fpTSG_sQNSqoQNhLoYNbcfLQ2KBRXy7ZKW0vkSx5vV9-kcBebGV67pJoirPLGEj3Myf4_iNe-3rJHms8zv-iF8"/>
                    </div>
                    <div class="space-y-0">
                        <p class="font-body-md font-bold text-white">Marcus Thorne</p>
                        <p class="font-body-sm text-primary-fixed/80">CFO, TechFlow Systems</p>
                    </div>
                </div>
                <div class="flex gap-1">
                    <span class="material-symbols-outlined text-secondary-fixed text-sm" data-icon="star" data-weight="fill">star</span>
                    <span class="material-symbols-outlined text-secondary-fixed text-sm" data-icon="star" data-weight="fill">star</span>
                    <span class="material-symbols-outlined text-secondary-fixed text-sm" data-icon="star" data-weight="fill">star</span>
                    <span class="material-symbols-outlined text-secondary-fixed text-sm" data-icon="star" data-weight="fill">star</span>
                    <span class="material-symbols-outlined text-secondary-fixed text-sm" data-icon="star" data-weight="fill">star</span>
                </div>
            </div>
        </div>
        
        <div class="relative z-10 flex justify-between items-center pt-8 border-t border-white/10">
            <p class="font-body-sm text-primary-fixed/60">© 2024 FinTrack Pro Enterprise</p>
            <div class="flex gap-4">
                <a class="font-body-sm text-primary-fixed/60 hover:text-white" href="#">Security Policy</a>
                <a class="font-body-sm text-primary-fixed/60 hover:text-white" href="#">Privacy</a>
            </div>
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
