{{-- resources/views/student/applications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Aplikasi Saya')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

<style>
    /* gpu acceleration untuk smooth rendering */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .marketplace-hero-applications {
        position: relative;
        background-image:
            linear-gradient(135deg, rgba(99, 102, 241, 0.35) 0%, rgba(129, 140, 248, 0.30) 50%, rgba(156, 163, 175, 0.25) 100%),
            url('/application-student.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 380px;
        transform: translate3d(0, 0, 0);
        will-change: transform;
        backface-visibility: hidden;
    }

    .hero-title-applications {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .application-fade-in {
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

    .gradient-mesh-bg {
        background-color: #ffffff;
        background-image:
            radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
            radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.08) 0px, transparent 50%),
            radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.08) 0px, transparent 50%),
            radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.08) 0px, transparent 50%);
    }

    /* tab styles */
    .tab-button {
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #6b7280;
        border-bottom: 3px solid transparent;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translate3d(0, 0, 0);
    }

    .tab-button:hover {
        color: #4f46e5;
    }

    .tab-button.active {
        color: #4f46e5;
        border-bottom-color: #4f46e5;
    }

    /* application item dengan gpu acceleration */
    .application-item {
        transform: translate3d(0, 0, 0);
        will-change: transform, box-shadow;
        backface-visibility: hidden;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease;
    }

    .application-item:hover {
        transform: translate3d(0, -2px, 0);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }

    /* bar chart styling */
    .bar-chart-container {
        display: flex !important;
        align-items: flex-end !important;
        justify-content: space-around !important;
        height: 280px !important;
        width: 100% !important;
        gap: 1.5rem !important;
        padding: 2rem 1rem 1rem 1rem !important;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.03) 0%, transparent 100%) !important;
        border-radius: 1rem !important;
    }

    .bar-item {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 0.75rem !important;
        max-width: 120px !important;
        height: 100% !important;
    }

    .bar-column {
        width: 100% !important;
        max-width: 80px !important;
        border-radius: 0.5rem 0.5rem 0 0 !important;
        transition: all 0.3s ease !important;
        position: relative !important;
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }

    .bar-column:hover {
        transform: translateY(-4px) !important;
    }

    .bar-column-green { background: linear-gradient(to top, #22c55e, #10b981) !important; }
    .bar-column-blue { background: linear-gradient(to top, #3b82f6, #2563eb) !important; }
    .bar-column-yellow { background: linear-gradient(to top, #eab308, #ca8a04) !important; }
    .bar-column-red { background: linear-gradient(to top, #ef4444, #dc2626) !important; }
    .bar-column-purple { background: linear-gradient(to top, #8b5cf6, #7c3aed) !important; }
    .bar-column-cyan { background: linear-gradient(to top, #06b6d4, #0891b2) !important; }

    .bar-value {
        position: absolute !important;
        top: -2rem !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        font-weight: 700 !important;
        font-size: 1.25rem !important;
    }

    .bar-label {
        text-align: center !important;
        font-weight: 600 !important;
        font-size: 0.8rem !important;
    }

    /* smooth scroll */
    html {
        scroll-behavior: smooth;
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            transition-duration: 0.01ms !important;
        }
        html {
            scroll-behavior: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen gradient-mesh-bg" x-data="applicationsPage()">

    {{-- hero section --}}
    <section class="marketplace-hero-applications text-white relative flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10 w-full py-12">
            <div class="max-w-4xl mx-auto text-center">
                <div class="application-fade-in">
                    <h1 class="hero-title-applications text-4xl md:text-5xl font-bold mb-6 text-white">
                        Aplikasi Saya
                    </h1>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
                        <a href="{{ route('student.browse-problems.index') }}"
                           class="inline-flex items-center justify-center px-8 py-3 bg-indigo-600 text-white font-bold rounded-full hover:bg-indigo-700 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Cari Proyek KKN</span>
                        </a>
                        <a href="{{ route('student.jobs.index') }}"
                           class="inline-flex items-center justify-center px-8 py-3 bg-white/20 backdrop-blur-sm text-white font-bold rounded-full hover:bg-white/30 transition-all duration-300 border-2 border-white hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>Cari Lowongan</span>
                        </a>
                    </div>

                    <p class="text-lg md:text-xl text-white/90 max-w-2xl mx-auto">
                        Kelola dan pantau status aplikasi proyek KKN dan lamaran kerja Anda
                    </p>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-white"></div>
    </section>

    {{-- main content --}}
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-8">

        {{-- tab navigation --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8">
            <div class="flex border-b border-gray-100">
                <button type="button"
                        @click="switchTab('projects')"
                        :class="activeTab === 'projects' ? 'active' : ''"
                        class="tab-button flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Proyek KKN
                    <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-700">{{ $stats['total'] }}</span>
                </button>
                <button type="button"
                        @click="switchTab('jobs')"
                        :class="activeTab === 'jobs' ? 'active' : ''"
                        class="tab-button flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Lowongan Kerja
                    <span class="px-2 py-0.5 text-xs rounded-full bg-purple-100 text-purple-700">{{ $jobStats['total'] }}</span>
                </button>
            </div>
        </div>

        {{-- TAB 1: PROYEK KKN --}}
        <div x-show="activeTab === 'projects'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

            {{-- statistik proyek --}}
            @php
                $accepted = $stats['accepted'] ?? 0;
                $reviewed = $stats['reviewed'] ?? 0;
                $pending = $stats['pending'] ?? 0;
                $rejected = $stats['rejected'] ?? 0;
                $maxValue = max($accepted, $reviewed, $pending, $rejected, 1);
            @endphp

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Statistik Proyek KKN</h2>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="bar-chart-container">
                        <div class="bar-item">
                            <div class="bar-column bar-column-green" style="height: {{ $accepted > 0 ? max(($accepted / $maxValue) * 100, 10) : 10 }}%;">
                                <div class="bar-value text-green-600">{{ $accepted }}</div>
                            </div>
                            <div class="bar-label text-gray-700">Diterima</div>
                        </div>
                        <div class="bar-item">
                            <div class="bar-column bar-column-blue" style="height: {{ $reviewed > 0 ? max(($reviewed / $maxValue) * 100, 10) : 10 }}%;">
                                <div class="bar-value text-blue-600">{{ $reviewed }}</div>
                            </div>
                            <div class="bar-label text-gray-700">Direview</div>
                        </div>
                        <div class="bar-item">
                            <div class="bar-column bar-column-yellow" style="height: {{ $pending > 0 ? max(($pending / $maxValue) * 100, 10) : 10 }}%;">
                                <div class="bar-value text-yellow-600">{{ $pending }}</div>
                            </div>
                            <div class="bar-label text-gray-700">Pending</div>
                        </div>
                        <div class="bar-item">
                            <div class="bar-column bar-column-red" style="height: {{ $rejected > 0 ? max(($rejected / $maxValue) * 100, 10) : 10 }}%;">
                                <div class="bar-value text-red-600">{{ $rejected }}</div>
                            </div>
                            <div class="bar-label text-gray-700">Ditolak</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- filter proyek --}}
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
                <form method="GET" action="{{ route('student.applications.index') }}" class="flex flex-wrap gap-4">
                    <input type="hidden" name="tab" value="projects">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari berdasarkan judul proyek atau instansi..."
                           class="flex-1 min-w-[250px] px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <select name="status" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Direview</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <select name="sort" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    </select>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Filter
                    </button>
                </form>
            </div>

            {{-- daftar aplikasi proyek --}}
            <div class="space-y-4">
                @forelse($applications as $application)
                <div class="application-item bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start gap-4">
                        @if($application->problem->coverImage)
                        <img src="{{ $application->problem->coverImage->image_url }}"
                             alt="{{ $application->problem->title }}"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/96?text=No+Image';"
                             class="w-24 h-24 object-cover rounded-lg flex-shrink-0">
                        @else
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        @endif

                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $application->problem->title }}</h3>
                                    <p class="text-gray-600 mb-1">{{ $application->problem->institution->name }}</p>
                                    <p class="text-sm text-gray-500">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        {{ $application->problem->regency->name ?? $application->problem->location_regency }}
                                    </p>
                                </div>
                                <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full
                                    {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $application->status === 'reviewed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $application->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-3">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Diajukan {{ $application->applied_at->format('d M Y') }}</span>
                                </div>
                            </div>

                            @if($application->feedback && in_array($application->status, ['accepted', 'rejected']))
                            <div class="p-3 rounded-lg mb-3 {{ $application->status === 'accepted' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                                <p class="text-sm font-medium {{ $application->status === 'accepted' ? 'text-green-800' : 'text-red-800' }} mb-1">
                                    {{ $application->status === 'accepted' ? 'Feedback:' : 'Alasan Penolakan:' }}
                                </p>
                                <p class="text-sm {{ $application->status === 'accepted' ? 'text-green-700' : 'text-red-700' }}">{{ $application->feedback }}</p>
                            </div>
                            @endif

                            <div class="flex items-center gap-3">
                                <a href="{{ route('student.applications.show', $application->id) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Lihat Detail
                                </a>
                                @if($application->status === 'pending')
                                <form action="{{ route('student.applications.withdraw', $application->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan aplikasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Batalkan
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Aplikasi Proyek</h3>
                    <p class="text-gray-600 mb-6">Anda belum mengajukan aplikasi untuk proyek KKN apapun</p>
                    <a href="{{ route('student.browse-problems.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Jelajahi Proyek
                    </a>
                </div>
                @endforelse
            </div>

            @if($applications->hasPages())
            <div class="mt-6">{{ $applications->withQueryString()->links() }}</div>
            @endif
        </div>

        {{-- TAB 2: LOWONGAN KERJA --}}
        <div x-show="activeTab === 'jobs'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

            {{-- statistik lowongan --}}
            @php
                $jobNew = $jobStats['new'] ?? 0;
                $jobReviewed = $jobStats['reviewed'] ?? 0;
                $jobShortlisted = $jobStats['shortlisted'] ?? 0;
                $jobInterview = $jobStats['interview'] ?? 0;
                $jobHired = $jobStats['hired'] ?? 0;
                $jobRejected = $jobStats['rejected'] ?? 0;
                $jobMaxValue = max($jobNew, $jobReviewed, $jobShortlisted, $jobInterview, $jobHired, $jobRejected, 1);
            @endphp

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Statistik Lamaran Kerja</h2>
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="bar-chart-container">
                        <div class="bar-item">
                            <div class="bar-column bar-column-blue" style="height: {{ $jobNew > 0 ? max(($jobNew / $jobMaxValue) * 100, 10) : 10 }}%;">
                                <div class="bar-value text-blue-600">{{ $jobNew }}</div>
                            </div>
                            <div class="bar-label text-gray-700">Baru</div>
                        </div>
                        <div class="bar-item">
                            <div class="bar-column bar-column-cyan" style="height: {{ $jobReviewed > 0 ? max(($jobReviewed / $jobMaxValue) * 100, 10) : 10 }}%;">
                                <div class="bar-value text-cyan-600">{{ $jobReviewed }}</div>
                            </div>
                            <div class="bar-label text-gray-700">Direview</div>
                        </div>
                        <div class="bar-item">
                            <div class="bar-column bar-column-purple" style="height: {{ $jobShortlisted > 0 ? max(($jobShortlisted / $jobMaxValue) * 100, 10) : 10 }}%;">
                                <div class="bar-value text-purple-600">{{ $jobShortlisted }}</div>
                            </div>
                            <div class="bar-label text-gray-700">Shortlist</div>
                        </div>
                        <div class="bar-item">
                            <div class="bar-column bar-column-yellow" style="height: {{ $jobInterview > 0 ? max(($jobInterview / $jobMaxValue) * 100, 10) : 10 }}%;">
                                <div class="bar-value text-yellow-600">{{ $jobInterview }}</div>
                            </div>
                            <div class="bar-label text-gray-700">Interview</div>
                        </div>
                        <div class="bar-item">
                            <div class="bar-column bar-column-green" style="height: {{ $jobHired > 0 ? max(($jobHired / $jobMaxValue) * 100, 10) : 10 }}%;">
                                <div class="bar-value text-green-600">{{ $jobHired }}</div>
                            </div>
                            <div class="bar-label text-gray-700">Diterima</div>
                        </div>
                        <div class="bar-item">
                            <div class="bar-column bar-column-red" style="height: {{ $jobRejected > 0 ? max(($jobRejected / $jobMaxValue) * 100, 10) : 10 }}%;">
                                <div class="bar-value text-red-600">{{ $jobRejected }}</div>
                            </div>
                            <div class="bar-label text-gray-700">Ditolak</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- filter lowongan --}}
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
                <form method="GET" action="{{ route('student.applications.index') }}" class="flex flex-wrap gap-4">
                    <input type="hidden" name="tab" value="jobs">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari berdasarkan posisi atau perusahaan..."
                           class="flex-1 min-w-[250px] px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <select name="job_status" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="new" {{ request('job_status') == 'new' ? 'selected' : '' }}>Baru</option>
                        <option value="reviewed" {{ request('job_status') == 'reviewed' ? 'selected' : '' }}>Direview</option>
                        <option value="shortlisted" {{ request('job_status') == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                        <option value="interview" {{ request('job_status') == 'interview' ? 'selected' : '' }}>Interview</option>
                        <option value="hired" {{ request('job_status') == 'hired' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ request('job_status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    <select name="sort" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    </select>
                    <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                        Filter
                    </button>
                </form>
            </div>

            {{-- daftar lamaran kerja --}}
            <div class="space-y-4">
                @forelse($jobApplications as $jobApp)
                <div class="application-item bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($jobApp->jobPosting && $jobApp->jobPosting->company && $jobApp->jobPosting->company->logo_url)
                                <img src="{{ $jobApp->jobPosting->company->logo_url }}" alt="{{ $jobApp->jobPosting->company->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ $jobApp->jobPosting->company->avatar_svg ?? '' }}" alt="{{ $jobApp->jobPosting->company->name ?? 'Company' }}" class="w-full h-full object-cover">
                            @endif
                        </div>

                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $jobApp->jobPosting->title ?? 'Posisi Tidak Tersedia' }}</h3>
                                    <p class="text-gray-600 mb-1">{{ $jobApp->jobPosting->company->name ?? 'Perusahaan' }}</p>
                                    @if($jobApp->jobPosting && $jobApp->jobPosting->location)
                                    <p class="text-sm text-gray-500">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        {{ $jobApp->jobPosting->location }}
                                    </p>
                                    @endif
                                </div>
                                <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full
                                    {{ $jobApp->status === 'new' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $jobApp->status === 'reviewed' ? 'bg-cyan-100 text-cyan-800' : '' }}
                                    {{ $jobApp->status === 'shortlisted' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $jobApp->status === 'interview' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $jobApp->status === 'hired' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $jobApp->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($jobApp->status) }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-3">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Dilamar {{ $jobApp->applied_at ? $jobApp->applied_at->format('d M Y') : '-' }}</span>
                                </div>
                                @if($jobApp->jobPosting && $jobApp->jobPosting->job_type)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                    {{ ucfirst(str_replace('_', ' ', $jobApp->jobPosting->job_type)) }}
                                </span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                @if($jobApp->jobPosting)
                                <a href="{{ route('student.jobs.show', $jobApp->job_posting_id) }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Lihat Lowongan
                                </a>
                                @endif
                                @if($jobApp->status === 'new')
                                <form action="{{ route('student.jobs.withdraw', $jobApp->job_posting_id) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin membatalkan lamaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Batalkan
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Lamaran Kerja</h3>
                    <p class="text-gray-600 mb-6">Anda belum melamar ke lowongan kerja apapun</p>
                    <a href="{{ route('student.jobs.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                        Jelajahi Lowongan
                    </a>
                </div>
                @endforelse
            </div>

            @if($jobApplications->hasPages())
            <div class="mt-6">{{ $jobApplications->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function applicationsPage() {
    return {
        activeTab: '{{ $activeTab }}',

        switchTab(tab) {
            this.activeTab = tab;
            // update url tanpa reload
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);
        }
    }
}
</script>
@endpush
@endsection
