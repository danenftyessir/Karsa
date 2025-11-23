@extends('layouts.app')

@section('title', 'Platform Digital untuk Kuliah Kerja Nyata Berkelanjutan')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Lora:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .hero-background {
        background-image:
            radial-gradient(at 27% 37%, rgba(255, 107, 107, 0.35) 0px, transparent 50%),
            radial-gradient(at 97% 21%, rgba(147, 51, 234, 0.4) 0px, transparent 50%),
            radial-gradient(at 52% 99%, rgba(59, 130, 246, 0.4) 0px, transparent 50%),
            radial-gradient(at 10% 29%, rgba(236, 72, 153, 0.35) 0px, transparent 50%),
            radial-gradient(at 97% 96%, rgba(168, 85, 247, 0.4) 0px, transparent 50%),
            radial-gradient(at 33% 50%, rgba(99, 102, 241, 0.35) 0px, transparent 50%),
            radial-gradient(at 79% 53%, rgba(14, 165, 233, 0.4) 0px, transparent 50%),
            url('/hero-background.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }

    .hero-text {
        font-family: 'Merriweather', Georgia, serif;
    }

    .hero-description {
        font-family: 'Lora', 'Times New Roman', serif;
    }

    #map {
        height: 500px;
        width: 100%;
        border-radius: 1rem;
        z-index: 1;
    }

    .gradient-mesh {
        background-color: #ffffff;
        background-image:
            radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
            radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.12) 0px, transparent 50%),
            radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
            radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.12) 0px, transparent 50%),
            radial-gradient(at 50% 50%, rgba(147, 51, 234, 0.1) 0px, transparent 50%);
    }

    .zigzag-shape {
        clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
    }

    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .quote-mark {
        font-size: 4rem;
        line-height: 0;
        opacity: 0.2;
    }
</style>
@endpush

@section('content')

<!-- hero section -->
<section class="relative hero-background text-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10 py-24 lg:py-32">
        <div class="text-center mx-auto" data-aos="fade-up">
            <h1 class="hero-text text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                Platform Digital untuk
                <span class="text-yellow-300 block mt-2">Kuliah Kerja Nyata</span>
                <span class="block mt-2">Berkelanjutan</span>
            </h1>

            <p class="hero-description text-xl text-gray-50 leading-relaxed max-w-3xl mx-auto mb-10">
                Menghubungkan mahasiswa dengan instansi pemerintah untuk menciptakan solusi berkelanjutan di seluruh Indonesia
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center" data-aos="fade-up" data-aos-delay="200">
                <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 bg-yellow-400 text-gray-900 font-semibold rounded-full hover:bg-yellow-300 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                    <span>Login Sekarang</span>
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-white bg-opacity-20 backdrop-blur-sm text-black font-semibold rounded-full hover:bg-opacity-30 transition-all duration-300 border-2 border-white hover:scale-105">
                    <span>Daftar Sekarang</span>
                </a>
            </div>
        </div>
    </div>

    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
            <path d="M0,0 L0,80 L720,120 L1440,80 L1440,0 L1440,120 L0,120 Z" fill="white"/>
        </svg>
    </div>
</section>

<!-- split screen section - Mahasiswa -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-12 items-center mb-24">
            <!-- Left side - Text Content -->
            <div data-aos="fade-right">
                <div class="mb-4">
                    <span class="inline-block px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold">
                        Untuk Mahasiswa
                    </span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                    Wujudkan Dampak Nyata Lewat KKN
                </h2>
                <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                    Bergabunglah dengan ribuan mahasiswa yang telah berkontribusi dalam proyek-proyek pembangunan berkelanjutan di seluruh Indonesia. Temukan proyek yang sesuai passion-mu dan bangun portofolio yang bermakna.
                </p>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Akses ke ratusan proyek KKN di berbagai bidang</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Networking dengan instansi pemerintah dan LSM</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Sertifikat terverifikasi untuk setiap proyek</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-green-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Bangun portofolio profesional yang tervalidasi</span>
                    </li>
                </ul>
            </div>

            <!-- Right side - SVG Illustration -->
            <div data-aos="fade-left" class="relative">
                <svg viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                    <!-- Background elements -->
                    <rect x="50" y="50" width="400" height="300" rx="20" fill="#F3F4F6"/>

                    <!-- Dashboard window -->
                    <rect x="70" y="70" width="360" height="260" rx="15" fill="white" stroke="#E5E7EB" stroke-width="2"/>

                    <!-- Header bar -->
                    <rect x="70" y="70" width="360" height="40" rx="15" fill="#6366F1"/>
                    <circle cx="90" cy="90" r="5" fill="white" opacity="0.7"/>
                    <circle cx="105" cy="90" r="5" fill="white" opacity="0.7"/>
                    <circle cx="120" cy="90" r="5" fill="white" opacity="0.7"/>

                    <!-- Student avatar -->
                    <circle cx="150" cy="170" r="30" fill="#DBEAFE"/>
                    <circle cx="150" cy="165" r="12" fill="#3B82F6"/>
                    <path d="M 130 190 Q 150 180 170 190" fill="#3B82F6"/>

                    <!-- Info bars -->
                    <rect x="210" y="150" width="180" height="15" rx="7" fill="#DDD6FE"/>
                    <rect x="210" y="175" width="140" height="12" rx="6" fill="#E0E7FF"/>
                    <rect x="210" y="195" width="160" height="12" rx="6" fill="#FEF3C7"/>

                    <!-- Projects grid -->
                    <rect x="90" y="230" width="90" height="70" rx="8" fill="#DBEAFE" stroke="#3B82F6" stroke-width="2"/>
                    <rect x="200" y="230" width="90" height="70" rx="8" fill="#D1FAE5" stroke="#10B981" stroke-width="2"/>
                    <rect x="310" y="230" width="90" height="70" rx="8" fill="#FEE2E2" stroke="#EF4444" stroke-width="2"/>

                    <!-- Icons in project cards -->
                    <circle cx="135" cy="255" r="8" fill="#3B82F6"/>
                    <circle cx="245" cy="255" r="8" fill="#10B981"/>
                    <circle cx="355" cy="255" r="8" fill="#EF4444"/>

                    <!-- Achievement badge -->
                    <circle cx="400" cy="120" r="35" fill="#FCD34D" stroke="white" stroke-width="3"/>
                    <path d="M 400 105 l 5 10 l 11 2 l -8 8 l 2 11 l -10 -5 l -10 5 l 2 -11 l -8 -8 l 11 -2 z" fill="white"/>

                    <!-- Decorative elements -->
                    <circle cx="450" cy="280" r="20" fill="#DDD6FE" opacity="0.5"/>
                    <circle cx="60" cy="300" r="15" fill="#FECACA" opacity="0.5"/>
                </svg>
            </div>
        </div>

        <!-- Split screen - Instansi -->
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left side - SVG Illustration -->
            <div data-aos="fade-right" class="relative order-2 lg:order-1">
                <svg viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
                    <!-- Background -->
                    <rect x="50" y="30" width="400" height="340" rx="20" fill="#F9FAFB"/>

                    <!-- Building/Institution -->
                    <rect x="150" y="100" width="200" height="220" rx="10" fill="white" stroke="#E5E7EB" stroke-width="3"/>

                    <!-- Roof -->
                    <path d="M 140 100 L 250 50 L 360 100" fill="#6366F1" stroke="#4F46E5" stroke-width="2"/>
                    <circle cx="250" cy="60" r="8" fill="#FCD34D"/>

                    <!-- Windows -->
                    <rect x="170" y="130" width="40" height="40" rx="5" fill="#DBEAFE"/>
                    <rect x="230" y="130" width="40" height="40" rx="5" fill="#DBEAFE"/>
                    <rect x="290" y="130" width="40" height="40" rx="5" fill="#DBEAFE"/>

                    <rect x="170" y="190" width="40" height="40" rx="5" fill="#D1FAE5"/>
                    <rect x="230" y="190" width="40" height="40" rx="5" fill="#D1FAE5"/>
                    <rect x="290" y="190" width="40" height="40" rx="5" fill="#D1FAE5"/>

                    <!-- Door -->
                    <rect x="220" y="260" width="60" height="60" rx="8" fill="#6366F1"/>
                    <circle cx="265" cy="290" r="3" fill="#FCD34D"/>

                    <!-- People/Students around -->
                    <circle cx="100" cy="280" r="15" fill="#BFDBFE"/>
                    <rect x="92" y="295" width="16" height="25" rx="3" fill="#3B82F6"/>

                    <circle cx="380" cy="270" r="15" fill="#FED7AA"/>
                    <rect x="372" y="285" width="16" height="25" rx="3" fill="#F97316"/>

                    <circle cx="120" cy="340" r="15" fill="#C7D2FE"/>
                    <rect x="112" y="355" width="16" height="15" rx="3" fill="#6366F1"/>

                    <circle cx="370" cy="330" r="15" fill="#FEE2E2"/>
                    <rect x="362" y="345" width="16" height="25" rx="3" fill="#EF4444"/>

                    <!-- Document/Project icon -->
                    <rect x="60" y="60" width="60" height="75" rx="5" fill="white" stroke="#6366F1" stroke-width="2"/>
                    <line x1="75" y1="80" x2="105" y2="80" stroke="#6366F1" stroke-width="2"/>
                    <line x1="75" y1="95" x2="105" y2="95" stroke="#9CA3AF" stroke-width="2"/>
                    <line x1="75" y1="105" x2="100" y2="105" stroke="#9CA3AF" stroke-width="2"/>
                    <circle cx="90" cy="120" r="8" fill="#10B981"/>
                    <path d="M 86 120 l 3 3 l 6 -6" stroke="white" stroke-width="2" fill="none"/>

                    <!-- Network connection lines -->
                    <line x1="115" y1="95" x2="150" y2="150" stroke="#818CF8" stroke-width="2" stroke-dasharray="5,5" opacity="0.5"/>
                    <line x1="380" y1="100" x2="350" y2="150" stroke="#818CF8" stroke-width="2" stroke-dasharray="5,5" opacity="0.5"/>
                </svg>
            </div>

            <!-- Right side - Text Content -->
            <div data-aos="fade-left" class="order-1 lg:order-2">
                <div class="mb-4">
                    <span class="inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                        Untuk Instansi
                    </span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                    Rekrut Talenta Terbaik untuk Proyek Anda
                </h2>
                <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                    Dapatkan akses ke ribuan mahasiswa berbakat dari berbagai universitas di Indonesia. Tingkatkan dampak program pembangunan daerah Anda dengan kolaborasi yang terstruktur dan terukur.
                </p>
                <ul class="space-y-4 mb-8">
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Posting proyek KKN secara gratis dan mudah</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Akses talenta mahasiswa dari seluruh Indonesia</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Sistem manajemen proyek yang terintegrasi</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-6 h-6 text-blue-500 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-700">Laporan dan dokumentasi hasil KKN yang terstruktur</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- platform info & map section -->
<section id="tentang" class="py-20 gradient-mesh">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12">
        <!-- Platform Info -->
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Kenapa Memilih Karsa?
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Platform terpadu yang memudahkan kolaborasi antara mahasiswa dan instansi untuk program KKN yang berdampak
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 mb-32">
            <div class="card-hover p-8 text-center group" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 bg-gradient-to-br from-primary-100 to-primary-200 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-4">Temukan Proyek</h3>
                <p class="text-gray-600 leading-relaxed">
                    Cari dan temukan proyek KKN yang sesuai dengan minat, keahlian, dan lokasi yang anda inginkan
                </p>
            </div>

            <div class="card-hover p-8 text-center group" data-aos="fade-up" data-aos-delay="200">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-4">Kolaborasi Mudah</h3>
                <p class="text-gray-600 leading-relaxed">
                    Platform terintegrasi untuk komunikasi, manajemen proyek, dan pelaporan hasil KKN
                </p>
            </div>

            <div class="card-hover p-8 text-center group" data-aos="fade-up" data-aos-delay="300">
                <div class="w-20 h-20 bg-gradient-to-br from-green-100 to-green-200 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 mb-4">Portofolio Tervalidasi</h3>
                <p class="text-gray-600 leading-relaxed">
                    Bangun portofolio profesional dengan hasil KKN yang terverifikasi oleh instansi mitra
                </p>
            </div>
        </div>

        <!-- Map Section -->
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Jangkauan di Seluruh Indonesia
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Temukan peluang KKN dari Sabang sampai Merauke
            </p>
        </div>

        <div data-aos="zoom-in" data-aos-delay="200">
            <div id="map" class="shadow-2xl"></div>
        </div>

        <div class="grid md:grid-cols-4 gap-6 mt-12" data-aos="fade-up" data-aos-delay="400">
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 text-center">
                <div class="text-3xl font-bold text-primary-600 mb-2">34</div>
                <div class="text-gray-600">Provinsi</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2">514</div>
                <div class="text-gray-600">Kabupaten/Kota</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">{{ $stats['total_institutions'] }}+</div>
                <div class="text-gray-600">Instansi Mitra</div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-100 text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $stats['completed_projects'] }}+</div>
                <div class="text-gray-600">Proyek Selesai</div>
            </div>
        </div>
    </div>
</section>

<!-- quote section with divider -->
<section class="relative bg-white overflow-hidden">
    <!-- Top tilt divider -->
    <div class="w-full overflow-hidden leading-none">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto" preserveAspectRatio="none">
            <path d="M0,60 L1440,0 L1440,120 L0,120 Z" fill="#ffffff"/>
        </svg>
    </div>

    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-20">
        <!-- Quote Section -->
        <div data-aos="fade-up">
            <div class="max-w-5xl mx-auto text-center">
                <div class="relative inline-block">
                    <span class="quote-mark text-yellow-500 absolute -top-8 -left-8">"</span>
                    <blockquote class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-8 leading-tight">
                        Tetaplah bergerak dan berdampak walau Anda berbeda, karena Tuhan tidak menciptakan dunia untuk satu jenis makhluknya saja.
                    </blockquote>
                </div>
                <p class="text-lg text-gray-500 italic mt-6">Karsa - Karya Untuk Bangsa</p>
            </div>
        </div>
    </div>

    <!-- Bottom tilt divider -->
    <div class="w-full overflow-hidden leading-none">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto" preserveAspectRatio="none">
            <path d="M0,0 L1440,60 L1440,120 L0,120 Z" fill="#ffffff"/>
        </svg>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>

// initialize AOS
AOS.init({
    duration: 600,
    easing: 'ease-out',
    once: true,
    offset: 50,
});

// initialize map
const map = L.map('map', {
    center: [-2.5489, 118.0149],
    zoom: 5,
    zoomControl: true,
    scrollWheelZoom: true
});

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 18,
}).addTo(map);

// Data proyek yang diperbanyak
const projects = [
    { lat: -6.2088, lng: 106.8456, name: 'Jakarta - Proyek Smart Village', institution: 'Dinas Komunikasi DKI' },
    { lat: -7.7956, lng: 110.3695, name: 'Yogyakarta - Pemberdayaan UMKM', institution: 'Pemda Sleman' },
    { lat: -6.9175, lng: 107.6191, name: 'Bandung - Digitalisasi Desa', institution: 'Pemkot Bandung' },
    { lat: -7.2575, lng: 112.7521, name: 'Surabaya - Pengelolaan Sampah', institution: 'Dinas LH Surabaya' },
    { lat: 3.5952, lng: 98.6722, name: 'Medan - Pertanian Organik', institution: 'Dinas Pertanian Sumut' },
    { lat: -5.1477, lng: 119.4327, name: 'Makassar - Edukasi Maritim', institution: 'Dinas Kelautan dan Perikanan Sulsel' },
    { lat: -8.6705, lng: 115.2126, name: 'Denpasar - Pengembangan Pariwisata Desa', institution: 'Dinas Pariwisata Bali' },
    { lat: 0.5330, lng: 101.4474, name: 'Pekanbaru - Konservasi Hutan Gambut', institution: 'LSM Lingkungan Riau' },
    { lat: -0.9436, lng: 100.3631, name: 'Padang - Mitigasi Bencana Tsunami', institution: 'BPBD Sumatera Barat' },
    { lat: -3.3190, lng: 114.5908, name: 'Banjarmasin - Restorasi Lahan Basah', institution: 'Yayasan Peduli Lahan Basah' }
];

const customIcon = L.divIcon({
    className: 'custom-marker',
    html: '<div style="background: #2563eb; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: 3px solid white;"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></div>',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32]
});

// Loop untuk menambahkan marker dan popup yang lebih informatif
projects.forEach(project => {
    L.marker([project.lat, project.lng], { icon: customIcon })
        .addTo(map)
        .bindPopup(`
            <div style="min-width: 220px; padding: 5px;">
                <h3 style="font-weight: bold; margin-bottom: 8px; color: #1e40af; font-size: 1.1em;">${project.name}</h3>
                <p style="color: #6b7280; margin-bottom: 12px; font-size: 0.9em;">
                    <span style="font-weight: 500;">Oleh:</span> ${project.institution}
                </p>
            </div>
        `);
});

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = 'fixed top-20 right-6 px-6 py-4 rounded-lg shadow-2xl text-white transform transition-all duration-300 z-[9999] flex items-center space-x-3';

    const colors = { success: 'bg-green-600', error: 'bg-red-600', info: 'bg-blue-600' };
    const icons = {
        success: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        error: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        info: '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
    };

    toast.classList.add(colors[type] || colors.info);
    toast.innerHTML = `
        <div class="flex-shrink-0">${icons[type]}</div>
        <p class="font-semibold">${message}</p>
        <button onclick="this.parentElement.remove()" class="ml-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;

    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100px)';
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100px)';
        setTimeout(() => toast.remove(), 300);
    }, 6000);
}

// Tampilkan toast dari flash message
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        setTimeout(() => showToast("{{ session('success') }}", 'success'), 300);
    @endif

    @if(session('error'))
        setTimeout(() => showToast("{{ session('error') }}", 'error'), 300);
    @endif

    @if(session('info'))
        setTimeout(() => showToast("{{ session('info') }}", 'info'), 300);
    @endif
});
</script>
@endpush