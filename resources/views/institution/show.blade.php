@extends('layouts.app')

@section('title', $problem->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- back button --}}
        <a href="{{ route('institution.problems.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center gap-2 mb-6">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Masalah
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- main content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- header --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $problem->title }}</h1>
                            <div class="flex items-center gap-3">
                                @if($problem->status == 'draft')
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-semibold rounded-full">Draft</span>
                                @elseif($problem->status == 'open')
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">Terbuka</span>
                                @elseif($problem->status == 'in_progress')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded-full">Berjalan</span>
                                @elseif($problem->status == 'completed')
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">Selesai</span>
                                @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded-full">Ditutup</span>
                                @endif

                                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-semibold rounded-full">
                                    @if($problem->difficulty_level == 'beginner') Pemula
                                    @elseif($problem->difficulty_level == 'intermediate') Menengah
                                    @else Lanjutan
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('institution.problems.edit', $problem->id) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                Edit
                            </a>
                        </div>
                    </div>

                    {{-- statistik --}}
                    <div class="grid grid-cols-3 gap-4 py-4 border-t border-b border-gray-200">
                        <div>
                            <p class="text-sm text-gray-600">Views</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $problem->views_count }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Aplikasi</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $problem->applications_count }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Diterima</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $problem->accepted_students }}/{{ $problem->required_students }}</p>
                        </div>
                    </div>
                </div>

                {{-- deskripsi --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Deskripsi</h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $problem->description }}</p>
                </div>

                @if($problem->background)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Latar Belakang</h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $problem->background }}</p>
                </div>
                @endif

                @if($problem->objectives)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Tujuan</h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $problem->objectives }}</p>
                </div>
                @endif

                {{-- requirements --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Requirements</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-semibold text-gray-700 mb-2">Skill yang Dibutuhkan:</p>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $skills = is_array($problem->required_skills) ? $problem->required_skills : json_decode($problem->required_skills, true) ?? [];
                                @endphp
                                @foreach($skills as $skill)
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>

                        @if($problem->required_majors)
                        <div>
                            <p class="text-sm font-semibold text-gray-700 mb-2">Jurusan yang Dibutuhkan:</p>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $majors = is_array($problem->required_majors) ? $problem->required_majors : json_decode($problem->required_majors, true) ?? [];
                                @endphp
                                @foreach($majors as $major)
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">{{ $major }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($problem->deliverables)
                        <div>
                            <p class="text-sm font-semibold text-gray-700 mb-2">Deliverables:</p>
                            <ul class="list-disc list-inside text-gray-700 space-y-1">
                                @php
                                    $deliverables = is_array($problem->deliverables) ? $problem->deliverables : json_decode($problem->deliverables, true) ?? [];
                                @endphp
                                @foreach($deliverables as $deliverable)
                                <li>{{ $deliverable }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- aplikasi terbaru --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Aplikasi Terbaru</h2>
                        <a href="{{ route('institution.applications.index', ['problem_id' => $problem->id]) }}" 
                           class="text-blue-600 hover:text-blue-700 font-semibold">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="space-y-3">
                        @forelse($problem->applications->take(5) as $application)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">
                                    {{ substr($application->student->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $application->student->user->name }}</p>
                                    <p class="text-xs text-gray-600">{{ $application->student->university->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($application->status == 'pending')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-semibold rounded">Pending</span>
                                @elseif($application->status == 'accepted')
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded">Diterima</span>
                                @elseif($application->status == 'rejected')
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">Ditolak</span>
                                @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded">Review</span>
                                @endif
                                <a href="{{ route('institution.applications.show', $application->id) }}" 
                                   class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                                    Lihat
                                </a>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-600 text-center py-4">Belum ada aplikasi</p>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- sidebar --}}
            <div class="space-y-6">
                
                {{-- quick stats --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Statistik Aplikasi</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pending</span>
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 font-bold rounded">{{ $applicationStats['pending'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Under Review</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 font-bold rounded">{{ $applicationStats['under_review'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Accepted</span>
                            <span class="px-3 py-1 bg-green-100 text-green-700 font-bold rounded">{{ $applicationStats['accepted'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Rejected</span>
                            <span class="px-3 py-1 bg-red-100 text-red-700 font-bold rounded">{{ $applicationStats['rejected'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- lokasi --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Lokasi</h3>
                    <div class="space-y-2 text-sm">
                        @if($problem->village)
                        <p><span class="font-semibold">Desa:</span> {{ $problem->village }}</p>
                        @endif
                        <p><span class="font-semibold">Kabupaten:</span> {{ $problem->regency->name }}</p>
                        <p><span class="font-semibold">Provinsi:</span> {{ $problem->province->name }}</p>
                        @if($problem->detailed_location)
                        <p><span class="font-semibold">Detail:</span> {{ $problem->detailed_location }}</p>
                        @endif
                    </div>
                </div>

                {{-- timeline --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Timeline</h3>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-semibold">Deadline Aplikasi:</span><br>{{ $problem->application_deadline->format('d M Y') }}</p>
                        <p><span class="font-semibold">Mulai:</span><br>{{ $problem->start_date->format('d M Y') }}</p>
                        <p><span class="font-semibold">Selesai:</span><br>{{ $problem->end_date->format('d M Y') }}</p>
                        <p><span class="font-semibold">Durasi:</span> {{ $problem->duration_months }} bulan</p>
                    </div>
                </div>

                {{-- kategori SDG --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Kategori SDG</h3>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $sdgCategories = is_array($problem->sdg_categories) ? $problem->sdg_categories : json_decode($problem->sdg_categories, true) ?? [];
                        @endphp
                        @foreach($sdgCategories as $sdg)
                        <span class="px-3 py-2 bg-blue-100 text-blue-700 text-sm font-semibold rounded-lg">SDG {{ $sdg }}</span>
                        @endforeach
                    </div>
                </div>

                {{-- fasilitas --}}
                @if($problem->facilities_provided)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Fasilitas</h3>
                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                        @php
                            $facilities = is_array($problem->facilities_provided) ? $problem->facilities_provided : json_decode($problem->facilities_provided, true) ?? [];
                        @endphp
                        @foreach($facilities as $facility)
                        <li>{{ $facility }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection