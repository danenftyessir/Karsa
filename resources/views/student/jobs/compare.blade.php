{{-- resources/views/student/jobs/compare.blade.php --}}
@extends('layouts.app')

@section('title', 'Bandingkan Lowongan')

@push('styles')
<style>
    .compare-table th {
        background: #f9fafb;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .compare-cell {
        vertical-align: top;
        min-width: 280px;
    }
    .highlight-best {
        background-color: #ecfdf5;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Bandingkan Lowongan</h1>
                    <p class="text-gray-600 mt-1">Membandingkan {{ $jobs->count() }} lowongan</p>
                </div>
                <a href="{{ route('student.jobs.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm font-medium">
                    Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>

    {{-- comparison table --}}
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full compare-table">
                    {{-- header row with job cards --}}
                    <thead>
                        <tr>
                            <th class="p-4 text-left text-sm font-medium text-gray-500 border-b border-r border-gray-200 w-40">
                                Kriteria
                            </th>
                            @foreach($jobs as $job)
                            <th class="compare-cell p-4 border-b border-r border-gray-200 last:border-r-0">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                        @if($job->company && $job->company->logo_url)
                                        <img src="{{ $job->company->logo_url }}" alt="" crossorigin="anonymous" class="w-full h-full object-cover rounded-lg">
                                        @else
                                        <span class="text-white font-bold">{{ substr($job->company->name ?? 'C', 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="text-left">
                                        <h3 class="font-semibold text-gray-900">{{ $job->title }}</h3>
                                        <p class="text-sm text-gray-600">{{ $job->company->name ?? 'Company' }}</p>
                                    </div>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        {{-- tipe pekerjaan --}}
                        <tr>
                            <td class="p-4 text-sm font-medium text-gray-700 border-r border-gray-200 bg-gray-50">Tipe Pekerjaan</td>
                            @foreach($jobs as $job)
                            <td class="compare-cell p-4 border-r border-gray-200 last:border-r-0">
                                <span class="px-3 py-1 rounded-full text-sm font-medium
                                    {{ $job->job_type == 'full_time' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $job->job_type == 'part_time' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $job->job_type == 'contract' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $job->job_type == 'internship' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ $job->job_type == 'freelance' ? 'bg-pink-100 text-pink-700' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $job->job_type)) }}
                                </span>
                            </td>
                            @endforeach
                        </tr>

                        {{-- lokasi --}}
                        <tr class="bg-gray-50/50">
                            <td class="p-4 text-sm font-medium text-gray-700 border-r border-gray-200 bg-gray-50">Lokasi</td>
                            @foreach($jobs as $job)
                            <td class="compare-cell p-4 border-r border-gray-200 last:border-r-0">
                                <div class="flex items-center gap-2 text-gray-700">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    {{ $job->location ?? 'Remote / Flexible' }}
                                </div>
                            </td>
                            @endforeach
                        </tr>

                        {{-- gaji --}}
                        <tr>
                            <td class="p-4 text-sm font-medium text-gray-700 border-r border-gray-200 bg-gray-50">Gaji</td>
                            @foreach($jobs as $job)
                            <td class="compare-cell p-4 border-r border-gray-200 last:border-r-0">
                                @if($job->salary_min || $job->salary_max)
                                <span class="text-gray-900 font-medium">{{ $job->salary_range }}</span>
                                @else
                                <span class="text-gray-400">Tidak disebutkan</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        {{-- experience --}}
                        <tr class="bg-gray-50/50">
                            <td class="p-4 text-sm font-medium text-gray-700 border-r border-gray-200 bg-gray-50">Pengalaman</td>
                            @foreach($jobs as $job)
                            <td class="compare-cell p-4 border-r border-gray-200 last:border-r-0">
                                {{ $job->experience_level ?? 'Entry Level' }}
                            </td>
                            @endforeach
                        </tr>

                        {{-- skills --}}
                        <tr>
                            <td class="p-4 text-sm font-medium text-gray-700 border-r border-gray-200 bg-gray-50">Skills</td>
                            @foreach($jobs as $job)
                            <td class="compare-cell p-4 border-r border-gray-200 last:border-r-0">
                                @if($job->skills && count($job->skills) > 0)
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($job->skills as $skill)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded text-xs">{{ $skill }}</span>
                                    @endforeach
                                </div>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        {{-- benefits --}}
                        <tr class="bg-gray-50/50">
                            <td class="p-4 text-sm font-medium text-gray-700 border-r border-gray-200 bg-gray-50">Benefits</td>
                            @foreach($jobs as $job)
                            <td class="compare-cell p-4 border-r border-gray-200 last:border-r-0">
                                @if($job->benefits && count($job->benefits) > 0)
                                <ul class="space-y-1 text-sm text-gray-700">
                                    @foreach(array_slice($job->benefits, 0, 5) as $benefit)
                                    <li class="flex items-start gap-1.5">
                                        <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        {{ $benefit }}
                                    </li>
                                    @endforeach
                                </ul>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        {{-- deadline --}}
                        <tr>
                            <td class="p-4 text-sm font-medium text-gray-700 border-r border-gray-200 bg-gray-50">Deadline</td>
                            @foreach($jobs as $job)
                            <td class="compare-cell p-4 border-r border-gray-200 last:border-r-0">
                                @if($job->expires_at)
                                <span class="{{ $job->expires_at->lt(now()->addDays(7)) ? 'text-red-600 font-medium' : 'text-gray-700' }}">
                                    {{ $job->expires_at->format('d M Y') }}
                                    ({{ $job->expires_at->diffForHumans() }})
                                </span>
                                @else
                                <span class="text-gray-400">Tidak ada batas</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        {{-- action row --}}
                        <tr>
                            <td class="p-4 border-r border-gray-200 bg-gray-50"></td>
                            @foreach($jobs as $job)
                            <td class="compare-cell p-4 border-r border-gray-200 last:border-r-0">
                                <div class="flex gap-2">
                                    <a href="{{ route('student.jobs.show', $job->id) }}"
                                       class="flex-1 text-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                                        Lihat Detail
                                    </a>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
