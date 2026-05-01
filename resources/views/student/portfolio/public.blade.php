@extends('layouts.app')

@section('title', $student->first_name . ' ' . $student->last_name . ' - Portfolio')

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

.portfolio-container {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.project-card {
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.project-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    border-color: #3b82f6;
}

.skill-tag {
    transition: all 0.2s ease;
}

.skill-tag:hover {
    transform: scale(1.05);
    background-color: #3b82f6;
    color: white;
}

.hero-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- hero section dengan gradient -->
    <div class="hero-gradient py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center gap-8 portfolio-container">
                <div class="flex-shrink-0">
                    @if($student->profile_photo_path)
                        <img src="{{ $student->profile_photo_url }}" 
                             alt="{{ $student->first_name }}" 
                             class="w-40 h-40 rounded-full object-cover border-4 border-white shadow-xl">
                    @else
                        <div class="w-40 h-40 rounded-full bg-white flex items-center justify-center text-purple-600 text-5xl font-bold border-4 border-white shadow-xl">
                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                
                <!-- info utama -->
                <div class="flex-1 text-center md:text-left text-white">
                    <h1 class="text-4xl md:text-5xl font-bold mb-2">
                        {{ $student->first_name }} {{ $student->last_name }}
                    </h1>
                    <p class="text-xl opacity-90 mb-4">
                        {{ $student->major }} • {{ $student->university->name ?? '' }}
                    </p>
                    
                    <!-- semester & nim -->
                    <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-white bg-opacity-20 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Semester {{ $student->semester }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-white bg-opacity-20 text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            {{ $student->nim }}
                        </span>
                    </div>
                    
                    @if($student->bio)
                        <p class="mt-6 text-lg opacity-90 leading-relaxed max-w-2xl">
                            {{ $student->bio }}
                        </p>
                    @endif
                    
                    <!-- share button -->
                    <div class="mt-6">
                        <button onclick="shareProfile()" class="inline-flex items-center px-6 py-3 bg-white text-purple-600 rounded-lg hover:bg-opacity-90 transition-all shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                            </svg>
                            Share Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- statistik cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 pb-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
            <div class="bg-white rounded-xl shadow-lg p-6 text-center border-2 border-blue-100">
                <p class="text-4xl font-bold text-blue-600 mb-2">{{ $statistics['completed_projects'] }}</p>
                <p class="text-sm text-gray-600">proyek selesai</p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center border-2 border-green-100">
                <p class="text-4xl font-bold text-green-600 mb-2">{{ $statistics['sdgs_addressed'] }}</p>
                <p class="text-sm text-gray-600">sdgs disentuh</p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center border-2 border-yellow-100">
                <p class="text-4xl font-bold text-yellow-600 mb-2">{{ $statistics['positive_reviews'] }}</p>
                <p class="text-sm text-gray-600">ulasan positif</p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center border-2 border-purple-100">
                <p class="text-4xl font-bold text-purple-600 mb-2">{{ number_format($statistics['average_rating'], 1) }} ⭐</p>
                <p class="text-sm text-gray-600">rating rata-rata</p>
            </div>
        </div>

        <!-- proyek yang diselesaikan -->
        @if($completed_projects && $completed_projects->count() > 0)
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Proyek yang Diselesaikan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($completed_projects as $project)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden project-card">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <h3 class="text-xl font-bold text-gray-900 flex-1">{{ $project->title }}</h3>
                                    @if($project->institutionReview)
                                        <div class="flex items-center ml-4">
                                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <span class="ml-1 text-gray-700 font-semibold">{{ number_format($project->institutionReview->rating, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <p class="text-gray-600 mb-4">{{ Str::limit($project->description, 120) }}</p>
                                
                                <div class="space-y-2 text-sm">
                                    <div class="flex items-center text-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ $project->problem->institution->name }}
                                    </div>
                                    <div class="flex items-center text-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $project->start_date->format('M Y') }} - {{ $project->end_date->format('M Y') }}
                                    </div>
                                    <div class="flex items-center text-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $project->problem->regency->name }}, {{ $project->problem->province->name }}
                                    </div>
                                </div>
                                
                                @if($project->institutionReview && $project->institutionReview->review_text)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-sm text-gray-600 italic">"{{ Str::limit($project->institutionReview->review_text, 100) }}"</p>
                                        <p class="text-xs text-gray-500 mt-1">- {{ $project->problem->institution->name }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg">Belum ada proyek yang diselesaikan</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function shareProfile() {
    const profileUrl = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: 'Portfolio {{ $student->first_name }} {{ $student->last_name }}',
            text: 'Lihat portfolio saya',
            url: profileUrl
        });
    } else {
        navigator.clipboard.writeText(profileUrl).then(() => {
            alert('Link profil berhasil disalin!');
        });
    }
}
</script>
@endpush
@endsection