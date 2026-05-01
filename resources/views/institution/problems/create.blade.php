@extends('layouts.app')

@section('title', 'Buat Masalah Baru')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Buat Masalah Baru</h1>
            <p class="mt-2 text-gray-600">Publikasikan Masalah Untuk Mendapatkan Bantuan Mahasiswa KKN</p>
        </div>

        @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <div class="font-semibold mb-2">Terdapat Kesalahan Pada Form:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('institution.problems.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="problemForm">
            @csrf

            {{-- informasi dasar --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Dasar</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Judul Masalah <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: Peningkatan Literasi Digital di Desa Sukamaju">
                        @error('title') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" rows="4" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Jelaskan masalah yang dihadapi...">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Latar Belakang</label>
                        <textarea name="background" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Jelaskan latar belakang masalah...">{{ old('background') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tujuan</label>
                        <textarea name="objectives" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Sebutkan tujuan dari proyek ini...">{{ old('objectives') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Hasil Yang Diharapkan
                        </label>
                        <textarea name="expected_outcomes" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Apa hasil yang diharapkan dari proyek ini?">{{ old('expected_outcomes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- lokasi --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Lokasi</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Provinsi <span class="text-red-500">*</span>
                        </label>
                        <select name="province_id" id="province_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('province_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kabupaten/Kota <span class="text-red-500">*</span>
                        </label>
                        <select name="regency_id" id="regency_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Kabupaten/Kota</option>
                        </select>
                        @error('regency_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Detail</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Contoh: Jl. Raya Desa No. 123">
                    </div>
                </div>
            </div>

            {{-- kategori sdg --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-4">
                    Kategori SDG <span class="text-red-500">*</span>
                </h2>
                <p class="text-sm text-gray-600 mb-4">Pilih minimal 1 kategori Sustainable Development Goals yang relevan</p>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @php
                        $sdgCategories = [
                            1 => 'Tanpa Kemiskinan',
                            2 => 'Tanpa Kelaparan',
                            3 => 'Kehidupan Sehat Dan Sejahtera',
                            4 => 'Pendidikan Berkualitas',
                            5 => 'Kesetaraan Gender',
                            6 => 'Air Bersih Dan Sanitasi Layak',
                            7 => 'Energi Bersih Dan Terjangkau',
                            8 => 'Pekerjaan Layak Dan Pertumbuhan Ekonomi',
                            9 => 'Industri, Inovasi Dan Infrastruktur',
                            10 => 'Berkurangnya Kesenjangan',
                            11 => 'Kota Dan Komunitas Berkelanjutan',
                            12 => 'Konsumsi Dan Produksi Bertanggung Jawab',
                            13 => 'Penanganan Perubahan Iklim',
                            14 => 'Ekosistem Laut',
                            15 => 'Ekosistem Daratan',
                            16 => 'Perdamaian, Keadilan Dan Kelembagaan Yang Tangguh',
                            17 => 'Kemitraan Untuk Mencapai Tujuan'
                        ];
                        $oldSdg = old('sdg_categories', []);
                    @endphp
                    @foreach($sdgCategories as $id => $name)
                        <label class="flex items-start gap-2 p-3 border border-gray-200 rounded-lg hover:bg-blue-50 cursor-pointer transition-colors">
                            <input type="checkbox" name="sdg_categories[]" value="{{ $id }}"
                                   {{ in_array($id, $oldSdg) ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500">
                            <span class="text-sm text-gray-700 leading-tight">{{ $name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('sdg_categories') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- requirements --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Requirements</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Mahasiswa Yang Dibutuhkan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="required_students" value="{{ old('required_students', 1) }}" min="1" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('required_students') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Skills Yang Dibutuhkan <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-2">Tambahkan skill satu per satu menggunakan tombol "+ Tambah Skill"</p>
                        <div id="skills-container" class="space-y-2">
                            @php
                                $skills = old('required_skills', []);
                                if (empty($skills)) {
                                    $skills = [''];
                                }
                            @endphp
                            @foreach($skills as $skill)
                            <input type="text" 
                                   name="required_skills[]" 
                                   value="{{ $skill }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Contoh: Komunikasi">
                            @endforeach
                        </div>
                        <button type="button" onclick="addSkillField()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            + Tambah Skill
                        </button>
                        @error('required_skills') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan Yang Dibutuhkan (Opsional)</label>
                        <p class="text-xs text-gray-500 mb-2">Tambahkan jurusan satu per satu menggunakan tombol "+ Tambah Jurusan"</p>
                        <div id="majors-container" class="space-y-2">
                            @php
                                $majors = old('required_majors', []);
                                if (empty($majors)) {
                                    $majors = [''];
                                }
                            @endphp
                            @foreach($majors as $major)
                            <input type="text" 
                                   name="required_majors[]" 
                                   value="{{ $major }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Contoh: Teknik Informatika">
                            @endforeach
                        </div>
                        <button type="button" onclick="addMajorField()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            + Tambah Jurusan
                        </button>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tingkat Kesulitan <span class="text-red-500">*</span>
                        </label>
                        <select name="difficulty_level" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="beginner" {{ old('difficulty_level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ old('difficulty_level', 'intermediate') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('difficulty_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                        @error('difficulty_level') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deliverables</label>
                        <p class="text-xs text-gray-500 mb-2">Output yang diharapkan dari mahasiswa</p>
                        <div id="deliverables-container" class="space-y-2">
                            @php
                                $deliverables = old('deliverables', []);
                                if (empty($deliverables)) {
                                    $deliverables = [''];
                                }
                            @endphp
                            @foreach($deliverables as $deliverable)
                            <input type="text" 
                                   name="deliverables[]" 
                                   value="{{ $deliverable }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Contoh: Laporan Penelitian">
                            @endforeach
                        </div>
                        <button type="button" onclick="addDeliverableField()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            + Tambah Deliverable
                        </button>
                    </div>
                </div>
            </div>

            {{-- timeline --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Timeline</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" value="{{ old('start_date') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('start_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('end_date') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Deadline Aplikasi <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="application_deadline" value="{{ old('application_deadline') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('application_deadline') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Durasi (Bulan) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="duration_months" value="{{ old('duration_months', 1) }}" min="1" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('duration_months') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- fasilitas --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Fasilitas Yang Disediakan</h2>
                <p class="text-sm text-gray-600 mb-4">Fasilitas yang akan diberikan kepada mahasiswa</p>
                
                <div id="facilities-container" class="space-y-2">
                    @php
                        $facilities = old('facilities_provided', []);
                        if (empty($facilities)) {
                            $facilities = [''];
                        }
                    @endphp
                    @foreach($facilities as $facility)
                    <input type="text" 
                           name="facilities_provided[]" 
                           value="{{ $facility }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Contoh: Akomodasi">
                    @endforeach
                </div>
                <button type="button" onclick="addFacilityField()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-semibold">
                    + Tambah Fasilitas
                </button>
            </div>

            {{-- dokumentasi --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Dokumentasi</h2>
                <p class="text-sm text-gray-600 mb-4">Upload foto dokumentasi masalah (maksimal 5 foto, masing-masing max 5MB)</p>
                
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-500 transition-colors cursor-pointer">
                    <input type="file" name="images[]" id="images-input" accept="image/*" multiple class="hidden">
                    <label for="images-input" class="cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Klik untuk memilih gambar atau drag & drop</p>
                        <p class="mt-1 text-xs text-gray-500">PNG, JPG, JPEG hingga 5MB</p>
                    </label>
                </div>
                
                <div id="images-preview" class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4"></div>
                @error('images') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
                @error('images.*') <p class="text-red-600 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- status publikasi --}}
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Status Publikasi</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft (Tidak Dipublikasikan)</option>
                        <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>Open (Terima Aplikasi)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih "Draft" jika ingin menyimpan terlebih dahulu, atau "Open" untuk langsung dipublikasikan</p>
                    @error('status') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- action buttons --}}
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Simpan Problem
                </button>
                <a href="{{ route('institution.problems.index') }}" 
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                    Batal
                </a>
            </div>

        </form>

    </div>
</div>

<script>
// fungsi untuk menambah field skill
function addSkillField() {
    const container = document.getElementById('skills-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'required_skills[]';
    input.required = true;
    input.placeholder = 'Contoh: Penelitian';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

// fungsi untuk menambah field jurusan
function addMajorField() {
    const container = document.getElementById('majors-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'required_majors[]';
    input.placeholder = 'Contoh: Kesehatan Masyarakat';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

// fungsi untuk menambah field deliverable
function addDeliverableField() {
    const container = document.getElementById('deliverables-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'deliverables[]';
    input.placeholder = 'Contoh: Modul Pelatihan';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

// fungsi untuk menambah field fasilitas
function addFacilityField() {
    const container = document.getElementById('facilities-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'facilities_provided[]';
    input.placeholder = 'Contoh: Transportasi';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

// load regencies berdasarkan provinsi
document.getElementById('province_id').addEventListener('change', function() {
    const provinceId = this.value;
    const regencySelect = document.getElementById('regency_id');
    
    if (provinceId) {
        fetch(`/api/regencies/${provinceId}`)
            .then(response => response.json())
            .then(data => {
                regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                data.forEach(regency => {
                    regencySelect.innerHTML += `<option value="${regency.id}">${regency.name}</option>`;
                });
            })
            .catch(error => {
                console.error('Error loading regencies:', error);
                alert('Gagal memuat data kabupaten/kota');
            });
    } else {
        regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
    }
});

// preview gambar yang diupload
document.getElementById('images-input').addEventListener('change', function(e) {
    const preview = document.getElementById('images-preview');
    preview.innerHTML = '';
    
    const files = Array.from(e.target.files).slice(0, 5); // max 5 images
    
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative aspect-square';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg border border-gray-300">
                <div class="absolute top-1 left-1 bg-blue-600 text-white text-xs px-2 py-1 rounded">
                    ${index === 0 ? 'Cover' : index + 1}
                </div>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});

// form validation sebelum submit
document.getElementById('problemForm').addEventListener('submit', function(e) {
    const sdgChecked = document.querySelectorAll('input[name="sdg_categories[]"]:checked');
    if (sdgChecked.length === 0) {
        e.preventDefault();
        alert('Pilih minimal 1 kategori SDG!');
        return false;
    }
});
</script>
@endsection