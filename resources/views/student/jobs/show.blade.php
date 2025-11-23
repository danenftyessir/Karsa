{{-- resources/views/student/jobs/show.blade.php --}}
@extends('layouts.app')

@section('title', $job->title . ' - Lowongan Kerja')

@push('styles')
<style>
    /* gpu acceleration */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .content-section {
        transform: translate3d(0, 0, 0);
        will-change: transform;
        backface-visibility: hidden;
    }

    /* job type badge */
    .job-type-badge {
        padding: 0.375rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .job-type-full_time { background-color: #dcfce7; color: #166534; }
    .job-type-part_time { background-color: #dbeafe; color: #1e40af; }
    .job-type-contract { background-color: #fef3c7; color: #92400e; }
    .job-type-internship { background-color: #f3e8ff; color: #7c3aed; }
    .job-type-freelance { background-color: #fce7f3; color: #be185d; }

    /* sticky apply card */
    .apply-card-sticky {
        position: sticky;
        top: 2rem;
        z-index: 10;
        align-self: flex-start;
    }

    /* similar job card */
    .similar-job-card {
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease;
    }

    .similar-job-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" x-data="jobDetailPage()">

    {{-- breadcrumb --}}
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-4">
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('student.dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('student.jobs.index') }}" class="text-gray-500 hover:text-gray-700">Lowongan</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 font-medium">{{ Str::limit($job->title, 30) }}</span>
            </nav>
        </div>
    </div>

    {{-- main content --}}
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- job details --}}
            <main class="flex-1">
                {{-- header --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 content-section">
                    <div class="flex items-start gap-4">
                        {{-- company logo --}}
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden">
                            @if($job->company && $job->company->logo_url)
                                <img src="{{ $job->company->logo_url }}" alt="{{ $job->company->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ $job->company->avatar_svg }}" alt="{{ $job->company->name ?? 'Company' }}" class="w-full h-full object-cover">
                            @endif
                        </div>

                        <div class="flex-1">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $job->title }}</h1>
                                    @if($job->company)
                                        <a href="{{ route('company.public', $job->company->id) }}"
                                           class="text-lg text-gray-600 hover:text-blue-600 mt-1 inline-block transition-colors">
                                            {{ $job->company->name }}
                                        </a>
                                    @else
                                        <p class="text-lg text-gray-600 mt-1">Company</p>
                                    @endif
                                </div>
                                <span class="job-type-badge job-type-{{ $job->job_type }}">
                                    {{ ucfirst(str_replace('_', ' ', $job->job_type)) }}
                                </span>
                            </div>

                            {{-- meta info --}}
                            <div class="flex flex-wrap items-center gap-4 mt-4 text-sm text-gray-600">
                                @if($job->location)
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <span>{{ $job->location }}</span>
                                </div>
                                @endif

                                @if($job->department)
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span>{{ $job->department }}</span>
                                </div>
                                @endif

                                @if($job->published_at)
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Diposting {{ $job->published_at->diffForHumans() }}</span>
                                </div>
                                @endif

                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>{{ number_format($job->views_count) }} views</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- description --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 content-section">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Deskripsi Pekerjaan</h2>
                    <div class="prose prose-gray max-w-none">
                        {!! nl2br(e($job->description)) !!}
                    </div>
                </div>

                {{-- responsibilities --}}
                @if($job->responsibilities)
                <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 content-section">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Tanggung Jawab</h2>
                    <div class="prose prose-gray max-w-none">
                        {!! nl2br(e($job->responsibilities)) !!}
                    </div>
                </div>
                @endif

                {{-- qualifications --}}
                @if($job->qualifications)
                <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 content-section">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Kualifikasi</h2>
                    <div class="prose prose-gray max-w-none">
                        {!! nl2br(e($job->qualifications)) !!}
                    </div>
                </div>
                @endif

                {{-- benefits --}}
                @if($job->benefits)
                <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 content-section">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Benefit</h2>
                    <div class="prose prose-gray max-w-none">
                        {!! nl2br(e($job->benefits)) !!}
                    </div>
                </div>
                @endif

                {{-- skills --}}
                @if($job->skills && count($job->skills) > 0)
                <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 content-section">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Skills Yang Dibutuhkan</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($job->skills as $skill)
                        <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full text-sm font-medium">
                            {{ $skill }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- sdg alignment --}}
                @if($job->sdg_alignment && count($job->sdg_alignment) > 0)
                <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 content-section">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">SDG Alignment</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($job->sdg_alignment as $sdgId)
                        <span class="px-3 py-1.5 bg-green-50 text-green-700 rounded-full text-sm font-medium">
                            SDG {{ $sdgId }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </main>

            {{-- sidebar --}}
            <aside class="w-full lg:w-80 flex-shrink-0">
                {{-- apply card --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6 apply-card-sticky">
                    @if($hasApplied)
                        <div class="text-center">
                            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Anda Sudah Melamar</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Status: <span class="font-medium text-blue-600">{{ ucfirst($application->status ?? 'New') }}</span>
                            </p>

                            @if($application && $application->status === 'new')
                            <form action="{{ route('student.jobs.withdraw', $job->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin membatalkan lamaran?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full px-4 py-2.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                    Batalkan Lamaran
                                </button>
                            </form>
                            @endif
                        </div>
                    @else
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Lamar Pekerjaan Ini</h3>

                        <form action="{{ route('student.jobs.apply', $job->id) }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cover Letter</label>
                                <textarea name="cover_letter"
                                          rows="4"
                                          placeholder="Ceritakan mengapa Anda cocok untuk posisi ini..."
                                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Link Resume (Opsional)</label>
                                <input type="url"
                                       name="resume_url"
                                       placeholder="https://..."
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Link Portfolio (Opsional)</label>
                                <input type="url"
                                       name="portfolio_url"
                                       placeholder="https://..."
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            </div>

                            <button type="submit"
                                    class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                Kirim Lamaran
                            </button>
                        </form>
                    @endif
                </div>

                {{-- company info --}}
                @if($job->company)
                <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Tentang Perusahaan</h3>

                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center overflow-hidden">
                            @if($job->company->logo_url)
                                <img src="{{ $job->company->logo_url }}" alt="{{ $job->company->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ $job->company->avatar_svg }}" alt="{{ $job->company->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $job->company->name }}</p>
                            @if($job->company->industry)
                            <p class="text-sm text-gray-600">{{ $job->company->industry }}</p>
                            @endif
                        </div>
                    </div>

                    @if($job->company->description)
                    <p class="text-sm text-gray-600 mb-4">{{ Str::limit($job->company->description, 150) }}</p>
                    @endif

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('company.public', $job->company->id) }}"
                           class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Lihat Profil Perusahaan
                        </a>

                        @if($job->company->website)
                        <a href="{{ $job->company->website }}"
                           target="_blank"
                           class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Kunjungi Website
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- similar jobs --}}
                @if($similarJobs->count() > 0)
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Lowongan Serupa</h3>

                    <div class="space-y-3">
                        @foreach($similarJobs as $similarJob)
                        <a href="{{ route('student.jobs.show', $similarJob->id) }}"
                           class="block p-3 rounded-xl border border-gray-100 similar-job-card hover:border-blue-200">
                            <p class="font-medium text-gray-900 text-sm">{{ $similarJob->title }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $similarJob->company->name ?? 'Company' }}</p>
                            @if($similarJob->location)
                            <p class="text-xs text-gray-400 mt-1">{{ $similarJob->location }}</p>
                            @endif
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </aside>
        </div>
    </div>

    {{-- success/error messages --}}
    @if(session('success'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 5000)"
         x-transition
         class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg z-50">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 5000)"
         x-transition
         class="fixed bottom-6 right-6 bg-red-600 text-white px-6 py-3 rounded-xl shadow-lg z-50">
        {{ session('error') }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function jobDetailPage() {
    return {
        // state jika diperlukan
    }
}
</script>
@endpush
@endsection
