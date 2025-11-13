{{-- resources/views/student/repository/show.blade.php --}}
@extends('layouts.app')

@section('title', $document->title)

@push('styles')
<style>
/* animasi fade in */
.fade-in-up {
    opacity: 0;
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

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

.fade-in-up:nth-child(1) { animation-delay: 0s; }
.fade-in-up:nth-child(2) { animation-delay: 0.1s; }
.fade-in-up:nth-child(3) { animation-delay: 0.2s; }
.fade-in-up:nth-child(4) { animation-delay: 0.3s; }
.fade-in-up:nth-child(5) { animation-delay: 0.4s; }

/* related document card */
.related-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e5e7eb;
}

.related-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
    border-color: #9333ea;
}

/* download button animation */
.download-btn {
    transition: all 0.3s ease;
}

.download-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(147, 51, 234, 0.3);
}

/* accessibility - prefers reduced motion */
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
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- breadcrumb --}}
        <nav class="mb-6 fade-in-up">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="{{ route('student.repository.index') }}" class="hover:text-blue-600 transition-colors">Repository</a></li>
                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                <li class="text-gray-900 font-semibold truncate">{{ Str::limit($document->title, 50) }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- main content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- document header --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 fade-in-up" style="animation-delay: 0.1s;">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $document->title }}</h1>
                            <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $document->author_name ?? 'Unknown' }}
                                </span>
                                @if($document->year)
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $document->year }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- categories --}}
                    @if($document->categories)
                    <div class="flex flex-wrap gap-2 mb-6">
                        @php
                            $categories = is_array($document->categories) 
                                ? $document->categories 
                                : json_decode($document->categories, true) ?? [];
                        @endphp
                        @foreach($categories as $category)
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm rounded-full">
                            {{ ucwords(str_replace('_', ' ', $category)) }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                    
                    {{-- description --}}
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $document->description }}</p>
                    </div>
                    
                    {{-- stats --}}
                    <div class="flex items-center gap-6 mt-6 pt-6 border-t border-gray-200 text-sm text-gray-600">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ $document->view_count }} views
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            {{ $document->download_count }} downloads
                        </span>
                    </div>
                </div>

                {{-- document info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 fade-in-up" style="animation-delay: 0.2s;">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Dokumen</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        @if($document->institution_name)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Instansi</p>
                            <p class="font-semibold text-gray-900">{{ $document->institution_name }}</p>
                        </div>
                        @endif
                        
                        @if($document->province)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Provinsi</p>
                            <p class="font-semibold text-gray-900">{{ $document->province->name }}</p>
                        </div>
                        @endif
                        
                        @if($document->regency)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Kabupaten/Kota</p>
                            <p class="font-semibold text-gray-900">{{ $document->regency->name }}</p>
                        </div>
                        @endif
                        
                        @if($document->university_name)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Universitas</p>
                            <p class="font-semibold text-gray-900">{{ $document->university_name }}</p>
                        </div>
                        @endif
                        
                        @if($document->file_type)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Tipe File</p>
                            <p class="font-semibold text-gray-900 uppercase">{{ $document->file_type }}</p>
                        </div>
                        @endif
                        
                        @if($document->file_size)
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Ukuran File</p>
                            <p class="font-semibold text-gray-900">{{ number_format($document->file_size / 1024, 2) }} KB</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- download and view buttons --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 fade-in-up" style="animation-delay: 0.3s;">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Akses Dokumen</h2>
                    <p class="text-gray-600 mb-6">Lihat dokumen secara online atau unduh untuk akses offline</p>
                    <div class="flex flex-wrap gap-3">
                        {{-- View Online Button --}}
                        @if($document->file_path)
                        <a href="{{ document_url($document->file_path) }}"
                           target="_blank"
                           class="download-btn inline-flex items-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <span>Lihat Online</span>
                        </a>
                        @endif

                        {{-- Download Button --}}
                        <button onclick="downloadDocument({{ $document->id }}, '{{ addslashes($document->title) }}')"
                           class="download-btn inline-flex items-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                           id="download-btn-{{ $document->id }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            <span class="download-text">Unduh Dokumen ({{ strtoupper($document->file_type ?? 'PDF') }})</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- related documents --}}
                @if($relatedDocuments->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.4s;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Dokumen Terkait</h3>
                    <div class="space-y-4">
                        @foreach($relatedDocuments as $relatedDoc)
                        <a href="{{ route('student.repository.show', $relatedDoc->id) }}"
                           class="related-card block p-4 rounded-lg bg-gray-50">
                            <h4 class="font-semibold text-gray-900 mb-2 hover:text-purple-600 transition-colors">
                                {{ Str::limit($relatedDoc->title, 60) }}
                            </h4>
                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ $relatedDoc->view_count }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    {{ $relatedDoc->download_count }}
                                </span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- back to repository --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.5s;">
                    <a href="{{ route('student.repository.index') }}"
                       class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Kembali Ke Repository</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
/**
 * Download document without page refresh
 */
async function downloadDocument(documentId, documentTitle) {
    const button = document.getElementById(`download-btn-${documentId}`);
    const originalText = button.querySelector('.download-text').textContent;

    // Disable button dan show loading state
    button.disabled = true;
    button.querySelector('.download-text').textContent = 'Mengunduh...';

    try {
        // Fetch file dari server
        const response = await fetch(`/student/repository/${documentId}/download`, {
            method: 'GET',
            headers: {
                'Accept': 'application/octet-stream',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Download failed');
        }

        // Get filename from Content-Disposition header or use default
        let filename = `${documentTitle}.pdf`;
        const contentDisposition = response.headers.get('Content-Disposition');
        if (contentDisposition) {
            const filenameMatch = contentDisposition.match(/filename="?(.+)"?/i);
            if (filenameMatch && filenameMatch[1]) {
                filename = filenameMatch[1];
            }
        }

        // Convert response to blob
        const blob = await response.blob();

        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = filename;

        // Trigger download
        document.body.appendChild(a);
        a.click();

        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);

        // Show success message
        showNotification('Dokumen berhasil diunduh!', 'success');

    } catch (error) {
        console.error('Download error:', error);
        showNotification('Gagal mengunduh dokumen. Silakan coba lagi.', 'error');
    } finally {
        // Re-enable button
        button.disabled = false;
        button.querySelector('.download-text').textContent = originalText;
    }
}

/**
 * Show notification toast
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-in ${
        type === 'success' ? 'bg-purple-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-purple-500 text-white'
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