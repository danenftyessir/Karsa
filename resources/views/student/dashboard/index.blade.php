@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - Karsa')

@push('styles')
{{-- Import Google Font - Space Grotesk for Hero --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

<style>
    /* VERSI 1: Minimalis Modern - Karsa Brand Colors (Indigo/Periwinkle + Gray) */
    .marketplace-hero {
        position: relative;
        background-image:
            linear-gradient(135deg, rgba(99, 102, 241, 0.35) 0%, rgba(129, 140, 248, 0.30) 50%, rgba(156, 163, 175, 0.25) 100%),
            url('/dashboard-student2.jpeg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 480px;
    }

    .hero-title {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    /* VERSI 2: Balanced Professional */
    /* .marketplace-hero {
        position: relative;
        background-image:
            linear-gradient(135deg, rgba(99, 102, 241, 0.45) 0%, rgba(124, 124, 255, 0.40) 40%, rgba(156, 163, 175, 0.35) 100%),
            url('/dashboard-student2.jpeg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 400px;
    } */

    /* VERSI 3: Energetic & Vibrant */
    /* .marketplace-hero {
        position: relative;
        background-image:
            linear-gradient(135deg, rgba(99, 102, 241, 0.50) 0%, rgba(139, 92, 246, 0.45) 35%, rgba(167, 139, 250, 0.40) 70%, rgba(156, 163, 175, 0.30) 100%),
            url('/dashboard-student2.jpeg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 400px;
    } */

    .stat-bar {
        height: 40px;
        transition: width 1s ease-out;
    }

    @keyframes growBar {
        from {
            width: 0;
        }
    }

    .stat-bar.animate {
        animation: growBar 1.5s ease-out;
    }

    .gradient-mesh-bg {
        background-color: #ffffff;
        background-image:
            radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
            radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.08) 0px, transparent 50%),
            radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.08) 0px, transparent 50%),
            radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.08) 0px, transparent 50%);
    }

    .text-shadow-strong {
        text-shadow:
            0 2px 4px rgba(0, 0, 0, 0.4),
            0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .dashboard-fade-in {
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

    .search-box {
        transition: all 0.3s ease;
    }

    .search-box:focus-within {
        transform: scale(1.02);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .quick-action-card {
        transition: all 0.3s ease;
    }

    .quick-action-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
</style>
@endpush

@section('content')

{{-- marketplace-style hero section --}}
<section class="marketplace-hero text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10 py-12">
        <div class="max-w-4xl mx-auto text-center">
            <div class="dashboard-fade-in">
                {{-- Welcome --}}
                <h1 class="hero-title text-4xl md:text-6xl font-bold mb-6 !text-white leading-tight" style="color: white !important;">
                    Selamat Datang Kembali,<br>
                    <span style="color: white !important;">{{ Auth::user()->name }}</span>
                </h1>

                {{-- primary CTA --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
                    <a href="{{ route('student.browse-problems.index') }}"
                       class="inline-flex items-center justify-center px-8 py-3 bg-indigo-600 text-white font-bold rounded-full hover:bg-indigo-700 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Cari Proyek KKN</span>
                    </a>
                    <a href="{{ route('student.applications.index') }}"
                       class="inline-flex items-center justify-center px-8 py-3 bg-white/20 backdrop-blur-sm text-white font-bold rounded-full hover:bg-white/30 transition-all duration-300 border-2 border-white hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Lihat Aplikasi Saya</span>
                    </a>
                </div>

                <p class="text-lg md:text-xl leading-relaxed max-w-2xl mx-auto font-medium" style="color: #ffffff !important; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5), 0 4px 12px rgba(0, 0, 0, 0.4);">
                    Siap untuk berkontribusi dalam proyek KKN yang bermakna? Temukan proyek yang sesuai dengan passion Anda!
                </p>
            </div>
        </div>
    </div>

    {{-- straight divider --}}
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-white"></div>
</section>

{{-- main dashboard content tanpa card design --}}
<div class="gradient-mesh-bg min-h-screen">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-16">

        {{-- profile section inline tanpa card --}}
        <div class="mb-16">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                {{-- profile photo --}}
                <div class="flex-shrink-0">
                    <img src="{{ Auth::user()->profile_photo_url }}"
                         alt="{{ Auth::user()->name }}"
                         class="w-32 h-32 rounded-3xl object-cover shadow-xl border-4 border-white">
                </div>

                {{-- profile info --}}
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">
                        {{ Auth::user()->name }}
                    </h2>
                    <p class="text-lg text-gray-700 mb-1">
                        {{ Auth::user()->student->major }}
                    </p>
                    <p class="text-base text-gray-600 mb-4">
                        {{ Auth::user()->student->university->name }}
                    </p>
                    <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                        <a href="{{ route('student.profile.index') }}"
                           class="inline-flex items-center px-6 py-2 bg-yellow-400 text-gray-900 font-semibold rounded-full hover:bg-yellow-300 transition-all duration-300 shadow-md hover:shadow-lg">
                            Lihat Profil
                        </a>
                        <a href="{{ route('student.browse-problems.index') }}"
                           class="inline-flex items-center px-6 py-2 bg-white text-gray-900 font-semibold rounded-full hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
                            Cari Proyek
                        </a>
                    </div>
                </div>

                {{-- quick actions --}}
                <div class="flex-shrink-0">
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('student.friends.index') }}"
                           class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:text-purple-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="font-medium">Jaringan</span>
                            @if(Auth::user()->student->pendingFriendRequests()->count() > 0)
                            <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                {{ Auth::user()->student->pendingFriendRequests()->count() }}
                            </span>
                            @endif
                        </a>
                        <a href="{{ route('student.wishlist.index') }}"
                           class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:text-pink-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <span class="font-medium">Wishlist</span>
                        </a>
                        <a href="{{ route('student.repository.index') }}"
                           class="flex items-center gap-2 px-4 py-2 text-gray-700 hover:text-blue-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="font-medium">Repository</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- network section tanpa card --}}
        @php
            $pendingRequests = Auth::user()->student->pendingFriendRequests()->take(3);
            $suggestions = Auth::user()->student->suggestedFriends(4);
        @endphp

        @if($pendingRequests->count() > 0 || $suggestions->count() > 0)
        <div class="mb-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Jaringan Saya</h2>
                    <p class="text-gray-600">Terhubung dengan mahasiswa lain untuk berbagi pengalaman KKN</p>
                </div>
                <a href="{{ route('student.friends.index') }}"
                   class="inline-flex items-center px-6 py-2 text-sm font-semibold text-purple-600 hover:text-purple-700 transition-colors">
                    Lihat Semua
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            {{-- network stats --}}
            <div class="grid grid-cols-3 gap-6 mb-10">
                <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-3xl">
                    <div class="text-4xl font-bold text-blue-600 mb-1">
                        {{ Auth::user()->student->friendsCount() }}
                    </div>
                    <div class="text-sm text-gray-700 font-medium">Koneksi</div>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-yellow-50 to-yellow-100/50 rounded-3xl">
                    <div class="text-4xl font-bold text-yellow-600 mb-1">
                        {{ Auth::user()->student->pendingFriendRequests()->count() }}
                    </div>
                    <div class="text-sm text-gray-700 font-medium">Permintaan</div>
                </div>
                <div class="text-center p-6 bg-gradient-to-br from-purple-50 to-purple-100/50 rounded-3xl">
                    <div class="text-4xl font-bold text-purple-600 mb-1">
                        {{ Auth::user()->student->suggestedFriends(10)->count() }}
                    </div>
                    <div class="text-sm text-gray-700 font-medium">Saran</div>
                </div>
            </div>

            {{-- pending requests --}}
            @if($pendingRequests->count() > 0)
            <div class="mb-10">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Permintaan Pertemanan</h3>
                <div class="grid md:grid-cols-3 gap-4">
                    @foreach($pendingRequests as $request)
                    <div class="flex items-center gap-4 p-6 bg-white rounded-2xl hover:shadow-lg transition-shadow border border-gray-100">
                        <img src="{{ $request->requester->profile_photo_url }}"
                             alt="{{ $request->requester->user->name }}"
                             class="w-16 h-16 rounded-full object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 truncate">
                                {{ $request->requester->user->name }}
                            </p>
                            <p class="text-sm text-gray-600 truncate">
                                {{ $request->requester->major }}
                            </p>
                            <div class="flex gap-2 mt-3">
                                <form method="POST" action="{{ route('student.friends.accept', $request->id) }}" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-3 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors">
                                        Terima
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('student.friends.reject', $request->id) }}">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- suggestions --}}
            @if($suggestions->count() > 0)
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-6">Rekomendasi Koneksi</h3>
                <div class="grid md:grid-cols-4 gap-4">
                    @foreach($suggestions as $suggestion)
                    <div class="p-6 bg-white rounded-2xl hover:shadow-lg transition-shadow text-center border border-gray-100">
                        <img src="{{ $suggestion->profile_photo_url }}"
                             alt="{{ $suggestion->user->name }}"
                             class="w-20 h-20 rounded-full object-cover mx-auto mb-4">
                        <p class="font-semibold text-gray-900 mb-1">
                            {{ $suggestion->user->name }}
                        </p>
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                            {{ $suggestion->major }}
                        </p>
                        <form method="POST" action="{{ route('student.friends.send-request', $suggestion->id) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-purple-500 text-white text-sm font-medium rounded-lg hover:bg-purple-600 transition-colors">
                                Hubungkan
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- statistics chart section --}}
        @php
            $totalApplications = Auth::user()->student->applications()->count();
            $activeProjects = Auth::user()->student->projects()->where('status', 'in_progress')->count();
            $completedProjects = Auth::user()->student->projects()->where('status', 'completed')->count();
            $connections = Auth::user()->student->friendsCount();
            $maxValue = max($totalApplications, $activeProjects, $completedProjects, $connections, 1);
        @endphp

        <div class="mb-16">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Statistik Aktivitas Anda</h2>
                <p class="text-gray-600">Ringkasan perjalanan KKN Anda</p>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-lg">
                {{-- Grid 4 kolom untuk statistik --}}
                <div class="grid grid-cols-4 gap-6 mb-8">
                    {{-- Total Aplikasi --}}
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-indigo-600 mb-1">{{ $totalApplications }}</div>
                        <div class="text-sm font-semibold text-gray-900 mb-1">Total Aplikasi</div>
                        <div class="text-xs text-gray-500">Diajukan</div>
                    </div>

                    {{-- Proyek Aktif --}}
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-blue-600 mb-1">{{ $activeProjects }}</div>
                        <div class="text-sm font-semibold text-gray-900 mb-1">Proyek Aktif</div>
                        <div class="text-xs text-gray-500">Dikerjakan</div>
                    </div>

                    {{-- Proyek Selesai --}}
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-green-600 mb-1">{{ $completedProjects }}</div>
                        <div class="text-sm font-semibold text-gray-900 mb-1">Proyek Selesai</div>
                        <div class="text-xs text-gray-500">Diselesaikan</div>
                    </div>

                    {{-- Koneksi --}}
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-bold text-gray-600 mb-1">{{ $connections }}</div>
                        <div class="text-sm font-semibold text-gray-900 mb-1">Koneksi</div>
                        <div class="text-xs text-gray-500">Jaringan</div>
                    </div>
                </div>

                {{-- Visualisasi bar chart --}}
                <div class="space-y-4">
                    {{-- Bar untuk Total Aplikasi --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Total Aplikasi</span>
                            <span class="text-sm font-bold text-indigo-600">{{ $totalApplications }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="stat-bar bg-gradient-to-r from-indigo-500 to-indigo-600 h-full rounded-full animate transition-all"
                                 style="width: {{ $maxValue > 0 ? ($totalApplications / $maxValue * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Bar untuk Proyek Aktif --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Proyek Aktif</span>
                            <span class="text-sm font-bold text-blue-600">{{ $activeProjects }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="stat-bar bg-gradient-to-r from-blue-400 to-blue-500 h-full rounded-full animate transition-all"
                                 style="width: {{ $maxValue > 0 ? ($activeProjects / $maxValue * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Bar untuk Proyek Selesai --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Proyek Selesai</span>
                            <span class="text-sm font-bold text-green-600">{{ $completedProjects }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="stat-bar bg-gradient-to-r from-green-400 to-green-500 h-full rounded-full animate transition-all"
                                 style="width: {{ $maxValue > 0 ? ($completedProjects / $maxValue * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Bar untuk Koneksi --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Koneksi</span>
                            <span class="text-sm font-bold text-gray-600">{{ $connections }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="stat-bar bg-gradient-to-r from-gray-400 to-gray-500 h-full rounded-full animate transition-all"
                                 style="width: {{ $maxValue > 0 ? ($connections / $maxValue * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- recent applications tanpa card --}}
        @php
            $recentApplications = Auth::user()->student->applications()
                ->with(['problem.institution'])
                ->latest()
                ->take(5)
                ->get();
        @endphp

        @if($recentApplications->count() > 0)
        <div class="mb-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Aplikasi Terbaru</h2>
                    <p class="text-gray-600">Status aplikasi KKN yang telah Anda ajukan</p>
                </div>
                <a href="{{ route('student.applications.index') }}"
                   class="inline-flex items-center px-6 py-2 text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors">
                    Lihat Semua
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="space-y-4">
                @foreach($recentApplications as $application)
                <div class="flex items-center gap-6 p-6 bg-white rounded-2xl hover:shadow-lg transition-shadow border border-gray-100">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">
                            {{ $application->problem->title }}
                        </h3>
                        <p class="text-sm text-gray-600 mb-3">
                            {{ $application->problem->institution->name }}
                        </p>
                        <div class="flex items-center gap-4 text-sm">
                            <span class="text-gray-500">{{ $application->created_at->diffForHumans() }}</span>
                            <span class="px-3 py-1 rounded-full font-medium text-sm
                                {{ $application->status === 'accepted' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $application->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $application->status === 'under_review' ? 'bg-blue-100 text-blue-700' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('student.applications.show', $application->id) }}"
                       class="px-6 py-3 text-sm font-semibold text-blue-600 hover:text-white hover:bg-blue-600 border-2 border-blue-600 rounded-xl transition-all">
                        Detail
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- active projects tanpa card --}}
        @php
            $activeProjects = Auth::user()->student->projects()
                ->where('status', 'in_progress')
                ->with(['problem.institution'])
                ->latest()
                ->take(3)
                ->get();
        @endphp

        @if($activeProjects->count() > 0)
        <div class="mb-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Proyek Aktif</h2>
                    <p class="text-gray-600">Proyek KKN yang sedang Anda kerjakan</p>
                </div>
                <a href="{{ route('student.projects.index') }}"
                   class="inline-flex items-center px-6 py-2 text-sm font-semibold text-green-600 hover:text-green-700 transition-colors">
                    Lihat Semua
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @foreach($activeProjects as $project)
                <div class="p-6 bg-gradient-to-br from-white to-blue-50/30 rounded-2xl hover:shadow-xl transition-shadow border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                        {{ $project->problem->title }}
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        {{ $project->problem->institution->name }}
                    </p>

                    {{-- progress bar --}}
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-medium text-gray-700">Progress</span>
                            <span class="text-xs font-bold text-blue-600">{{ $project->progress ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all"
                                 style="width: {{ $project->progress ?? 0 }}%"></div>
                        </div>
                    </div>

                    <a href="{{ route('student.projects.show', $project->id) }}"
                       class="block w-full text-center px-4 py-3 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors">
                        Lihat Detail
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
// auto hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
</script>
@endpush
@endsection
