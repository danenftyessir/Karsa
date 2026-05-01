{{-- resources/views/student/browse-problems/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Browse Problems')

@push('styles')
{{-- Import Google Font - Space Grotesk for Hero --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">

{{-- Leaflet CSS - Load BEFORE any scripts --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

<link rel="stylesheet" href="{{ asset('css/browse-problems.css') }}">
<style>
    /* Hero section style mirip dashboard */
    .marketplace-hero-browse {
        position: relative;
        background-image:
            linear-gradient(135deg, rgba(99, 102, 241, 0.35) 0%, rgba(129, 140, 248, 0.30) 50%, rgba(156, 163, 175, 0.25) 100%),
            url('/dashboard-student.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        min-height: 480px;
    }

    .hero-title-browse {
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .text-shadow-strong {
        text-shadow:
            0 2px 4px rgba(0, 0, 0, 0.4),
            0 4px 8px rgba(0, 0, 0, 0.3);
    }

    .browse-fade-in {
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Background style seperti homepage */
    .gradient-mesh-bg {
        background-color: #ffffff;
        background-image:
            radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
            radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.12) 0px, transparent 50%),
            radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
            radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.12) 0px, transparent 50%),
            radial-gradient(at 50% 50%, rgba(147, 51, 234, 0.1) 0px, transparent 50%);
    }

    /* Pagination styling - minimalist tegas */
    .pagination-section nav {
        display: inline-flex;
        background-color: white;
        border-radius: 0.5rem;
        padding: 0.375rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .pagination-section .flex {
        gap: 0.25rem;
        align-items: center;
    }

    /* Base styling untuk semua pagination items */
    .pagination-section a,
    .pagination-section span {
        min-width: 2.25rem;
        height: 2.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280 !important;
        background-color: transparent !important;
        border: none !important;
        border-radius: 0.375rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        cursor: pointer;
    }

    /* Hover state */
    .pagination-section a:hover {
        background-color: #f3f4f6 !important;
        color: #111827 !important;
    }

    /* Active/current page */
    .pagination-section .relative span[aria-current="page"] {
        background-color: #6366f1 !important;
        color: white !important;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.25);
    }

    /* Disabled state (Previous/Next when not available) */
    .pagination-section span[aria-disabled="true"] {
        color: #d1d5db !important;
        cursor: not-allowed;
        opacity: 0.5;
    }

    .pagination-section span[aria-disabled="true"]:hover {
        background-color: transparent !important;
    }

    /* Arrow icons styling */
    .pagination-section svg {
        width: 1.125rem;
        height: 1.125rem;
    }

    /* Hide "Previous" and "Next" text, show only arrows */
    .pagination-section a > span:not(.sr-only),
    .pagination-section span > span:not(.sr-only) {
        display: none;
    }

    /* Show only arrow SVGs */
    .pagination-section svg {
        display: block !important;
    }

    /* Hide "Showing X to Y of Z results" text */
    .pagination-section p,
    .pagination-section nav + p,
    .pagination-section p.text-sm {
        display: none !important;
    }

    /* Responsive: stack vertically on mobile */
    @media (max-width: 640px) {
        #problems-pagination {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        #problems-pagination .pagination-section {
            width: 100%;
            display: flex;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen gradient-mesh-bg">
    
    {{-- marketplace-style hero section mirip dashboard --}}
    <section class="marketplace-hero-browse text-white relative flex items-center justify-center">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10 w-full">
            <div class="max-w-4xl mx-auto text-center">
                <div class="browse-fade-in">
                    {{-- Judul dan deskripsi --}}
                    <h1 class="hero-title-browse text-4xl md:text-6xl font-bold mb-6 text-white leading-tight" style="color: white !important;">
                        Jelajahi Proyek KKN
                    </h1>

                    <p class="text-lg md:text-xl leading-relaxed max-w-2xl mx-auto font-medium" style="color: #ffffff !important; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5), 0 4px 12px rgba(0, 0, 0, 0.4);">
                        Temukan proyek KKN yang sesuai dengan minat dan keahlian Anda
                    </p>
                </div>
            </div>
        </div>

        {{-- straight divider --}}
        <div class="absolute bottom-0 left-0 right-0 bg-white" style="height: 4px; margin: 0;"></div>
    </section>

    {{-- main content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- filter toggle button (mobile) --}}
        <div class="mb-4 lg:hidden">
            <button id="toggle-filter" class="w-full bg-white px-4 py-3 rounded-lg shadow-sm border border-gray-200 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <span class="text-gray-900 font-medium">Filter & Pencarian</span>
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>

        {{-- layout grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            {{-- sidebar filter --}}
            <aside class="lg:col-span-1">
                <div id="filter-section" class="hidden lg:block">
                    @include('student.browse-problems.components.filter-sidebar')
                </div>
            </aside>

            {{-- main problems area --}}
            <main class="lg:col-span-3">
                
                {{-- search bar --}}
                <div class="bg-white rounded-lg shadow-sm p-4 mb-6 border border-gray-200">
                    <form action="{{ route('student.browse-problems.index') }}" method="GET" class="flex gap-3">
                        @foreach(request()->except(['search', 'page']) as $key => $value)
                            @if(is_array($value))
                                {{-- jika value adalah array (contoh: sdg_categories), buat multiple hidden inputs --}}
                                @foreach($value as $item)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                {{-- jika value bukan array, buat single hidden input --}}
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        
                        <div class="flex-1">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cari proyek berdasarkan judul, atau deskripsi..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Cari
                        </button>
                    </form>
                </div>

                {{-- view switcher buttons only --}}
                <div class="flex items-center justify-end mb-6">
                    <div class="flex items-center gap-2">
                        <button id="grid-view-btn"
                                onclick="switchView('grid')"
                                class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 active-view bg-blue-50 border-blue-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                        </button>
                        <button id="list-view-btn"
                                onclick="switchView('list')"
                                class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <button id="map-view-btn"
                                onclick="switchView('map')"
                                class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- problems display --}}
                <div id="grid-view" class="view-content">
                    @include('student.browse-problems.components.problems-grid')
                </div>

                <div id="list-view" class="view-content hidden space-y-6">
                    @foreach($problems as $problem)
                        @include('student.browse-problems.components.problem-card-list', ['problem' => $problem])
                    @endforeach
                </div>

                <div id="map-view" class="view-content hidden">
                    @include('student.browse-problems.components.map-view', ['mapProblems' => $allProblems])
                </div>

                {{-- results count & pagination combined --}}
                <div id="problems-pagination" class="flex items-center justify-between mt-8">
                    <p class="text-gray-700 text-sm font-semibold">
                        Menampilkan <span class="font-bold">{{ $problems->firstItem() ?? 0 }}</span>
                        dari <span class="font-bold">{{ $problems->total() }}</span> proyek
                    </p>

                    <div class="pagination-section">
                        {{ $problems->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Leaflet JS - Load BEFORE Alpine.js initializes map --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script src="{{ asset('js/pages/browse-problems.js') }}"></script>
<script>
    // toggle filter section
    document.getElementById('toggle-filter').addEventListener('click', function() {
        const filterSection = document.getElementById('filter-section');
        filterSection.classList.toggle('hidden');
    });

    // view switcher
    function switchView(view) {
        // hide all views
        document.querySelectorAll('.view-content').forEach(el => el.classList.add('hidden'));

        // reset button styles
        document.querySelectorAll('[id$="-view-btn"]').forEach(btn => {
            btn.classList.remove('active-view', 'bg-blue-50', 'border-blue-500');
        });

        // show active view
        document.getElementById(view + '-view').classList.remove('hidden');
        const activeBtn = document.getElementById(view + '-view-btn');
        activeBtn.classList.add('active-view', 'bg-blue-50', 'border-blue-500');

        // show/hide pagination (hidden untuk map view, visible untuk grid & list)
        const paginationSection = document.getElementById('problems-pagination');
        if (view === 'map') {
            paginationSection.classList.add('hidden');
        } else {
            paginationSection.classList.remove('hidden');
        }

        // save view mode ke localStorage
        localStorage.setItem('browseProblemsView', view);

        // update semua pagination links untuk include view parameter
        updatePaginationLinks(view);

        // trigger event untuk map view
        if (view === 'map') {
            window.dispatchEvent(new CustomEvent('mapViewActivated'));
        }
    }

    // update pagination links dengan view parameter
    function updatePaginationLinks(view) {
        // skip untuk map view (tidak ada pagination)
        if (view === 'map') return;

        const paginationSection = document.getElementById('problems-pagination');
        if (!paginationSection) return;

        const links = paginationSection.querySelectorAll('a');
        links.forEach(link => {
            const url = new URL(link.href);
            url.searchParams.set('view', view);
            link.href = url.toString();
        });
    }

    // restore view mode saat page load
    document.addEventListener('DOMContentLoaded', function() {
        // check URL parameter dulu
        const urlParams = new URLSearchParams(window.location.search);
        const urlView = urlParams.get('view');

        // kalau ada view di URL, gunakan itu
        // kalau tidak, gunakan dari localStorage
        // default: grid
        const savedView = urlView || localStorage.getItem('browseProblemsView') || 'grid';

        switchView(savedView);
    });

    async function toggleWishlist(problemId, button) {
        button.disabled = true;
        const originalHTML = button.innerHTML;
        button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        
        try {
            const response = await fetch(`/student/wishlist/${problemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                button.innerHTML = originalHTML;
                button.setAttribute('data-wishlisted', data.saved ? 'true' : 'false');
                
                const svg = button.querySelector('svg');
                if (svg) {
                    if (data.saved) {
                        svg.setAttribute('fill', 'currentColor');
                        svg.classList.add('fill-red-500', 'text-red-500');
                        svg.classList.remove('text-gray-600');
                    } else {
                        svg.setAttribute('fill', 'none');
                        svg.classList.remove('fill-red-500', 'text-red-500');
                        svg.classList.add('text-gray-600');
                    }
                }
                
                button.style.transform = 'scale(1.2)';
                setTimeout(() => { button.style.transform = 'scale(1)'; }, 200);
                
                // notifikasi
                const notif = document.createElement('div');
                notif.className = 'fixed top-20 right-4 bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded shadow-lg z-50';
                notif.innerHTML = `<div class="flex items-center"><svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg><span>${data.message || (data.saved ? 'Ditambahkan ke wishlist' : 'Dihapus dari wishlist')}</span></div>`;
                document.body.appendChild(notif);
                setTimeout(() => { notif.style.opacity='0'; notif.style.transition='all 0.3s'; setTimeout(() => notif.remove(), 300); }, 3000);
            }
        } catch (error) {
            console.error('Error:', error);
            button.innerHTML = originalHTML;
            alert('Terjadi kesalahan. Silakan coba lagi.');
        } finally {
            button.disabled = false;
        }
    }
</script>
@endpush