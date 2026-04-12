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
                    <div class="hidden sm:-my-px sm:ml-4 sm:flex sm:space-x-8">
                        <a href="#" class="tab-active inline-flex items-center px-1 pt-1 text-sm transition">Suppliers</a>
                        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Layups</a>
                        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Layers</a>
                    </div>
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

    <!-- ========== MODAL: ADD SUPPLIER ========== -->
    <div id="modal-add" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeAddModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-box bg-white rounded-xl shadow-xl w-full max-w-md relative">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900" style="font-family:'Merriweather',serif;">Add Supplier</h3>
                    <p class="text-xs text-gray-500 mt-0.5">A user account will be created with the supplier role.</p>
                </div>
                <form id="add-supplier-form" onsubmit="submitCreate(event)" class="px-6 py-5 space-y-4">
                    <div id="add-error" class="hidden bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-r-md text-sm"></div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name <span class="text-red-500">*</span></label>
                        <input type="text" id="add-name" required maxlength="255"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition"
                            placeholder="e.g. PT Graha Kalasta">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="add-email" required
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition"
                            placeholder="supplier@example.com">
                        <p class="text-xs text-gray-400 mt-1">If email already exists, a new supplier entry will be linked to that account.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input type="password" id="add-password" minlength="8"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition"
                            placeholder="Min. 8 characters">
                        <p class="text-xs text-gray-400 mt-1">Leave blank to auto-generate. If the user already exists, their password won't change.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeAddModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit" id="add-submit-btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#367b59] rounded-lg hover:bg-[#2c6448] transition disabled:opacity-60">
                            Create Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== MODAL: EDIT SUPPLIER ========== -->
    <div id="modal-edit" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeEditModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-box bg-white rounded-xl shadow-xl w-full max-w-md relative">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900" style="font-family:'Merriweather',serif;">Edit Supplier</h3>
                </div>
                <form id="edit-supplier-form" onsubmit="submitUpdate(event)" class="px-6 py-5 space-y-4">
                    <input type="hidden" id="edit-id">
                    <div id="edit-error" class="hidden bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-r-md text-sm"></div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name <span class="text-red-500">*</span></label>
                        <input type="text" id="edit-name" required maxlength="255"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition">
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit" id="edit-submit-btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#367b59] rounded-lg hover:bg-[#2c6448] transition disabled:opacity-60">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== MODAL: DELETE CONFIRM ========== -->
    <div id="modal-delete" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeDeleteModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-box bg-white rounded-xl shadow-xl w-full max-w-sm relative p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Delete Supplier</h3>
                        <p class="text-sm text-gray-500 mt-1">Are you sure you want to delete <span id="delete-name" class="font-semibold text-gray-700"></span>? This will also remove all associated layups and layers. This action cannot be undone.</p>
                    </div>
                </div>
                <input type="hidden" id="delete-id">
                <div class="flex justify-end gap-3 mt-5">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="button" onclick="submitDelete()" id="delete-confirm-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition disabled:opacity-60">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== TOAST ========== -->
    <div id="toast-container" class="fixed top-4 right-4 z-[60] flex flex-col gap-2 pointer-events-none"></div>

    @stack('scripts')
</body>
</html>
