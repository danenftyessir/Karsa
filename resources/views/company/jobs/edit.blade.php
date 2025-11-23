@extends('layouts.app')

@section('title', 'Edit Lowongan Pekerjaan')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="jobEditWizard()">

    {{-- hero section --}}
    <div class="relative h-48 bg-cover bg-center" style="background-image: url('{{ asset('company3.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white px-4">
            <h1 class="text-2xl md:text-3xl font-bold mb-2 fade-in-up" style="font-family: 'Space Grotesk', sans-serif;">
                Edit Lowongan Pekerjaan
            </h1>
            <p class="text-sm md:text-base text-gray-300 max-w-xl fade-in-up" style="animation-delay: 0.1s;">
                Perbarui informasi lowongan pekerjaan Anda
            </p>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

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
            @csrf
            @method('PUT')

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

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select x-model="formData.status"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                            <option value="draft">Draft</option>
                            <option value="published">Dipublikasikan</option>
                            <option value="closed">Ditutup</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select x-model="formData.job_category_id"
                                class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rentang Gaji</label>
                        <input type="text" x-model="formData.salary_range"
                               placeholder="cth: Rp 15.000.000 - Rp 25.000.000 per bulan"
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Pekerjaan</label>
                        <textarea x-model="formData.description" rows="4"
                                  placeholder="Jelaskan tentang posisi ini dan apa yang akan dikerjakan..."
                                  class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggung Jawab</label>
                        <textarea x-model="formData.responsibilities" rows="4"
                                  placeholder="- Mengembangkan fitur baru&#10;- Melakukan code review&#10;- Berkolaborasi dengan tim"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kualifikasi</label>
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
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('company.jobs.index') }}"
                   class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Batal
                </a>

                <div class="flex items-center gap-3">
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
                        Perbarui Lowongan
                    </button>
                </div>
            </div>
        </form>
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
function jobEditWizard() {
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
            title: '{{ $jobPosting->title ?? "" }}',
            department: '{{ $jobPosting->department ?? "" }}',
            location: '{{ $jobPosting->location ?? "" }}',
            job_type: '{{ $jobPosting->job_type ?? "" }}',
            status: '{{ $jobPosting->status ?? "draft" }}',
            job_category_id: '{{ $jobPosting->job_category_id ?? "" }}',
            salary_range: '{{ $jobPosting->salary_range ?? "" }}',
            description: `{{ $jobPosting->description ?? "" }}`,
            responsibilities: `{{ $jobPosting->responsibilities ?? "" }}`,
            qualifications: `{{ $jobPosting->qualifications ?? "" }}`,
            skills: @json($jobPosting->skills ?? []),
            sdg_alignment: @json($jobPosting->sdg_alignment ?? []),
            impact_metrics: `{{ $jobPosting->impact_metrics ?? "" }}`,
            success_criteria: `{{ $jobPosting->success_criteria ?? "" }}`
        },
        errors: {},

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

            // kirim data ke server via form submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("company.jobs.update", $jobPosting->id) }}';

            // CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // method spoofing untuk PUT
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

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
