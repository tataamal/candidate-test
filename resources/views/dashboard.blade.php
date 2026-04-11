<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Suppliers - CLT Manager</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700;900&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: { extend: { fontFamily: { sans: ['Figtree', 'sans-serif'], serif: ['Merriweather', 'serif'] }, }, }
            }
        </script>
    @endif
    <style>
        body { background-color: #f8fafc; }
        .tab-active { border-bottom: 2px solid #367b59; color: #367b59; font-weight: 600; }
        .tab-inactive { color: #6b7280; font-weight: 500; }
        .tab-inactive:hover { color: #374151; border-bottom: 2px solid #e5e7eb; }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-10 w-full shadow-sm">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo & Nav Links -->
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center gap-3 pr-6">
                        <div class="w-8 h-8 rounded-md flex items-center justify-center text-[#367b59]">
                            <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13h-13L12 6.5z"/>
                                <path d="M12 10.5L9 17h6l-3-6.5z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[13px] font-bold tracking-wide leading-none text-gray-900 uppercase">CLT Layup</span>
                            <span class="text-[10px] uppercase font-semibold text-gray-500 leading-none mt-1 text-left">Manager</span>
                        </div>
                    </div>
                    <div class="hidden sm:-my-px sm:ml-4 sm:flex sm:space-x-8">
                        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Overview</a>
                        <a href="#" class="tab-active inline-flex items-center px-1 pt-1 text-sm transition">Suppliers</a>
                        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Layups</a>
                        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Layers</a>
                        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Settings</a>
                    </div>
                </div>

                <!-- Profile Group -->
                <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-6">
                    <!-- Notification -->
                    <button type="button" class="bg-white rounded-full text-gray-400 hover:text-gray-600 transition focus:outline-none relative">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute -top-0.5 -right-0.5 block w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>

                    <!-- Avatar Profile -->
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-sm" id="user-initials">
                            AM
                        </div>
                        <div class="flex flex-col text-left hidden lg:block">
                            <span class="text-xs font-bold text-gray-900" id="user-name-display">Alex Morgan</span>
                            <span class="text-[10px] text-gray-500" id="user-role-display">Engineering Lead</span>
                        </div>
                    </div>
                    
                    <button onclick="logout()" class="ml-2 text-xs font-semibold text-gray-400 hover:text-red-500 transition border-l pl-4 border-gray-200">
                        Logout
                    </button>
                    
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-screen-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold font-serif text-gray-900 mb-2" style="font-family: 'Merriweather', serif;">Suppliers</h1>
                <p class="text-sm text-gray-500">Manage timber suppliers and material sourcing.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button type="button" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#367b59] hover:bg-[#2c6448] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#367b59] transition">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Supplier
                </button>
            </div>
        </div>

        <!-- Controls -->
        <div class="flex flex-col sm:flex-row sm:justify-between gap-4 mb-4">
            <div class="relative max-w-sm w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-400 text-sm focus:outline-none focus:ring-1 focus:ring-[#367b59] focus:border-[#367b59]" placeholder="Search suppliers by name...">
            </div>
            
            <div class="flex items-center gap-3">
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#367b59] transition">
                    <svg class="-ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#367b59] transition">
                    <svg class="-ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">
                                NAME
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">
                                TOTAL LAYUPS
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">
                                CREATED AT
                            </th>
                            <th scope="col" class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">
                                ACTIONS
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" id="supplier-table-body">
                        <!-- Standard Mock Rows Matching Image -->
                        <tr class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-11 w-11 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 font-bold text-[13px] border border-blue-100">
                                        NT
                                    </div>
                                    <div class="ml-4 flex flex-col">
                                        <span class="text-[14px] font-bold text-gray-900 font-serif" style="font-family: 'Merriweather', serif;">Nordic Timber Co.</span>
                                        <span class="text-xs text-gray-400 mt-0.5">ID: SUP-2023-001</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-[14px] text-gray-600">
                                24
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-[14px] text-gray-600">
                                Oct 24, 2023
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-gray-400 hover:text-[#367b59] transition">
                                    <span class="sr-only">Options</span>
                                    <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        
                        <tr class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-11 w-11 flex items-center justify-center rounded-full bg-emerald-50 text-emerald-600 font-bold text-[13px] border border-emerald-100">
                                        AC
                                    </div>
                                    <div class="ml-4 flex flex-col">
                                        <span class="text-[14px] font-bold text-gray-900 font-serif" style="font-family: 'Merriweather', serif;">Alpine CLT Solutions</span>
                                        <span class="text-xs text-gray-400 mt-0.5">ID: SUP-2023-042</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-[14px] text-gray-600">12</td>
                            <td class="px-6 py-5 whitespace-nowrap text-[14px] text-gray-600">Nov 02, 2023</td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-gray-400 hover:text-[#367b59] transition">
                                    <svg class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                            </td>
                        </tr>
                        
                        <tr class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-11 w-11 flex items-center justify-center rounded-full bg-orange-50 text-orange-600 font-bold text-[13px] border border-orange-100">
                                        MW
                                    </div>
                                    <div class="ml-4 flex flex-col">
                                        <span class="text-[14px] font-bold text-gray-900 font-serif" style="font-family: 'Merriweather', serif;">MassivWood Ltd.</span>
                                        <span class="text-xs text-gray-400 mt-0.5">ID: SUP-2024-003</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-[14px] text-gray-600">156</td>
                            <td class="px-6 py-5 whitespace-nowrap text-[14px] text-gray-600">Jan 15, 2024</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-gray-400 hover:text-[#367b59] transition"><svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                            </td>
                        </tr>
                        
                        <tr class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-11 w-11 flex items-center justify-center rounded-full bg-purple-50 text-purple-600 font-bold text-[13px] border border-purple-100">
                                        TS
                                    </div>
                                    <div class="ml-4 flex flex-col">
                                        <span class="text-[14px] font-bold text-gray-900 font-serif" style="font-family: 'Merriweather', serif;">TimberStruct Inc.</span>
                                        <span class="text-xs text-gray-400 mt-0.5">ID: SUP-2024-008</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-[14px] text-gray-600">89</td>
                            <td class="px-6 py-5 whitespace-nowrap text-[14px] text-gray-600">Feb 10, 2024</td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-gray-400 hover:text-[#367b59] transition"><svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                            </td>
                        </tr>

                        <tr class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-11 w-11 flex items-center justify-center rounded-full bg-teal-50 text-teal-600 font-bold text-[13px] border border-teal-100">
                                        EL
                                    </div>
                                    <div class="ml-4 flex flex-col">
                                        <span class="text-[14px] font-bold text-gray-900 font-serif" style="font-family: 'Merriweather', serif;">EuroLam Systems</span>
                                        <span class="text-xs text-gray-400 mt-0.5">ID: SUP-2024-015</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-[14px] text-gray-600">45</td>
                            <td class="px-6 py-5 whitespace-nowrap text-[14px] text-gray-600">Feb 28, 2024</td>
                            <td class="px-6 py-5 whitespace-nowrap text-right text-sm font-medium">
                                <button class="text-gray-400 hover:text-[#367b59] transition"><svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Footer -->
            <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-500">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">42</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded shadow-sm -space-x-px border border-gray-200" aria-label="Pagination">
                            <button class="relative inline-flex items-center px-2 py-2 rounded-l bg-white text-sm font-medium text-gray-400 hover:bg-gray-50 border-r border-gray-200 disabled:opacity-50" disabled>
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button class="relative inline-flex items-center px-2 py-2 rounded-r bg-white text-sm font-medium text-gray-400 hover:text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        // Check authentication and populate user data if authenticated via API
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const response = await fetch('/api/v1/me', {
                    headers: { 'Accept': 'application/json' }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.user) {
                        const name = data.user.name;
                        const role = data.user.role;
                        
                        document.getElementById('user-name-display').textContent = name;
                        
                        const formattedRole = role.charAt(0).toUpperCase() + role.slice(1);
                        document.getElementById('user-role-display').textContent = formattedRole;
                        
                        const names = name.split(' ');
                        let initials = names[0].charAt(0).toUpperCase();
                        if (names.length > 1) {
                            initials += names[names.length - 1].charAt(0).toUpperCase();
                        }
                        document.getElementById('user-initials').textContent = initials;
                    }
                }
            } catch (error) {
                console.log('Using default mock user profile.', error);
            }
        });

        async function logout() {
            try {
                await fetch('/api/v1/logout', { 
                    method: 'POST', 
                    headers: { 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    } 
                });
            } catch (err) {
                console.error(err);
            } finally {
                window.location.href = '/login';
            }
        }
    </script>
</body>
</html>
