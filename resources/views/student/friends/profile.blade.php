@extends('layouts.app')

@section('title', $student->user->name . ' - Profil')

@section('content')
{{-- profile header dengan background --}}
<div class="relative bg-white">
    {{-- cover photo --}}
    <div class="h-64 relative overflow-hidden">
        <img src="{{ asset('placeholder-profile-cover.jpg') }}" 
             alt="Cover Photo" 
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-black/30"></div>
    </div>

    {{-- profile info section --}}
    <div class="container mx-auto px-6">
        <div class="relative -mt-24 pb-6">
            <div class="flex flex-col md:flex-row items-start md:items-end gap-6">
                {{-- profile photo --}}
                <img src="{{ $student->profile_photo_url }}"
                     alt="{{ $student->user->name }}"
                     class="w-40 h-40 rounded-2xl border-4 border-white shadow-xl object-cover">

                {{-- name and headline --}}
                <div class="flex-1 bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        {{ $student->user->name }}
                    </h1>
                    <p class="text-xl text-gray-700 mb-2">
                        {{ $student->major }}
                    </p>
                    <p class="text-gray-600 mb-4">
                        {{ $student->university->name }} · Semester {{ $student->semester }}
                    </p>

                    {{-- action buttons --}}
                    <div class="flex flex-wrap gap-3">
                        @if($friendshipStatus === 'friends')
                        <button disabled 
                                class="px-6 py-2.5 bg-green-50 text-green-700 font-medium rounded-lg cursor-not-allowed">
                            <svg class="w-5 h-5 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Terhubung
                        </button>
                        @elseif($friendshipStatus === 'pending_sent')
                        <button disabled 
                                class="px-6 py-2.5 bg-yellow-50 text-yellow-700 font-medium rounded-lg cursor-not-allowed">
                            <svg class="w-5 h-5 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            Menunggu Respons
                        </button>
                        @elseif($friendshipStatus === 'pending_received')
                        <a href="{{ route('student.friends.index') }}" 
                           class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            Lihat Permintaan
                        </a>
                        @else
                        <form method="POST" action="{{ route('student.friends.send-request', $student->id) }}">
                            @csrf
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                Hubungkan
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('student.friends.search') }}" 
                           class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            Kembali Ke Pencarian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- main content --}}
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-6 py-8">
        <div class="flex flex-col lg:flex-row gap-6">
            
            {{-- sidebar kiri --}}
            <div class="lg:w-1/3 space-y-6">
                
                {{-- statistics card --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Statistik</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Koneksi</span>
                            <span class="text-xl font-bold text-blue-600">{{ $stats['friends_count'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Proyek Selesai</span>
                            <span class="text-xl font-bold text-green-600">{{ $stats['completed_projects'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Proyek</span>
                            <span class="text-xl font-bold text-purple-600">{{ $stats['total_projects'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- mutual friends --}}
                @if($mutualFriends->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        Koneksi Bersama ({{ $mutualFriends->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($mutualFriends as $mutual)
                        <a href="{{ route('student.friends.profile', $mutual->id) }}"
                           class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                            <img src="{{ $mutual->profile_photo_url }}"
                                 alt="{{ $mutual->user->name }}"
                                 class="w-10 h-10 rounded-full object-cover">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 text-sm truncate">
                                    {{ $mutual->user->name }}
                                </p>
                                <p class="text-xs text-gray-600 truncate">
                                    {{ $mutual->major }}
                                </p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- contact info --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Kontak</h3>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Email</p>
                                <p class="text-gray-900">{{ $student->user->email }}</p>
                            </div>
                        </div>
                        @if($student->whatsapp_number)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">WhatsApp</p>
                                <p class="text-gray-900">{{ $student->whatsapp_number }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- main content area --}}
            <div class="lg:w-2/3 space-y-6">
                
                {{-- about section --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Tentang</h2>
                    @if($student->bio)
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                        {{ $student->bio }}
                    </p>
                    @else
                    <p class="text-gray-500 italic">
                        Belum ada informasi tentang mahasiswa ini.
                    </p>
                    @endif
                </div>

                {{-- skills section --}}
                @if($student->skills && is_array($student->skills) && count($student->skills) > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Keahlian</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($student->skills as $skill)
                        <span class="px-4 py-2 bg-blue-50 text-blue-700 font-medium rounded-lg border border-blue-200">
                            {{ $skill }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- experience / projects section --}}
                @if($student->portfolio_visible && $stats['completed_projects'] > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        Pengalaman Proyek KKN
                    </h2>
                    <div class="space-y-6">
                        @foreach($student->projects()->where('status', 'completed')->limit(5)->get() as $project)
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h3 class="font-semibold text-gray-900 text-lg mb-1">
                                {{ $project->problem->title }}
                            </h3>
                            <p class="text-sm text-gray-600 mb-2">
                                {{ $project->problem->institution->name }}
                            </p>
                            <p class="text-sm text-gray-700 mb-2">
                                {{ $project->problem->province->name }}, {{ $project->problem->regency->name }}
                            </p>
                            @if($project->problem->sdgs && count($project->problem->sdgs) > 0)
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach($project->problem->sdgs as $sdg)
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">
                                    SDG {{ $sdg->number }}: {{ $sdg->name }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- activity / applications --}}
                @if($student->portfolio_visible && $stats['total_applications'] > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">
                        Aktivitas Terbaru
                    </h2>
                    <div class="space-y-4">
                        @foreach($student->applications()->latest()->limit(5)->get() as $application)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0 mt-1">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-700">
                                    Melamar untuk proyek:
                                    <span class="font-semibold">{{ $application->problem->title }}</span>
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $application->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection