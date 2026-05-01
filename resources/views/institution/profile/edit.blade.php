@extends('layouts.app')

@section('title', 'Edit Profil Instansi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- header --}}
        <div class="mb-8">
            <a href="{{ route('institution.profile.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2 mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Edit Profil Instansi</h1>
            <p class="text-gray-600 mt-1">Perbarui Informasi Profil Instansi Anda</p>
        </div>

        {{-- pesan sukses/error --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        {{-- form edit profile --}}
        {{-- PERBAIKAN BUG: tambahkan enctype="multipart/form-data" untuk upload file --}}
        <form method="POST" action="{{ route('institution.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- logo section --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Logo Instansi</h2>
                
                <div class="flex items-start gap-6">
                    {{-- preview logo --}}
                    <div class="flex-shrink-0">
                        @if($institution->logo_path)
                        <img id="logo-preview" 
                             src="{{ $institution->getLogoUrl() }}" 
                             alt="Logo" 
                             class="w-32 h-32 rounded-lg object-cover border-2 border-gray-200">
                        @else
                        <div id="logo-preview" class="w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-4xl font-bold">{{ substr($institution->name, 0, 1) }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Logo Baru</label>
                        <input type="file" 
                               name="logo" 
                               id="logo-input"
                               accept="image/jpeg,image/jpg,image/png"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               onchange="previewLogo(event)">
                        <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG. Maksimal 2MB</p>
                        @error('logo')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- informasi dasar --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Informasi Dasar</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- nama instansi --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Instansi *</label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $institution->name) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- jenis instansi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jenis Instansi *</label>
                        <select name="type" 
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Jenis</option>
                            @foreach($institutionTypes as $key => $value)
                            <option value="{{ $key }}" {{ old('type', $institution->type) == $key ? 'selected' : '' }}>
                                {{ ucwords($value) }}
                            </option>
                            @endforeach
                        </select>
                        @error('type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- email --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- lokasi --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Lokasi</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- provinsi --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi *</label>
                        <select name="province_id" 
                                id="province-select"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                            <option value="{{ $province->id }}" {{ old('province_id', $institution->province_id) == $province->id ? 'selected' : '' }}>
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
                                id="regency-select"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Kabupaten/Kota</option>
                            @foreach($regencies as $regency)
                            <option value="{{ $regency->id }}" {{ old('regency_id', $institution->regency_id) == $regency->id ? 'selected' : '' }}>
                                {{ $regency->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('regency_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- alamat lengkap --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap *</label>
                        <textarea name="address" 
                                  rows="3"
                                  required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('address', $institution->address) }}</textarea>
                        @error('address')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- telepon --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Telepon *</label>
                        <input type="tel" 
                               name="phone" 
                               value="{{ old('phone', $institution->phone) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('phone')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- website --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Website (Opsional)</label>
                        <input type="url" 
                               name="website" 
                               value="{{ old('website', $institution->website) }}"
                               placeholder="https://example.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('website')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- penanggung jawab --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Penanggung Jawab</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- nama PIC --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama PIC *</label>
                        <input type="text" 
                               name="pic_name" 
                               value="{{ old('pic_name', $institution->pic_name) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('pic_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- jabatan PIC --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jabatan *</label>
                        <input type="text" 
                               name="pic_position" 
                               value="{{ old('pic_position', $institution->pic_position) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('pic_position')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- deskripsi --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Deskripsi Instansi</h2>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea name="description" 
                              rows="5"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Ceritakan tentang instansi Anda...">{{ old('description', $institution->description) }}</textarea>
                    @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- action buttons --}}
            <div class="flex gap-4">
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Simpan Perubahan
                </button>
                <a href="{{ route('institution.profile.index') }}" 
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// preview logo saat dipilih
function previewLogo(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logo-preview');
            preview.innerHTML = `<img src="${e.target.result}" alt="Logo Preview" class="w-32 h-32 rounded-lg object-cover border-2 border-gray-200">`;
        }
        reader.readAsDataURL(file);
    }
}

// dynamic loading kabupaten/kota berdasarkan provinsi
document.getElementById('province-select').addEventListener('change', function() {
    const provinceId = this.value;
    const regencySelect = document.getElementById('regency-select');
    
    // reset regency select
    regencySelect.innerHTML = '<option value="">Loading...</option>';
    regencySelect.disabled = true;
    
    if (!provinceId) {
        regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
        return;
    }
    
    // fetch regencies dari API
    fetch(`/api/regencies/${provinceId}`)
        .then(response => response.json())
        .then(data => {
            regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
            
            data.forEach(regency => {
                const option = document.createElement('option');
                option.value = regency.id;
                option.textContent = regency.name;
                regencySelect.appendChild(option);
            });
            
            regencySelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading regencies:', error);
            regencySelect.innerHTML = '<option value="">Error Loading Data</option>';
        });
});
</script>
@endpush
@endsection