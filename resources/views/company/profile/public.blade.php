@extends('layouts.app')

@section('title', $company->name . ' - Profil Perusahaan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50">
    <!-- Hero Section -->
    <div class="relative bg-cover bg-center text-white py-20 overflow-hidden" style="background-image: url('{{ asset('company-profile.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row items-start gap-8 fade-in-up">
                <!-- Logo -->
                <div class="relative flex-shrink-0">
                    @if($company->logo)
                        <img src="{{ $company->logo_url }}"
                             alt="{{ $company->name }}"
                             crossorigin="anonymous"
                             class="w-32 h-32 rounded-2xl object-cover border-4 border-white/30 shadow-2xl">
                    @else
                        <div class="w-32 h-32 rounded-2xl border-4 border-white/30 shadow-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white text-5xl font-bold">{{ substr($company->name, 0, 1) }}</span>
                        </div>
                    @endif
                    @if($company->is_verified)
                        <div class="absolute -bottom-2 -right-2 bg-blue-500 rounded-full p-2">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Company Info -->
                <div class="flex-1">
                    <h1 class="text-5xl font-bold mb-3" style="font-family: 'Space Grotesk', sans-serif; color: white !important;">
                        {{ $company->name }}
                    </h1>
                    @if($company->tagline)
                        <p class="text-xl text-blue-100 mb-4">{{ $company->tagline }}</p>
                    @endif
                    <div class="flex flex-wrap gap-2">
                        @if($company->industry)
                            <span class="px-4 py-2 bg-blue-500/30 backdrop-blur-sm text-white rounded-full text-sm font-medium border border-white/20">
                                {{ $company->industry }}
                            </span>
                        @endif
                        @if($company->company_size)
                            <span class="px-4 py-2 bg-purple-500/30 backdrop-blur-sm text-white rounded-full text-sm font-medium border border-white/20">
                                {{ $company->company_size }} Karyawan
                            </span>
                        @endif
                        @if($company->is_verified)
                            <span class="px-4 py-2 bg-green-500/30 backdrop-blur-sm text-white rounded-full text-sm font-medium border border-white/20 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Terverifikasi
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- About -->
                @if($company->description)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Tentang Perusahaan
                    </h3>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $company->description }}</p>
                </div>
                @endif

                <!-- Active Jobs -->
                @if($activeJobs->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.1s;">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Lowongan Pekerjaan Aktif
                    </h3>
                    <div class="space-y-3">
                        @foreach($activeJobs as $job)
                        <a href="{{ route('student.jobs.show', $job->id) }}"
                           class="block p-4 border border-gray-200 rounded-xl hover:border-blue-300 hover:shadow-md transition-all duration-200">
                            <h4 class="font-bold text-gray-900 mb-1">{{ $job->title }}</h4>
                            <div class="flex flex-wrap gap-2 text-sm text-gray-600">
                                @if($job->location)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        {{ $job->location }}
                                    </span>
                                @endif
                                @if($job->job_type)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-md text-xs">
                                        {{ $job->job_type }}
                                    </span>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @if($activeJobs->count() >= 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('student.jobs.index', ['company' => $company->id]) }}"
                           class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                            Lihat Semua Lowongan →
                        </a>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Contact Information -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Kontak
                    </h3>
                    <div class="space-y-3">
                        @if($company->email)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-1">Email</p>
                                <a href="mailto:{{ $company->email }}" class="text-gray-900 hover:text-blue-600 transition-colors break-words">
                                    {{ $company->email }}
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($company->phone)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-1">Telepon</p>
                                <a href="tel:{{ $company->phone }}" class="text-gray-900 hover:text-blue-600 transition-colors">
                                    {{ $company->phone }}
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($company->website)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-1">Website</p>
                                <a href="{{ $company->website }}" target="_blank" class="text-gray-900 hover:text-blue-600 transition-colors break-words">
                                    {{ str_replace(['http://', 'https://'], '', $company->website) }}
                                </a>
                            </div>
                        </div>
                        @endif

                        @if($company->location)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-1">Lokasi</p>
                                <p class="text-gray-900">{{ $company->location }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Social Media -->
                @if($company->linkedin || $company->twitter || $company->facebook || $company->instagram)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.1s;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        Media Sosial
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @if($company->linkedin)
                        <a href="{{ $company->linkedin }}"
                           target="_blank"
                           class="flex items-center gap-2 px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                            LinkedIn
                        </a>
                        @endif

                        @if($company->twitter)
                        <a href="{{ $company->twitter }}"
                           target="_blank"
                           class="flex items-center gap-2 px-3 py-2 bg-sky-50 text-sky-700 rounded-lg hover:bg-sky-100 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"/>
                            </svg>
                            Twitter
                        </a>
                        @endif

                        @if($company->facebook)
                        <a href="{{ $company->facebook }}"
                           target="_blank"
                           class="flex items-center gap-2 px-3 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z" clip-rule="evenodd"/>
                            </svg>
                            Facebook
                        </a>
                        @endif

                        @if($company->instagram)
                        <a href="{{ $company->instagram }}"
                           target="_blank"
                           class="flex items-center gap-2 px-3 py-2 bg-pink-50 text-pink-700 rounded-lg hover:bg-pink-100 transition-colors duration-200 text-sm">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 0C7.284 0 6.944.012 5.877.06 4.813.109 4.086.277 3.45.525a4.902 4.902 0 00-1.772 1.153A4.902 4.902 0 00.525 3.45C.277 4.086.109 4.813.06 5.877.012 6.944 0 7.284 0 10s.012 3.056.06 4.123c.049 1.064.217 1.791.465 2.427a4.902 4.902 0 001.153 1.772 4.902 4.902 0 001.772 1.153c.636.248 1.363.416 2.427.465C6.944 19.988 7.284 20 10 20s3.056-.012 4.123-.06c1.064-.049 1.791-.217 2.427-.465a4.902 4.902 0 001.772-1.153 4.902 4.902 0 001.153-1.772c.248-.636.416-1.363.465-2.427.048-1.067.06-1.407.06-4.123s-.012-3.056-.06-4.123c-.049-1.064-.217-1.791-.465-2.427a4.902 4.902 0 00-1.153-1.772A4.902 4.902 0 0016.55.525C15.914.277 15.187.109 14.123.06 13.056.012 12.716 0 10 0zm0 1.802c2.67 0 2.986.01 4.04.058.976.045 1.505.207 1.858.344.466.181.8.398 1.15.748.35.35.567.684.748 1.15.137.353.3.882.344 1.857.048 1.055.058 1.37.058 4.041 0 2.67-.01 2.986-.058 4.04-.045.976-.207 1.505-.344 1.858a3.097 3.097 0 01-.748 1.15c-.35.35-.684.567-1.15.748-.353.137-.882.3-1.857.344-1.054.048-1.37.058-4.041.058-2.67 0-2.987-.01-4.04-.058-.976-.045-1.505-.207-1.858-.344a3.097 3.097 0 01-1.15-.748 3.097 3.097 0 01-.748-1.15c-.137-.353-.3-.882-.344-1.857-.048-1.055-.058-1.37-.058-4.041 0-2.67.01-2.986.058-4.04.045-.976.207-1.505.344-1.858.181-.466.398-.8.748-1.15.35-.35.684-.567 1.15-.748.353-.137.882-.3 1.857-.344 1.055-.048 1.37-.058 4.041-.058zm0 12.896a4.698 4.698 0 110-9.396 4.698 4.698 0 010 9.396zm0-7.594a2.896 2.896 0 100 5.792 2.896 2.896 0 000-5.792zm5.228-.456a1.1 1.1 0 11-2.2 0 1.1 1.1 0 012.2 0z" clip-rule="evenodd"/>
                            </svg>
                            Instagram
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Statistics -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.2s;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Statistik</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <span class="text-gray-600 text-sm">Lowongan Aktif</span>
                            <span class="text-2xl font-bold text-blue-600">{{ $stats['active_jobs'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Total Lowongan</span>
                            <span class="text-2xl font-bold text-purple-600">{{ $stats['total_jobs'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
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

/* reduced motion support untuk aksesibilitas */
@media (prefers-reduced-motion: reduce) {
    .fade-in-up {
        animation: none;
        opacity: 1;
    }
}
</style>
@endsection
