{{-- resources/views/student/projects/create-report.blade.php --}}
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
                <li class="text-gray-900 font-semibold">Buat Laporan</li>
            </ol>
        </nav>

        {{-- header --}}
        <div class="mb-8 fade-in-up" style="animation-delay: 0.1s;">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Buat Laporan Progress</h1>
            <p class="text-gray-600">Dokumentasikan kemajuan proyek Anda</p>
        </div>

        {{-- form --}}
        <form action="{{ route('student.projects.store-report', $project->id) }}" 
              method="POST" 
              enctype="multipart/form-data"
              class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 fade-in-up" 
              style="animation-delay: 0.2s;">
            @csrf

            {{-- tipe laporan --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Tipe Laporan <span class="text-red-500">*</span></label>
                <select name="type" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                    <option value="">Pilih Tipe Laporan</option>
                    <option value="weekly" {{ old('type') == 'weekly' ? 'selected' : '' }}>Laporan Mingguan</option>
                    <option value="monthly" {{ old('type') == 'monthly' ? 'selected' : '' }}>Laporan Bulanan</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- judul --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Judul Laporan <span class="text-red-500">*</span></label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title') }}"
                       required
                       placeholder="Contoh: Laporan Mingguan - Minggu ke-1"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- periode --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Periode Mulai <span class="text-red-500">*</span></label>
                    <input type="date" 
                           name="period_start" 
                           value="{{ old('period_start') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('period_start') border-red-500 @enderror">
                    @error('period_start')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Periode Berakhir <span class="text-red-500">*</span></label>
                    <input type="date" 
                           name="period_end" 
                           value="{{ old('period_end') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('period_end') border-red-500 @enderror">
                    @error('period_end')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ringkasan --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Ringkasan Kegiatan <span class="text-red-500">*</span></label>
                <textarea name="summary" 
                          rows="4" 
                          required
                          placeholder="Tuliskan ringkasan singkat tentang kegiatan yang telah dilakukan..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('summary') border-red-500 @enderror">{{ old('summary') }}</textarea>
                @error('summary')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- kegiatan detail --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Detail Kegiatan <span class="text-red-500">*</span></label>
                <textarea name="activities" 
                          rows="6" 
                          required
                          placeholder="Jelaskan secara detail kegiatan yang telah dilakukan, metodologi, dan hasil yang dicapai..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('activities') border-red-500 @enderror">{{ old('activities') }}</textarea>
                @error('activities')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- kendala --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Kendala/Tantangan (Optional)</label>
                <textarea name="challenges" 
                          rows="4" 
                          placeholder="Jelaskan kendala atau tantangan yang dihadapi (jika ada)..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('challenges') border-red-500 @enderror">{{ old('challenges') }}</textarea>
                @error('challenges')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- rencana selanjutnya --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Rencana Selanjutnya (Optional)</label>
                <textarea name="next_plans" 
                          rows="4" 
                          placeholder="Jelaskan rencana kegiatan untuk periode berikutnya..."
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('next_plans') border-red-500 @enderror">{{ old('next_plans') }}</textarea>
                @error('next_plans')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- upload dokumen --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Upload Dokumen (Optional)</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <input type="file" 
                           name="document" 
                           accept=".pdf,.doc,.docx"
                           class="hidden"
                           id="documentUpload"
                           onchange="updateFileName(this)">
                    <label for="documentUpload" class="cursor-pointer">
                        <span class="text-blue-600 hover:text-blue-700 font-medium">Upload file</span>
                        <span class="text-gray-600"> atau drag and drop</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-2">PDF, DOC, DOCX maksimal 10MB</p>
                    <p id="fileName" class="text-sm text-gray-700 mt-2 font-medium"></p>
                </div>
                @error('document')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- upload foto --}}
            <div class="mb-8">
                <label class="block text-sm font-semibold text-gray-900 mb-2">Upload Foto Dokumentasi (Optional)</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <input type="file" 
                           name="photos[]" 
                           accept="image/*"
                           multiple
                           class="hidden"
                           id="photosUpload"
                           onchange="updatePhotoCount(this)">
                    <label for="photosUpload" class="cursor-pointer">
                        <span class="text-blue-600 hover:text-blue-700 font-medium">Upload foto</span>
                        <span class="text-gray-600"> atau drag and drop</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-2">JPG, PNG maksimal 5MB per foto (bisa multiple)</p>
                    <p id="photoCount" class="text-sm text-gray-700 mt-2 font-medium"></p>
                </div>
                @error('photos.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- action buttons --}}
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-lg hover:shadow-lg transition-all font-semibold">
                    Submit Laporan
                </button>
                <a href="{{ route('student.projects.show', $project->id) }}" 
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                    Batal
                </a>
            </div>

        </form>

    </div>
</div>

<script>
function updateFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files.length > 0) {
        fileName.textContent = '📄 ' + input.files[0].name;
    } else {
        fileName.textContent = '';
    }
}

function updatePhotoCount(input) {
    const photoCount = document.getElementById('photoCount');
    if (input.files && input.files.length > 0) {
        photoCount.textContent = `📸 ${input.files.length} foto dipilih`;
    } else {
        photoCount.textContent = '';
    }
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