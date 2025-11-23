{{-- resources/views/student/jobs/alerts.blade.php --}}
@extends('layouts.app')

@section('title', 'Job Alerts')

@push('styles')
<style>
    .alert-card {
        transform: translate3d(0, 0, 0);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .alert-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" x-data="alertsPage()">
    {{-- header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-6 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Job Alerts</h1>
                    <p class="text-gray-600 mt-1">Dapatkan notifikasi lowongan yang sesuai kriteria Anda</p>
                </div>
                <button @click="showCreateModal = true"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    Buat Alert Baru
                </button>
            </div>
        </div>
    </div>

    {{-- alerts list --}}
    <div class="max-w-4xl mx-auto px-6 py-8">
        <div class="space-y-4">
            @forelse($alerts as $alert)
            <div class="alert-card bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <h3 class="font-semibold text-gray-900">{{ $alert->name }}</h3>
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $alert->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $alert->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        {{-- criteria --}}
                        <div class="flex flex-wrap gap-2 mt-3">
                            @if($alert->keywords)
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs">
                                "{{ $alert->keywords }}"
                            </span>
                            @endif
                            @if($alert->job_types && count($alert->job_types) > 0)
                            @foreach($alert->job_types as $type)
                            <span class="px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full text-xs">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                            @endforeach
                            @endif
                            @if($alert->locations && count($alert->locations) > 0)
                            @foreach($alert->locations as $loc)
                            <span class="px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-xs">{{ $loc }}</span>
                            @endforeach
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 mt-3">
                            Frekuensi: {{ ucfirst($alert->frequency) }}
                            @if($alert->last_sent_at)
                            &bull; Terakhir dikirim {{ $alert->last_sent_at->diffForHumans() }}
                            @endif
                        </p>
                    </div>

                    {{-- actions --}}
                    <div class="flex items-center gap-2">
                        <button @click="toggleAlert({{ $alert->id }})"
                                class="p-2 rounded-lg transition-colors {{ $alert->is_active ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $alert->is_active ? 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' : 'M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2' }}"/>
                            </svg>
                        </button>
                        <button @click="deleteAlert({{ $alert->id }})"
                                class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Job Alert</h3>
                <p class="text-gray-600 mb-4">Buat alert untuk mendapatkan notifikasi lowongan baru</p>
                <button @click="showCreateModal = true"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    Buat Alert Pertama
                </button>
            </div>
            @endforelse
        </div>
    </div>

    {{-- create modal --}}
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-60 overflow-y-auto" x-transition>
        <div class="flex items-start justify-center min-h-screen px-4 py-20">
            <div class="fixed inset-0 bg-black/50" @click="showCreateModal = false"></div>
            <div class="relative bg-white rounded-2xl max-w-lg w-full p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Buat Job Alert Baru</h3>

                <form @submit.prevent="createAlert">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Alert *</label>
                            <input type="text" x-model="newAlert.name" required
                                   placeholder="Contoh: Lowongan Frontend Jakarta"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kata Kunci</label>
                            <input type="text" x-model="newAlert.keywords"
                                   placeholder="Contoh: frontend, react, javascript"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Pekerjaan</label>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="type in jobTypes" :key="type.value">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" :value="type.value" x-model="newAlert.job_types"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-600" x-text="type.label"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                            <input type="text" x-model="locationInput" @keydown.enter.prevent="addLocation"
                                   placeholder="Tekan Enter untuk menambah lokasi"
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="flex flex-wrap gap-2 mt-2">
                                <template x-for="(loc, idx) in newAlert.locations" :key="idx">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                                        <span x-text="loc"></span>
                                        <button type="button" @click="newAlert.locations.splice(idx, 1)" class="text-gray-400 hover:text-red-500">&times;</button>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Frekuensi Notifikasi *</label>
                            <select x-model="newAlert.frequency" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="instant">Instant (Langsung)</option>
                                <option value="daily">Harian</option>
                                <option value="weekly">Mingguan</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="button" @click="showCreateModal = false"
                                class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Buat Alert
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function alertsPage() {
    return {
        showCreateModal: false,
        locationInput: '',
        jobTypes: [
            { value: 'full_time', label: 'Full Time' },
            { value: 'part_time', label: 'Part Time' },
            { value: 'contract', label: 'Contract' },
            { value: 'internship', label: 'Magang' },
            { value: 'freelance', label: 'Freelance' }
        ],
        newAlert: {
            name: '',
            keywords: '',
            job_types: [],
            locations: [],
            frequency: 'daily'
        },

        addLocation() {
            if (this.locationInput.trim()) {
                this.newAlert.locations.push(this.locationInput.trim());
                this.locationInput = '';
            }
        },

        async createAlert() {
            try {
                const res = await fetch('{{ route("student.jobs.alerts.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.newAlert)
                });
                if (res.ok) {
                    this.showCreateModal = false;
                    window.location.reload();
                }
            } catch (e) { console.error(e); }
        },

        async toggleAlert(id) {
            try {
                const res = await fetch(`{{ url('student/jobs/alerts') }}/${id}/toggle`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if (res.ok) window.location.reload();
            } catch (e) { console.error(e); }
        },

        async deleteAlert(id) {
            if (!confirm('Hapus job alert ini?')) return;
            try {
                const res = await fetch(`{{ url('student/jobs/alerts') }}/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if (res.ok) window.location.reload();
            } catch (e) { console.error(e); }
        }
    }
}
</script>
@endpush
@endsection
