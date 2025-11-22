{{-- resources/views/institution/projects/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Proyek')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- breadcrumb --}}
        <nav class="mb-6">
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('institution.dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">Dashboard</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="{{ route('institution.projects.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">Proyek</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-900 font-medium">Detail</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- main content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- project header --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $project->title }}</h1>
                            <div class="flex items-center gap-2">
                                @if($project->status === 'active')
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Aktif</span>
                                @elseif($project->status === 'completed')
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Selesai</span>
                                @elseif($project->status === 'on_hold')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded-full">Ditahan</span>
                                @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">{{ ucfirst($project->status) }}</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('institution.projects.manage', $project->id) }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold text-sm">
                            Kelola Proyek
                        </a>
                    </div>

                    {{-- progress bar --}}
                    <div class="mt-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span class="font-medium">Progress Keseluruhan</span>
                            <span class="font-bold text-gray-900">{{ $progressPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" 
                                 style="width: {{ $progressPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- info mahasiswa --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Mahasiswa</h2>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-2xl">
                            {{ substr($project->student->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $project->student->user->name }}</h3>
                            <p class="text-gray-600">{{ $project->student->university->name }}</p>
                            <p class="text-sm text-gray-500">{{ $project->student->major }} - Semester {{ $project->student->semester }}</p>
                        </div>
                    </div>
                </div>

                {{-- deskripsi --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Deskripsi Proyek</h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $project->description }}</p>
                </div>

                {{-- milestones --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Milestones</h2>
                    
                    @forelse($project->milestones as $milestone)
                    <div class="mb-4 pb-4 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                        <div class="flex items-start gap-4">
                            {{-- status icon --}}
                            <div class="flex-shrink-0 mt-1">
                                @if($milestone->status === 'completed')
                                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                @elseif($milestone->status === 'in_progress')
                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                                @else
                                <div class="w-6 h-6 bg-gray-300 rounded-full"></div>
                                @endif
                            </div>
                            
                            {{-- milestone content --}}
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <h3 class="font-bold text-gray-900">{{ $milestone->title }}</h3>
                                        @if($milestone->description)
                                        <p class="text-sm text-gray-600 mt-1">{{ $milestone->description }}</p>
                                        @endif
                                    </div>
                                    
                                    {{-- status badge --}}
                                    @if($milestone->status === 'completed')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded">Selesai</span>
                                    @elseif($milestone->status === 'in_progress')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">Berjalan</span>
                                    @elseif($milestone->status === 'delayed')
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">Terlambat</span>
                                    @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded">Pending</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Target: {{ $milestone->target_date ? $milestone->target_date->format('d M Y') : '-' }}</span>
                                    @if($milestone->completed_at)
                                    <span class="ml-2">• Selesai: {{ $milestone->completed_at->format('d M Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-600 text-center py-4">Belum ada milestone</p>
                    @endforelse
                </div>

                {{-- reports --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Laporan Kemajuan</h2>
                    
                    @forelse($project->reports as $report)
                    <div class="mb-4 pb-4 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $report->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $report->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            
                            @if($report->status == 'approved')
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded">Disetujui</span>
                            @elseif($report->status == 'revision_required')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded">Perlu Revisi</span>
                            @else
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">Menunggu Review</span>
                            @endif
                        </div>
                        
                        @if($report->summary)
                        <p class="text-sm text-gray-700 mb-2">{{ Str::limit($report->summary, 150) }}</p>
                        @endif
                        
                        @if($report->document_path)
                        <a href="{{ document_url($report->document_path) }}"
                           target="_blank"
                           download
                           class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Download Dokumen →
                        </a>
                        @endif
                    </div>
                    @empty
                    <p class="text-gray-600 text-center py-4">Belum ada laporan</p>
                    @endforelse
                </div>

            </div>

            {{-- sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- stats card --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Statistik</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Progress</span>
                            <span class="font-bold text-gray-900">{{ $progressPercentage }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Milestones</span>
                            <span class="font-bold text-gray-900">
                                {{ $project->milestones->where('status', 'completed')->count() }}/{{ $project->milestones->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Laporan</span>
                            <span class="font-bold text-gray-900">{{ $project->reports->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- timeline --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Timeline</h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-semibold">Mulai:</span><br>{{ $project->start_date->format('d M Y') }}</p>
                        <p><span class="font-semibold">Selesai:</span><br>{{ $project->end_date->format('d M Y') }}</p>
                        <p><span class="font-semibold">Durasi:</span> {{ $project->start_date->diffInDays($project->end_date) }} hari</p>
                    </div>
                </div>

                {{-- kontak mahasiswa --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Kontak</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600 mb-1">Email</p>
                            <a href="mailto:{{ $project->student->user->email }}" class="text-blue-600 hover:text-blue-700 break-all">
                                {{ $project->student->user->email }}
                            </a>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">WhatsApp</p>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $project->student->whatsapp) }}" 
                               target="_blank"
                               class="text-green-600 hover:text-green-700">
                                {{ $project->student->whatsapp }}
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection