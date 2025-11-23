{{-- resources/views/student/jobs/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Lowongan Kerja')

@push('styles')
{{-- Import Google Font - Space Grotesk for Hero --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

<style>
    /* gpu acceleration untuk performa smooth */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Hero section style mirip dashboard dan browse-problems */
    .marketplace-hero-jobs {
        position: relative;
        background-image:
            linear-gradient(135deg, rgba(99, 102, 241, 0.35) 0%, rgba(129, 140, 248, 0.30) 50%, rgba(156, 163, 175, 0.25) 100%),
            url('{{ asset('magang-preview.jpg') }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 480px;
    }

    .hero-title-jobs {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .text-shadow-strong {
        text-shadow:
            0 2px 4px rgba(0, 0, 0, 0.4),
            0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .jobs-fade-in {
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* gradient background */
    .gradient-mesh-bg {
        background-color: #ffffff;
        background-image:
            radial-gradient(at 15% 15%, rgba(59, 130, 246, 0.08) 0px, transparent 50%),
            radial-gradient(at 85% 20%, rgba(147, 51, 234, 0.08) 0px, transparent 50%),
            radial-gradient(at 25% 75%, rgba(99, 102, 241, 0.08) 0px, transparent 50%);
    }

    /* job card dengan gpu acceleration */
    .job-card {
        transform: translate3d(0, 0, 0);
        will-change: transform, box-shadow;
        backface-visibility: hidden;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .job-card:hover {
        transform: translate3d(0, -4px, 0);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
    }

    /* smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }

    /* reduced motion untuk aksesibilitas */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        html {
            scroll-behavior: auto;
        }
    }

    /* fade in animation */
    .fade-in {
        animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* filter sidebar - sticky positioning */
    .filter-sidebar {
        position: sticky;
        top: 2rem;
        max-height: calc(100vh - 4rem);
        overflow-y: auto;
    }

    /* filter sidebar scrollbar styling */
    .filter-sidebar::-webkit-scrollbar {
        width: 6px;
    }

    .filter-sidebar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .filter-sidebar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .filter-sidebar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* filter section */
    .filter-section {
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }

    .filter-section:last-child {
        border-bottom: none;
    }

    /* checkbox custom style */
    .filter-checkbox {
        width: 1.125rem;
        height: 1.125rem;
        border-radius: 0.25rem;
        border: 2px solid #d1d5db;
        transition: all 0.2s ease;
    }

    .filter-checkbox:checked {
        background-color: #3b82f6;
        border-color: #3b82f6;
    }

    /* job type badge */
    .job-type-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .job-type-full_time { background-color: #dcfce7; color: #166534; }
    .job-type-part_time { background-color: #dbeafe; color: #1e40af; }
    .job-type-contract { background-color: #fef3c7; color: #92400e; }
    .job-type-internship { background-color: #f3e8ff; color: #7c3aed; }
    .job-type-freelance { background-color: #fce7f3; color: #be185d; }
</style>
@endpush

@section('content')
<div class="min-h-screen gradient-mesh-bg" x-data="jobsPage()">

    {{-- marketplace-style hero section mirip dashboard dan browse-problems --}}
    <section class="marketplace-hero-jobs text-white relative flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10 w-full">
            <div class="max-w-4xl mx-auto text-center">
                <div class="jobs-fade-in">
                    {{-- Judul dan deskripsi --}}
                    <h1 class="hero-title-jobs text-4xl md:text-6xl font-bold mb-6 text-white leading-tight" style="color: white !important;">
                        Lowongan Kerja & Magang
                    </h1>

                    <p class="text-lg md:text-xl leading-relaxed max-w-2xl mx-auto font-medium mb-8" style="color: #ffffff !important; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5), 0 4px 12px rgba(0, 0, 0, 0.4);">
                        Temukan peluang karir yang sesuai dengan minat dan keahlian Anda
                    </p>

                    {{-- stats --}}
                    <div class="flex justify-center gap-8 mb-6">
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold" style="color: white !important;">{{ $totalJobs }}</div>
                            <div class="text-sm md:text-base font-medium" style="color: rgba(255, 255, 255, 0.9) !important;">Lowongan Aktif</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold" style="color: white !important;">{{ $totalCompanies }}</div>
                            <div class="text-sm md:text-base font-medium" style="color: rgba(255, 255, 255, 0.9) !important;">Perusahaan</div>
                        </div>
                    </div>

                    {{-- primary CTAs --}}
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('student.jobs.saved') }}"
                           class="inline-flex items-center justify-center px-8 py-3 bg-indigo-600 text-white font-bold rounded-full hover:bg-indigo-700 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                            <span>Lowongan Tersimpan</span>
                        </a>
                        <a href="{{ route('student.jobs.alerts') }}"
                           class="inline-flex items-center justify-center px-8 py-3 bg-white/20 backdrop-blur-sm text-white font-bold rounded-full hover:bg-white/30 transition-all duration-300 border-2 border-white hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span>Job Alert</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- straight divider --}}
        <div class="absolute bottom-0 left-0 right-0 bg-white" style="height: 4px; margin: 0;"></div>
    </section>

    {{-- main content --}}
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- filter sidebar --}}
            <aside class="w-full lg:w-72 flex-shrink-0">
                <div class="filter-sidebar bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Filter</h3>

                    <form method="GET" action="{{ route('student.jobs.index') }}" id="filterForm">
                        {{-- sdg alignment --}}
                        <div class="filter-section">
                            <label class="block text-sm font-medium text-gray-700 mb-3">SDG Alignment</label>
                            <div class="space-y-2 max-h-60 overflow-y-auto">
                                @foreach($sdgOptions as $sdg)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox"
                                           name="sdg[]"
                                           value="{{ $sdg['id'] }}"
                                           {{ in_array($sdg['id'], (array) request('sdg', [])) ? 'checked' : '' }}
                                           class="filter-checkbox">
                                    <span class="text-sm text-gray-600">SDG {{ $sdg['id'] }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- buttons --}}
                        <div class="flex gap-2 mt-4">
                            <button type="submit"
                                    class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                Terapkan Filter
                            </button>
                            @if(request()->has('sdg'))
                            <a href="{{ route('student.jobs.index') }}"
                               class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                Reset
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </aside>

            {{-- job listings --}}
            <main class="flex-1">
                {{-- sort and view options --}}
                <div class="flex items-center justify-between mb-6">
                    <p class="text-gray-600">
                        Menampilkan <span class="font-semibold text-gray-900">{{ $jobs->total() }}</span> lowongan
                    </p>

                    <div class="flex items-center gap-4">
                        <select name="sort"
                                form="filterForm"
                                onchange="document.getElementById('filterForm').submit()"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="salary_high" {{ request('sort') == 'salary_high' ? 'selected' : '' }}>Gaji Tertinggi</option>
                            <option value="salary_low" {{ request('sort') == 'salary_low' ? 'selected' : '' }}>Gaji Terendah</option>
                        </select>
                    </div>
                </div>

                {{-- job list --}}
                <div class="space-y-4">
                    @forelse($jobs as $job)
                    <div class="job-card bg-white rounded-2xl border border-gray-100 p-6 hover:border-blue-200">
                        <div class="flex items-start gap-4">
                            {{-- company logo --}}
                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                @if($job->company && $job->company->logo_url)
                                <img src="{{ $job->company->logo_url }}" alt="{{ $job->company->name }}" crossorigin="anonymous" class="w-full h-full object-cover">
                                @else
                                <span class="text-white text-xl font-bold">{{ substr($job->company->name ?? 'C', 0, 1) }}</span>
                                @endif
                            </div>

                            {{-- job info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 hover:text-blue-600 transition-colors">
                                            <a href="{{ route('student.jobs.show', $job->id) }}">{{ $job->title }}</a>
                                        </h3>
                                        <p class="text-gray-600 mt-0.5">{{ $job->company->name ?? 'Company' }}</p>
                                    </div>

                                    {{-- job type badge --}}
                                    <span class="job-type-badge job-type-{{ $job->job_type }}">
                                        {{ $jobTypes[$job->job_type] ?? $job->job_type }}
                                    </span>
                                </div>

                                {{-- meta info --}}
                                <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500">
                                    @if($job->location)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span>{{ $job->location }}</span>
                                    </div>
                                    @endif

                                    @if($job->salary_min || $job->salary_max)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $job->salary_range }}</span>
                                    </div>
                                    @endif

                                    @if($job->published_at)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $job->published_at->diffForHumans() }}</span>
                                    </div>
                                    @endif
                                </div>

                                {{-- skills --}}
                                @if($job->skills && count($job->skills) > 0)
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach(array_slice($job->skills, 0, 4) as $skill)
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                                        {{ $skill }}
                                    </span>
                                    @endforeach
                                    @if(count($job->skills) > 4)
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-500 rounded-full text-xs">
                                        +{{ count($job->skills) - 4 }} lainnya
                                    </span>
                                    @endif
                                </div>
                                @endif

                                {{-- actions --}}
                                <div class="flex items-center gap-3 mt-4">
                                    <a href="{{ route('student.jobs.show', $job->id) }}"
                                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                        Lihat Detail
                                    </a>

                                    @if(in_array($job->id, $appliedJobIds))
                                    <span class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium">
                                        Sudah Melamar
                                    </span>
                                    @endif

                                    {{-- bookmark button --}}
                                    <button @click="toggleSave({{ $job->id }})"
                                            class="p-2 rounded-lg transition-colors"
                                            :class="savedJobs.includes({{ $job->id }}) ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'">
                                        <svg class="w-5 h-5" :fill="savedJobs.includes({{ $job->id }}) ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                        </svg>
                                    </button>

                                    {{-- compare checkbox --}}
                                    <label class="flex items-center gap-1.5 cursor-pointer text-sm text-gray-500 hover:text-gray-700">
                                        <input type="checkbox"
                                               :checked="compareList.includes({{ $job->id }})"
                                               @change="toggleCompare({{ $job->id }})"
                                               :disabled="!compareList.includes({{ $job->id }}) && compareList.length >= 3"
                                               class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span>Bandingkan</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Lowongan</h3>
                        <p class="text-gray-600 mb-4">Belum ada lowongan yang sesuai dengan filter Anda</p>
                        <a href="{{ route('student.jobs.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            Reset Filter
                        </a>
                    </div>
                    @endforelse
                </div>

                {{-- pagination --}}
                @if($jobs->hasPages())
                <div class="mt-8">
                    {{ $jobs->withQueryString()->links() }}
                </div>
                @endif
            </main>
        </div>
    </div>

    {{-- compare bar --}}
    <div x-show="compareList.length > 0"
         x-transition
         class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg p-4 z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-700">
                    <span x-text="compareList.length"></span> lowongan dipilih
                </span>
                <button @click="clearCompare" class="text-sm text-red-600 hover:text-red-700">Hapus Semua</button>
            </div>
            <button @click="goCompare"
                    :disabled="compareList.length < 2"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                Bandingkan Lowongan
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function jobsPage() {
    return {
        savedJobs: @json($savedJobIds ?? []),
        compareList: [],

        init() {
            // load compare list dari localStorage
            const stored = localStorage.getItem('compareJobs');
            if (stored) this.compareList = JSON.parse(stored);
        },

        async toggleSave(jobId) {
            try {
                const response = await fetch(`{{ url('student/jobs') }}/${jobId}/toggle-save`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.saved) {
                    this.savedJobs.push(jobId);
                } else {
                    this.savedJobs = this.savedJobs.filter(id => id !== jobId);
                }
            } catch (e) {
                console.error(e);
            }
        },

        toggleCompare(jobId) {
            if (this.compareList.includes(jobId)) {
                this.compareList = this.compareList.filter(id => id !== jobId);
            } else if (this.compareList.length < 3) {
                this.compareList.push(jobId);
            }
            localStorage.setItem('compareJobs', JSON.stringify(this.compareList));
        },

        goCompare() {
            if (this.compareList.length >= 2) {
                window.location.href = `{{ route('student.jobs.compare') }}?ids=${this.compareList.join(',')}`;
            }
        },

        clearCompare() {
            this.compareList = [];
            localStorage.removeItem('compareJobs');
        }
    }
}
</script>
@endpush
@endsection
