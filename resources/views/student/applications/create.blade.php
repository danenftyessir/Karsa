{{-- resources/views/student/applications/create.blade.php --}}
{{-- form untuk apply ke proyek --}}

@extends('layouts.app')

@section('title', 'Apply Ke Proyek')

@push('styles')
<style>
/* form animations */
.form-container {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* input focus effects */
.form-input, .form-textarea {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-input:focus, .form-textarea:focus {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* character counter */
.char-counter {
    transition: color 0.3s ease;
}

.char-counter.warning {
    color: #f59e0b;
}

.char-counter.danger {
    color: #ef4444;
}

/* file upload area */
.file-upload-area {
    transition: all 0.3s ease;
    border: 2px dashed #d1d5db;
}

.file-upload-area:hover {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

.file-upload-area.dragging {
    border-color: #3b82f6;
    background-color: #dbeafe;
    transform: scale(1.02);
}

/* submit button animation */
.submit-btn {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.submit-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.submit-btn:active::before {
    width: 300px;
    height: 300px;
}

/* tips card */
.tips-card {
    transition: all 0.3s ease;
}

.tips-card:hover {
    transform: translateX(4px);
}

/* reduced motion support */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- back button -->
        <div class="mb-6">
            <a href="{{ route('student.browse-problems.show', $problem->id) }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali Ke Detail Proyek
            </a>
        </div>

        <!-- header -->
        <div class="form-container bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start">
                @if($problem->institution->logo_path)
                <img src="{{ $problem->institution->getLogoUrl() }}" 
                     alt="{{ $problem->institution->name }}"
                     class="w-16 h-16 rounded-lg object-cover mr-4">
                @else
                <div class="w-16 h-16 rounded-lg bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center text-white text-2xl font-bold mr-4">
                    {{ strtoupper(substr($problem->institution->name, 0, 1)) }}
                </div>
                @endif
                
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $problem->title }}</h1>
                    <p class="text-gray-600 mb-3">{{ $problem->institution->name }}</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-md">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $problem->regency->name ?? 'Lokasi' }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 text-sm rounded-md">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Deadline: {{ $problem->application_deadline->format('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- main form -->
            <div class="lg:col-span-2">
                <div class="form-container bg-white rounded-xl shadow-sm border border-gray-200 p-6" style="animation-delay: 0.1s;">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Form Aplikasi</h2>

                    <form action="{{ route('student.applications.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data"
                          x-data="applicationForm()"
                          @submit="submitForm">
                        @csrf

                        {{-- hidden problem id --}}
                        <input type="hidden" name="problem_id" value="{{ $problem->id }}">

                        <!-- motivation -->
                        <div class="mb-6">
                            <label for="motivation" class="block text-sm font-semibold text-gray-900 mb-2">
                                Motivasi <span class="text-red-500">*</span>
                            </label>
                            <p class="text-sm text-gray-600 mb-3">Jelaskan mengapa Anda tertarik dengan proyek ini dan apa yang bisa Anda kontribusikan (minimal 100 karakter)</p>
                            <textarea id="motivation" 
                                      name="motivation"
                                      rows="6"
                                      x-model="motivation"
                                      @input="updateCharCount"
                                      class="form-textarea w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Contoh: Saya tertarik dengan proyek ini karena saya memiliki pengalaman dalam..."
                                      required>{{ old('motivation') }}</textarea>
                            <div class="flex justify-between items-center mt-2">
                                <p class="text-sm" 
                                   :class="charCount < 100 ? 'text-red-600' : 'text-green-600'"
                                   x-text="charCount + ' / min. 100 karakter'"></p>
                                <p class="text-sm char-counter" 
                                   :class="{
                                       'danger': charCount > 1800,
                                       'warning': charCount > 1500 && charCount <= 1800,
                                       'text-gray-500': charCount <= 1500
                                   }"
                                   x-text="'Maksimal: ' + charCount + ' / 2000'"></p>
                            </div>
                            @error('motivation')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- proposal upload (optional) -->
                        <div class="mb-6">
                            <label for="proposal" class="block text-sm font-semibold text-gray-900 mb-2">
                                Upload Proposal <span class="text-gray-500 font-normal">(Opsional)</span>
                            </label>
                            <p class="text-sm text-gray-600 mb-3">Format: PDF, DOC, DOCX. Maksimal 5MB</p>
                            
                            <div class="file-upload-area rounded-lg p-6 text-center cursor-pointer"
                                 @click="$refs.proposalInput.click()"
                                 @dragover.prevent="isDragging = true"
                                 @dragleave.prevent="isDragging = false"
                                 @drop.prevent="handleDrop"
                                 :class="{'dragging': isDragging}">
                                
                                <input type="file" 
                                       x-ref="proposalInput"
                                       name="proposal"
                                       id="proposal"
                                       accept=".pdf,.doc,.docx"
                                       @change="handleFileSelect"
                                       class="hidden">
                                
                                <div x-show="!fileName">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span class="font-semibold text-blue-600">Klik untuk upload</span> atau drag and drop
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">PDF, DOC, DOCX (Max. 5MB)</p>
                                </div>
                                
                                <div x-show="fileName" class="flex items-center justify-center">
                                    <svg class="h-8 w-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span x-text="fileName" class="text-sm text-gray-700"></span>
                                    <button type="button" 
                                            @click.stop="clearFile"
                                            class="ml-4 text-red-600 hover:text-red-800">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @error('proposal')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- submit buttons -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" 
                                    class="submit-btn flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="submitting"
                                    x-text="submitting ? 'Mengirim...' : 'Kirim Aplikasi'">
                            </button>
                            <a href="{{ route('student.browse-problems.show', $problem->id) }}" 
                               class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 text-center transition-colors">
                                Batal
                            </a>
                        </div>

                        <p class="mt-4 text-sm text-gray-500 text-center">
                            Dengan mengirim aplikasi, Anda menyetujui bahwa informasi yang diberikan adalah benar dan dapat dipertanggungjawabkan.
                        </p>

                    </form>
                </div>
            </div>

            <!-- sidebar info -->
            <div class="lg:col-span-1 space-y-6">
                <!-- tips -->
                <div class="form-container bg-gradient-to-br from-blue-50 to-green-50 rounded-xl shadow-sm border border-blue-100 p-6" style="animation-delay: 0.2s;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">💡 Tips Sukses</h3>
                    <div class="space-y-3">
                        <div class="tips-card p-3 bg-white rounded-lg border border-blue-100">
                            <p class="text-sm text-blue-900 font-medium mb-1">🎯 Spesifik</p>
                            <p class="text-xs text-blue-700">Jelaskan pengalaman atau skill yang relevan dengan proyek</p>
                        </div>
                        
                        <div class="tips-card p-3 bg-white rounded-lg border border-blue-100">
                            <p class="text-sm text-blue-900 font-medium mb-1">🎨 Relevan</p>
                            <p class="text-xs text-blue-700">Hubungkan pengalaman dan skill Anda dengan kebutuhan proyek</p>
                        </div>
                        
                        <div class="tips-card p-3 bg-white rounded-lg border border-blue-100">
                            <p class="text-sm text-blue-900 font-medium mb-1">💡 Inovatif</p>
                            <p class="text-xs text-blue-700">Tunjukkan ide atau perspektif unik yang bisa Anda bawa</p>
                        </div>
                        
                        <div class="tips-card p-3 bg-white rounded-lg border border-blue-100">
                            <p class="text-sm text-blue-900 font-medium mb-1">📄 Proposal (Opsional)</p>
                            <p class="text-xs text-blue-700">Lampirkan proposal untuk meningkatkan peluang diterima</p>
                        </div>
                    </div>
                </div>

                <!-- project info -->
                <div class="form-container bg-white rounded-xl shadow-sm border border-gray-200 p-6" style="animation-delay: 0.3s;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Info Proyek</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-600">Mahasiswa Dibutuhkan</dt>
                            <dd class="text-sm font-semibold text-gray-900 mt-1">{{ $problem->required_students }} Orang</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Sudah Melamar</dt>
                            <dd class="text-sm font-semibold text-gray-900 mt-1">{{ $problem->applications_count }} Aplikasi</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Periode</dt>
                            <dd class="text-sm font-semibold text-gray-900 mt-1">
                                {{ $problem->start_date->format('M Y') }} - {{ $problem->end_date->format('M Y') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-600">Deadline</dt>
                            <dd class="text-sm font-semibold text-red-600 mt-1">
                                {{ $problem->application_deadline->format('d M Y') }}
                                <span class="text-xs text-gray-500">
                                    ({{ $problem->application_deadline->diffForHumans() }})
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function applicationForm() {
    return {
        motivation: '',
        charCount: 0,
        fileName: '',
        isDragging: false,
        submitting: false,
        
        init() {
            this.updateCharCount();
        },
        
        updateCharCount() {
            this.charCount = this.motivation.length;
        },
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.validateFile(file);
            }
        },
        
        handleDrop(event) {
            this.isDragging = false;
            const file = event.dataTransfer.files[0];
            if (file) {
                this.$refs.proposalInput.files = event.dataTransfer.files;
                this.fileName = file.name;
                this.validateFile(file);
            }
        },
        
        validateFile(file) {
            const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            const maxSize = 5 * 1024 * 1024; // 5MB
            
            if (!validTypes.includes(file.type)) {
                alert('Format file tidak valid. Hanya PDF, DOC, dan DOCX yang diperbolehkan.');
                this.clearFile();
                return;
            }
            
            if (file.size > maxSize) {
                alert('Ukuran file terlalu besar. Maksimal 5MB.');
                this.clearFile();
                return;
            }
        },
        
        clearFile() {
            this.fileName = '';
            this.$refs.proposalInput.value = '';
        },
        
        submitForm(event) {
            // validasi minimal karakter motivasi
            if (this.charCount < 100) {
                event.preventDefault();
                alert('Motivasi minimal 100 karakter. Saat ini: ' + this.charCount + ' karakter.');
                return;
            }
            
            this.submitting = true;
        }
    }
}
</script>
@endpush
@endsection