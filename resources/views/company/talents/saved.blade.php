@extends('layouts.app')

@section('title', 'Saved Talents - ' . $company->name)

@push('styles')
<style>
    /* optimisasi performa dengan GPU acceleration */
    .talent-group {
        transform: translateZ(0);
        backface-visibility: hidden;
        will-change: transform;
    }

    .talent-item {
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateZ(0);
    }

    .talent-item:hover {
        transform: translateY(-2px) translateZ(0);
        box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.1);
    }

    /* animasi accordion yang smooth */
    .accordion-content {
        transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                    opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .accordion-arrow {
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .accordion-arrow.rotated {
        transform: rotate(180deg);
    }

    /* hero section styling */
    .hero-section {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%);
    }

    /* respek reduced motion untuk aksesibilitas */
    @media (prefers-reduced-motion: reduce) {
        .talent-item,
        .accordion-content,
        .accordion-arrow {
            transition: none;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" x-data="savedTalentsPage()">

    <!-- hero section -->
    <div class="relative h-64 overflow-hidden">
        <img src="{{ asset('images/team-meeting.jpg') }}"
             alt="Team Meeting"
             class="absolute inset-0 w-full h-full object-cover"
             onerror="this.src='{{ asset('images/hero-bg.jpg') }}'">
        <div class="hero-section absolute inset-0 flex flex-col items-center justify-center text-white px-4">
            <h1 class="text-3xl md:text-4xl font-bold mb-3 text-center">Your Curated Talent Pool</h1>
            <p class="text-center text-white/90 max-w-2xl mb-6">
                Efficiently manage and engage with your bookmarked talents. Organize profiles into folders, initiate quick contacts, and export your selections.
            </p>
            <button @click="exportAllTalents()"
                    class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                Export All Talents
            </button>
        </div>
    </div>

    <!-- main content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- page title -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Your Saved Talent Pool</h2>
        </div>

        <!-- talent groups (accordion) -->
        <div class="space-y-4">
            <template x-for="(group, groupIndex) in talentGroups" :key="group.id">
                <div class="talent-group bg-white rounded-xl border border-gray-200 overflow-hidden">

                    <!-- accordion header -->
                    <button @click="toggleGroup(groupIndex)"
                            class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-semibold text-gray-900" x-text="group.name"></h3>
                            <span class="text-gray-500" x-text="'(' + group.talents.length + ')'"></span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 accordion-arrow"
                             :class="{ 'rotated': expandedGroups.includes(groupIndex) }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- accordion content -->
                    <div class="accordion-content"
                         x-show="expandedGroups.includes(groupIndex)"
                         x-collapse>
                        <div class="px-6 pb-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <template x-for="talent in group.talents" :key="talent.id">
                                    <div class="talent-item bg-white border border-gray-200 rounded-xl p-5">

                                        <!-- talent header -->
                                        <div class="flex items-start gap-4 mb-4">
                                            <img :src="'/storage/profiles/' + talent.avatar"
                                                 :alt="talent.name"
                                                 class="w-14 h-14 rounded-full object-cover flex-shrink-0"
                                                 onerror="this.src='/images/default-avatar.png'">
                                            <div class="min-w-0 flex-1">
                                                <h4 class="font-semibold text-gray-900 truncate" x-text="talent.name"></h4>
                                                <p class="text-sm text-gray-500 truncate" x-text="talent.title"></p>
                                                <template x-if="talent.verified">
                                                    <div class="flex items-center gap-1 mt-1">
                                                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="text-xs text-green-600 font-medium">Verified</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- talent description -->
                                        <p class="text-sm text-gray-600 mb-5 line-clamp-3" x-text="talent.description"></p>

                                        <!-- action buttons -->
                                        <div class="flex items-center gap-3">
                                            <a :href="'/company/talents/' + talent.id"
                                               class="flex-1 inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-150">
                                                View Profile
                                            </a>
                                            <button @click="openContactModal(talent)"
                                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors duration-150">
                                                Contact
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- empty state -->
        <div x-show="talentGroups.length === 0" class="text-center py-16">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Talent Tersimpan</h3>
            <p class="text-gray-500 mb-4">Mulai simpan talent favorit Anda dari halaman pencarian.</p>
            <a href="{{ route('company.talents.index') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition-colors duration-150">
                Browse Talents
            </a>
        </div>
    </div>

    <!-- contact modal -->
    <div x-show="contactModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="contactModalOpen = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" @click="contactModalOpen = false"></div>

            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 transform transition-all">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Contact Talent</h3>
                    <button @click="contactModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <template x-if="selectedTalent">
                    <div>
                        <!-- talent info -->
                        <div class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-lg">
                            <img :src="'/storage/profiles/' + selectedTalent.avatar"
                                 class="w-12 h-12 rounded-full object-cover"
                                 onerror="this.src='/images/default-avatar.png'">
                            <div>
                                <p class="font-medium text-gray-900" x-text="selectedTalent.name"></p>
                                <p class="text-sm text-gray-500" x-text="selectedTalent.title"></p>
                            </div>
                        </div>

                        <!-- contact type -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Kontak</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button @click="contactType = 'message'"
                                        :class="contactType === 'message' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-300 text-gray-700'"
                                        class="px-4 py-2 border rounded-lg text-sm font-medium transition-colors duration-150">
                                    Send Message
                                </button>
                                <button @click="contactType = 'interview_request'"
                                        :class="contactType === 'interview_request' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-300 text-gray-700'"
                                        class="px-4 py-2 border rounded-lg text-sm font-medium transition-colors duration-150">
                                    Request Interview
                                </button>
                            </div>
                        </div>

                        <!-- message -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                            <textarea x-model="contactMessage"
                                      rows="4"
                                      class="w-full rounded-lg border-gray-300 text-sm"
                                      placeholder="Tulis pesan Anda di sini..."></textarea>
                        </div>

                        <!-- actions -->
                        <div class="flex gap-3">
                            <button @click="contactModalOpen = false"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                Batal
                            </button>
                            <button @click="sendContact()"
                                    :disabled="!contactMessage.trim()"
                                    class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150">
                                Kirim
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function savedTalentsPage() {
    return {
        expandedGroups: [0, 1, 2], // semua grup terbuka secara default
        contactModalOpen: false,
        selectedTalent: null,
        contactType: 'message',
        contactMessage: '',

        // TO DO: ambil data dari database via API
        talentGroups: @json($savedTalentGroups),

        toggleGroup(index) {
            const idx = this.expandedGroups.indexOf(index);
            if (idx > -1) {
                this.expandedGroups.splice(idx, 1);
            } else {
                this.expandedGroups.push(index);
            }
        },

        openContactModal(talent) {
            this.selectedTalent = talent;
            this.contactType = 'message';
            this.contactMessage = '';
            this.contactModalOpen = true;
        },

        // TO DO: implementasi kirim kontak ke backend
        async sendContact() {
            if (!this.selectedTalent || !this.contactMessage.trim()) return;

            try {
                const response = await fetch(`/company/talents/${this.selectedTalent.id}/contact`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message: this.contactMessage,
                        type: this.contactType
                    })
                });

                if (response.ok) {
                    this.contactModalOpen = false;
                    // TO DO: tampilkan notifikasi sukses
                    alert('Pesan berhasil dikirim!');
                }
            } catch (error) {
                console.error('Error sending contact:', error);
            }
        },

        // TO DO: implementasi export ke CSV/Excel
        exportAllTalents() {
            // TO DO: panggil endpoint export
            console.log('Exporting all talents...');
            alert('Fitur export akan segera tersedia!');
        }
    }
}
</script>
@endpush
