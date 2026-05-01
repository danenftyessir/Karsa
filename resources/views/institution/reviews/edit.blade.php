@extends('layouts.app')

@section('title', 'Edit Review - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- breadcrumb --}}
        <nav class="mb-8 flex items-center gap-2 text-sm text-gray-600">
            <a href="{{ route('institution.dashboard') }}" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('institution.reviews.index') }}" class="hover:text-blue-600 transition-colors">Reviews</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-gray-900 font-medium">Edit Review</span>
        </nav>

        {{-- alert jika masa edit hampir habis --}}
        @if($review->created_at->addDays(30)->diffInDays(now()) <= 7)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-lg">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-yellow-800 mb-1">Peringatan Masa Edit</h3>
                    <p class="text-sm text-yellow-700">Review hanya dapat diedit dalam 30 hari setelah dibuat. Waktu tersisa: <span class="font-semibold">{{ $review->created_at->addDays(30)->diffForHumans() }}</span></p>
                </div>
            </div>
        </div>
        @endif

        {{-- header --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            @if($review->reviewee && $review->reviewee->student)
                <div class="flex items-start gap-4">
                    {{-- FIXED: gunakan reviewee langsung, bukan student.user --}}
                    <img src="{{ $review->reviewee->profile_picture ? asset('storage/' . $review->reviewee->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($review->reviewee->name) }}" 
                         alt="{{ $review->reviewee->name }}"
                         class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                    
                    <div class="flex-1">
                        {{-- FIXED: gunakan reviewee.name langsung --}}
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">Edit Review Untuk {{ $review->reviewee->name }}</h1>
                        <div class="space-y-1 text-sm text-gray-600">
                            {{-- FIXED: gunakan reviewee.student.university --}}
                            <p><span class="font-medium text-gray-700">Universitas:</span> {{ $review->reviewee->student->university->name }}</p>
                            <p><span class="font-medium text-gray-700">Proyek:</span> {{ $review->project->problem->title }}</p>
                            <p><span class="font-medium text-gray-700">Direview:</span> {{ $review->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            @else
                {{-- fallback jika data reviewee atau student tidak ada --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-yellow-800 mb-1">Data Mahasiswa Tidak Tersedia</h3>
                            <p class="text-sm text-yellow-700">Informasi mahasiswa untuk review ini tidak dapat ditemukan.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- form edit review --}}
        <form action="{{ route('institution.reviews.update', $review->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- rating section --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <label class="block text-lg font-semibold text-gray-900 mb-4">Rating Keseluruhan <span class="text-red-500">*</span></label>
                
                <div x-data="{ rating: {{ old('rating', $review->rating) }}, hover: 0 }" class="flex items-center gap-4">
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    @click="rating = {{ $i }}"
                                    @mouseenter="hover = {{ $i }}"
                                    @mouseleave="hover = 0"
                                    class="transition-transform duration-200 transform hover:scale-110">
                                <svg class="w-12 h-12 transition-colors duration-200" 
                                     :class="(hover >= {{ $i }} || rating >= {{ $i }}) ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current'"
                                     viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" x-model="rating">
                    <div class="text-2xl font-bold text-gray-900">
                        <span x-text="rating"></span> / 5
                    </div>
                </div>
                @error('rating')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- review text --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <label for="review" class="block text-lg font-semibold text-gray-900 mb-4">
                    Ulasan Anda <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="review" 
                    name="review" 
                    rows="6" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('review') border-red-500 @enderror"
                    placeholder="Ceritakan pengalaman Anda bekerja sama dengan mahasiswa ini..."
                    maxlength="1000"
                    required>{{ old('review', $review->review_text) }}</textarea>
                <div class="flex items-center justify-between mt-2">
                    @error('review')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @else
                        <p class="text-sm text-gray-500">Maksimal 1000 karakter</p>
                    @enderror
                </div>
            </div>

            {{-- strengths --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <label for="strengths" class="block text-lg font-semibold text-gray-900 mb-4">
                    Kelebihan
                    <span class="text-sm font-normal text-gray-600">(Opsional)</span>
                </label>
                <textarea 
                    id="strengths" 
                    name="strengths" 
                    rows="4" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 @error('strengths') border-red-500 @enderror"
                    placeholder="Apa yang menjadi kelebihan mahasiswa ini?"
                    maxlength="500">{{ old('strengths', $review->strengths) }}</textarea>
                @error('strengths')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @else
                    <p class="mt-2 text-sm text-gray-500">Maksimal 500 karakter</p>
                @enderror
            </div>

            {{-- improvements --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <label for="improvements" class="block text-lg font-semibold text-gray-900 mb-4">
                    Area Pengembangan
                    <span class="text-sm font-normal text-gray-600">(Opsional)</span>
                </label>
                <textarea 
                    id="improvements" 
                    name="improvements" 
                    rows="4" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('improvements') border-red-500 @enderror"
                    placeholder="Apa yang bisa ditingkatkan oleh mahasiswa ini?"
                    maxlength="500">{{ old('improvements', $review->improvements) }}</textarea>
                @error('improvements')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @else
                    <p class="mt-2 text-sm text-gray-500">Maksimal 500 karakter</p>
                @enderror
            </div>

            {{-- action buttons --}}
            <div class="flex items-center justify-end gap-4 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <a href="{{ route('institution.reviews.show', $review->id) }}" 
                   class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 font-medium">
                    Batal
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
@endsection