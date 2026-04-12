@extends('layouts.auth')

@push('nav-links')
    <div class="hidden sm:-my-px sm:ml-4 sm:flex sm:space-x-8">
        <a href="/dashboard" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Suppliers</a>
        <a href="#" class="tab-active inline-flex items-center px-1 pt-1 text-sm transition">Layups</a>
        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Layers</a>
    </div>
@endpush

@section('content')

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
                id="supplier-avatar" style="background:#ecfdf5;color:#059669;border-color:#a7f3d0">--</div>
            <div class="flex-1 min-w-0">
                <div class="h-5 skeleton rounded w-48 mb-2" id="supplier-name-skeleton"></div>
                <div class="h-3 skeleton rounded w-24" id="supplier-date-skeleton"></div>
                <h1 class="text-xl font-bold text-gray-900 hidden" id="supplier-name" style="font-family:'Merriweather',serif;"></h1>
                <p class="text-sm text-gray-500 hidden" id="supplier-meta"></p>
            </div>
            <!-- Edit button: admin only -->
            <div id="supplier-edit-btn" class="hidden">
                <button onclick="openEditSupplierModal()"
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
            <!-- Add Layup: admin + supplier -->
            <button id="add-layup-btn" onclick="openAddLayupModal()" style="display:none;"
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
                    <button id="layup-prev-btn" onclick="goToLayupPage(layupPage - 1)"
                        class="relative inline-flex items-center px-2 py-2 rounded-l bg-white text-sm text-gray-400 hover:bg-gray-50 border-r border-gray-200 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <span id="layup-page-indicator" class="relative inline-flex items-center px-4 py-2 bg-white text-sm text-gray-700 border-r border-gray-200">1 / 1</span>
                    <button id="layup-next-btn" onclick="goToLayupPage(layupPage + 1)"
                        class="relative inline-flex items-center px-2 py-2 rounded-r bg-white text-sm text-gray-400 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </div>

@endsection

@push('modals')
    <!-- ========== MODAL: EDIT SUPPLIER ========== -->
    <div id="modal-edit-supplier" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeEditSupplierModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-box bg-white rounded-xl shadow-xl w-full max-w-md relative">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900" style="font-family:'Merriweather',serif;">Edit Supplier</h3>
                </div>
                <form onsubmit="submitEditSupplier(event)" class="px-6 py-5 space-y-4">
                    <div id="edit-supplier-error" class="hidden bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-r-md text-sm"></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier Name <span class="text-red-500">*</span></label>
                        <input type="text" id="edit-supplier-name" required maxlength="255"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition">
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeEditSupplierModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" id="edit-supplier-submit-btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#367b59] rounded-lg hover:bg-[#2c6448] transition disabled:opacity-60">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== MODAL: ADD LAYUP ========== -->
    <div id="modal-add-layup" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeAddLayupModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-box bg-white rounded-xl shadow-xl w-full max-w-md relative">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900" style="font-family:'Merriweather',serif;">Add Layup</h3>
                </div>
                <form id="add-layup-form" onsubmit="submitAddLayup(event)" class="px-6 py-5 space-y-4">
                    <div id="add-layup-error" class="hidden bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-r-md text-sm"></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Layup Name <span class="text-red-500">*</span></label>
                        <input type="text" id="add-layup-name" required maxlength="255"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition"
                            placeholder="e.g. Layup A">
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeAddLayupModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" id="add-layup-submit-btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#367b59] rounded-lg hover:bg-[#2c6448] transition disabled:opacity-60">Create Layup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== MODAL: EDIT LAYUP ========== -->
    <div id="modal-edit-layup" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeEditLayupModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-box bg-white rounded-xl shadow-xl w-full max-w-md relative">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900" style="font-family:'Merriweather',serif;">Edit Layup</h3>
                </div>
                <form id="edit-layup-form" onsubmit="submitEditLayup(event)" class="px-6 py-5 space-y-4">
                    <input type="hidden" id="edit-layup-id">
                    <div id="edit-layup-error" class="hidden bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-r-md text-sm"></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Layup Name <span class="text-red-500">*</span></label>
                        <input type="text" id="edit-layup-name" required maxlength="255"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition">
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeEditLayupModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" id="edit-layup-submit-btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#367b59] rounded-lg hover:bg-[#2c6448] transition disabled:opacity-60">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== MODAL: DELETE LAYUP ========== -->
    <div id="modal-delete-layup" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeDeleteLayupModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-box bg-white rounded-xl shadow-xl w-full max-w-sm relative p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Delete Layup</h3>
                        <p class="text-sm text-gray-500 mt-1">Are you sure you want to delete <span id="delete-layup-name" class="font-semibold text-gray-700"></span>? All associated layers will also be removed. This action cannot be undone.</p>
                    </div>
                </div>
                <input type="hidden" id="delete-layup-id">
                <div class="flex justify-end gap-3 mt-5">
                    <button type="button" onclick="closeDeleteLayupModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button type="button" onclick="submitDeleteLayup()" id="delete-layup-confirm-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition disabled:opacity-60">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
const SUPPLIER_ID = {{ $supplierId }};

let supplier  = null;
let allLayups = [];
let layupPage = 1, layupLastPage = 1, layupTotal = 0, layupPerPage = 15;

// ─── Register Escape handler for page modals ──────────────────────────────────
window._escapeHandlers = function () {
    ['modal-edit-supplier','modal-add-layup','modal-edit-layup','modal-delete-layup'].forEach(id => {
        const el = document.getElementById(id);
        if (el && !el.classList.contains('hidden')) el.classList.add('hidden');
    });
};

// ─── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    const user = await window.authReady;
    await Promise.all([loadSupplier(), loadLayups(1)]);
});

// ─── Load Supplier ────────────────────────────────────────────────────────────
async function loadSupplier() {
    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}`);
        const data = await res.json();
        if (!res.ok) {
            if (res.status === 401) { window.location.href = '/login'; return; }
            window.location.href = '/dashboard';
            return;
        }
        supplier = data.data;
        renderSupplierHeader();
    } catch (err) { console.error(err); }
}

function renderSupplierHeader() {
    if (!supplier) return;
    const user  = window.currentUser;
    const color = window.avatarColor(supplier.name);
    const av    = document.getElementById('supplier-avatar');
    av.textContent       = window.initials(supplier.name);
    av.style.background  = color.bg;
    av.style.color       = color.text;
    av.style.borderColor = color.border;

    document.getElementById('supplier-name-skeleton').style.display = 'none';
    document.getElementById('supplier-date-skeleton').style.display = 'none';

    const nameEl = document.getElementById('supplier-name');
    nameEl.textContent = supplier.name;
    nameEl.classList.remove('hidden');

    const metaEl = document.getElementById('supplier-meta');
    metaEl.textContent = `ID #${String(supplier.id).padStart(4,'0')} · Added ${window.formatDate(supplier.created_at)}`;
    metaEl.classList.remove('hidden');

    // Admin: show edit supplier + add layup
    if (user?.role === 'admin') {
        document.getElementById('supplier-edit-btn').classList.remove('hidden');
        document.getElementById('add-layup-btn').style.display = '';
    }
    // Supplier: add layup only (no edit supplier)
    if (user?.role === 'supplier') {
        document.getElementById('add-layup-btn').style.display = '';
    }
}

// ─── Load Layups ──────────────────────────────────────────────────────────────
async function loadLayups(page = 1) {
    document.getElementById('layup-table-body').innerHTML =
        [1,2,3].map(() => `<tr><td colspan="3" class="px-6 py-5"><div class="h-4 skeleton rounded w-2/3"></div></td></tr>`).join('');
    document.getElementById('layup-empty').style.display = 'none';

    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}/layups?page=${page}`);
        const data = await res.json();
        if (!res.ok) { renderLayupEmpty(); return; }

        allLayups    = data.data || [];
        layupPage     = data.meta?.current_page ?? 1;
        layupLastPage = data.meta?.last_page     ?? 1;
        layupTotal    = data.meta?.total         ?? allLayups.length;
        layupPerPage  = data.meta?.per_page      ?? 15;

        renderLayups();
    } catch (err) { console.error(err); renderLayupEmpty(); }
}

function renderLayups() {
    const tbody   = document.getElementById('layup-table-body');
    const user    = window.currentUser;
    const canEdit = user?.role === 'admin' || user?.role === 'supplier';

    if (allLayups.length === 0) { renderLayupEmpty(); return; }
    document.getElementById('layup-empty').style.display = 'none';

    tbody.innerHTML = allLayups.map(l => {
        const crudBtns = canEdit ? `
            <button onclick="openEditLayupModal(${l.id})"
                class="p-1.5 text-gray-400 hover:text-[#367b59] hover:bg-[#ecfdf5] rounded transition" title="Edit">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </button>
            <button onclick="openDeleteLayupModal(${l.id})"
                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition" title="Delete">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>` : '';
        return `
        <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${window.escapeHtml(l.name)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${window.formatDate(l.created_at)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="inline-flex items-center gap-1">
                    <a href="/suppliers/${SUPPLIER_ID}/layups/${l.id}"
                        class="p-1.5 text-gray-400 hover:text-[#367b59] hover:bg-[#ecfdf5] rounded transition" title="View layers">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    ${crudBtns}
                </div>
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
    const start = layupTotal > 0 ? (layupPage - 1) * layupPerPage + 1 : 0;
    const end   = Math.min(layupPage * layupPerPage, layupTotal);
    document.getElementById('layup-pagination-info').textContent =
        layupTotal > 0 ? `Showing ${start}–${end} of ${layupTotal} layups` : 'No layups';
    document.getElementById('layup-page-indicator').textContent = `${layupPage} / ${layupLastPage}`;
    document.getElementById('layup-prev-btn').disabled = layupPage <= 1;
    document.getElementById('layup-next-btn').disabled = layupPage >= layupLastPage;
}

function goToLayupPage(page) {
    if (page < 1 || page > layupLastPage) return;
    loadLayups(page);
}

// ─── Edit Supplier ────────────────────────────────────────────────────────────
function openEditSupplierModal() {
    if (!supplier) return;
    document.getElementById('edit-supplier-name').value = supplier.name;
    document.getElementById('edit-supplier-error').classList.add('hidden');
    document.getElementById('modal-edit-supplier').classList.remove('hidden');
    setTimeout(() => document.getElementById('edit-supplier-name').focus(), 50);
}

function closeEditSupplierModal() {
    document.getElementById('modal-edit-supplier').classList.add('hidden');
}

async function submitEditSupplier(e) {
    e.preventDefault();
    const btn      = document.getElementById('edit-supplier-submit-btn');
    const errorDiv = document.getElementById('edit-supplier-error');
    const name     = document.getElementById('edit-supplier-name').value.trim();

    errorDiv.classList.add('hidden');
    btn.disabled = true; btn.textContent = 'Saving...';

    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}`, { method: 'PUT', body: JSON.stringify({ name }) });
        const data = await res.json();
        if (res.ok && data.success) {
            supplier = data.data;
            closeEditSupplierModal();
            renderSupplierHeader();
            window.showToast('Supplier updated successfully.', 'success');
        } else {
            const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Failed to update.');
            errorDiv.textContent = msg;
            errorDiv.classList.remove('hidden');
        }
    } catch (err) {
        errorDiv.textContent = 'Network error. Please try again.';
        errorDiv.classList.remove('hidden');
    } finally {
        btn.disabled = false; btn.textContent = 'Save Changes';
    }
}

// ─── Add Layup ────────────────────────────────────────────────────────────────
function openAddLayupModal() {
    document.getElementById('add-layup-form').reset();
    document.getElementById('add-layup-error').classList.add('hidden');
    document.getElementById('modal-add-layup').classList.remove('hidden');
    setTimeout(() => document.getElementById('add-layup-name').focus(), 50);
}

function closeAddLayupModal() {
    document.getElementById('modal-add-layup').classList.add('hidden');
}

async function submitAddLayup(e) {
    e.preventDefault();
    const btn      = document.getElementById('add-layup-submit-btn');
    const errorDiv = document.getElementById('add-layup-error');
    const name     = document.getElementById('add-layup-name').value.trim();

    errorDiv.classList.add('hidden');
    btn.disabled = true; btn.textContent = 'Creating...';

    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}/layups`, { method: 'POST', body: JSON.stringify({ name }) });
        const data = await res.json();
        if (res.ok && data.success) {
            closeAddLayupModal();
            window.showToast(`Layup "${name}" created successfully.`, 'success');
            await loadLayups(layupPage);
        } else {
            const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Failed to create layup.');
            errorDiv.textContent = msg;
            errorDiv.classList.remove('hidden');
        }
    } catch (err) {
        errorDiv.textContent = 'Network error. Please try again.';
        errorDiv.classList.remove('hidden');
    } finally {
        btn.disabled = false; btn.textContent = 'Create Layup';
    }
}

// ─── Edit Layup ───────────────────────────────────────────────────────────────
function openEditLayupModal(id) {
    const layup = allLayups.find(l => l.id === id);
    if (!layup) return;
    document.getElementById('edit-layup-id').value   = layup.id;
    document.getElementById('edit-layup-name').value = layup.name;
    document.getElementById('edit-layup-error').classList.add('hidden');
    document.getElementById('modal-edit-layup').classList.remove('hidden');
    setTimeout(() => document.getElementById('edit-layup-name').focus(), 50);
}

function closeEditLayupModal() {
    document.getElementById('modal-edit-layup').classList.add('hidden');
}

async function submitEditLayup(e) {
    e.preventDefault();
    const btn      = document.getElementById('edit-layup-submit-btn');
    const errorDiv = document.getElementById('edit-layup-error');
    const id       = document.getElementById('edit-layup-id').value;
    const name     = document.getElementById('edit-layup-name').value.trim();

    errorDiv.classList.add('hidden');
    btn.disabled = true; btn.textContent = 'Saving...';

    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}/layups/${id}`, { method: 'PUT', body: JSON.stringify({ name }) });
        const data = await res.json();
        if (res.ok && data.success) {
            closeEditLayupModal();
            window.showToast('Layup updated successfully.', 'success');
            await loadLayups(layupPage);
        } else {
            const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Failed to update layup.');
            errorDiv.textContent = msg;
            errorDiv.classList.remove('hidden');
        }
    } catch (err) {
        errorDiv.textContent = 'Network error. Please try again.';
        errorDiv.classList.remove('hidden');
    } finally {
        btn.disabled = false; btn.textContent = 'Save Changes';
    }
}

// ─── Delete Layup ─────────────────────────────────────────────────────────────
function openDeleteLayupModal(id) {
    const layup = allLayups.find(l => l.id === id);
    if (!layup) return;
    document.getElementById('delete-layup-id').value         = layup.id;
    document.getElementById('delete-layup-name').textContent = layup.name;
    document.getElementById('modal-delete-layup').classList.remove('hidden');
}

function closeDeleteLayupModal() {
    document.getElementById('modal-delete-layup').classList.add('hidden');
}

async function submitDeleteLayup() {
    const btn = document.getElementById('delete-layup-confirm-btn');
    const id  = document.getElementById('delete-layup-id').value;

    btn.disabled = true; btn.textContent = 'Deleting...';

    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}/layups/${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (res.ok && data.success) {
            closeDeleteLayupModal();
            window.showToast('Layup deleted successfully.', 'success');
            const newPage = (allLayups.length === 1 && layupPage > 1) ? layupPage - 1 : layupPage;
            await loadLayups(newPage);
        } else {
            closeDeleteLayupModal();
            window.showToast(data.message || 'Failed to delete layup.', 'error');
        }
    } catch (err) {
        closeDeleteLayupModal();
        window.showToast('Network error. Please try again.', 'error');
    } finally {
        btn.disabled = false; btn.textContent = 'Delete';
    }
}
</script>
@endpush
