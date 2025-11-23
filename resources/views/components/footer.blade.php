{{-- resources/views/components/footer.blade.php --}}
<footer class="bg-gray-900 text-white border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-16">
        {{-- logo dan tagline --}}
        <div class="flex flex-col items-center mb-12 space-y-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset('karsa-logo.png') }}" alt="Karsa - Karya Untuk Bangsa" class="h-12 w-auto">
            </div>
            <p class="text-gray-400 text-center max-w-2xl text-base leading-relaxed">
                Platform Digital Yang Menghubungkan Mahasiswa Dengan Instansi Untuk Program Kuliah Kerja Nyata Berkelanjutan
            </p>
        </div>

        {{-- navigasi footer --}}
        <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 mb-12">
            <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors duration-300 font-medium">
                Beranda
            </a>
            <a href="{{ route('about') }}" class="text-gray-400 hover:text-white transition-colors duration-300 font-medium">
                Tentang Kami
            </a>
            <a href="{{ route('contact') }}" class="text-gray-400 hover:text-white transition-colors duration-300 font-medium">
                Kontak
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300 font-medium">
                Kebijakan Privasi
            </a>
        </div>

        {{-- copyright --}}
        <div class="border-t border-gray-800 pt-8">
            <p class="text-center text-sm text-gray-500 leading-relaxed max-w-3xl mx-auto">
                Hak cipta © {{ date('Y') }}. Seluruh hak cipta dilindungi undang-undang dan terdaftar pada Direktorat Jendral Kekayaan Intelektual Republik Indonesia.
            </p>
        </div>
    </div>
</footer>