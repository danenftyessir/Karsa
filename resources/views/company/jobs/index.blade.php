@extends('layouts.app')

@section('title', 'Kelola Lowongan Pekerjaan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50">
    <!-- Header Section -->
    <div class="relative bg-cover bg-center text-white py-16 overflow-hidden" style="background-image: url('{{ asset('company2.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex justify-between items-center fade-in-up">
                <div>
                    <h1 class="text-4xl font-extrabold text-white mb-2" style="font-family: 'Space Grotesk', sans-serif;">
                        Kelola Lowongan Pekerjaan
                    </h1>
                    <p class="text-white text-lg font-medium">
                        Kelola Dan Monitor Lowongan Pekerjaan Anda
                    </p>
                </div>
                <a href="{{ route('company.jobs.create') }}"
                   class="bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold hover-lift transition-all duration-300 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Lowongan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8" x-data="jobManager()">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 fade-in-up">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 gpu-accelerate hover-lift">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 text-sm font-medium">Total Lowongan</span>
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 gpu-accelerate hover-lift">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 text-sm font-medium">Lowongan Aktif</span>
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['active'] ?? 0 }}</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 gpu-accelerate hover-lift">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 text-sm font-medium">Total Pelamar</span>
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['applications'] ?? 0 }}</p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 gpu-accelerate hover-lift">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 text-sm font-medium">Lowongan Ditutup</span>
                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['closed'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 fade-in-up">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Lowongan</label>
                    <div class="relative">
                        <input type="text"
                               x-model="search"
                               @input.debounce.300ms="filterJobs()"
                               placeholder="Cari berdasarkan judul atau kategori..."
                               class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select x-model="statusFilter"
                            @change="filterJobs()"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                        <option value="">Semua Status</option>
                        <option value="published">Dipublikasikan</option>
                        <option value="draft">Draft</option>
                        <option value="closed">Ditutup</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select x-model="categoryFilter"
                            @change="filterJobs()"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Job Listings Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden fade-in-up">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Judul Lowongan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Pelamar</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Dilihat</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal Dibuat</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($jobPostings as $job)
                            <tr class="hover:bg-gray-50 transition-colors duration-200 gpu-accelerate">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $job->title }}</div>
                                            <div class="text-sm text-gray-500">{{ $job->location }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700">{{ $job->jobCategory->name ?? 'Tidak Ada Kategori' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $job->status_badge_class }}">
                                        {{ $job->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-900">{{ $job->job_applications_count }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700">{{ $job->views_count }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500">{{ $job->created_at->format('d M Y') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('company.jobs.show', $job->id) }}"
                                           class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                           title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('company.jobs.edit', $job->id) }}"
                                           class="text-green-600 hover:text-green-800 transition-colors duration-200"
                                           title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <button @click="deleteJob({{ $job->id }})"
                                                class="text-red-600 hover:text-red-800 transition-colors duration-200"
                                                title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-gray-500 text-lg font-medium mb-2">Belum Ada Lowongan</p>
                                        <p class="text-gray-400 mb-4">Mulai buat lowongan pertama Anda sekarang</p>
                                        <a href="{{ route('company.jobs.create') }}"
                                           class="bg-blue-600 text-white px-6 py-2 rounded-xl font-semibold hover:bg-blue-700 transition-all duration-300">
                                            Buat Lowongan Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($jobPostings->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $jobPostings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function jobManager() {
    return {
        search: '',
        statusFilter: '',
        categoryFilter: '',

        filterJobs() {
            const params = new URLSearchParams();
            if (this.search) params.append('search', this.search);
            if (this.statusFilter) params.append('status', this.statusFilter);
            if (this.categoryFilter) params.append('category', this.categoryFilter);

            window.location.href = '{{ route("company.jobs.index") }}?' + params.toString();
        },

        async deleteJob(jobId) {
            if (!confirm('Apakah Anda yakin ingin menghapus lowongan ini?')) {
                return;
            }

            try {
                const response = await fetch(`/company/jobs/${jobId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Gagal menghapus lowongan');
                }
            } catch (error) {
                alert('Terjadi kesalahan saat menghapus lowongan');
            }
        }
    }
}
</script>
@endsection
