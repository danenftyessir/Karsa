{{-- resources/views/student/applications/tracker.blade.php --}}
@extends('layouts.app')

@section('title', 'Application Tracker')

@push('styles')
<style>
    .tracker-card {
        transform: translate3d(0, 0, 0);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .tracker-card:hover {
        transform: translate3d(0, -2px, 0);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .status-pipeline {
        display: flex;
        gap: 4px;
    }
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #e5e7eb;
    }
    .status-dot.active { background: #3b82f6; }
    .status-dot.success { background: #22c55e; }
    .status-dot.rejected { background: #ef4444; }
    .chart-bar {
        transition: height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Application Tracker</h1>
                    <p class="text-gray-600 mt-1">Pantau progres semua lamaran Anda</p>
                </div>
                <a href="{{ route('student.applications.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">
                    Lihat Semua Aplikasi
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
        {{-- stats cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="text-3xl font-bold text-gray-900">{{ $totalApplications }}</div>
                <div class="text-sm text-gray-600 mt-1">Total Lamaran</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="text-3xl font-bold text-blue-600">{{ $activeCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Sedang Diproses</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="text-3xl font-bold text-green-600">{{ $successCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Diterima</div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <div class="text-3xl font-bold text-red-500">{{ $rejectedCount }}</div>
                <div class="text-sm text-gray-600 mt-1">Ditolak</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- main content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- weekly activity chart --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Aktivitas Minggu Ini</h3>
                    <div class="flex items-end justify-between h-32 gap-2">
                        @foreach($weeklyActivity as $day)
                        <div class="flex-1 flex flex-col items-center gap-2">
                            <div class="w-full bg-gray-100 rounded-t relative" style="height: 100px;">
                                <div class="chart-bar absolute bottom-0 left-0 right-0 bg-blue-500 rounded-t"
                                     style="height: {{ $day['applications'] > 0 ? max(20, min(100, $day['applications'] * 30)) : 0 }}%;">
                                </div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $day['date'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- job applications --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Lamaran Lowongan Kerja</h3>
                        <span class="text-sm text-gray-500">{{ $jobApplications->count() }} lamaran</span>
                    </div>

                    @if($jobApplications->count() > 0)
                    <div class="space-y-3">
                        @foreach($jobApplications->take(5) as $app)
                        <div class="tracker-card p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $app->jobPosting->title ?? 'Lowongan' }}</h4>
                                    <p class="text-sm text-gray-600">{{ $app->jobPosting->company->name ?? 'Company' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Applied {{ $app->applied_at?->diffForHumans() }}</p>
                                </div>
                                <div class="text-right">
                                    @php
                                        $statusColors = [
                                            'new' => 'bg-blue-100 text-blue-700',
                                            'reviewed' => 'bg-yellow-100 text-yellow-700',
                                            'shortlisted' => 'bg-purple-100 text-purple-700',
                                            'interview' => 'bg-indigo-100 text-indigo-700',
                                            'hired' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$app->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                    {{-- status pipeline --}}
                                    <div class="status-pipeline mt-2 justify-end">
                                        @php
                                            $stages = ['new', 'reviewed', 'shortlisted', 'interview', 'hired'];
                                            $currentIndex = array_search($app->status, $stages);
                                        @endphp
                                        @foreach($stages as $i => $stage)
                                        <div class="status-dot {{ $app->status === 'rejected' ? 'rejected' : ($i <= $currentIndex ? ($app->status === 'hired' ? 'success' : 'active') : '') }}"></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-sm">Belum ada lamaran lowongan kerja</p>
                    @endif
                </div>

                {{-- project applications --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-gray-900">Lamaran Proyek KKN</h3>
                        <span class="text-sm text-gray-500">{{ $projectApplications->count() }} lamaran</span>
                    </div>

                    @if($projectApplications->count() > 0)
                    <div class="space-y-3">
                        @foreach($projectApplications->take(5) as $app)
                        <div class="tracker-card p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">{{ $app->problem->title ?? 'Proyek' }}</h4>
                                    <p class="text-sm text-gray-600">{{ $app->problem->institution->name ?? 'Instansi' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Applied {{ $app->applied_at?->diffForHumans() }}</p>
                                </div>
                                <div class="text-right">
                                    @php
                                        $projectStatusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'reviewed' => 'bg-blue-100 text-blue-700',
                                            'accepted' => 'bg-green-100 text-green-700',
                                            'rejected' => 'bg-red-100 text-red-700',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $projectStatusColors[$app->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-sm">Belum ada lamaran proyek KKN</p>
                    @endif
                </div>
            </div>

            {{-- sidebar --}}
            <div class="space-y-6">
                {{-- success rate --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Tingkat Keberhasilan</h3>
                    @php
                        $successRate = $totalApplications > 0 ? round(($successCount / $totalApplications) * 100) : 0;
                    @endphp
                    <div class="relative pt-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-2xl font-bold text-green-600">{{ $successRate }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ $successRate }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ $successCount }} dari {{ $totalApplications }} lamaran diterima</p>
                    </div>
                </div>

                {{-- quick actions --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
                    <div class="space-y-3">
                        <a href="{{ route('student.jobs.index') }}"
                           class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-medium text-blue-700">Cari Lowongan Baru</span>
                        </a>
                        <a href="{{ route('student.browse-problems.index') }}"
                           class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span class="text-sm font-medium text-purple-700">Jelajahi Proyek KKN</span>
                        </a>
                        <a href="{{ route('student.profile.index') }}"
                           class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Update Profil</span>
                        </a>
                    </div>
                </div>

                {{-- recent updates --}}
                @if($recentUpdates->count() > 0)
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Update Terbaru</h3>
                    <div class="space-y-3">
                        @foreach($recentUpdates as $update)
                        <div class="flex items-start gap-3 text-sm">
                            <div class="w-2 h-2 rounded-full bg-blue-500 mt-1.5"></div>
                            <div>
                                <p class="text-gray-700">{{ $update->jobPosting->title ?? 'Lamaran' }}</p>
                                <p class="text-xs text-gray-500">{{ $update->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
