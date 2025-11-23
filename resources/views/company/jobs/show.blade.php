@extends('layouts.app')

@section('title', $jobPosting['title'] . ' - ' . $company->name)

@push('styles')
<style>
    .stat-item {
        transition: transform 0.2s ease, background-color 0.2s ease;
    }

    .stat-item:hover {
        transform: translateY(-2px);
        background-color: #f9fafb;
    }

    .action-button {
        transition: all 0.2s ease;
    }

    .action-button:hover {
        transform: translateY(-1px);
    }

    .linkedin-btn {
        background-color: #0077b5;
    }

    .linkedin-btn:hover {
        background-color: #006399;
    }

    @media (prefers-reduced-motion: reduce) {
        .stat-item,
        .action-button {
            transition: none;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" x-data="jobShowPage()">

    {{-- header bar --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-4">
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('company.dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('company.jobs.index') }}" class="text-gray-500 hover:text-gray-700">Jobs</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-gray-900 font-medium">{{ Str::limit($jobPosting['title'], 40) }}</span>
            </nav>
        </div>
    </div>

    {{-- main content card --}}
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-8">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- header section --}}
            <div class="px-8 py-6 border-b border-gray-200">
                <div class="flex items-start justify-between gap-6">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $jobPosting['title'] }}</h1>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span>Posted {{ \Carbon\Carbon::parse($jobPosting['created_at'])->diffForHumans() }}</span>
                            <span>•</span>
                            <span>{{ number_format($statistics['total_views'] ?? 0) }} views</span>
                            <span>•</span>
                            <span>{{ $jobPosting['total_applicants'] }} applicants</span>
                            <span>•</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Active
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('company.jobs.edit', $jobPosting['id']) }}"
                           class="action-button inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Job
                        </a>
                        <button @click="shareToLinkedIn()"
                                class="action-button linkedin-btn inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                            Share on LinkedIn
                        </button>
                        <button @click="shareModalOpen = true"
                                class="action-button inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            Share
                        </button>
                    </div>
                </div>
            </div>

            {{-- stats section --}}
            <div class="px-8 py-6 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                    <div class="stat-item p-4 rounded-lg bg-white border border-gray-200">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 text-center">{{ $statistics['applications_received'] ?? 0 }}</p>
                        <p class="text-xs text-gray-600 text-center mt-1">Applications</p>
                    </div>

                    <div class="stat-item p-4 rounded-lg bg-white border border-gray-200">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 text-center">{{ $statistics['shortlisted'] ?? 0 }}</p>
                        <p class="text-xs text-gray-600 text-center mt-1">Shortlisted</p>
                    </div>

                    <div class="stat-item p-4 rounded-lg bg-white border border-gray-200">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 text-center">{{ $statistics['messaged'] ?? 0 }}</p>
                        <p class="text-xs text-gray-600 text-center mt-1">Messaged</p>
                    </div>

                    <div class="stat-item p-4 rounded-lg bg-white border border-gray-200">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 text-center">{{ $statistics['offers_extended'] ?? 0 }}</p>
                        <p class="text-xs text-gray-600 text-center mt-1">Offers</p>
                    </div>

                    <div class="stat-item p-4 rounded-lg bg-white border border-gray-200">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 text-center">{{ $statistics['hired'] ?? 0 }}</p>
                        <p class="text-xs text-gray-600 text-center mt-1">Hired</p>
                    </div>

                    <div class="stat-item p-4 rounded-lg bg-white border border-gray-200">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 text-center">{{ number_format($statistics['total_views'] ?? 0) }}</p>
                        <p class="text-xs text-gray-600 text-center mt-1">Views</p>
                    </div>
                </div>
            </div>

            {{-- main content --}}
            <div class="px-8 py-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    {{-- left section (2/3 width) --}}
                    <div class="lg:col-span-2 space-y-8">

                        {{-- recent applicants --}}
                        @if(count($recentApplicants) > 0)
                        <section>
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900">Recent Applicants</h2>
                                <a href="{{ route('company.applications.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                    View All ({{ $jobPosting['total_applicants'] }}) →
                                </a>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex items-center -space-x-3">
                                    @foreach($recentApplicants as $index => $applicant)
                                        @if($index < 5)
                                            @if(!empty($applicant['avatar']))
                                                <img src="{{ asset('storage/profiles/' . $applicant['avatar']) }}"
                                                     alt="{{ $applicant['name'] }}"
                                                     class="w-12 h-12 rounded-full border-3 border-white object-cover ring-2 ring-gray-100"
                                                     title="{{ $applicant['name'] }}">
                                            @else
                                                <div class="w-12 h-12 rounded-full border-3 border-white bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center ring-2 ring-gray-100"
                                                     title="{{ $applicant['name'] }}">
                                                    @php
                                                        $nameParts = explode(' ', $applicant['name']);
                                                        $initials = strtoupper(substr($nameParts[0], 0, 1) . (count($nameParts) > 1 ? substr($nameParts[count($nameParts)-1], 0, 1) : ''));
                                                    @endphp
                                                    <span class="text-white text-sm font-bold">{{ $initials }}</span>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                    @if(count($recentApplicants) > 5)
                                        <div class="w-12 h-12 rounded-full border-3 border-white bg-gray-200 flex items-center justify-center ring-2 ring-gray-100">
                                            <span class="text-gray-600 text-sm font-medium">+{{ count($recentApplicants) - 5 }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 flex-1">
                                    @php
                                        $newCount = collect($recentApplicants)->where('is_new', true)->count();
                                    @endphp
                                    @if($newCount > 0)
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                            {{ $newCount }} New
                                        </span>
                                    @endif
                                    <span class="text-sm text-gray-600">
                                        {{ $jobPosting['reviewed_count'] }}/{{ $jobPosting['total_applicants'] }} reviewed
                                    </span>
                                </div>

                                <a href="{{ route('company.applications.index') }}"
                                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    Review Applicants
                                </a>
                            </div>
                        </section>
                        @endif

                        {{-- job description --}}
                        <section>
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900">Job Description</h2>
                                <a href="{{ route('company.jobs.edit', $jobPosting['id']) }}" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>

                            <div class="prose prose-gray max-w-none">
                                <p class="text-gray-700 leading-relaxed">{{ $jobPosting['description'] }}</p>

                                @if(!empty($jobPosting['responsibilities']))
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Key Responsibilities</h3>
                                    <ul class="space-y-2">
                                        @foreach(explode("\n", $jobPosting['responsibilities']) as $responsibility)
                                            @if(trim($responsibility))
                                                <li class="flex items-start gap-2 text-gray-700">
                                                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span>{{ trim($responsibility) }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </section>

                        {{-- share section --}}
                        <section class="pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Share This Job</h3>
                            <div class="flex items-center gap-3">
                                <input type="text"
                                       value="{{ $jobPosting['share_url'] }}"
                                       readonly
                                       class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-600 focus:outline-none">
                                <button @click="copyShareUrl()"
                                        class="px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                    Copy Link
                                </button>
                                <button @click="shareToLinkedIn()"
                                        class="linkedin-btn px-4 py-2.5 text-white rounded-lg text-sm font-medium">
                                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                    </svg>
                                    LinkedIn
                                </button>
                            </div>
                        </section>

                    </div>

                    {{-- right section (1/3 width) --}}
                    <div class="space-y-6">

                        {{-- quick actions --}}
                        <section class="space-y-3">
                            <button @click="inviteModalOpen = true"
                                    class="w-full py-2.5 px-4 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                                Invite Candidates
                            </button>

                            <button @click="linkedinReferenceOpen = true"
                                    class="w-full py-2.5 px-4 linkedin-btn text-white rounded-lg text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                                Add LinkedIn Reference
                            </button>

                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg bg-gray-50">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Guest View Only</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Guests can view but not apply</p>
                                </div>
                                <button @click="allowGuestView = !allowGuestView"
                                        :class="allowGuestView ? 'bg-blue-600' : 'bg-gray-300'"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                    <span :class="allowGuestView ? 'translate-x-6' : 'translate-x-1'"
                                          class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                                </button>
                            </div>
                        </section>

                        {{-- job details --}}
                        <section class="pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Job Details</h3>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Budget</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ !empty($jobPosting['budget']) ? $jobPosting['budget'] : 'Not specified' }}
                                    </p>
                                    @if(!empty($jobPosting['budget_type']))
                                        <p class="text-sm text-gray-600">{{ $jobPosting['budget_type'] }}</p>
                                    @endif
                                </div>

                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Delivery Time</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ !empty($jobPosting['delivery_time']) ? $jobPosting['delivery_time'] : 'Flexible' }}
                                    </p>
                                </div>

                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Positions</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ !empty($jobPosting['individual_hires']) ? $jobPosting['individual_hires'] . ' hire(s)' : '1 hire' }}
                                    </p>
                                </div>

                                @if(!empty($jobPosting['tags']) && is_array($jobPosting['tags']))
                                <div class="border-t border-gray-100 pt-4">
                                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Required Skills</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($jobPosting['tags'] as $tag)
                                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-medium">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <div class="border-t border-gray-100 pt-4 space-y-2">
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                        </svg>
                                        <span>{{ $jobPosting['language'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $jobPosting['timezone'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- job owner --}}
                        @if(!empty($jobPosting['owner']) && !empty($jobPosting['owner']['name']))
                        <section class="pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Job Owner</h3>

                            <div class="flex items-center gap-3">
                                @if(!empty($jobPosting['owner']['avatar']))
                                    <img src="{{ asset('storage/profiles/' . $jobPosting['owner']['avatar']) }}"
                                         alt="{{ $jobPosting['owner']['name'] }}"
                                         class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                        @php
                                            $nameParts = explode(' ', $jobPosting['owner']['name']);
                                            $initials = strtoupper(substr($nameParts[0], 0, 1) . (count($nameParts) > 1 ? substr($nameParts[count($nameParts)-1], 0, 1) : ''));
                                        @endphp
                                        <span class="text-white text-sm font-bold">{{ $initials }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $jobPosting['owner']['name'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $jobPosting['owner']['role'] ?? 'Job Owner' }}</p>
                                </div>
                            </div>
                        </section>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- share modal --}}
    <div x-show="shareModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="shareModalOpen = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="shareModalOpen = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Share Job Posting</h3>
                <div class="space-y-4">
                    <input type="text" value="{{ $jobPosting['share_url'] }}" readonly
                           class="w-full rounded-lg border-gray-300 bg-gray-50 text-sm">
                    <div class="flex gap-3">
                        <button @click="copyShareUrl(); shareModalOpen = false"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                            Copy Link
                        </button>
                        <button @click="shareToLinkedIn(); shareModalOpen = false"
                                class="flex-1 linkedin-btn px-4 py-2 text-white rounded-lg font-medium">
                            LinkedIn
                        </button>
                        <button @click="shareModalOpen = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- invite modal --}}
    <div x-show="inviteModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="inviteModalOpen = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="inviteModalOpen = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Invite Candidates</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" x-model="inviteEmail" placeholder="candidate@example.com"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message (Optional)</label>
                        <textarea x-model="inviteMessage" rows="3" placeholder="Add a personal message..."
                                  class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button @click="sendInvite()"
                                class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            Send Invite
                        </button>
                        <button @click="inviteModalOpen = false"
                                class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- linkedin reference modal --}}
    <div x-show="linkedinReferenceOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="linkedinReferenceOpen = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="linkedinReferenceOpen = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full p-6">
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900">Add LinkedIn Reference & SDG Requirements</h3>
                </div>

                <form action="{{ route('company.jobs.update-linkedin', $jobPosting['id']) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn Job URL (Optional)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                            <input type="url" name="linkedin_url" x-model="linkedinJobUrl" placeholder="https://www.linkedin.com/jobs/view/..."
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Link to your LinkedIn job posting as reference</p>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Required SDG Goals for KKN Students</label>
                        <p class="text-xs text-gray-600 mb-3">Select the SDG goals that students must have experience with from their KKN projects</p>

                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="sdg in [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17]" :key="sdg">
                                <label class="flex items-center p-2.5 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                                       :class="selectedSDGs.includes(sdg) ? 'border-blue-500 bg-blue-50' : 'border-gray-300'">
                                    <input type="checkbox" :name="'sdg_goals[]'" :value="sdg"
                                           @change="toggleSDG(sdg)"
                                           :checked="selectedSDGs.includes(sdg)"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                    <span class="text-sm font-medium text-gray-900">SDG <span x-text="sdg"></span></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save
                        </button>
                        <button type="button" @click="linkedinReferenceOpen = false"
                                class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function jobShowPage() {
    return {
        allowGuestView: {{ $jobPosting['allow_guest_applications'] ? 'true' : 'false' }},
        shareModalOpen: false,
        inviteModalOpen: false,
        linkedinReferenceOpen: false,
        inviteEmail: '',
        inviteMessage: '',
        linkedinJobUrl: '',
        selectedSDGs: [],
        shareUrl: '{{ $jobPosting['share_url'] }}',
        jobTitle: '{{ $jobPosting['title'] }}',

        copyShareUrl() {
            navigator.clipboard.writeText(this.shareUrl).then(() => {
                alert('Link copied to clipboard!');
            }).catch(() => {
                alert('Failed to copy link');
            });
        },

        shareToLinkedIn() {
            // LinkedIn share URL format
            const linkedInUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(this.shareUrl)}`;
            window.open(linkedInUrl, '_blank', 'width=600,height=400');
        },

        async sendInvite() {
            if (!this.inviteEmail) {
                alert('Please enter an email address');
                return;
            }

            // TODO: Send invite via API
            console.log('Sending invite to:', this.inviteEmail, 'Message:', this.inviteMessage);

            this.inviteModalOpen = false;
            this.inviteEmail = '';
            this.inviteMessage = '';
            alert('Invitation sent successfully!');
        },

        toggleSDG(sdgNumber) {
            const index = this.selectedSDGs.indexOf(sdgNumber);
            if (index === -1) {
                this.selectedSDGs.push(sdgNumber);
            } else {
                this.selectedSDGs.splice(index, 1);
            }
        }
    }
}
</script>
@endpush
