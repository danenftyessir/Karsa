{{-- resources/views/student/applications/show.blade.php --}}
{{-- halaman detail aplikasi mahasiswa --}}

@extends('layouts.app')

@section('title', 'Detail Aplikasi')

@push('styles')
<style>
/* detail page animations */
.detail-card {
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

.detail-card:nth-child(1) { animation-delay: 0s; }
.detail-card:nth-child(2) { animation-delay: 0.1s; }
.detail-card:nth-child(3) { animation-delay: 0.2s; }
.detail-card:nth-child(4) { animation-delay: 0.3s; }
.detail-card:nth-child(5) { animation-delay: 0.4s; }

/* status badge animations */
.status-badge {
    transition: all 0.3s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}

/* document preview hover */
.document-preview {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.document-preview:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* timeline styling */
.timeline-item {
    position: relative;
    padding-left: 32px;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: 9px;
    top: 36px;
    bottom: -16px;
    width: 2px;
    background: linear-gradient(to bottom, #3b82f6, transparent);
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-dot {
    position: absolute;
    left: 0;
    top: 8px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #3b82f6;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #dbeafe;
}

/* action button animations */
.action-btn {
    transition: all 0.3s ease;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- back button -->
        <div class="mb-6">
            <a href="{{ route('student.applications.index') }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali Ke Daftar Aplikasi
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- main content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- status card -->
                <div class="detail-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $application->problem->title }}</h1>
                            <p class="text-gray-600 mb-4">{{ $application->problem->institution->name }}</p>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $status = match($application->status) {
                                        'pending' => ['text' => 'Menunggu Review', 'bg' => 'bg-yellow-100', 'text-color' => 'text-yellow-800'],
                                        'reviewed' => ['text' => 'Sedang Direview', 'bg' => 'bg-blue-100', 'text-color' => 'text-blue-800'],
                                        'accepted' => ['text' => 'Diterima', 'bg' => 'bg-green-100', 'text-color' => 'text-green-800'],
                                        'rejected' => ['text' => 'Ditolak', 'bg' => 'bg-red-100', 'text-color' => 'text-red-800'],
                                        default => ['text' => 'Unknown', 'bg' => 'bg-gray-100', 'text-color' => 'text-gray-800'],
                                    };
                                @endphp
                                <span class="status-badge inline-flex items-center px-3 py-1 {{ $status['bg'] }} {{ $status['text-color'] }} text-sm font-semibold rounded-full">
                                    {{ $status['text'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- problem detail -->
                <div class="detail-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Detail Proyek</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Deskripsi</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $application->problem->description }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Lokasi</h3>
                            <p class="text-gray-700">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $application->problem->regency->name ?? $application->problem->location_regency }}, 
                                {{ $application->problem->province->name ?? $application->problem->location_province }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- timeline -->
                <div class="detail-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Timeline</h2>
                    <div class="space-y-6">
                        <!-- submitted -->
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div>
                                <p class="font-semibold text-gray-900">Aplikasi Diajukan</p>
                                <p class="text-sm text-gray-600">{{ $application->applied_at->format('d M Y, H:i') }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $application->applied_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <!-- reviewed -->
                        @if($application->reviewed_at)
                        <div class="timeline-item">
                            <div class="timeline-dot bg-blue-500" style="box-shadow: 0 0 0 3px #dbeafe;"></div>
                            <div>
                                <p class="font-semibold text-gray-900">Sedang Direview</p>
                                <p class="text-sm text-gray-600">{{ $application->reviewed_at->format('d M Y, H:i') }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $application->reviewed_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- accepted/rejected -->
                        @if($application->accepted_at)
                        <div class="timeline-item">
                            <div class="timeline-dot bg-green-500" style="box-shadow: 0 0 0 3px #dcfce7;"></div>
                            <div>
                                <p class="font-semibold text-gray-900">Aplikasi Diterima 🎉</p>
                                <p class="text-sm text-gray-600">{{ $application->accepted_at->format('d M Y, H:i') }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $application->accepted_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @elseif($application->rejected_at)
                        <div class="timeline-item">
                            <div class="timeline-dot bg-red-500" style="box-shadow: 0 0 0 3px #fee2e2;"></div>
                            <div>
                                <p class="font-semibold text-gray-900">Aplikasi Ditolak</p>
                                <p class="text-sm text-gray-600">{{ $application->rejected_at->format('d M Y, H:i') }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $application->rejected_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- motivasi -->
                <div class="detail-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Motivasi</h2>
                    <div class="prose prose-sm max-w-none text-gray-700 break-all">
                        {!! nl2br(e($application->motivation)) !!}
                    </div>
                </div>

                <!-- proposal document - DIGABUNG DI SINI, DIBAWAH MOTIVASI -->
                @if($application->proposal_content)
                <div class="detail-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Dokumen Proposal</h2>
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $application->proposal_filename ?? 'Proposal.pdf' }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $application->proposal_size_formatted ?? 'PDF Document' }} • 
                                Diupload {{ $application->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <a href="{{ route('student.applications.download-proposal', $application->id) }}" 
                           target="_blank"
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Lihat
                        </a>
                    </div>
                </div>
                @endif

                <!-- feedback from institution -->
                @if($application->feedback)
                <div class="detail-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Feedback Dari Instansi</h2>
                    <div class="p-4 {{ $application->status === 'accepted' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }} border rounded-lg">
                        <p class="text-sm {{ $application->status === 'accepted' ? 'text-green-700' : 'text-red-700' }}">
                            {!! nl2br(e($application->feedback)) !!}
                        </p>
                    </div>
                </div>
                @endif

            </div>

            <!-- sidebar -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- informasi aplikasi -->
                <div class="detail-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Aplikasi</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-600">ID Aplikasi</dt>
                            <dd class="text-sm font-semibold text-gray-900 mt-1">#{{ str_pad($application->id, 6, '0', STR_PAD_LEFT) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Tanggal Diajukan</dt>
                            <dd class="text-sm font-semibold text-gray-900 mt-1">{{ $application->applied_at->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 {{ $status['bg'] }} {{ $status['text-color'] }} text-xs font-semibold rounded-full">
                                    {{ $status['text'] }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Durasi Proyek</dt>
                            <dd class="text-sm font-semibold text-gray-900 mt-1">{{ $application->problem->duration_months }} Bulan</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Periode</dt>
                            <dd class="text-sm font-semibold text-gray-900 mt-1">
                                {{ $application->problem->start_date->format('M Y') }} - {{ $application->problem->end_date->format('M Y') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Deadline</dt>
                            <dd class="text-sm font-semibold text-red-600 mt-1">
                                {{ $application->problem->application_deadline->format('d M Y') }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- instansi info -->
                <div class="detail-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Instansi</h3>
                    <div class="flex items-center mb-4">
                        @if($application->problem->institution->logo_url)
                        <img src="{{ $application->problem->institution->logo_url }}" 
                             alt="{{ $application->problem->institution->name }}"
                             class="w-12 h-12 rounded-lg object-cover mr-3">
                        @else
                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900">{{ $application->problem->institution->name }}</p>
                            <p class="text-sm text-gray-600">{{ $application->problem->institution->type }}</p>
                        </div>
                    </div>
                    <a href="{{ route('student.browse-problems.show', $application->problem->id) }}" 
                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 transition-colors">
                        Lihat Detail Proyek
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                <!-- action buttons -->
                @if($application->status === 'pending')
                <div class="detail-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
                    <form action="{{ route('student.applications.withdraw', $application->id) }}" 
                          method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin membatalkan aplikasi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="action-btn w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
                            Batalkan Aplikasi
                        </button>
                    </form>
                    <p class="text-xs text-gray-500 mt-2">
                        Anda dapat membatalkan aplikasi selama masih dalam status pending
                    </p>
                </div>
                @endif

            </div>

        </div>
    </div>
</div>
@endsection