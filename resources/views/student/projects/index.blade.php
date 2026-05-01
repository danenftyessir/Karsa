{{-- resources/views/student/projects/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Proyek Saya')

@push('styles')
{{-- Import Google Font - Space Grotesk for Hero --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

<style>
    /* Hero section style mirip applications */
    .marketplace-hero-projects {
        position: relative;
        background-image:
            linear-gradient(135deg, rgba(99, 102, 241, 0.35) 0%, rgba(129, 140, 248, 0.30) 50%, rgba(156, 163, 175, 0.25) 100%),
            url('/projects-student.jpeg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 480px;
    }

    .hero-title-projects {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .text-shadow-strong {
        text-shadow:
            0 2px 4px rgba(0, 0, 0, 0.4),
            0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .project-fade-in {
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

    /* Background style seperti applications */
    .gradient-mesh-bg {
        background-color: #ffffff;
        background-image:
            radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.08) 0px, transparent 50%),
            radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.08) 0px, transparent 50%),
            radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.08) 0px, transparent 50%),
            radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.08) 0px, transparent 50%);
    }

    /* Stats cards dengan glassmorphism */
    .stats-card-projects {
        background: rgba(255, 255, 255, 0.20);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
    }

    .stats-card-projects:hover {
        background: rgba(255, 255, 255, 0.30);
        transform: translate3d(0, -4px, 0);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
    }

    .stats-card-projects svg {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
    }

    /* smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* GPU acceleration untuk performa */
    .stats-card-projects {
        transform: translateZ(0);
        backface-visibility: hidden;
        perspective: 1000px;
    }

    /* accessibility - prefers reduced motion */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }

        .stats-card-projects:hover {
            transform: none;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen gradient-mesh-bg">

    {{-- marketplace-style hero section mirip applications --}}
    <section class="marketplace-hero-projects text-white relative flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10 w-full py-12">
            <div class="max-w-4xl mx-auto text-center">
                <div class="project-fade-in">
                    {{-- Judul dan deskripsi --}}
                    <h1 class="hero-title-projects text-4xl md:text-6xl font-bold mb-6 text-white leading-tight" style="color: white !important;">
                        Proyek Saya
                    </h1>

                    <p class="text-lg md:text-xl leading-relaxed max-w-2xl mx-auto font-medium" style="color: #ffffff !important; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5), 0 4px 12px rgba(0, 0, 0, 0.4);">
                        Kelola dan pantau progress proyek KKN Anda
                    </p>
                </div>
            </div>
        </div>

        {{-- straight divider --}}
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-white"></div>
    </section>

    {{-- main content area --}}
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-12">

        {{-- filter section --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <form method="GET" action="{{ route('student.projects.index') }}" class="flex flex-wrap gap-4">
                {{-- status filter --}}
                <select name="status" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="on_hold" {{ request('status') == 'on_hold' ? 'selected' : '' }}>Ditunda</option>
                </select>

                {{-- sort --}}
                <select name="sort" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                </select>

                {{-- submit button --}}
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                    Filter
                </button>

                {{-- reset --}}
                @if(request()->hasAny(['status', 'sort']))
                <a href="{{ route('student.projects.index') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium text-center">
                    Reset
                </a>
                @endif
            </form>
        </div>

        {{-- projects list --}}
        <div class="space-y-4">
            @forelse($projects as $project)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-300">
                <div class="flex items-start gap-6">
                    {{-- project image/cover --}}
                    <div class="flex-shrink-0">
                        @php
                            $coverImage = $project->problem->cover_image;
                        @endphp
                        @if($coverImage)
                            <img src="{{ $coverImage->image_url }}"
                                 alt="{{ $project->title }}"
                                 class="w-24 h-24 rounded-lg object-cover shadow-lg"
                                 onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center shadow-lg" style="display:none;">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @else
                            <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center shadow-lg">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- project info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $project->title }}</h3>
                                <div class="flex items-center text-gray-600 mb-2">
                                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span>{{ $project->institution->name }}</span>
                                </div>
                            </div>

                            {{-- status badge --}}
                            <div class="flex flex-col gap-2 items-end">
                                @if($project->status === 'active')
                                    <span class="px-4 py-2 bg-green-100 text-green-800 text-sm font-semibold rounded-full">Aktif</span>
                                @elseif($project->status === 'completed')
                                    <span class="px-4 py-2 bg-indigo-100 text-indigo-800 text-sm font-semibold rounded-full">Selesai</span>
                                @elseif($project->status === 'on_hold')
                                    <span class="px-4 py-2 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">Ditunda</span>
                                @else
                                    <span class="px-4 py-2 bg-gray-100 text-gray-800 text-sm font-semibold rounded-full">{{ ucfirst($project->status) }}</span>
                                @endif

                                @if($project->is_overdue && $project->status === 'active')
                                    <span class="px-4 py-2 bg-red-100 text-red-800 text-sm font-semibold rounded-full">Overdue</span>
                                @endif
                            </div>
                        </div>

                        {{-- AI Suggestion --}}
                        @if(isset($aiSuggestions[$project->id]))
                        <div class="mb-3 p-3 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-lg">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-xs font-semibold text-indigo-700 mb-1">Saran AI</p>
                                    <p class="text-sm text-gray-700">{{ $aiSuggestions[$project->id] }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- progress bar --}}
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span class="font-medium">Progress Proyek</span>
                                <span class="font-bold text-indigo-600">{{ $project->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-500"
                                     style="width: {{ $project->progress_percentage }}%"></div>
                            </div>
                        </div>

                        {{-- metadata --}}
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-3">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $project->start_date->format('d M Y') }} - {{ $project->end_date->format('d M Y') }}</span>
                            </div>
                        </div>

                        {{-- Team Members Display (if available) --}}
                        @if(isset($teamMembers) && $teamMembers->count() > 0)
                        <div class="mb-3">
                            <p class="text-xs font-medium text-gray-600 mb-2">Kolaborator Network</p>
                            <div class="flex items-center gap-2">
                                @foreach($teamMembers->take(4) as $member)
                                <a href="{{ route('student.friends.profile', $member->id) }}"
                                   class="group relative"
                                   title="{{ $member->full_name }}">
                                    <img src="{{ $member->profile_photo_url }}"
                                         alt="{{ $member->full_name }}"
                                         class="w-10 h-10 rounded-full border-2 border-white shadow-md group-hover:scale-110 transition-transform">
                                </a>
                                @endforeach
                                @if($teamMembers->count() > 4)
                                <a href="{{ route('student.friends.index') }}"
                                   class="w-10 h-10 rounded-full bg-gray-200 border-2 border-white shadow-md flex items-center justify-center hover:bg-gray-300 transition-colors">
                                    <span class="text-xs font-bold text-gray-600">+{{ $teamMembers->count() - 4 }}</span>
                                </a>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- actions --}}
                        <div class="flex items-center gap-3">
                            <a href="{{ route('student.projects.show', $project->id) }}"
                               class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium shadow-sm hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat Detail
                            </a>

                            {{-- Toggle Portfolio Button (only for completed projects) --}}
                            @if($project->status === 'completed')
                            <button onclick="togglePortfolio({{ $project->id }}, this)"
                                    data-project-id="{{ $project->id }}"
                                    data-is-visible="{{ $project->is_portfolio_visible ? 'true' : 'false' }}"
                                    class="portfolio-toggle-btn inline-flex items-center gap-2 px-4 py-2.5 rounded-lg transition-all text-sm font-medium shadow-sm hover:shadow-md {{ $project->is_portfolio_visible ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($project->is_portfolio_visible)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    @endif
                                </svg>
                                <span class="portfolio-text">
                                    {{ $project->is_portfolio_visible ? 'Di Portfolio' : 'Tambah ke Portfolio' }}
                                </span>
                            </button>
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
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Proyek</h3>
                <p class="text-gray-600 mb-6">Anda belum memiliki proyek yang sedang berjalan</p>
                <a href="{{ route('student.browse-problems.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari Proyek Baru
                </a>
            </div>
            @endforelse
        </div>

        {{-- pagination --}}
        @if($projects->hasPages())
        <div class="mt-6">
            {{ $projects->links() }}
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
    /**
     * Toggle portfolio visibility untuk proyek
     */
    function togglePortfolio(projectId, buttonElement) {
        const button = buttonElement;
        const originalText = button.querySelector('.portfolio-text').textContent;
        const originalHtml = button.innerHTML;

        // Disable button dan show loading
        button.disabled = true;
        button.classList.add('opacity-50', 'cursor-not-allowed');
        button.querySelector('.portfolio-text').textContent = 'Memproses...';

        // Send AJAX request
        fetch(`/student/projects/${projectId}/toggle-portfolio`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update button appearance
                if (data.is_visible) {
                    button.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    button.classList.add('bg-green-100', 'text-green-700', 'hover:bg-green-200');
                    button.querySelector('.portfolio-text').textContent = 'Di Portfolio';
                    button.querySelector('svg path').setAttribute('d', 'M5 13l4 4L19 7');
                } else {
                    button.classList.remove('bg-green-100', 'text-green-700', 'hover:bg-green-200');
                    button.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    button.querySelector('.portfolio-text').textContent = 'Tambah ke Portfolio';
                    button.querySelector('svg path').setAttribute('d', 'M12 4v16m8-8H4');
                }

                // Show success notification (optional - you can implement your own notification system)
                showNotification(data.message, 'success');
            } else {
                // Restore button
                button.innerHTML = originalHtml;
                showNotification(data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            button.innerHTML = originalHtml;
            showNotification('Terjadi kesalahan saat mengubah visibility portfolio', 'error');
        })
        .finally(() => {
            // Re-enable button
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
        });
    }

    /**
     * Show notification (simple implementation)
     */
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-in ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('opacity-0', 'transition-opacity');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
</script>

<style>
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
</style>
@endpush

@endsection