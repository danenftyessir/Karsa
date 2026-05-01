{{-- resources/views/student/repository/components/filter-sidebar.blade.php --}}

{{-- 
    komponen filter sidebar untuk knowledge repository
    menggunakan Alpine.js untuk interaktivitas
    mendukung multiple selection untuk SDG categories
--}}

<div class="filter-sidebar bg-white rounded-xl shadow-sm p-6 sticky top-24" x-data="repositoryFilterHandler()">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-900">Filter</h3>
        @if(request()->hasAny(['search', 'category', 'province_id', 'regency_id', 'year', 'file_type']))
            <button type="button" 
                    @click="clearFilters()"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium transition-colors">
                Reset
            </button>
        @endif
    </div>
    
    <form action="{{ route('student.repository.index') }}" method="GET" class="space-y-6">
        
        {{-- pencarian teks --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Cari Dokumen
            </label>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Judul, deskripsi, penulis..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                   x-model="filters.search">
        </div>

        {{-- ✅ KATEGORI SDG - MULTIPLE CHECKBOX SELECTION --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
                Kategori SDG
            </label>
            <div class="space-y-2 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                @php
                    $sdgCategories = sdg_categories_array();
                    $selectedCategories = request('category', []);
                    if (!is_array($selectedCategories)) {
                        $selectedCategories = [$selectedCategories];
                    }
                @endphp
                
                @foreach($sdgCategories as $value => $label)
                <label class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors group">
                    <input type="checkbox" 
                           name="category[]" 
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
            
            {{-- info jumlah kategori dipilih --}}
            <div class="mt-2 text-xs text-gray-500" x-show="getSelectedCategoriesCount() > 0" x-cloak>
                <span x-text="getSelectedCategoriesCount()"></span> kategori dipilih
            </div>
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

        {{-- tipe file --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Tipe File
            </label>
            <select name="file_type" 
                    x-model="filters.fileType"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <option value="">Semua Tipe</option>
                <option value="pdf" {{ request('file_type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                <option value="docx" {{ request('file_type') == 'docx' ? 'selected' : '' }}>DOCX</option>
                <option value="xlsx" {{ request('file_type') == 'xlsx' ? 'selected' : '' }}>XLSX</option>
                <option value="pptx" {{ request('file_type') == 'pptx' ? 'selected' : '' }}>PPTX</option>
            </select>
        </div>

        {{-- tahun --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Tahun
            </label>
            <select name="year" 
                    x-model="filters.year"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                <option value="">Semua Tahun</option>
                @foreach($availableYears ?? [] as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
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
                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>
                    Paling Banyak Diunduh
                </option>
                <option value="most_viewed" {{ request('sort') == 'most_viewed' ? 'selected' : '' }}>
                    Paling Banyak Dilihat
                </option>
                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>
                    Judul (A-Z)
                </option>
            </select>
        </div>

        {{-- status featured --}}
        <div class="space-y-3">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" 
                       name="featured" 
                       value="1"
                       {{ request('featured') == '1' ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-700">Hanya Dokumen Featured</span>
            </label>
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
function repositoryFilterHandler() {
    return {
        filters: {
            search: '{{ request('search') }}',
            provinceId: '{{ request('province_id') }}',
            regencyId: '{{ request('regency_id') }}',
            fileType: '{{ request('file_type') }}',
            year: '{{ request('year') }}',
            sort: '{{ request('sort', 'latest') }}'
        },
        regencies: [],
        
        // load regencies berdasarkan province yang dipilih
        async loadRegencies() {
            if (!this.filters.provinceId) {
                this.regencies = [];
                this.filters.regencyId = '';
                return;
            }
            
            try {
                const response = await fetch(`{{ route('student.repository.get-regencies') }}?province_id=${this.filters.provinceId}`);
                this.regencies = await response.json();
                
                // reset regency selection jika province berubah
                const currentRegencyValid = this.regencies.some(r => r.id == this.filters.regencyId);
                if (!currentRegencyValid) {
                    this.filters.regencyId = '';
                }
            } catch (error) {
                console.error('Error loading regencies:', error);
            }
        },
        
        // hitung jumlah kategori yang dipilih
        getSelectedCategoriesCount() {
            const checkboxes = document.querySelectorAll('input[name="category[]"]:checked');
            return checkboxes.length;
        },
        
        // reset semua filter
        clearFilters() {
            window.location.href = '{{ route('student.repository.index') }}';
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