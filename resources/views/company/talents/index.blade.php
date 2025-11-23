@extends('layouts.app')

@section('title', 'Temukan Talenta')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="talentBrowser()">

    {{-- hero section with gradient background and search --}}
    <div class="relative bg-gradient-to-br from-violet-600 via-purple-600 to-indigo-700 py-16">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-white mb-8">
                <h1 class="text-3xl md:text-4xl font-bold mb-3 fade-in-up" style="font-family: 'Space Grotesk', sans-serif;">
                    Temukan Talenta Terbaik Untuk Tim Anda
                </h1>
                <p class="text-base md:text-lg text-violet-100 max-w-2xl mx-auto fade-in-up" style="animation-delay: 0.1s;">
                    Jelajahi profil terverifikasi, sesuaikan dengan nilai Anda, dan temukan kecocokan sempurna
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="max-w-3xl mx-auto fade-in-up" style="animation-delay: 0.2s;">
                <div class="relative">
                    <input type="text"
                           x-model="searchQuery"
                           @input="handleSearch()"
                           placeholder="Cari mahasiswa berdasarkan nama, skill, atau universitas..."
                           class="w-full px-6 py-4 pl-14 pr-4 text-gray-900 placeholder-gray-500 bg-white rounded-2xl shadow-2xl focus:outline-none focus:ring-4 focus:ring-violet-300 transition-all">
                    <svg class="absolute left-5 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <div x-show="searchQuery"
                         @click="searchQuery = ''; handleSearch()"
                         class="absolute right-5 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400 hover:text-gray-600 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Quick Actions Panel --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6 fade-in-up gpu-accelerate">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Aksi Cepat</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                {{-- Save Current Search --}}
                <button @click="saveCurrentSearch()"
                        class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-violet-400 hover:bg-violet-50 transition-all group">
                    <div class="flex-shrink-0 w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center group-hover:bg-violet-200 transition-colors">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-gray-900">Simpan Pencarian</p>
                        <p class="text-xs text-gray-500">Filter saat ini</p>
                    </div>
                </button>

                {{-- Compare Talents --}}
                <button @click="toggleComparisonMode()"
                        :class="comparisonMode ? 'border-violet-400 bg-violet-50' : 'border-gray-200 hover:border-blue-400 hover:bg-blue-50'"
                        class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed transition-all group">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center transition-colors"
                         :class="comparisonMode ? 'bg-violet-200' : 'bg-blue-100 group-hover:bg-blue-200'">
                        <svg class="w-5 h-5" :class="comparisonMode ? 'text-violet-600' : 'text-blue-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-gray-900" x-text="comparisonMode ? 'Mode Aktif' : 'Bandingkan'"></p>
                        <p class="text-xs text-gray-500" x-text="selectedForComparison.length + ' dipilih'"></p>
                    </div>
                </button>

                {{-- Export List --}}
                <a href="{{ route('company.talents.export') }}"
                   class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-green-400 hover:bg-green-50 transition-all group">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-gray-900">Export CSV</p>
                        <p class="text-xs text-gray-500">Data talenta</p>
                    </div>
                </a>

                {{-- Browse Saved --}}
                <a href="{{ route('company.talents.saved') }}"
                   class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-amber-400 hover:bg-amber-50 transition-all group">
                    <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-gray-900">Talenta Tersimpan</p>
                        <p class="text-xs text-gray-500">Lihat koleksi</p>
                    </div>
                </a>
            </div>
        </div>

        {{-- Saved Searches Section --}}
        <div x-show="savedSearches.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 fade-in-up gpu-accelerate">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Pencarian Tersimpan</h3>
                <button @click="clearAllSavedSearches()" class="text-xs text-red-600 hover:text-red-700 font-medium">
                    Hapus Semua
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                <template x-for="(search, index) in savedSearches" :key="index">
                    <div class="group relative">
                        <button @click="loadSavedSearch(index)"
                                class="flex items-center gap-2 px-3 py-1.5 bg-violet-50 text-violet-700 rounded-lg text-xs font-medium hover:bg-violet-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            <span x-text="search.name"></span>
                        </button>
                        <button @click="removeSavedSearch(index)"
                                class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        {{-- Comparison Mode Banner --}}
        <div x-show="comparisonMode" class="bg-violet-50 border border-violet-200 rounded-xl p-4 mb-6 fade-in-up">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-violet-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-violet-900">Mode Perbandingan Aktif</p>
                        <p class="text-xs text-violet-700">Pilih hingga 3 talenta untuk dibandingkan (<span x-text="selectedForComparison.length"></span>/3 dipilih)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="compareSelected()"
                            :disabled="selectedForComparison.length < 2"
                            :class="selectedForComparison.length >= 2 ? 'bg-violet-600 hover:bg-violet-700' : 'bg-gray-300 cursor-not-allowed'"
                            class="px-4 py-2 text-white text-sm font-semibold rounded-lg transition-colors">
                        Bandingkan
                    </button>
                    <button @click="toggleComparisonMode()"
                            class="px-4 py-2 bg-white text-violet-700 text-sm font-semibold rounded-lg hover:bg-violet-100 transition-colors">
                        Batalkan
                    </button>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">

            {{-- sidebar filters --}}
            <aside class="w-full lg:w-72 flex-shrink-0 fade-in-up" style="animation-delay: 0.15s;">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Filter</h2>

                    {{-- SDG alignment filter --}}
                    <div class="mb-6" x-data="{ open: true }">
                        <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                            <span class="font-semibold text-gray-800">SDG Alignment</span>
                            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-3 space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($sdgOptions as $sdg)
                            <label class="flex items-start gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded transition-colors">
                                <input type="checkbox"
                                       x-model="filters.sdg_alignment"
                                       value="{{ $sdg['id'] }}"
                                       class="w-4 h-4 mt-0.5 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                <span class="text-sm text-gray-700 leading-tight">{{ $sdg['name'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- location filter --}}
                    <div class="mb-6" x-data="{ open: true }">
                        <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                            <span class="font-semibold text-gray-800">Lokasi</span>
                            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-3">
                            <input type="text"
                                   x-model="filters.location"
                                   placeholder="cth: Berlin, Germany"
                                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                        </div>
                    </div>

                    {{-- impact score filter --}}
                    <div class="mb-6" x-data="{ open: true }">
                        <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                            <span class="font-semibold text-gray-800">Impact Score</span>
                            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-3">
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-2">
                                <span>Min: <span x-text="filters.impact_score_min"></span></span>
                                <span>Max: <span x-text="filters.impact_score_max"></span></span>
                            </div>
                            <input type="range"
                                   x-model="filters.impact_score_min"
                                   min="0" max="100"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-violet-600">
                        </div>
                    </div>

                    {{-- verification filter --}}
                    <div class="mb-4" x-data="{ open: true }">
                        <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                            <span class="font-semibold text-gray-800">Verifikasi</span>
                            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox"
                                       x-model="filters.verified_only"
                                       class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                <span class="text-sm text-gray-700">Tampilkan Hanya Talenta Terverifikasi</span>
                            </label>
                        </div>
                    </div>

                    {{-- apply filter button --}}
                    <button @click="applyFilters()"
                            class="w-full py-2.5 bg-violet-600 text-white text-sm font-semibold rounded-lg hover:bg-violet-700 transition-colors">
                        Terapkan Filter
                    </button>
                </div>
            </aside>

            {{-- main content --}}
            <main class="flex-1">
                {{-- header dengan view toggle --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 fade-in-up" style="animation-delay: 0.2s;">
                    <h2 class="text-lg font-bold text-gray-900">
                        <span x-text="filteredTalents.length"></span> Talenta Ditemukan
                    </h2>

                    <div class="flex items-center gap-2 bg-white rounded-lg p-1 shadow-sm border border-gray-100">
                        <button @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-violet-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            Grid
                        </button>
                        <button @click="viewMode = 'list'"
                                :class="viewMode === 'list' ? 'bg-violet-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            List
                        </button>
                    </div>
                </div>

                {{-- empty state --}}
                <div x-show="filteredTalents.length === 0" class="text-center py-16 fade-in-up">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Talenta Ditemukan</h3>
                    <p class="text-gray-500 mb-6">Coba ubah filter pencarian atau hapus beberapa kriteria untuk melihat lebih banyak hasil.</p>
                    <button @click="resetFilters()"
                            class="px-6 py-2.5 bg-violet-600 text-white font-semibold rounded-lg hover:bg-violet-700 transition-colors">
                        Reset Filter
                    </button>
                </div>

                {{-- grid view --}}
                <div x-show="viewMode === 'grid' && paginatedTalents.length > 0"
                     class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <template x-for="(talent, index) in paginatedTalents" :key="talent.id">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover-lift gpu-accelerate fade-in-up"
                             :style="'animation-delay: ' + (0.25 + index * 0.05) + 's'">

                            {{-- header dengan avatar dan bookmark --}}
                            <div class="flex items-start justify-between mb-3">
                                <a :href="'/profile/' + talent.username" class="flex items-center gap-3 flex-1 min-w-0 hover:opacity-80 transition-opacity">
                                    <div class="relative shrink-0">
                                        <img :src="'/'+talent.avatar"
                                             :alt="talent.name"
                                             class="w-12 h-12 rounded-full object-cover"
                                             onerror="this.src='https://ui-avatars.com/api/?name='+encodeURIComponent(this.alt)+'&background=6366F1&color=fff'">
                                        <span x-show="talent.online"
                                              class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-sm truncate" x-text="talent.name"></h3>
                                        <p class="text-xs text-gray-500 truncate" x-text="talent.title"></p>
                                    </div>
                                </a>
                                <div class="flex items-center gap-1 shrink-0">
                                    {{-- Comparison checkbox --}}
                                    <div x-show="comparisonMode">
                                        <input type="checkbox"
                                               :checked="isSelectedForComparison(talent.id)"
                                               @change="toggleTalentSelection(talent.id)"
                                               :disabled="!isSelectedForComparison(talent.id) && selectedForComparison.length >= 3"
                                               class="w-5 h-5 text-violet-600 border-gray-300 rounded focus:ring-violet-500 cursor-pointer">
                                    </div>
                                    {{-- Bookmark button --}}
                                    <button @click="toggleSave(talent.id)"
                                            x-show="!comparisonMode"
                                            :class="isSaved(talent.id) ? 'text-amber-500' : 'text-gray-400 hover:text-amber-500'"
                                            class="transition-colors">
                                        <svg class="w-5 h-5" :fill="isSaved(talent.id) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- verified badge --}}
                            <div x-show="talent.verified" class="flex items-center gap-1 mb-3">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-xs text-green-600 font-medium">Terverifikasi</span>
                            </div>

                            {{-- skills --}}
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                <template x-for="skill in talent.skills.slice(0, 4)" :key="skill">
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full" x-text="skill"></span>
                                </template>
                            </div>

                            {{-- SDG badges --}}
                            <div class="space-y-1.5 mb-3">
                                <template x-for="sdg in talent.sdg_badges" :key="sdg.id">
                                    <div class="px-2 py-1 rounded text-xs font-medium truncate"
                                         :class="{
                                             'bg-orange-100 text-orange-700': sdg.color === 'orange',
                                             'bg-amber-100 text-amber-700': sdg.color === 'amber',
                                             'bg-yellow-100 text-yellow-700': sdg.color === 'yellow',
                                             'bg-blue-100 text-blue-700': sdg.color === 'blue',
                                             'bg-red-100 text-red-700': sdg.color === 'red',
                                             'bg-pink-100 text-pink-700': sdg.color === 'pink',
                                         }"
                                         x-text="sdg.name">
                                    </div>
                                </template>
                            </div>

                            {{-- location --}}
                            <div class="flex items-center gap-1 text-xs text-gray-500 mb-3">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span x-text="talent.location"></span>
                            </div>

                            {{-- stats --}}
                            <div class="grid grid-cols-2 gap-2 text-xs border-t border-gray-100 pt-3">
                                <div x-show="talent.projects_completed">
                                    <p class="text-gray-500">Proyek Selesai</p>
                                    <p class="font-semibold text-gray-900" x-text="talent.projects_completed"></p>
                                </div>
                                <div x-show="talent.success_rate">
                                    <p class="text-gray-500">Tingkat Sukses</p>
                                    <p class="font-semibold text-gray-900" x-text="talent.success_rate + '%'"></p>
                                </div>
                                <div x-show="talent.algorithms_deployed">
                                    <p class="text-gray-500">Algoritma Deploy</p>
                                    <p class="font-semibold text-gray-900" x-text="talent.algorithms_deployed"></p>
                                </div>
                                <div x-show="talent.impact_score">
                                    <p class="text-gray-500">Impact Score</p>
                                    <p class="font-semibold text-gray-900" x-text="talent.impact_score"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Modern Pagination with Page Numbers --}}
                <div x-show="filteredTalents.length > 0" class="mt-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            {{-- Results Info --}}
                            <div class="text-sm text-gray-600">
                                Menampilkan
                                <span class="font-semibold text-gray-900" x-text="((currentPage - 1) * perPage) + 1"></span>
                                -
                                <span class="font-semibold text-gray-900" x-text="Math.min(currentPage * perPage, filteredTalents.length)"></span>
                                dari
                                <span class="font-semibold text-gray-900" x-text="filteredTalents.length"></span>
                                talenta
                            </div>

                            {{-- Page Numbers --}}
                            <div class="flex items-center gap-2">
                                {{-- Previous Button --}}
                                <button @click="goToPage(currentPage - 1)"
                                        :disabled="currentPage === 1"
                                        :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-violet-50'"
                                        class="p-2 rounded-lg border border-gray-200 transition-colors">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>

                                {{-- Page Numbers --}}
                                <template x-for="page in visiblePages" :key="page">
                                    <button @click="page !== '...' && goToPage(page)"
                                            :class="{
                                                'bg-violet-600 text-white font-semibold': page === currentPage,
                                                'text-gray-700 hover:bg-gray-50': page !== currentPage && page !== '...',
                                                'cursor-default': page === '...'
                                            }"
                                            class="min-w-[40px] h-10 px-3 rounded-lg border border-gray-200 text-sm transition-colors"
                                            x-text="page">
                                    </button>
                                </template>

                                {{-- Next Button --}}
                                <button @click="goToPage(currentPage + 1)"
                                        :disabled="currentPage === totalPages"
                                        :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-violet-50'"
                                        class="p-2 rounded-lg border border-gray-200 transition-colors">
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Per Page Selector --}}
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-600 whitespace-nowrap">Per halaman:</label>
                                <select x-model="perPage" @change="perPageChanged()"
                                        class="px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent bg-white">
                                    <option value="8">8</option>
                                    <option value="12">12</option>
                                    <option value="16">16</option>
                                    <option value="24">24</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- list view with improved styling --}}
                <div x-show="viewMode === 'list' && paginatedTalents.length > 0" class="space-y-4">
                    <template x-for="(talent, index) in paginatedTalents" :key="talent.id">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover-lift gpu-accelerate transition-all hover:shadow-md"
                             :style="'animation-delay: ' + (0.25 + index * 0.03) + 's'">
                            <div class="p-5">
                                <div class="flex items-start gap-5">
                                    {{-- Avatar Section --}}
                                    <div class="relative flex-shrink-0">
                                        <img :src="'/'+talent.avatar"
                                             :alt="talent.name"
                                             class="w-20 h-20 rounded-xl object-cover ring-2 ring-gray-100"
                                             onerror="this.src='https://ui-avatars.com/api/?name='+encodeURIComponent(this.alt)+'&background=6366F1&color=fff'">
                                        <span x-show="talent.online"
                                              class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-3 border-white rounded-full"></span>
                                    </div>

                                    {{-- Info Section --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <h3 class="text-lg font-bold text-gray-900" x-text="talent.name"></h3>
                                                    <span x-show="talent.verified" class="flex items-center gap-1 px-2 py-0.5 bg-green-50 text-green-700 text-xs font-medium rounded-full">
                                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Terverifikasi
                                                    </span>
                                                </div>
                                                <p class="text-sm font-medium text-violet-600 mb-1" x-text="talent.title"></p>
                                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        </svg>
                                                        <span x-text="talent.location"></span>
                                                    </span>
                                                    <span class="flex items-center gap-1" x-show="talent.university">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                        <span x-text="talent.university"></span>
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Comparison Checkbox --}}
                                            <div x-show="comparisonMode" class="ml-4">
                                                <input type="checkbox"
                                                       :checked="isSelectedForComparison(talent.id)"
                                                       @change="toggleTalentSelection(talent.id)"
                                                       :disabled="!isSelectedForComparison(talent.id) && selectedForComparison.length >= 3"
                                                       class="w-5 h-5 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                            </div>
                                        </div>

                                        {{-- Skills --}}
                                        <div class="flex flex-wrap gap-2 mb-3" x-show="talent.skills && talent.skills.length > 0">
                                            <template x-for="skill in talent.skills.slice(0, 5)" :key="skill">
                                                <span class="px-3 py-1 bg-violet-50 text-violet-700 text-xs font-medium rounded-full border border-violet-100" x-text="skill"></span>
                                            </template>
                                            <span x-show="talent.skills.length > 5" class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                                                +<span x-text="talent.skills.length - 5"></span> lainnya
                                            </span>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                                            <a :href="'/profile/' + talent.username"
                                               class="px-4 py-2 bg-violet-600 text-white text-sm font-semibold rounded-lg hover:bg-violet-700 transition-colors">
                                                Lihat Profil
                                            </a>
                                            <button @click="toggleSave(talent.id)"
                                                    :class="savedTalents.includes(talent.id) ? 'bg-amber-50 text-amber-600 border-amber-200' : 'bg-white text-gray-600 border-gray-200'"
                                                    class="px-4 py-2 border text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                                <span x-show="!savedTalents.includes(talent.id)">Simpan</span>
                                                <span x-show="savedTalents.includes(talent.id)">Tersimpan</span>
                                            </button>
                                            <button class="ml-auto p-2 text-gray-400 hover:text-gray-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

            </main>
        </div>
    </div>
</div>

<style>
/* animasi fade in up */
.fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translate3d(0, 20px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

/* GPU acceleration untuk performa smooth */
.gpu-accelerate {
    transform: translateZ(0);
    will-change: transform, opacity;
    backface-visibility: hidden;
}

/* hover effect untuk cards */
.hover-lift {
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
}

/* custom scrollbar untuk filter sidebar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #F3F4F6;
    border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #D1D5DB;
    border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF;
}

/* reduced motion support untuk aksesibilitas */
@media (prefers-reduced-motion: reduce) {
    .fade-in-up {
        animation: none;
        opacity: 1;
    }

    .hover-lift:hover {
        transform: none;
    }
}
</style>
@endsection

@push('scripts')
<script>
function talentBrowser() {
    return {
        viewMode: '{{ $viewMode }}',
        searchQuery: '',
        filters: {
            skills: [],
            sdg_alignment: [],
            location: '',
            impact_score_min: 0,
            impact_score_max: 100,
            verified_only: false
        },
        talents: @json($talents->items()),
        savedTalents: [], // IDs of saved talents
        savedSearches: [],
        comparisonMode: false,
        selectedForComparison: [],
        loading: false,
        perPage: 12,
        currentPage: 1,
        hasMore: {{ $talents->hasMorePages() ? 'true' : 'false' }},

        init() {
            // Load saved searches dari localStorage
            const saved = localStorage.getItem('company_saved_searches');
            if (saved) {
                this.savedSearches = JSON.parse(saved);
            }

            // Load saved talents dari localStorage
            const savedTalents = localStorage.getItem('company_saved_talents');
            if (savedTalents) {
                this.savedTalents = JSON.parse(savedTalents);
            }
        },

        get filteredTalents() {
            let result = this.talents;

            // filter berdasarkan search query
            if (this.searchQuery.trim()) {
                const query = this.searchQuery.toLowerCase().trim();
                result = result.filter(talent => {
                    return (
                        talent.name.toLowerCase().includes(query) ||
                        talent.title.toLowerCase().includes(query) ||
                        (talent.university && talent.university.toLowerCase().includes(query)) ||
                        (talent.skills && talent.skills.some(skill =>
                            skill.toLowerCase().includes(query)
                        ))
                    );
                });
            }

            // filter berdasarkan skills
            if (this.filters.skills.length > 0) {
                result = result.filter(talent =>
                    this.filters.skills.some(skill => talent.skills.includes(skill))
                );
            }

            // filter berdasarkan SDG alignment
            if (this.filters.sdg_alignment.length > 0) {
                result = result.filter(talent =>
                    talent.sdg_badges.some(sdg =>
                        this.filters.sdg_alignment.includes(String(sdg.id))
                    )
                );
            }

            // filter berdasarkan lokasi
            if (this.filters.location) {
                const locationLower = this.filters.location.toLowerCase();
                result = result.filter(talent =>
                    talent.location.toLowerCase().includes(locationLower)
                );
            }

            // filter berdasarkan verified only
            if (this.filters.verified_only) {
                result = result.filter(talent => talent.verified);
            }

            return result;
        },

        get totalPages() {
            return Math.ceil(this.filteredTalents.length / parseInt(this.perPage));
        },

        get paginatedTalents() {
            const start = (this.currentPage - 1) * parseInt(this.perPage);
            const end = start + parseInt(this.perPage);
            return this.filteredTalents.slice(start, end);
        },

        get visiblePages() {
            const total = this.totalPages;
            const current = this.currentPage;
            const pages = [];

            if (total <= 7) {
                // Show all pages if total is 7 or less
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                // Always show first page
                pages.push(1);

                if (current > 3) {
                    pages.push('...');
                }

                // Show pages around current page
                const start = Math.max(2, current - 1);
                const end = Math.min(total - 1, current + 1);

                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }

                if (current < total - 2) {
                    pages.push('...');
                }

                // Always show last page
                pages.push(total);
            }

            return pages;
        },

        handleSearch() {
            // Reset to first page when searching
            this.currentPage = 1;
        },

        goToPage(page) {
            if (page < 1 || page > this.totalPages) return;
            this.currentPage = page;
            // Scroll to top of results
            window.scrollTo({ top: 300, behavior: 'smooth' });
        },

        applyFilters() {
            this.currentPage = 1; // Reset to first page when applying filters
            console.log('Filters applied:', this.filters);
        },

        // Saved Searches functionality
        saveCurrentSearch() {
            const name = prompt('Beri nama untuk pencarian ini:');
            if (!name) return;

            const search = {
                name: name,
                filters: JSON.parse(JSON.stringify(this.filters))
            };

            this.savedSearches.push(search);
            localStorage.setItem('company_saved_searches', JSON.stringify(this.savedSearches));

            window.showNotification('Pencarian berhasil disimpan!', 'success');
        },

        loadSavedSearch(index) {
            const search = this.savedSearches[index];
            if (search) {
                this.filters = JSON.parse(JSON.stringify(search.filters));
                window.showNotification('Pencarian dimuat: ' + search.name, 'info');
            }
        },

        removeSavedSearch(index) {
            this.savedSearches.splice(index, 1);
            localStorage.setItem('company_saved_searches', JSON.stringify(this.savedSearches));
            window.showNotification('Pencarian dihapus', 'info');
        },

        clearAllSavedSearches() {
            if (confirm('Hapus semua pencarian tersimpan?')) {
                this.savedSearches = [];
                localStorage.removeItem('company_saved_searches');
                window.showNotification('Semua pencarian dihapus', 'info');
            }
        },

        resetFilters() {
            this.filters = {
                skills: [],
                sdg_alignment: [],
                location: '',
                impact_score_min: 0,
                impact_score_max: 100,
                verified_only: false
            };
            this.searchQuery = ''; // Reset search query
            this.currentPage = 1; // Reset to first page
            window.showNotification('Filter dan pencarian direset', 'info');
        },

        // Comparison Mode functionality
        toggleComparisonMode() {
            this.comparisonMode = !this.comparisonMode;
            if (!this.comparisonMode) {
                this.selectedForComparison = [];
            }
        },

        toggleTalentSelection(talentId) {
            const index = this.selectedForComparison.indexOf(talentId);
            if (index > -1) {
                this.selectedForComparison.splice(index, 1);
            } else if (this.selectedForComparison.length < 3) {
                this.selectedForComparison.push(talentId);
            }
        },

        isSelectedForComparison(talentId) {
            return this.selectedForComparison.includes(talentId);
        },

        compareSelected() {
            if (this.selectedForComparison.length < 2) {
                window.showNotification('Pilih minimal 2 talenta untuk dibandingkan', 'warning');
                return;
            }

            // Redirect ke comparison page dengan query params
            const ids = this.selectedForComparison.join(',');
            window.location.href = `/company/talents/compare?ids=${ids}`;
        },

        // Save/Bookmark functionality
        async toggleSave(talentId) {
            try {
                const response = await fetch(`/company/talents/${talentId}/toggle-save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    if (data.action === 'saved') {
                        this.savedTalents.push(talentId);
                        window.showNotification('Talenta berhasil disimpan!', 'success');
                    } else {
                        const index = this.savedTalents.indexOf(talentId);
                        if (index > -1) {
                            this.savedTalents.splice(index, 1);
                        }
                        window.showNotification('Talenta dihapus dari tersimpan', 'info');
                    }

                    // Update localStorage
                    localStorage.setItem('company_saved_talents', JSON.stringify(this.savedTalents));
                }
            } catch (error) {
                console.error('Error toggling save:', error);
                window.showNotification('Terjadi kesalahan', 'error');
            }
        },

        isSaved(talentId) {
            return this.savedTalents.includes(talentId);
        },

        // Pagination handler
        perPageChanged() {
            this.currentPage = 1; // Reset to first page
            console.log('Per page changed to:', this.perPage);
            window.showNotification(`Menampilkan ${this.perPage} talenta per halaman`, 'info');
        },

        // Load More functionality (kept for compatibility if needed later)
        async loadMore() {
            if (this.loading || !this.hasMore) return;

            this.loading = true;
            const nextPage = this.currentPage + 1;

            try {
                const response = await fetch(`{{ route('company.talents.index') }}?page=${nextPage}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.talents && data.talents.length > 0) {
                    // Append new talents to existing array
                    this.talents = [...this.talents, ...data.talents];
                    this.currentPage = nextPage;
                    this.hasMore = data.hasMore;

                    window.showNotification(`${data.talents.length} talenta berhasil dimuat`, 'success');
                } else {
                    this.hasMore = false;
                    window.showNotification('Tidak ada talenta lagi', 'info');
                }
            } catch (error) {
                console.error('Error loading more:', error);
                window.showNotification('Terjadi kesalahan saat memuat data', 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
