@extends('layouts.app')

@section('title', 'Dashboard Perusahaan')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- hero section dengan background --}}
    <div class="relative h-64 bg-cover bg-center" style="background-image: url('{{ asset('deal-work-together.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white px-4">
            <h1 class="text-3xl md:text-4xl font-bold mb-3 fade-in-up" style="font-family: 'Space Grotesk', sans-serif; color: white !important;">
                Selamat Datang Di Dashboard Perusahaan Anda
            </h1>
            <p class="text-base md:text-lg text-gray-200 max-w-2xl fade-in-up" style="animation-delay: 0.1s;">
                Dapatkan insight penting tentang proses akuisisi talenta dan kelola pipeline rekrutmen Anda secara efektif.
            </p>
        </div>
    </div>

    {{-- quick actions panel --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-20 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Aksi Cepat</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="{{ route('company.jobs.create') }}" class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-amber-500 hover:bg-amber-50 transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 group-hover:bg-amber-500 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-amber-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Post Job</p>
                        <p class="text-xs text-gray-500">Buat lowongan baru</p>
                    </div>
                </a>

                <a href="{{ route('company.applications.index') }}" class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-blue-500 hover:bg-blue-50 transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 group-hover:bg-blue-500 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Lihat Lamaran</p>
                        <p class="text-xs text-gray-500">{{ $stats['applications_received'] }} total</p>
                    </div>
                </a>

                <a href="{{ route('company.talents.index') }}" class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-green-500 hover:bg-green-50 transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-lg bg-green-100 group-hover:bg-green-500 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-green-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Cari Talent</p>
                        <p class="text-xs text-gray-500">Browse kandidat</p>
                    </div>
                </a>

                <a href="{{ route('company.jobs.index') }}" class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-200 hover:border-purple-500 hover:bg-purple-50 transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 group-hover:bg-purple-500 flex items-center justify-center transition-colors">
                        <svg class="w-5 h-5 text-purple-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-900">Kelola Jobs</p>
                        <p class="text-xs text-gray-500">{{ $stats['active_jobs'] }} aktif</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- statistik cards --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

            {{-- total jobs --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.15s;">
                <p class="text-sm text-gray-600 mb-1">Total Lowongan</p>
                <p class="text-3xl font-bold text-amber-500">{{ number_format($stats['total_jobs']) }}</p>
                <div class="flex items-center mt-2 text-xs">
                    <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="text-green-600">{{ $stats['total_jobs_growth'] }}% bulan lalu</span>
                </div>
            </div>

            {{-- applications received --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.2s;">
                <p class="text-sm text-gray-600 mb-1">Lamaran Diterima</p>
                <p class="text-3xl font-bold text-amber-500">{{ number_format($stats['applications_received']) }}</p>
                <div class="flex items-center mt-2 text-xs">
                    <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="text-green-600">{{ $stats['applications_growth'] }}% bulan lalu</span>
                </div>
            </div>

            {{-- shortlisted candidates --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.25s;">
                <p class="text-sm text-gray-600 mb-1">Kandidat Terpilih</p>
                <p class="text-3xl font-bold text-amber-500">{{ number_format($stats['shortlisted_candidates']) }}</p>
                <div class="flex items-center mt-2 text-xs">
                    <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="text-green-600">{{ $stats['shortlisted_growth'] }}% bulan lalu</span>
                </div>
            </div>

            {{-- hires made --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.3s;">
                <p class="text-sm text-gray-600 mb-1">Rekrutmen Berhasil</p>
                <p class="text-3xl font-bold text-amber-500">{{ number_format($stats['hires_made']) }}</p>
                <div class="flex items-center mt-2 text-xs">
                    <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="text-green-600">{{ $stats['hires_growth'] }}% bulan lalu</span>
                </div>
            </div>
        </div>

        {{-- main content grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            {{-- recent applications --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.35s;">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Lamaran Terbaru</h2>
                    @if($recentApplications->count() > 0)
                    <a href="{{ route('company.applications.index') }}" class="text-sm text-amber-600 hover:text-amber-700 font-semibold transition-colors flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    @endif
                </div>
                <div class="space-y-4">
                    @forelse($recentApplications as $application)
                    <a href="{{ route('company.applications.show', $application['id']) }}" class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover-lift cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ substr($application['name'], 0, 1) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $application['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $application['position'] }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($application['status'] === 'shortlisted') bg-green-100 text-green-700
                            @elseif($application['status'] === 'reviewing') bg-amber-100 text-amber-700
                            @else bg-blue-100 text-blue-700
                            @endif">
                            @if($application['status'] === 'shortlisted') Terpilih
                            @elseif($application['status'] === 'reviewing') Direview
                            @else Baru
                            @endif
                        </span>
                    </a>
                    @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500 font-medium mb-1">Belum Ada Lamaran</p>
                        <p class="text-sm text-gray-400 mb-4">Posting lowongan untuk mulai menerima lamaran</p>
                        <a href="{{ route('company.jobs.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Post Lowongan Baru
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- AI talent recommendations --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.4s;">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Rekomendasi Talenta AI</h2>
                        <p class="text-xs text-gray-500 mt-1">Kandidat potensial untuk posisi Anda</p>
                    </div>
                    @if($talentRecommendations->count() > 0)
                    <a href="{{ route('company.talents.index') }}" class="text-sm text-amber-600 hover:text-amber-700 font-semibold transition-colors flex items-center gap-1">
                        Browse Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    @endif
                </div>
                <div class="space-y-4">
                    @forelse($talentRecommendations as $talent)
                    <a href="{{ route('profile.public', $talent['username']) }}" class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover-lift cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                @if($talent['avatar'] && $talent['avatar'] !== 'default-avatar.jpg')
                                <img src="{{ asset($talent['avatar']) }}" alt="{{ $talent['name'] }}" class="w-10 h-10 rounded-full object-cover">
                                @else
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    {{ substr($talent['name'], 0, 1) }}
                                </div>
                                @endif
                                @if($talent['online'])
                                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 text-sm">{{ $talent['name'] }}</p>
                                <p class="text-xs text-gray-500 line-clamp-1">{{ $talent['expertise'] }}</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    @empty
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-gray-500 font-medium mb-1">Belum Ada Rekomendasi</p>
                        <p class="text-sm text-gray-400 mb-4">Mulai posting lowongan untuk mendapat rekomendasi talent</p>
                        <a href="{{ route('company.talents.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Browse Talents
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- charts section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-12">

            {{-- applications over time chart --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.45s;">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Lamaran Seiring Waktu</h2>
                <div class="h-64">
                    <canvas id="applicationsChart"></canvas>
                </div>
                <div class="flex items-center justify-center gap-6 mt-4 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                        <span class="text-gray-600">Baru</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <span class="text-gray-600">Direview</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-gray-600">Terpilih</span>
                    </div>
                </div>
            </div>

            {{-- jobs by category chart --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.5s;">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Lowongan Berdasarkan Kategori</h2>
                <div class="h-64">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="flex items-center justify-center gap-4 mt-4 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                        <span class="text-gray-600">Lowongan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* animasi fade in up */
.fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translate3d(0, 20px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

/* GPU acceleration untuk performa smooth */
.gpu-accelerate {
    transform: translateZ(0);
    will-change: transform, opacity;
    backface-visibility: hidden;
}

/* hover effect untuk list items */
.hover-lift {
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.hover-lift:hover {
    transform: translateX(4px);
}

/* reduced motion support untuk aksesibilitas */
@media (prefers-reduced-motion: reduce) {
    .fade-in-up {
        animation: none;
        opacity: 1;
    }

    .hover-lift:hover {
        transform: none;
    }
}
</style>
@endsection

@push('scripts')
{{-- chart.js untuk visualisasi data --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // konfigurasi default chart untuk konsistensi visual
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6B7280';

    // data dari controller
    const applicationsData = @json($applicationsOverTime);
    const categoryData = @json($jobsByCategory);

    // applications over time line chart
    const applicationsCtx = document.getElementById('applicationsChart').getContext('2d');
    new Chart(applicationsCtx, {
        type: 'line',
        data: {
            labels: applicationsData.labels,
            datasets: [
                {
                    label: 'Baru',
                    data: applicationsData.datasets[0].data,
                    borderColor: '#F97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Direview',
                    data: applicationsData.datasets[1].data,
                    borderColor: '#22C55E',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                },
                {
                    label: 'Terpilih',
                    data: applicationsData.datasets[2].data,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#F3F4F6'
                    }
                }
            }
        }
    });

    // jobs by category bar chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: categoryData.labels,
            datasets: [{
                label: 'Lowongan',
                data: categoryData.data,
                backgroundColor: '#F97316',
                borderRadius: 6,
                barThickness: 40,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#F3F4F6'
                    },
                    ticks: {
                        stepSize: 15
                    }
                }
            }
        }
    });
});
</script>
@endpush
