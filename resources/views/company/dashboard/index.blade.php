@extends('layouts.app')

@section('title', 'Dashboard Perusahaan')

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- hero section dengan background --}}
    <div class="relative h-64 bg-cover bg-center" style="background-image: url('{{ asset('deal-work-together.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white px-4">
            <h1 class="text-3xl md:text-4xl font-bold mb-3 fade-in-up" style="font-family: 'Space Grotesk', sans-serif;">
                Selamat Datang Di Dashboard Perusahaan Anda
            </h1>
            <p class="text-base md:text-lg text-gray-200 max-w-2xl fade-in-up" style="animation-delay: 0.1s;">
                Dapatkan insight penting tentang proses akuisisi talenta dan kelola pipeline rekrutmen Anda secara efektif.
            </p>
        </div>
    </div>

    {{-- statistik cards --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-20">
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
                <h2 class="text-xl font-bold text-gray-900 mb-4">Lamaran Terbaru</h2>
                <div class="space-y-4">
                    @foreach($recentApplications as $application)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover-lift">
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
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- AI talent recommendations --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.4s;">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Rekomendasi Talenta AI</h2>
                <div class="space-y-4">
                    @foreach($talentRecommendations as $talent)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0 hover-lift">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                @if($talent['avatar'])
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
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">{{ $talent['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $talent['expertise'] }}</p>
                            </div>
                        </div>
                        {{-- TO DO: implementasi route company.talents.show --}}
                        <a href="#" class="text-sm text-amber-600 hover:text-amber-700 font-semibold transition-colors">
                            Lihat Profil
                        </a>
                    </div>
                    @endforeach
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
