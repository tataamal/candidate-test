<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supplier Detail - CLT Manager</title>
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
        .skeleton { background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
        .toast-enter { animation: toast-in 0.3s ease-out; }
        @keyframes toast-in { from { opacity: 0; transform: translateX(100%); } to { opacity: 1; transform: translateX(0); } }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 min-h-screen">

    <!-- ========== NAVBAR ========== -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-10 w-full shadow-sm">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
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
                            <span class="text-[10px] uppercase font-semibold text-gray-500 leading-none mt-1">Manager</span>
                        </div>
                    </div>
                    <div class="hidden sm:-my-px sm:ml-4 sm:flex sm:space-x-8">
                        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Overview</a>
                        <a href="/dashboard" class="tab-active inline-flex items-center px-1 pt-1 text-sm transition">Suppliers</a>
                        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Layups</a>
                        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Layers</a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-6">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-[#e8f5ee] flex items-center justify-center text-[#367b59] font-bold text-sm" id="user-initials">--</div>
                        <div class="hidden lg:flex flex-col text-left">
                            <span class="text-xs font-bold text-gray-900" id="user-name-display">Loading...</span>
                            <span class="text-[10px] text-gray-500 capitalize" id="user-role-display">—</span>
                        </div>
                    </div>
                    <button onclick="handleLogout()" class="text-xs font-semibold text-gray-400 hover:text-red-500 transition border-l pl-4 border-gray-200">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- ========== MAIN CONTENT ========== -->
    <main class="max-w-screen-2xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

        <!-- Back link -->
        <div class="mb-6 animate-fade-in">
            <a href="/dashboard" class="inline-flex items-center text-sm text-gray-500 hover:text-[#367b59] transition gap-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Suppliers
            </a>
        </div>

        <!-- Supplier Header Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6 animate-fade-in" id="supplier-header">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-full flex items-center justify-center font-bold text-lg border flex-shrink-0"
                    id="supplier-avatar" style="background:#ecfdf5;color:#059669;border-color:#a7f3d0">
                    --
                </div>
                <div class="flex-1 min-w-0">
                    <div class="h-5 skeleton rounded w-48 mb-2" id="supplier-name-skeleton"></div>
                    <div class="h-3 skeleton rounded w-24" id="supplier-date-skeleton"></div>
                    <h1 class="text-xl font-bold text-gray-900 hidden" id="supplier-name" style="font-family:'Merriweather',serif;"></h1>
                    <p class="text-sm text-gray-500 hidden" id="supplier-meta"></p>
                </div>
                <div id="supplier-edit-btn" class="hidden">
                    <button onclick="openEditModal()"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition gap-1.5">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Edit
                    </button>
                </div>
            </div>
        </div>

        <!-- Layups Section -->
        <div class="animate-fade-in">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900" style="font-family:'Merriweather',serif;">Layups</h2>
                <button id="add-layup-btn" onclick="showExportToast()" style="display:none;"
                    class="inline-flex items-center px-3 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-[#367b59] hover:bg-[#2c6448] transition gap-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Layup
                </button>
            </div>

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
                        <tbody class="bg-white divide-y divide-gray-100" id="layup-table-body">
                            <tr><td colspan="3" class="px-6 py-5"><div class="h-4 skeleton rounded w-3/4"></div></td></tr>
                            <tr><td colspan="3" class="px-6 py-5"><div class="h-4 skeleton rounded w-2/3"></div></td></tr>
                            <tr><td colspan="3" class="px-6 py-5"><div class="h-4 skeleton rounded w-1/2"></div></td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="layup-empty" style="display:none;" class="py-12 text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
                    </svg>
                    <p class="text-sm text-gray-500">No layups found for this supplier.</p>
                </div>
                <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-sm text-gray-500" id="layup-pagination-info">—</p>
                    <nav class="relative z-0 inline-flex rounded shadow-sm -space-x-px border border-gray-200">
                        <button id="layup-prev-btn" onclick="goToLayupPage(layupCurrentPage - 1)"
                            class="relative inline-flex items-center px-2 py-2 rounded-l bg-white text-sm text-gray-400 hover:bg-gray-50 border-r border-gray-200 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                        <span id="layup-page-indicator" class="relative inline-flex items-center px-4 py-2 bg-white text-sm text-gray-700 border-r border-gray-200">1 / 1</span>
                        <button id="layup-next-btn" onclick="goToLayupPage(layupCurrentPage + 1)"
                            class="relative inline-flex items-center px-2 py-2 rounded-r bg-white text-sm text-gray-400 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
        </div>

    </main>

    <!-- ========== MODAL: EDIT SUPPLIER ========== -->
    <div id="modal-edit" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeEditModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md relative">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900" style="font-family:'Merriweather',serif;">Edit Supplier</h3>
                </div>
                <form onsubmit="submitUpdate(event)" class="px-6 py-5 space-y-4">
                    <div id="edit-error" class="hidden bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-r-md text-sm"></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name <span class="text-red-500">*</span></label>
                        <input type="text" id="edit-name" required maxlength="255"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition">
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" id="edit-submit-btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#367b59] rounded-lg hover:bg-[#2c6448] transition disabled:opacity-60">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== TOAST ========== -->
    <div id="toast-container" class="fixed top-4 right-4 z-[60] flex flex-col gap-2 pointer-events-none"></div>

    <script>
        const SUPPLIER_ID = {{ $supplierId }};
        let currentUser  = null;
        let supplier     = null;
        let layupCurrentPage = 1;
        let layupLastPage    = 1;
        let layupTotal       = 0;
        let layupPerPage     = 15;

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
            return new Date(str).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' });
        }

        async function apiFetch(url, options = {}) {
            const defaults = { headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } };
            return fetch(url, { ...defaults, ...options, headers: { ...defaults.headers, ...(options.headers || {}) } });
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await loadUser();
            await Promise.all([loadSupplier(), loadLayups(1)]);
        });

        async function loadUser() {
            try {
                const res  = await apiFetch('/api/v1/me');
                const data = await res.json();
                if (!res.ok || !data.success) { window.location.href = '/login'; return; }
                currentUser = data.user;
                document.getElementById('user-name-display').textContent = currentUser.name;
                document.getElementById('user-role-display').textContent  = currentUser.role;
                document.getElementById('user-initials').textContent      = initials(currentUser.name);
            } catch (err) { console.error(err); }
        }

        async function loadSupplier() {
            try {
                const res  = await apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}`);
                const data = await res.json();
                if (!res.ok) {
                    if (res.status === 401) { window.location.href = '/login'; return; }
                    if (res.status === 404 || res.status === 403) { window.location.href = '/dashboard'; return; }
                    return;
                }
                supplier = data.data;
                renderSupplierHeader();
            } catch (err) { console.error(err); }
        }

        function renderSupplierHeader() {
            if (!supplier) return;
            const color = avatarColor(supplier.name);
            const avatar = document.getElementById('supplier-avatar');
            avatar.textContent = initials(supplier.name);
            avatar.style.background   = color.bg;
            avatar.style.color        = color.text;
            avatar.style.borderColor  = color.border;

            document.getElementById('supplier-name-skeleton').style.display = 'none';
            document.getElementById('supplier-date-skeleton').style.display = 'none';

            const nameEl = document.getElementById('supplier-name');
            nameEl.textContent = supplier.name;
            nameEl.classList.remove('hidden');

            const metaEl = document.getElementById('supplier-meta');
            metaEl.textContent = `ID #${String(supplier.id).padStart(4,'0')} · Added ${formatDate(supplier.created_at)}`;
            metaEl.classList.remove('hidden');

            // Show edit button for admin
            if (currentUser?.role === 'admin') {
                document.getElementById('supplier-edit-btn').classList.remove('hidden');
                document.getElementById('add-layup-btn').style.display = '';
            }
            // Supplier role can also add/edit their own layups
            if (currentUser?.role === 'supplier') {
                document.getElementById('add-layup-btn').style.display = '';
            }
        }

        async function loadLayups(page = 1) {
            document.getElementById('layup-table-body').innerHTML = `
                ${[1,2,3].map(() => `<tr><td colspan="3" class="px-6 py-5"><div class="h-4 skeleton rounded w-2/3"></div></td></tr>`).join('')}`;
            document.getElementById('layup-empty').style.display = 'none';

            try {
                const res  = await apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}/layups?page=${page}`);
                const data = await res.json();
                if (!res.ok) { renderLayupEmpty(); return; }

                const layups = data.data || [];
                layupCurrentPage = data.meta?.current_page ?? 1;
                layupLastPage    = data.meta?.last_page     ?? 1;
                layupTotal       = data.meta?.total         ?? layups.length;
                layupPerPage     = data.meta?.per_page      ?? 15;

                renderLayups(layups);
            } catch (err) {
                console.error(err);
                renderLayupEmpty();
            }
        }

        function renderLayups(layups) {
            const tbody = document.getElementById('layup-table-body');
            const empty = document.getElementById('layup-empty');
            const isAdmin    = currentUser?.role === 'admin';
            const isSupplier = currentUser?.role === 'supplier';

            if (layups.length === 0) { renderLayupEmpty(); return; }

            empty.style.display = 'none';
            tbody.innerHTML = layups.map(l => {
                const canEdit = isAdmin || isSupplier;
                const actions = canEdit ? `
                    <a href="/suppliers/${SUPPLIER_ID}/layups/${l.id}"
                        class="p-1.5 text-gray-400 hover:text-[#367b59] hover:bg-[#ecfdf5] rounded transition" title="View layers">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>` : `
                    <a href="/suppliers/${SUPPLIER_ID}/layups/${l.id}"
                        class="p-1.5 text-gray-400 hover:text-[#367b59] hover:bg-[#ecfdf5] rounded transition" title="View layers">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>`;
                return `
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-medium text-gray-900">${escapeHtml(l.name)}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${formatDate(l.created_at)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="inline-flex items-center gap-1">${actions}</div>
                    </td>
                </tr>`;
            }).join('');

            updateLayupPagination();
        }

        function renderLayupEmpty() {
            document.getElementById('layup-table-body').innerHTML = '';
            document.getElementById('layup-empty').style.display = '';
            updateLayupPagination();
        }

        function updateLayupPagination() {
            const startItem = layupTotal > 0 ? (layupCurrentPage - 1) * layupPerPage + 1 : 0;
            const endItem   = Math.min(layupCurrentPage * layupPerPage, layupTotal);
            document.getElementById('layup-pagination-info').textContent =
                layupTotal > 0 ? `Showing ${startItem}–${endItem} of ${layupTotal} layups` : 'No layups';
            document.getElementById('layup-page-indicator').textContent = `${layupCurrentPage} / ${layupLastPage}`;
            document.getElementById('layup-prev-btn').disabled = layupCurrentPage <= 1;
            document.getElementById('layup-next-btn').disabled = layupCurrentPage >= layupLastPage;
        }

        function goToLayupPage(page) {
            if (page < 1 || page > layupLastPage) return;
            loadLayups(page);
        }

        // ─── Edit Supplier ────────────────────────────────────────────────────
        function openEditModal() {
            if (!supplier) return;
            document.getElementById('edit-name').value = supplier.name;
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
            const name     = document.getElementById('edit-name').value.trim();

            errorDiv.classList.add('hidden');
            btn.disabled    = true;
            btn.textContent = 'Saving...';

            try {
                const res  = await apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}`, { method: 'PUT', body: JSON.stringify({ name }) });
                const data = await res.json();
                if (res.ok && data.success) {
                    supplier = data.data;
                    closeEditModal();
                    renderSupplierHeader();
                    showToast('Supplier updated successfully.', 'success');
                } else {
                    const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Failed to update.');
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

        // ─── Helpers ──────────────────────────────────────────────────────────
        async function handleLogout() {
            try { await apiFetch('/api/v1/logout', { method: 'POST' }); } catch (e) {}
            window.location.href = '/login';
        }

        function showExportToast() {
            showToast('Fitur ini masih dalam pengembangan 🚧', 'info');
        }

        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const colors = { success:'bg-white border-l-4 border-[#367b59]', error:'bg-white border-l-4 border-red-500', info:'bg-white border-l-4 border-blue-400' };
            const icons  = {
                success: '<svg class="h-4 w-4 text-[#367b59] flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
                error:   '<svg class="h-4 w-4 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
                info:    '<svg class="h-4 w-4 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            };
            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg shadow-md text-sm text-gray-800 max-w-sm ${colors[type]} toast-enter`;
            toast.innerHTML = `${icons[type]}<span class="flex-1">${escapeHtml(message)}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0'; toast.style.transform = 'translateX(100%)'; toast.style.transition = 'opacity 0.3s, transform 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        function escapeHtml(str) {
            const map = { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' };
            return String(str).replace(/[&<>"']/g, m => map[m]);
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditModal(); });
    </script>
</body>
</html>
