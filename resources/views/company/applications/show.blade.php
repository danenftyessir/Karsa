@extends('layouts.app')

@section('title', 'Detail Lamaran - ' . $application->user->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50" x-data="applicationDetail()">
    <!-- Header Section -->
    <div class="relative bg-cover bg-center text-white py-12 overflow-hidden" style="background-image: url('{{ asset('company1.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex items-center gap-4 mb-6 fade-in-up">
                <a href="{{ route('company.applications.index') }}"
                   class="text-white hover:text-blue-100 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold" style="font-family: 'Space Grotesk', sans-serif; color: white !important;">
                    Detail Lamaran
                </h1>
            </div>

            <!-- Quick Info -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 fade-in-up" style="animation-delay: 0.1s;">
                <div>
                    <h2 class="text-2xl font-bold mb-2" style="color: white !important;">{{ $application->user->name }}</h2>
                    <p class="text-blue-100 mb-2">Melamar untuk: {{ $application->jobPosting->title }}</p>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $application->status_badge_class }}">
                            {{ $application->status_label }}
                        </span>
                        <span class="text-sm text-blue-100">
                            Melamar {{ $application->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Application Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Applicant Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informasi Pelamar
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Nama Lengkap</label>
                            <p class="text-gray-900 font-medium">{{ $application->user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                            <p class="text-gray-900">{{ $application->user->email }}</p>
                        </div>
                        @if($application->expected_salary)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Ekspektasi Gaji</label>
                            <p class="text-gray-900 font-medium">Rp {{ number_format($application->expected_salary, 0, ',', '.') }}</p>
                        </div>
                        @endif
                        @if($application->available_from)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Tersedia Dari</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($application->available_from)->format('d M Y') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Cover Letter -->
                @if($application->cover_letter)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.1s;">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Surat Lamaran
                    </h3>
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $application->cover_letter }}</p>
                </div>
                @endif

                <!-- Documents -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.2s;">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Dokumen
                    </h3>
                    <div class="space-y-3">
                        @if($application->resume_url)
                        <a href="{{ $application->resume_url }}"
                           target="_blank"
                           class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Resume / CV</p>
                                    <p class="text-sm text-gray-500">Klik untuk melihat</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                        @endif

                        @if($application->portfolio_url)
                        <a href="{{ $application->portfolio_url }}"
                           target="_blank"
                           class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Portfolio</p>
                                    <p class="text-sm text-gray-500">Klik untuk melihat</p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Notes -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.3s;">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Catatan Internal
                    </h3>

                    @if($application->notes)
                        <p class="text-gray-700 mb-4 whitespace-pre-line">{{ $application->notes }}</p>
                    @else
                        <p class="text-gray-500 italic mb-4">Belum ada catatan</p>
                    @endif

                    <form @submit.prevent="addNotes()" class="mt-4">
                        <textarea x-model="formData.notes"
                                  rows="3"
                                  placeholder="Tambahkan atau perbarui catatan..."
                                  class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"></textarea>
                        <button type="submit"
                                class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            Simpan Catatan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column - Actions & Status -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi Cepat</h3>
                    <div class="space-y-3">
                        <button @click="showStatusModal = true"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Ubah Status
                        </button>

                        <button @click="showShortlistModal = true"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Shortlist
                        </button>

                        <button @click="showRejectModal = true"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Tolak
                        </button>

                        <button @click="showHireModal = true"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Terima
                        </button>
                    </div>
                </div>

                <!-- Rating -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.1s;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Penilaian</h3>
                    <div class="flex items-center justify-center gap-2 mb-4">
                        <template x-for="star in 5" :key="star">
                            <button @click="setRating(star)"
                                    class="focus:outline-none transition-transform hover:scale-110">
                                <svg :class="star <= (formData.rating || {{ $application->rating ?? 0 }}) ? 'text-yellow-500' : 'text-gray-300'"
                                     class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                    <p class="text-center text-sm text-gray-500">
                        Rating saat ini: <span class="font-semibold text-gray-900" x-text="formData.rating || {{ $application->rating ?? 0 }}"></span>/5
                    </p>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.2s;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Dilamar</p>
                                <p class="text-sm font-medium text-gray-900">{{ $application->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        @if($application->reviewed_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Direview</p>
                                <p class="text-sm font-medium text-gray-900">{{ $application->reviewed_at->format('d M Y, H:i') }}</p>
                                @if($application->reviewer)
                                    <p class="text-xs text-gray-400">oleh {{ $application->reviewer->name }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($application->interview_scheduled_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Interview Dijadwalkan</p>
                                <p class="text-sm font-medium text-gray-900">{{ $application->interview_scheduled_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($application->offer_extended_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Penawaran Diberikan</p>
                                <p class="text-sm font-medium text-gray-900">{{ $application->offer_extended_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($application->hired_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-emerald-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Diterima</p>
                                <p class="text-sm font-medium text-gray-900">{{ $application->hired_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($application->rejected_at)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">Ditolak</p>
                                <p class="text-sm font-medium text-gray-900">{{ $application->rejected_at->format('d M Y, H:i') }}</p>
                                @if($application->rejection_reason)
                                    <p class="text-xs text-gray-600 mt-1">{{ $application->rejection_reason }}</p>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div x-show="showStatusModal"
         x-cloak
         @click.self="showStatusModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 gpu-accelerate"
             @click.stop>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Ubah Status Lamaran</h3>
            <form @submit.prevent="updateStatus()">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru</label>
                    <select x-model="formData.status"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="new">Baru</option>
                        <option value="reviewing">Sedang Direview</option>
                        <option value="shortlisted">Shortlist</option>
                        <option value="interview">Interview</option>
                        <option value="offer">Penawaran Diberikan</option>
                        <option value="hired">Diterima</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="button"
                            @click="showStatusModal = false"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div x-show="showRejectModal"
         x-cloak
         @click.self="showRejectModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 gpu-accelerate"
             @click.stop>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Tolak Lamaran</h3>
            <form @submit.prevent="rejectApplication()">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                    <textarea x-model="formData.rejection_reason"
                              rows="3"
                              placeholder="Tuliskan alasan penolakan..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button"
                            @click="showRejectModal = false"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Shortlist Modal -->
    <div x-show="showShortlistModal"
         x-cloak
         @click.self="showShortlistModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 gpu-accelerate"
             @click.stop>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Shortlist Lamaran</h3>
            <p class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menambahkan pelamar ini ke daftar shortlist?
            </p>
            <div class="flex gap-3">
                <button type="button"
                        @click="showShortlistModal = false"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="button"
                        @click="shortlistApplication()"
                        class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Ya, Shortlist
                </button>
            </div>
        </div>
    </div>

    <!-- Hire Modal -->
    <div x-show="showHireModal"
         x-cloak
         @click.self="showHireModal = false"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 gpu-accelerate"
             @click.stop>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Terima Pelamar</h3>
            <p class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menerima pelamar ini? Pelamar akan dinotifikasi tentang penerimaan mereka.
            </p>
            <div class="flex gap-3">
                <button type="button"
                        @click="showHireModal = false"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="button"
                        @click="hireApplicant()"
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Ya, Terima
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function applicationDetail() {
    return {
        showStatusModal: false,
        showRejectModal: false,
        showShortlistModal: false,
        showHireModal: false,
        formData: {
            status: '{{ $application->status }}',
            rating: {{ $application->rating ?? 0 }},
            notes: '',
            rejection_reason: ''
        },

        async updateStatus() {
            try {
                const response = await fetch('{{ route("company.applications.update-status", $application->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        status: this.formData.status
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showStatusModal = false;
                    window.location.reload();
                } else {
                    alert('Gagal mengubah status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        },

        async setRating(rating) {
            this.formData.rating = rating;

            try {
                const response = await fetch('{{ route("company.applications.add-rating", $application->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        rating: rating
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    alert('Gagal menyimpan rating');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        },

        async addNotes() {
            if (!this.formData.notes.trim()) {
                alert('Catatan tidak boleh kosong');
                return;
            }

            try {
                const response = await fetch('{{ route("company.applications.add-notes", $application->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        notes: this.formData.notes
                    })
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Gagal menyimpan catatan');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        },

        async shortlistApplication() {
            try {
                const response = await fetch('{{ route("company.applications.shortlist", $application->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.showShortlistModal = false;
                    window.location.reload();
                } else {
                    alert('Gagal shortlist lamaran');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        },

        async rejectApplication() {
            try {
                const response = await fetch('{{ route("company.applications.reject", $application->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        reason: this.formData.rejection_reason
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showRejectModal = false;
                    window.location.reload();
                } else {
                    alert('Gagal menolak lamaran');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        },

        async hireApplicant() {
            try {
                const response = await fetch('{{ route("company.applications.hire", $application->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.showHireModal = false;
                    window.location.reload();
                } else {
                    alert('Gagal menerima pelamar');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }

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

/* reduced motion support untuk aksesibilitas */
@media (prefers-reduced-motion: reduce) {
    .fade-in-up {
        animation: none;
        opacity: 1;
    }
}
</style>
@endsection
