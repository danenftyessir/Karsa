@extends('layouts.app')

@section('title', 'Dashboard Instansi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- header --}}
        <div class="mb-8 fade-in-up">
            <h1 class="text-3xl font-bold text-gray-600">Dashboard</h1>
            <p class="text-gray-600 mt-1">Selamat datang, {{ $institution->name }}</p>
        </div>

        {{-- Verification Alert (Feature 1) --}}
        @if(in_array($institution->verification_status, ['pending_verification', 'rejected', 'needs_review']))
        <div class="mb-6 fade-in-up" style="animation-delay: 0.05s;">
            @if($institution->verification_status === 'pending_verification' || $institution->verification_status === null)
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-semibold text-blue-800">Verifikasi Dokumen Diperlukan</h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Institusi Anda belum terverifikasi. Upload dokumen untuk verifikasi otomatis menggunakan AI.
                        </p>
                        <div class="mt-3">
                            <a href="{{ route('institution.verification.upload') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-sm inline-block transition-colors">
                                Upload Dokumen Verifikasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @elseif($institution->verification_status === 'rejected')
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-semibold text-red-800">Dokumen Ditolak</h3>
                        <p class="mt-1 text-sm text-red-700">
                            Dokumen verifikasi Anda tidak lolos verifikasi AI. Harap periksa hasil verifikasi dan upload ulang dokumen yang sesuai.
                        </p>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('institution.verification.status') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold text-sm inline-block transition-colors">
                                Lihat Hasil Verifikasi
                            </a>
                            <a href="{{ route('institution.verification.upload') }}" class="px-4 py-2 bg-white text-red-600 border border-red-600 rounded-lg hover:bg-red-50 font-semibold text-sm inline-block transition-colors">
                                Upload Ulang Dokumen
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @elseif($institution->verification_status === 'needs_review')
            <div class="bg-orange-50 border-l-4 border-orange-400 p-4 rounded">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-orange-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-semibold text-orange-800">Perlu Review Manual</h3>
                        <p class="mt-1 text-sm text-orange-700">
                            Dokumen Anda memerlukan review manual oleh tim kami. Kami akan menghubungi Anda dalam 1-2 hari kerja.
                        </p>
                        <div class="mt-3">
                            <a href="{{ route('institution.verification.status') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-semibold text-sm inline-block transition-colors">
                                Lihat Detail Verifikasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- urgent items --}}
        @if($urgentItems['pending_applications'] > 0 || $urgentItems['pending_reviews'] > 0 || $urgentItems['overdue_milestones'] > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 fade-in-up" style="animation-delay: 0.1s;">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-semibold text-yellow-800">Perhatian Diperlukan</h3>
                    <div class="mt-2 text-sm text-yellow-700 space-y-1">
                        @if($urgentItems['pending_applications'] > 0)
                        <p>• {{ $urgentItems['pending_applications'] }} aplikasi menunggu review</p>
                        @endif
                        @if($urgentItems['pending_reviews'] > 0)
                        <p>• {{ $urgentItems['pending_reviews'] }} proyek selesai menunggu review Anda</p>
                        @endif
                        @if($urgentItems['overdue_milestones'] > 0)
                        <p>• {{ $urgentItems['overdue_milestones'] }} milestone melewati deadline</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- statistik cards - problems --}}
        <div class="mb-6 fade-in-up" style="animation-delay: 0.15s;">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Masalah</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['problems']['total'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                @if($stats['problems']['growth'] > 0)
                                <span class="text-green-600">↑ {{ number_format($stats['problems']['growth'], 1) }}%</span>
                                @else
                                <span class="text-red-600">↓ {{ number_format(abs($stats['problems']['growth']), 1) }}%</span>
                                @endif
                                dari bulan lalu
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Terbuka</p>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['problems']['open'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">Menerima aplikasi</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Views</p>
                            <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['problems']['total_views']) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Rata-rata {{ number_format($stats['problems']['avg_views_per_problem'], 1) }}/masalah</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Selesai</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['problems']['completed'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">Proyek sukses</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- statistik cards - applications & projects --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- applications stats --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.2s;">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aplikasi</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['applications']['total'] }}</p>
                        <p class="text-sm text-gray-600">Total Aplikasi</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['applications']['pending'] }}</p>
                        <p class="text-sm text-gray-600">Pending</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['applications']['accepted'] }}</p>
                        <p class="text-sm text-gray-600">Diterima</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($stats['applications']['acceptance_rate'], 1) }}%</p>
                        <p class="text-sm text-gray-600">Acceptance Rate</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('institution.applications.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">
                        Lihat Semua Aplikasi
                    </a>
                </div>
            </div>

            {{-- projects stats --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.25s;">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Proyek</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['projects']['total'] }}</p>
                        <p class="text-sm text-gray-600">Total Proyek</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['projects']['active'] }}</p>
                        <p class="text-sm text-gray-600">Aktif</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['projects']['completed'] }}</p>
                        <p class="text-sm text-gray-600">Selesai</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['projects']['avg_progress'], 1) }}%</p>
                        <p class="text-sm text-gray-600">Rata-rata Progress</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('institution.projects.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">
                        Lihat Semua Proyek
                    </a>
                </div>
            </div>
        </div>

        {{-- main content grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- left column --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- recent applications --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.3s;">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Aplikasi Terbaru</h3>
                        <a href="{{ route('institution.applications.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentApplications as $application)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                                    {{ substr($application->student->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $application->student->user->name }}</p>
                                    <p class="text-xs text-gray-600">{{ Str::limit($application->problem->title, 40) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($application->status == 'pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded">Pending</span>
                                @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">Review</span>
                                @endif
                                <a href="{{ route('institution.applications.show', $application->id) }}" 
                                   class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                                    Lihat
                                </a>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-600 text-center py-4">Belum ada aplikasi baru</p>
                        @endforelse
                    </div>
                </div>

                {{-- active projects --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.35s;">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Proyek Aktif</h3>
                        <a href="{{ route('institution.projects.index', ['status' => 'active']) }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse($activeProjects as $project)
                        <div class="border-l-4 border-blue-500 pl-4 py-2">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ Str::limit($project->title, 50) }}</p>
                                    <p class="text-xs text-gray-600">{{ $project->student->user->name }}</p>
                                </div>
                                <a href="{{ route('institution.projects.show', $project->id) }}" 
                                   class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                                    Lihat
                                </a>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                         style="width: {{ $project->progress_percentage ?? 0 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600">{{ round($project->progress_percentage ?? 0) }}%</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-600 text-center py-4">Belum ada proyek aktif</p>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- right column --}}
            <div class="space-y-6">
                
                {{-- quick actions --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.4s;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('institution.problems.create') }}" 
                           class="block w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold text-center">
                            + Buat Masalah Baru
                        </a>
                        <a href="{{ route('institution.applications.index', ['status' => 'pending']) }}" 
                           class="block w-full px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-semibold text-center">
                            Review Aplikasi
                        </a>
                        <a href="{{ route('institution.projects.index') }}" 
                           class="block w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold text-center">
                            Monitor Proyek
                        </a>
                    </div>
                </div>

                {{-- pending reviews --}}
                @if($pendingReviews->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.45s;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Perlu Review</h3>
                    <div class="space-y-3">
                        @foreach($pendingReviews as $project)
                        <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="font-semibold text-gray-900 text-sm">{{ Str::limit($project->title, 40) }}</p>
                            <p class="text-xs text-gray-600 mb-2">{{ $project->student->user->name }}</p>
                            <a href="{{ route('institution.reviews.create', $project->id) }}" 
                               class="text-blue-600 hover:text-blue-700 text-xs font-semibold">
                                Tulis Review
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- recent problems --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 fade-in-up" style="animation-delay: 0.5s;">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Masalah Terbaru</h3>
                        <a href="{{ route('institution.problems.index') }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentProblems as $problem)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="font-semibold text-gray-900 text-sm">{{ Str::limit($problem->title, 40) }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center gap-2 text-xs text-gray-600">
                                    <span>{{ $problem->applications_count }} aplikasi</span>
                                    @if($problem->status == 'open')
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded">Open</span>
                                    @endif
                                </div>
                                <a href="{{ route('institution.problems.show', $problem->id) }}" 
                                   class="text-blue-600 hover:text-blue-700 text-xs font-semibold">
                                    Lihat
                                </a>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-600 text-center py-4 text-sm">Belum ada masalah</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
.fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
    opacity: 0;
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
</style>
@endsection