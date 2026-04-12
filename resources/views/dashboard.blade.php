@extends('layouts.auth')

@section('content')  

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
@endsection