{{-- resources/views/institution/profile/public.blade.php --}}
@extends('layouts.app')

@section('title', $institution->name . ' - Profil Publik')

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

.problem-card {
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.problem-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    border-color: #10b981;
}

.hero-gradient {
    background: linear-gradient(135deg, #6ee7b7 0%, #14b8a6 50%, #10b981 100%);
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    
    {{-- hero section dengan gradient hijau --}}
    <div class="hero-gradient py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center gap-8 profile-container">
                
                {{-- logo instansi --}}
                <div class="flex-shrink-0">
                    @if($institution->logo_path)
                        <img src="{{ $institution->getLogoUrl() }}" 
                             alt="{{ $institution->name }}" 
                             class="w-40 h-40 rounded-full object-cover border-4 border-white shadow-xl">
                    @else
                        <div class="w-40 h-40 rounded-full bg-white flex items-center justify-center text-emerald-600 text-5xl font-bold border-4 border-white shadow-xl">
                            {{ strtoupper(substr($institution->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                
                {{-- info utama --}}
                <div class="flex-1 text-center md:text-left text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-2">
                        {{ $institution->name }}
                    </h1>
                    <p class="text-xl opacity-90 mb-4">
                        {{ ucwords(str_replace('_', ' ', $institution->type)) }} • {{ $institution->regency->name }}, {{ $institution->province->name }}
                    </p>
                    
                    @if($institution->description)
                        <p class="text-lg opacity-80 max-w-2xl">{{ $institution->description }}</p>
                    @endif

                    @if($institution->website)
                    <div class="mt-4">
                        <a href="{{ $institution->website }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            <span>Kunjungi Website</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- main content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        {{-- statistik cards --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-12 profile-container" style="animation-delay: 0.1s;">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_problems'] }}</p>
                        <p class="text-sm text-gray-600">Total Masalah</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['active_problems'] }}</p>
                        <p class="text-sm text-gray-600">Masalah Aktif</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['completed_problems'] }}</p>
                        <p class="text-sm text-gray-600">Masalah Selesai</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['active_projects'] }}</p>
                        <p class="text-sm text-gray-600">Proyek Berjalan</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-lime-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-lime-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['completed_projects'] }}</p>
                        <p class="text-sm text-gray-600">Proyek Selesai</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- masalah yang dipublikasikan --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-12 profile-container" style="animation-delay: 0.2s;">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Masalah Yang Dipublikasikan</h2>
            
            @if($institution->problems->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500">Belum Ada Masalah Yang Dipublikasi</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($institution->problems as $problem)
                    <a href="{{ route('student.browse-problems.show', $problem->id) }}" class="block bg-gray-50 rounded-lg p-4 hover:shadow-md transition-all duration-300 border border-gray-100 problem-card">
                        @if($problem->coverImage)
                            <img src="{{ $problem->coverImage->image_url }}" 
                                alt="{{ $problem->title }}"
                                onerror="this.onerror=null; this.src='https://via.placeholder.com/400x200?text=No+Image';"
                                class="w-full h-40 object-cover rounded-lg mb-4">
                        @endif
                        
                        <h3 class="font-bold text-gray-900 mb-2 line-clamp-2">{{ $problem->title }}</h3>
                        
                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $problem->regency->name }}</span>
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                {{ $problem->status === 'open' ? 'bg-green-100 text-green-800' : 
                                ($problem->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $problem->status)) }}
                            </span>
                            
                            <span class="text-sm text-gray-500">
                                {{ $problem->applications_count ?? 0 }} aplikasi
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection