<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Waitinglist</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Inter font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Quill Editor CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <!-- Quill Editor JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <!-- Tailwind config for CDN -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ff6b00',
                        secondary: '#333333',
                        accent: '#ff6b00',
                        muted: '#888888',
                        background: '#ffffff',
                        foreground: '#111827',
                        card: '#ffffff',
                        'card-foreground': '#111827',
                        border: '#e5e7eb',
                        input: '#e5e7eb',
                        ring: '#ff6b00',
                        'primary-foreground': '#ffffff',
                        'secondary-foreground': '#ffffff',
                        destructive: '#ef4444',
                        'destructive-foreground': '#ffffff',
                        'muted-foreground': '#6b7280',
                        popover: '#ffffff',
                        'popover-foreground': '#111827'
                    },
                    fontFamily: {
                        sans: ['Inter', 'Helvetica Neue', 'sans-serif']
                    },
                    borderRadius: {
                        xl: '12px',
                        '2xl': '16px'
                    },
                    spacing: {
                        section: '3rem',
                        container: '2rem'
                    },
                    maxWidth: {
                        container: '960px'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'scale-in': 'scaleIn 0.2s ease-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(10px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.95)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>

    <!-- Enhanced Custom Styles -->
    <style>
        :root {
            --color-primary: #ff6b00;
            --color-secondary: #333333;
            --color-background: #ffffff;
            --color-foreground: #111827;
            --color-muted: #888888;
            --color-border: #e5e7eb;
            --container-max: 1280px;
            --section-padding: 1rem;
            --section-gap: 1.5rem;
            --radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", "Helvetica Neue", sans-serif;
            background: var(--color-background);
            color: var(--color-foreground);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Typography Hierarchy */
        h1 { 
            font-size: 2.25rem; 
            font-weight: 700; 
            color: var(--color-primary); 
            letter-spacing: -0.025em;
            line-height: 1.2;
        }
        
        h2 { 
            font-size: 1.75rem; 
            font-weight: 600; 
            color: var(--color-secondary);
            letter-spacing: -0.025em;
            line-height: 1.3;
        }
        
        h3 { 
            font-size: 1.25rem; 
            font-weight: 500; 
            color: #444444;
            line-height: 1.4;
        }
        
        p { 
            font-size: 1rem; 
            font-weight: 400; 
            color: #555555;
            line-height: 1.6;
        }
        
        small { 
            font-size: 0.875rem; 
            font-weight: 400; 
            color: #888888;
        }

        /* Layout Utilities */
        .app-container {
            width: 100%;
            max-width: var(--container-max);
            margin: 0 auto;
            padding: 1rem 2rem;
        }
        
        .section {
            margin-bottom: 1.5rem;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        /* Card Styles */
        .card {
            background: var(--color-background);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            transition: all 0.2s ease-in-out;
        }
        
        .card:hover {
            box-shadow: var(--shadow-xl);
            transform: translateY(-1px);
        }

        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--color-border);
        }

        .card-content {
            padding: 1.5rem;
        }

        .card-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--color-border);
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }

        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--color-primary), 0 0 0 4px rgba(255, 107, 0, 0.2);
        }

        .btn-primary {
            background: var(--color-primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #e66000;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: var(--color-secondary);
            color: #ffffff;
        }

        .btn-secondary:hover {
            background: #1a1a1a;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline {
            background: transparent;
            color: var(--color-secondary);
            border: 1px solid var(--color-border);
        }

        .btn-outline:hover {
            background: #f9fafb;
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .btn-ghost {
            background: transparent;
            color: var(--color-secondary);
        }

        .btn-ghost:hover {
            background: #f3f4f6;
        }

        /* Navigation Styles */
        .navbar {
            background: var(--color-primary);
            box-shadow: var(--shadow-lg);
        }

        .navbar-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #ffffff;
            opacity: 0.9;
            transition: all 0.2s ease-in-out;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
        }

        .navbar-link:hover, .navbar-link.active {
            opacity: 1;
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-1px);
        }

        /* Mobile Navigation */
        .mobile-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #ffffff;
            opacity: 0.9;
            transition: all 0.2s ease-in-out;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 0.25rem;
        }

        .mobile-nav-link:hover, .mobile-nav-link.active {
            opacity: 1;
            background: rgba(255, 255, 255, 0.12);
        }

        .mobile-nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--color-secondary);
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            height: 48px;
            min-height: 48px;
            border: 1px solid var(--color-border);
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.2s ease-in-out;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
        }

        /* Grid Layouts */
        .grid-responsive {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .grid-stats {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        /* Responsive Utilities */
        @media (max-width: 768px) {
            .app-container {
                padding: 0.75rem 1rem;
            }

            .section {
                margin-bottom: 1rem;
            }

            h1 { font-size: 1.875rem; }
            h2 { font-size: 1.5rem; }
            h3 { font-size: 1.125rem; }
        }

        /* Animation Classes */
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        .animate-slide-up {
            animation: slideUp 0.3s ease-out;
        }

        .animate-scale-in {
            animation: scaleIn 0.2s ease-out;
        }

        /* Utility Classes */
        .text-muted { color: var(--color-muted); }
        .text-primary { color: var(--color-primary); }
        .bg-primary { background-color: var(--color-primary); }
        .border-primary { border-color: var(--color-primary); }

        /* Loading States */
        .loading {
            position: relative;
            overflow: hidden;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--color-primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #e66000;
        }
    </style>

    @yield('head')
</head>
<body class="min-h-screen bg-gray-50">
    @auth
        <!-- Enhanced Navigation -->
        <nav class="navbar">
            <div class="app-container">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-white text-xl font-bold flex items-center gap-2">
                                <i data-lucide="layout-dashboard" class="w-6 h-6"></i>
                                Waitinglist Admin
                            </h1>
                        </div>
                        <!-- Desktop Navigation -->
                        <div class="hidden md:ml-8 md:flex md:space-x-1">
                            <a href="{{ route('admin.dashboard') }}"
                               class="navbar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i data-lucide="home" class="w-4 h-4"></i>
                                Dashboard
                            </a>
                            <a href="{{ route('admin.restaurants') }}"
                               class="navbar-link {{ request()->routeIs('admin.restaurants*') ? 'active' : '' }}">
                                <i data-lucide="building" class="w-4 h-4"></i>
                                Restaurants
                            </a>
                            <a href="{{ route('admin.transactions') }}"
                               class="navbar-link {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                                Transactions
                            </a>
                            <a href="{{ route('admin.pages.index') }}"
                               class="navbar-link {{ request()->routeIs('admin.pages*') ? 'active' : '' }}">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Pages
                            </a>
                            <a href="{{ route('admin.settings') }}"
                               class="navbar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                                <i data-lucide="settings" class="w-4 h-4"></i>
                                Settings
                            </a>
                        </div>

                        <!-- Mobile Menu Button -->
                        <div class="md:hidden">
                            <button onclick="toggleMobileMenu()" class="navbar-link" id="mobile-menu-button">
                                <i data-lucide="menu" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="hidden md:flex items-center space-x-2">
                            <span class="text-white text-sm font-medium">{{ Auth::user()->name }}</span>
                            <span class="text-white text-xs opacity-75">Admin</span>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn btn-ghost text-white hover:bg-white/10">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div id="mobile-menu" class="md:hidden hidden bg-primary border-t border-white/20">
                <div class="px-4 py-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                       class="mobile-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i data-lucide="home" class="w-4 h-4"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.restaurants') }}"
                       class="mobile-nav-link {{ request()->routeIs('admin.restaurants*') ? 'active' : '' }}">
                        <i data-lucide="building" class="w-4 h-4"></i>
                        Restaurants
                    </a>
                    <a href="{{ route('admin.transactions') }}"
                       class="mobile-nav-link {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        Transactions
                    </a>
                    <a href="{{ route('admin.pages.index') }}"
                       class="mobile-nav-link {{ request()->routeIs('admin.pages*') ? 'active' : '' }}">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        Pages
                    </a>
                    <a href="{{ route('admin.settings') }}"
                       class="mobile-nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                        <i data-lucide="settings" class="w-4 h-4"></i>
                        Settings
                    </a>
                </div>
            </div>
        </nav>
    @endauth

    <!-- Main Content -->
    <main class="@auth py-8 @else py-0 @endauth">
        @yield('content')
    </main>

    <!-- Enhanced Flash Messages -->
    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up flex items-center gap-2">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up flex items-center gap-2">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();

        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuButton = document.getElementById('mobile-menu-button');
            const menuIcon = menuButton.querySelector('i');

            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
                menuIcon.setAttribute('data-lucide', 'x');
            } else {
                mobileMenu.classList.add('hidden');
                menuIcon.setAttribute('data-lucide', 'menu');
            }

            // Reinitialize icons
            lucide.createIcons();
        }

        // Auto-hide flash messages
        setTimeout(function() {
            const alerts = document.querySelectorAll('.fixed.top-4.right-4');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            });
        }, 4000);
    </script>
</body>
</html>
