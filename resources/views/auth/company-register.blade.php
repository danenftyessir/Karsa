{{-- resources/views/auth/company-register.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Sebagai Company - Karsa</title>

    @vite(['resources/css/app.css'])

    {{-- tambahkan Alpine.js CDN untuk membuat dropdown dinamis bekerja --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <style>
        /* background image dengan opacity */
        .register-container.company-register {
            position: relative;
            min-height: 100vh;
        }

        .register-container.company-register::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset('reg-com-background.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.35;
            z-index: 0;
            pointer-events: none;
        }

        .register-container.company-register > * {
            position: relative;
            z-index: 1;
        }

        /* Form Styling */
        .form-field-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-label.required::after {
            content: ' *';
            color: #ef4444;
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            padding-right: 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            transition: all 0.2s;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            ring: 2px;
            ring-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-input-icon {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.25rem;
            height: 1.25rem;
            color: #9ca3af;
            pointer-events: none;
        }

        .error-message {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #ef4444;
        }

        .error-message.hidden {
            display: none;
        }

        .error-message svg {
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 2rem;
            font-weight: 600;
            font-size: 0.9375rem;
            border-radius: 0.5rem;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        .btn-primary:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: white;
            color: #6b7280;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background: #f3f4f6;
            border-color: #3b82f6;
            color: #3b82f6;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            padding: 0;
            display: flex;
            align-items: center;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #3b82f6;
        }

        .password-toggle svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* file upload styling */
        .file-upload-container {
            position: relative;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border: 2px dashed #d1d5db;
            border-radius: 0.5rem;
            background: #f9fafb;
            cursor: pointer;
            transition: all 0.2s;
        }

        .file-upload-label:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .file-upload-input {
            display: none;
        }

        .file-preview {
            margin-top: 1rem;
            padding: 0.75rem;
            background: #f9fafb;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .file-preview img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 0.375rem;
        }

        /* Loading State */
        .spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="register-container company-register">
    <!-- Navigation -->
    <div class="absolute top-6 left-6 right-6 flex items-center justify-between z-10">
        <a href="{{ route('register') }}" class="inline-flex items-center text-gray-800 hover:text-blue-600 transition-colors group">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-medium">Kembali</span>
        </a>
        <div class="flex items-center space-x-6">
            <a href="{{ route('contact') }}" class="text-gray-800 hover:text-blue-600 font-medium transition-colors">Contact</a>
            <a href="{{ route('about') }}" class="text-gray-800 hover:text-blue-600 font-medium transition-colors">About</a>
        </div>
    </div>

    <div class="flex items-center justify-center min-h-screen p-8 pt-24">
        <div class="w-full max-w-3xl">
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <img src="{{ asset('karsa-logo.png') }}"
                     alt="Karsa - Karya Untuk Bangsa"
                     class="h-20 w-auto transform hover:scale-105 transition-transform">
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Daftar Sebagai Company</h1>
                    <p class="text-gray-600">Lengkapi informasi perusahaan untuk mulai mencari talent terbaik</p>
                </div>

                <!-- Error/Success Messages -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-red-800 mb-1">Terdapat kesalahan pada form:</h3>
                                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Validation Alert (hidden by default) -->
                <div id="validation-alert" class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg hidden">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-yellow-800 mb-1">Mohon lengkapi semua field yang wajib diisi!</h3>
                            <p class="text-sm text-yellow-700">Field dengan tanda <span class="text-red-600 font-bold">*</span> wajib diisi.</p>
                        </div>
                    </div>
                </div>

                <!-- Registration Form -->
                <form method="POST"
                      action="{{ route('register.company.submit') }}"
                      enctype="multipart/form-data"
                      x-data="companyRegisterForm()"
                      novalidate>
                    @csrf

                    <!-- Company Information Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            Informasi Perusahaan
                        </h3>

                        <!-- Company Name -->
                        <div class="form-field-group">
                            <label for="company_name" class="form-label required">Nama Perusahaan</label>
                            <div class="form-input-wrapper">
                                <input type="text"
                                       id="company_name"
                                       name="company_name"
                                       class="form-input @error('company_name') border-red-500 @enderror"
                                       value="{{ old('company_name') }}"
                                       placeholder="PT Teknologi Nusantara"
                                       required>
                                <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            @error('company_name')
                                <div class="error-message">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Industry & Company Size -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Industry -->
                            <div class="form-field-group">
                                <label for="industry" class="form-label required">Industri</label>
                                <div class="form-input-wrapper">
                                    <select id="industry"
                                            name="industry"
                                            class="form-input @error('industry') border-red-500 @enderror"
                                            required>
                                        <option value="">Pilih Industri</option>
                                        <option value="Teknologi Informasi" {{ old('industry') == 'Teknologi Informasi' ? 'selected' : '' }}>Teknologi Informasi</option>
                                        <option value="Keuangan" {{ old('industry') == 'Keuangan' ? 'selected' : '' }}>Keuangan</option>
                                        <option value="E-commerce" {{ old('industry') == 'E-commerce' ? 'selected' : '' }}>E-commerce</option>
                                        <option value="Pendidikan" {{ old('industry') == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                                        <option value="Kesehatan" {{ old('industry') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                        <option value="Manufaktur" {{ old('industry') == 'Manufaktur' ? 'selected' : '' }}>Manufaktur</option>
                                        <option value="Retail" {{ old('industry') == 'Retail' ? 'selected' : '' }}>Retail</option>
                                        <option value="Konstruksi" {{ old('industry') == 'Konstruksi' ? 'selected' : '' }}>Konstruksi</option>
                                        <option value="Media & Hiburan" {{ old('industry') == 'Media & Hiburan' ? 'selected' : '' }}>Media & Hiburan</option>
                                        <option value="Transportasi" {{ old('industry') == 'Transportasi' ? 'selected' : '' }}>Transportasi</option>
                                        <option value="Lainnya" {{ old('industry') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                @error('industry')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <!-- Company Size -->
                            <div class="form-field-group">
                                <label for="company_size" class="form-label required">Ukuran Perusahaan</label>
                                <div class="form-input-wrapper">
                                    <select id="company_size"
                                            name="company_size"
                                            class="form-input @error('company_size') border-red-500 @enderror"
                                            required>
                                        <option value="">Pilih Ukuran</option>
                                        <option value="1-10" {{ old('company_size') == '1-10' ? 'selected' : '' }}>1-10 karyawan</option>
                                        <option value="11-50" {{ old('company_size') == '11-50' ? 'selected' : '' }}>11-50 karyawan</option>
                                        <option value="51-200" {{ old('company_size') == '51-200' ? 'selected' : '' }}>51-200 karyawan</option>
                                        <option value="201-500" {{ old('company_size') == '201-500' ? 'selected' : '' }}>201-500 karyawan</option>
                                        <option value="501-1000" {{ old('company_size') == '501-1000' ? 'selected' : '' }}>501-1000 karyawan</option>
                                        <option value="1000+" {{ old('company_size') == '1000+' ? 'selected' : '' }}>1000+ karyawan</option>
                                    </select>
                                </div>
                                @error('company_size')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Founded Year & Website -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Founded Year -->
                            <div class="form-field-group">
                                <label for="founded_year" class="form-label">Tahun Didirikan</label>
                                <div class="form-input-wrapper">
                                    <input type="number"
                                           id="founded_year"
                                           name="founded_year"
                                           class="form-input @error('founded_year') border-red-500 @enderror"
                                           value="{{ old('founded_year') }}"
                                           placeholder="2020"
                                           min="1800"
                                           max="{{ date('Y') }}">
                                    <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @error('founded_year')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <!-- Website -->
                            <div class="form-field-group">
                                <label for="website" class="form-label">Website</label>
                                <div class="form-input-wrapper">
                                    <input type="url"
                                           id="website"
                                           name="website"
                                           class="form-input @error('website') border-red-500 @enderror"
                                           value="{{ old('website') }}"
                                           placeholder="https://www.perusahaan.com">
                                    <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                </div>
                                @error('website')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-field-group">
                            <label for="description" class="form-label">Deskripsi Perusahaan</label>
                            <textarea id="description"
                                      name="description"
                                      rows="4"
                                      class="form-input @error('description') border-red-500 @enderror"
                                      placeholder="Ceritakan tentang perusahaan Anda...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="error-message">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Logo Upload -->
                        <div class="form-field-group">
                            <label class="form-label">Logo Perusahaan</label>
                            <div class="file-upload-container">
                                <label for="logo" class="file-upload-label">
                                    <div class="text-center">
                                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm text-gray-600"><span class="font-semibold text-blue-600">Klik untuk upload</span> atau drag & drop</p>
                                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, JPEG, WEBP (max. 2MB)</p>
                                    </div>
                                </label>
                                <input type="file"
                                       id="logo"
                                       name="logo"
                                       class="file-upload-input"
                                       accept="image/png,image/jpeg,image/jpg,image/webp"
                                       @change="previewLogo">

                                <!-- Logo Preview -->
                                <div x-show="logoPreview" class="file-preview" x-cloak>
                                    <img :src="logoPreview" alt="Preview">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900" x-text="logoName"></p>
                                        <p class="text-xs text-gray-500" x-text="logoSize"></p>
                                    </div>
                                    <button type="button" @click="removeLogo" class="text-red-600 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @error('logo')
                                <div class="error-message">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Location Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            Lokasi
                        </h3>

                        <!-- Address -->
                        <div class="form-field-group">
                            <label for="address" class="form-label">Alamat Lengkap</label>
                            <textarea id="address"
                                      name="address"
                                      rows="3"
                                      class="form-input @error('address') border-red-500 @enderror"
                                      placeholder="Jl. Sudirman No. 123">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="error-message">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- City & Province -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- City -->
                            <div class="form-field-group">
                                <label for="city" class="form-label">Kota</label>
                                <div class="form-input-wrapper">
                                    <input type="text"
                                           id="city"
                                           name="city"
                                           class="form-input @error('city') border-red-500 @enderror"
                                           value="{{ old('city') }}"
                                           placeholder="Jakarta">
                                    <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                @error('city')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <!-- Province -->
                            <div class="form-field-group">
                                <label for="province_id" class="form-label">Provinsi</label>
                                <div class="form-input-wrapper">
                                    <select id="province_id"
                                            name="province_id"
                                            class="form-input @error('province_id') border-red-500 @enderror">
                                        <option value="">Pilih Provinsi</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                                {{ $province->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('province_id')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            Informasi Kontak
                        </h3>

                        <!-- Email & Phone -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Email -->
                            <div class="form-field-group">
                                <label for="email" class="form-label required">Email Perusahaan</label>
                                <div class="form-input-wrapper">
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           class="form-input @error('email') border-red-500 @enderror"
                                           value="{{ old('email') }}"
                                           placeholder="info@perusahaan.com"
                                           required>
                                    <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @error('email')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="form-field-group">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <div class="form-input-wrapper">
                                    <input type="tel"
                                           id="phone"
                                           name="phone"
                                           class="form-input @error('phone') border-red-500 @enderror"
                                           value="{{ old('phone') }}"
                                           placeholder="08123456789">
                                    <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                @error('phone')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- PIC Name & Position -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- PIC Name -->
                            <div class="form-field-group">
                                <label for="pic_name" class="form-label required">Nama Penanggung Jawab</label>
                                <div class="form-input-wrapper">
                                    <input type="text"
                                           id="pic_name"
                                           name="pic_name"
                                           class="form-input @error('pic_name') border-red-500 @enderror"
                                           value="{{ old('pic_name') }}"
                                           placeholder="John Doe"
                                           required>
                                    <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                @error('pic_name')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <!-- PIC Position -->
                            <div class="form-field-group">
                                <label for="pic_position" class="form-label required">Jabatan</label>
                                <div class="form-input-wrapper">
                                    <input type="text"
                                           id="pic_position"
                                           name="pic_position"
                                           class="form-input @error('pic_position') border-red-500 @enderror"
                                           value="{{ old('pic_position') }}"
                                           placeholder="HR Manager"
                                           required>
                                    <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @error('pic_position')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Account Section -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            Informasi Akun
                        </h3>

                        <!-- Username -->
                        <div class="form-field-group">
                            <label for="username" class="form-label required">Username</label>
                            <div class="form-input-wrapper">
                                <input type="text"
                                       id="username"
                                       name="username"
                                       class="form-input @error('username') border-red-500 @enderror"
                                       value="{{ old('username') }}"
                                       placeholder="company_username"
                                       required>
                                <svg class="form-input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Hanya huruf, angka, titik, underscore, dan strip</p>
                            @error('username')
                                <div class="error-message">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <!-- Password & Confirm Password -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Password -->
                            <div class="form-field-group">
                                <label for="password" class="form-label required">Password</label>
                                <div class="form-input-wrapper">
                                    <input :type="showPassword ? 'text' : 'password'"
                                           id="password"
                                           name="password"
                                           class="form-input @error('password') border-red-500 @enderror"
                                           placeholder="Minimal 8 karakter"
                                           required>
                                    <button type="button" class="password-toggle" @click="showPassword = !showPassword">
                                        <svg x-show="!showPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg x-show="showPassword" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Min. 8 karakter dengan huruf besar, kecil, angka, dan simbol</p>
                                @error('password')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-field-group">
                                <label for="password_confirmation" class="form-label required">Konfirmasi Password</label>
                                <div class="form-input-wrapper">
                                    <input :type="showPasswordConfirmation ? 'text' : 'password'"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           class="form-input @error('password_confirmation') border-red-500 @enderror"
                                           placeholder="Ulangi password"
                                           required>
                                    <button type="button" class="password-toggle" @click="showPasswordConfirmation = !showPasswordConfirmation">
                                        <svg x-show="!showPasswordConfirmation" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg x-show="showPasswordConfirmation" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <div class="error-message">
                                        <svg fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('register') }}" class="btn btn-secondary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali
                        </a>

                        <button type="submit"
                                class="btn btn-primary"
                                :disabled="isSubmitting">
                            <span x-show="!isSubmitting">
                                Daftar Sekarang
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </span>
                            <span x-show="isSubmitting" x-cloak class="flex items-center">
                                <span class="spinner mr-2"></span>
                                Mendaftar...
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Login Link -->
                <div class="mt-8 text-center">
                    <p class="text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline transition-colors">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function companyRegisterForm() {
            return {
                showPassword: false,
                showPasswordConfirmation: false,
                isSubmitting: false,
                logoPreview: null,
                logoName: '',
                logoSize: '',

                previewLogo(event) {
                    const file = event.target.files[0];
                    if (file) {
                        // Validate file size (2MB)
                        if (file.size > 2048 * 1024) {
                            alert('Ukuran file maksimal 2MB');
                            event.target.value = '';
                            return;
                        }

                        // Preview image
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.logoPreview = e.target.result;
                        };
                        reader.readAsDataURL(file);

                        this.logoName = file.name;
                        this.logoSize = this.formatFileSize(file.size);
                    }
                },

                removeLogo() {
                    this.logoPreview = null;
                    this.logoName = '';
                    this.logoSize = '';
                    document.getElementById('logo').value = '';
                },

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                }
            }
        }

        // Handle form submission with AJAX
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const validationAlert = document.getElementById('validation-alert');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Always prevent default, we'll use AJAX

                    // Hide validation alert first
                    if (validationAlert) {
                        validationAlert.classList.add('hidden');
                    }

                    // Remove all previous error borders
                    const previousErrors = form.querySelectorAll('.border-red-500');
                    previousErrors.forEach(el => el.classList.remove('border-red-500'));

                    // Check if form is valid
                    const requiredFields = form.querySelectorAll('[required]');
                    let firstInvalidField = null;
                    let hasInvalidFields = false;

                    requiredFields.forEach(field => {
                        if (!field.value || !field.validity.valid) {
                            field.classList.add('border-red-500');
                            hasInvalidFields = true;
                            if (!firstInvalidField) {
                                firstInvalidField = field;
                            }
                        }
                    });

                    if (hasInvalidFields) {
                        // Show validation alert
                        if (validationAlert) {
                            validationAlert.classList.remove('hidden');
                        }

                        // Scroll to validation alert
                        if (validationAlert) {
                            validationAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }

                        return false;
                    }

                    // Form is valid, submit via AJAX
                    submitFormViaAjax();
                });

                // Remove error styling when user starts typing
                const inputs = form.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    input.addEventListener('input', function() {
                        this.classList.remove('border-red-500');
                    });

                    input.addEventListener('change', function() {
                        this.classList.remove('border-red-500');
                    });
                });
            }

            function submitFormViaAjax() {
                const submitBtn = form.querySelector('button[type="submit"]');
                const formData = new FormData(form);

                // Disable submit button and show loading
                if (submitBtn) {
                    submitBtn.disabled = true;
                }

                // Get Alpine.js component to trigger loading state
                const alpineComponent = form.__x;
                if (alpineComponent && alpineComponent.$data) {
                    alpineComponent.$data.isSubmitting = true;
                }

                // Create abort controller for timeout
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 60000); // 60 seconds timeout

                // Submit form via fetch API
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    signal: controller.signal
                })
                .then(response => {
                    clearTimeout(timeoutId); // Clear timeout on successful response

                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        // Clone response to read text for debugging
                        return response.clone().text().then(text => {
                            console.log('Raw response text:', text);
                            console.log('Response length:', text.length);

                            try {
                                const data = JSON.parse(text);
                                return { data, status: response.status };
                            } catch (e) {
                                console.error('JSON parse error:', e);
                                console.error('Character at position 252:', text.charAt(252));
                                console.error('Text around position 252:', text.substring(240, 270));
                                throw new Error('Invalid JSON response from server. Please check browser console for details.');
                            }
                        });
                    } else {
                        // If not JSON, might be a redirect or HTML
                        // Consider it as success and redirect to home
                        window.location.href = '{{ route("home") }}';
                        return null;
                    }
                })
                .then(result => {
                    if (!result) return; // Already redirected

                    const { data, status } = result;

                    if (status >= 200 && status < 300) {
                        // Success
                        console.log('Registration successful:', data);

                        // Show success message if available
                        if (data.message) {
                            alert(data.message);
                        }

                        // Redirect to the URL provided in response
                        if (data.data && data.data.redirect_url) {
                            window.location.href = data.data.redirect_url;
                        } else if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            // Default redirect to home
                            window.location.href = '{{ route("home") }}';
                        }
                    } else {
                        // Error response
                        handleErrorResponse(data, submitBtn, alpineComponent);
                    }
                })
                .catch(error => {
                    clearTimeout(timeoutId); // Clear timeout on error
                    console.error('Registration error:', error);

                    // Re-enable submit button
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }
                    if (alpineComponent && alpineComponent.$data) {
                        alpineComponent.$data.isSubmitting = false;
                    }

                    // Show appropriate error message
                    let errorMessage = 'Terjadi kesalahan saat registrasi. Silakan coba lagi.';
                    if (error.name === 'AbortError') {
                        errorMessage = 'Request timeout. Data mungkin sudah tersimpan. Silakan cek email Anda atau coba login.';
                    }
                    alert(errorMessage);
                });
            }

            function handleErrorResponse(data, submitBtn, alpineComponent) {
                // Re-enable submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
                if (alpineComponent && alpineComponent.$data) {
                    alpineComponent.$data.isSubmitting = false;
                }

                // Show error message
                let errorMessage = 'Terjadi kesalahan saat registrasi. Silakan coba lagi.';
                if (data.message) {
                    errorMessage = data.message;
                } else if (data.error) {
                    errorMessage = data.error;
                }

                alert(errorMessage);

                // Scroll to top to show error
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
