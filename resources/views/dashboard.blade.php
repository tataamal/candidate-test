@extends('layouts.auth')

@push('nav-links')
    <div class="hidden sm:-my-px sm:ml-4 sm:flex sm:space-x-8">
        <a href="/dashboard" class="tab-active inline-flex items-center px-1 pt-1 text-sm transition">Suppliers</a>
        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Layups</a>
        <a href="#" class="tab-inactive inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm transition">Layers</a>
    </div>
@endpush

@section('content')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 animate-fade-in">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-1" style="font-family:'Merriweather',serif;">Suppliers</h1>
            <p class="text-sm text-gray-500">Manage timber suppliers and material sourcing.</p>
        </div>
        <!-- Add button: admin only, hidden by default -->
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
                    <tr><td colspan="3" class="px-6 py-5"><div class="h-4 skeleton rounded w-3/4"></div></td></tr>
                    <tr><td colspan="3" class="px-6 py-5"><div class="h-4 skeleton rounded w-2/3"></div></td></tr>
                    <tr><td colspan="3" class="px-6 py-5"><div class="h-4 skeleton rounded w-1/2"></div></td></tr>
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
        <div class="bg-white px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500" id="pagination-info">—</p>
            <nav class="relative z-0 inline-flex rounded shadow-sm -space-x-px border border-gray-200" aria-label="Pagination">
                <button id="prev-btn" onclick="goToPage(currentPage - 1)"
                    class="relative inline-flex items-center px-2 py-2 rounded-l bg-white text-sm font-medium text-gray-400 hover:bg-gray-50 border-r border-gray-200 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </button>
                <span id="page-indicator" class="relative inline-flex items-center px-4 py-2 bg-white text-sm text-gray-700 border-r border-gray-200">1 / 1</span>
                <button id="next-btn" onclick="goToPage(currentPage + 1)"
                    class="relative inline-flex items-center px-2 py-2 rounded-r bg-white text-sm font-medium text-gray-400 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </nav>
        </div>
    </div>

@endsection

@push('modals')
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
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" id="add-submit-btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#367b59] rounded-lg hover:bg-[#2c6448] transition disabled:opacity-60">Create Supplier</button>
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
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        <button type="submit" id="edit-submit-btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-[#367b59] rounded-lg hover:bg-[#2c6448] transition disabled:opacity-60">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========== MODAL: DELETE SUPPLIER ========== -->
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
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button type="button" onclick="submitDelete()" id="delete-confirm-btn"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition disabled:opacity-60">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endpush
