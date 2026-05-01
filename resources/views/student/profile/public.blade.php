{{-- resources/views/student/profile/public.blade.php --}}
@extends('layouts.app')

@section('title', $student->first_name . ' ' . $student->last_name . ' - profil publik')

@push('styles')
<style>
/* animasi fade in */
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

.profile-container {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.project-item {
    transition: all 0.3s ease;
}

.project-item:hover {
    transform: translateX(8px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.skill-tag {
    transition: all 0.2s ease;
}

.skill-tag:hover {
    transform: scale(1.05);
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-white">

    {{-- Hero Section dengan Background Image --}}
    <div class="relative h-[550px] overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('dashboard-student3.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 via-blue-800/85 to-transparent"></div>

        <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-full flex items-center">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 w-full items-center">
                    {{-- Left: Profile Info --}}
                    <div class="text-white space-y-6 profile-container">
                        <div class="flex items-start gap-6">
                            @if($student->profile_photo_path)
                                <img src="{{ $student->profile_photo_url }}"
                                     alt="{{ $student->first_name }}"
                                     class="w-32 h-32 rounded-2xl object-cover border-4 border-white/30 shadow-2xl backdrop-blur-sm">
                            @else
                                <div class="w-32 h-32 rounded-2xl border-4 border-white/30 shadow-2xl backdrop-blur-sm bg-white/10 flex items-center justify-center">
                                    <span class="text-white text-5xl font-bold">{{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}</span>
                                </div>
                            @endif

                            <div class="flex-1">
                                <h1 class="text-4xl md:text-5xl font-bold mb-3 leading-tight">
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </h1>
                                <div class="space-y-2 text-lg text-white/90">
                                    <p class="font-medium">{{ $student->major }}</p>
                                    <p class="text-white/80">{{ $student->university->name ?? '' }}</p>
                                </div>
                            </div>
                        </div>

                        @if($student->bio)
                        <div class="pl-2 border-l-4 border-white/40">
                            <p class="text-lg text-white/90 leading-relaxed">{{ $student->bio }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Right: Statistics --}}
                    <div class="grid grid-cols-2 gap-6 profile-container" style="animation-delay: 0.1s;">
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-blue-500/30 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-white">{{ $statistics['completed_projects'] }}</p>
                                    <p class="text-sm text-white/70">Proyek Selesai</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-green-500/30 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-white">{{ $statistics['sdgs_addressed'] }}</p>
                                    <p class="text-sm text-white/70">SDG Disentuh</p>
                                </div>
                            </div>
                        </div>

                        @if($statistics['positive_reviews'] > 0)
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-yellow-500/30 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-white">{{ $statistics['positive_reviews'] }}</p>
                                    <p class="text-sm text-white/70">Review Positif</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($statistics['average_rating'] > 0)
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-purple-500/30 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-white">{{ number_format($statistics['average_rating'], 1) }}</p>
                                    <p class="text-sm text-white/70">Rating Rata-rata</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        {{-- Skills & Contact Section - Balanced Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">

            {{-- Left: Skills --}}
            @php
                $skillsList = $skills ?? [];
                // pastikan dalam bentuk array
                if (is_string($skillsList)) {
                    $skillsList = json_decode($skillsList, true) ?? [];
                }
                if (!is_array($skillsList)) {
                    $skillsList = [];
                }
            @endphp
            @if(count($skillsList) > 0)
            <div class="profile-container" style="animation-delay: 0.2s;">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1 h-12 bg-gradient-to-b from-blue-600 to-green-600 rounded-full"></div>
                    <h2 class="text-3xl font-bold text-gray-900">Skills & Keahlian</h2>
                </div>
                <div class="flex flex-wrap gap-3">
                    @foreach($skillsList as $skill)
                        <span class="px-5 py-2.5 bg-gradient-to-r from-blue-50 to-green-50 text-gray-800 text-base rounded-xl hover:from-blue-100 hover:to-green-100 transition-all font-medium border border-blue-100 skill-tag">
                            {{ $skill }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Right: Contact Info --}}
            <div class="profile-container" style="animation-delay: 0.3s;">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1 h-12 bg-gradient-to-b from-purple-600 to-pink-600 rounded-full"></div>
                    <h2 class="text-3xl font-bold text-gray-900">Informasi Kontak</h2>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-white rounded-xl border-l-4 border-blue-500">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Email</p>
                            <p class="text-base text-gray-900 font-medium">{{ $student->user->email }}</p>
                        </div>
                    </div>

                    @if($student->whatsapp)
                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-green-50 to-white rounded-xl border-l-4 border-green-500">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">WhatsApp</p>
                            <p class="text-base text-gray-900 font-medium">{{ $student->whatsapp }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-purple-50 to-white rounded-xl border-l-4 border-purple-500">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Universitas</p>
                            <p class="text-base text-gray-900 font-medium">{{ $student->university->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Story / Experience Section --}}
        <div class="mb-16 profile-container" style="animation-delay: 0.4s;">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-1 h-12 bg-gradient-to-b from-orange-600 to-red-600 rounded-full"></div>
                <h2 class="text-3xl font-bold text-gray-900">Cerita & Pengalaman</h2>
            </div>

            @if($student->stories && count($student->stories) > 0)
                <div class="space-y-6">
                    @foreach($student->stories as $index => $story)
                        <div class="bg-gradient-to-br from-orange-50 via-white to-red-50 rounded-3xl p-10 border-l-4 border-orange-500">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="flex-shrink-0 w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">{{ $index + 1 }}</span>
                                </div>
                                <div class="prose prose-lg max-w-none flex-1">
                                    <p class="text-gray-700 leading-relaxed text-lg m-0">
                                        {{ $story }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gradient-to-br from-orange-50 via-white to-red-50 rounded-3xl p-10 border-l-4 border-orange-500">
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-orange-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <p class="text-gray-500 text-lg">Belum ada cerita yang dibagikan</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Projects Section --}}
        <div class="profile-container" style="animation-delay: 0.5s;">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-1 h-12 bg-gradient-to-b from-blue-600 to-purple-600 rounded-full"></div>
                <h2 class="text-3xl font-bold text-gray-900">Proyek yang Telah Diselesaikan</h2>
            </div>

            @if(!isset($completed_projects) || $completed_projects->isEmpty())
                <div class="text-center py-20 bg-gray-50 rounded-3xl">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-xl">Belum ada proyek yang diselesaikan</p>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($completed_projects as $project)
                        <div class="group border-l-4 border-blue-500 bg-gray-50 hover:bg-white rounded-r-2xl p-8 transition-all hover:shadow-xl project-item">
                            <div class="flex items-start justify-between gap-6">
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                                        {{ $project->problem->title }}
                                    </h3>

                                    <div class="mb-4">
                                        <span class="inline-flex items-center px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                            {{ $project->problem->institution->name }}
                                        </span>
                                    </div>

                                    <p class="text-gray-600 text-base mb-4 leading-relaxed">
                                        {{ Str::limit($project->problem->description, 200) }}
                                    </p>

                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-4">
                                        <span class="flex items-center gap-2 font-medium">
                                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $project->problem->regency->name ?? '-' }}, {{ $project->problem->province->name ?? '-' }}
                                        </span>

                                        @if($project->role_in_team)
                                            <span class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                {{ $project->role_in_team }}
                                            </span>
                                        @endif

                                        @if($project->actual_end_date)
                                            <span class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                Selesai {{ \Carbon\Carbon::parse($project->actual_end_date)->format('M Y') }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- SDG categories --}}
                                    @php
                                        $sdgCategories = $project->problem->sdg_categories;
                                        if (is_string($sdgCategories)) {
                                            $sdgCategories = json_decode($sdgCategories, true) ?? [];
                                        }
                                        if (!is_array($sdgCategories)) {
                                            $sdgCategories = [];
                                        }
                                    @endphp
                                    @if(count($sdgCategories) > 0)
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            @foreach($sdgCategories as $sdg)
                                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">
                                                    SDG {{ $sdg }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- review dari institution --}}
                                    @if($project->institutionReview && $project->institutionReview->comment)
                                        <div class="mt-4 p-4 bg-blue-50 rounded-xl border-l-4 border-blue-500">
                                            <p class="text-xs font-bold text-blue-900 mb-1 uppercase tracking-wide">Review dari Instansi</p>
                                            <p class="text-sm text-gray-700 italic leading-relaxed">"{{ $project->institutionReview->comment }}"</p>
                                        </div>
                                    @endif

                                    {{-- 🎓 SERTIFIKAT SECTION --}}
                                    @if($project->certificate_path)
                                    <div class="mt-4 p-4 bg-linear-to-r from-yellow-50 to-amber-50 border-2 border-yellow-300 rounded-xl">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <div class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900">Sertifikat Tersedia</p>
                                                    <p class="text-xs text-gray-600">No: {{ $project->certificate_number }}</p>
                                                </div>
                                            </div>
                                            <a href="{{ document_url($project->certificate_path) }}"
                                               target="_blank"
                                               download
                                               class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold text-sm transition-colors flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                @if($project->institutionReview)
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center gap-2 bg-yellow-100 px-5 py-3 rounded-xl">
                                            <svg class="w-6 h-6 text-yellow-500 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <span class="text-xl font-bold text-gray-900">{{ number_format($project->institutionReview->rating, 1) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection