@extends('layouts.app')

@section('title', 'Manajemen Lamaran - ' . $company->name)

@push('styles')
<style>
    /* optimasi performa dengan GPU acceleration */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* smooth scrolling native */
    html {
        scroll-behavior: smooth;
    }

    /* status badge colors */
    .status-new { background-color: #3b82f6; }
    .status-reviewing { background-color: #eab308; }
    .status-shortlisted { background-color: #f97316; }
    .status-interview { background-color: #a855f7; }
    .status-offer { background-color: #22c55e; }
    .status-hired { background-color: #10b981; }
    .status-rejected { background-color: #ef4444; }

    /* rating stars */
    .star-rating {
        display: inline-flex;
        gap: 2px;
    }

    .star-rating .star {
        cursor: pointer;
        transition: transform 0.1s ease, color 0.1s ease;
    }

    .star-rating .star:hover {
        transform: scale(1.2);
    }

    .star-rating .star.filled {
        color: #f59e0b;
    }

    .star-rating .star.empty {
        color: #d1d5db;
    }

    /* timeline styling */
    .timeline-item {
        position: relative;
        padding-left: 24px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 7px;
        top: 24px;
        bottom: -12px;
        width: 2px;
        background-color: #e5e7eb;
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-dot {
        position: absolute;
        left: 0;
        top: 6px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #3b82f6;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #e5e7eb;
    }

    /* comparison modal grid */
    .comparison-grid {
        display: grid;
        gap: 16px;
    }

    .comparison-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
    .comparison-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }

    /* modal transitions */
    .modal-backdrop {
        transition: opacity 0.2s ease-out;
    }

    .modal-content {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                    opacity 0.2s ease-out;
    }

    .modal-content.entering {
        transform: scale(0.95) translateY(10px);
        opacity: 0;
    }

    /* respek reduced motion */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }

        html {
            scroll-behavior: auto;
        }
    }

    /* skeleton loader */
    .skeleton {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: skeleton-loading 1.5s infinite;
    }

    @keyframes skeleton-loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* toast notification */
    .toast {
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .toast.show {
        transform: translateX(0);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" x-data="applicationsKanban()" x-init="init()">

    <!-- toast notification -->
    <div x-show="toast.show" x-cloak
         :class="'toast fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg ' + (toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white')"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-full"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform translate-x-full">
        <div class="flex items-center gap-2">
            <span x-text="toast.message"></span>
            <button @click="toast.show = false" class="ml-2 hover:opacity-75">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- header section -->
    <div class="relative bg-cover bg-center text-white py-12 overflow-hidden" style="background-image: url('{{ asset('company3.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>

        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                <!-- title dan stats -->
                <div>
                    <h1 class="text-3xl font-bold mb-2" style="font-family: 'Space Grotesk', sans-serif; color: white !important;">Manajemen Lamaran</h1>
                    <p class="mt-1 text-base text-blue-100">
                        Total <span class="font-semibold text-white" x-text="totalApplications"></span> lamaran aktif
                        <span x-show="selectedApplications.length > 0" class="ml-2 text-yellow-300">
                            (<span x-text="selectedApplications.length"></span> dipilih)
                        </span>
                    </p>
                </div>

                <!-- actions -->
                <div class="flex items-center gap-3 flex-wrap">

                    <!-- compare button -->
                    <button x-show="selectedApplications.length >= 2 && selectedApplications.length <= 3"
                            @click="openComparisonModal()"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Bandingkan Kandidat
                    </button>

                    <!-- bulk actions -->
                    <div x-show="selectedApplications.length > 0" x-cloak class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Aksi Massal
                            <span class="ml-2 bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full text-xs" x-text="selectedApplications.length"></span>
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <button @click="bulkUpdateStatus('shortlisted'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Shortlist Semua
                            </button>
                            <button @click="bulkUpdateStatus('interview'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Jadwalkan Interview
                            </button>
                            <button @click="bulkUpdateStatus('rejected'); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Tolak Semua
                            </button>
                            <hr class="my-1">
                            <button @click="exportSelected(); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Export Ke CSV
                            </button>
                        </div>
                    </div>

                    <!-- filter button -->
                    <button @click="showFilters = !showFilters"
                            :class="hasActiveFilters ? 'border-primary-500 text-primary-700' : 'border-gray-300 text-gray-700'"
                            class="inline-flex items-center px-4 py-2 border rounded-lg text-sm font-medium bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter
                        <span x-show="hasActiveFilters" class="ml-2 w-2 h-2 bg-primary-500 rounded-full"></span>
                    </button>
                </div>
            </div>

            <!-- filter panel -->
            <div x-show="showFilters" x-collapse x-cloak class="mt-4 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                        <select x-model="filters.position" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Semua Posisi</option>
                            @foreach($jobPostings as $job)
                                <option value="{{ $job->id }}">{{ $job->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Melamar</label>
                        <select x-model="filters.dateRange" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Semua Waktu</option>
                            <option value="today">Hari Ini</option>
                            <option value="week">Minggu Ini</option>
                            <option value="month">Bulan Ini</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nama</label>
                        <input type="text" x-model="filters.search" placeholder="Ketik nama kandidat..."
                               class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
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

    <!-- list view -->
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="application in allFilteredApplications" :key="application.id">
                        <tr class="hover:bg-gray-50 transition-colors duration-150"
                            :class="{'bg-blue-50': selectedApplications.includes(application.id)}">
                            <td class="px-6 py-4">
                                <input type="checkbox" :value="application.id" x-model="selectedApplications"
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <template x-if="application.avatar_url">
                                        <img :src="application.avatar_url"
                                             :alt="application.name"
                                             class="w-10 h-10 rounded-full object-cover">
                                    </template>
                                    <template x-if="!application.avatar_url">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                            <span class="text-white text-sm font-bold" x-text="application.name ? (application.name.split(' ')[0]?.[0] + (application.name.split(' ').slice(-1)[0]?.[0] || '')).toUpperCase() : '?'"></span>
                                        </div>
                                    </template>
                                    <div class="flex-1 min-w-0">
                                        <span class="font-medium text-gray-900" x-text="application.name"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="application.position"></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <template x-for="i in 5" :key="i">
                                        <svg class="w-4 h-4 cursor-pointer"
                                             :class="i <= (application.rating || 0) ? 'text-yellow-400' : 'text-gray-300'"
                                             fill="currentColor" viewBox="0 0 20 20"
                                             @click="setRating(application, i)">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </template>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="formatDate(application.applied_date)"></td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white"
                                      :class="'status-' + application.status"
                                      x-text="getStatusLabel(application.status)"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a :href="'/company/applications/' + application.id"
                                       class="text-sm font-medium text-primary-600 hover:text-primary-700">Profil</a>
                                    <button @click="openQuickActions(application)"
                                            class="text-sm font-medium text-gray-600 hover:text-gray-900">Aksi</button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- quick actions modal -->
    <div x-show="quickActionsOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="quickActionsOpen = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 modal-backdrop" @click="quickActionsOpen = false"></div>

            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6 modal-content"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Kandidat</h3>
                    <button @click="quickActionsOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <template x-if="selectedApplication">
                    <div>
                        <!-- kandidat info -->
                        <div class="flex items-center gap-3 mb-6 p-4 bg-gray-50 rounded-lg">
                            <template x-if="selectedApplication.avatar_url">
                                <img :src="selectedApplication.avatar_url"
                                     class="w-14 h-14 rounded-full object-cover">
                            </template>
                            <template x-if="!selectedApplication.avatar_url">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <span class="text-white text-xl font-bold" x-text="selectedApplication.name ? (selectedApplication.name.split(' ')[0]?.[0] + (selectedApplication.name.split(' ').slice(-1)[0]?.[0] || '')).toUpperCase() : '?'"></span>
                                </div>
                            </template>
                            <div>
                                <p class="font-medium text-gray-900" x-text="selectedApplication.name"></p>
                                <p class="text-sm text-gray-500" x-text="selectedApplication.position"></p>
                                <p class="text-xs text-gray-400" x-text="selectedApplication.email"></p>
                            </div>
                        </div>

                        <!-- tabs -->
                        <div class="flex border-b border-gray-200 mb-4">
                            <button @click="quickActionTab = 'status'"
                                    :class="quickActionTab === 'status' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px">
                                Status
                            </button>
                            <button @click="quickActionTab = 'rating'"
                                    :class="quickActionTab === 'rating' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px">
                                Rating
                            </button>
                            <button @click="quickActionTab = 'notes'"
                                    :class="quickActionTab === 'notes' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px">
                                Catatan
                            </button>
                            <button @click="quickActionTab = 'interview'"
                                    :class="quickActionTab === 'interview' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px">
                                Interview
                            </button>
                        </div>

                        <!-- status tab -->
                        <div x-show="quickActionTab === 'status'" class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Ubah Status</label>
                            <select x-model="newStatus" class="w-full rounded-lg border-gray-300 focus:ring-primary-500 focus:border-primary-500">
                                <option value="new">Lamaran Baru</option>
                                <option value="reviewing">Sedang Direview</option>
                                <option value="shortlisted">Terpilih</option>
                                <option value="interview">Interview</option>
                                <option value="offer">Penawaran</option>
                                <option value="hired">Diterima</option>
                                <option value="rejected">Ditolak</option>
                            </select>

                            <!-- email template checkbox -->
                            <div class="flex items-center gap-2">
                                <input type="checkbox" x-model="sendEmailNotification" id="sendEmail"
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label for="sendEmail" class="text-sm text-gray-700">Kirim notifikasi email ke kandidat</label>
                            </div>

                            <!-- rejection reason -->
                            <div x-show="newStatus === 'rejected'" class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Alasan Penolakan</label>
                                <textarea x-model="rejectionReason" rows="3"
                                          placeholder="Jelaskan alasan penolakan (opsional)..."
                                          class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                            </div>

                            <button @click="updateApplicationStatus()"
                                    :disabled="isLoading"
                                    class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isLoading">Simpan Status</span>
                                <span x-show="isLoading">Menyimpan...</span>
                            </button>
                        </div>

                        <!-- rating tab -->
                        <div x-show="quickActionTab === 'rating'" class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Beri Rating Kandidat</label>
                            <div class="flex items-center justify-center gap-2 py-4">
                                <template x-for="i in 5" :key="i">
                                    <button @click="tempRating = i"
                                            class="focus:outline-none transition-transform hover:scale-110">
                                        <svg class="w-10 h-10"
                                             :class="i <= tempRating ? 'text-yellow-400' : 'text-gray-300'"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <p class="text-center text-sm text-gray-500" x-text="getRatingLabel(tempRating)"></p>
                            <button @click="saveRating()"
                                    :disabled="isLoading || tempRating === 0"
                                    class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isLoading">Simpan Rating</span>
                                <span x-show="isLoading">Menyimpan...</span>
                            </button>
                        </div>

                        <!-- notes tab -->
                        <div x-show="quickActionTab === 'notes'" class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Catatan Internal</label>
                            <textarea x-model="tempNotes" rows="5"
                                      placeholder="Tambahkan catatan tentang kandidat ini..."
                                      class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                            <p class="text-xs text-gray-400">Catatan ini hanya terlihat oleh tim Anda</p>
                            <button @click="saveNotes()"
                                    :disabled="isLoading"
                                    class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isLoading">Simpan Catatan</span>
                                <span x-show="isLoading">Menyimpan...</span>
                            </button>
                        </div>

                        <!-- interview tab -->
                        <div x-show="quickActionTab === 'interview'" class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Jadwalkan Interview</label>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Tanggal</label>
                                    <input type="date" x-model="interviewDate"
                                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Waktu</label>
                                    <input type="time" x-model="interviewTime"
                                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Tipe Interview</label>
                                <select x-model="interviewType" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                                    <option value="online">Online (Video Call)</option>
                                    <option value="onsite">Onsite (Di Kantor)</option>
                                    <option value="phone">Phone Interview</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Catatan Interview</label>
                                <textarea x-model="interviewNotes" rows="3"
                                          placeholder="Link meeting, alamat, atau catatan lainnya..."
                                          class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" x-model="sendInterviewEmail" id="sendInterviewEmail"
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label for="sendInterviewEmail" class="text-sm text-gray-700">Kirim undangan ke kandidat</label>
                            </div>
                            <button @click="scheduleInterview()"
                                    :disabled="isLoading || !interviewDate || !interviewTime"
                                    class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!isLoading">Jadwalkan Interview</span>
                                <span x-show="isLoading">Menyimpan...</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- timeline modal -->
    <div x-show="timelineModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="timelineModalOpen = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="timelineModalOpen = false"></div>

            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Riwayat Aktivitas</h3>
                    <button @click="timelineModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <template x-if="selectedApplication">
                    <div>
                        <div class="flex items-center gap-3 mb-6 p-3 bg-gray-50 rounded-lg">
                            <template x-if="selectedApplication.avatar_url">
                                <img :src="selectedApplication.avatar_url"
                                     class="w-10 h-10 rounded-full object-cover">
                            </template>
                            <template x-if="!selectedApplication.avatar_url">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <span class="text-white text-sm font-bold" x-text="selectedApplication.name ? (selectedApplication.name.split(' ')[0]?.[0] + (selectedApplication.name.split(' ').slice(-1)[0]?.[0] || '')).toUpperCase() : '?'"></span>
                                </div>
                            </template>
                            <div>
                                <p class="font-medium text-gray-900" x-text="selectedApplication.name"></p>
                                <p class="text-sm text-gray-500" x-text="selectedApplication.position"></p>
                            </div>
                        </div>

                        <!-- timeline -->
                        <div class="space-y-4 max-h-80 overflow-y-auto">
                            <template x-for="(activity, index) in timelineActivities" :key="index">
                                <div class="timeline-item">
                                    <div class="timeline-dot" :style="'background-color: ' + activity.color"></div>
                                    <div class="pb-4">
                                        <p class="text-sm font-medium text-gray-900" x-text="activity.title"></p>
                                        <p class="text-xs text-gray-500" x-text="activity.description"></p>
                                        <p class="text-xs text-gray-400 mt-1" x-text="activity.date"></p>
                                    </div>
                                </div>
                            </template>

                            <div x-show="timelineActivities.length === 0" class="text-center py-8 text-gray-400">
                                <p class="text-sm">Belum ada riwayat aktivitas</p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- comparison modal -->
    <div x-show="comparisonModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="comparisonModalOpen = false">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-black/50" @click="comparisonModalOpen = false"></div>

            <div class="relative bg-white rounded-xl shadow-xl max-w-6xl w-full p-6 max-h-[90vh] overflow-y-auto"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100">

                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Perbandingan Kandidat</h3>
                    <button @click="comparisonModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="comparison-grid" :class="'cols-' + comparisonCandidates.length">
                    <template x-for="candidate in comparisonCandidates" :key="candidate.id">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <!-- header -->
                            <div class="text-center mb-4 pb-4 border-b border-gray-100">
                                <template x-if="candidate.avatar_url">
                                    <img :src="candidate.avatar_url"
                                         class="w-20 h-20 rounded-full mx-auto mb-3 object-cover">
                                </template>
                                <template x-if="!candidate.avatar_url">
                                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center mx-auto mb-3">
                                        <span class="text-white text-2xl font-bold" x-text="candidate.name ? (candidate.name.split(' ')[0]?.[0] + (candidate.name.split(' ').slice(-1)[0]?.[0] || '')).toUpperCase() : '?'"></span>
                                    </div>
                                </template>
                                <h4 class="font-semibold text-gray-900" x-text="candidate.name"></h4>
                                <p class="text-sm text-gray-500" x-text="candidate.position"></p>
                            </div>

                            <!-- stats -->
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Rating</span>
                                    <div class="flex items-center gap-1">
                                        <template x-for="i in 5" :key="i">
                                            <svg class="w-4 h-4" :class="i <= (candidate.rating || 0) ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Status</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-white"
                                          :class="'status-' + candidate.status"
                                          x-text="getStatusLabel(candidate.status)"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Tanggal Melamar</span>
                                    <span class="text-sm text-gray-900" x-text="formatDate(candidate.applied_date)"></span>
                                </div>
                                <div x-show="candidate.email">
                                    <span class="text-sm text-gray-500">Email</span>
                                    <p class="text-sm text-gray-900 truncate" x-text="candidate.email"></p>
                                </div>
                            </div>

                            <!-- notes preview -->
                            <div x-show="candidate.notes" class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Catatan</p>
                                <p class="text-sm text-gray-700 line-clamp-3" x-text="candidate.notes"></p>
                            </div>

                            <!-- action -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <a :href="'/company/applications/' + candidate.id"
                                   class="block w-full text-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm hover:bg-primary-700">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function applicationsKanban() {
    return {
        // state utama
        showFilters: false,
        selectedApplications: [],
        isLoading: false,

        // toast notification
        toast: {
            show: false,
            message: '',
            type: 'success'
        },

        // modal states
        quickActionsOpen: false,
        timelineModalOpen: false,
        comparisonModalOpen: false,

        // selected application untuk modal
        selectedApplication: null,
        quickActionTab: 'status',
        newStatus: '',
        sendEmailNotification: true,
        rejectionReason: '',

        // rating
        tempRating: 0,

        // notes
        tempNotes: '',

        // interview scheduling
        interviewDate: '',
        interviewTime: '',
        interviewType: 'online',
        interviewNotes: '',
        sendInterviewEmail: true,

        // timeline
        timelineActivities: [],

        // comparison
        comparisonCandidates: [],

        // filter
        filters: {
            position: '',
            dateRange: '',
            search: ''
        },

        // data dari backend
        columns: @json($applicationsByStatus ?? []),
        applications: [],
        totalApplications: {{ $stats['total'] ?? 0 }},

        // inisialisasi
        init() {
            // flatten applications dari columns
            this.flattenApplications();
        },

        flattenApplications() {
            let apps = [];
            Object.keys(this.columns).forEach(status => {
                const columnApps = this.columns[status];
                if (Array.isArray(columnApps)) {
                    columnApps.forEach(app => {
                        apps.push({
                            ...app,
                            id: app.id,
                            name: app.user?.name || 'Unknown',
                            email: app.user?.email || '',
                            position: app.job_posting?.title || app.jobPosting?.title || 'Unknown Position',
                            status: status,
                            applied_date: app.created_at,
                            avatar_url: app.user?.profile?.avatar_url || null,
                            rating: app.rating || 0,
                            notes: app.notes || ''
                        });
                    });
                }
            });
            this.applications = apps;
        },

        // computed untuk filter
        get hasActiveFilters() {
            return this.filters.position || this.filters.dateRange || this.filters.search;
        },

        get allFilteredApplications() {
            return this.applications.filter(app => this.matchesFilters(app));
        },

        matchesFilters(app) {
            if (this.filters.position && app.job_posting_id != this.filters.position) return false;
            if (this.filters.search && !app.name.toLowerCase().includes(this.filters.search.toLowerCase())) return false;
            if (this.filters.dateRange) {
                const appDate = new Date(app.applied_date);
                const now = new Date();
                if (this.filters.dateRange === 'today') {
                    if (appDate.toDateString() !== now.toDateString()) return false;
                } else if (this.filters.dateRange === 'week') {
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    if (appDate < weekAgo) return false;
                } else if (this.filters.dateRange === 'month') {
                    const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                    if (appDate < monthAgo) return false;
                }
            }
            return true;
        },

        getStatusLabel(status) {
            const labels = {
                'new': 'Baru',
                'reviewing': 'Direview',
                'shortlisted': 'Terpilih',
                'interview': 'Interview',
                'offer': 'Penawaran',
                'hired': 'Diterima',
                'rejected': 'Ditolak'
            };
            return labels[status] || status;
        },

        getRatingLabel(rating) {
            const labels = {
                0: 'Pilih rating',
                1: 'Sangat Kurang',
                2: 'Kurang',
                3: 'Cukup',
                4: 'Baik',
                5: 'Sangat Baik'
            };
            return labels[rating] || '';
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        },

        resetFilters() {
            this.filters = { position: '', dateRange: '', search: '' };
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedApplications = this.allFilteredApplications.map(a => a.id);
            } else {
                this.selectedApplications = [];
            }
        },

        // toast helper
        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => {
                this.toast.show = false;
            }, 3000);
        },

        // modal handlers
        openQuickActions(application) {
            this.selectedApplication = application;
            this.newStatus = application.status;
            this.tempRating = application.rating || 0;
            this.tempNotes = application.notes || '';
            this.quickActionTab = 'status';
            this.rejectionReason = '';
            this.interviewDate = '';
            this.interviewTime = '';
            this.interviewNotes = '';
            this.quickActionsOpen = true;
        },

        openTimelineModal(application) {
            this.selectedApplication = application;
            this.loadTimeline(application.id);
            this.timelineModalOpen = true;
        },

        openComparisonModal() {
            this.comparisonCandidates = this.applications.filter(app =>
                this.selectedApplications.includes(app.id)
            );
            this.comparisonModalOpen = true;
        },

        // API calls
        async updateApplicationStatus() {
            if (!this.selectedApplication) return;
            this.isLoading = true;

            try {
                const response = await fetch(`/company/applications/${this.selectedApplication.id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: this.newStatus,
                        send_email: this.sendEmailNotification,
                        rejection_reason: this.rejectionReason
                    })
                });

                if (response.ok) {
                    // update local state
                    const app = this.applications.find(a => a.id === this.selectedApplication.id);
                    if (app) {
                        app.status = this.newStatus;
                    }

                    this.quickActionsOpen = false;
                    this.showToast('Status berhasil diperbarui');
                } else {
                    this.showToast('Gagal memperbarui status', 'error');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                this.showToast('Terjadi kesalahan', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async saveRating() {
            if (!this.selectedApplication || this.tempRating === 0) return;
            this.isLoading = true;

            try {
                const response = await fetch(`/company/applications/${this.selectedApplication.id}/rating`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ rating: this.tempRating })
                });

                if (response.ok) {
                    const app = this.applications.find(a => a.id === this.selectedApplication.id);
                    if (app) {
                        app.rating = this.tempRating;
                    }
                    this.selectedApplication.rating = this.tempRating;
                    this.showToast('Rating berhasil disimpan');
                } else {
                    this.showToast('Gagal menyimpan rating', 'error');
                }
            } catch (error) {
                console.error('Error saving rating:', error);
                this.showToast('Terjadi kesalahan', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async setRating(application, rating) {
            try {
                const response = await fetch(`/company/applications/${application.id}/rating`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ rating: rating })
                });

                if (response.ok) {
                    application.rating = rating;
                    this.showToast('Rating berhasil disimpan');
                }
            } catch (error) {
                console.error('Error saving rating:', error);
            }
        },

        async saveNotes() {
            if (!this.selectedApplication) return;
            this.isLoading = true;

            try {
                const response = await fetch(`/company/applications/${this.selectedApplication.id}/notes`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ notes: this.tempNotes })
                });

                if (response.ok) {
                    const app = this.applications.find(a => a.id === this.selectedApplication.id);
                    if (app) {
                        app.notes = this.tempNotes;
                    }
                    this.selectedApplication.notes = this.tempNotes;
                    this.showToast('Catatan berhasil disimpan');
                } else {
                    this.showToast('Gagal menyimpan catatan', 'error');
                }
            } catch (error) {
                console.error('Error saving notes:', error);
                this.showToast('Terjadi kesalahan', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async scheduleInterview() {
            if (!this.selectedApplication || !this.interviewDate || !this.interviewTime) return;
            this.isLoading = true;

            try {
                // update status ke interview
                const response = await fetch(`/company/applications/${this.selectedApplication.id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: 'interview',
                        notes: `Interview dijadwalkan: ${this.interviewDate} ${this.interviewTime} (${this.interviewType}). ${this.interviewNotes}`,
                        send_email: this.sendInterviewEmail
                    })
                });

                if (response.ok) {
                    const app = this.applications.find(a => a.id === this.selectedApplication.id);
                    if (app) {
                        app.status = 'interview';
                    }

                    this.quickActionsOpen = false;
                    this.showToast('Interview berhasil dijadwalkan');
                } else {
                    this.showToast('Gagal menjadwalkan interview', 'error');
                }
            } catch (error) {
                console.error('Error scheduling interview:', error);
                this.showToast('Terjadi kesalahan', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async loadTimeline(applicationId) {
            // simulasi data timeline - seharusnya dari API
            this.timelineActivities = [
                {
                    title: 'Lamaran Diterima',
                    description: 'Kandidat mengirimkan lamaran',
                    date: this.formatDate(this.selectedApplication?.applied_date),
                    color: '#3b82f6'
                },
                {
                    title: 'Status Diperbarui',
                    description: 'Status diubah menjadi ' + this.getStatusLabel(this.selectedApplication?.status),
                    date: 'Baru saja',
                    color: '#22c55e'
                }
            ];
        },

        async bulkUpdateStatus(status) {
            if (this.selectedApplications.length === 0) return;
            this.isLoading = true;

            try {
                const response = await fetch('/company/applications/bulk-update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        application_ids: this.selectedApplications,
                        status: status
                    })
                });

                if (response.ok) {
                    // update local state
                    this.selectedApplications.forEach(id => {
                        const app = this.applications.find(a => a.id === id);
                        if (app) {
                            app.status = status;
                        }
                    });

                    this.selectedApplications = [];
                    this.showToast(`${this.selectedApplications.length} lamaran berhasil diperbarui`);
                } else {
                    this.showToast('Gagal memperbarui status', 'error');
                }
            } catch (error) {
                console.error('Error bulk updating:', error);
                this.showToast('Terjadi kesalahan', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        exportSelected() {
            const ids = this.selectedApplications.join(',');
            window.location.href = `/company/applications/export?ids=${ids}`;
        }
    }
}
</script>
@endpush
