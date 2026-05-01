@extends('layouts.app')

@section('title', 'Tentang Kami - Karsa')

@section('content')
<div class="min-h-screen bg-white m-0 p-0">

    {{-- carousel container --}}
    <div class="carousel-container relative overflow-hidden m-0 p-0">

        {{-- carousel slides wrapper --}}
        <div class="carousel-slides flex transition-transform duration-700 ease-in-out" id="carouselSlides">

            {{-- slide 1: hero section --}}
            <div class="carousel-slide min-w-full flex-shrink-0">
                <section class="relative h-screen min-h-[600px] overflow-hidden">
                    {{-- background image --}}
                    <div class="absolute inset-0">
                        <img src="{{ asset('mahasiswa-about.jpeg') }}"
                             alt="About Us Karsa"
                             class="w-full h-full object-cover">
                        {{-- overlay gradient - lebih gelap di bawah --}}
                        <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/40 to-black/70"></div>
                    </div>

                    {{-- content - text di kiri bawah --}}
                    <div class="relative h-full">
                        <div class="container mx-auto px-24 md:px-32 h-full flex items-end pb-20">
                            <div class="max-w-4xl">
                                <h1 class="text-6xl md:text-7xl lg:text-8xl font-black leading-tight tracking-tight drop-shadow-2xl" style="font-family: 'Montserrat', 'Poppins', 'Outfit', sans-serif; font-weight: 900; color: #FFFFFF; text-shadow: 0 4px 20px rgba(0, 0, 0, 0.8);">
                                    About Us
                                </h1>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- slide 2: perkenalan (intro only) --}}
            <div class="carousel-slide min-w-full flex-shrink-0">
                <section class="h-screen min-h-[600px] flex items-center justify-center" style="background-color: #ffffff; background-image: radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.15) 0px, transparent 50%), radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.12) 0px, transparent 50%), radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.15) 0px, transparent 50%), radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.12) 0px, transparent 50%), radial-gradient(at 50% 50%, rgba(147, 51, 234, 0.1) 0px, transparent 50%);">
                    <div class="container mx-auto px-24 md:px-32 py-12">
                        <div class="max-w-5xl mx-auto text-center">
                            {{-- heading --}}
                            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-8 leading-tight">
                                Perkenalkan, <span class="text-blue-600">Karsa</span>
                            </h2>

                            {{-- intro paragraph --}}
                            <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12 border border-gray-100">
                                <p class="text-lg md:text-xl lg:text-2xl text-gray-700 leading-relaxed text-center">
                                    Indonesia memiliki potensi intelektual yang luar biasa dengan <span class="font-bold text-blue-600">8,3 juta mahasiswa aktif</span> di 4.500 perguruan tinggi, dimana sekitar <span class="font-bold text-blue-600">520.000 mahasiswa melaksanakan KKN</span> setiap tahun menghasilkan lebih dari 100.000 laporan penelitian.
                                </p>

                                <div class="my-8 flex items-center justify-center">
                                    <div class="h-1 w-24 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full"></div>
                                </div>

                                <p class="text-lg md:text-xl lg:text-2xl text-gray-700 leading-relaxed text-center">
                                    Namun, data menunjukkan bahwa <span class="font-bold text-red-600">76% hasil penelitian mahasiswa hanya berakhir sebagai dokumen arsip</span> tanpa implementasi nyata, menciptakan pemborosan sumber daya senilai <span class="font-bold text-red-600">±Rp 1,2 triliun per tahun</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- slide 3: tiga masalah krusial --}}
            <div class="carousel-slide min-w-full flex-shrink-0">
                <section class="h-screen min-h-[600px] flex items-center py-8" style="background-color: #ffffff; background-image: radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.15) 0px, transparent 50%), radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.12) 0px, transparent 50%), radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.15) 0px, transparent 50%), radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.12) 0px, transparent 50%), radial-gradient(at 50% 50%, rgba(147, 51, 234, 0.1) 0px, transparent 50%);">
                    <div class="container mx-auto px-24 md:px-32">
                        {{-- header --}}
                        <div class="text-center mb-8">
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">
                                Tiga Masalah Krusial Yang Kami Selesaikan
                            </h2>
                            <p class="text-base md:text-lg text-gray-600">
                                Karsa hadir untuk mentransformasi ekosistem KKN Indonesia
                            </p>
                        </div>

                        {{-- 3 cards --}}
                        <div class="max-w-7xl mx-auto">
                            <div class="grid md:grid-cols-3 gap-6">
                                {{-- solusi 1 --}}
                                <div class="bg-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-shadow border-t-4 border-red-500">
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-3">Marketplace Masalah</h3>
                                    <p class="text-sm text-gray-600 leading-relaxed mb-3">
                                        Platform yang menghubungkan mahasiswa dengan masalah nyata dari pemerintah daerah, meningkatkan relevansi program hingga <span class="font-bold text-blue-600">75%</span>.
                                    </p>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600 italic">
                                            "Mahasiswa dapat langsung fokus pada penyelesaian masalah tanpa menghabiskan waktu untuk identifikasi."
                                        </p>
                                    </div>
                                </div>

                                {{-- solusi 2 --}}
                                <div class="bg-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-shadow border-t-4 border-yellow-500">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-3">Impact Portfolio</h3>
                                    <p class="text-sm text-gray-600 leading-relaxed mb-3">
                                        Sistem validasi resmi dari pemerintah daerah yang menciptakan portofolio profesional terverifikasi untuk mahasiswa.
                                    </p>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600 italic">
                                            "94% mahasiswa menyatakan portofolio tervalidasi sangat berharga dalam proses rekrutmen."
                                        </p>
                                    </div>
                                </div>

                                {{-- solusi 3 --}}
                                <div class="bg-white rounded-lg p-6 shadow-lg hover:shadow-xl transition-shadow border-t-4 border-blue-500">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-3">Knowledge Repository</h3>
                                    <p class="text-sm text-gray-600 leading-relaxed mb-3">
                                        Perpustakaan digital nasional yang mengubah hasil KKN menjadi sumber pembelajaran kolektif yang dapat diakses seluruh masyarakat.
                                    </p>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <p class="text-xs text-gray-600 italic">
                                            "Potensi penghematan hingga Rp 540 miliar per tahun dengan mencegah duplikasi program."
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- slide 4: Split Screen - Dari Tahun Ke Tahun (LEFT) + Dari Mahasiswa (RIGHT) --}}
            <div class="carousel-slide min-w-full flex-shrink-0">
                <section class="h-screen min-h-[600px] flex">
                    <div class="w-1/2 relative overflow-hidden flex items-center justify-center p-12">
                        {{-- background image --}}
                        <div class="absolute inset-0">
                            <img src="{{ asset('about.jpg') }}"
                                 alt="Karsa Background"
                                 class="w-full h-full object-cover">
                            {{-- mesh gradient overlay biru-ungu --}}
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/90 via-indigo-700/85 to-purple-800/90"></div>
                        </div>
                        <div class="max-w-xl text-white relative z-10">
                            <div class="mb-6">
                                <div class="w-16 h-1 bg-white rounded-full mb-6"></div>
                                <h2 class="text-3xl md:text-4xl font-bold leading-tight mb-6 text-white">
                                    Karsa Dari Tahun Ke Tahun
                                </h2>
                            </div>
                            <p class="text-lg md:text-xl leading-relaxed mb-8 text-blue-100">
                                Karsa berdiri sebagai katalisator dalam menciptakan ekosistem Kuliah Kerja Nyata 4.0 di Indonesia. Kami adalah laboratorium inovasi sekaligus wadah sinergi antara mahasiswa dan pemerintah daerah.
                            </p>
                            <div class="grid grid-cols-3 gap-6 mt-8">
                                <div class="text-center">
                                    <div class="text-4xl font-bold mb-2">520K+</div>
                                    <div class="text-sm text-blue-200">Mahasiswa KKN</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-4xl font-bold mb-2">83K+</div>
                                    <div class="text-sm text-blue-200">Desa Target</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-4xl font-bold mb-2">100K+</div>
                                    <div class="text-sm text-blue-200">Repository</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT SIDE: Dari Mahasiswa --}}
                    <div class="w-1/2 flex items-center justify-center p-8" style="background-color: #ffffff; background-image: radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.15) 0px, transparent 50%), radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.12) 0px, transparent 50%), radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.15) 0px, transparent 50%), radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.12) 0px, transparent 50%), radial-gradient(at 50% 50%, rgba(147, 51, 234, 0.1) 0px, transparent 50%);">
                        <div class="max-w-xl">
                            <div class="flex items-center justify-center mb-6">
                                <div class="relative">
                                    <div class="w-28 h-28 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-2xl">
                                        <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center">
                                <div class="w-16 h-1 bg-blue-600 rounded-full mb-4 mx-auto"></div>
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-5 leading-tight">
                                    Dari Mahasiswa, Untuk Indonesia
                                </h2>
                                <p class="text-base md:text-lg text-gray-700 leading-relaxed mb-4">
                                    Menyongsong usia ke-2 tahun, Karsa terus tumbuh dan bertransformasi. Dari pelabuhan kecil di masa lalu hingga platform digital masa kini, Karsa telah menghadapi beragam tantangan dan membuka banyak peluang.
                                </p>
                                <p class="text-base md:text-lg text-gray-700 leading-relaxed">
                                    Saatnya kita melangkah bersama untuk membentuk Karsa sebagai <span class="font-bold text-blue-600 italic">revolusi mahasiswa</span> yang lebih inklusif dan siap bersaing di pentas nasional.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- slide 5: Split Screen - Image (LEFT) + Platform Terpadu (RIGHT) --}}
            <div class="carousel-slide min-w-full flex-shrink-0">
                <section class="h-screen min-h-[600px] flex">
                    {{-- LEFT SIDE: Image --}}
                    <div class="w-1/2 relative overflow-hidden">
                        <img src="{{ asset('handprints-about.jpeg') }}"
                             alt="Aktivitas Mahasiswa KKN"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent to-black/20"></div>
                    </div>

                    {{-- RIGHT SIDE: Platform Terpadu --}}
                    <div class="w-1/2 flex items-center justify-center p-8" style="background-color: #ffffff; background-image: radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.15) 0px, transparent 50%), radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.12) 0px, transparent 50%), radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.15) 0px, transparent 50%), radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.12) 0px, transparent 50%), radial-gradient(at 50% 50%, rgba(147, 51, 234, 0.1) 0px, transparent 50%);">
                        <div class="max-w-xl">
                            <div class="w-14 h-1 bg-blue-600 rounded-full mb-4"></div>
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 leading-tight">
                                Platform Terpadu Untuk Mahasiswa Dan Instansi
                            </h2>
                            <p class="text-sm md:text-base text-gray-600 mb-6 leading-relaxed">
                                Selalu up-to-date dengan informasi dan data program KKN yang terintegrasi, aktual, serta transparan dari seluruh perguruan tinggi dan pemerintah daerah.
                            </p>

                            {{-- icon list --}}
                            <div class="space-y-4">
                                {{-- item 1 --}}
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-lg bg-blue-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Real-Time Updates</h4>
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            Pembaruan data setiap hari oleh mahasiswa dan instansi mitra
                                        </p>
                                    </div>
                                </div>

                                {{-- item 2 --}}
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-lg bg-blue-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Managed by Experts</h4>
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            Dikelola oleh tim profesional dari perguruan tinggi terkemuka
                                        </p>
                                    </div>
                                </div>

                                {{-- item 3 --}}
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-lg bg-blue-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Collaborative Synergy</h4>
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            Hasil sinergi perguruan tinggi dan pemerintah daerah untuk pembangunan berkelanjutan
                                        </p>
                                    </div>
                                </div>

                                {{-- item 4 --}}
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-lg bg-blue-600 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-1">Wide Reach</h4>
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            Dikunjungi oleh lebih dari 520,000+ mahasiswa setiap tahun
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- slide 6: Split Screen - SDGs (LEFT) + Image (RIGHT) --}}
            <div class="carousel-slide min-w-full flex-shrink-0">
                <section class="h-screen min-h-[600px] flex">
                    {{-- LEFT SIDE: SDGs Content --}}
                    <div class="w-1/2 flex items-center justify-center p-8" style="background-color: #ffffff; background-image: radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.15) 0px, transparent 50%), radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.12) 0px, transparent 50%), radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.15) 0px, transparent 50%), radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.12) 0px, transparent 50%), radial-gradient(at 50% 50%, rgba(147, 51, 234, 0.1) 0px, transparent 50%);">
                        <div class="max-w-xl">
                            <div class="w-12 h-1 bg-blue-600 rounded-full mb-4"></div>
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 leading-tight">
                                Mendukung Pencapaian Sustainable Development Goals
                            </h2>
                            <p class="text-sm md:text-base text-gray-600 mb-6 leading-relaxed">
                                Karsa secara langsung berkontribusi pada pencapaian beberapa target SDGs melalui kolaborasi mahasiswa dan pemerintah daerah.
                            </p>

                            {{-- sdgs list --}}
                            <div class="space-y-3">
                                {{-- sdg 4 --}}
                                <div class="bg-white p-3 rounded-lg shadow-md hover:shadow-lg transition-all border-l-4 border-red-500">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-lg bg-red-500 flex items-center justify-center text-white font-bold text-base">
                                                4
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-900 mb-1">Pendidikan Berkualitas</h3>
                                            <p class="text-xs text-gray-600 leading-relaxed">
                                                Menciptakan pengalaman belajar bermakna yang menghubungkan teori dengan praktik
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- sdg 11 --}}
                                <div class="bg-white p-3 rounded-lg shadow-md hover:shadow-lg transition-all border-l-4 border-orange-500">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-lg bg-orange-500 flex items-center justify-center text-white font-bold text-base">
                                                11
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-900 mb-1">Kota Dan Komunitas Berkelanjutan</h3>
                                            <p class="text-xs text-gray-600 leading-relaxed">
                                                Mendukung pengembangan daerah berbasis data dan riset
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- sdg 16 --}}
                                <div class="bg-white p-3 rounded-lg shadow-md hover:shadow-lg transition-all border-l-4 border-blue-700">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-lg bg-blue-700 flex items-center justify-center text-white font-bold text-base">
                                                16
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-900 mb-1">Institusi Yang Kuat</h3>
                                            <p class="text-xs text-gray-600 leading-relaxed">
                                                Memperkuat kapasitas pemerintahan lokal dalam pengambilan keputusan
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- sdg 17 --}}
                                <div class="bg-white p-3 rounded-lg shadow-md hover:shadow-lg transition-all border-l-4 border-blue-900">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-lg bg-blue-900 flex items-center justify-center text-white font-bold text-base">
                                                17
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-bold text-gray-900 mb-1">Kemitraan</h3>
                                            <p class="text-xs text-gray-600 leading-relaxed">
                                                Menciptakan jembatan kolaborasi antara akademisi, pemerintah, dan masyarakat
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT SIDE: Image --}}
                    <div class="w-1/2 relative overflow-hidden">
                        <img src="{{ asset('jaket-about.jpeg') }}"
                             alt="SDGs Karsa"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-l from-transparent to-black/10"></div>
                    </div>
                </section>
            </div>

            {{-- slide 7: How It Works (tanpa CTA) --}}
            <div class="carousel-slide min-w-full flex-shrink-0">
                <section class="h-screen min-h-[600px] flex items-center py-6" style="background-color: #ffffff; background-image: radial-gradient(at 15% 15%, rgba(99, 102, 241, 0.15) 0px, transparent 50%), radial-gradient(at 85% 20%, rgba(236, 72, 153, 0.12) 0px, transparent 50%), radial-gradient(at 25% 75%, rgba(59, 130, 246, 0.15) 0px, transparent 50%), radial-gradient(at 75% 85%, rgba(168, 85, 247, 0.12) 0px, transparent 50%), radial-gradient(at 50% 50%, rgba(147, 51, 234, 0.1) 0px, transparent 50%);">
                    <div class="container mx-auto px-6 md:px-12">
                        <div class="max-w-7xl mx-auto">

                            {{-- header --}}
                            <h2 class="text-2xl md:text-3xl font-bold text-blue-700 mb-6 text-center">
                                How It Works
                            </h2>

                            {{-- tab switcher --}}
                            <div class="flex justify-center mb-6">
                                <div class="inline-flex rounded-lg bg-gray-200 p-1">
                                    <button onclick="switchTab('mahasiswa')" id="tabMahasiswa" class="tab-button active">
                                        Untuk Mahasiswa
                                    </button>
                                    <button onclick="switchTab('institusi')" id="tabInstitusi" class="tab-button">
                                        Untuk Institusi
                                    </button>
                                </div>
                            </div>

                            {{-- navigation arrows --}}
                            <div class="flex justify-center mb-3">
                                <div class="flex gap-3">
                                    <button onclick="scrollTimelineLeft()" class="arrow-btn">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>
                                    <button onclick="scrollTimelineRight()" class="arrow-btn">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- timeline container mahasiswa --}}
                            <div id="contentMahasiswa" class="tab-content active">
                                <div class="relative pb-6" id="timelineMahasiswa">
                                    <div class="flex min-w-max relative pt-10">

                                        {{-- garis horizontal --}}
                                        <div class="absolute left-0 right-0 bg-blue-600" style="top: 75px; height: 2px;"></div>

                                        {{-- langkah 1 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 1</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Daftar & Lengkapi Profil</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Buat akun Anda sebagai mahasiswa dalam hitungan menit. Lengkapi profil Anda dengan keahlian, riwayat pendidikan, dan portofolio awal untuk menarik perhatian institusi.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- langkah 2 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 2</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Jelajahi Masalah</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Temukan ratusan masalah nyata dari berbagai institusi. Gunakan filter berdasarkan lokasi, kategori keilmuan, atau jenis institusi untuk menemukan tantangan yang paling relevan.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- langkah 3 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 3</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Ajukan Proposal Solusi</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Tulis dan kirimkan proposal solusi Anda yang paling inovatif. Jelaskan ide, metodologi, dan estimasi waktu pengerjaan proyek secara rinci.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- langkah 4 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 4</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Tunggu Peninjauan</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Institusi akan meninjau proposal Anda. Anda akan menerima notifikasi apakah proposal Anda diterima, ditolak, atau membutuhkan revisi.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- langkah 5 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 5</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Kerjakan Proyek & Lapor</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Setelah proposal disetujui, mulailah pengerjaan proyek. Laporkan kemajuan Anda secara berkala melalui dasbor proyek agar institusi dapat memantau perkembangan.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- langkah 6 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 6</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Dapatkan Pengakuan</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Selesaikan proyek dan dapatkan ulasan serta sertifikat digital. Proyek yang berhasil akan otomatis masuk ke portofolio online Anda, memperkuat reputasi profesional Anda.
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{-- timeline container institusi --}}
                            <div id="contentInstitusi" class="tab-content">
                                <div class="relative pb-6" id="timelineInstitusi">
                                    <div class="flex min-w-max relative pt-10">

                                        {{-- garis horizontal --}}
                                        <div class="absolute left-0 right-0 bg-blue-600" style="top: 75px; height: 2px;"></div>

                                        {{-- langkah 1 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 1</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Daftar & Verifikasi</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Daftarkan institusi Anda (pemerintah desa, UKM, NGO, dll.) dan lengkapi profil. Tim kami akan melakukan verifikasi untuk memastikan kredibilitas platform.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- langkah 2 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 2</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Publikasikan Masalah</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Jabarkan masalah, tantangan, atau kebutuhan yang sedang dihadapi institusi Anda. Semakin detail Anda menjelaskannya, semakin relevan solusi yang akan Anda terima.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- langkah 3 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 3</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Tinjau Proposal Masuk</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Anda akan menerima beragam proposal solusi dari mahasiswa di seluruh Indonesia. Bandingkan setiap ide, kreativitas, dan kelayakan proposal yang diajukan.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- langkah 4 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 4</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Pilih Mahasiswa Terbaik</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Pilih mahasiswa atau tim dengan proposal terbaik. Anda dapat berkomunikasi langsung dengan mereka melalui platform untuk diskusi lebih lanjut sebelum membuat keputusan akhir.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- langkah 5 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 5</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Bimbing & Pantau Proyek</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Dampingi mahasiswa selama pengerjaan proyek. Pantau laporan kemajuan mereka melalui dasbor dan berikan masukan agar hasil akhir sesuai dengan ekspektasi.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- langkah 6 --}}
                                        <div class="flex-shrink-0 px-3" style="width: 320px;">
                                            <div class="flex flex-col items-start">
                                                <p class="text-xs text-gray-600 mb-3">Langkah 6</p>
                                                <div class="relative flex items-center justify-start w-full mb-4">
                                                    <div class="w-3 h-3 bg-blue-600 rounded-full relative z-10"></div>
                                                </div>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                                    </svg>
                                                    <h3 class="text-base font-semibold text-gray-900">Beri Ulasan & Terima Hasil</h3>
                                                </div>
                                                <p class="text-xs text-gray-700 leading-relaxed">
                                                    Setelah proyek selesai, terima laporan akhir dan hasil kerja dari mahasiswa. Berikan ulasan yang membangun untuk membantu mereka di karir masa depan.
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </div>

        </div>

        {{-- carousel navigation - prev/next buttons --}}
        <button onclick="prevSlide()" id="prevBtn" class="carousel-nav-btn carousel-nav-left">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button onclick="nextSlide()" id="nextBtn" class="carousel-nav-btn carousel-nav-right">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        {{-- carousel dots indicator --}}
        <div class="carousel-dots">
            <button onclick="goToSlide(0)" class="carousel-dot active" data-slide="0"></button>
            <button onclick="goToSlide(1)" class="carousel-dot" data-slide="1"></button>
            <button onclick="goToSlide(2)" class="carousel-dot" data-slide="2"></button>
            <button onclick="goToSlide(3)" class="carousel-dot" data-slide="3"></button>
            <button onclick="goToSlide(4)" class="carousel-dot" data-slide="4"></button>
            <button onclick="goToSlide(5)" class="carousel-dot" data-slide="5"></button>
            <button onclick="goToSlide(6)" class="carousel-dot" data-slide="6"></button>
        </div>

    </div>

</div>

@push('styles')
<style>
    /* hide footer for about page */
    footer {
        display: none !important;
    }

    /* hide scroll to top button */
    #scroll-to-top {
        display: none !important;
    }

    /* hide body scrollbar only */
    body {
        overflow: hidden !important;
    }

    /* carousel styles */
    .carousel-container {
        position: relative;
        height: 100vh;
        overflow: hidden;
        margin: 0 !important;
        padding: 0 !important;
        width: 100vw;
        margin-left: calc(-50vw + 50%) !important;
    }

    .carousel-slides {
        height: 100%;
        margin: 0 !important;
        padding: 0 !important;
        width: 100%;
    }

    .carousel-slide {
        height: 100%;
        margin: 0 !important;
        padding: 0 !important;
        width: 100vw;
    }

    /* remove any gaps between slides */
    .carousel-slides > * {
        flex-shrink: 0;
    }

    /* allow scrolling for timeline */
    #timelineMahasiswa,
    #timelineInstitusi {
        overflow-x: auto;
        overflow-y: visible;
    }

    /* hide scrollbar styling */
    #timelineMahasiswa::-webkit-scrollbar,
    #timelineInstitusi::-webkit-scrollbar {
        display: none;
    }

    /* navigation buttons */
    .carousel-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 50;
        background: rgba(255, 255, 255, 0.4);
        color: #1f2937;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .carousel-nav-btn:hover {
        background: rgba(255, 255, 255, 0.7);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        transform: translateY(-50%) scale(1.1);
    }

    .carousel-nav-left {
        left: 30px;
    }

    .carousel-nav-right {
        right: 30px;
    }

    /* dots indicator */
    .carousel-dots {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 50;
        display: flex;
        gap: 12px;
        background: rgba(0, 0, 0, 0.5);
        padding: 12px 20px;
        border-radius: 30px;
        backdrop-filter: blur(10px);
    }

    .carousel-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.5);
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .carousel-dot.active {
        background: white;
        width: 40px;
        border-radius: 6px;
    }

    .carousel-dot:hover {
        background: rgba(255, 255, 255, 0.8);
    }

    /* tab switcher */
    .tab-button {
        padding: 12px 32px;
        border-radius: 8px;
        background: transparent;
        color: #6b7280;
        font-weight: 600;
        font-size: 15px;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .tab-button.active {
        background: #2563eb;
        color: white;
    }

    /* tab content */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* arrow buttons */
    .arrow-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid #d1d5db;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .arrow-btn:hover {
        border-color: #2563eb;
        background: #eff6ff;
    }

    /* scrollbar */
    #timelineMahasiswa::-webkit-scrollbar,
    #timelineInstitusi::-webkit-scrollbar {
        height: 6px;
    }

    #timelineMahasiswa::-webkit-scrollbar-track,
    #timelineInstitusi::-webkit-scrollbar-track {
        background: #f3f4f6;
    }

    #timelineMahasiswa::-webkit-scrollbar-thumb,
    #timelineInstitusi::-webkit-scrollbar-thumb {
        background: #2563eb;
        border-radius: 3px;
    }

    /* keyboard navigation hint */
    @media (min-width: 768px) {
        .carousel-container::after {
            content: 'Use ← → arrow keys to navigate';
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            z-index: 40;
            animation: fadeInOut 3s ease-in-out infinite;
        }
    }

    @keyframes fadeInOut {
        0%, 100% { opacity: 0; }
        50% { opacity: 1; }
    }
</style>
@endpush

@push('scripts')
<script>
    let currentSlide = 0;
    const totalSlides = 7;

    function updateCarousel() {
        const slides = document.getElementById('carouselSlides');
        slides.style.transform = `translateX(-${currentSlide * 100}%)`;

        // update dots
        document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });

        // update button visibility
        document.getElementById('prevBtn').style.display = currentSlide === 0 ? 'none' : 'flex';
        document.getElementById('nextBtn').style.display = currentSlide === totalSlides - 1 ? 'none' : 'flex';
    }

    function nextSlide() {
        if (currentSlide < totalSlides - 1) {
            currentSlide++;
            updateCarousel();
        }
    }

    function prevSlide() {
        if (currentSlide > 0) {
            currentSlide--;
            updateCarousel();
        }
    }

    function goToSlide(index) {
        currentSlide = index;
        updateCarousel();
    }

    // keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });

    // touch/swipe support
    let touchStartX = 0;
    let touchEndX = 0;

    document.querySelector('.carousel-container').addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    document.querySelector('.carousel-container').addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        if (touchStartX - touchEndX > 50) {
            nextSlide();
        }
        if (touchEndX - touchStartX > 50) {
            prevSlide();
        }
    }

    // tab switching functions (for How It Works section)
    function switchTab(tab) {
        // update tab buttons
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active');
        });
        document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.add('active');

        // update content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById('content' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.add('active');
    }

    // scroll functions for timeline
    function scrollTimelineLeft() {
        // cek tab mana yang aktif
        const activeMahasiswa = document.getElementById('contentMahasiswa').classList.contains('active');
        const timelineId = activeMahasiswa ? 'timelineMahasiswa' : 'timelineInstitusi';
        const timeline = document.getElementById(timelineId);
        if (timeline) {
            timeline.scrollBy({ left: -320, behavior: 'smooth' });
        }
    }

    function scrollTimelineRight() {
        // cek tab mana yang aktif
        const activeMahasiswa = document.getElementById('contentMahasiswa').classList.contains('active');
        const timelineId = activeMahasiswa ? 'timelineMahasiswa' : 'timelineInstitusi';
        const timeline = document.getElementById(timelineId);
        if (timeline) {
            timeline.scrollBy({ left: 320, behavior: 'smooth' });
        }
    }

    // initialize carousel
    updateCarousel();
</script>
@endpush
@endsection
