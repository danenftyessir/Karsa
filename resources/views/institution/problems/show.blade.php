{{-- resources/views/institution/problems/show.blade.php --}}
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
            Kembali Ke Daftar Masalah
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- main content --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- header --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $problem->title }}</h1>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $problem->regency->name }}, {{ $problem->province->name }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $problem->views_count }} Views</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                    <span>{{ $problem->applications_count }} Aplikasi</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('institution.problems.edit', $problem) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                Edit
                            </a>
                            <form action="{{ route('institution.problems.destroy', $problem) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Apakah Anda Yakin Ingin Menghapus Masalah Ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- sdg categories --}}
                    @php
                        $sdgCategories = is_array($problem->sdg_categories) ? $problem->sdg_categories : json_decode($problem->sdg_categories, true) ?? [];
                    @endphp
                    @if(count($sdgCategories) > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($sdgCategories as $sdg)
                            <span class="px-3 py-1 bg-gradient-to-r from-blue-500 to-green-500 text-white text-sm font-semibold rounded-full">
                                SDG {{ $sdg }}
                            </span>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- galeri foto --}}
                @if($problem->images && $problem->images->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Galeri Foto</h2>
                    
                    {{-- main image --}}
                    @php
                        $coverImage = $problem->images->where('is_cover', true)->first() ?? $problem->images->first();
                    @endphp
                    @if($coverImage)
                    <div class="mb-4 rounded-lg overflow-hidden">
                        <img id="mainImage" 
                             src="{{ $coverImage->image_url }}" 
                             alt="{{ $coverImage->caption ?? 'Gambar Problem' }}"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/800x400?text=Gambar+Tidak+Tersedia'; this.classList.add('opacity-50');"
                             class="w-full h-96 object-cover">
                    </div>
                    @endif

                    {{-- thumbnail gallery --}}
                    @if($problem->images->count() > 1)
                    <div class="grid grid-cols-4 gap-4">
                        @foreach($problem->images as $image)
                        <button type="button" 
                                onclick="document.getElementById('mainImage').src='{{ $image->image_url }}'; document.getElementById('mainImage').onerror = function() { this.src='https://via.placeholder.com/800x400?text=Gambar+Tidak+Tersedia'; this.classList.add('opacity-50'); };"
                                class="aspect-square rounded-lg overflow-hidden border-2 transition-all {{ $loop->first ? 'border-blue-500 ring-2 ring-blue-200' : 'border-transparent hover:border-blue-300' }}">
                            <img src="{{ $image->image_url }}" 
                                 alt="{{ $image->caption ?? 'Thumbnail' }}"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/200?text=No+Image'; this.classList.add('opacity-50');"
                                 class="w-full h-full object-cover"
                                 loading="lazy">
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif

                {{-- description section --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Deskripsi Masalah</h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($problem->description)) !!}
                    </div>
                </div>

                {{-- background --}}
                @if($problem->background)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Latar Belakang</h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($problem->background)) !!}
                    </div>
                </div>
                @endif

                {{-- objectives --}}
                @if($problem->objectives)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Tujuan</h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($problem->objectives)) !!}
                    </div>
                </div>
                @endif

                {{-- scope --}}
                @if($problem->scope)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Ruang Lingkup</h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($problem->scope)) !!}
                    </div>
                </div>
                @endif

                {{-- expected outcomes --}}
                @if($problem->expected_outcomes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Output Yang Diharapkan</h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($problem->expected_outcomes)) !!}
                    </div>
                </div>
                @endif

                {{-- deliverables --}}
                @php
                    $deliverables = is_array($problem->deliverables) ? $problem->deliverables : json_decode($problem->deliverables, true) ?? [];
                @endphp
                @if(count($deliverables) > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Deliverables</h2>
                    <ul class="list-disc list-inside space-y-2 text-gray-700">
                        @foreach($deliverables as $deliverable)
                            <li>{{ $deliverable }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- facilities provided --}}
                @php
                    $facilities = is_array($problem->facilities_provided) ? $problem->facilities_provided : json_decode($problem->facilities_provided, true) ?? [];
                @endphp
                @if(count($facilities) > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Fasilitas Yang Disediakan</h2>
                    <ul class="list-disc list-inside space-y-2 text-gray-700">
                        @foreach($facilities as $facility)
                            <li>{{ $facility }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

            </div>

            {{-- sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                
                {{-- status & info card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Status & Informasi</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Status</p>
                            @if($problem->status == 'draft')
                                <span class="inline-flex px-3 py-1 bg-gray-100 text-gray-700 text-sm font-semibold rounded-full">Draft</span>
                            @elseif($problem->status == 'open')
                                <span class="inline-flex px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">Terbuka</span>
                            @elseif($problem->status == 'in_progress')
                                <span class="inline-flex px-3 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">Berjalan</span>
                            @elseif($problem->status == 'completed')
                                <span class="inline-flex px-3 py-1 bg-purple-100 text-purple-700 text-sm font-semibold rounded-full">Selesai</span>
                            @else
                                <span class="inline-flex px-3 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded-full">Ditutup</span>
                            @endif
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 mb-1">Tingkat Kesulitan</p>
                            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                {{ $problem->difficulty_level == 'beginner' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $problem->difficulty_level == 'intermediate' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $problem->difficulty_level == 'advanced' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ ucfirst($problem->difficulty_level) }}
                            </span>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 mb-1">Mahasiswa Dibutuhkan</p>
                            <p class="font-semibold text-gray-900">{{ $problem->required_students }} Orang</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-600 mb-1">Aplikasi Diterima</p>
                            <p class="font-semibold text-gray-900">{{ $problem->accepted_students }}/{{ $problem->required_students }}</p>
                        </div>
                    </div>
                </div>

                {{-- timeline card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Timeline</h3>
                    
                    <div class="space-y-4 text-sm">
                        <div>
                            <p class="text-gray-600 mb-1">Deadline Aplikasi</p>
                            <p class="font-semibold text-gray-900">{{ $problem->application_deadline->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">Mulai Proyek</p>
                            <p class="font-semibold text-gray-900">{{ $problem->start_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">Selesai Proyek</p>
                            <p class="font-semibold text-gray-900">{{ $problem->end_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 mb-1">Durasi</p>
                            <p class="font-semibold text-gray-900">{{ $problem->duration_months }} Bulan</p>
                        </div>
                    </div>
                </div>

                {{-- requirements card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Requirements</h3>
                    
                    @php
                        $requiredSkills = is_array($problem->required_skills) ? $problem->required_skills : json_decode($problem->required_skills, true) ?? [];
                    @endphp
                    @if(count($requiredSkills) > 0)
                    <div class="mb-4">
                        <p class="text-sm font-semibold text-gray-700 mb-2">Skill Yang Dibutuhkan:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($requiredSkills as $skill)
                                <span class="inline-flex px-3 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @php
                        $requiredMajors = is_array($problem->required_majors) ? $problem->required_majors : json_decode($problem->required_majors, true) ?? [];
                    @endphp
                    @if(count($requiredMajors) > 0)
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-2">Jurusan Yang Dibutuhkan:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($requiredMajors as $major)
                                <span class="inline-flex px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">
                                    {{ $major }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- statistik card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Statistik</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Views</span>
                            <span class="font-semibold text-gray-900">{{ $problem->views_count }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Aplikasi</span>
                            <span class="font-semibold text-gray-900">{{ $problem->applications_count }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Diterima</span>
                            <span class="font-semibold text-green-600">{{ $problem->accepted_students }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection