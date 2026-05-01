@extends('layouts.app')

@section('title', 'Review Aplikasi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- back button --}}
        <a href="{{ route('institution.applications.show', $application->id) }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2 mb-6">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>

        {{-- header --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Review Aplikasi</h1>
            <p class="text-gray-600">{{ $application->student->user->name }} - {{ $application->problem->title }}</p>
        </div>

        {{-- pilihan action --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Pilih Tindakan</h2>
            <div class="grid grid-cols-2 gap-4">
                <button onclick="showAcceptForm()" 
                        id="accept-btn"
                        class="px-6 py-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-semibold flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Terima Aplikasi
                </button>

                <button onclick="showRejectForm()" 
                        id="reject-btn"
                        class="px-6 py-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 font-semibold flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Tolak Aplikasi
                </button>
            </div>
        </div>

        {{-- form terima --}}
        <div id="accept-form" class="hidden">
            <form method="POST" action="{{ route('institution.applications.accept', $application->id) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                @csrf

                <h2 class="text-xl font-bold text-gray-900 mb-4">Terima Aplikasi</h2>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-green-900">Proyek akan otomatis dibuat</p>
                            <p class="text-sm text-green-700 mt-1">Setelah menerima aplikasi ini, proyek akan dibuat dan mahasiswa dapat mulai bekerja.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- feedback opsional --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Feedback (Opsional)</label>
                        <textarea name="feedback" 
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                  placeholder="Berikan feedback positif atau arahan untuk mahasiswa...">{{ old('feedback') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Feedback ini akan dikirim ke mahasiswa bersama pemberitahuan penerimaan</p>
                    </div>

                    {{-- catatan internal --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Internal (Opsional)</label>
                        <textarea name="notes" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                  placeholder="Catatan untuk tim internal (tidak dilihat mahasiswa)...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                        Konfirmasi Terima
                    </button>
                    <button type="button" 
                            onclick="hideAllForms()"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                        Batal
                    </button>
                </div>
            </form>
        </div>

        {{-- form tolak --}}
        <div id="reject-form" class="hidden">
            <form method="POST" action="{{ route('institution.applications.reject', $application->id) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                @csrf

                <h2 class="text-xl font-bold text-gray-900 mb-4">Tolak Aplikasi</h2>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-red-900">Pastikan memberikan feedback yang konstruktif</p>
                            <p class="text-sm text-red-700 mt-1">Feedback yang baik membantu mahasiswa berkembang untuk peluang berikutnya.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    {{-- feedback wajib --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Feedback *</label>
                        <textarea name="feedback" 
                                  rows="4"
                                  required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                  placeholder="Berikan feedback yang membangun untuk mahasiswa...">{{ old('feedback') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Contoh: "Proposal Anda sangat baik, namun kami mencari kandidat dengan pengalaman lebih spesifik di bidang X."</p>
                        @error('feedback')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- alasan penolakan wajib --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Penolakan *</label>
                        <textarea name="rejection_reason" 
                                  rows="3"
                                  required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                  placeholder="Jelaskan alasan penolakan secara singkat...">{{ old('rejection_reason') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Ini akan membantu mahasiswa memahami keputusan Anda</p>
                        @error('rejection_reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
                        Konfirmasi Tolak
                    </button>
                    <button type="button" 
                            onclick="hideAllForms()"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                        Batal
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
function showAcceptForm() {
    hideAllForms();
    document.getElementById('accept-form').classList.remove('hidden');
    document.getElementById('accept-btn').classList.add('ring-4', 'ring-green-200');
    document.getElementById('reject-btn').classList.remove('ring-4', 'ring-red-200');
}

function showRejectForm() {
    hideAllForms();
    document.getElementById('reject-form').classList.remove('hidden');
    document.getElementById('reject-btn').classList.add('ring-4', 'ring-red-200');
    document.getElementById('accept-btn').classList.remove('ring-4', 'ring-green-200');
}

function hideAllForms() {
    document.getElementById('accept-form').classList.add('hidden');
    document.getElementById('reject-form').classList.add('hidden');
    document.getElementById('accept-btn').classList.remove('ring-4', 'ring-green-200');
    document.getElementById('reject-btn').classList.remove('ring-4', 'ring-red-200');
}
</script>
@endsection