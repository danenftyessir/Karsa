@extends('layouts.app')

@section('title', 'Temukan Talenta')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="talentBrowser()">

    {{-- hero section --}}
    <div class="relative h-48 bg-cover bg-center" style="background-image: url('{{ asset('deal-work-together.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white px-4">
            <h1 class="text-2xl md:text-3xl font-bold mb-2 fade-in-up" style="font-family: 'Space Grotesk', sans-serif;">
                Temukan Talenta Terbaik Untuk Tim Anda
            </h1>
            <p class="text-sm md:text-base text-gray-200 max-w-xl fade-in-up" style="animation-delay: 0.1s;">
                Jelajahi profil terverifikasi, sesuaikan dengan nilai Anda, dan temukan kecocokan sempurna dengan insight berbasis data.
            </p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- sidebar filters --}}
            <aside class="w-full lg:w-72 flex-shrink-0 fade-in-up" style="animation-delay: 0.15s;">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Filter</h2>

                    {{-- skills filter --}}
                    <div class="mb-6" x-data="{ open: true }">
                        <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                            <span class="font-semibold text-gray-800">Keahlian</span>
                            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="mt-3 space-y-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($availableSkills as $skill)
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 p-1 rounded transition-colors">
                                <input type="checkbox"
                                       x-model="filters.skills"
                                       value="{{ $skill }}"
                                       class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                <span class="text-sm text-gray-700">{{ $skill }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

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
                        <button @click="viewMode = 'leaderboard'"
                                :class="viewMode === 'leaderboard' ? 'bg-violet-600 text-white' : 'text-gray-600 hover:bg-gray-100'"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Leaderboard
                        </button>
                    </div>
                </div>

                {{-- grid view --}}
                <div x-show="viewMode === 'grid'"
                     class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <template x-for="(talent, index) in filteredTalents" :key="talent.id">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover-lift gpu-accelerate fade-in-up"
                             :style="'animation-delay: ' + (0.25 + index * 0.05) + 's'">

                            {{-- header dengan avatar dan bookmark --}}
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <img :src="'/'+talent.avatar"
                                             :alt="talent.name"
                                             class="w-12 h-12 rounded-full object-cover"
                                             onerror="this.src='https://ui-avatars.com/api/?name='+encodeURIComponent(this.alt)+'&background=6366F1&color=fff'">
                                        <span x-show="talent.online"
                                              class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 text-sm" x-text="talent.name"></h3>
                                        <p class="text-xs text-gray-500" x-text="talent.title"></p>
                                    </div>
                                </div>
                                <button class="text-gray-400 hover:text-violet-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                    </svg>
                                </button>
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

                {{-- list view --}}
                <div x-show="viewMode === 'list'" class="space-y-3">
                    <template x-for="(talent, index) in filteredTalents" :key="talent.id">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover-lift gpu-accelerate">
                            <div class="flex items-center gap-4">
                                <div class="relative flex-shrink-0">
                                    <img :src="'/'+talent.avatar"
                                         :alt="talent.name"
                                         class="w-14 h-14 rounded-full object-cover"
                                         onerror="this.src='https://ui-avatars.com/api/?name='+encodeURIComponent(this.alt)+'&background=6366F1&color=fff'">
                                    <span x-show="talent.online"
                                          class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-500 border-2 border-white rounded-full"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-gray-900" x-text="talent.name"></h3>
                                        <span x-show="talent.verified" class="flex items-center gap-1 text-xs text-green-600">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Terverifikasi
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500" x-text="talent.title"></p>
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        <template x-for="skill in talent.skills" :key="skill">
                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full" x-text="skill"></span>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-right text-sm">
                                        <p class="text-gray-500" x-text="talent.location"></p>
                                    </div>
                                    {{-- TO DO: implementasi route company.talents.show --}}
                                    <a :href="'/company/talents/' + talent.id"
                                       class="px-4 py-2 bg-violet-600 text-white text-sm font-semibold rounded-lg hover:bg-violet-700 transition-colors">
                                        Lihat Profil
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- leaderboard view --}}
                <div x-show="viewMode === 'leaderboard'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Peringkat</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Talenta</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keahlian</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lokasi</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(talent, index) in filteredTalents" :key="talent.id">
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold"
                                              :class="{
                                                  'bg-amber-100 text-amber-700': index === 0,
                                                  'bg-gray-200 text-gray-700': index === 1,
                                                  'bg-orange-100 text-orange-700': index === 2,
                                                  'bg-gray-100 text-gray-600': index > 2
                                              }"
                                              x-text="index + 1">
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <img :src="'/'+talent.avatar"
                                                 :alt="talent.name"
                                                 class="w-10 h-10 rounded-full object-cover"
                                                 onerror="this.src='https://ui-avatars.com/api/?name='+encodeURIComponent(this.alt)+'&background=6366F1&color=fff'">
                                            <div>
                                                <p class="font-semibold text-gray-900 text-sm" x-text="talent.name"></p>
                                                <p class="text-xs text-gray-500" x-text="talent.title"></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            <template x-for="skill in talent.skills.slice(0, 3)" :key="skill">
                                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full" x-text="skill"></span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600" x-text="talent.location"></td>
                                    <td class="px-4 py-3">
                                        {{-- TO DO: implementasi route company.talents.show --}}
                                        <a :href="'/company/talents/' + talent.id"
                                           class="text-violet-600 hover:text-violet-700 text-sm font-semibold">
                                            Lihat Profil
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
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
        filters: {
            skills: [],
            sdg_alignment: [],
            location: '',
            impact_score_min: 0,
            impact_score_max: 100,
            verified_only: false
        },
        talents: @json($talents),

        get filteredTalents() {
            let result = this.talents;

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

        applyFilters() {
            // TO DO: implementasi server-side filtering via AJAX
            // untuk saat ini filtering dilakukan client-side
            console.log('Filters applied:', this.filters);
        }
    }
}
</script>
@endpush
