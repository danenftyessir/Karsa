{{-- resources/views/student/projects/create-final-report.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- breadcrumb --}}
        <nav class="mb-6 fade-in-up">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="{{ route('student.projects.index') }}" class="hover:text-blue-600">Proyek Saya</a></li>
                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                <li><a href="{{ route('student.projects.show', $project->id) }}" class="hover:text-blue-600">{{ Str::limit($project->title, 30) }}</a></li>
                <li><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg></li>
                <li class="text-gray-900 font-semibold">Laporan Akhir</li>
            </ol>
        </nav>

        {{-- header --}}
        <div class="mb-8 fade-in-up" style="animation-delay: 0.1s;">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-green-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Submit Laporan Akhir</h1>
                    <p class="text-gray-600">Selesaikan proyek Anda dengan mengirim laporan akhir</p>
                </div>
            </div>
        </div>

        {{-- warning box --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-8 fade-in-up" style="animation-delay: 0.15s;">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h3 class="font-semibold text-yellow-900 mb-2">Perhatian!</h3>
                    <ul class="text-sm text-yellow-800 space-y-1">
                        <li>• Setelah submit laporan akhir, proyek akan ditandai sebagai <strong>selesai</strong></li>
                        <li>• Pastikan semua milestone telah diselesaikan</li>
                        <li>• File yang diupload harus dalam format PDF</li>
                        <li>• Laporan akhir akan direview oleh institusi mitra</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- form --}}
        <form action="{{ route('student.projects.store-final-report', $project->id) }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 fade-in-up" 
              style="animation-delay: 0.2s;">
            @csrf

            {{-- ringkasan eksekutif --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                    Ringkasan Eksekutif <span class="text-red-500">*</span>
                </label>
                <p class="text-sm text-gray-600 mb-3">Ringkasan singkat tentang proyek, tujuan, kegiatan yang dilakukan, dan hasil yang dicapai</p>
                <textarea name="summary" 
                          rows="6" 
                          required
                          placeholder="Tuliskan ringkasan eksekutif proyek Anda..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('summary') border-red-500 @enderror">{{ old('summary') }}</textarea>
                @error('summary')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- kegiatan lengkap --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                    Deskripsi Kegiatan Lengkap <span class="text-red-500">*</span>
                </label>
                <p class="text-sm text-gray-600 mb-3">Jelaskan secara detail semua kegiatan yang telah dilakukan selama proyek</p>
                <textarea name="activities" 
                          rows="8" 
                          required
                          placeholder="Jelaskan kegiatan yang telah dilakukan, metodologi, pendekatan, dan proses pelaksanaan..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('activities') border-red-500 @enderror">{{ old('activities') }}</textarea>
                @error('activities')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- impact metrics --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Impact Metrics</h3>
                <p class="text-sm text-gray-600 mb-4">Berikan data kuantitatif tentang dampak proyek Anda</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Jumlah Penerima Manfaat
                        </label>
                        <input type="number" 
                               name="beneficiaries" 
                               value="{{ old('beneficiaries', 0) }}"
                               min="0"
                               placeholder="Contoh: 100"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('beneficiaries') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Berapa orang yang terdampak langsung dari proyek ini?</p>
                        @error('beneficiaries')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Jumlah Kegiatan Terlaksana
                        </label>
                        <input type="number" 
                               name="activities_count" 
                               value="{{ old('activities_count', 0) }}"
                               min="0"
                               placeholder="Contoh: 15"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('activities_count') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Berapa kegiatan yang telah dilaksanakan?</p>
                        @error('activities_count')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- upload laporan akhir --}}
            <div class="mb-8">
                <label class="block text-sm font-semibold text-gray-900 mb-2">
                    Upload Laporan Akhir (PDF) <span class="text-red-500">*</span>
                </label>
                <p class="text-sm text-gray-600 mb-3">File laporan lengkap dalam format PDF, maksimal 20MB</p>
                
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    
                    <input type="file" 
                           name="final_report" 
                           accept=".pdf"
                           required
                           class="hidden"
                           id="finalReportUpload"
                           onchange="updateFinalReportName(this)">
                    
                    <label for="finalReportUpload" class="cursor-pointer">
                        <span class="text-blue-600 hover:text-blue-700 font-semibold text-lg">Upload File</span>
                        <span class="text-gray-600"> atau drag and drop</span>
                    </label>
                    
                    <p class="text-sm text-gray-500 mt-2">PDF maksimal 20MB</p>
                    <p id="finalReportName" class="text-sm text-gray-900 mt-3 font-medium"></p>
                </div>
                
                @error('final_report')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- checklist konfirmasi --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                <h4 class="font-semibold text-gray-900 mb-4">Checklist Konfirmasi</h4>
                <div class="space-y-3">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" 
                               required
                               class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Saya menyatakan bahwa semua informasi yang diberikan adalah benar dan akurat</span>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" 
                               required
                               class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Laporan ini adalah hasil kerja saya sendiri dan tidak melanggar hak cipta</span>
                    </label>
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" 
                               required
                               class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Saya memahami bahwa laporan akan direview oleh institusi mitra</span>
                    </label>
                </div>
            </div>

            {{-- action buttons --}}
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-lg hover:shadow-lg transition-all font-bold text-lg">
                    Submit Laporan Akhir & Selesaikan Proyek
                </button>
                <a href="{{ route('student.projects.show', $project->id) }}" 
                   class="px-6 py-4 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                    Batal
                </a>
            </div>

        </form>

    </div>
</div>

<script>
function updateFinalReportName(input) {
    const fileName = document.getElementById('finalReportName');
    if (input.files && input.files.length > 0) {
        const file = input.files[0];
        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
        fileName.innerHTML = `📄 <strong>${file.name}</strong> (${sizeMB} MB)`;
    } else {
        fileName.textContent = '';
    }
}

// prevent accidental form submission
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
    const confirmed = confirm('Apakah Anda yakin ingin submit laporan akhir? Proyek akan ditandai sebagai selesai dan tidak dapat diubah lagi.');
    if (!confirmed) {
        e.preventDefault();
    }
});
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