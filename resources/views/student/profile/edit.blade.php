@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- header section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Profil</h1>
        <p class="text-gray-600 mt-2">Perbarui informasi profil dan password Anda</p>
    </div>

    <!-- alert success/error -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="space-y-6">
        <!-- ========================================================== -->
        <!-- FORM 1: UNTUK UPDATE PROFIL                                 -->
        <!-- ========================================================== -->
        <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden edit-container">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <h2 class="text-xl font-bold text-gray-900">Informasi Profil</h2>

                <!-- foto profil -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                    <div class="flex items-center space-x-4">
                        <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-200">
                            @if(Auth::user()->profile_photo_url)
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="Profile" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div>
                            <input type="file" 
                                   id="profile_photo" 
                                   name="profile_photo" 
                                   accept="image/jpeg,image/jpg,image/png"
                                   class="hidden">
                            <button type="button" 
                                    onclick="document.getElementById('profile_photo').click()"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Pilih Foto
                            </button>
                            <p class="text-xs text-gray-500 mt-1">JPG, JPEG atau PNG. Maksimal 2MB</p>
                        </div>
                    </div>
                    @error('profile_photo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- nama depan dan belakang -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Depan <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name', Auth::user()->first_name) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                               required>
                        @error('first_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Belakang <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ old('last_name', Auth::user()->last_name) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                               required>
                        @error('last_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', Auth::user()->email) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- universitas -->
                <div>
                    <label for="university_id" class="block text-sm font-medium text-gray-700 mb-2">Universitas <span class="text-red-500">*</span></label>
                    <select id="university_id" 
                            name="university_id" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            required>
                        <option value="">Pilih Universitas</option>
                        @foreach($universities as $university)
                            <option value="{{ $university->id }}" 
                                    {{ old('university_id', Auth::user()->student->university_id) == $university->id ? 'selected' : '' }}>
                                {{ $university->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('university_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- jurusan -->
                <div>
                    <label for="major" class="block text-sm font-medium text-gray-700 mb-2">Jurusan <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="major" 
                           name="major" 
                           value="{{ old('major', Auth::user()->student->major) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           required>
                    @error('major')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- nim dan semester -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nim" class="block text-sm font-medium text-gray-700 mb-2">NIM <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="nim" 
                               name="nim" 
                               value="{{ old('nim', Auth::user()->student->nim) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                               required>
                        @error('nim')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Semester <span class="text-red-500">*</span></label>
                        <select id="semester" 
                                name="semester" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                required>
                            <option value="">Pilih Semester</option>
                            @for($i = 1; $i <= 14; $i++)
                                <option value="{{ $i }}" 
                                        {{ old('semester', Auth::user()->student->semester) == $i ? 'selected' : '' }}>
                                    Semester {{ $i }}
                                </option>
                            @endfor
                        </select>
                        @error('semester')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- nomor whatsapp -->
                <div>
                    <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="whatsapp_number" 
                           name="whatsapp_number" 
                           value="{{ old('whatsapp_number', Auth::user()->student->whatsapp_number) }}"
                           placeholder="Contoh: 081234567890"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           required>
                    @error('whatsapp_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                    <textarea id="bio"
                              name="bio"
                              rows="4"
                              maxlength="500"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                              placeholder="Ceritakan tentang diri Anda...">{{ old('bio', Auth::user()->student->bio) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Maksimal 500 karakter</p>
                    @error('bio')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- cerita & pengalaman -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Cerita & Pengalaman
                        <span class="text-gray-500 font-normal">(Opsional)</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">Bagikan cerita dan pengalaman Anda dalam menjalani proyek-proyek KKN. Anda bisa menambahkan lebih dari satu cerita!</p>
                    <div id="stories-container" class="space-y-3">
                        @php
                            $stories = old('stories', json_decode(Auth::user()->student->stories ?? '[]', true));
                        @endphp
                        @foreach($stories as $index => $story)
                            <div class="story-item border border-gray-200 rounded-lg p-3">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-sm font-medium text-gray-700">Cerita {{ $index + 1 }}</span>
                                    <button type="button"
                                            onclick="removeStory(this)"
                                            class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                        Hapus
                                    </button>
                                </div>
                                <textarea name="stories[]"
                                          rows="4"
                                          maxlength="2000"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                                          placeholder="Contoh: Saat mengerjakan proyek desa wisata, saya belajar betapa pentingnya mendengarkan aspirasi masyarakat lokal...">{{ $story }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Maksimal 2000 karakter per cerita</p>
                            </div>
                        @endforeach
                    </div>
                    <button type="button"
                            onclick="addStory()"
                            class="mt-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        + Tambah Cerita
                    </button>
                    @error('stories')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- skills -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Skills</label>
                    <div id="skills-container" class="space-y-2">
                        @php
                            $skills = old('skills', json_decode(Auth::user()->student->skills ?? '[]', true));
                        @endphp
                        @foreach($skills as $index => $skill)
                            <div class="flex gap-2 skill-item">
                                <input type="text" 
                                       name="skills[]" 
                                       value="{{ $skill }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                       placeholder="Contoh: Laravel, PHP, JavaScript">
                                <button type="button" 
                                        onclick="removeSkill(this)"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    Hapus
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" 
                            onclick="addSkill()"
                            class="mt-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        + Tambah Skill
                    </button>
                </div>

                <!-- interests -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Interests</label>
                    <div id="interests-container" class="space-y-2">
                        @php
                            $interests = old('interests', json_decode(Auth::user()->student->interests ?? '[]', true));
                        @endphp
                        @foreach($interests as $index => $interest)
                            <div class="flex gap-2 interest-item">
                                <input type="text" 
                                       name="interests[]" 
                                       value="{{ $interest }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                       placeholder="Contoh: Web Development, AI, Data Science">
                                <button type="button" 
                                        onclick="removeInterest(this)"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    Hapus
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" 
                            onclick="addInterest()"
                            class="mt-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        + Tambah Interest
                    </button>
                </div>
            </div>

            <!-- tombol aksi -->
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t border-gray-200">
                <a href="{{ route('student.profile.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <!-- ========================================================== -->
        <!-- FORM 2: UNTUK UPDATE PASSWORD (TERPISAH)                   -->
        <!-- ========================================================== -->
        <form action="{{ route('student.profile.password.update') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden edit-container mt-6">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <h2 class="text-xl font-bold text-gray-900">Ubah Password</h2>

            <!-- password lama -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Password Lama <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" 
                        id="current_password" 
                        name="current_password" 
                        class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        required>
                    <button type="button" 
                            onclick="togglePassword('current_password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors">
                        <svg id="current_password_icon_hide" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="current_password_icon_show" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('current_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- password baru -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        required>
                    <button type="button" 
                            onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors">
                        <svg id="password_icon_hide" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="password_icon_show" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter</p>
            </div>

            <!-- konfirmasi password baru -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                        required>
                    <button type="button" 
                            onclick="togglePassword('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors">
                        <svg id="password_confirmation_icon_hide" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="password_confirmation_icon_show" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- tombol aksi -->
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-end border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Ubah Password
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// fungsi toggle show/hide password
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const iconHide = document.getElementById(fieldId + '_icon_hide');
    const iconShow = document.getElementById(fieldId + '_icon_show');
    
    if (field.type === 'password') {
        field.type = 'text';
        iconHide.classList.add('hidden');
        iconShow.classList.remove('hidden');
    } else {
        field.type = 'password';
        iconHide.classList.remove('hidden');
        iconShow.classList.add('hidden');
    }
}

// fungsi untuk tambah skill
function addSkill() {
    const container = document.getElementById('skills-container');
    const newSkill = document.createElement('div');
    newSkill.className = 'flex gap-2 skill-item';
    newSkill.innerHTML = `
        <input type="text" 
               name="skills[]" 
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
               placeholder="Contoh: Laravel, PHP, JavaScript">
        <button type="button" 
                onclick="removeSkill(this)"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            Hapus
        </button>
    `;
    container.appendChild(newSkill);
}

// fungsi untuk hapus skill
function removeSkill(button) {
    button.closest('.skill-item').remove();
}

// fungsi untuk tambah interest
function addInterest() {
    const container = document.getElementById('interests-container');
    const newInterest = document.createElement('div');
    newInterest.className = 'flex gap-2 interest-item';
    newInterest.innerHTML = `
        <input type="text" 
               name="interests[]" 
               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
               placeholder="Contoh: Web Development, AI, Data Science">
        <button type="button" 
                onclick="removeInterest(this)"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            Hapus
        </button>
    `;
    container.appendChild(newInterest);
}

// fungsi untuk hapus interest
function removeInterest(button) {
    button.closest('.interest-item').remove();
}

// fungsi untuk tambah cerita & pengalaman
function addStory() {
    const container = document.getElementById('stories-container');
    const storyCount = container.querySelectorAll('.story-item').length + 1;
    const newStory = document.createElement('div');
    newStory.className = 'story-item border border-gray-200 rounded-lg p-3';
    newStory.innerHTML = `
        <div class="flex justify-between items-start mb-2">
            <span class="text-sm font-medium text-gray-700">Cerita ${storyCount}</span>
            <button type="button"
                    onclick="removeStory(this)"
                    class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                Hapus
            </button>
        </div>
        <textarea name="stories[]"
                  rows="4"
                  maxlength="2000"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none"
                  placeholder="Contoh: Saat mengerjakan proyek desa wisata, saya belajar betapa pentingnya mendengarkan aspirasi masyarakat lokal..."></textarea>
        <p class="text-xs text-gray-500 mt-1">Maksimal 2000 karakter per cerita</p>
    `;
    container.appendChild(newStory);
    updateStoryNumbers();
}

// fungsi untuk hapus cerita & pengalaman
function removeStory(button) {
    button.closest('.story-item').remove();
    updateStoryNumbers();
}

// fungsi untuk update nomor cerita setelah add/remove
function updateStoryNumbers() {
    const stories = document.querySelectorAll('.story-item');
    stories.forEach((story, index) => {
        const label = story.querySelector('span.text-sm');
        if (label) {
            label.textContent = `Cerita ${index + 1}`;
        }
    });
}

// preview foto profil
document.getElementById('profile_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.querySelector('.w-20.h-20 img');
            if (img) {
                img.src = e.target.result;
            } else {
                const container = document.querySelector('.w-20.h-20');
                container.innerHTML = `<img src="${e.target.result}" alt="Profile" class="w-full h-full object-cover">`;
            }
        };
        reader.readAsDataURL(file);
    }
});

// smooth scroll behavior sudah dihandle di CSS
document.addEventListener('DOMContentLoaded', function() {
    // auto hide success/error messages after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        });
    }, 5000);
});
</script>
@endpush
@endsection