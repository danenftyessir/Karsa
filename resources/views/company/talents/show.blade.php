@extends('layouts.app')

@section('title', 'Detail Talenta - ' . $talent->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50" x-data="talentDetail()">
    <!-- Header Section -->
    <div class="relative bg-cover bg-center text-white py-12 overflow-hidden" style="background-image: url('{{ asset('company5.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex items-center gap-4 mb-6 fade-in-up">
                <a href="{{ route('company.talents.index') }}"
                   class="text-white hover:text-blue-100 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold" style="font-family: 'Space Grotesk', sans-serif;">
                    Profil Talenta
                </h1>
            </div>

            <!-- Talent Quick Info -->
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6 fade-in-up" style="animation-delay: 0.1s;">
                <!-- Avatar -->
                <div class="relative">
                    @if($talent->profile && $talent->profile->avatar_url)
                        <img src="{{ $talent->profile->avatar_url }}"
                             alt="{{ $talent->name }}"
                             class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-lg">
                    @else
                        <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center border-4 border-white shadow-lg">
                            @php
                                $nameParts = explode(' ', $talent->name);
                                $initials = strtoupper(substr($nameParts[0], 0, 1) . (count($nameParts) > 1 ? substr($nameParts[count($nameParts)-1], 0, 1) : ''));
                            @endphp
                            <span class="text-white text-3xl font-bold">{{ $initials }}</span>
                        </div>
                    @endif
                    @if($talent->is_verified)
                        <div class="absolute -bottom-2 -right-2 bg-blue-500 rounded-full p-1">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-2">{{ $talent->name }}</h2>
                    <p class="text-blue-100 text-lg mb-3">
                        {{ $talent->profile->title ?? 'Talenta' }}
                    </p>
                    <div class="flex flex-wrap gap-3">
                        @if($talent->profile && $talent->profile->location)
                            <span class="flex items-center gap-1 text-sm bg-white/20 px-3 py-1 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $talent->profile->location }}
                            </span>
                        @endif
                        @if($talent->email)
                            <span class="flex items-center gap-1 text-sm bg-white/20 px-3 py-1 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $talent->email }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col gap-3">
                    <button @click="toggleSave()"
                            class="flex items-center gap-2 px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover-lift"
                            :class="isSaved ? 'bg-yellow-500 text-white' : 'bg-white text-blue-600'">
                        <svg class="w-5 h-5" :fill="isSaved ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                        <span x-text="isSaved ? 'Tersimpan' : 'Simpan Talenta'"></span>
                    </button>
                    <a href="mailto:{{ $talent->email }}"
                       class="flex items-center justify-center gap-2 px-6 py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition-all duration-300 hover-lift">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Hubungi
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - About & Skills -->
            <div class="lg:col-span-2 space-y-6">
                <!-- About -->
                @if($talent->profile && $talent->profile->bio)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Tentang
                    </h3>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $talent->profile->bio }}</p>
                </div>
                @endif

                <!-- Skills -->
                @if($talent->profile && $talent->profile->skills && count($talent->profile->skills) > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.1s;">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        Keahlian
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($talent->profile->skills as $skill)
                            <span class="px-4 py-2 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium border border-purple-100">
                                {{ $skill }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Experience -->
                @if($talent->experiences && count($talent->experiences) > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.2s;">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Pengalaman
                    </h3>
                    <div class="space-y-4">
                        @foreach($talent->experiences as $experience)
                            <div class="border-l-2 border-green-200 pl-4">
                                <h4 class="font-semibold text-gray-900">{{ $experience->title }}</h4>
                                <p class="text-gray-600 text-sm">{{ $experience->company }}</p>
                                <p class="text-gray-500 text-sm">
                                    {{ $experience->start_date ? \Carbon\Carbon::parse($experience->start_date)->format('M Y') : '' }} -
                                    {{ $experience->is_current ? 'Sekarang' : ($experience->end_date ? \Carbon\Carbon::parse($experience->end_date)->format('M Y') : '') }}
                                </p>
                                @if($experience->description)
                                    <p class="text-gray-700 text-sm mt-2 whitespace-pre-line">{{ $experience->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Education -->
                @if($talent->educations && count($talent->educations) > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.3s;">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Pendidikan
                    </h3>
                    <div class="space-y-4">
                        @foreach($talent->educations as $education)
                            <div class="border-l-2 border-indigo-200 pl-4">
                                <h4 class="font-semibold text-gray-900">{{ $education->institution }}</h4>
                                <p class="text-gray-600 text-sm">{{ $education->degree }} - {{ $education->field_of_study }}</p>
                                <p class="text-gray-500 text-sm">
                                    {{ $education->start_date ? \Carbon\Carbon::parse($education->start_date)->format('Y') : '' }} -
                                    {{ $education->is_current ? 'Sekarang' : ($education->end_date ? \Carbon\Carbon::parse($education->end_date)->format('Y') : '') }}
                                </p>
                                @if($education->description)
                                    <p class="text-gray-700 text-sm mt-2 whitespace-pre-line">{{ $education->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Stats & Info -->
            <div class="space-y-6">
                <!-- Impact Score -->
                @if($talent->impact_score)
                <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white fade-in-up gpu-accelerate" style="animation-delay: 0.1s;">
                    <h3 class="text-lg font-bold mb-3">Impact Score</h3>
                    <div class="flex items-end gap-2 mb-2">
                        <span class="text-5xl font-bold">{{ number_format($talent->impact_score, 1) }}</span>
                        <span class="text-2xl mb-2">/100</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-white rounded-full h-2 transition-all duration-500"
                             style="width: {{ $talent->impact_score }}%"></div>
                    </div>
                </div>
                @endif

                <!-- Quick Stats -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.2s;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Statistik</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <span class="text-gray-600 text-sm">Total Proyek</span>
                            <span class="text-gray-900 font-semibold">{{ $talent->projects_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <span class="text-gray-600 text-sm">Total Sertifikat</span>
                            <span class="text-gray-900 font-semibold">{{ $talent->certifications_count ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <span class="text-gray-600 text-sm">Bergabung Sejak</span>
                            <span class="text-gray-900 font-semibold">{{ $talent->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Status</span>
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                {{ $talent->profile->availability ?? 'Tersedia' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Links -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.3s;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tautan</h3>
                    <div class="space-y-3">
                        @if($talent->profile && $talent->profile->linkedin_url)
                            <a href="{{ $talent->profile->linkedin_url }}"
                               target="_blank"
                               class="flex items-center gap-3 text-gray-700 hover:text-blue-600 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"/>
                                </svg>
                                LinkedIn
                            </a>
                        @endif
                        @if($talent->profile && $talent->profile->github_url)
                            <a href="{{ $talent->profile->github_url }}"
                               target="_blank"
                               class="flex items-center gap-3 text-gray-700 hover:text-purple-600 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd"/>
                                </svg>
                                GitHub
                            </a>
                        @endif
                        @if($talent->profile && $talent->profile->portfolio_url)
                            <a href="{{ $talent->profile->portfolio_url }}"
                               target="_blank"
                               class="flex items-center gap-3 text-gray-700 hover:text-green-600 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                Portfolio
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function talentDetail() {
    return {
        isSaved: {{ $isSaved ? 'true' : 'false' }},

        async toggleSave() {
            try {
                const response = await fetch('{{ route("company.talents.toggle-save", $talent->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.isSaved = data.is_saved;
                } else {
                    alert('Gagal menyimpan talenta');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        }
    }
}
</script>

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

/* hover effect untuk lift */
.hover-lift {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translate3d(0, -4px, 0);
    box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.2);
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
