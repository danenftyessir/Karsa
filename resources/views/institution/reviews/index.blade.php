@extends('layouts.app')

@section('title', 'Daftar Review Mahasiswa')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Review Mahasiswa</h1>
            <p class="mt-2 text-gray-600">Kelola dan lihat review yang telah Anda berikan</p>
        </div>

        {{-- statistik cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            {{-- total reviews --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Review</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- rating rata-rata --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Rating Rata-rata</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['average_rating'], 1) }}</p>
                        <div class="flex items-center mt-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($stats['average_rating']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- rating 5 bintang --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Rating 5★</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['five_star'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round(($stats['five_star'] / $stats['total']) * 100, 1) : 0 }}% dari total</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- rating 4 bintang --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Rating 4★</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['four_star'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['total'] > 0 ? round(($stats['four_star'] / $stats['total']) * 100, 1) : 0 }}% dari total</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- filter & search section --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <form method="GET" action="{{ route('institution.reviews.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                {{-- search --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Mahasiswa</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Nama mahasiswa..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                </div>

                {{-- filter rating --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <select name="rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="">Semua Rating</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>★★★★★</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>★★★★</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>★★★</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>★★</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>★</option>
                    </select>
                </div>

                {{-- sort --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                    <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="rating_high" {{ request('sort') == 'rating_high' ? 'selected' : '' }}>Rating Tertinggi</option>
                        <option value="rating_low" {{ request('sort') == 'rating_low' ? 'selected' : '' }}>Rating Terendah</option>
                    </select>
                </div>

                <div class="md:col-span-4 flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('institution.reviews.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- reviews list --}}
        <div class="space-y-4">
            @forelse($reviews as $review)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 transition-all duration-300 hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            
                            {{-- FIXED: akses langsung dari reviewee yang sudah di-eager load --}}
                            @if($review->reviewee && $review->reviewee->student)
                                {{-- avatar mahasiswa --}}
                                @if($review->reviewee->profile_photo_url)
                                    <img src="{{ $review->reviewee->profile_photo_url }}" 
                                         alt="{{ $review->reviewee->name }}"
                                         class="w-16 h-16 rounded-full object-cover flex-shrink-0 border-2 border-gray-200">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center flex-shrink-0 text-white text-xl font-bold border-2 border-gray-200">
                                        {{ strtoupper(substr($review->reviewee->name, 0, 1)) }}
                                    </div>
                                @endif
                                
                                <div class="flex-1">
                                    {{-- info mahasiswa --}}
                                    <div class="mb-3">
                                        {{-- FIXED: gunakan reviewee->name langsung --}}
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $review->reviewee->name }}</h3>
                                        {{-- FIXED: gunakan reviewee->student->university->name --}}
                                        <p class="text-sm text-gray-600">{{ $review->reviewee->student->university->name }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Proyek: {{ $review->project->problem->title }}</p>
                                    </div>

                                    {{-- rating --}}
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= $review->rating ? 'fill-current' : 'fill-gray-300' }}" viewBox="0 0 20 20">
                                                    <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($review->rating, 1) }}/5</span>
                                    </div>

                                    {{-- review text --}}
                                    <div class="mb-3">
                                        <p class="text-gray-700 leading-relaxed">{{ $review->review_text }}</p>
                                    </div>

                                    {{-- strengths & improvements --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                        @if($review->strengths)
                                            <div class="bg-green-50 rounded-lg p-3 border border-green-100">
                                                <h4 class="text-sm font-semibold text-green-900 mb-1 flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Kelebihan
                                                </h4>
                                                <p class="text-sm text-green-700">{{ $review->strengths }}</p>
                                            </div>
                                        @endif

                                        @if($review->improvements)
                                            <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                                                <h4 class="text-sm font-semibold text-blue-900 mb-1 flex items-center gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Saran Perbaikan
                                                </h4>
                                                <p class="text-sm text-blue-700">{{ $review->improvements }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- metadata --}}
                                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $review->created_at->format('d M Y') }}
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('institution.reviews.show', $review->id) }}" 
                                               class="text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors">
                                                Lihat Detail
                                            </a>
                                            @if($review->created_at->addDays(30)->isFuture())
                                                <a href="{{ route('institution.reviews.edit', $review->id) }}" 
                                                   class="text-sm text-gray-600 hover:text-gray-700 font-medium transition-colors">
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- fallback jika data tidak lengkap --}}
                                <div class="flex-1">
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <p class="text-sm text-yellow-800">
                                            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Data mahasiswa tidak tersedia untuk review ini.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Review</h3>
                    <p class="text-gray-600">Anda belum memberikan review kepada mahasiswa manapun.</p>
                </div>
            @endforelse
        </div>

        {{-- pagination --}}
        @if($reviews->hasPages())
            <div class="mt-8">
                {{ $reviews->links() }}
            </div>
        @endif

    </div>
</div>
@endsection