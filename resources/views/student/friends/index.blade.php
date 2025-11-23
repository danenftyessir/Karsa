@extends('layouts.app')

@section('title', 'Jaringan - KKN-Go')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/friends.css') }}">
<style>
    .network-hero {
        position: relative;
        min-height: 400px;
        overflow: hidden;
    }

    /* Ensure text is white and on top */
    .network-hero h1,
    .network-hero p,
    .network-hero * {
        color: white !important;
    }

    /* Search bar styling */
    .hero-search {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        padding: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .hero-search input {
        flex: 1;
        border: none;
        padding: 12px 16px;
        font-size: 16px;
        outline: none;
    }

    .hero-search button {
        background: #0a66c2;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .hero-search button:hover {
        background: #004182;
        transform: translateY(-2px);
    }

    /* Add Connection button styling */
    @media (max-width: 768px) {
        .hero-search {
            margin: 0;
        }
    }

    /* Clean list-based design */
    .connection-item {
        background: white;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s ease;
    }

    .connection-item:hover {
        background-color: #f9fafb;
    }

    .connection-item:last-child {
        border-bottom: none;
    }

    /* Profile image styling */
    .profile-image {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }

    /* Subtle section backgrounds */
    .section-with-bg {
        position: relative;
        background: white;
    }

    .section-with-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('/dashboard-student2.jpeg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        opacity: 0.03;
        pointer-events: none;
    }

    /* Clean button styles */
    .btn-primary-linkedin {
        background: #0a66c2;
        color: white;
        padding: 8px 16px;
        border-radius: 24px;
        font-weight: 600;
        font-size: 14px;
        border: 2px solid #0a66c2;
        transition: all 0.2s ease;
    }

    .btn-primary-linkedin:hover {
        background: #004182;
        border-color: #004182;
    }

    .btn-secondary-linkedin {
        background: white;
        color: #0a66c2;
        padding: 8px 16px;
        border-radius: 24px;
        font-weight: 600;
        font-size: 14px;
        border: 2px solid #0a66c2;
        transition: all 0.2s ease;
    }

    .btn-secondary-linkedin:hover {
        background: #f3f6f8;
    }

    /* Stats clean design */
    .stat-item {
        text-align: center;
        padding: 16px;
    }

    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: #0a66c2;
        display: block;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
        margin-top: 4px;
    }
</style>
@endpush

@section('content')
{{-- Hero section - Style marketplace dengan search di tengah --}}
<section class="network-hero flex items-center justify-center">
    {{-- Background image --}}
    <div class="absolute inset-0">
        <img src="{{ asset('network-student.JPG') }}"
             alt="Jaringan KKN-Go"
             class="w-full h-full object-cover">
        {{-- Overlay gradient - lebih gelap di bawah --}}
        <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/40 to-black/70"></div>
    </div>

    {{-- Content - text di tengah --}}
    <div class="relative container mx-auto px-6 text-center">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight mb-3">
                Jaringan Anda
            </h1>
            <p class="text-base md:text-lg text-white/90 font-medium">
                Kelola koneksi profesional Anda
            </p>
        </div>
    </div>
</section>

{{-- Stats bar - clean and minimal --}}
<div class="bg-white border-b border-gray-200">
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-3 divide-x divide-gray-200">
            <div class="stat-item">
                <span class="stat-number">{{ $stats['total_friends'] }}</span>
                <span class="stat-label">Koneksi</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $stats['pending_requests'] }}</span>
                <span class="stat-label">Permintaan</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $stats['sent_requests'] }}</span>
                <span class="stat-label">Terkirim</span>
            </div>
        </div>
    </div>
</div>

{{-- Search bar dan Add Connection --}}
<div class="py-4 border-b border-gray-200">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex flex-col md:flex-row gap-3 items-center justify-center">
            <div class="hero-search flex-1 w-full md:max-w-xl">
                <svg class="w-6 h-6 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       id="hero-search-input"
                       placeholder="Cari teman berdasarkan nama atau universitas..."
                       class="hero-search-input">
                <button type="button" onclick="performHeroSearch()">
                    Cari
                </button>
            </div>
            <a href="{{ route('student.friends.search') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Add Connection
            </a>
        </div>
    </div>
</div>

<div class="bg-gray-50 min-h-screen py-6">
    <div class="max-w-6xl mx-auto px-6">

        {{-- Pending Requests Section --}}
        @if($pendingRequests->count() > 0)
        <div class="mb-6 section-with-bg">
            <div class="relative bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">
                        Permintaan Pertemanan ({{ $pendingRequests->count() }})
                    </h2>
                </div>

                <div>
                    @foreach($pendingRequests as $request)
                    <div class="connection-item px-6 py-4">
                        <div class="flex items-start gap-4">
                            <a href="{{ route('profile.public', $request->requester->user->username) }}">
                                <img src="{{ $request->requester->profile_photo_url }}"
                                     alt="{{ $request->requester->user->name }}"
                                     class="avatar-circle avatar-md">
                            </a>

                            <div class="flex-1 min-w-0">
                                <a href="{{ route('profile.public', $request->requester->user->username) }}"
                                   class="text-lg font-semibold text-gray-900 hover:text-blue-600 hover:underline">
                                    {{ $request->requester->user->name }}
                                </a>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $request->requester->major }} • {{ $request->requester->university->name }}
                                </p>
                                @if($request->message)
                                <p class="text-sm text-gray-700 mt-2 italic">
                                    "{{ $request->message }}"
                                </p>
                                @endif
                                <p class="text-xs text-gray-500 mt-2">
                                    {{ $request->created_at->diffForHumans() }}
                                </p>
                            </div>

                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('student.friends.accept', $request->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary-linkedin">
                                        Terima
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('student.friends.reject', $request->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-secondary-linkedin">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Friends List Section --}}
        <div class="section-with-bg">
            <div class="relative bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">
                        Koneksi Anda ({{ $friends->count() }})
                    </h2>
                    <div class="flex-1 max-w-xs ml-6">
                        <input type="text"
                               id="friend-search"
                               placeholder="Cari koneksi..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                @if($friends->count() > 0)
                <div id="friends-list">
                    @foreach($friends as $friend)
                    <div class="connection-item friend-item px-6 py-4"
                         data-name="{{ strtolower($friend->user->name) }}"
                         data-university="{{ strtolower($friend->university->name ?? '') }}">
                        <div class="flex items-center gap-4">
                            <a href="{{ route('profile.public', $friend->user->username) }}">
                                <img src="{{ $friend->profile_photo_url }}"
                                     alt="{{ $friend->user->name }}"
                                     class="avatar-circle avatar-md">
                            </a>

                            <div class="flex-1 min-w-0">
                                <a href="{{ route('profile.public', $friend->user->username) }}"
                                   class="text-lg font-semibold text-gray-900 hover:text-blue-600 hover:underline">
                                    {{ $friend->user->name }}
                                </a>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $friend->major }} • {{ $friend->university->name }}
                                </p>
                            </div>

                            <a href="{{ route('profile.public', $friend->user->username) }}"
                               class="btn-secondary-linkedin">
                                Lihat Profil
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="px-6 py-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        Belum Ada Koneksi
                    </h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        Mulai bangun jaringan profesional Anda dengan terhubung ke mahasiswa KKN lainnya
                    </p>
                    <a href="{{ route('student.friends.search') }}"
                       class="inline-block btn-primary-linkedin">
                        Cari Teman
                    </a>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// Fungsi search untuk hero section dan friends list
function performSearch(searchTerm) {
    const friendItems = document.querySelectorAll('.friend-item');
    const lowerSearchTerm = searchTerm.toLowerCase();

    friendItems.forEach(item => {
        const name = item.dataset.name;
        const university = item.dataset.university;

        if (name.includes(lowerSearchTerm) || university.includes(lowerSearchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });

    // Scroll ke section friends list
    if (searchTerm) {
        document.getElementById('friends-list')?.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Hero search button click
function performHeroSearch() {
    const heroSearchInput = document.getElementById('hero-search-input');
    const searchTerm = heroSearchInput.value;

    // Sinkronkan dengan search bar di bawah
    const friendSearch = document.getElementById('friend-search');
    if (friendSearch) {
        friendSearch.value = searchTerm;
    }

    performSearch(searchTerm);
}

// Hero search on Enter key
document.getElementById('hero-search-input')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performHeroSearch();
    }
});

// Real-time search on input (untuk search bar di friends list section)
document.getElementById('friend-search')?.addEventListener('input', function(e) {
    performSearch(e.target.value);
});

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
</script>
@endpush
@endsection