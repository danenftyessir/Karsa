@extends('layouts.app')

@section('title', 'Kelola Masalah')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Masalah</h1>
                    <p class="mt-2 text-gray-600">Publikasikan dan kelola masalah untuk mahasiswa KKN</p>
                </div>
                <a href="{{ route('institution.problems.create') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-blue-600 to-green-600 text-white rounded-lg hover:from-blue-700 hover:to-green-700 transition-all duration-200 flex items-center gap-2 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Masalah Baru
                </a>
            </div>
        </div>

        {{-- statistik cards --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Total</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Draft</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['draft'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Open</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['open'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">In Progress</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['in_progress'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Completed</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['completed'] }}</p>
            </div>
        </div>

        {{-- filter & search --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6 border border-gray-100">
            <form method="GET" action="{{ route('institution.problems.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[250px]">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari masalah..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>

                <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                    <option value="most_applied" {{ request('sort') == 'most_applied' ? 'selected' : '' }}>Paling Banyak Aplikasi</option>
                </select>

                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200">
                    Filter
                </button>
                <a href="{{ route('institution.problems.index') }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200">
                    Reset
                </a>
            </form>
        </div>

        {{-- problems list --}}
        <div class="space-y-4">
            @forelse($problems as $problem)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-md">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                {{-- title & status --}}
                                <div class="flex items-start gap-3 mb-3">
                                    <h3 class="text-xl font-bold text-gray-900">{{ $problem->title }}</h3>
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $problem->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $problem->status === 'open' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $problem->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $problem->status === 'completed' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $problem->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst($problem->status) }}
                                    </span>
                                </div>

                                {{-- description --}}
                                <p class="text-gray-600 mb-4">{{ Str::limit($problem->description, 150) }}</p>

                                {{-- meta info --}}
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $problem->regency->name }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        {{ $problem->required_students }} mahasiswa
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ $problem->applications_count }} aplikasi
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Deadline: {{ $problem->application_deadline->format('d M Y') }}
                                    </div>
                                </div>
                            </div>

                            {{-- actions --}}
                            <div class="flex items-center gap-2">
                                <a href="{{ route('institution.problems.show', $problem->id) }}" 
                                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 text-sm">
                                    Detail
                                </a>
                                <a href="{{ route('institution.problems.edit', $problem->id) }}" 
                                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 text-sm">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow-sm p-12 text-center border border-gray-100">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Masalah</h3>
                    <p class="text-gray-600 mb-4">Mulai publikasikan masalah untuk mendapatkan bantuan mahasiswa KKN</p>
                    <a href="{{ route('institution.problems.create') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Masalah Baru
                    </a>
                </div>
            @endforelse
        </div>

        {{-- pagination --}}
        @if($problems->hasPages())
            <div class="mt-8">
                {{ $problems->links() }}
            </div>
        @endif

    </div>
</div>
@endsection