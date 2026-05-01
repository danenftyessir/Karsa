@extends('layouts.app')

@section('title', 'Detail Review - ' . config('app.name'))

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
            <span class="text-gray-900 font-medium">Detail Review</span>
        </nav>

        {{-- header section --}}
        <div class="bg-white rounded-xl shadow-sm p-8 mb-6 border border-gray-100">
            @if($review->reviewee && $review->reviewee->student)
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-start gap-6">
                        {{-- student avatar --}}
                        <img src="{{ $review->reviewee->profile_picture ? asset('storage/' . $review->reviewee->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($review->reviewee->name) }}" 
                             alt="{{ $review->reviewee->name }}"
                             class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 shadow-md">
                        
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $review->reviewee->name }}</h1>
                            <div class="space-y-2 text-gray-600">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span>{{ $review->reviewee->student->university->name }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span>{{ $review->reviewee->student->major }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Direview {{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- action buttons --}}
                    <div class="flex items-center gap-2">
                        @if($review->created_at->addDays(30)->isFuture())
                            <a href="{{ route('institution.reviews.edit', $review->id) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Review
                            </a>
                        @endif
                        
                        <a href="{{ route('institution.reviews.index') }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            @else
                {{-- fallback jika data reviewee atau student tidak ada --}}
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-yellow-800 mb-1">Data Mahasiswa Tidak Tersedia</h3>
                            <p class="text-sm text-yellow-700">Informasi mahasiswa untuk review ini tidak dapat ditemukan. Mungkin akun mahasiswa telah dihapus.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- project info --}}
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Informasi Proyek</h3>
                <div class="space-y-1 text-sm text-gray-600">
                    <p><span class="font-medium text-gray-700">Proyek:</span> {{ $review->project->problem->title }}</p>
                    <p><span class="font-medium text-gray-700">Tanggal Review:</span> {{ $review->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- rating section --}}
        <div class="bg-white rounded-xl shadow-sm p-8 mb-6 border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Rating & Penilaian</h2>
            
            {{-- overall rating --}}
            <div class="flex items-center gap-4 mb-8 pb-8 border-b border-gray-200">
                <div class="text-center">
                    <div class="text-5xl font-bold text-blue-600 mb-2">{{ number_format($review->rating, 1) }}</div>
                    <div class="flex text-yellow-400 justify-center mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-6 h-6 {{ $i <= $review->rating ? 'fill-current' : 'fill-gray-300' }}" viewBox="0 0 24 24">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-600">Rating Keseluruhan</p>
                </div>

                {{-- detailed ratings --}}
                <div class="flex-1 space-y-3">
                    @if($review->professionalism_rating)
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700 w-32">Profesionalisme</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($review->professionalism_rating / 5) * 100 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 w-8">{{ $review->professionalism_rating }}</span>
                    </div>
                    @endif

                    @if($review->communication_rating)
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700 w-32">Komunikasi</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($review->communication_rating / 5) * 100 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 w-8">{{ $review->communication_rating }}</span>
                    </div>
                    @endif

                    @if($review->quality_rating)
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700 w-32">Kualitas Kerja</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($review->quality_rating / 5) * 100 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 w-8">{{ $review->quality_rating }}</span>
                    </div>
                    @endif

                    @if($review->timeliness_rating)
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700 w-32">Ketepatan Waktu</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($review->timeliness_rating / 5) * 100 }}%"></div>
                        </div>
                        <span class="text-sm font-semibold text-gray-900 w-8">{{ $review->timeliness_rating }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- review text --}}
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Ulasan</h3>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-700 leading-relaxed">{{ $review->review_text }}</p>
                </div>
            </div>

            {{-- strengths --}}
            @if($review->strengths)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Kelebihan
                </h3>
                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                    <p class="text-gray-700 leading-relaxed">{{ $review->strengths }}</p>
                </div>
            </div>
            @endif

            {{-- improvements --}}
            @if($review->improvements)
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Area Pengembangan
                </h3>
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <p class="text-gray-700 leading-relaxed">{{ $review->improvements }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- metadata --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Review</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-600 mb-1">Status</p>
                    <p class="font-semibold text-gray-900">
                        @if($review->is_public)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                                Publik
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"/>
                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
                                </svg>
                                Privat
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-600 mb-1">Tanggal Dibuat</p>
                    <p class="font-semibold text-gray-900">{{ $review->created_at->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-1">Terakhir Diupdate</p>
                    <p class="font-semibold text-gray-900">{{ $review->updated_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection