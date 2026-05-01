{{-- resources/views/student/browse-problems/components/filter-sidebar.blade.php --}}

{{-- 
    komponen filter sidebar untuk browse problems
    menggunakan Alpine.js untuk interaktivitas
    mendukung multiple selection untuk SDG categories
--}}

<div class="filter-sidebar bg-white rounded-xl shadow-sm p-6 sticky top-24" x-data="filterHandler()">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-900">Filter</h3>
        @if(request()->hasAny(['search', 'province_id', 'regency_id', 'difficulty', 'sdg_categories', 'duration']))
            <button type="button"
                    @click="clearFilters()"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors">
                Reset
            </button>
        @endif
    </div>

    {{-- Statistik Informasi --}}
    <div class="mb-6 pb-6 border-b border-gray-200">
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <span class="text-sm text-gray-700 font-medium">Total Proyek</span>
                <span class="text-lg font-bold text-blue-600">{{ $totalProblems ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                <span class="text-sm text-gray-700 font-medium">Kategori SDG</span>
                <span class="text-lg font-bold text-purple-600">{{ $sdgCategories ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <span class="text-sm text-gray-700 font-medium">Provinsi Tersedia</span>
                <span class="text-lg font-bold text-green-600">{{ $provinces->count() ?? 0 }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('student.browse-problems.index') }}" method="GET" class="space-y-6">
        
        {{-- pencarian teks --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Cari Problem
            </label>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Judul atau deskripsi..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                   x-model="filters.search">
        </div>

        {{-- lokasi - province dan regency --}}
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Provinsi
                </label>
                <select name="province_id" 
                        x-model="filters.provinceId"
                        @change="loadRegencies()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">Semua Provinsi</option>
                    @foreach($provinces ?? [] as $province)
                        <option value="{{ $province->id }}" {{ request('province_id') == $province->id ? 'selected' : '' }}>
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div x-show="filters.provinceId" x-cloak>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Kabupaten/Kota
                </label>
                <select name="regency_id" 
                        x-model="filters.regencyId"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    <option value="">Semua Kabupaten/Kota</option>
                    <template x-for="regency in regencies" :key="regency.id">
                        <option :value="regency.id" x-text="regency.name"></option>
                    </template>
                </select>
            </div>
        </div>

        {{-- KATEGORI SDG - MULTIPLE CHECKBOX SELECTION --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Kategori SDG
            </label>
            <div class="space-y-2 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                @php
                    $sdgCategories = [
                        1 => 'Tanpa Kemiskinan',
                        2 => 'Tanpa Kelaparan',
                        3 => 'Kehidupan Sehat Dan Sejahtera',
                        4 => 'Pendidikan Berkualitas',
                        5 => 'Kesetaraan Gender',
                        6 => 'Air Bersih Dan Sanitasi',
                        7 => 'Energi Bersih Dan Terjangkau',
                        8 => 'Pekerjaan Layak Dan Pertumbuhan Ekonomi',
                        9 => 'Industri, Inovasi Dan Infrastruktur',
                        10 => 'Berkurangnya Kesenjangan',
                        11 => 'Kota Dan Komunitas Berkelanjutan',
                        12 => 'Konsumsi Dan Produksi Bertanggung Jawab',
                        13 => 'Penanganan Perubahan Iklim',
                        14 => 'Ekosistem Laut',
                        15 => 'Ekosistem Daratan',
                        16 => 'Perdamaian, Keadilan Dan Kelembagaan Yang Kuat',
                        17 => 'Kemitraan Untuk Mencapai Tujuan'
                    ];
                    $selectedCategories = request('sdg_categories', []);
                @endphp
                
                @foreach($sdgCategories as $value => $label)
                <label class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors group">
                    <input type="checkbox" 
                           name="sdg_categories[]" 
                           value="{{ $value }}"
                           {{ in_array($value, $selectedCategories) ? 'checked' : '' }}
                           class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 transition-all">
                    <div class="ml-3 flex-1">
                        <span class="text-sm text-gray-900 group-hover:text-blue-600 transition-colors">
                            {{ $value }}. {{ $label }}
                        </span>
                    </div>
                </label>
                @endforeach
            </div>
            
            {{-- info jumlah SDG dipilih --}}
            <div class="mt-2 text-xs text-gray-500" x-show="getSelectedSDGCount() > 0" x-cloak>
                <span x-text="getSelectedSDGCount()"></span> kategori dipilih
            </div>
        </div>

        {{-- tingkat kesulitan --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Tingkat Kesulitan
            </label>
            <select name="difficulty" 
                    x-model="filters.difficulty"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <option value="">Semua Tingkat</option>
                <option value="beginner" {{ request('difficulty') == 'beginner' ? 'selected' : '' }}>
                    Pemula
                </option>
                <option value="intermediate" {{ request('difficulty') == 'intermediate' ? 'selected' : '' }}>
                    Menengah
                </option>
                <option value="advanced" {{ request('difficulty') == 'advanced' ? 'selected' : '' }}>
                    Lanjutan
                </option>
            </select>
        </div>

        {{-- durasi proyek --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Durasi Proyek
            </label>
            <select name="duration" 
                    x-model="filters.duration"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <option value="">Semua Durasi</option>
                <option value="1-2" {{ request('duration') == '1-2' ? 'selected' : '' }}>
                    1-2 Bulan
                </option>
                <option value="3-4" {{ request('duration') == '3-4' ? 'selected' : '' }}>
                    3-4 Bulan
                </option>
                <option value="5-6" {{ request('duration') == '5-6' ? 'selected' : '' }}>
                    5-6 Bulan
                </option>
                <option value="6+" {{ request('duration') == '6+' ? 'selected' : '' }}>
                    Lebih dari 6 Bulan
                </option>
            </select>
        </div>

        {{-- status problem --}}
        <div class="space-y-3">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" 
                       name="is_urgent" 
                       value="1"
                       {{ request('is_urgent') == '1' ? 'checked' : '' }}
                       class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-2 focus:ring-red-500">
                <span class="ml-2 text-sm text-gray-700">Urgent</span>
            </label>
            
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" 
                       name="is_featured" 
                       value="1"
                       {{ request('is_featured') == '1' ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Featured</span>
            </label>
        </div>

        {{-- sorting --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Urutkan
            </label>
            <select name="sort" 
                    x-model="filters.sort"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                    Terbaru
                </option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                    Terlama
                </option>
                <option value="deadline" {{ request('sort') == 'deadline' ? 'selected' : '' }}>
                    Deadline Terdekat
                </option>
                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>
                    Terpopuler
                </option>
            </select>
        </div>

        {{-- tombol apply filter --}}
        <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors shadow-sm hover:shadow-md">
            Terapkan Filter
        </button>
    </form>
</div>

{{-- custom scrollbar style --}}
<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}
</style>

{{-- Alpine.js handler untuk filter interaktif --}}
<script>
function filterHandler() {
    return {
        filters: {
            search: '{{ request('search') }}',
            provinceId: '{{ request('province_id') }}',
            regencyId: '{{ request('regency_id') }}',
            difficulty: '{{ request('difficulty') }}',
            duration: '{{ request('duration') }}',
            sort: '{{ request('sort', 'latest') }}'
        },
        regencies: @json($regencies ?? []),
        
        // load regencies berdasarkan province yang dipilih
        async loadRegencies() {
            if (!this.filters.provinceId) {
                this.regencies = [];
                this.filters.regencyId = '';
                return;
            }
            
            try {
                const url = '/student/browse-problems/get-regencies?province_id=' + this.filters.provinceId;
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error('Failed to load regencies');
                }
                
                this.regencies = await response.json();
                
                // reset regency selection jika province berubah
                const currentRegencyValid = this.regencies.some(r => r.id == this.filters.regencyId);
                if (!currentRegencyValid) {
                    this.filters.regencyId = '';
                }
            } catch (error) {
                console.error('Error loading regencies:', error);
                this.regencies = [];
            }
        },
        
        // hitung jumlah SDG yang dipilih
        getSelectedSDGCount() {
            const checkboxes = document.querySelectorAll('input[name="sdg_categories[]"]:checked');
            return checkboxes.length;
        },
        
        // reset semua filter
        clearFilters() {
            window.location.href = '{{ route('student.browse-problems.index') }}';
        },
        
        // init: load regencies jika province sudah dipilih
        init() {
            if (this.filters.provinceId) {
                this.loadRegencies();
            }
        }
    }
}
</script>