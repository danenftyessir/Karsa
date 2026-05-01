@extends('layouts.app')

@section('title', 'Kelola Proyek')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- back button --}}
        <a href="{{ route('institution.projects.show', $project->id) }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2 mb-6">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Detail Proyek
        </a>

        {{-- header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Kelola Proyek</h1>
            <p class="text-gray-600 mt-1">{{ $project->title }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- main content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- update status proyek --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Status Proyek</h2>
                    
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status Saat Ini</label>
                            <select id="project-status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="planning" {{ $project->status == 'planning' ? 'selected' : '' }}>Planning</option>
                                <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="review" {{ $project->status == 'review' ? 'selected' : '' }}>Review</option>
                                <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ $project->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                            </select>
                        </div>
                        <button onclick="updateProjectStatus()" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold mt-7">
                            Update
                        </button>
                    </div>
                </div>

                {{-- milestone management --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Milestone Management</h2>
                        <button onclick="showAddMilestoneModal()" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold text-sm">
                            + Tambah Milestone
                        </button>
                    </div>

                    <div class="space-y-4">
                        @forelse($project->milestones as $milestone)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-900">{{ $milestone->title }}</h3>
                                    @if($milestone->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ $milestone->description }}</p>
                                    @endif
                                </div>
                                
                                @if($milestone->status == 'completed')
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">Selesai</span>
                                @elseif($milestone->status == 'in_progress')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded-full">Berjalan</span>
                                @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-semibold rounded-full">Pending</span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Target: {{ $milestone->target_date ? $milestone->target_date->format('d M Y') : '-' }}</span>
                                    @if($milestone->target_date && $milestone->target_date < now() && $milestone->status != 'completed')
                                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-semibold rounded">Overdue</span>
                                    @endif
                                </div>
                                
                                <div class="flex gap-2">
                                    <button onclick="editMilestone({{ $milestone->id }})" 
                                            class="text-blue-600 hover:text-blue-700 font-semibold">
                                        Edit
                                    </button>
                                    <button onclick="deleteMilestone({{ $milestone->id }})" 
                                            class="text-red-600 hover:text-red-700 font-semibold">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-600 text-center py-8">Belum ada milestone. Tambahkan milestone pertama!</p>
                        @endforelse
                    </div>
                </div>

                {{-- report review --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Review Laporan</h2>

                    <div class="space-y-4">
                        @forelse($project->reports->where('status', 'pending') as $report)
                        <div class="border border-blue-200 bg-blue-50 rounded-lg p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ $report->title }}</h3>
                                    <p class="text-xs text-gray-600">Disubmit: {{ $report->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">Menunggu Review</span>
                            </div>

                            <p class="text-sm text-gray-700 mb-3">{{ Str::limit($report->summary ?? $report->activities ?? '', 200) }}</p>

                            @if($report->document_path)
                            <a href="{{ document_url($report->document_path) }}"
                               target="_blank"
                               download
                               class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 mb-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download Dokumen
                            </a>
                            @endif

                            <div class="flex gap-2 mt-3">
                                <button onclick="showApproveModal({{ $report->id }})" 
                                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold text-sm">
                                    Setujui
                                </button>
                                <button onclick="showRejectModal({{ $report->id }})" 
                                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold text-sm">
                                    Minta Revisi
                                </button>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-600 text-center py-8">Tidak ada laporan yang perlu direview</p>
                        @endforelse

                        {{-- laporan yang sudah direview --}}
                        @if($project->reports->whereIn('status', ['approved', 'revision_required'])->count() > 0)
                        <div class="border-t border-gray-200 pt-4 mt-6">
                            <h3 class="font-bold text-gray-900 mb-3">Riwayat Review</h3>
                            @foreach($project->reports->whereIn('status', ['approved', 'revision_required']) as $report)
                            <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $report->title }}</p>
                                    @if($report->status == 'approved')
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded">Disetujui</span>
                                    @else
                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded">Perlu Revisi</span>
                                    @endif
                                </div>
                                @if($report->institution_feedback)
                                <p class="text-xs text-gray-600">Feedback: {{ $report->institution_feedback }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- sidebar --}}
            <div class="space-y-6">
                
                {{-- project info --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Info Proyek</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-600">Mahasiswa</p>
                            <p class="font-semibold text-gray-900">{{ $project->student->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Progress</p>
                            <p class="font-semibold text-gray-900">{{ round($project->progress_percentage ?? 0) }}%</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Status</p>
                            <p class="font-semibold text-gray-900 capitalize">{{ $project->status }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Timeline</p>
                            <p class="font-semibold text-gray-900">{{ $project->start_date->format('d M Y') }} - {{ $project->end_date->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                {{-- quick stats --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Statistik</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Total Milestones</span>
                            <span class="font-bold text-gray-900">{{ $project->milestones->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Selesai</span>
                            <span class="font-bold text-green-600">{{ $project->milestones->where('status', 'completed')->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Total Laporan</span>
                            <span class="font-bold text-gray-900">{{ $project->reports->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Pending Review</span>
                            <span class="font-bold text-yellow-600">{{ $project->reports->where('status', 'pending')->count() }}</span>
                        </div>
                    </div>
                </div>

                {{-- actions --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('institution.projects.show', $project->id) }}" 
                           class="block w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold text-center text-sm">
                            Lihat Detail Lengkap
                        </a>
                        @if($project->status == 'completed' && !$project->rating)
                        <a href="{{ route('institution.reviews.create', $project->id) }}" 
                           class="block w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold text-center text-sm">
                            Berikan Review
                        </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- modal add milestone --}}
<div id="add-milestone-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Tambah Milestone</h3>
        <form method="POST" action="{{ route('institution.projects.add-milestone', $project->id) }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul *</label>
                    <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Target Tanggal *</label>
                    <input type="date" name="target_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    Simpan
                </button>
                <button type="button" onclick="closeModal('add-milestone-modal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- modal approve report --}}
<div id="approve-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Setujui Laporan</h3>
        <form id="approve-form" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Feedback (Opsional)</label>
                    <textarea name="feedback" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Berikan feedback positif untuk mahasiswa..."></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                    Setujui Laporan
                </button>
                <button type="button" onclick="closeModal('approve-modal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- modal reject report --}}
<div id="reject-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Minta Revisi Laporan</h3>
        <form id="reject-form" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Feedback *</label>
                    <textarea name="feedback" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Jelaskan apa yang perlu diperbaiki..."></textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">
                    Minta Revisi
                </button>
                <button type="button" onclick="closeModal('reject-modal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- modal edit milestone --}}
<div id="edit-milestone-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Edit Milestone</h3>
        <form id="edit-milestone-form" onsubmit="submitEditMilestone(event)">
            <input type="hidden" id="edit-milestone-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Judul *</label>
                    <input type="text" id="edit-milestone-title" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="edit-milestone-description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Target Tanggal *</label>
                    <input type="date" id="edit-milestone-target-date" name="target_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select id="edit-milestone-status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="pending">Pending</option>
                        <option value="in_progress">Berjalan</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    Update
                </button>
                <button type="button" onclick="closeModal('edit-milestone-modal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// modal functions
function showAddMilestoneModal() {
    document.getElementById('add-milestone-modal').classList.remove('hidden');
    document.getElementById('add-milestone-modal').classList.add('flex');
}

function showApproveModal(reportId) {
    const form = document.getElementById('approve-form');
    form.action = `/institution/projects/{{ $project->id }}/reports/${reportId}/approve`;
    document.getElementById('approve-modal').classList.remove('hidden');
    document.getElementById('approve-modal').classList.add('flex');
}

function showRejectModal(reportId) {
    const form = document.getElementById('reject-form');
    form.action = `/institution/projects/{{ $project->id }}/reports/${reportId}/reject`;
    document.getElementById('reject-modal').classList.remove('hidden');
    document.getElementById('reject-modal').classList.add('flex');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
    document.getElementById(modalId).classList.remove('flex');
}

// update project status
function updateProjectStatus() {
    const status = document.getElementById('project-status').value;
    
    fetch(`/institution/projects/{{ $project->id }}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status berhasil diupdate!');
            location.reload();
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error);
    });
}

// edit milestone
function editMilestone(milestoneId) {
    // fetch milestone data
    fetch(`/institution/projects/{{ $project->id }}/milestones/${milestoneId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const milestone = data.milestone;

                // populate form
                document.getElementById('edit-milestone-id').value = milestoneId;
                document.getElementById('edit-milestone-title').value = milestone.title;
                document.getElementById('edit-milestone-description').value = milestone.description || '';
                document.getElementById('edit-milestone-target-date').value = milestone.target_date;
                document.getElementById('edit-milestone-status').value = milestone.status;

                // show modal
                document.getElementById('edit-milestone-modal').classList.remove('hidden');
                document.getElementById('edit-milestone-modal').classList.add('flex');
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan: ' + error);
        });
}

// submit edit milestone
function submitEditMilestone(event) {
    event.preventDefault();

    const milestoneId = document.getElementById('edit-milestone-id').value;
    const formData = {
        title: document.getElementById('edit-milestone-title').value,
        description: document.getElementById('edit-milestone-description').value,
        target_date: document.getElementById('edit-milestone-target-date').value,
        status: document.getElementById('edit-milestone-status').value
    };

    fetch(`/institution/projects/{{ $project->id }}/milestones/${milestoneId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Terjadi kesalahan: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error);
    });
}

// delete milestone
function deleteMilestone(milestoneId) {
    if (!confirm('Yakin ingin menghapus milestone ini?')) return;

    fetch(`/institution/projects/{{ $project->id }}/milestones/${milestoneId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan: ' + error);
    });
}
</script>
@endsection