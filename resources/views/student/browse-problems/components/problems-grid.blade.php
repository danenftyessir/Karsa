{{-- resources/views/student/browse-problems/components/problems-grid.blade.php --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($problems as $problem)
        @include('student.browse-problems.components.problem-card', ['problem' => $problem])
    @empty
        <div class="col-span-full">
            <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Proyek Ditemukan</h3>
                <p class="text-gray-600 mb-4">Maaf, tidak ada proyek yang sesuai dengan kriteria pencarian Anda.</p>
                <a href="{{ route('student.browse-problems.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Lihat Semua Proyek
                </a>
            </div>
        </div>
    @endforelse
</div>