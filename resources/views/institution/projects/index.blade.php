@extends('layouts.app')

@section('title', 'Kelola Proyek')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Kelola Proyek</h1>
            <p class="text-gray-600 mt-1">Monitor dan kelola proyek yang sedang berjalan</p>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Planning</p>
                        <p class="text-2xl font-bold text-gray-500">{{ $stats['planning'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Aktif</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Review</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['review'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Selesai</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['completed'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- filter dan search --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('institution.projects.index') }}" class="flex flex-wrap gap-4">
                {{-- search --}}
                <div class="flex-1 min-w-[250px]">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari nama mahasiswa atau judul proyek..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- filter status --}}
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>Review</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
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
                    <option value="progress" {{ request('sort') == 'progress' ? 'selected' : '' }}>Progress</option>
                    <option value="deadline" {{ request('sort') == 'deadline' ? 'selected' : '' }}>Deadline</option>
                </select>

                {{-- submit button --}}
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Filter
                </button>

                {{-- reset button --}}
                @if(request()->hasAny(['search', 'status', 'problem_id', 'sort']))
                <a href="{{ route('institution.projects.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                    Reset
                </a>
                @endif
            </form>
        </div>

        {{-- daftar proyek --}}
        <div class="space-y-4">
            @forelse($projects as $project)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-start gap-4">
                    {{-- avatar --}}
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-full flex items-center justify-center font-bold text-xl flex-shrink-0">
                        {{ substr($project->student->user->name, 0, 1) }}
                    </div>

                    {{-- info --}}
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $project->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $project->student->user->name }} - {{ $project->student->university->name }}</p>
                            </div>

                            {{-- status badge --}}
                            <div class="flex items-center gap-2">
                                @if($project->status == 'planning')
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-semibold rounded-full">Planning</span>
                                @elseif($project->status == 'active')
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">Aktif</span>
                                @elseif($project->status == 'review')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded-full">Review</span>
                                @elseif($project->status == 'completed')
                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">Selesai</span>
                                @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded-full">Dibatalkan</span>
                                @endif
                            </div>
                        </div>

                        {{-- progress bar --}}
                        <div class="mb-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-semibold text-gray-700">Progress</span>
                                <span class="text-gray-600">{{ round($project->progress_percentage ?? 0) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $project->progress_percentage ?? 0 }}%"></div>
                            </div>
                        </div>

                        {{-- meta info --}}
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-3">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $project->start_date->format('d M Y') }} - {{ $project->end_date->format('d M Y') }}
                            </div>

                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                {{ $project->milestones->count() }} milestone
                            </div>

                            @if($project->rating)
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ $project->rating }}/5
                            </div>
                            @endif
                        </div>

                        {{-- actions --}}
                        <div class="flex gap-2">
                            <a href="{{ route('institution.projects.show', $project->id) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold">
                                Lihat Detail
                            </a>
                            <a href="{{ route('institution.projects.manage', $project->id) }}" 
                               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-semibold">
                                Kelola
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-600 text-lg">Belum ada proyek yang berjalan</p>
            </div>
            @endforelse
        </div>

        {{-- pagination --}}
        @if($projects->hasPages())
        <div class="mt-8">
            {{ $projects->links() }}
        </div>
        @endif

    </div>
</div>
@endsection