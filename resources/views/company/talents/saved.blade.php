@extends('layouts.app')

@section('title', 'Talent Tersimpan - ' . $company->name)

@push('styles')
<style>
    /* optimisasi performa dengan GPU acceleration */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    html {
        scroll-behavior: smooth;
    }

    .talent-group {
        transform: translateZ(0);
        backface-visibility: hidden;
        will-change: transform;
    }

    .talent-item {
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                    border-color 0.15s ease-out;
        transform: translate3d(0, 0, 0);
        backface-visibility: hidden;
    }

    .talent-item:hover {
        transform: translate3d(0, -2px, 0);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .talent-item.dragging {
        opacity: 0.6;
        transform: rotate(2deg) scale(1.02);
        box-shadow: 0 15px 35px -10px rgba(0, 0, 0, 0.2);
        z-index: 1000;
    }

    .talent-item.selected {
        border-color: #3b82f6;
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
    }

    /* folder sidebar */
    .folder-item {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        border-left: 3px solid transparent;
    }

    .folder-item:hover {
        background-color: rgba(139, 92, 246, 0.08);
        transform: translateX(2px);
    }

    .folder-item.active {
        background-color: rgba(139, 92, 246, 0.12);
        border-left-color: #8b5cf6;
        font-weight: 500;
    }

    .folder-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 60%;
        background: linear-gradient(to bottom, transparent, #8b5cf6, transparent);
    }

    .folder-item.drag-over {
        background-color: rgba(139, 92, 246, 0.15);
        border: 2px dashed #8b5cf6;
        border-radius: 8px;
    }

    /* status badge colors */
    .status-new { background-color: #3b82f6; }
    .status-contacted { background-color: #eab308; }
    .status-interviewing { background-color: #a855f7; }
    .status-offered { background-color: #22c55e; }
    .status-declined { background-color: #ef4444; }

    /* rating stars */
    .star-rating .star {
        cursor: pointer;
        transition: transform 0.1s ease, color 0.1s ease;
    }

    .star-rating .star:hover {
        transform: scale(1.2);
    }

    /* modal transitions */
    .modal-content {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                    opacity 0.2s ease-out;
    }

    /* toast notification */
    .toast {
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .toast.show {
        transform: translateX(0);
    }

    /* comparison grid */
    .comparison-grid {
        display: grid;
        gap: 16px;
    }

    .comparison-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
    .comparison-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }

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
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" x-data="savedTalentsPage()" x-init="init()">

    <!-- toast notification -->
    <div x-show="toast.show" x-cloak
         class="fixed top-4 right-4 z-[9999] px-4 py-3 rounded-lg shadow-lg"
         :class="toast.type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
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
    <div class="relative bg-cover bg-center text-white py-12 overflow-hidden" style="background-image: url('{{ asset('company4.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold mb-2" style="font-family: 'Space Grotesk', sans-serif; color: white;">Talent Tersimpan</h1>
                    <p class="mt-1 text-base text-blue-100">
                        Total <span class="font-semibold text-white" x-text="totalTalents"></span> talent tersimpan
                        <span x-show="selectedTalents.length > 0" class="ml-2 text-yellow-300">
                            (<span x-text="selectedTalents.length"></span> dipilih)
                        </span>
                    </p>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <!-- compare button -->
                    <button x-show="selectedTalents.length >= 2 && selectedTalents.length <= 3"
                            @click="openComparisonModal()"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Bandingkan
                    </button>

                    <!-- bulk actions -->
                    <div x-show="selectedTalents.length > 0" x-cloak class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Aksi Massal
                            <span class="ml-2 bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full text-xs" x-text="selectedTalents.length"></span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                            <button @click="bulkMoveToFolder(); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Pindahkan Ke Folder
                            </button>
                            <button @click="bulkUpdateStatus(); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Ubah Status
                            </button>
                            <button @click="bulkRemove(); open = false" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                Hapus Dari Tersimpan
                            </button>
                            <hr class="my-1">
                            <button @click="exportSelected(); open = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                Export Ke CSV
                            </button>
                        </div>
                    </div>

                    <!-- create folder -->
                    <button @click="openCreateFolderModal()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        Folder Baru
                    </button>

                    <!-- export all -->
                    <button @click="exportAllTalents()"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Semua
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- main content with sidebar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex gap-6">

            <!-- folder sidebar -->
            <div class="w-64 flex-shrink-0">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden sticky top-6">
                    <div class="px-4 py-3 bg-gradient-to-r from-violet-50 to-purple-50 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            Folder
                        </h3>
                    </div>
                    <div class="p-2">
                        <!-- all talents -->
                        <button @click="selectFolder(null)"
                                class="folder-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left"
                                :class="{'active': selectedFolder === null}">
                            <svg class="w-5 h-5 transition-colors"
                                 :class="selectedFolder === null ? 'text-violet-600' : 'text-gray-400'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span class="text-sm text-gray-700 flex-1">Semua Talent</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                  :class="selectedFolder === null ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500'"
                                  x-text="totalTalents"></span>
                        </button>

                        <!-- folder list -->
                        <template x-for="folder in folders" :key="folder.id">
                            <div class="folder-item w-full flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer group"
                                 :class="{'active': selectedFolder === folder.id, 'drag-over': dragOverFolder === folder.id}"
                                 @click="selectFolder(folder.id)"
                                 @dragover.prevent="dragOverFolder = folder.id"
                                 @dragleave="dragOverFolder = null"
                                 @drop="dropToFolder($event, folder.id)">
                                <svg class="w-5 h-5 transition-colors"
                                     :class="selectedFolder === folder.id ? 'text-violet-600' : 'text-gray-400'"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                                <span class="text-sm text-gray-700 truncate flex-1" x-text="folder.name"></span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                      :class="selectedFolder === folder.id ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500'"
                                      x-text="getFolderCount(folder.id)"></span>
                                <button @click.stop="openEditFolderModal(folder)"
                                        class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-violet-600 transition-all p-1 hover:bg-violet-50 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- talent list -->
            <div class="flex-1">
                <!-- search bar -->
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" x-model="searchQuery" placeholder="Cari talent berdasarkan nama..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                        <div x-show="searchQuery" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button @click="searchQuery = ''" class="text-gray-400 hover:text-gray-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- talent grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="talent in filteredTalents" :key="talent.id">
                        <div class="talent-item bg-white border border-gray-200 rounded-xl p-5"
                             :class="{'selected': selectedTalents.includes(talent.id)}"
                             draggable="true"
                             @dragstart="dragStart($event, talent)"
                             @dragend="dragEnd($event)">

                            <!-- header dengan checkbox -->
                            <div class="flex items-start gap-3 mb-4">
                                <input type="checkbox"
                                       :value="talent.id"
                                       x-model="selectedTalents"
                                       class="mt-1 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                       @click.stop>

                                <template x-if="talent.avatar_url">
                                    <img :src="talent.avatar_url"
                                         :alt="talent.name"
                                         class="w-12 h-12 rounded-full object-cover flex-shrink-0">
                                </template>
                                <template x-if="!talent.avatar_url">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-lg font-bold" x-text="talent.name ? (talent.name.split(' ')[0]?.[0] + (talent.name.split(' ').slice(-1)[0]?.[0] || '')).toUpperCase() : '?'"></span>
                                    </div>
                                </template>

                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-gray-900 truncate" x-text="talent.name"></h4>
                                    <p class="text-sm text-gray-500 truncate" x-text="talent.title"></p>
                                </div>
                            </div>

                            <!-- status badge -->
                            <div class="flex items-center gap-2 mb-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-white"
                                      :class="'status-' + (talent.status || 'new')"
                                      x-text="getStatusLabel(talent.status || 'new')"></span>
                                <template x-if="talent.verified">
                                    <span class="inline-flex items-center text-xs text-green-600">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Terverifikasi
                                    </span>
                                </template>
                            </div>

                            <!-- rating -->
                            <div class="flex items-center gap-1 mb-3">
                                <template x-for="i in 5" :key="i">
                                    <svg class="w-4 h-4 cursor-pointer"
                                         :class="i <= (talent.rating || 0) ? 'text-yellow-400' : 'text-gray-300'"
                                         fill="currentColor" viewBox="0 0 20 20"
                                         @click.stop="setRating(talent, i)">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </template>
                            </div>

                            <!-- notes preview -->
                            <div x-show="talent.notes" class="mb-3 p-2 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-600 line-clamp-2" x-text="talent.notes"></p>
                            </div>

                            <!-- reminder indicator -->
                            <div x-show="talent.reminder_date" class="mb-3 flex items-center gap-1 text-xs text-orange-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Reminder: <span x-text="formatDate(talent.reminder_date)"></span></span>
                            </div>

                            <!-- action buttons -->
                            <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                                <a :href="'/profile/' + talent.username"
                                   class="flex-1 text-center px-3 py-1.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    Profil
                                </a>
                                <button @click.stop="openQuickActions(talent)"
                                        class="flex-1 text-center px-3 py-1.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                                    Aksi
                                </button>
                                <button @click.stop="removeTalent(talent)"
                                        class="px-3 py-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- empty state -->
                <div x-show="filteredTalents.length === 0" class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Talent</h3>
                    <p class="text-gray-500 mb-4">Mulai simpan talent favorit Anda dari halaman pencarian.</p>
                    <a href="{{ route('company.talents.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors">
                        Cari Talent
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- quick actions modal -->
    <div x-show="quickActionsOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="quickActionsOpen = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="quickActionsOpen = false"></div>

            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Aksi Talent</h3>
                    <button @click="quickActionsOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <template x-if="selectedTalent">
                    <div>
                        <!-- talent info -->
                        <div class="flex items-center gap-3 mb-6 p-4 bg-gray-50 rounded-lg">
                            <template x-if="selectedTalent.avatar_url">
                                <img :src="selectedTalent.avatar_url"
                                     class="w-14 h-14 rounded-full object-cover">
                            </template>
                            <template x-if="!selectedTalent.avatar_url">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                    <span class="text-white text-xl font-bold" x-text="selectedTalent.name ? (selectedTalent.name.split(' ')[0]?.[0] + (selectedTalent.name.split(' ').slice(-1)[0]?.[0] || '')).toUpperCase() : '?'"></span>
                                </div>
                            </template>
                            <div>
                                <p class="font-medium text-gray-900" x-text="selectedTalent.name"></p>
                                <p class="text-sm text-gray-500" x-text="selectedTalent.title"></p>
                            </div>
                        </div>

                        <!-- tabs -->
                        <div class="flex border-b border-gray-200 mb-4 overflow-x-auto">
                            <button @click="quickActionTab = 'status'"
                                    :class="quickActionTab === 'status' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px whitespace-nowrap">
                                Status
                            </button>
                            <button @click="quickActionTab = 'notes'"
                                    :class="quickActionTab === 'notes' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px whitespace-nowrap">
                                Catatan
                            </button>
                            <button @click="quickActionTab = 'folder'"
                                    :class="quickActionTab === 'folder' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px whitespace-nowrap">
                                Folder
                            </button>
                            <button @click="quickActionTab = 'reminder'"
                                    :class="quickActionTab === 'reminder' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px whitespace-nowrap">
                                Pengingat
                            </button>
                            <button @click="quickActionTab = 'contact'"
                                    :class="quickActionTab === 'contact' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500'"
                                    class="px-4 py-2 text-sm font-medium border-b-2 -mb-px whitespace-nowrap">
                                Kontak
                            </button>
                        </div>

                        <!-- status tab -->
                        <div x-show="quickActionTab === 'status'" class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Status Interaksi</label>
                            <div class="grid grid-cols-2 gap-2">
                                <template x-for="status in statusOptions" :key="status.value">
                                    <button @click="tempStatus = status.value"
                                            class="px-4 py-2 border rounded-lg text-sm font-medium transition-colors flex items-center gap-2"
                                            :class="tempStatus === status.value ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-300 text-gray-700 hover:bg-gray-50'">
                                        <span class="w-2 h-2 rounded-full" :class="'status-' + status.value"></span>
                                        <span x-text="status.label"></span>
                                    </button>
                                </template>
                            </div>
                            <button @click="saveStatus()"
                                    :disabled="isLoading"
                                    class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                                <span x-show="!isLoading">Simpan Status</span>
                                <span x-show="isLoading">Menyimpan...</span>
                            </button>
                        </div>

                        <!-- notes tab -->
                        <div x-show="quickActionTab === 'notes'" class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Catatan Internal</label>
                            <textarea x-model="tempNotes" rows="5"
                                      placeholder="Tambahkan catatan tentang talent ini..."
                                      class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                            <p class="text-xs text-gray-400">Catatan ini hanya terlihat oleh tim Anda</p>
                            <button @click="saveNotes()"
                                    :disabled="isLoading"
                                    class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                                <span x-show="!isLoading">Simpan Catatan</span>
                                <span x-show="isLoading">Menyimpan...</span>
                            </button>
                        </div>

                        <!-- folder tab -->
                        <div x-show="quickActionTab === 'folder'" class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Pindahkan Ke Folder</label>
                            <select x-model="tempFolder" class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Tidak Ada Folder</option>
                                <template x-for="folder in folders" :key="folder.id">
                                    <option :value="folder.id" x-text="folder.name"></option>
                                </template>
                            </select>
                            <button @click="saveTalentFolder()"
                                    :disabled="isLoading"
                                    class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                                <span x-show="!isLoading">Simpan</span>
                                <span x-show="isLoading">Menyimpan...</span>
                            </button>
                        </div>

                        <!-- reminder tab -->
                        <div x-show="quickActionTab === 'reminder'" class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Set Pengingat Follow-up</label>
                            <input type="date" x-model="tempReminderDate"
                                   class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500">
                            <textarea x-model="tempReminderNote" rows="3"
                                      placeholder="Catatan untuk pengingat..."
                                      class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                            <button @click="saveReminder()"
                                    :disabled="isLoading || !tempReminderDate"
                                    class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                                <span x-show="!isLoading">Set Pengingat</span>
                                <span x-show="isLoading">Menyimpan...</span>
                            </button>
                        </div>

                        <!-- contact tab -->
                        <div x-show="quickActionTab === 'contact'" class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">Hubungi Talent</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button @click="contactType = 'message'"
                                        :class="contactType === 'message' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-300 text-gray-700'"
                                        class="px-4 py-2 border rounded-lg text-sm font-medium transition-colors">
                                    Kirim Pesan
                                </button>
                                <button @click="contactType = 'interview_request'"
                                        :class="contactType === 'interview_request' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-300 text-gray-700'"
                                        class="px-4 py-2 border rounded-lg text-sm font-medium transition-colors">
                                    Undang Interview
                                </button>
                            </div>
                            <textarea x-model="contactMessage" rows="4"
                                      placeholder="Tulis pesan Anda..."
                                      class="w-full rounded-lg border-gray-300 text-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                            <button @click="sendContact()"
                                    :disabled="isLoading || !contactMessage.trim()"
                                    class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                                <span x-show="!isLoading">Kirim</span>
                                <span x-show="isLoading">Mengirim...</span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- create/edit folder modal -->
    <div x-show="folderModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="folderModalOpen = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="folderModalOpen = false"></div>

            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6"
                 x-transition>
                <h3 class="text-lg font-semibold text-gray-900 mb-4" x-text="editingFolder ? 'Edit Folder' : 'Buat Folder Baru'"></h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Folder</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                            </div>
                            <input type="text" x-model="folderName" placeholder="Contoh: Frontend Developer"
                                   @keydown.enter="saveFolderModal()"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border-gray-300 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 shadow-sm">
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button @click="folderModalOpen = false"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <template x-if="editingFolder">
                            <button @click="deleteFolder()"
                                    class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50">
                                Hapus
                            </button>
                        </template>
                        <button @click="saveFolderModal()"
                                :disabled="!folderName.trim()"
                                class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50">
                            Simpan
                        </button>
                    </div>
                </div>
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
                 x-transition>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Perbandingan Talent</h3>
                    <button @click="comparisonModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="comparison-grid" :class="'cols-' + comparisonTalents.length">
                    <template x-for="talent in comparisonTalents" :key="talent.id">
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-center mb-4 pb-4 border-b border-gray-100">
                                <template x-if="talent.avatar_url">
                                    <img :src="talent.avatar_url"
                                         class="w-20 h-20 rounded-full mx-auto mb-3 object-cover">
                                </template>
                                <template x-if="!talent.avatar_url">
                                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center mx-auto mb-3">
                                        <span class="text-white text-2xl font-bold" x-text="talent.name ? (talent.name.split(' ')[0]?.[0] + (talent.name.split(' ').slice(-1)[0]?.[0] || '')).toUpperCase() : '?'"></span>
                                    </div>
                                </template>
                                <h4 class="font-semibold text-gray-900" x-text="talent.name"></h4>
                                <p class="text-sm text-gray-500" x-text="talent.title"></p>
                            </div>

                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Rating</span>
                                    <div class="flex items-center gap-1">
                                        <template x-for="i in 5" :key="i">
                                            <svg class="w-4 h-4" :class="i <= (talent.rating || 0) ? 'text-yellow-400' : 'text-gray-300'" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Status</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-white"
                                          :class="'status-' + (talent.status || 'new')"
                                          x-text="getStatusLabel(talent.status || 'new')"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Disimpan</span>
                                    <span class="text-sm text-gray-900" x-text="formatDate(talent.saved_at)"></span>
                                </div>
                            </div>

                            <div x-show="talent.notes" class="mt-4 pt-4 border-t border-gray-100">
                                <p class="text-xs text-gray-500 mb-1">Catatan</p>
                                <p class="text-sm text-gray-700 line-clamp-3" x-text="talent.notes"></p>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <a :href="'/profile/' + talent.username"
                                   class="block w-full text-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm hover:bg-primary-700">
                                    Lihat Profil
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
function savedTalentsPage() {
    return {
        // state utama
        isLoading: false,
        searchQuery: '',
        selectedFolder: null,
        selectedTalents: [],
        dragOverFolder: null,
        draggedTalent: null,

        // toast
        toast: { show: false, message: '', type: 'success' },

        // modals
        quickActionsOpen: false,
        folderModalOpen: false,
        comparisonModalOpen: false,

        // quick actions state
        selectedTalent: null,
        quickActionTab: 'status',
        tempStatus: '',
        tempNotes: '',
        tempFolder: '',
        tempReminderDate: '',
        tempReminderNote: '',
        contactType: 'message',
        contactMessage: '',

        // folder state
        editingFolder: null,
        folderName: '',

        // comparison
        comparisonTalents: [],

        // data
        talents: [],
        folders: [],
        totalTalents: {{ $totalSavedTalents ?? 0 }},

        statusOptions: [
            { value: 'new', label: 'Baru' },
            { value: 'contacted', label: 'Dihubungi' },
            { value: 'interviewing', label: 'Interview' },
            { value: 'offered', label: 'Ditawari' },
            { value: 'declined', label: 'Ditolak' }
        ],

        init() {
            // parse data dari backend
            this.parseBackendData();
        },

        parseBackendData() {
            const groups = @json($savedTalentGroups ?? []);
            let allTalents = [];
            let folderList = [];

            groups.forEach((group, index) => {
                if (group.name && group.name !== 'Uncategorized') {
                    folderList.push({
                        id: group.id || 'folder-' + index,
                        name: group.name
                    });
                }

                if (group.talents) {
                    group.talents.forEach(talent => {
                        allTalents.push({
                            ...talent,
                            folder_id: group.name !== 'Uncategorized' ? (group.id || 'folder-' + index) : null,
                            status: talent.status || 'new',
                            rating: talent.rating || 0,
                            saved_at: talent.saved_at || new Date().toISOString()
                        });
                    });
                }
            });

            this.talents = allTalents;
            this.folders = folderList;
            this.totalTalents = allTalents.length;
        },

        get filteredTalents() {
            return this.talents.filter(talent => {
                // folder filter
                if (this.selectedFolder !== null && talent.folder_id !== this.selectedFolder) {
                    return false;
                }

                // search filter
                if (this.searchQuery) {
                    const query = this.searchQuery.toLowerCase();
                    return talent.name.toLowerCase().includes(query) ||
                           (talent.title && talent.title.toLowerCase().includes(query));
                }

                return true;
            });
        },

        selectFolder(folderId) {
            this.selectedFolder = folderId;
        },

        getFolderCount(folderId) {
            return this.talents.filter(t => t.folder_id === folderId).length;
        },

        getStatusLabel(status) {
            const labels = {
                'new': 'Baru',
                'contacted': 'Dihubungi',
                'interviewing': 'Interview',
                'offered': 'Ditawari',
                'declined': 'Ditolak'
            };
            return labels[status] || status;
        },

        formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        },

        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            setTimeout(() => { this.toast.show = false; }, 3000);
        },

        // drag and drop
        dragStart(event, talent) {
            this.draggedTalent = talent;
            event.target.classList.add('dragging');
        },

        dragEnd(event) {
            event.target.classList.remove('dragging');
            this.draggedTalent = null;
            this.dragOverFolder = null;
        },

        async dropToFolder(event, folderId) {
            this.dragOverFolder = null;
            if (!this.draggedTalent) return;

            const oldFolder = this.draggedTalent.folder_id;
            this.draggedTalent.folder_id = folderId;

            // sync ke backend
            try {
                await this.updateTalentFolder(this.draggedTalent.id, folderId);
                this.showToast('Talent dipindahkan ke folder');
            } catch (error) {
                this.draggedTalent.folder_id = oldFolder;
                this.showToast('Gagal memindahkan talent', 'error');
            }
        },

        // rating
        async setRating(talent, rating) {
            const oldRating = talent.rating;
            talent.rating = rating;

            try {
                // placeholder - would call API
                this.showToast('Rating disimpan');
            } catch (error) {
                talent.rating = oldRating;
            }
        },

        // quick actions
        openQuickActions(talent) {
            this.selectedTalent = talent;
            this.quickActionTab = 'status';
            this.tempStatus = talent.status || 'new';
            this.tempNotes = talent.notes || '';
            this.tempFolder = talent.folder_id || '';
            this.tempReminderDate = talent.reminder_date || '';
            this.tempReminderNote = talent.reminder_note || '';
            this.contactMessage = '';
            this.quickActionsOpen = true;
        },

        async saveStatus() {
            if (!this.selectedTalent) return;
            this.isLoading = true;

            try {
                this.selectedTalent.status = this.tempStatus;
                this.quickActionsOpen = false;
                this.showToast('Status berhasil disimpan');
            } catch (error) {
                this.showToast('Gagal menyimpan status', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async saveNotes() {
            if (!this.selectedTalent) return;
            this.isLoading = true;

            try {
                this.selectedTalent.notes = this.tempNotes;
                this.showToast('Catatan berhasil disimpan');
            } catch (error) {
                this.showToast('Gagal menyimpan catatan', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async saveTalentFolder() {
            if (!this.selectedTalent) return;
            this.isLoading = true;

            try {
                const oldFolder = this.selectedTalent.folder_id;
                this.selectedTalent.folder_id = this.tempFolder || null;

                await this.updateTalentFolder(this.selectedTalent.id, this.tempFolder);

                this.quickActionsOpen = false;
                this.showToast('Talent berhasil dipindahkan ke folder');
            } catch (error) {
                this.selectedTalent.folder_id = oldFolder;
                this.showToast('Gagal memindahkan talent', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async saveReminder() {
            if (!this.selectedTalent || !this.tempReminderDate) return;
            this.isLoading = true;

            try {
                this.selectedTalent.reminder_date = this.tempReminderDate;
                this.selectedTalent.reminder_note = this.tempReminderNote;
                this.quickActionsOpen = false;
                this.showToast('Pengingat berhasil diset');
            } catch (error) {
                this.showToast('Gagal menyimpan pengingat', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async sendContact() {
            if (!this.selectedTalent || !this.contactMessage.trim()) return;
            this.isLoading = true;

            try {
                await fetch(`/company/talents/${this.selectedTalent.id}/contact`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: this.contactMessage,
                        type: this.contactType
                    })
                });

                this.selectedTalent.status = 'contacted';
                this.quickActionsOpen = false;
                this.showToast('Pesan berhasil dikirim');
            } catch (error) {
                this.showToast('Gagal mengirim pesan', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        async updateTalentFolder(talentId, folderId) {
            // placeholder untuk API call
            console.log('Update folder:', talentId, folderId);
        },

        // folder management
        openCreateFolderModal() {
            this.editingFolder = null;
            this.folderName = '';
            this.folderModalOpen = true;
        },

        openEditFolderModal(folder) {
            this.editingFolder = folder;
            this.folderName = folder.name;
            this.folderModalOpen = true;
        },

        async saveFolderModal() {
            if (!this.folderName.trim()) return;

            if (this.editingFolder) {
                this.editingFolder.name = this.folderName;
                this.showToast('Folder berhasil diupdate');
            } else {
                const newFolder = {
                    id: 'folder-' + Date.now(),
                    name: this.folderName
                };
                this.folders.push(newFolder);
                this.showToast('Folder berhasil dibuat');
            }

            this.folderModalOpen = false;
        },

        async deleteFolder() {
            if (!this.editingFolder) return;

            // pindahkan talents ke uncategorized
            this.talents.forEach(t => {
                if (t.folder_id === this.editingFolder.id) {
                    t.folder_id = null;
                }
            });

            this.folders = this.folders.filter(f => f.id !== this.editingFolder.id);
            this.folderModalOpen = false;
            this.showToast('Folder berhasil dihapus');
        },

        // comparison
        openComparisonModal() {
            this.comparisonTalents = this.talents.filter(t =>
                this.selectedTalents.includes(t.id)
            );
            this.comparisonModalOpen = true;
        },

        // bulk actions
        bulkMoveToFolder() {
            // placeholder - would open folder selection modal
            this.showToast('Pilih folder untuk memindahkan talent');
        },

        bulkUpdateStatus() {
            // placeholder - would open status selection modal
            this.showToast('Pilih status baru');
        },

        async bulkRemove() {
            if (!confirm('Hapus ' + this.selectedTalents.length + ' talent dari tersimpan?')) return;

            this.talents = this.talents.filter(t => !this.selectedTalents.includes(t.id));
            this.totalTalents = this.talents.length;
            this.selectedTalents = [];
            this.showToast('Talent berhasil dihapus');
        },

        async removeTalent(talent) {
            if (!confirm('Hapus ' + talent.name + ' dari tersimpan?')) return;

            try {
                await fetch(`/company/talents/${talent.id}/toggle-save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                this.talents = this.talents.filter(t => t.id !== talent.id);
                this.totalTalents = this.talents.length;
                this.showToast('Talent dihapus dari tersimpan');
            } catch (error) {
                this.showToast('Gagal menghapus talent', 'error');
            }
        },

        // export
        exportSelected() {
            const ids = this.selectedTalents.join(',');
            window.location.href = `/company/talents/export-saved?ids=${ids}`;
        },

        exportAllTalents() {
            window.location.href = '/company/talents/export-saved';
        }
    }
}
</script>
@endpush
