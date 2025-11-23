@extends('layouts.app')

@section('title', 'Edit Profil Perusahaan')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50" x-data="profileEditor()">
    <!-- Header Section -->
    <div class="relative bg-cover bg-center text-white py-12 overflow-hidden" style="background-image: url('{{ asset('company-profile.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex items-center gap-4 mb-4 fade-in-up">
                <a href="{{ route('company.profile.index') }}"
                   class="text-white hover:text-blue-100 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold" style="font-family: 'Space Grotesk', sans-serif;">
                    Edit Profil Perusahaan
                </h1>
            </div>
            <p class="text-blue-100 fade-in-up" style="animation-delay: 0.1s;">
                Perbarui informasi perusahaan Anda untuk menarik talenta terbaik
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
        <form action="{{ route('company.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Informasi Dasar
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Perusahaan <span class="text-red-500">*</span></label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name', $company->name) }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tagline</label>
                                <input type="text"
                                       name="tagline"
                                       value="{{ old('tagline', $company->tagline) }}"
                                       placeholder="Tagline singkat tentang perusahaan Anda"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Industri</label>
                                <input type="text"
                                       name="industry"
                                       value="{{ old('industry', $company->industry) }}"
                                       placeholder="cth: Teknologi, Kesehatan"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ukuran Perusahaan</label>
                                <select name="company_size"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    <option value="">Pilih ukuran</option>
                                    <option value="1-10" {{ old('company_size', $company->company_size) == '1-10' ? 'selected' : '' }}>1-10</option>
                                    <option value="11-50" {{ old('company_size', $company->company_size) == '11-50' ? 'selected' : '' }}>11-50</option>
                                    <option value="51-200" {{ old('company_size', $company->company_size) == '51-200' ? 'selected' : '' }}>51-200</option>
                                    <option value="201-500" {{ old('company_size', $company->company_size) == '201-500' ? 'selected' : '' }}>201-500</option>
                                    <option value="501-1000" {{ old('company_size', $company->company_size) == '501-1000' ? 'selected' : '' }}>501-1000</option>
                                    <option value="1000+" {{ old('company_size', $company->company_size) == '1000+' ? 'selected' : '' }}>1000+</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Perusahaan</label>
                                <textarea name="description"
                                          rows="5"
                                          placeholder="Ceritakan tentang perusahaan Anda, misi, dan nilai-nilai..."
                                          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none">{{ old('description', $company->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.1s;">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Informasi Kontak
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email"
                                       name="email"
                                       value="{{ old('email', $company->email) }}"
                                       placeholder="contact@company.com"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                                <input type="tel"
                                       name="phone"
                                       value="{{ old('phone', $company->phone) }}"
                                       placeholder="+62 xxx xxxx xxxx"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                                <input type="url"
                                       name="website"
                                       value="{{ old('website', $company->website) }}"
                                       placeholder="https://company.com"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                                <input type="text"
                                       name="location"
                                       value="{{ old('location', $company->location) }}"
                                       placeholder="Kota, Negara"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                                <input type="text"
                                       name="postal_code"
                                       value="{{ old('postal_code', $company->postal_code) }}"
                                       placeholder="12345"
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                                <textarea name="address"
                                          rows="3"
                                          placeholder="Alamat lengkap perusahaan..."
                                          class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all resize-none">{{ old('address', $company->address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.2s;">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            Media Sosial
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn</label>
                                <input type="url"
                                       name="linkedin"
                                       value="{{ old('linkedin', $company->linkedin) }}"
                                       placeholder="https://linkedin.com/company/..."
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Twitter</label>
                                <input type="url"
                                       name="twitter"
                                       value="{{ old('twitter', $company->twitter) }}"
                                       placeholder="https://twitter.com/..."
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Facebook</label>
                                <input type="url"
                                       name="facebook"
                                       value="{{ old('facebook', $company->facebook) }}"
                                       placeholder="https://facebook.com/..."
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Instagram</label>
                                <input type="url"
                                       name="instagram"
                                       value="{{ old('instagram', $company->instagram) }}"
                                       placeholder="https://instagram.com/..."
                                       class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Logo & Actions -->
                <div class="space-y-6">
                    <!-- Logo Upload -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Logo Perusahaan</h3>

                        <!-- Current Logo -->
                        <div class="mb-4 flex justify-center">
                            @if($company->logo)
                                <div x-show="!logoPreview" class="relative">
                                    <img src="{{ $company->logo_url }}"
                                         alt="{{ $company->name }}"
                                         crossorigin="anonymous"
                                         class="w-32 h-32 rounded-2xl object-cover border-2 border-gray-200">
                                    <button type="button"
                                            @click="deleteLogo()"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <div x-show="!logoPreview" class="w-32 h-32 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <span class="text-white text-4xl font-bold">{{ substr($company->name, 0, 1) }}</span>
                                </div>
                            @endif

                            <!-- Preview -->
                            <div x-show="logoPreview" x-cloak>
                                <img :src="logoPreview"
                                     alt="Preview"
                                     class="w-32 h-32 rounded-2xl object-cover border-2 border-gray-200">
                            </div>
                        </div>

                        <!-- Upload Button -->
                        <div class="space-y-3">
                            <label class="block">
                                <input type="file"
                                       name="logo"
                                       accept="image/*"
                                       @change="previewLogo($event)"
                                       class="hidden">
                                <span class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors duration-200 cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Upload Logo Baru
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 text-center">Format: JPG, PNG (Max 2MB)</p>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 fade-in-up gpu-accelerate" style="animation-delay: 0.1s;">
                        <div class="space-y-3">
                            <button type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 font-semibold hover-lift">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('company.profile.index') }}"
                               class="w-full flex items-center justify-center gap-2 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-300">
                                Batal
                            </a>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white fade-in-up gpu-accelerate" style="animation-delay: 0.2s;">
                        <h3 class="text-lg font-bold mb-3">Tips Profil</h3>
                        <ul class="space-y-2 text-sm text-purple-100">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Gunakan logo berkualitas tinggi
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Lengkapi semua informasi kontak
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Tulis deskripsi yang menarik
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Tambahkan link media sosial
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function profileEditor() {
    return {
        logoPreview: null,

        previewLogo(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.logoPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        async deleteLogo() {
            if (!confirm('Apakah Anda yakin ingin menghapus logo?')) {
                return;
            }

            try {
                const response = await fetch('{{ route("company.profile.delete-logo") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Gagal menghapus logo');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }

/* animasi fade in up */
.fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translate3d(0, 20px, 0);
    }
    to {
        opacity: 1;
        transform: translate3d(0, 0, 0);
    }
}

/* GPU acceleration untuk performa smooth */
.gpu-accelerate {
    transform: translateZ(0);
    will-change: transform, opacity;
    backface-visibility: hidden;
}

/* hover effect untuk lift */
.hover-lift {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translate3d(0, -4px, 0);
    box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.2);
}

/* reduced motion support untuk aksesibilitas */
@media (prefers-reduced-motion: reduce) {
    .fade-in-up {
        animation: none;
        opacity: 1;
    }

    .hover-lift:hover {
        transform: none;
    }
}
</style>
@endsection
