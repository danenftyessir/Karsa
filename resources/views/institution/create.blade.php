@extends('layouts.app')

@section('title', 'Buat Masalah Baru')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- header --}}
        <div class="mb-8">
            <a href="{{ route('institution.problems.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Buat Masalah Baru</h1>
            <p class="text-gray-600 mt-1">Isi informasi lengkap tentang masalah yang ingin diselesaikan</p>
        </div>

        {{-- wizard progress --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex items-center" id="step-1-indicator">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">1</div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-900">Informasi Dasar</p>
                    </div>
                </div>
                <div class="w-16 h-1 bg-gray-300" id="line-1"></div>
                <div class="flex-1 flex items-center" id="step-2-indicator">
                    <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold">2</div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-600">Lokasi & SDG</p>
                    </div>
                </div>
                <div class="w-16 h-1 bg-gray-300" id="line-2"></div>
                <div class="flex-1 flex items-center" id="step-3-indicator">
                    <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold">3</div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-600">Requirements</p>
                    </div>
                </div>
                <div class="w-16 h-1 bg-gray-300" id="line-3"></div>
                <div class="flex-1 flex items-center" id="step-4-indicator">
                    <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold">4</div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-600">Timeline & Fasilitas</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- form --}}
        <form method="POST" action="{{ route('institution.problems.store') }}" enctype="multipart/form-data" id="problem-form">
            @csrf

            {{-- step 1: informasi dasar --}}
            <div class="wizard-step" id="step-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Dasar</h2>

                    <div class="space-y-4">
                        {{-- judul --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Masalah *</label>
                            <input type="text" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Contoh: Pengolahan Sampah di Desa Sukamaju">
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
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Jelaskan masalah secara singkat dan jelas...">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- background --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Latar Belakang</label>
                            <textarea name="background" 
                                      rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Latar belakang masalah (opsional)">{{ old('background') }}</textarea>
                        </div>

                        {{-- objectives --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tujuan</label>
                            <textarea name="objectives" 
                                      rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Tujuan yang ingin dicapai (opsional)">{{ old('objectives') }}</textarea>
                        </div>

                        {{-- scope --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ruang Lingkup</label>
                            <textarea name="scope" 
                                      rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Ruang lingkup pekerjaan (opsional)">{{ old('scope') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="button" onclick="nextStep(2)" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            Lanjut
                        </button>
                    </div>
                </div>
            </div>

            {{-- step 2: lokasi & sdg --}}
            <div class="wizard-step hidden" id="step-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
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
                                <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('province_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- kabupaten/kota --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kabupaten/Kota *</label>
                            <select name="regency_id" 
                                    id="regency_id" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Pilih Kabupaten/Kota</option>
                            </select>
                            @error('regency_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- desa --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Desa/Kelurahan</label>
                            <input type="text" 
                                   name="village" 
                                   value="{{ old('village') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Nama desa/kelurahan (opsional)">
                        </div>

                        {{-- lokasi detail --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Detail Lokasi</label>
                            <textarea name="detailed_location" 
                                      rows="2" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Detail alamat atau petunjuk lokasi (opsional)">{{ old('detailed_location') }}</textarea>
                        </div>

                        {{-- kategori SDG --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori SDG * (Pilih minimal 1)</label>
                            <div class="grid grid-cols-3 gap-3">
                                @for($i = 1; $i <= 17; $i++)
                                <label class="flex items-center gap-2 p-3 border border-gray-300 rounded-lg hover:bg-blue-50 cursor-pointer transition-colors">
                                    <input type="checkbox" 
                                           name="sdg_categories[]" 
                                           value="{{ $i }}"
                                           class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-500">
                                    <span class="text-sm">SDG {{ $i }}</span>
                                </label>
                                @endfor
                            </div>
                            @error('sdg_categories')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- upload images --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Foto Dokumentasi (Maks 5 foto)</label>
                            <input type="file" 
                                   name="images[]" 
                                   multiple 
                                   accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB per file</p>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <button type="button" onclick="prevStep(1)" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                            Kembali
                        </button>
                        <button type="button" onclick="nextStep(3)" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            Lanjut
                        </button>
                    </div>
                </div>
            </div>

            {{-- step 3: requirements --}}
            <div class="wizard-step hidden" id="step-3">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Requirements</h2>

                    <div class="space-y-4">
                        {{-- jumlah mahasiswa --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah Mahasiswa Dibutuhkan *</label>
                            <input type="number" 
                                   name="required_students" 
                                   value="{{ old('required_students', 1) }}"
                                   min="1"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        {{-- skill yang dibutuhkan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Skill yang Dibutuhkan * (Pisahkan dengan Enter)</label>
                            <div id="skills-container" class="space-y-2">
                                <input type="text" 
                                       name="required_skills[]" 
                                       placeholder="Contoh: Komunikasi, Pengolahan Data"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <button type="button" onclick="addSkillField()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-semibold">
                                + Tambah Skill
                            </button>
                        </div>

                        {{-- jurusan yang dibutuhkan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jurusan yang Dibutuhkan (Opsional)</label>
                            <div id="majors-container" class="space-y-2">
                                <input type="text" 
                                       name="required_majors[]" 
                                       placeholder="Contoh: Teknik Lingkungan, Kesehatan Masyarakat"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                                <option value="beginner" {{ old('difficulty_level') == 'beginner' ? 'selected' : '' }}>Pemula</option>
                                <option value="intermediate" {{ old('difficulty_level', 'intermediate') == 'intermediate' ? 'selected' : '' }}>Menengah</option>
                                <option value="advanced" {{ old('difficulty_level') == 'advanced' ? 'selected' : '' }}>Lanjutan</option>
                            </select>
                        </div>

                        {{-- expected outcomes --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Expected Outcomes</label>
                            <textarea name="expected_outcomes" 
                                      rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Hasil yang diharapkan dari proyek ini">{{ old('expected_outcomes') }}</textarea>
                        </div>

                        {{-- deliverables --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Deliverables (Opsional)</label>
                            <div id="deliverables-container" class="space-y-2">
                                <input type="text" 
                                       name="deliverables[]" 
                                       placeholder="Contoh: Laporan Akhir, Dokumentasi Foto"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <button type="button" onclick="addDeliverableField()" class="mt-2 text-blue-600 hover:text-blue-700 text-sm font-semibold">
                                + Tambah Deliverable
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <button type="button" onclick="prevStep(2)" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                            Kembali
                        </button>
                        <button type="button" onclick="nextStep(4)" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                            Lanjut
                        </button>
                    </div>
                </div>
            </div>

            {{-- step 4: timeline & fasilitas --}}
            <div class="wizard-step hidden" id="step-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Timeline & Fasilitas</h2>

                    <div class="space-y-4">
                        {{-- tanggal mulai --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Mulai *</label>
                            <input type="date" 
                                   name="start_date" 
                                   value="{{ old('start_date') }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        {{-- tanggal selesai --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Selesai *</label>
                            <input type="date" 
                                   name="end_date" 
                                   value="{{ old('end_date') }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        {{-- deadline aplikasi --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Deadline Aplikasi *</label>
                            <input type="date" 
                                   name="application_deadline" 
                                   value="{{ old('application_deadline') }}"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        {{-- fasilitas yang disediakan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Fasilitas yang Disediakan (Opsional)</label>
                            <div id="facilities-container" class="space-y-2">
                                <input type="text" 
                                       name="facilities_provided[]" 
                                       placeholder="Contoh: Akomodasi, Transportasi, Makan"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft (Belum Dipublikasikan)</option>
                                <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>Publikasikan Sekarang</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih "Draft" untuk menyimpan terlebih dahulu, atau "Publikasikan" untuk langsung membuka aplikasi</p>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <button type="button" onclick="prevStep(3)" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                            Kembali
                        </button>
                        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                            Simpan Masalah
                        </button>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

<script>
// wizard navigation
function nextStep(step) {
    document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('hidden'));
    document.getElementById('step-' + step).classList.remove('hidden');
    updateProgress(step);
}

function prevStep(step) {
    document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('hidden'));
    document.getElementById('step-' + step).classList.remove('hidden');
    updateProgress(step);
}

function updateProgress(currentStep) {
    for (let i = 1; i <= 4; i++) {
        const indicator = document.getElementById('step-' + i + '-indicator');
        const circle = indicator.querySelector('div:first-child');
        const text = indicator.querySelector('p');
        
        if (i < currentStep) {
            circle.className = 'w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center font-bold';
            text.className = 'text-sm font-semibold text-gray-900';
            if (i < 4) {
                document.getElementById('line-' + i).className = 'w-16 h-1 bg-green-600';
            }
        } else if (i === currentStep) {
            circle.className = 'w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold';
            text.className = 'text-sm font-semibold text-gray-900';
        } else {
            circle.className = 'w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold';
            text.className = 'text-sm font-semibold text-gray-600';
            if (i > 1) {
                document.getElementById('line-' + (i - 1)).className = 'w-16 h-1 bg-gray-300';
            }
        }
    }
}

// dynamic fields
function addSkillField() {
    const container = document.getElementById('skills-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'required_skills[]';
    input.placeholder = 'Skill lainnya';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

function addMajorField() {
    const container = document.getElementById('majors-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'required_majors[]';
    input.placeholder = 'Jurusan lainnya';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

function addDeliverableField() {
    const container = document.getElementById('deliverables-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'deliverables[]';
    input.placeholder = 'Deliverable lainnya';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

function addFacilityField() {
    const container = document.getElementById('facilities-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'facilities_provided[]';
    input.placeholder = 'Fasilitas lainnya';
    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent';
    container.appendChild(input);
}

// load regencies berdasarkan provinsi
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
    } else {
        regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
    }
});
</script>
@endsection