<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }} - {{ config('app.name', 'Waitinglist') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Inter font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Enhanced Custom Styles -->
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
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --color-primary: #ff6b00;
            --color-secondary: #333333;
            --color-background: #ffffff;
            --color-foreground: #111827;
            --color-muted: #888888;
            --color-border: #e5e7eb;
            --container-max: 1280px;
            --radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        body {
            font-family: "Inter", "Helvetica Neue", sans-serif;
            background: var(--color-background);
            color: var(--color-foreground);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Typography */
        h1 { 
            font-size: 2.25rem; 
            font-weight: 700; 
            color: var(--color-primary); 
            letter-spacing: -0.025em;
            line-height: 1.2;
            margin-bottom: 1rem;
        }
        
        h2 { 
            font-size: 1.75rem; 
            font-weight: 600; 
            color: var(--color-secondary);
            letter-spacing: -0.025em;
            line-height: 1.3;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        
        h3 { 
            font-size: 1.25rem; 
            font-weight: 500; 
            color: #444444;
            line-height: 1.4;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        
        p { 
            font-size: 1rem; 
            font-weight: 400; 
            color: #555555;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        /* Content Styles */
        .content a {
            color: var(--color-primary);
            text-decoration: underline;
        }
        
        .content a:hover {
            color: #e66000;
        }
        
        .content ul, .content ol {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }
        
        .content li {
            margin-bottom: 0.5rem;
        }
        
        .content blockquote {
            border-left: 4px solid var(--color-primary);
            padding-left: 1rem;
            margin: 1.5rem 0;
            color: #666666;
        }
        
        .content img {
            max-width: 100%;
            height: auto;
            border-radius: var(--radius);
            margin: 1rem 0;
        }
        
        .content pre {
            background: #f5f5f5;
            padding: 1rem;
            border-radius: var(--radius);
            overflow-x: auto;
            margin: 1.5rem 0;
        }
        
        .content code {
            background: #f5f5f5;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="text-xl font-bold text-primary">
                            {{ config('app.name', 'Waitinglist') }}
                        </a>
                    </div>
                    <div class="flex items-center">
                        <a href="/" class="text-gray-600 hover:text-primary px-3 py-2 text-sm font-medium">
                            Home
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6 sm:p-8">
                        <h1>{{ $page->title }}</h1>
                        <div class="content mt-6">
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <p class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Waitinglist') }}. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>