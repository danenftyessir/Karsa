@extends('layouts.app')

@section('title', 'Edit Masalah')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- header --}}
        <div class="mb-8">
            <a href="{{ route('institution.problems.show', $problem->id) }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Masalah</h1>
            <p class="text-gray-600 mt-1">Perbarui informasi masalah</p>
        </div>

        {{-- form --}}
        <form action="{{ route('institution.problems.update', $problem->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- informasi dasar --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Dasar</h2>

                <div class="space-y-4">
                    {{-- judul --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Masalah *</label>
                        <input type="text" 
                               name="title" 
                               value="{{ old('title', $problem->title) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('title')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- deskripsi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Masalah *</label>
                        <textarea name="description" 
                                  rows="4" 
                                  required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $problem->description) }}</textarea>
                        @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- background --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Latar Belakang</label>
                        <textarea name="background" 
                                  rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('background', $problem->background) }}</textarea>
                    </div>

                    {{-- objectives --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tujuan</label>
                        <textarea name="objectives" 
                                  rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('objectives', $problem->objectives) }}</textarea>
                    </div>

                    {{-- scope --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ruang Lingkup</label>
                        <textarea name="scope" 
                                  rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('scope', $problem->scope) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- lokasi & sdg --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Lokasi & Kategori SDG</h2>

                <div class="space-y-4">
                    {{-- provinsi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi *</label>
                        <select name="province_id" 
                                id="province_id" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                            <option value="{{ $province->id }}" 
                                    {{ old('province_id', $problem->province_id) == $province->id ? 'selected' : '' }}>
                                {{ $province->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- kabupaten/kota --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kabupaten/Kota *</label>
                        <select name="regency_id" 
                                id="regency_id" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Kabupaten/Kota</option>
                            @foreach($regencies as $regency)
                            <option value="{{ $regency->id }}" 
                                    {{ old('regency_id', $problem->regency_id) == $regency->id ? 'selected' : '' }}>
                                {{ $regency->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- desa --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Desa/Kelurahan</label>
                        <input type="text" 
                               name="village" 
                               value="{{ old('village', $problem->village) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- lokasi detail --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Detail Lokasi</label>
                        <textarea name="detailed_location" 
                                  rows="2" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('detailed_location', $problem->detailed_location) }}</textarea>
                    </div>

                    {{-- kategori SDG --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori SDG *</label>
                        <div class="grid grid-cols-3 gap-3">
                            @php
                                $selectedSdg = old('sdg_categories', is_array($problem->sdg_categories) ? $problem->sdg_categories : json_decode($problem->sdg_categories, true) ?? []);
                            @endphp
                            @for($i = 1; $i <= 17; $i++)
                            <label class="flex items-center gap-2 p-3 border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer transition-colors">
                                <input type="checkbox" 
                                       name="sdg_categories[]" 
                                       value="{{ $i }}"
                                       {{ in_array($i, $selectedSdg) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500">
                                <span class="text-sm">SDG {{ $i }}</span>
                            </label>
                            @endfor
                        </div>
                    </div>

                    {{-- gambar yang sudah ada --}}
                    @if($problem->images->count() > 0)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Gambar Saat Ini</label>
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($problem->images as $image)
                            <div class="relative group">
                                <img src="{{ $image->image_url }}" 
                                    alt="Problem image" 
                                    onerror="this.onerror=null; this.src='https://via.placeholder.com/200?text=No+Image';"
                                    class="w-full h-32 object-cover rounded-lg border-2 border-gray-200">
                                
                                {{-- badge cover --}}
                                @if($image->is_cover)
                                <div class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">
                                    Cover
                                </div>
                                @endif
                                
                                {{-- checkbox untuk hapus --}}
                                <label class="absolute top-2 right-2 cursor-pointer bg-white rounded-lg shadow-md p-1 hover:bg-gray-100 transition-colors">
                                    <input type="checkbox" 
                                        name="delete_images[]" 
                                        value="{{ $image->id }}"
                                        class="hidden peer"
                                        onchange="this.parentElement.classList.toggle('bg-red-100', this.checked)">
                                    <span class="text-red-600 font-semibold text-xs px-2 py-1 block peer-checked:bg-red-600 peer-checked:text-white rounded">
                                        Hapus
                                    </span>
                                </label>
                                
                                {{-- overlay saat dipilih untuk dihapus --}}
                                <div class="absolute inset-0 bg-red-500 bg-opacity-0 pointer-events-none transition-all rounded-lg"
                                    style="display: none;">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Centang "Hapus" pada gambar yang ingin dihapus</p>
                    </div>
                    @endif

                    {{-- upload gambar baru --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tambah Gambar Baru 
                            @if($problem->images->count() > 0)
                                (Opsional - Maksimal {{ 5 - $problem->images->count() }} gambar lagi)
                            @else
                                (Maksimal 5 gambar)
                            @endif
                        </label>
                        <input type="file" 
                            name="images[]" 
                            multiple 
                            accept="image/*"
                            id="new-images-input"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 5MB per file</p>
                        
                        {{-- preview gambar baru --}}
                        <div id="new-images-preview" class="mt-4 grid grid-cols-3 gap-4"></div>
                    </div>

                    {{-- script untuk preview gambar baru --}}
                    <script>
                    document.getElementById('new-images-input').addEventListener('change', function(e) {
                        const preview = document.getElementById('new-images-preview');
                        preview.innerHTML = '';
                        
                        const files = Array.from(e.target.files);
                        const maxFiles = 5 - {{ $problem->images->count() }};
                        
                        files.slice(0, maxFiles).forEach((file, index) => {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const div = document.createElement('div');
                                div.className = 'relative';
                                div.innerHTML = `
                                    <img src="${e.target.result}" 
                                        class="w-full h-32 object-cover rounded-lg border-2 border-green-300">
                                    <div class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded">
                                        Baru ${index + 1}
                                    </div>
                                `;
                                preview.appendChild(div);
                            };
                            reader.readAsDataURL(file);
                        });
                    });

                    // visual feedback untuk gambar yang akan dihapus
                    document.querySelectorAll('input[name="delete_images[]"]').forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const imageDiv = this.closest('.relative');
                            const overlay = imageDiv.querySelector('.absolute.inset-0');
                            
                            if (this.checked) {
                                imageDiv.querySelector('img').classList.add('opacity-50', 'grayscale');
                                if (overlay) overlay.style.display = 'block';
                            } else {
                                imageDiv.querySelector('img').classList.remove('opacity-50', 'grayscale');
                                if (overlay) overlay.style.display = 'none';
                            }
                        });
                    });
                    </script>

            {{-- requirements --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Requirements</h2>

                <div class="space-y-4">
                    {{-- jumlah mahasiswa --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Mahasiswa *</label>
                        <input type="number" 
                               name="required_students" 
                               value="{{ old('required_students', $problem->required_students) }}"
                               min="1"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- skill --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Skill yang Dibutuhkan *</label>
                        <div id="skills-container" class="space-y-2">
                            @php
                                $skills = old('required_skills', is_array($problem->required_skills) ? $problem->required_skills : json_decode($problem->required_skills, true) ?? []);
                            @endphp
                            @foreach($skills as $skill)
                            <input type="text" 
                                   name="required_skills[]" 
                                   value="{{ $skill }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @endforeach
                        </div>
                        <button type="button" onclick="addSkillField()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            + Tambah Skill
                        </button>
                    </div>

                    {{-- jurusan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jurusan yang Dibutuhkan</label>
                        <div id="majors-container" class="space-y-2">
                            @php
                                $majors = old('required_majors', is_array($problem->required_majors) ? $problem->required_majors : json_decode($problem->required_majors, true) ?? []);
                            @endphp
                            @if(count($majors) > 0)
                                @foreach($majors as $major)
                                <input type="text" 
                                       name="required_majors[]" 
                                       value="{{ $major }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @endforeach
                            @else
                                <input type="text" 
                                       name="required_majors[]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @endif
                        </div>
                        <button type="button" onclick="addMajorField()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            + Tambah Jurusan
                        </button>
                    </div>

                    {{-- tingkat kesulitan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tingkat Kesulitan *</label>
                        <select name="difficulty_level" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="beginner" {{ old('difficulty_level', $problem->difficulty_level) == 'beginner' ? 'selected' : '' }}>Pemula</option>
                            <option value="intermediate" {{ old('difficulty_level', $problem->difficulty_level) == 'intermediate' ? 'selected' : '' }}>Menengah</option>
                            <option value="advanced" {{ old('difficulty_level', $problem->difficulty_level) == 'advanced' ? 'selected' : '' }}>Lanjutan</option>
                        </select>
                    </div>

                    {{-- expected outcomes --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Expected Outcomes</label>
                        <textarea name="expected_outcomes" 
                                  rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('expected_outcomes', $problem->expected_outcomes) }}</textarea>
                    </div>

                    {{-- deliverables --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deliverables</label>
                        <div id="deliverables-container" class="space-y-2">
                            @php
                                $deliverables = old('deliverables', is_array($problem->deliverables) ? $problem->deliverables : json_decode($problem->deliverables, true) ?? []);
                            @endphp
                            @if(count($deliverables) > 0)
                                @foreach($deliverables as $deliverable)
                                <input type="text" 
                                       name="deliverables[]" 
                                       value="{{ $deliverable }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @endforeach
                            @else
                                <input type="text" 
                                       name="deliverables[]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @endif
                        </div>
                        <button type="button" onclick="addDeliverableField()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            + Tambah Deliverable
                        </button>
                    </div>
                </div>
            </div>

            {{-- timeline & fasilitas --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Timeline & Fasilitas</h2>

                <div class="space-y-4">
                    {{-- tanggal mulai --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai *</label>
                        <input type="date" 
                               name="start_date" 
                               value="{{ old('start_date', $problem->start_date->format('Y-m-d')) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- tanggal selesai --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai *</label>
                        <input type="date" 
                               name="end_date" 
                               value="{{ old('end_date', $problem->end_date->format('Y-m-d')) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- deadline aplikasi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deadline Aplikasi *</label>
                        <input type="date" 
                               name="application_deadline" 
                               value="{{ old('application_deadline', $problem->application_deadline->format('Y-m-d')) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- durasi (bulan) --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Durasi (Bulan) *</label>
                        <input type="number" 
                               name="duration_months" 
                               value="{{ old('duration_months', $problem->duration_months) }}"
                               required
                               min="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('duration_months')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- fasilitas --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fasilitas yang Disediakan</label>
                        <div id="facilities-container" class="space-y-2">
                            @php
                                $facilities = old('facilities_provided', is_array($problem->facilities_provided) ? $problem->facilities_provided : json_decode($problem->facilities_provided, true) ?? []);
                            @endphp
                            @if(count($facilities) > 0)
                                @foreach($facilities as $facility)
                                <input type="text" 
                                       name="facilities_provided[]" 
                                       value="{{ $facility }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @endforeach
                            @else
                                <input type="text" 
                                       name="facilities_provided[]" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @endif
                        </div>
                        <button type="button" onclick="addFacilityField()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            + Tambah Fasilitas
                        </button>
                    </div>

                    {{-- status --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                        <select name="status" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="draft" {{ old('status', $problem->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="open" {{ old('status', $problem->status) == 'open' ? 'selected' : '' }}>Terbuka</option>
                            <option value="closed" {{ old('status', $problem->status) == 'closed' ? 'selected' : '' }}>Ditutup</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- action buttons --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex gap-3">
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('institution.problems.show', $problem->id) }}" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                        Batal
                    </a>
                </div>
            </div>

        </form>

    </div>
</div>

<script>
// dynamic fields functions
function addSkillField() {
    const container = document.getElementById('skills-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'required_skills[]';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

function addMajorField() {
    const container = document.getElementById('majors-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'required_majors[]';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

function addDeliverableField() {
    const container = document.getElementById('deliverables-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'deliverables[]';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

function addFacilityField() {
    const container = document.getElementById('facilities-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'facilities_provided[]';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

// load regencies based on province
document.getElementById('province_id').addEventListener('change', function() {
    const provinceId = this.value;
    const regencySelect = document.getElementById('regency_id');
    
    if (provinceId) {
        fetch(`/institution/problems/regencies/${provinceId}`)
            .then(response => response.json())
            .then(data => {
                regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                data.forEach(regency => {
                    regencySelect.innerHTML += `<option value="${regency.id}">${regency.name}</option>`;
                });
            });
    }
});
</script>
@endsection