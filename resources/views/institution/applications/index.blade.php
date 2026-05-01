@extends('layouts.app')

@section('title', 'Review Aplikasi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Review Aplikasi Mahasiswa</h1>
            <p class="text-gray-600 mt-1">Kelola dan review aplikasi yang masuk</p>
        </div>

        {{-- statistik cards --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Review</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['under_review'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Diterima</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['accepted'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Ditolak</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- filter dan search --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('institution.applications.index') }}" class="flex flex-wrap gap-4">
                {{-- search --}}
                <div class="flex-1 min-w-[250px]">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nama mahasiswa..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- filter status --}}
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>

                {{-- filter problem --}}
                <select name="problem_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Masalah</option>
                    @foreach($problems as $prob)
                    <option value="{{ $prob->id }}" {{ request('problem_id') == $prob->id ? 'selected' : '' }}>
                        {{ Str::limit($prob->title, 40) }}
                    </option>
                    @endforeach
                </select>

                {{-- sorting --}}
                <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    <option value="problem" {{ request('sort') == 'problem' ? 'selected' : '' }}>Berdasarkan Masalah</option>
                </select>

                {{-- submit button --}}
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Filter
                </button>

                {{-- reset button --}}
                @if(request()->hasAny(['search', 'status', 'problem_id', 'sort']))
                <a href="{{ route('institution.applications.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                    Reset
                </a>
                @endif
            </form>
        </div>

        {{-- daftar aplikasi --}}
        <div class="space-y-4">
            @forelse($applications as $application)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-start gap-4">
                    {{-- avatar --}}
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl flex-shrink-0">
                        {{ substr($application->student->user->name, 0, 1) }}
                    </div>

                    {{-- info --}}
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $application->student->user->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $application->student->university->name }}</p>
                                <p class="text-xs text-gray-500">{{ $application->student->major }} - Semester {{ $application->student->semester }}</p>
                            </div>

                            {{-- status badge --}}
                            <div class="flex items-center gap-2">
                                @if($application->status == 'pending')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded-full">Pending</span>
                                @elseif($application->status == 'under_review')
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">Under Review</span>
                                @elseif($application->status == 'accepted')
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">Diterima</span>
                                @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded-full">Ditolak</span>
                                @endif
                            </div>
                        </div>

                        {{-- problem info --}}
                        <div class="bg-gray-50 rounded-lg p-3 mb-3">
                            <p class="text-sm font-semibold text-gray-700">Untuk Masalah:</p>
                            <p class="text-sm text-gray-900">{{ $application->problem->title }}</p>
                        </div>

                        {{-- motivation preview --}}
                        @if($application->motivation)
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $application->motivation }}</p>
                        @endif

                        {{-- meta info --}}
                        <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                            <span>Diajukan: {{ $application->created_at->diffForHumans() }}</span>
                            @if($application->reviewed_at)
                            <span>Direview: {{ $application->reviewed_at->diffForHumans() }}</span>
                            @endif
                        </div>

                        {{-- actions --}}
                        <div class="flex gap-2">
                            <a href="{{ route('institution.applications.show', $application->id) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold">
                                Lihat Detail
                            </a>

                            @if(in_array($application->status, ['pending', 'under_review']))
                            <form method="POST" action="{{ route('institution.applications.accept', $application->id) }}" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('Yakin ingin menerima aplikasi ini?')"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-semibold">
                                    Terima
                                </button>
                            </form>

                            <a href="{{ route('institution.applications.review', $application->id) }}" 
                               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-semibold">
                                Review Detail
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-600 text-lg">Belum ada aplikasi yang masuk</p>
            </div>
            @endforelse
        </div>

        {{-- pagination --}}
        @if($applications->hasPages())
        <div class="mt-8">
            {{ $applications->links() }}
        </div>
        @endif

    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection