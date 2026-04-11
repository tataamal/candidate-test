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
                theme: { extend: { fontFamily: { sans: ['Figtree', 'sans-serif'], serif: ['Merriweather', 'serif'] } } }
            }
        </script>
    @endif
    <style>
        body { background-color: #f8fafc; }
        .tab-active { border-bottom: 2px solid #367b59; color: #367b59; font-weight: 600; }
        .tab-inactive { color: #6b7280; font-weight: 500; }
        .tab-inactive:hover { color: #374151; border-bottom: 2px solid #e5e7eb; }
        .animate-fade-in { animation: fade-in 0.4s ease-out; }
        @keyframes fade-in { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .modal-backdrop { transition: opacity 0.2s ease; }
        .modal-box { transition: transform 0.2s ease, opacity 0.2s ease; }
        .toast-enter { animation: toast-in 0.3s ease-out; }
        @keyframes toast-in { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
        .skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 min-h-screen">

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
                        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Overview</a>
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

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1" style="font-family:'Merriweather',serif;">Suppliers</h1>
                <p class="text-sm text-gray-500">Manage timber suppliers and material sourcing.</p>
            </div>
            <!-- Add button: admin only -->
            <div class="mt-4 sm:mt-0" id="add-supplier-btn-wrapper" style="display:none;">
                <button onclick="openAddModal()" type="button"
                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#367b59] hover:bg-[#2c6448] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#367b59] transition">
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" id="search-input" oninput="handleSearch(this.value)"
                    class="block w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg bg-white placeholder-gray-400 text-sm focus:outline-none focus:ring-1 focus:ring-[#367b59] focus:border-[#367b59]"
                    placeholder="Search suppliers by name...">
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="showExportToast()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#367b59] transition">
                    <svg class="-ml-1 mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">Name</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">Created At</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" id="supplier-table-body">
                        <!-- Skeleton rows on initial load -->
                        <tr id="skeleton-row-1"><td colspan="3" class="px-6 py-5"><div class="h-4 skeleton rounded w-3/4"></div></td></tr>
                        <tr id="skeleton-row-2"><td colspan="3" class="px-6 py-5"><div class="h-4 skeleton rounded w-2/3"></div></td></tr>
                        <tr id="skeleton-row-3"><td colspan="3" class="px-6 py-5"><div class="h-4 skeleton rounded w-1/2"></div></td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty state -->
            <div id="empty-state" style="display:none;" class="py-16 text-center">
                <svg class="mx-auto h-10 w-10 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <p class="text-sm font-medium text-gray-500">No suppliers found</p>
                <p class="text-xs text-gray-400 mt-1" id="empty-state-hint">No suppliers have been added yet.</p>
            </div>

            <!-- Pagination Footer -->
            <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between" id="pagination-footer">
                <p class="text-sm text-gray-500" id="pagination-info">—</p>
                <nav class="relative z-0 inline-flex rounded shadow-sm -space-x-px border border-gray-200" aria-label="Pagination">
                    <button id="prev-btn" onclick="goToPage(currentPage - 1)"
                        class="relative inline-flex items-center px-2 py-2 rounded-l bg-white text-sm font-medium text-gray-400 hover:bg-gray-50 border-r border-gray-200 disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <span id="page-indicator" class="relative inline-flex items-center px-4 py-2 bg-white text-sm text-gray-700 border-r border-gray-200">1 / 1</span>
                    <button id="next-btn" onclick="goToPage(currentPage + 1)"
                        class="relative inline-flex items-center px-2 py-2 rounded-r bg-white text-sm font-medium text-gray-400 hover:text-gray-500 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>

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

    <!-- ========== SCRIPT ========== -->
    <script>
        // ─── State ────────────────────────────────────────────────────────────
        let currentUser   = null;
        let allSuppliers  = [];  // full page from API
        let filteredRows  = [];  // after client-side search
        let currentPage   = 1;
        let lastPage      = 1;
        let totalItems    = 0;
        let perPage       = 15;
        let searchQuery   = '';

        // Avatar palette (deterministic by name)
        const AVATAR_COLORS = [
            { bg:'#eff6ff', text:'#2563eb', border:'#bfdbfe' },
            { bg:'#ecfdf5', text:'#059669', border:'#a7f3d0' },
            { bg:'#fff7ed', text:'#d97706', border:'#fed7aa' },
            { bg:'#faf5ff', text:'#7c3aed', border:'#ddd6fe' },
            { bg:'#f0fdfa', text:'#0d9488', border:'#99f6e4' },
            { bg:'#fdf2f8', text:'#db2777', border:'#fbcfe8' },
        ];

        function avatarColor(name) {
            let h = 0;
            for (let c of (name || '')) h = (h * 31 + c.charCodeAt(0)) % AVATAR_COLORS.length;
            return AVATAR_COLORS[h];
        }

        function initials(name) {
            if (!name) return '??';
            const parts = name.trim().split(/\s+/);
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }

        function formatDate(str) {
            if (!str) return '—';
            const d = new Date(str);
            return d.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' });
        }

        // ─── API Helpers ──────────────────────────────────────────────────────
        async function apiFetch(url, options = {}) {
            const defaults = {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            };
            return fetch(url, { ...defaults, ...options, headers: { ...defaults.headers, ...(options.headers || {}) } });
        }

        // ─── Init ─────────────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', async () => {
            await loadUser();
            await loadSuppliers(1);
        });

        // ─── Load User ────────────────────────────────────────────────────────
        async function loadUser() {
            try {
                const res  = await apiFetch('/api/v1/me');
                const data = await res.json();

                if (!res.ok || !data.success) {
                    window.location.href = '/login';
                    return;
                }

                currentUser = data.user;

                document.getElementById('user-name-display').textContent = currentUser.name;
                document.getElementById('user-role-display').textContent  = currentUser.role;
                document.getElementById('user-initials').textContent      = initials(currentUser.name);

                // Show Add Supplier button only for admin
                if (currentUser.role === 'admin') {
                    document.getElementById('add-supplier-btn-wrapper').style.display = '';
                }

            } catch (err) {
                console.error('Failed to load user', err);
            }
        }

        // ─── Load Suppliers ───────────────────────────────────────────────────
        async function loadSuppliers(page = 1) {
            setTableLoading(true);
            try {
                const res  = await apiFetch(`/api/v1/suppliers?page=${page}`);
                const data = await res.json();

                if (!res.ok) {
                    if (res.status === 401) { window.location.href = '/login'; return; }
                    showToast(data.message || 'Failed to load suppliers.', 'error');
                    setTableEmpty('Failed to load data. Please refresh.');
                    return;
                }

                allSuppliers = data.data  || [];
                currentPage  = data.meta?.current_page ?? 1;
                lastPage     = data.meta?.last_page     ?? 1;
                totalItems   = data.meta?.total         ?? allSuppliers.length;
                perPage      = data.meta?.per_page      ?? 15;

                applySearch();

            } catch (err) {
                console.error(err);
                showToast('Network error. Please try again.', 'error');
                setTableEmpty('Network error. Please refresh.');
            } finally {
                setTableLoading(false);
            }
        }

        // ─── Search (client-side on current page) ────────────────────────────
        let searchTimer = null;
        function handleSearch(value) {
            searchQuery = value.trim().toLowerCase();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(applySearch, 200);
        }

        function applySearch() {
            filteredRows = searchQuery
                ? allSuppliers.filter(s => s.name.toLowerCase().includes(searchQuery))
                : [...allSuppliers];
            renderTable();
        }

        // ─── Render Table ─────────────────────────────────────────────────────
        function renderTable() {
            const tbody = document.getElementById('supplier-table-body');
            const empty = document.getElementById('empty-state');

            if (filteredRows.length === 0) {
                tbody.innerHTML = '';
                empty.style.display = '';
                document.getElementById('empty-state-hint').textContent =
                    searchQuery ? `No suppliers matching "${searchQuery}".` : 'No suppliers have been added yet.';
                updatePagination();
                return;
            }

            empty.style.display = 'none';
            const isAdmin = currentUser?.role === 'admin';

            tbody.innerHTML = filteredRows.map(s => {
                const color = avatarColor(s.name);
                const init  = initials(s.name);
                const date  = formatDate(s.created_at);
                const id    = String(s.id).padStart(4, '0');

                const adminActions = isAdmin ? `
                    <button onclick="openEditModal(${s.id})"
                        class="p-1.5 text-gray-400 hover:text-[#367b59] hover:bg-[#ecfdf5] rounded transition" title="Edit">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>
                    <button onclick="openDeleteModal(${s.id})"
                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition" title="Delete">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>` : '';

                return `
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center font-bold text-[13px] border"
                                style="background:${color.bg};color:${color.text};border-color:${color.border}">
                                ${init}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[14px] font-semibold text-gray-900">${escapeHtml(s.name)}</span>
                                <span class="text-xs text-gray-400">#${id}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${date}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="inline-flex items-center gap-1">
                            <a href="/suppliers/${s.id}"
                                class="p-1.5 text-gray-400 hover:text-[#367b59] hover:bg-[#ecfdf5] rounded transition" title="View detail">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            ${adminActions}
                        </div>
                    </td>
                </tr>`;
            }).join('');

            updatePagination();
        }

        // ─── Pagination ───────────────────────────────────────────────────────
        function updatePagination() {
            const startItem = filteredRows.length ? (currentPage - 1) * perPage + 1 : 0;
            const endItem   = Math.min(currentPage * perPage, totalItems);

            document.getElementById('pagination-info').textContent =
                totalItems > 0 ? `Showing ${startItem}–${endItem} of ${totalItems} suppliers` : 'No results';

            document.getElementById('page-indicator').textContent = `${currentPage} / ${lastPage}`;

            document.getElementById('prev-btn').disabled = currentPage <= 1;
            document.getElementById('next-btn').disabled = currentPage >= lastPage;
        }

        function goToPage(page) {
            if (page < 1 || page > lastPage) return;
            loadSuppliers(page);
        }

        // ─── Loading / Empty States ───────────────────────────────────────────
        function setTableLoading(loading) {
            if (loading) {
                document.getElementById('supplier-table-body').innerHTML = `
                    ${[1,2,3,4,5].map(() => `
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full skeleton"></div>
                                <div class="flex flex-col gap-2">
                                    <div class="h-3 skeleton rounded w-40"></div>
                                    <div class="h-2 skeleton rounded w-16"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><div class="h-3 skeleton rounded w-24"></div></td>
                        <td class="px-6 py-4 text-right"><div class="h-3 skeleton rounded w-16 ml-auto"></div></td>
                    </tr>`).join('')}`;
                document.getElementById('empty-state').style.display = 'none';
            }
        }

        function setTableEmpty(hint) {
            document.getElementById('supplier-table-body').innerHTML = '';
            const empty = document.getElementById('empty-state');
            empty.style.display = '';
            document.getElementById('empty-state-hint').textContent = hint;
        }

        // ─── Modal: Add ───────────────────────────────────────────────────────
        function openAddModal() {
            document.getElementById('add-supplier-form').reset();
            document.getElementById('add-error').classList.add('hidden');
            document.getElementById('modal-add').classList.remove('hidden');
            setTimeout(() => document.getElementById('add-name').focus(), 50);
        }

        function closeAddModal() {
            document.getElementById('modal-add').classList.add('hidden');
        }

        async function submitCreate(e) {
            e.preventDefault();
            const btn      = document.getElementById('add-submit-btn');
            const errorDiv = document.getElementById('add-error');
            const name     = document.getElementById('add-name').value.trim();
            const email    = document.getElementById('add-email').value.trim();
            const password = document.getElementById('add-password').value;

            errorDiv.classList.add('hidden');
            btn.disabled    = true;
            btn.textContent = 'Creating...';

            try {
                const body = { name, email };
                if (password) body.password = password;

                const res  = await apiFetch('/api/v1/suppliers', { method: 'POST', body: JSON.stringify(body) });
                const data = await res.json();

                if (res.ok && data.success) {
                    closeAddModal();
                    showToast(`Supplier "${name}" created successfully.`, 'success');
                    await loadSuppliers(currentPage);
                } else {
                    const msg = data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : (data.message || 'Failed to create supplier.');
                    errorDiv.textContent = msg;
                    errorDiv.classList.remove('hidden');
                }
            } catch (err) {
                errorDiv.textContent = 'Network error. Please try again.';
                errorDiv.classList.remove('hidden');
            } finally {
                btn.disabled    = false;
                btn.textContent = 'Create Supplier';
            }
        }

        // ─── Modal: Edit ──────────────────────────────────────────────────────
        function openEditModal(id) {
            const supplier = allSuppliers.find(s => s.id === id);
            if (!supplier) return;
            document.getElementById('edit-id').value    = supplier.id;
            document.getElementById('edit-name').value  = supplier.name;
            document.getElementById('edit-error').classList.add('hidden');
            document.getElementById('modal-edit').classList.remove('hidden');
            setTimeout(() => document.getElementById('edit-name').focus(), 50);
        }

        function closeEditModal() {
            document.getElementById('modal-edit').classList.add('hidden');
        }

        async function submitUpdate(e) {
            e.preventDefault();
            const btn      = document.getElementById('edit-submit-btn');
            const errorDiv = document.getElementById('edit-error');
            const id       = document.getElementById('edit-id').value;
            const name     = document.getElementById('edit-name').value.trim();

            errorDiv.classList.add('hidden');
            btn.disabled    = true;
            btn.textContent = 'Saving...';

            try {
                const res  = await apiFetch(`/api/v1/suppliers/${id}`, { method: 'PUT', body: JSON.stringify({ name }) });
                const data = await res.json();

                if (res.ok && data.success) {
                    closeEditModal();
                    showToast(`Supplier updated successfully.`, 'success');
                    await loadSuppliers(currentPage);
                } else {
                    const msg = data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : (data.message || 'Failed to update supplier.');
                    errorDiv.textContent = msg;
                    errorDiv.classList.remove('hidden');
                }
            } catch (err) {
                errorDiv.textContent = 'Network error. Please try again.';
                errorDiv.classList.remove('hidden');
            } finally {
                btn.disabled    = false;
                btn.textContent = 'Save Changes';
            }
        }

        // ─── Modal: Delete ────────────────────────────────────────────────────
        function openDeleteModal(id) {
            const supplier = allSuppliers.find(s => s.id === id);
            if (!supplier) return;
            document.getElementById('delete-id').value         = supplier.id;
            document.getElementById('delete-name').textContent = supplier.name;
            document.getElementById('modal-delete').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('modal-delete').classList.add('hidden');
        }

        async function submitDelete() {
            const btn = document.getElementById('delete-confirm-btn');
            const id  = document.getElementById('delete-id').value;

            btn.disabled    = true;
            btn.textContent = 'Deleting...';

            try {
                const res  = await apiFetch(`/api/v1/suppliers/${id}`, { method: 'DELETE' });
                const data = await res.json();

                if (res.ok && data.success) {
                    closeDeleteModal();
                    showToast('Supplier deleted successfully.', 'success');
                    const newPage = (allSuppliers.length === 1 && currentPage > 1) ? currentPage - 1 : currentPage;
                    await loadSuppliers(newPage);
                } else {
                    closeDeleteModal();
                    showToast(data.message || 'Failed to delete supplier.', 'error');
                }
            } catch (err) {
                closeDeleteModal();
                showToast('Network error. Please try again.', 'error');
            } finally {
                btn.disabled    = false;
                btn.textContent = 'Delete';
            }
        }

        // ─── Logout ───────────────────────────────────────────────────────────
        async function handleLogout() {
            try {
                await apiFetch('/api/v1/logout', { method: 'POST' });
            } catch (err) {
                console.error(err);
            } finally {
                window.location.href = '/login';
            }
        }

        // ─── Export Toast ─────────────────────────────────────────────────────
        function showExportToast() {
            showToast('Fitur ini masih dalam pengembangan 🚧', 'info');
        }

        // ─── Toast ────────────────────────────────────────────────────────────
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const colors = {
                success: 'bg-white border-l-4 border-[#367b59] text-gray-800',
                error:   'bg-white border-l-4 border-red-500 text-gray-800',
                info:    'bg-white border-l-4 border-blue-400 text-gray-800',
            };
            const icons = {
                success: '<svg class="h-4 w-4 text-[#367b59]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
                error:   '<svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
                info:    '<svg class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            };

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-md text-sm max-w-sm ${colors[type]} toast-enter`;
            toast.innerHTML = `${icons[type]}<span class="flex-1">${escapeHtml(message)}</span>`;
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'opacity 0.3s, transform 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // ─── Escape Helpers ───────────────────────────────────────────────────
        function escapeHtml(str) {
            const map = { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' };
            return String(str).replace(/[&<>"']/g, m => map[m]);
        }

        function escapeAttr(str) {
            return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }

        // ─── Keyboard: close modals on Escape ────────────────────────────────
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>
