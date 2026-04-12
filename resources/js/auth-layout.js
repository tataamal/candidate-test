// ─── State ────────────────────────────────────────────────────────────────────
let currentUser  = null;
let allSuppliers = [];   // full page from API
let filteredRows = [];   // after client-side search
let currentPage  = 1;
let lastPage     = 1;
let totalItems   = 0;
let perPage      = 15;
let searchQuery  = '';

// ─── Avatar palette (deterministic by name) ───────────────────────────────────
const AVATAR_COLORS = [
    { bg: '#eff6ff', text: '#2563eb', border: '#bfdbfe' },
    { bg: '#ecfdf5', text: '#059669', border: '#a7f3d0' },
    { bg: '#fff7ed', text: '#d97706', border: '#fed7aa' },
    { bg: '#faf5ff', text: '#7c3aed', border: '#ddd6fe' },
    { bg: '#f0fdfa', text: '#0d9488', border: '#99f6e4' },
    { bg: '#fdf2f8', text: '#db2777', border: '#fbcfe8' },
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
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

// ─── API Helpers ──────────────────────────────────────────────────────────────
async function apiFetch(url, options = {}) {
    const defaults = {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    };
    return fetch(url, {
        ...defaults,
        ...options,
        headers: { ...defaults.headers, ...(options.headers || {}) }
    });
}

// ─── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    await loadUser();
    await loadSuppliers(1);
});

// ─── Load User ────────────────────────────────────────────────────────────────
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

// ─── Load Suppliers ───────────────────────────────────────────────────────────
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

// ─── Search (client-side on current page) ────────────────────────────────────
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

// ─── Render Table ─────────────────────────────────────────────────────────────
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

// ─── Pagination ───────────────────────────────────────────────────────────────
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

// ─── Loading / Empty States ───────────────────────────────────────────────────
function setTableLoading(loading) {
    if (loading) {
        document.getElementById('supplier-table-body').innerHTML = `
            ${[1, 2, 3, 4, 5].map(() => `
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

// ─── Modal: Add ───────────────────────────────────────────────────────────────
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

// ─── Modal: Edit ──────────────────────────────────────────────────────────────
function openEditModal(id) {
    const supplier = allSuppliers.find(s => s.id === id);
    if (!supplier) return;
    document.getElementById('edit-id').value   = supplier.id;
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
            showToast('Supplier updated successfully.', 'success');
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

// ─── Modal: Delete ────────────────────────────────────────────────────────────
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

// ─── Logout ───────────────────────────────────────────────────────────────────
async function handleLogout() {
    try {
        await apiFetch('/api/v1/logout', { method: 'POST' });
    } catch (err) {
        console.error(err);
    } finally {
        window.location.href = '/login';
    }
}

// ─── Export Toast ─────────────────────────────────────────────────────────────
function showExportToast() {
    showToast('Fitur ini masih dalam pengembangan 🚧', 'info');
}

// ─── Toast ────────────────────────────────────────────────────────────────────
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
        toast.style.opacity   = '0';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'opacity 0.3s, transform 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// ─── Escape Helpers ───────────────────────────────────────────────────────────
function escapeHtml(str) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return String(str).replace(/[&<>"']/g, m => map[m]);
}

function escapeAttr(str) {
    return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

// ─── Keyboard: close modals on Escape ────────────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
        closeDeleteModal();
    }
});
