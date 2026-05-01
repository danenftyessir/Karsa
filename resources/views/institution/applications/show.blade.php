{{-- resources/views/institution/applications/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Aplikasi')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- breadcrumb --}}
        <nav class="mb-6">
            <ol class="flex items-center gap-2 text-sm">
                <li><a href="{{ route('institution.dashboard') }}" class="text-gray-500 hover:text-gray-700 transition-colors">Dashboard</a></li>
                <li class="text-gray-400">/</li>
                <li><a href="{{ route('institution.applications.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">Aplikasi</a></li>
                <li class="text-gray-400">/</li>
                <li class="text-gray-900 font-medium">Detail</li>
            </ol>
        </nav>

        {{-- header section --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start gap-4">
                    {{-- PERBAIKAN BUG: gunakan accessor profile_photo_url yang sudah support Supabase --}}
                    <img src="{{ $application->student->profile_photo_url }}" 
                         alt="{{ $application->student->user->name }}"
                         class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                    
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-1">{{ $application->student->user->name }}</h1>
                        <p class="text-gray-600">{{ $application->student->university->name }}</p>
                        <p class="text-sm text-gray-500">{{ $application->student->major }}</p>
                    </div>
                </div>

                <span class="inline-flex px-4 py-2 text-sm font-semibold rounded-full
                    {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $application->status === 'under_review' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $application->status === 'accepted' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $application->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                </span>
            </div>
        </div>

        {{-- student info --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Informasi Mahasiswa</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">NIM</p>
                    <p class="font-medium text-gray-900">{{ $application->student->nim }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Semester</p>
                    <p class="font-medium text-gray-900">{{ $application->student->semester }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Email</p>
                    <p class="font-medium text-gray-900">{{ $application->student->user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">WhatsApp</p>
                    <p class="font-medium text-gray-900">{{ $application->student->phone ?? '-' }}</p>
                </div>
            </div>

            @if($application->student->bio)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-500 mb-2">Bio</p>
                <p class="text-gray-700 leading-relaxed">{{ $application->student->bio }}</p>
            </div>
            @endif
        </div>

        {{-- problem info --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Masalah Yang Dilamar</h2>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                <h3 class="font-semibold text-gray-900 mb-2">{{ $application->problem->title }}</h3>
                <p class="text-sm text-gray-600">{{ Str::limit($application->problem->description, 200) }}</p>
                <a href="{{ route('institution.problems.show', $application->problem->id) }}" 
                   class="inline-flex items-center gap-2 mt-3 text-blue-600 hover:text-blue-700 text-sm font-medium transition-colors">
                    Lihat Detail Masalah
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- motivation --}}
        @if($application->motivation)
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Motivasi</h2>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $application->motivation }}</p>
            </div>
        </div>
        @endif

    {{-- proposal document download --}}
    @if($application->proposal_content)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Proposal</h2>
        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-medium text-gray-900">{{ $application->proposal_filename ?? 'Proposal.pdf' }}</p>
                <p class="text-sm text-gray-500">
                    {{ $application->proposal_size_formatted ?? 'PDF Document' }} • 
                    Diupload {{ $application->created_at->diffForHumans() }}
                </p>
            </div>
            <a href="{{ route('institution.applications.download-proposal', $application->id) }}"
            target="_blank"
            download
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download
            </a>
        </div>
    </div>
    @endif
    
        {{-- feedback dari instansi (jika ada) --}}
        @if($application->feedback)
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Feedback</h2>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                <p class="text-blue-700">{{ $application->feedback }}</p>
            </div>
        </div>
        @endif

        {{-- institution notes (internal) --}}
        @if($application->institution_notes)
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Catatan Internal</h2>
            <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                <p class="text-yellow-700">{{ $application->institution_notes }}</p>
            </div>
        </div>
        @endif

        {{-- timeline --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Timeline</h2>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Aplikasi Diajukan</p>
                        <p class="text-sm text-gray-500">{{ $application->applied_at ? $application->applied_at->format('d F Y, H:i') : $application->created_at->format('d F Y, H:i') }}</p>
                    </div>
                </div>

                @if($application->reviewed_at)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Aplikasi Direview</p>
                        <p class="text-sm text-gray-500">{{ $application->reviewed_at->format('d F Y, H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- action buttons --}}
        @if(in_array($application->status, ['pending', 'under_review']))
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Tindakan</h2>
            <div class="flex gap-3">
                <form method="POST" action="{{ route('institution.applications.accept', $application->id) }}" class="flex-1">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Yakin ingin menerima aplikasi ini?')"
                            class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all font-semibold transform hover:scale-105">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Terima Aplikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('institution.applications.reject', $application->id) }}" class="flex-1">
                    @csrf
                    <button type="submit" 
                            onclick="return confirm('Yakin ingin menolak aplikasi ini?')"
                            class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all font-semibold transform hover:scale-105">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Tolak Aplikasi
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection