@extends('layouts.app')

@section('title', 'Lowongan Tersimpan')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="savedJobsPage()">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Lowongan Tersimpan</h1>
                    <p class="text-gray-600 mt-1">{{ $totalSaved }} lowongan disimpan</p>
                </div>

                @if($expiringCount > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
                    <span class="text-amber-700 text-sm">
                        <i class="fas fa-clock mr-1"></i>
                        {{ $expiringCount }} lowongan akan expired dalam 7 hari
                    </span>
                </div>
                @endif
            </div>

            <!-- Filter by Folder -->
            @if(count($folders) > 0)
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('student.jobs.saved') }}"
                   class="px-3 py-1.5 rounded-full text-sm {{ !request('folder') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Semua
                </a>
                @foreach($folders as $folder)
                <a href="{{ route('student.jobs.saved', ['folder' => $folder]) }}"
                   class="px-3 py-1.5 rounded-full text-sm {{ request('folder') === $folder ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $folder }}
                </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if($savedJobs->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($savedJobs as $saved)
            @php $job = $saved->jobPosting; @endphp
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                <div class="p-5">
                    <!-- Company Info -->
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center overflow-hidden">
                            @if($job->company->logo_url)
                                <img src="{{ $job->company->logo_url }}" alt="{{ $job->company->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ $job->company->avatar_svg }}" alt="{{ $job->company->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 truncate">{{ $job->title }}</h3>
                            <p class="text-sm text-gray-600 truncate">{{ $job->company->name }}</p>
                        </div>
                        <button @click="removeSaved({{ $job->id }})" class="text-gray-400 hover:text-red-500">
                            <i class="fas fa-bookmark text-blue-600"></i>
                        </button>
                    </div>

                    <!-- Job Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt w-5 text-gray-400"></i>
                            {{ $job->location ?? 'Remote' }}
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-briefcase w-5 text-gray-400"></i>
                            {{ ucfirst(str_replace('_', ' ', $job->job_type)) }}
                        </div>
                        @if($job->salary_min || $job->salary_max)
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-money-bill-wave w-5 text-gray-400"></i>
                            @if($job->salary_min && $job->salary_max)
                            Rp {{ number_format($job->salary_min/1000000, 0) }} - {{ number_format($job->salary_max/1000000, 0) }} jt
                            @else
                            Rp {{ number_format(($job->salary_min ?? $job->salary_max)/1000000, 0) }} jt
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Folder & Notes -->
                    @if($saved->folder)
                    <div class="mb-3">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                            <i class="fas fa-folder mr-1"></i>{{ $saved->folder }}
                        </span>
                    </div>
                    @endif

                    <!-- Expiry Warning -->
                    @if($job->expires_at && $job->expires_at <= now()->addDays(7))
                    <div class="mb-3 text-xs text-amber-600 bg-amber-50 rounded px-2 py-1">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Berakhir {{ $job->expires_at->diffForHumans() }}
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <a href="{{ route('student.jobs.show', $job->id) }}"
                           class="flex-1 text-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">
                            Lihat Detail
                        </a>
                        <button @click="openEditModal({{ json_encode($saved) }})"
                                class="px-3 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $savedJobs->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <div class="w-20 h-20 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-bookmark text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Lowongan Tersimpan</h3>
            <p class="text-gray-600 mb-6">Simpan lowongan yang menarik untuk dilihat nanti</p>
            <a href="{{ route('student.jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i>Cari Lowongan
            </a>
        </div>
        @endif
    </div>

    <!-- Edit Modal -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="showEditModal = false"></div>
            <div class="relative bg-white rounded-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold mb-4">Edit Bookmark</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Folder</label>
                        <input type="text" x-model="editData.folder" placeholder="Contoh: Prioritas, Magang"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea x-model="editData.notes" rows="3" placeholder="Tambahkan catatan..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pengingat</label>
                        <input type="datetime-local" x-model="editData.reminder_at"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button @click="showEditModal = false" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg">Batal</button>
                    <button @click="saveEdit()" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function savedJobsPage() {
    return {
        showEditModal: false,
        editData: { job_id: null, folder: '', notes: '', reminder_at: '' },

        openEditModal(saved) {
            this.editData = {
                job_id: saved.job_posting_id,
                folder: saved.folder || '',
                notes: saved.notes || '',
                reminder_at: saved.reminder_at || ''
            };
            this.showEditModal = true;
        },

        async saveEdit() {
            try {
                const res = await fetch(`/student/jobs/${this.editData.job_id}/update-saved`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.editData)
                });
                if (res.ok) {
                    this.showEditModal = false;
                    window.location.reload();
                }
            } catch (e) { console.error(e); }
        },

        async removeSaved(jobId) {
            if (!confirm('Hapus dari bookmark?')) return;
            try {
                const res = await fetch(`/student/jobs/${jobId}/toggle-save`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if (res.ok) window.location.reload();
            } catch (e) { console.error(e); }
        }
    }
}
</script>
@endpush
@endsection
