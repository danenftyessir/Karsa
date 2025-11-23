@extends('layouts.app')

@section('title', 'Buat Lowongan Baru')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="jobWizard()">

    {{-- hero section --}}
    <div class="relative h-48 bg-cover bg-center" style="background-image: url('{{ asset('company3.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white px-4">
            <h1 class="text-2xl md:text-3xl font-bold mb-2 fade-in-up" style="font-family: 'Space Grotesk', sans-serif;">
                Buat Lowongan Baru
            </h1>
            <p class="text-sm md:text-base text-gray-300 max-w-xl fade-in-up" style="animation-delay: 0.1s;">
                Pandu rekrutmen Anda dengan deskripsi lowongan yang jelas dan berdampak.
            </p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Quick Actions & Auto-save Indicator --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 fade-in-up gpu-accelerate">
            <div class="flex items-center justify-between flex-wrap gap-3">
                {{-- Left: Auto-save indicator --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <div x-show="autoSaving" class="flex items-center gap-2 text-sm text-blue-600">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Menyimpan draft...</span>
                        </div>
                        <div x-show="!autoSaving && lastSaved" class="flex items-center gap-2 text-sm text-gray-500">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span x-text="'Draft tersimpan ' + lastSaved"></span>
                        </div>
                    </div>
                </div>

                {{-- Right: Quick actions --}}
                <div class="flex items-center gap-2">
                    <button type="button" @click="showTemplateManager = true"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-violet-700 bg-violet-50 rounded-lg hover:bg-violet-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                        </svg>
                        Templates
                    </button>
                    <button type="button" @click="showPreview = true"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Preview
                    </button>
                    <button type="button" @click="clearDraft()"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Draft
                    </button>
                </div>
            </div>
        </div>

        {{-- step indicator --}}
        <div class="mb-8 fade-in-up" style="animation-delay: 0.15s;">
            <div class="flex items-center justify-center">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex items-center">
                        {{-- step circle --}}
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300"
                                 :class="currentStep > index ? 'bg-violet-600 text-white' :
                                         currentStep === index ? 'bg-violet-600 text-white ring-4 ring-violet-200' :
                                         'bg-gray-200 text-gray-500'">
                                <span x-text="index + 1"></span>
                            </div>
                            <span class="mt-2 text-xs font-medium text-center max-w-[80px]"
                                  :class="currentStep >= index ? 'text-violet-600' : 'text-gray-400'"
                                  x-text="step.name"></span>
                        </div>
                        {{-- connector line --}}
                        <div x-show="index < steps.length - 1"
                             class="w-16 md:w-24 h-0.5 mx-2 transition-all duration-300"
                             :class="currentStep > index ? 'bg-violet-600' : 'bg-gray-200'">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- form container --}}
        <form @submit.prevent="submitForm" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8 fade-in-up gpu-accelerate" style="animation-delay: 0.2s;">

            {{-- step 1: job details --}}
            <div x-show="currentStep === 0" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-4"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                <h2 class="text-xl font-bold text-gray-900 mb-6">1. Detail Lowongan</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Lowongan</label>
                        <input type="text" x-model="formData.title"
                               placeholder="cth: Senior Software Engineer"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                        <p x-show="errors.title" class="mt-1 text-sm text-red-500" x-text="errors.title"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                        <input type="text" x-model="formData.department"
                               placeholder="cth: Engineering"
                               list="departments"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                        <datalist id="departments">
                            @foreach($departments as $dept)
                            <option value="{{ $dept }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                        <input type="text" x-model="formData.location"
                               placeholder="cth: Remote, Berlin, Germany"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Pekerjaan</label>
                        <select x-model="formData.job_type"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                            <option value="">Pilih tipe pekerjaan</option>
                            @foreach($jobTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Rentang Gaji</label>
                            <button type="button" @click="suggestSalary()"
                                    class="text-xs text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Saran Gaji
                            </button>
                        </div>
                        <input type="text" x-model="formData.salary_range"
                               placeholder="cth: Rp 15.000.000 - Rp 25.000.000 per bulan"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                        <p x-show="salaryBenchmark" class="mt-2 text-xs text-gray-500 bg-amber-50 border border-amber-200 rounded-lg p-2" x-text="salaryBenchmark"></p>
                    </div>
                </div>
            </div>

            {{-- step 2: description --}}
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-4"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                <h2 class="text-xl font-bold text-gray-900 mb-6">2. Deskripsi</h2>

                <div class="space-y-6">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi Pekerjaan</label>
                            <button type="button" @click="generateDescription('description')"
                                    :disabled="!formData.title"
                                    class="text-xs text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Generate AI
                            </button>
                        </div>
                        <textarea x-model="formData.description" rows="4"
                                  placeholder="Jelaskan tentang posisi ini dan apa yang akan dikerjakan..."
                                  class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all resize-none"></textarea>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Tanggung Jawab</label>
                            <button type="button" @click="generateDescription('responsibilities')"
                                    :disabled="!formData.title"
                                    class="text-xs text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Generate AI
                            </button>
                        </div>
                        <textarea x-model="formData.responsibilities" rows="4"
                                  placeholder="- Mengembangkan fitur baru&#10;- Melakukan code review&#10;- Berkolaborasi dengan tim"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all resize-none"></textarea>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Kualifikasi</label>
                            <button type="button" @click="generateDescription('qualifications')"
                                    :disabled="!formData.title"
                                    class="text-xs text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Generate AI
                            </button>
                        </div>
                        <textarea x-model="formData.qualifications" rows="4"
                                  placeholder="- Mahasiswa aktif minimal semester 5&#10;- Berpengalaman dalam kegiatan lapangan/kemasyarakatan&#10;- Komunikatif dan dapat bekerja dengan masyarakat desa"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- step 3: skills --}}
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-4"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                <h2 class="text-xl font-bold text-gray-900 mb-6">3. Keahlian</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Pilih Keahlian Yang Dibutuhkan</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($availableSkills as $skill)
                        <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-gray-50 transition-colors">
                            <input type="checkbox"
                                   value="{{ $skill }}"
                                   x-model="formData.skills"
                                   class="w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                            <span class="text-sm text-gray-700">{{ $skill }}</span>
                        </label>
                        @endforeach
                    </div>

                    {{-- selected skills preview --}}
                    <div x-show="formData.skills.length > 0" class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-sm text-gray-500 mb-2">Keahlian Terpilih:</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="skill in formData.skills" :key="skill">
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-violet-100 text-violet-700 text-sm rounded-full">
                                    <span x-text="skill"></span>
                                    <button type="button" @click="formData.skills = formData.skills.filter(s => s !== skill)"
                                            class="hover:text-violet-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- step 4: SDG alignment --}}
            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-4"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                <h2 class="text-xl font-bold text-gray-900 mb-6">4. SDG Alignment</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Pilih SDG Yang Relevan Dengan Posisi Ini</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($sdgOptions as $sdg)
                        <label class="flex items-start gap-3 cursor-pointer p-3 rounded-lg border border-gray-200 hover:border-violet-300 hover:bg-violet-50 transition-all">
                            <input type="checkbox"
                                   value="{{ $sdg['id'] }}"
                                   x-model="formData.sdg_alignment"
                                   class="w-4 h-4 mt-0.5 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                            <span class="text-sm text-gray-700">{{ $sdg['name'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- step 5: impact criteria --}}
            <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-4"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                <h2 class="text-xl font-bold text-gray-900 mb-6">5. Kriteria Dampak</h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Metrik Dampak</label>
                        <textarea x-model="formData.impact_metrics" rows="4"
                                  placeholder="Jelaskan bagaimana keberhasilan posisi ini akan diukur...&#10;cth: Peningkatan efisiensi sistem sebesar 20%"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kriteria Keberhasilan</label>
                        <textarea x-model="formData.success_criteria" rows="4"
                                  placeholder="Apa yang menandakan kandidat berhasil dalam posisi ini...&#10;cth: Mampu deliver minimal 2 fitur utama dalam 3 bulan pertama"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- navigation buttons --}}
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="button"
                        x-show="currentStep > 0"
                        @click="prevStep()"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Langkah Sebelumnya
                </button>

                <button type="button"
                        x-show="currentStep < steps.length - 1"
                        @click="nextStep()"
                        class="px-6 py-2.5 bg-violet-600 text-white rounded-lg hover:bg-violet-700 font-medium transition-colors">
                    Langkah Berikutnya
                </button>

                <button type="submit"
                        x-show="currentStep === steps.length - 1"
                        class="px-6 py-2.5 bg-violet-600 text-white rounded-lg hover:bg-violet-700 font-medium transition-colors">
                    Buat Lowongan
                </button>
            </div>
        </form>

        {{-- Template Manager Modal --}}
        <div x-show="showTemplateManager"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             @click.self="showTemplateManager = false">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-50"></div>
                <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full p-6 z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900">Template Manager</h3>
                        <button @click="showTemplateManager = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Save as Template --}}
                    <div class="mb-6 p-4 bg-violet-50 rounded-lg border border-violet-200">
                        <h4 class="font-semibold text-gray-900 mb-2">Simpan Sebagai Template</h4>
                        <div class="flex gap-2">
                            <input type="text"
                                   x-model="newTemplateName"
                                   placeholder="Nama template (cth: Senior Engineer Template)"
                                   class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-violet-500">
                            <button @click="saveAsTemplate()"
                                    class="px-4 py-2 bg-violet-600 text-white text-sm font-semibold rounded-lg hover:bg-violet-700 transition-colors">
                                Simpan
                            </button>
                        </div>
                    </div>

                    {{-- Templates List --}}
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Template Tersimpan</h4>
                        <div x-show="savedTemplates.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            <p>Belum ada template tersimpan</p>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(template, index) in savedTemplates" :key="index">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 text-sm" x-text="template.name"></p>
                                        <p class="text-xs text-gray-500" x-text="'Dibuat: ' + new Date(template.createdAt).toLocaleDateString('id-ID')"></p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click="loadTemplate(index)"
                                                class="px-3 py-1.5 text-xs font-medium text-violet-700 bg-violet-100 rounded-lg hover:bg-violet-200 transition-colors">
                                            Load
                                        </button>
                                        <button @click="deleteTemplate(index)"
                                                class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preview Modal --}}
        <div x-show="showPreview"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             @click.self="showPreview = false">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-black opacity-50"></div>
                <div class="relative bg-white rounded-xl shadow-xl max-w-4xl w-full p-6 z-10 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Preview Lowongan</h3>
                        <button @click="showPreview = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Preview Content --}}
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2" x-text="formData.title || 'Judul Lowongan'"></h1>
                        <div class="flex flex-wrap gap-3 mb-4 text-sm text-gray-600">
                            <span x-show="formData.department" class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="formData.department"></span>
                            </span>
                            <span x-show="formData.location" class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <span x-text="formData.location"></span>
                            </span>
                            <span x-show="formData.job_type" class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="formData.job_type"></span>
                            </span>
                        </div>

                        <div x-show="formData.salary_range" class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <p class="text-sm font-semibold text-gray-700">Rentang Gaji</p>
                            <p class="text-lg font-bold text-amber-700" x-text="formData.salary_range"></p>
                        </div>

                        <div x-show="formData.description" class="mb-4">
                            <h3 class="font-semibold text-gray-900 mb-2">Deskripsi</h3>
                            <p class="text-gray-700 whitespace-pre-line" x-text="formData.description"></p>
                        </div>

                        <div x-show="formData.responsibilities" class="mb-4">
                            <h3 class="font-semibold text-gray-900 mb-2">Tanggung Jawab</h3>
                            <p class="text-gray-700 whitespace-pre-line" x-text="formData.responsibilities"></p>
                        </div>

                        <div x-show="formData.qualifications" class="mb-4">
                            <h3 class="font-semibold text-gray-900 mb-2">Kualifikasi</h3>
                            <p class="text-gray-700 whitespace-pre-line" x-text="formData.qualifications"></p>
                        </div>

                        <div x-show="formData.skills.length > 0" class="mb-4">
                            <h3 class="font-semibold text-gray-900 mb-2">Keahlian</h3>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="skill in formData.skills" :key="skill">
                                    <span class="px-3 py-1 bg-violet-100 text-violet-700 text-sm rounded-full" x-text="skill"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

/* custom scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #F3F4F6;
    border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #D1D5DB;
    border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #9CA3AF;
}

/* hide elements with x-cloak until Alpine.js loads */
[x-cloak] {
    display: none !important;
}

/* reduced motion support untuk aksesibilitas */
@media (prefers-reduced-motion: reduce) {
    .fade-in-up {
        animation: none;
        opacity: 1;
    }
}
</style>
@endsection

@push('scripts')
<script>
function jobWizard() {
    return {
        currentStep: 0,
        steps: [
            { name: 'Detail Lowongan' },
            { name: 'Deskripsi' },
            { name: 'Keahlian' },
            { name: 'SDG Alignment' },
            { name: 'Kriteria Dampak' }
        ],
        formData: {
            title: '',
            department: '',
            location: '',
            job_type: '',
            salary_range: '',
            description: '',
            responsibilities: '',
            qualifications: '',
            skills: [],
            sdg_alignment: [],
            impact_metrics: '',
            success_criteria: ''
        },
        errors: {},
        autoSaving: false,
        lastSaved: '',
        autoSaveTimeout: null,
        showTemplateManager: false,
        showPreview: false,
        newTemplateName: '',
        savedTemplates: [],
        salaryBenchmark: '',

        init() {
            // Load draft from localStorage
            this.loadDraft();

            // Load templates from localStorage
            const templates = localStorage.getItem('company_job_templates');
            if (templates) {
                this.savedTemplates = JSON.parse(templates);
            }

            // Watch formData for changes and trigger auto-save
            this.$watch('formData', () => {
                this.scheduleAutoSave();
            }, { deep: true });
        },

        scheduleAutoSave() {
            if (this.autoSaveTimeout) {
                clearTimeout(this.autoSaveTimeout);
            }

            this.autoSaveTimeout = setTimeout(() => {
                this.autoSaveDraft();
            }, 2000); // Auto-save after 2 seconds of inactivity
        },

        autoSaveDraft() {
            this.autoSaving = true;

            try {
                localStorage.setItem('company_job_draft', JSON.stringify({
                    formData: this.formData,
                    currentStep: this.currentStep,
                    savedAt: new Date().toISOString()
                }));

                // Update last saved time
                const now = new Date();
                this.lastSaved = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                setTimeout(() => {
                    this.autoSaving = false;
                }, 500);
            } catch (error) {
                console.error('Error auto-saving draft:', error);
                this.autoSaving = false;
            }
        },

        loadDraft() {
            const draft = localStorage.getItem('company_job_draft');
            if (draft) {
                try {
                    const parsed = JSON.parse(draft);

                    if (confirm('Ditemukan draft tersimpan. Lanjutkan draft?')) {
                        this.formData = parsed.formData;
                        this.currentStep = parsed.currentStep || 0;

                        const savedAt = new Date(parsed.savedAt);
                        this.lastSaved = savedAt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    }
                } catch (error) {
                    console.error('Error loading draft:', error);
                }
            }
        },

        clearDraft() {
            if (confirm('Hapus draft dan mulai dari awal?')) {
                localStorage.removeItem('company_job_draft');
                this.formData = {
                    title: '',
                    department: '',
                    location: '',
                    job_type: '',
                    salary_range: '',
                    description: '',
                    responsibilities: '',
                    qualifications: '',
                    skills: [],
                    sdg_alignment: [],
                    impact_metrics: '',
                    success_criteria: ''
                };
                this.currentStep = 0;
                this.lastSaved = '';
                window.showNotification('Draft dihapus', 'info');
            }
        },

        // Template Management
        saveAsTemplate() {
            if (!this.newTemplateName.trim()) {
                window.showNotification('Masukkan nama template', 'warning');
                return;
            }

            const template = {
                name: this.newTemplateName,
                data: JSON.parse(JSON.stringify(this.formData)),
                createdAt: new Date().toISOString()
            };

            this.savedTemplates.push(template);
            localStorage.setItem('company_job_templates', JSON.stringify(this.savedTemplates));

            this.newTemplateName = '';
            window.showNotification('Template berhasil disimpan!', 'success');
        },

        loadTemplate(index) {
            if (confirm('Load template ini? Data form saat ini akan diganti.')) {
                const template = this.savedTemplates[index];
                this.formData = JSON.parse(JSON.stringify(template.data));
                this.showTemplateManager = false;
                window.showNotification('Template dimuat: ' + template.name, 'success');
            }
        },

        deleteTemplate(index) {
            if (confirm('Hapus template ini?')) {
                this.savedTemplates.splice(index, 1);
                localStorage.setItem('company_job_templates', JSON.stringify(this.savedTemplates));
                window.showNotification('Template dihapus', 'info');
            }
        },

        // AI Description Generator (Placeholder - would use actual API)
        async generateDescription(field) {
            if (!this.formData.title) {
                window.showNotification('Isi judul lowongan terlebih dahulu', 'warning');
                return;
            }

            window.showNotification('Generating konten dengan AI...', 'info');

            // TODO: Implement actual AI API call here
            // For now, showing placeholder
            setTimeout(() => {
                let generatedText = '';

                if (field === 'description') {
                    generatedText = `Kami mencari ${this.formData.title} yang berpengalaman untuk bergabung dengan tim kami. Posisi ini akan bertanggung jawab untuk mengembangkan dan memelihara sistem kami dengan fokus pada kualitas dan performa tinggi.`;
                } else if (field === 'responsibilities') {
                    generatedText = `- Mengembangkan dan memelihara aplikasi sesuai best practices\n- Berkolaborasi dengan tim lintas fungsi\n- Melakukan code review dan mentoring\n- Berkontribusi pada arsitektur teknis\n- Memastikan kualitas dan keamanan kode`;
                } else if (field === 'qualifications') {
                    generatedText = `- Minimal 3+ tahun pengalaman di posisi serupa\n- Menguasai teknologi yang relevan\n- Pengalaman dengan metodologi Agile/Scrum\n- Kemampuan problem solving yang kuat\n- Komunikasi dan kolaborasi tim yang baik`;
                }

                this.formData[field] = generatedText;
                window.showNotification('Konten berhasil di-generate! Silahkan edit sesuai kebutuhan.', 'success');
            }, 1500);
        },

        // Salary Benchmarking (Placeholder)
        suggestSalary() {
            if (!this.formData.title) {
                window.showNotification('Isi judul lowongan terlebih dahulu', 'warning');
                return;
            }

            // TODO: Implement actual salary API call
            // For now, showing placeholder based on title keywords
            let benchmark = '';
            const title = this.formData.title.toLowerCase();

            if (title.includes('senior') || title.includes('lead')) {
                benchmark = '💡 Berdasarkan data pasar untuk Senior positions: Rp 15.000.000 - Rp 30.000.000/bulan';
            } else if (title.includes('junior') || title.includes('entry')) {
                benchmark = '💡 Berdasarkan data pasar untuk Junior positions: Rp 6.000.000 - Rp 12.000.000/bulan';
            } else if (title.includes('manager') || title.includes('head')) {
                benchmark = '💡 Berdasarkan data pasar untuk Manager positions: Rp 20.000.000 - Rp 40.000.000/bulan';
            } else {
                benchmark = '💡 Berdasarkan data pasar untuk Mid-level positions: Rp 10.000.000 - Rp 20.000.000/bulan';
            }

            this.salaryBenchmark = benchmark;
            window.showNotification('Saran gaji ditampilkan di bawah field', 'info');
        },

        nextStep() {
            if (this.validateCurrentStep()) {
                if (this.currentStep < this.steps.length - 1) {
                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        },

        prevStep() {
            if (this.currentStep > 0) {
                this.currentStep--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        validateCurrentStep() {
            this.errors = {};

            if (this.currentStep === 0) {
                if (!this.formData.title.trim()) {
                    this.errors.title = 'Judul lowongan wajib diisi';
                    return false;
                }
            }

            if (this.currentStep === 2) {
                if (this.formData.skills.length === 0) {
                    alert('Pilih minimal satu keahlian');
                    return false;
                }
            }

            return true;
        },

        submitForm() {
            if (!this.validateCurrentStep()) return;

            // TO DO: kirim data ke server via AJAX atau form submit
            console.log('Form data:', this.formData);

            // simulasi submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("company.jobs.store") }}';

            // CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // append form data
            for (const [key, value] of Object.entries(this.formData)) {
                if (Array.isArray(value)) {
                    value.forEach((v, i) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `${key}[]`;
                        input.value = v;
                        form.appendChild(input);
                    });
                } else {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    form.appendChild(input);
                }
            }

            document.body.appendChild(form);
            form.submit();
        }
    }
}
</script>
@endpush
