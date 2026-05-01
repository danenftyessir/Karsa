@extends('layouts.app')

@section('title', 'Profil Instansi')

@push('styles')
<style>
.profile-transition {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.profile-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
}

.profile-photo-wrapper {
    position: relative;
    animation: scaleIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.fade-in {
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- header --}}
        <div class="mb-8 profile-transition">
            <h1 class="text-3xl font-bold text-gray-900">Profil Instansi</h1>
            <p class="text-gray-600 mt-2">Kelola Informasi Dan Statistik Instansi Anda</p>
        </div>

        {{-- pesan sukses/error --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 flex items-center profile-transition fade-in">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 flex items-center profile-transition">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- sidebar kiri --}}
            <div class="lg:col-span-1">
                {{-- kartu profil --}}
                <div class="bg-white rounded-lg shadow-sm p-6 text-center profile-card">
                    <div class="inline-block relative profile-photo-wrapper">
                        @if($institution->logo_path)
                            {{-- PERBAIKAN BUG: gunakan getLogoUrl() untuk support Supabase --}}
                            <img src="{{ $institution->getLogoUrl() }}" 
                                 alt="{{ $institution->name }}" 
                                 class="w-32 h-32 rounded-lg object-cover border-4 border-blue-500 mx-auto">
                        @else
                            <div class="w-32 h-32 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-4xl font-bold border-4 border-blue-500 mx-auto">
                                {{ substr($institution->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mt-4">{{ $institution->name }}</h2>
                    <p class="text-gray-600 mt-1">{{ ucwords(str_replace('_', ' ', $institution->type)) }}</p>

                    <div class="mt-4 pt-4 border-t border-gray-100 space-y-3 text-sm text-left">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-700 break-all">{{ $user->email }}</span>
                        </div>

                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="text-gray-700">{{ $institution->regency->name ?? '-' }}, {{ $institution->province->name ?? '-' }}</span>
                        </div>

                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span class="text-gray-700">{{ $institution->phone }}</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                        <a href="{{ route('institution.profile.edit') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Profil
                        </a>

                        <a href="{{ route('institution.public', $institution->id) }}" 
                           target="_blank"
                           class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Lihat Profil Publik
                        </a>
                    </div>
                </div>
            </div>

            {{-- konten utama --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- statistik grid --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center profile-card">
                        <div class="text-3xl font-bold text-blue-600">{{ $stats['total_problems'] }}</div>
                        <div class="text-sm text-gray-600 mt-2">Total Masalah</div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 text-center profile-card">
                        <div class="text-3xl font-bold text-green-600">{{ $stats['active_problems'] }}</div>
                        <div class="text-sm text-gray-600 mt-2">Masalah Aktif</div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 text-center profile-card">
                        <div class="text-3xl font-bold text-purple-600">{{ $stats['completed_problems'] }}</div>
                        <div class="text-sm text-gray-600 mt-2">Masalah Selesai</div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 text-center profile-card">
                        <div class="text-3xl font-bold text-orange-600">{{ $stats['total_projects'] }}</div>
                        <div class="text-sm text-gray-600 mt-2">Total Proyek</div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 text-center profile-card">
                        <div class="text-3xl font-bold text-teal-600">{{ $stats['active_projects'] }}</div>
                        <div class="text-sm text-gray-600 mt-2">Proyek Berjalan</div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6 text-center profile-card">
                        <div class="text-3xl font-bold text-indigo-600">{{ $stats['completed_projects'] }}</div>
                        <div class="text-sm text-gray-600 mt-2">Proyek Selesai</div>
                    </div>
                </div>

                {{-- informasi instansi --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Informasi Instansi</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Nama Instansi</label>
                            <p class="text-gray-900 mt-1">{{ $institution->name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-600">Jenis Instansi</label>
                            <p class="text-gray-900 mt-1">{{ ucwords(str_replace('_', ' ', $institution->type)) }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-600">Email</label>
                            <p class="text-gray-900 mt-1">{{ $user->email }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-600">Nomor Telepon</label>
                            <p class="text-gray-900 mt-1">{{ $institution->phone }}</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-600">Alamat</label>
                            <p class="text-gray-900 mt-1">{{ $institution->address }}</p>
                        </div>

                        @if($institution->website)
                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-600">Website</label>
                            <a href="{{ $institution->website }}" target="_blank" class="text-blue-600 hover:text-blue-700 mt-1 block">
                                {{ $institution->website }}
                            </a>
                        </div>
                        @endif

                        @if($institution->description)
                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-gray-600">Deskripsi</label>
                            <p class="text-gray-900 mt-1">{{ $institution->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- penanggung jawab --}}
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Penanggung Jawab</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Nama PIC</label>
                            <p class="text-gray-900 mt-1">{{ $institution->pic_name }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-gray-600">Jabatan</label>
                            <p class="text-gray-900 mt-1">{{ $institution->pic_position }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection