{{-- resources/views/student/portfolio/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- header dengan share button --}}
        <div class="mb-8 fade-in-up">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Portfolio Saya</h1>
                    <p class="text-gray-600">Showcase proyek dan pencapaian Anda</p>
                </div>
                <button onclick="sharePortfolio()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                    </svg>
                    Share Portfolio
                </button>
            </div>
        </div>

        {{-- profile header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8 fade-in-up" style="animation-delay: 0.1s;">
            <div class="h-32 bg-gradient-to-r from-blue-600 to-green-600"></div>
            <div class="px-8 pb-8">
                <div class="flex items-end justify-between -mt-16 mb-6">
                    <div class="flex items-end space-x-6">
                        @if($student->profile_photo_path)
                            <img src="{{ $student->profile_photo_url }}" 
                                 alt="{{ $student->user->name }}"
                                 class="w-32 h-32 rounded-xl border-4 border-white shadow-lg object-cover">
                        @else
                            <div class="w-32 h-32 rounded-xl border-4 border-white shadow-lg bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center">
                                <span class="text-white text-4xl font-bold">{{ strtoupper(substr($student->first_name, 0, 1)) }}</span>
                            </div>
                        @endif
                        <div class="mb-4">
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $student->first_name }} {{ $student->last_name }}</h2>
                            <p class="text-gray-600">{{ $student->university->name }}</p>
                            <p class="text-sm text-gray-500">{{ $student->major }} • Semester {{ $student->semester }}</p>
                        </div>
                    </div>
                    <a href="{{ route('portfolio.public', $student->user->username) }}" 
                       target="_blank"
                       class="mb-4 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Preview Public View
                    </a>
                </div>

                @if($student->bio)
                    <p class="text-gray-600 leading-relaxed mb-6">{{ $student->bio }}</p>
                @endif

                {{-- statistics --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-3xl font-bold text-blue-600">{{ $statistics['completed_projects'] }}</p>
                        <p class="text-sm text-gray-600 mt-1">Proyek Selesai</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-3xl font-bold text-green-600">{{ $statistics['sdgs_addressed'] }}</p>
                        <p class="text-sm text-gray-600 mt-1">SDGs Disentuh</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-3xl font-bold text-yellow-600">{{ $statistics['positive_reviews'] }}</p>
                        <p class="text-sm text-gray-600 mt-1">Ulasan Positif</p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <p class="text-3xl font-bold text-purple-600">{{ number_format($statistics['average_rating'], 1) }}</p>
                        <p class="text-sm text-gray-600 mt-1">Rating Rata-rata</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- completed projects --}}
        <div class="fade-in-up" style="animation-delay: 0.2s;">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Proyek yang Diselesaikan</h3>
            
            @if($completed_projects->isEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">Belum ada proyek yang diselesaikan</p>
                    <p class="text-gray-400 text-sm mt-2">Selesaikan proyek pertama Anda untuk mulai membangun portfolio</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($completed_projects as $project)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 mb-2">{{ $project->title }}</h4>
                                    <p class="text-sm text-gray-600 mb-3">{{ Str::limit($project->description, 150) }}</p>
                                    
                                    <div class="flex items-center gap-4 text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            {{ $project->problem->institution->name }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $project->start_date->format('M Y') }} - {{ $project->end_date->format('M Y') }}
                                        </span>
                                        @if($project->institutionReview)
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                {{ number_format($project->institutionReview->rating, 1) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-2 ml-4">
                                    <a href="{{ route('student.projects.show', $project->id) }}" 
                                       class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
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

.fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
@endpush

@push('scripts')
<script>
function sharePortfolio() {
    const portfolioUrl = "{{ route('portfolio.public', $student->user->username) }}";
    
    if (navigator.share) {
        navigator.share({
            title: 'Portfolio {{ $student->first_name }} {{ $student->last_name }}',
            text: 'Lihat portfolio saya',
            url: portfolioUrl
        });
    } else {
        // fallback: copy to clipboard
        navigator.clipboard.writeText(portfolioUrl).then(() => {
            alert('Link portfolio berhasil disalin!');
        });
    }
}
</script>
@endpush
@endsection