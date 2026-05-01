{{-- resources/views/student/browse-problems/detail.blade.php --}}
@extends('layouts.app')

@section('title', $problem->title)

@push('styles')
<style>
/* animasi smooth scroll */
html {
    scroll-behavior: smooth;
}

/* animasi fade in */
.fade-in {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* stagger animations */
.fade-in-delay-1 { animation-delay: 0.1s; }
.fade-in-delay-2 { animation-delay: 0.2s; }
.fade-in-delay-3 { animation-delay: 0.3s; }
.fade-in-delay-4 { animation-delay: 0.4s; }
.fade-in-delay-5 { animation-delay: 0.5s; }

/* image gallery */
.gallery-container {
    position: relative;
    overflow: hidden;
}

.gallery-main-image {
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.gallery-main-image:hover {
    transform: scale(1.05);
}

/* thumbnail hover effect */
.gallery-thumbnail {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0.7;
}

.gallery-thumbnail:hover,
.gallery-thumbnail.active {
    opacity: 1;
    transform: translateY(-4px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* wishlist button animation */
.wishlist-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.wishlist-btn:hover {
    transform: scale(1.1) rotate(-5deg);
}

.wishlist-btn.wishlisted {
    animation: heartbeat 0.6s ease-in-out;
}

@keyframes heartbeat {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.3); }
    50% { transform: scale(1.1); }
    75% { transform: scale(1.2); }
}

/* share button ripple effect */
.share-btn {
    position: relative;
    overflow: hidden;
}

.share-btn::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(59, 130, 246, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.share-btn:active::after {
    width: 200px;
    height: 200px;
}

/* smooth hover effects */
.hover-lift {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.15);
}

/* badge animation */
.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.05);
}

/* apply button gradient animation */
.apply-btn {
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.apply-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.5);
}

/* institution card animation */
.institution-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.institution-card:hover {
    transform: translateX(4px);
}

/* similar projects animation */
.similar-project {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.similar-project:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.1);
}

/* reduced motion support */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- breadcrumb --}}
        <nav class="mb-6 fade-in">
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('student.dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors">Dashboard</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="{{ route('student.browse-problems.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">Browse Problems</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-900 font-medium">{{ Str::limit($problem->title, 30) }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- main content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- header card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $problem->title }}</h1>
                            
                            {{-- meta info --}}
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span>{{ $problem->regency->name ?? '-' }}, {{ $problem->province->name ?? '-' }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $problem->duration_months }} Bulan</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>{{ $problem->required_students }} Mahasiswa</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>{{ $problem->views_count ?? 0 }} Views</span>
                                </div>
                            </div>
                        </div>

                        {{-- action buttons --}}
                        <div class="flex gap-2">
                            {{-- wishlist button --}}
                            @auth
                                @if(auth()->user()->isStudent())
                                <button onclick="toggleWishlist({{ $problem->id }})" 
                                        class="wishlist-btn p-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 {{ $isWishlisted ? 'wishlisted' : '' }}">
                                    <svg class="w-5 h-5 {{ $isWishlisted ? 'text-red-500 fill-current' : 'text-gray-600' }}" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>
                                @endif
                            @endauth

                            {{-- share button --}}
                            <button onclick="shareProject()" class="share-btn p-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- badges --}}
                    <div class="flex flex-wrap gap-2">
                        @if($problem->is_featured)
                        <span class="badge px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                            ⭐ Unggulan
                        </span>
                        @endif
                        
                        @if($problem->is_urgent)
                        <span class="badge px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full animate-pulse">
                            🔥 Mendesak
                        </span>
                        @endif

                        <span class="badge px-3 py-1 
                            {{ $problem->difficulty_level === 'beginner' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $problem->difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $problem->difficulty_level === 'advanced' ? 'bg-red-100 text-red-700' : '' }}
                            text-xs font-semibold rounded-full">
                            {{ ucfirst($problem->difficulty_level) }}
                        </span>

                        {{-- SDG categories --}}
                        @if($problem->sdg_categories)
                            @php
                                // gunakan helper function sdg_label() untuk konsistensi
                                $categories = is_array($problem->sdg_categories) 
                                    ? $problem->sdg_categories 
                                    : json_decode($problem->sdg_categories, true) ?? [];
                            @endphp
                            @foreach($categories as $sdg)
                            <span class="badge px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                {{ $sdg }}. {{ sdg_label($sdg) }}
                            </span>
                            @endforeach
                        @endif

                        {{-- deadline badge --}}
                        @php
                            $deadline = \Carbon\Carbon::parse($problem->application_deadline);
                            $daysLeft = now()->diffInDays($deadline, false);
                        @endphp
                        @if($daysLeft <= 7 && $daysLeft > 0)
                        <span class="badge px-3 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">
                            ⏰ {{ $daysLeft }} Hari Lagi
                        </span>
                        @endif
                    </div>
                </div>

                {{-- image gallery --}}
                @if($problem->images && $problem->images->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in fade-in-delay-1">
                    <div class="gallery-container rounded-lg overflow-hidden mb-4">
                        <img id="main-gallery-image" 
                             src="{{ supabase_url($problem->images->first()->image_path) }}" 
                             alt="{{ $problem->title }}"
                             class="gallery-main-image w-full h-96 object-cover">
                    </div>
                    
                    @if($problem->images->count() > 1)
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($problem->images as $index => $image)
                        <button onclick="changeGalleryImage('{{ supabase_url($image->image_path) }}', {{ $index }})" 
                                class="gallery-thumbnail rounded-lg overflow-hidden h-24 {{ $index === 0 ? 'active' : '' }}"
                                data-index="{{ $index }}">
                            <img src="{{ supabase_url($image->image_path) }}" 
                                 alt="Gallery {{ $index + 1 }}"
                                 class="w-full h-full object-cover">
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif

                {{-- description --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in fade-in-delay-2">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Deskripsi Proyek
                    </h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($problem->description)) !!}
                    </div>
                </div>

                {{-- background (optional) --}}
                @if($problem->background)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in fade-in-delay-3">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Latar Belakang
                    </h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($problem->background)) !!}
                    </div>
                </div>
                @endif

                {{-- objectives (optional) --}}
                @if($problem->objectives)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in fade-in-delay-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        Tujuan Proyek
                    </h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($problem->objectives)) !!}
                    </div>
                </div>
                @endif

                {{-- requirements --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in fade-in-delay-5">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        Requirements
                    </h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-1">Jumlah Mahasiswa</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $problem->required_students }}</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 mb-1">Durasi</p>
                                <p class="text-2xl font-bold text-green-600">{{ $problem->duration_months }} Bulan</p>
                            </div>
                        </div>

                        {{-- safety check untuk required_skills --}}
                        @php
                            $skills = is_array($problem->required_skills) ? $problem->required_skills : (json_decode($problem->required_skills, true) ?? []);
                        @endphp
                        @if(!empty($skills))
                        <div class="mt-6">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Skills Yang Dibutuhkan:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($skills as $skill)
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-sm rounded-full">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- expected outcomes --}}
                        @if($problem->expected_outcomes)
                        <div class="mt-6">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Expected Outcomes:</p>
                            <p class="text-gray-600 text-sm">{{ $problem->expected_outcomes }}</p>
                        </div>
                        @endif
                    </div>

                </div>

            </div>

            {{-- SIDEBAR --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- combined card: CTA + similar problems --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in fade-in-delay-2">
                    
                    {{-- CTA section --}}
                    @auth
                        @if(!$hasApplied)
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-6 text-white mb-6">
                                <h3 class="font-bold text-lg mb-2">Tertarik Dengan Proyek Ini?</h3>
                                <p class="text-sm mb-4 text-blue-50">Aplikasikan dirimu sekarang dan mulai berkontribusi!</p>
                                {{-- ubah parameter route dari problem_id menjadi problemId --}}
                                <a href="{{ route('student.applications.create', $problem->id) }}" 
                                   class="apply-btn block w-full text-center px-4 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors">
                                    Apply Sekarang
                                </a>
                                <p class="text-xs mt-3 text-blue-100 text-center">
                                    Deadline: {{ \Carbon\Carbon::parse($problem->application_deadline)->format('d M Y') }}
                                </p>
                            </div>
                        @else
                            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-6 text-white mb-6">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <h3 class="font-bold text-lg">Aplikasi Terkirim</h3>
                                </div>
                                <p class="text-sm text-green-50">Anda sudah mengajukan aplikasi untuk proyek ini. Cek status di halaman My Applications.</p>
                                <a href="{{ route('student.applications.index') }}" 
                                   class="block w-full text-center px-4 py-2 bg-white text-green-600 font-semibold rounded-lg hover:bg-green-50 transition-colors mt-4">
                                    Lihat Status Aplikasi
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-lg p-6 text-white mb-6">
                            <h3 class="font-bold text-lg mb-2">Tertarik Dengan Proyek Ini?</h3>
                            <p class="text-sm mb-4 text-gray-100">Login terlebih dahulu untuk apply ke proyek ini!</p>
                            <a href="{{ route('login') }}" 
                               class="block w-full text-center px-4 py-3 bg-white text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                Login
                            </a>
                        </div>
                    @endauth

                    {{-- similar problems section --}}
                    @if(isset($similarProblems) && $similarProblems->isNotEmpty())
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Proyek Serupa
                            </h3>
                            <div class="space-y-3">
                                @foreach($similarProblems as $similar)
                                <a href="{{ route('student.browse-problems.show', $similar->id) }}"
                                   class="block group similar-project">
                                    <div class="border border-gray-200 rounded-lg p-3 hover:border-blue-400 hover:bg-blue-50 transition-all duration-200">
                                        <h4 class="font-semibold text-gray-900 text-sm mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                                            {{ $similar->title }}
                                        </h4>
                                        <div class="flex items-center gap-2 text-xs text-gray-600 mb-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            </svg>
                                            <span>{{ $similar->regency->name ?? 'N/A' }}, {{ $similar->province->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600">{{ $similar->institution->name ?? 'N/A' }}</span>
                                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full font-semibold">
                                                {{ $similar->duration_months }} bulan
                                            </span>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- institution info card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover-lift">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Tentang Instansi
                    </h3>
                    
                    <a href="{{ route('institution.public', $problem->institution->id) }}" 
                       class="institution-card block">
                        <div class="flex items-start gap-3 mb-4">
                            @if($problem->institution->logo_path)
                            <img src="{{ $problem->institution->getLogoUrl() }}" 
                                 alt="{{ $problem->institution->name }}"
                                 class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                            @else
                            <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                                {{ strtoupper(substr($problem->institution->name, 0, 1)) }}
                            </div>
                            @endif
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 mb-1">{{ $problem->institution->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $problem->institution->type ?? 'Instansi' }}</p>
                            </div>
                        </div>
                    </a>

                    @if($problem->institution->description)
                    <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $problem->institution->description }}</p>
                    @endif

                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate">{{ $problem->institution->getFullAddress() }}</span>
                        </div>
                        
                        @if($problem->institution->phone)
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ $problem->institution->phone }}</span>
                        </div>
                        @endif

                        @if($problem->institution->email)
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="truncate">{{ $problem->institution->email }}</span>
                        </div>
                        @endif

                        @if($problem->institution->website)
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            <a href="{{ $problem->institution->website }}" target="_blank" class="text-blue-600 hover:underline truncate">
                                Website
                            </a>
                        </div>
                        @endif
                    </div>

                    <a href="{{ route('institution.public', $problem->institution->id) }}" 
                       class="block w-full text-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors mt-4">
                        Lihat Profil Lengkap
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// gallery image changer
function changeGalleryImage(src, index) {
    const mainImage = document.getElementById('main-gallery-image');
    mainImage.src = src;
    
    // update active thumbnail
    document.querySelectorAll('.gallery-thumbnail').forEach((thumb, i) => {
        if (i === index) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });
}

// wishlist toggle
async function toggleWishlist(problemId) {
    try {
        const response = await fetch(`/student/wishlist/${problemId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            const btn = document.querySelector('.wishlist-btn');
            const svg = btn.querySelector('svg');
            
            if (data.saved) {
                btn.classList.add('wishlisted');
                svg.classList.add('text-red-500', 'fill-current');
                svg.classList.remove('text-gray-600');
            } else {
                btn.classList.remove('wishlisted');
                svg.classList.remove('text-red-500', 'fill-current');
                svg.classList.add('text-gray-600');
            }
            
            // show notification
            showNotification(data.message, 'success');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan', 'error');
    }
}

// share project
function shareProject() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $problem->title }}',
            text: 'Lihat proyek KKN menarik ini!',
            url: window.location.href
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showNotification('Link berhasil disalin!', 'success');
        });
    }
}

// notification helper
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300`;
    notification.textContent = message;
    notification.style.transform = 'translateY(-100px)';
    notification.style.opacity = '0';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateY(0)';
        notification.style.opacity = '1';
    }, 10);
    
    setTimeout(() => {
        notification.style.transform = 'translateY(-100px)';
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// smooth scroll untuk semua anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endpush