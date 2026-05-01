{{-- resources/views/student/projects/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- breadcrumb --}}
        <nav class="mb-6 fade-in-up">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="{{ route('student.projects.index') }}" class="hover:text-blue-600 transition-colors">Proyek Saya</a></li>
                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                <li class="text-gray-900 font-semibold truncate">{{ Str::limit($project->title, 40) }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- main content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- project header --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.1s;">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-3">
                                @if($project->status === 'active')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Aktif</span>
                                @elseif($project->status === 'completed')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Selesai</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">{{ ucfirst($project->status) }}</span>
                                @endif
                                
                                @if($project->is_overdue && $project->status === 'active')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Overdue</span>
                                @endif
                            </div>
                            
                            <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $project->title }}</h1>
                            
                            {{-- institution info --}}
                            <div class="flex items-center space-x-3">
                                @if($project->institution->logo_path)
                                    <img src="{{ asset('storage/' . $project->institution->logo_path) }}" 
                                         alt="{{ $project->institution->name }}"
                                         class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center">
                                        <span class="text-white font-bold">{{ strtoupper(substr($project->institution->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $project->institution->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $project->institution->type }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- progress bar --}}
                    <div class="mt-6">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span class="font-medium">Progress Keseluruhan</span>
                            <span class="font-bold text-indigo-600">{{ $project->progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-500"
                                 style="width: {{ $project->progress_percentage }}%"></div>
                        </div>
                    </div>

                    {{-- timeline --}}
                    <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Tanggal Mulai</p>
                            <p class="font-semibold text-gray-900">{{ $project->start_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Tanggal Berakhir</p>
                            <p class="font-semibold text-gray-900">{{ $project->end_date->format('d M Y') }}</p>
                        </div>
                    </div>

                    @if($project->description)
                        <div class="mt-6 pt-6 border-t">
                            <h3 class="font-semibold text-gray-900 mb-2">Deskripsi</h3>
                            <p class="text-gray-600 text-sm leading-relaxed">{{ $project->description }}</p>
                        </div>
                    @endif
                </div>

                {{-- milestones --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Milestone Proyek</h2>
                        <span class="text-sm text-gray-600">{{ $project->milestones->where('status', 'completed')->count() }}/{{ $project->milestones->count() }} Selesai</span>
                    </div>

                    <div class="space-y-4">
                        @foreach($project->milestones as $index => $milestone)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all" 
                                 x-data="{ open: false }">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start flex-1">
                                        {{-- status icon --}}
                                        <div class="mt-1 mr-3">
                                            @if($milestone->status === 'completed')
                                                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                            @elseif($milestone->status === 'in_progress')
                                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                                    <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                                </div>
                                            @elseif($milestone->is_overdue)
                                                <div class="w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-6 h-6 bg-gray-300 rounded-full"></div>
                                            @endif
                                        </div>

                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 mb-1">{{ $milestone->title }}</h4>
                                            @if($milestone->description)
                                                <p class="text-sm text-gray-600 mb-2">{{ $milestone->description }}</p>
                                            @endif
                                            
                                            <div class="flex items-center gap-4 text-xs text-gray-600">
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Target: {{ $milestone->target_date->format('d M Y') }}
                                                </span>
                                                @if($milestone->status === 'completed')
                                                    <span class="text-green-600 font-medium">✓ Selesai</span>
                                                @elseif($milestone->is_overdue)
                                                    <span class="text-red-600 font-medium">Terlambat</span>
                                                @else
                                                    <span>{{ $milestone->days_remaining }} hari lagi</span>
                                                @endif
                                            </div>

                                            {{-- progress bar untuk milestone --}}
                                            @if($milestone->status !== 'completed')
                                                <div class="mt-3">
                                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                        <span>Progress</span>
                                                        <span class="font-semibold">{{ $milestone->progress_percentage }}%</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-1.5 rounded-full transition-all"
                                                             style="width: {{ $milestone->progress_percentage }}%"></div>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- notes --}}
                                            @if($milestone->notes)
                                                <div class="mt-2 p-2 bg-gray-50 rounded text-xs text-gray-600">
                                                    <strong>Catatan:</strong> {{ $milestone->notes }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- update button --}}
                                    @if($project->status === 'active' && $milestone->status !== 'completed')
                                        <button @click="open = true"
                                                class="ml-4 px-3 py-1 text-sm bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors">
                                            Update
                                        </button>
                                    @endif
                                </div>

                                {{-- update form (hidden by default) --}}
                                <div x-show="open" 
                                     x-transition
                                     class="mt-4 pt-4 border-t"
                                     style="display: none;">
                                    <form @submit.prevent="updateMilestone({{ $milestone->id }}, $event)" class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Progress (%)</label>
                                            <input type="number"
                                                   name="progress_percentage"
                                                   min="0"
                                                   max="100"
                                                   value="{{ $milestone->progress_percentage }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                                            <textarea name="notes"
                                                      rows="2"
                                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ $milestone->notes }}</textarea>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit"
                                                    class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                                                Simpan
                                            </button>
                                            <button type="button"
                                                    @click="open = false"
                                                    class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition-colors">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- reports --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Laporan Progress</h2>
                        @if($project->status === 'active')
                            <a href="{{ route('student.projects.create-report', $project->id) }}"
                               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                                + Buat Laporan
                            </a>
                        @endif
                    </div>

                    @if($project->reports->isEmpty())
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-600">Belum ada laporan progress</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($project->reports as $report)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h4 class="font-semibold text-gray-900">{{ $report->title }}</h4>
                                                <span class="px-2 py-0.5 bg-{{ $report->status_badge['color'] }}-100 text-{{ $report->status_badge['color'] }}-700 text-xs rounded-full">
                                                    {{ $report->status_badge['text'] }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">{{ Str::limit($report->summary, 150) }}</p>
                                            <div class="flex items-center gap-4 text-xs text-gray-600">
                                                <span>{{ $report->type_label }}</span>
                                                <span>{{ $report->period_start->format('d M') }} - {{ $report->period_end->format('d M Y') }}</span>
                                                <span>{{ $report->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        @if($report->document_path)
                                            <a href="{{ document_url($report->document_path) }}"
                                               target="_blank"
                                               download
                                               class="ml-4 text-indigo-600 hover:text-indigo-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- sidebar --}}
            <div class="space-y-6">
                
                {{-- action buttons --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.15s;">
                    <h3 class="font-semibold text-gray-900 mb-4">Aksi</h3>
                    <div class="space-y-3">
                        @if($project->status === 'active')
                            <a href="{{ route('student.projects.create-report', $project->id) }}"
                               class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                Buat Laporan Progress
                            </a>

                            @if($project->progress_percentage >= 80)
                                <a href="{{ route('student.projects.create-final-report', $project->id) }}"
                                   class="block w-full text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    Submit Laporan Akhir
                                </a>
                            @endif
                        @endif

                        @if($project->final_report_path)
                            <a href="{{ asset('storage/' . $project->final_report_path) }}"
                               target="_blank"
                               class="block w-full text-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                                Download Laporan Akhir
                            </a>
                        @endif
                    </div>
                </div>

                {{-- project info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.2s;">
                    <h3 class="font-semibold text-gray-900 mb-4">Informasi Proyek</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600 mb-1">Durasi</p>
                            <p class="font-semibold text-gray-900">{{ $project->duration_days ?? $project->start_date->diffInDays($project->end_date) }} hari</p>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">Lokasi</p>
                            <p class="font-semibold text-gray-900">
                                {{ $project->problem->regency->name }}, {{ $project->problem->province->name }}
                            </p>
                        </div>
                        @if($project->problem->sdg_categories)
                            <div>
                                <p class="text-gray-600 mb-2">Kategori SDG</p>
                                <div class="flex flex-wrap gap-1">
                                    @php
                                        // pastikan sdg_categories adalah array
                                        $sdgCategories = is_array($project->problem->sdg_categories) 
                                            ? $project->problem->sdg_categories 
                                            : (is_string($project->problem->sdg_categories) 
                                                ? json_decode($project->problem->sdg_categories, true) ?? [] 
                                                : []);
                                    @endphp
                                    
                                    @foreach($sdgCategories as $sdg)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">SDG {{ $sdg }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- team members / collaborators --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.25s;">
                    <div class="mb-4">
                        <h3 class="font-semibold text-gray-900">Anggota Tim</h3>
                        <p class="text-xs text-gray-500 mt-1">Mahasiswa lain yang bekerja di problem ini</p>
                    </div>

                    @if(isset($teamMembers) && $teamMembers->count() > 0)
                        <div class="space-y-3">
                            @foreach($teamMembers as $member)
                                <a href="{{ route('student.friends.profile', $member->id) }}"
                                   class="flex items-center gap-3 p-3 rounded-lg hover:bg-indigo-50 transition-colors group">
                                    <img src="{{ $member->profile_photo_url }}"
                                         alt="{{ $member->user->name }}"
                                         class="w-12 h-12 rounded-full object-cover ring-2 ring-gray-200 group-hover:ring-indigo-300 transition-all">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm truncate group-hover:text-indigo-600 transition-colors">
                                            {{ $member->user->name }}
                                        </p>
                                        <p class="text-xs text-gray-600 truncate">
                                            {{ $member->university->name ?? 'Universitas' }}
                                        </p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-sm text-gray-600 mb-1">Anda satu-satunya yang mengerjakan problem ini</p>
                            <p class="text-xs text-gray-500">Mahasiswa lain yang bekerja di problem yang sama akan muncul di sini</p>
                        </div>
                    @endif
                </div>

                {{-- impact metrics (for completed projects) --}}
                @if($project->status === 'completed' && $project->impact_metrics)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.25s;">
                        <h3 class="font-semibold text-gray-900 mb-4">Impact Metrics</h3>
                        <div class="space-y-4">
                            @php
                                // pastikan impact_metrics adalah array
                                $impactMetrics = is_array($project->impact_metrics) 
                                    ? $project->impact_metrics 
                                    : json_decode($project->impact_metrics, true) ?? [];
                            @endphp
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <p class="text-3xl font-bold text-blue-600">{{ $impactMetrics['beneficiaries'] ?? 0 }}</p>
                                <p class="text-sm text-gray-600 mt-1">Penerima Manfaat</p>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <p class="text-3xl font-bold text-green-600">{{ $impactMetrics['activities'] ?? 0 }}</p>
                                <p class="text-sm text-gray-600 mt-1">Kegiatan</p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        </div>

    </div>
</div>

<script>
function updateMilestone(milestoneId, event) {
    const formData = new FormData(event.target);

    fetch(`/student/projects/milestones/${milestoneId}/update`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            progress_percentage: formData.get('progress_percentage'),
            notes: formData.get('notes')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal update milestone: ' + data.message);
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan. Silakan coba lagi.');
        console.error('Error:', error);
    });
}
</script>

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
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
}
</style>
@endsection