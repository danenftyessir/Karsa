{{-- resources/views/student/wishlist/index.blade.php --}}
{{-- halaman wishlist/saved problems --}}

@extends('layouts.app')

@section('title', 'Wishlist Saya')

@push('styles')
<style>
/* wishlist card animations */
.wishlist-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    will-change: transform;
}

.wishlist-card:hover {
    transform: translateY(-4px) scale3d(1.01, 1.01, 1);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

/* notes textarea */
.notes-textarea {
    transition: all 0.3s ease;
}

.notes-textarea:focus {
    max-height: 200px;
}

/* delete button animation */
.delete-btn {
    transition: all 0.2s ease;
}

.delete-btn:hover {
    transform: scale(1.1);
}

/* smooth entrance animation */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translate3d(0, 30px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

.slide-in-up {
    animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* saved badge animation */
@keyframes heartBeat {
    0%, 100% { transform: scale(1); }
    10%, 30% { transform: scale(0.9); }
    20%, 40%, 60%, 80% { transform: scale(1.1); }
    50%, 70% { transform: scale(1.05); }
}

.heart-beat {
    animation: heartBeat 1.3s ease-in-out;
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8" x-data="wishlistPage()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- header section -->
        <div class="mb-8 slide-in-up">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Wishlist Saya</h1>
                    <p class="text-gray-600 mt-1">Proyek yang Anda simpan untuk dilihat nanti</p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg font-semibold">
                        {{ $wishlists->total() }} Proyek Tersimpan
                    </span>
                    <a href="{{ route('student.browse-problems.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari Proyek Lain
                    </a>
                </div>
            </div>
        </div>

        @if($wishlists->isEmpty())
            <!-- empty state -->
            <div class="bg-white rounded-xl shadow-sm p-12 text-center slide-in-up">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-4">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Wishlist Masih Kosong</h3>
                <p class="text-gray-600 mb-6">Mulai eksplorasi dan simpan proyek yang Anda minati!</p>
                <a href="{{ route('student.browse-problems.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Jelajahi Proyek
                </a>
            </div>
        @else
            <!-- wishlist grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($wishlists as $wishlist)
                <div class="wishlist-card bg-white rounded-xl shadow-sm hover:shadow-lg overflow-hidden border border-gray-100 slide-in-up" 
                     style="animation-delay: {{ $loop->index * 0.1 }}s">
                    
                    <!-- saved badge -->
                    <div class="absolute top-3 right-3 z-10">
                        <span class="px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-full shadow-lg heart-beat flex items-center">
                            <svg class="w-3 h-3 mr-1 fill-current" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                            </svg>
                            Tersimpan
                        </span>
                    </div>

                    <!-- thumbnail -->
                    @if($wishlist->problem->images->isNotEmpty())
                        <div class="relative h-48 overflow-hidden bg-gradient-to-br from-blue-500 to-green-500">
                            <img src="{{ supabase_url($wishlist->problem->images->first()->image_path) }}" 
                                 alt="{{ $wishlist->problem->title }}"
                                 class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                                 loading="lazy">
                        </div>
                    @else
                        <div class="h-48 bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center">
                            <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    @endif

                    <!-- content -->
                    <div class="p-5">
                        <!-- institution -->
                        <div class="flex items-center mb-3">
                            @if($wishlist->problem->institution->logo_path)
                            <img src="{{ supabase_url($wishlist->problem->institution->logo_path) }}" 
                                 alt="{{ $wishlist->problem->institution->name }}"
                                 class="w-8 h-8 rounded-full object-cover mr-2"
                                 loading="lazy">
                            @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-green-500 flex items-center justify-center mr-2">
                                <span class="text-white text-xs font-bold">
                                    {{ strtoupper(substr($wishlist->problem->institution->name, 0, 1)) }}
                                </span>
                            </div>
                            @endif
                            <span class="text-sm text-gray-600 truncate">{{ $wishlist->problem->institution->name }}</span>
                        </div>

                        <!-- title -->
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                            {{ $wishlist->problem->title }}
                        </h3>

                        <!-- details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                {{ $wishlist->problem->regency->name }}, {{ $wishlist->problem->province->name }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                {{ $wishlist->problem->required_students }} mahasiswa
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Deadline: {{ \Carbon\Carbon::parse($wishlist->problem->application_deadline)->format('d M Y') }}
                            </div>
                        </div>

                        <!-- notes (if any) -->
                        @if($wishlist->notes)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                            <p class="text-xs text-gray-600 mb-1 font-semibold">Catatan Saya:</p>
                            <p class="text-sm text-gray-700">{{ $wishlist->notes }}</p>
                        </div>
                        @endif

                        <!-- actions -->
                        <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                            <a href="{{ route('student.browse-problems.show', $wishlist->problem->id) }}" 
                               class="flex-1 text-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 hover:shadow-md">
                                Lihat Detail
                            </a>
                            <button onclick="removeFromWishlist({{ $wishlist->id }})" 
                                    class="delete-btn px-4 py-2 border border-red-300 text-red-600 text-sm font-semibold rounded-lg hover:bg-red-50 hover:border-red-500 transition-all duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- gunakan pagination laravel default, BUKAN custom pagination --}}
            <div class="mt-8">
                {{ $wishlists->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// alpine component untuk wishlist page
function wishlistPage() {
    return {
        init() {
            console.log('Wishlist page initialized');
        }
    }
}

// fungsi untuk remove dari wishlist
async function removeFromWishlist(wishlistId) {
    if (!confirm('Apakah Anda yakin ingin menghapus proyek ini dari wishlist?')) {
        return;
    }

    try {
        const response = await fetch(`/student/wishlist/${wishlistId}/remove`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // reload page untuk update tampilan
            window.location.reload();
        } else {
            alert(data.message || 'Gagal menghapus dari wishlist');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus dari wishlist');
    }
}
</script>
@endpush