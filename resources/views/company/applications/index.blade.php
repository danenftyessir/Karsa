@extends('layouts.app')

@section('title', 'Manajemen Lamaran - ' . $company->name)

@push('styles')
<style>
    /* animasi dan transisi yang dioptimasi untuk performa */
    .kanban-column {
        min-height: 500px;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
    }

    .application-item {
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    .application-item:hover {
        transform: translateY(-2px) translateZ(0);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
    }

    /* drag state styling */
    .application-item.dragging {
        opacity: 0.5;
        transform: rotate(2deg);
    }

    .kanban-column.drag-over {
        background-color: rgba(59, 130, 246, 0.05);
        border: 2px dashed #3b82f6;
    }

    /* smooth scroll untuk kanban container */
    .kanban-container {
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }

    /* status badge colors */
    .status-new { background-color: #3b82f6; }
    .status-reviewing { background-color: #eab308; }
    .status-shortlisted { background-color: #f97316; }
    .status-interview { background-color: #a855f7; }
    .status-offer { background-color: #22c55e; }
    .status-rejected { background-color: #ef4444; }

    /* column header colors */
    .column-header-new { border-left: 4px solid #3b82f6; }
    .column-header-reviewing { border-left: 4px solid #eab308; }
    .column-header-shortlisted { border-left: 4px solid #f97316; }
    .column-header-interview { border-left: 4px solid #a855f7; }
    .column-header-offer { border-left: 4px solid #22c55e; }
    .column-header-rejected { border-left: 4px solid #ef4444; }

    /* respek reduced motion */
    @media (prefers-reduced-motion: reduce) {
        .kanban-column,
        .application-item {
            transition: none;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" x-data="applicationsKanban()">

    <!-- header section -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                <!-- title dan stats -->
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manajemen Lamaran</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Total <span class="font-medium text-gray-900" x-text="totalApplications"></span> lamaran aktif
                    </p>
                </div>

                <!-- actions -->
                <div class="flex items-center gap-3">

                    <!-- view toggle -->
                    <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1">
                        <button @click="viewMode = 'kanban'"
                                :class="viewMode === 'kanban' ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                            </svg>
                            Kanban Board
                        </button>
                        <button @click="viewMode = 'list'"
                                :class="viewMode === 'list' ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            List View
                        </button>
                    </div>

                    <!-- bulk actions -->
                    <div x-show="selectedApplications.length > 0" x-cloak class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Bulk Actions
                            <span class="ml-2 bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full text-xs" x-text="selectedApplications.length"></span>
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <button @click="bulkUpdateStatus('shortlisted'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Shortlist Semua
                            </button>
                            <button @click="bulkUpdateStatus('rejected'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Tolak Semua
                            </button>
                            <button @click="exportSelected(); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Export ke CSV
                            </button>
                        </div>
                    </div>

                    <!-- filter button -->
                    <button @click="showFilters = !showFilters"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter
                    </button>
                </div>
            </div>

            <!-- filter panel -->
            <div x-show="showFilters" x-collapse x-cloak class="mt-4 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                        <select x-model="filters.position" class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="">Semua Posisi</option>
                            <template x-for="position in availablePositions" :key="position">
                                <option :value="position" x-text="position"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Melamar</label>
                        <select x-model="filters.dateRange" class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="">Semua Waktu</option>
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nama</label>
                        <input type="text" x-model="filters.search" placeholder="Ketik nama kandidat..."
                               class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div class="flex items-end">
                        <button @click="resetFilters()" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">
                            Reset Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- kanban board view -->
    <div x-show="viewMode === 'kanban'" class="kanban-container overflow-x-auto">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex gap-4 min-w-max">

                <!-- columns -->
                <template x-for="(column, status) in columns" :key="status">
                    <div class="kanban-column w-80 flex-shrink-0 bg-gray-100 rounded-xl p-4"
                         :class="'column-' + status"
                         @dragover.prevent="dragOver($event, status)"
                         @dragleave="dragLeave($event)"
                         @drop="drop($event, status)">

                        <!-- column header -->
                        <div class="flex items-center justify-between mb-4 p-3 bg-white rounded-lg"
                             :class="'column-header-' + status">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-900" x-text="column.title"></h3>
                                <span class="bg-gray-200 text-gray-600 text-xs font-medium px-2 py-0.5 rounded-full"
                                      x-text="getColumnCount(status)"></span>
                            </div>
                        </div>

                        <!-- applications list -->
                        <div class="space-y-3">
                            <template x-for="application in getFilteredApplications(status)" :key="application.id">
                                <div class="application-item bg-white rounded-lg p-4 shadow-sm border border-gray-200 cursor-move"
                                     draggable="true"
                                     @dragstart="dragStart($event, application)"
                                     @dragend="dragEnd($event)">

                                    <!-- checkbox untuk bulk select -->
                                    <div class="flex items-start gap-3">
                                        <input type="checkbox"
                                               :value="application.id"
                                               x-model="selectedApplications"
                                               class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                               @click.stop>

                                        <div class="flex-1 min-w-0">
                                            <!-- avatar dan nama -->
                                            <div class="flex items-center gap-3 mb-2">
                                                <img :src="'/storage/profiles/' + application.avatar"
                                                     :alt="application.name"
                                                     class="w-10 h-10 rounded-full object-cover"
                                                     onerror="this.src='/images/default-avatar.png'">
                                                <div class="min-w-0">
                                                    <h4 class="font-medium text-gray-900 truncate" x-text="application.name"></h4>
                                                    <p class="text-sm text-gray-500 truncate" x-text="application.position"></p>
                                                </div>
                                            </div>

                                            <!-- applied date -->
                                            <div class="flex items-center gap-1 text-xs text-gray-400 mb-3">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span x-text="formatDate(application.applied_date)"></span>
                                            </div>

                                            <!-- status badge -->
                                            <div class="mb-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                                      :class="'status-' + application.status"
                                                      x-text="getStatusLabel(application.status)"></span>
                                            </div>

                                            <!-- action links -->
                                            <div class="flex items-center gap-4 pt-2 border-t border-gray-100">
                                                <a :href="'/company/applications/' + application.id"
                                                   class="text-sm font-medium text-primary-600 hover:text-primary-700"
                                                   @click.stop>
                                                    Profile
                                                </a>
                                                <button @click.stop="openReviewModal(application)"
                                                        class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                                    Review
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- empty state -->
                            <div x-show="getColumnCount(status) === 0"
                                 class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-sm">Tidak Ada Lamaran</p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- list view -->
    <div x-show="viewMode === 'list'" x-cloak class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" @change="toggleSelectAll($event)"
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kandidat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Melamar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="application in allFilteredApplications" :key="application.id">
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <input type="checkbox" :value="application.id" x-model="selectedApplications"
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img :src="'/storage/profiles/' + application.avatar"
                                         :alt="application.name"
                                         class="w-10 h-10 rounded-full object-cover"
                                         onerror="this.src='/images/default-avatar.png'">
                                    <span class="font-medium text-gray-900" x-text="application.name"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="application.position"></td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="formatDate(application.applied_date)"></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                      :class="'status-' + application.status"
                                      x-text="getStatusLabel(application.status)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a :href="'/company/applications/' + application.id"
                                       class="text-sm font-medium text-primary-600 hover:text-primary-700">Profile</a>
                                    <button @click="openReviewModal(application)"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-900">Review</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- review modal -->
    <div x-show="reviewModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="reviewModalOpen = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" @click="reviewModalOpen = false"></div>

            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 transform transition-all">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Review Kandidat</h3>

                <template x-if="selectedApplication">
                    <div>
                        <div class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-lg">
                            <img :src="'/storage/profiles/' + selectedApplication.avatar"
                                 class="w-12 h-12 rounded-full object-cover"
                                 onerror="this.src='/images/default-avatar.png'">
                            <div>
                                <p class="font-medium text-gray-900" x-text="selectedApplication.name"></p>
                                <p class="text-sm text-gray-500" x-text="selectedApplication.position"></p>
                            </div>
                        </div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">Ubah Status</label>
                        <select x-model="newStatus" class="w-full rounded-lg border-gray-300 mb-4">
                            <option value="new">Lamaran Baru</option>
                            <option value="reviewing">Sedang Direview</option>
                            <option value="shortlisted">Terpilih</option>
                            <option value="interview">Interview</option>
                            <option value="offer">Penawaran</option>
                            <option value="rejected">Ditolak</option>
                        </select>

                        <div class="flex gap-3">
                            <button @click="reviewModalOpen = false"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                Batal
                            </button>
                            <button @click="updateApplicationStatus()"
                                    class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                                Simpan
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function applicationsKanban() {
    return {
        viewMode: '{{ $viewMode }}',
        showFilters: false,
        selectedApplications: [],
        reviewModalOpen: false,
        selectedApplication: null,
        newStatus: '',
        draggedApplication: null,
        totalApplications: {{ $totalApplications }},

        filters: {
            position: '',
            dateRange: '',
            search: ''
        },

        // TO DO: ambil data dari database via API
        columns: @json($columns),

        applications: @json($applications),

        get availablePositions() {
            return [...new Set(this.applications.map(a => a.position))];
        },

        get allFilteredApplications() {
            return this.applications.filter(app => this.matchesFilters(app));
        },

        matchesFilters(app) {
            if (this.filters.position && app.position !== this.filters.position) return false;
            if (this.filters.search && !app.name.toLowerCase().includes(this.filters.search.toLowerCase())) return false;
            // TO DO: implementasi date range filter
            return true;
        },

        getFilteredApplications(status) {
            const columnApps = this.columns[status]?.applications || [];
            return Object.values(columnApps).filter(app => this.matchesFilters(app));
        },

        getColumnCount(status) {
            return this.getFilteredApplications(status).length;
        },

        getStatusLabel(status) {
            const labels = {
                'new': 'Baru',
                'reviewing': 'Direview',
                'shortlisted': 'Terpilih',
                'interview': 'Interview',
                'offer': 'Penawaran',
                'rejected': 'Ditolak'
            };
            return labels[status] || status;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        },

        resetFilters() {
            this.filters = { position: '', dateRange: '', search: '' };
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedApplications = this.applications.map(a => a.id);
            } else {
                this.selectedApplications = [];
            }
        },

        openReviewModal(application) {
            this.selectedApplication = application;
            this.newStatus = application.status;
            this.reviewModalOpen = true;
        },

        // TO DO: implementasi update status ke database
        async updateApplicationStatus() {
            if (!this.selectedApplication) return;

            try {
                const response = await fetch(`/company/applications/${this.selectedApplication.id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: this.newStatus })
                });

                if (response.ok) {
                    // update local state
                    const oldStatus = this.selectedApplication.status;
                    this.selectedApplication.status = this.newStatus;

                    // move between columns
                    this.moveApplicationBetweenColumns(this.selectedApplication, oldStatus, this.newStatus);

                    this.reviewModalOpen = false;
                }
            } catch (error) {
                console.error('Error updating status:', error);
            }
        },

        moveApplicationBetweenColumns(application, fromStatus, toStatus) {
            // remove from old column
            const oldApps = this.columns[fromStatus].applications;
            const index = Object.keys(oldApps).find(key => oldApps[key].id === application.id);
            if (index) delete this.columns[fromStatus].applications[index];

            // add to new column
            this.columns[toStatus].applications[application.id] = application;
        },

        // drag and drop handlers
        dragStart(event, application) {
            this.draggedApplication = application;
            event.target.classList.add('dragging');
            event.dataTransfer.effectAllowed = 'move';
        },

        dragEnd(event) {
            event.target.classList.remove('dragging');
            document.querySelectorAll('.kanban-column').forEach(col => col.classList.remove('drag-over'));
        },

        dragOver(event, status) {
            event.currentTarget.classList.add('drag-over');
        },

        dragLeave(event) {
            event.currentTarget.classList.remove('drag-over');
        },

        async drop(event, newStatus) {
            event.currentTarget.classList.remove('drag-over');

            if (!this.draggedApplication || this.draggedApplication.status === newStatus) return;

            const oldStatus = this.draggedApplication.status;
            this.draggedApplication.status = newStatus;

            this.moveApplicationBetweenColumns(this.draggedApplication, oldStatus, newStatus);

            // TO DO: sync dengan database
            try {
                await fetch(`/company/applications/${this.draggedApplication.id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                });
            } catch (error) {
                console.error('Error updating status:', error);
            }

            this.draggedApplication = null;
        },

        // TO DO: implementasi bulk actions
        async bulkUpdateStatus(status) {
            // implementasi bulk update
            console.log('Bulk update to:', status, this.selectedApplications);
        },

        exportSelected() {
            // TO DO: implementasi export ke CSV
            console.log('Export selected:', this.selectedApplications);
        }
    }
}
</script>
@endpush
