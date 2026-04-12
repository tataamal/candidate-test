<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CLT Toolbox') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth-layout.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased text-gray-900 min-h-screen" style="background-color:#f8fafc;">

    <!-- ========== NAVBAR ========== -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-10 w-full shadow-sm">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center gap-3 pr-6">
                        <div class="w-8 h-8 rounded-md flex items-center justify-center text-[#367b59]">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13h-13L12 6.5z"/>
                                <path d="M12 10.5L9 17h6l-3-6.5z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[13px] font-bold tracking-wide leading-none text-gray-900 uppercase">CLT Layup</span>
                            <span class="text-[10px] uppercase font-semibold text-gray-500 leading-none mt-1">Manager</span>
                        </div>
                    </div>
                    <!-- Nav Links -->
                    @stack('nav-links')
                </div>

                <!-- User Profile + Logout -->
                <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-6">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-[#e8f5ee] flex items-center justify-center text-[#367b59] font-bold text-sm" id="user-initials">
                            --
                        </div>
                        <div class="hidden lg:flex flex-col text-left">
                            <span class="text-xs font-bold text-gray-900" id="user-name-display">Loading...</span>
                            <span class="text-[10px] text-gray-500 capitalize" id="user-role-display">—</span>
                        </div>
                    </div>
                    <button onclick="handleLogout()" class="text-xs font-semibold text-gray-400 hover:text-red-500 transition border-l pl-4 border-gray-200">
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="max-w-screen-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <!-- ========== PAGE-SPECIFIC MODALS ========== -->
    @stack('modals')

    <!-- ========== TOAST ========== -->
    <div id="toast-container" class="fixed top-4 right-4 z-[60] flex flex-col gap-2 pointer-events-none"></div>

    @stack('scripts')
</body>
</html>
