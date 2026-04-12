@extends('layouts.auth')

@push('nav-links')
    <div class="hidden sm:-my-px sm:ml-4 sm:flex sm:space-x-8">
        <a href="/dashboard" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Suppliers</a>
        <a href="/suppliers/{{ $supplierId }}" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Layups</a>
        <a href="#" class="tab-active inline-flex items-center px-1 pt-1 text-sm transition">Layers</a>
    </div>
@endpush

@section('content')

    <!-- Back link -->
    <div class="mb-6 animate-fade-in">
        <a href="/suppliers/{{ $supplierId }}" class="inline-flex items-center text-sm text-gray-500 hover:text-[#367b59] transition gap-1">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Layups
        </a>
    </div>

    <!-- Layup Header Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6 animate-fade-in">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-lg bg-[#ecfdf5] flex items-center justify-center flex-shrink-0">
                <svg class="h-6 w-6 text-[#367b59]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="h-5 skeleton rounded w-48 mb-2" id="layup-name-skeleton"></div>
                <div class="h-3 skeleton rounded w-32" id="layup-meta-skeleton"></div>
                <h1 class="text-xl font-bold text-gray-900 hidden" id="layup-title" style="font-family:'Merriweather',serif;"></h1>
                <p class="text-sm text-gray-500 hidden" id="layup-meta"></p>
            </div>
            <!-- Edit Layup: admin + supplier -->
            <div id="edit-layup-header-btn" class="hidden">
                <button onclick="openEditLayupModal()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition gap-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Edit Layup
                </button>
            </div>
        </div>
    </div>

    <!-- Layers Section -->
    <div class="animate-fade-in">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900" style="font-family:'Merriweather',serif;">Layers</h2>
            <!-- Add Layer: admin + supplier -->
            <button id="add-layer-btn" onclick="openAddLayerModal()" style="display:none;"
                class="inline-flex items-center px-3 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-[#367b59] hover:bg-[#2c6448] transition gap-1.5">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Layer
            </button>
        </div>

        <div class="bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">Order</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">Thickness</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">Width</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">Angle (°)</th>
                            <th class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" id="layer-table-body">
                        <tr><td colspan="5" class="px-6 py-5"><div class="h-4 skeleton rounded w-3/4"></div></td></tr>
                        <tr><td colspan="5" class="px-6 py-5"><div class="h-4 skeleton rounded w-2/3"></div></td></tr>
                        <tr><td colspan="5" class="px-6 py-5"><div class="h-4 skeleton rounded w-1/2"></div></td></tr>
                    </tbody>
                </table>
            </div>

            <div id="layer-empty" style="display:none;" class="py-12 text-center">
                <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                <p class="text-sm text-gray-500">No layers found for this layup.</p>
            </div>

            <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <p class="text-sm text-gray-500" id="layer-pagination-info">—</p>
                <nav class="relative z-0 inline-flex rounded shadow-sm -space-x-px border border-gray-200">
                    <button id="layer-prev-btn" onclick="goToLayerPage(layerPage - 1)"
                        class="relative inline-flex items-center px-2 py-2 rounded-l bg-white text-sm text-gray-400 hover:bg-gray-50 border-r border-gray-200 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <span id="layer-page-indicator" class="relative inline-flex items-center px-4 py-2 bg-white text-sm text-gray-700 border-r border-gray-200">1 / 1</span>
                    <button id="layer-next-btn" onclick="goToLayerPage(layerPage + 1)"
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
    <!-- ========== MODAL: EDIT LAYUP (header) ========== -->
    <div id="modal-edit-layup" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeEditLayupModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-box bg-white rounded-xl shadow-xl w-full max-w-md relative">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900" style="font-family:'Merriweather',serif;">Edit Layup</h3>
                </div>
                <form onsubmit="submitEditLayup(event)" class="px-6 py-5 space-y-4">
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

    <!-- ========== MODAL: ADD LAYER ========== -->
    <div id="modal-add-layer" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeAddLayerModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-box bg-white rounded-xl shadow-xl w-full max-w-md relative">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900" style="font-family:'Merriweather',serif;">Add Layer</h3>
                </div>
                <form id="add-layer-form" onsubmit="submitAddLayer(event)" class="px-6 py-5 space-y-4">
                    <div id="add-layer-error" class="hidden bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-r-md text-sm"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Layer Order <span class="text-red-500">*</span></label>
                            <input type="number" id="add-layer-order" required min="1" step="1"
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition"
                                placeholder="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Angle (°) <span class="text-red-500">*</span></label>
                            <input type="number" id="add-layer-angle" required min="-180" max="180" step="any"
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition"
                                placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thickness <span class="text-red-500">*</span></label>
                            <input type="number" id="add-layer-thickness" required min="0" step="any"
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Width <span class="text-red-500">*</span></label>
                            <input type="number" id="add-layer-width" required min="0" step="any"
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition"
                                placeholder="0.00">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeAddLayerModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" id="add-layer-submit-btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#367b59] rounded-lg hover:bg-[#2c6448] transition disabled:opacity-60">Create Layer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== MODAL: EDIT LAYER ========== -->
    <div id="modal-edit-layer" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeEditLayerModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-box bg-white rounded-xl shadow-xl w-full max-w-md relative">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-900" style="font-family:'Merriweather',serif;">Edit Layer</h3>
                </div>
                <form id="edit-layer-form" onsubmit="submitEditLayer(event)" class="px-6 py-5 space-y-4">
                    <input type="hidden" id="edit-layer-id">
                    <div id="edit-layer-error" class="hidden bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-r-md text-sm"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Layer Order <span class="text-red-500">*</span></label>
                            <input type="number" id="edit-layer-order" required min="1" step="1"
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Angle (°) <span class="text-red-500">*</span></label>
                            <input type="number" id="edit-layer-angle" required min="-180" max="180" step="any"
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thickness <span class="text-red-500">*</span></label>
                            <input type="number" id="edit-layer-thickness" required min="0" step="any"
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Width <span class="text-red-500">*</span></label>
                            <input type="number" id="edit-layer-width" required min="0" step="any"
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#367b59] focus:border-[#367b59] transition">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeEditLayerModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" id="edit-layer-submit-btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#367b59] rounded-lg hover:bg-[#2c6448] transition disabled:opacity-60">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== MODAL: DELETE LAYER ========== -->
    <div id="modal-delete-layer" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="modal-backdrop fixed inset-0 bg-black/40" onclick="closeDeleteLayerModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="modal-box bg-white rounded-xl shadow-xl w-full max-w-sm relative p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Delete Layer</h3>
                        <p class="text-sm text-gray-500 mt-1">Delete Layer #<span id="delete-layer-order" class="font-semibold text-gray-700"></span>? This action cannot be undone.</p>
                    </div>
                </div>
                <input type="hidden" id="delete-layer-id">
                <div class="flex justify-end gap-3 mt-5">
                    <button type="button" onclick="closeDeleteLayerModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button type="button" onclick="submitDeleteLayer()" id="delete-layer-confirm-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition disabled:opacity-60">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
const SUPPLIER_ID = {{ $supplierId }};
const LAYUP_ID    = {{ $layupId }};

let layup     = null;
let allLayers = [];
let layerPage = 1, layerLastPage = 1, layerTotal = 0, layerPerPage = 10;

// ─── Register Escape handler for page modals ──────────────────────────────────
window._escapeHandlers = function () {
    ['modal-edit-layup','modal-add-layer','modal-edit-layer','modal-delete-layer'].forEach(id => {
        const el = document.getElementById(id);
        if (el && !el.classList.contains('hidden')) el.classList.add('hidden');
    });
};

// ─── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    await window.authReady;
    await Promise.all([loadLayup(), loadLayers(1)]);
});

// ─── Load Layup ───────────────────────────────────────────────────────────────
async function loadLayup() {
    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}/layups/${LAYUP_ID}`);
        const data = await res.json();
        if (!res.ok) {
            if (res.status === 401) { window.location.href = '/login'; return; }
            window.location.href = `/suppliers/${SUPPLIER_ID}`;
            return;
        }
        layup = data.data;
        renderLayupHeader();
    } catch (err) { console.error(err); }
}

function renderLayupHeader() {
    if (!layup) return;
    const user     = window.currentUser;
    const canEdit  = user?.role === 'admin' || user?.role === 'supplier';

    document.getElementById('layup-name-skeleton').style.display = 'none';
    document.getElementById('layup-meta-skeleton').style.display = 'none';

    const titleEl = document.getElementById('layup-title');
    titleEl.textContent = layup.name;
    titleEl.classList.remove('hidden');

    const metaEl = document.getElementById('layup-meta');
    metaEl.textContent = `Layup #${String(layup.id).padStart(4,'0')} · Created ${window.formatDate(layup.created_at)}`;
    metaEl.classList.remove('hidden');

    if (canEdit) {
        document.getElementById('edit-layup-header-btn').classList.remove('hidden');
        document.getElementById('add-layer-btn').style.display = '';
    }
}

// ─── Load Layers ──────────────────────────────────────────────────────────────
async function loadLayers(page = 1) {
    document.getElementById('layer-table-body').innerHTML =
        [1,2,3].map(() => `<tr><td colspan="5" class="px-6 py-5"><div class="h-4 skeleton rounded w-2/3"></div></td></tr>`).join('');
    document.getElementById('layer-empty').style.display = 'none';

    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}/layups/${LAYUP_ID}/layers?page=${page}`);
        const data = await res.json();
        if (!res.ok) { renderLayerEmpty(); return; }

        allLayers     = data.data || [];
        layerPage     = data.meta?.current_page ?? 1;
        layerLastPage = data.meta?.last_page     ?? 1;
        layerTotal    = data.meta?.total         ?? allLayers.length;
        layerPerPage  = data.meta?.per_page      ?? 10;

        renderLayers();
    } catch (err) { console.error(err); renderLayerEmpty(); }
}

function renderLayers() {
    const tbody   = document.getElementById('layer-table-body');
    const user    = window.currentUser;
    const canEdit = user?.role === 'admin' || user?.role === 'supplier';

    if (allLayers.length === 0) { renderLayerEmpty(); return; }
    document.getElementById('layer-empty').style.display = 'none';

    tbody.innerHTML = allLayers.map(l => {
        const crudBtns = canEdit ? `
            <button onclick="openEditLayerModal(${l.id})"
                class="p-1.5 text-gray-400 hover:text-[#367b59] hover:bg-[#ecfdf5] rounded transition" title="Edit">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </button>
            <button onclick="openDeleteLayerModal(${l.id})"
                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition" title="Delete">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>` : '';

        return `
        <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center justify-center h-7 w-7 rounded-full bg-[#ecfdf5] text-[#367b59] text-xs font-bold">${l.layer_order}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${l.thickness}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${l.width}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${l.angle}°</td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="inline-flex items-center gap-1">${crudBtns}</div>
            </td>
        </tr>`;
    }).join('');

    updateLayerPagination();
}

function renderLayerEmpty() {
    document.getElementById('layer-table-body').innerHTML = '';
    document.getElementById('layer-empty').style.display = '';
    updateLayerPagination();
}

function updateLayerPagination() {
    const start = layerTotal > 0 ? (layerPage - 1) * layerPerPage + 1 : 0;
    const end   = Math.min(layerPage * layerPerPage, layerTotal);
    document.getElementById('layer-pagination-info').textContent =
        layerTotal > 0 ? `Showing ${start}–${end} of ${layerTotal} layers` : 'No layers';
    document.getElementById('layer-page-indicator').textContent = `${layerPage} / ${layerLastPage}`;
    document.getElementById('layer-prev-btn').disabled = layerPage <= 1;
    document.getElementById('layer-next-btn').disabled = layerPage >= layerLastPage;
}

function goToLayerPage(page) {
    if (page < 1 || page > layerLastPage) return;
    loadLayers(page);
}

// ─── Edit Layup (header button) ───────────────────────────────────────────────
function openEditLayupModal() {
    if (!layup) return;
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
    const name     = document.getElementById('edit-layup-name').value.trim();

    errorDiv.classList.add('hidden');
    btn.disabled = true; btn.textContent = 'Saving...';

    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}/layups/${LAYUP_ID}`, { method: 'PUT', body: JSON.stringify({ name }) });
        const data = await res.json();
        if (res.ok && data.success) {
            layup = data.data;
            closeEditLayupModal();
            renderLayupHeader();
            window.showToast('Layup updated successfully.', 'success');
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

// ─── Add Layer ────────────────────────────────────────────────────────────────
function openAddLayerModal() {
    document.getElementById('add-layer-form').reset();
    document.getElementById('add-layer-error').classList.add('hidden');
    document.getElementById('modal-add-layer').classList.remove('hidden');
    setTimeout(() => document.getElementById('add-layer-order').focus(), 50);
}

function closeAddLayerModal() {
    document.getElementById('modal-add-layer').classList.add('hidden');
}

async function submitAddLayer(e) {
    e.preventDefault();
    const btn      = document.getElementById('add-layer-submit-btn');
    const errorDiv = document.getElementById('add-layer-error');
    const body = {
        layer_order: parseInt(document.getElementById('add-layer-order').value),
        thickness:   parseFloat(document.getElementById('add-layer-thickness').value),
        width:       parseFloat(document.getElementById('add-layer-width').value),
        angle:       parseFloat(document.getElementById('add-layer-angle').value),
    };

    errorDiv.classList.add('hidden');
    btn.disabled = true; btn.textContent = 'Creating...';

    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}/layups/${LAYUP_ID}/layers`, { method: 'POST', body: JSON.stringify(body) });
        const data = await res.json();
        if (res.ok && data.success) {
            closeAddLayerModal();
            window.showToast('Layer created successfully.', 'success');
            await loadLayers(layerPage);
        } else {
            const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Failed to create layer.');
            errorDiv.textContent = msg;
            errorDiv.classList.remove('hidden');
        }
    } catch (err) {
        errorDiv.textContent = 'Network error. Please try again.';
        errorDiv.classList.remove('hidden');
    } finally {
        btn.disabled = false; btn.textContent = 'Create Layer';
    }
}

// ─── Edit Layer ───────────────────────────────────────────────────────────────
function openEditLayerModal(id) {
    const layer = allLayers.find(l => l.id === id);
    if (!layer) return;
    document.getElementById('edit-layer-id').value        = layer.id;
    document.getElementById('edit-layer-order').value     = layer.layer_order;
    document.getElementById('edit-layer-thickness').value = layer.thickness;
    document.getElementById('edit-layer-width').value     = layer.width;
    document.getElementById('edit-layer-angle').value     = layer.angle;
    document.getElementById('edit-layer-error').classList.add('hidden');
    document.getElementById('modal-edit-layer').classList.remove('hidden');
    setTimeout(() => document.getElementById('edit-layer-order').focus(), 50);
}

function closeEditLayerModal() {
    document.getElementById('modal-edit-layer').classList.add('hidden');
}

async function submitEditLayer(e) {
    e.preventDefault();
    const btn      = document.getElementById('edit-layer-submit-btn');
    const errorDiv = document.getElementById('edit-layer-error');
    const id       = document.getElementById('edit-layer-id').value;
    const body = {
        layer_order: parseInt(document.getElementById('edit-layer-order').value),
        thickness:   parseFloat(document.getElementById('edit-layer-thickness').value),
        width:       parseFloat(document.getElementById('edit-layer-width').value),
        angle:       parseFloat(document.getElementById('edit-layer-angle').value),
    };

    errorDiv.classList.add('hidden');
    btn.disabled = true; btn.textContent = 'Saving...';

    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}/layups/${LAYUP_ID}/layers/${id}`, { method: 'PUT', body: JSON.stringify(body) });
        const data = await res.json();
        if (res.ok && data.success) {
            closeEditLayerModal();
            window.showToast('Layer updated successfully.', 'success');
            await loadLayers(layerPage);
        } else {
            const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Failed to update layer.');
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

// ─── Delete Layer ─────────────────────────────────────────────────────────────
function openDeleteLayerModal(id) {
    const layer = allLayers.find(l => l.id === id);
    if (!layer) return;
    document.getElementById('delete-layer-id').value         = layer.id;
    document.getElementById('delete-layer-order').textContent = layer.layer_order;
    document.getElementById('modal-delete-layer').classList.remove('hidden');
}

function closeDeleteLayerModal() {
    document.getElementById('modal-delete-layer').classList.add('hidden');
}

async function submitDeleteLayer() {
    const btn = document.getElementById('delete-layer-confirm-btn');
    const id  = document.getElementById('delete-layer-id').value;

    btn.disabled = true; btn.textContent = 'Deleting...';

    try {
        const res  = await window.apiFetch(`/api/v1/suppliers/${SUPPLIER_ID}/layups/${LAYUP_ID}/layers/${id}`, { method: 'DELETE' });
        const data = await res.json();
        if (res.ok && data.success) {
            closeDeleteLayerModal();
            window.showToast('Layer deleted successfully.', 'success');
            const newPage = (allLayers.length === 1 && layerPage > 1) ? layerPage - 1 : layerPage;
            await loadLayers(newPage);
        } else {
            closeDeleteLayerModal();
            window.showToast(data.message || 'Failed to delete layer.', 'error');
        }
    } catch (err) {
        closeDeleteLayerModal();
        window.showToast('Network error. Please try again.', 'error');
    } finally {
        btn.disabled = false; btn.textContent = 'Delete';
    }
}
</script>
@endpush
