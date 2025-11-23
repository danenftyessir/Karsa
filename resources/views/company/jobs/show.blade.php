@extends('layouts.app')

@section('title', $jobPosting['title'] . ' - ' . $company->name)

@push('styles')
<style>
    /* optimisasi performa dengan GPU acceleration */
    .tab-item {
        transition: color 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                    border-color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateZ(0);
    }

    .stat-item {
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateZ(0);
        backface-visibility: hidden;
    }

    .stat-item:hover {
        transform: translateY(-2px) translateZ(0);
    }

    .action-btn {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateZ(0);
    }

    .action-btn:hover {
        transform: translateY(-1px) translateZ(0);
    }

    /* smooth toggle animation */
    .toggle-switch {
        transition: background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .toggle-dot {
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* applicant avatars stack */
    .avatar-stack img {
        transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .avatar-stack img:hover {
        transform: scale(1.1) translateZ(0);
        z-index: 10;
    }

    /* respek reduced motion untuk aksesibilitas */
    @media (prefers-reduced-motion: reduce) {
        .tab-item,
        .stat-item,
        .action-btn,
        .toggle-switch,
        .toggle-dot,
        .avatar-stack img {
            transition: none;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" x-data="jobShowPage()">

    <!-- header section -->
    <div class="relative bg-cover bg-center text-white py-12 overflow-hidden" style="background-image: url('{{ asset('company4.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- breadcrumb dan title -->
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-sm text-blue-100 mb-2">
                        <a href="{{ route('company.jobs.index') }}" class="hover:text-white">Jobs</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-white" style="font-family: 'Space Grotesk', sans-serif;">{{ $jobPosting['title'] }}</h1>
                </div>

                <!-- action buttons -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('company.jobs.edit', $jobPosting['id']) }}"
                       class="action-btn inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Edit
                    </a>
                    <button @click="shareModalOpen = true"
                            class="action-btn inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        Share
                    </button>
                    <button @click="boostJob()"
                            class="action-btn inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Boost Job
                    </button>
                    <div class="flex items-center gap-2 px-3 py-2 bg-green-500 text-white rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Posted
                    </div>
                </div>
            </div>

            <!-- tabs navigation -->
            <div class="mt-6 border-b border-white/20">
                <nav class="flex gap-8 -mb-px">
                    <button @click="activeTab = 'overview'"
                            :class="activeTab === 'overview' ? 'border-white text-white' : 'border-transparent text-blue-100 hover:text-white'"
                            class="tab-item pb-4 px-1 border-b-2 font-medium text-sm">
                        Overview
                    </button>
                    <button @click="activeTab = 'applicants'"
                            :class="activeTab === 'applicants' ? 'border-white text-white' : 'border-transparent text-blue-100 hover:text-white'"
                            class="tab-item pb-4 px-1 border-b-2 font-medium text-sm">
                        Applicants
                    </button>
                    <button @click="activeTab = 'shortlisted'"
                            :class="activeTab === 'shortlisted' ? 'border-white text-white' : 'border-transparent text-blue-100 hover:text-white'"
                            class="tab-item pb-4 px-1 border-b-2 font-medium text-sm">
                        Shortlisted
                    </button>
                    <button @click="activeTab = 'messaged'"
                            :class="activeTab === 'messaged' ? 'border-white text-white' : 'border-transparent text-blue-100 hover:text-white'"
                            class="tab-item pb-4 px-1 border-b-2 font-medium text-sm">
                        Messaged
                    </button>
                    <button @click="activeTab = 'hires'"
                            :class="activeTab === 'hires' ? 'border-white text-white' : 'border-transparent text-blue-100 hover:text-white'"
                            class="tab-item pb-4 px-1 border-b-2 font-medium text-sm">
                        Hires
                    </button>
                    <button @click="activeTab = 'not_a_fit'"
                            :class="activeTab === 'not_a_fit' ? 'border-white text-white' : 'border-transparent text-blue-100 hover:text-white'"
                            class="tab-item pb-4 px-1 border-b-2 font-medium text-sm">
                        Not A Fit
                    </button>
                </nav>
            </div>
        </div>
    </div>

    <!-- main content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- left column (main content) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- allow guest applications -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <button @click="allowGuestApplications = !allowGuestApplications"
                                    :class="allowGuestApplications ? 'bg-primary-600' : 'bg-gray-200'"
                                    class="toggle-switch relative inline-flex h-6 w-11 items-center rounded-full">
                                <span :class="allowGuestApplications ? 'translate-x-6' : 'translate-x-1'"
                                      class="toggle-dot inline-block h-4 w-4 transform rounded-full bg-white"></span>
                            </button>
                            <div>
                                <h3 class="font-semibold text-gray-900">Allow Guest Applications</h3>
                                <p class="text-sm text-gray-500">Allow anyone to apply to this position, whether they're using Talent Sphere or not. Learn more.</p>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- share this job -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Share This Job</h3>
                    <div class="flex items-center gap-3">
                        <input type="text"
                               value="{{ $jobPosting['share_url'] }}"
                               readonly
                               class="flex-1 rounded-lg border-gray-300 bg-gray-50 text-sm text-gray-600">
                        <button @click="copyShareUrl()"
                                class="action-btn inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                            Copy
                        </button>
                        <button class="action-btn inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                            </svg>
                        </button>
                        <button class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- review candidates & invite -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- review candidates -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="font-semibold text-gray-900 mb-1">Review Candidates</h3>
                        <p class="text-sm text-gray-500 mb-4">Review applications and message candidates</p>

                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center avatar-stack">
                                @foreach($recentApplicants as $index => $applicant)
                                    @if(!empty($applicant['avatar']))
                                        <img src="{{ asset('storage/profiles/' . $applicant['avatar']) }}"
                                             alt="{{ $applicant['name'] }}"
                                             class="w-10 h-10 rounded-full border-2 border-white object-cover {{ $index > 0 ? '-ml-3' : '' }}"
                                             style="z-index: {{ count($recentApplicants) - $index }}">
                                    @else
                                        <div class="w-10 h-10 rounded-full border-2 border-white bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center {{ $index > 0 ? '-ml-3' : '' }}"
                                             style="z-index: {{ count($recentApplicants) - $index }}">
                                            @php
                                                $nameParts = explode(' ', $applicant['name']);
                                                $initials = strtoupper(substr($nameParts[0], 0, 1) . (count($nameParts) > 1 ? substr($nameParts[count($nameParts)-1], 0, 1) : ''));
                                            @endphp
                                            <span class="text-white text-sm font-bold">{{ $initials }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="flex items-center gap-2">
                                @php
                                    $newCount = collect($recentApplicants)->where('is_new', true)->count();
                                @endphp
                                @if($newCount > 0)
                                    <span class="px-2 py-1 bg-green-500 text-white text-xs font-medium rounded-full">{{ $newCount }} New</span>
                                @endif
                                <span class="text-sm text-gray-500">{{ $jobPosting['reviewed_count'] }}/{{ $jobPosting['total_applicants'] }} reviewed</span>
                            </div>
                        </div>

                        <a href="{{ route('company.applications.index') }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            View All Applicants
                        </a>
                    </div>

                    <!-- invite from network -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h3 class="font-semibold text-gray-900 mb-1">Invite From Your Network</h3>
                        <p class="text-sm text-gray-500 mb-4">Send this project to talents you know to see if they are interested</p>

                        <button @click="inviteModalOpen = true"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            Send Invite
                        </button>
                    </div>
                </div>

                <!-- description -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Description</h2>
                        <a href="{{ route('company.jobs.edit', $jobPosting['id']) }}"
                           class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-3">About The Position:</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $jobPosting['description'] }}</p>
                        </div>

                        <div>
                            <h3 class="font-semibold text-gray-900 mb-3">What You'll Do:</h3>
                            <ul class="space-y-2">
                                @foreach(explode("\n", $jobPosting['responsibilities'] ?? '') as $responsibility)
                                    @if(trim($responsibility))
                                        <li class="flex items-start gap-2 text-gray-600">
                                            <span class="text-gray-400 mt-1.5">•</span>
                                            <span>{{ trim($responsibility) }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- right column (sidebar) -->
            <div class="space-y-6">

                <!-- details -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Details</h2>
                        <a href="{{ route('company.jobs.edit', $jobPosting['id']) }}"
                           class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Budget</p>
                            <p class="font-medium text-gray-900">{{ $jobPosting['budget'] }} • {{ $jobPosting['budget_type'] }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Delivery Time</p>
                            <p class="font-medium text-gray-900">{{ $jobPosting['delivery_time'] }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Individual Hires</p>
                            <p class="font-medium text-gray-900">{{ $jobPosting['individual_hires'] }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500 mb-2">Tags</p>
                            <div class="flex flex-wrap gap-2">
                                @if(!empty($jobPosting['tags']) && is_array($jobPosting['tags']))
                                    @foreach($jobPosting['tags'] as $tag)
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">{{ $tag }}</span>
                                    @endforeach
                                @else
                                    <span class="text-sm text-gray-400 italic">No tags</span>
                                @endif
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-500 mb-2">Additional Info</p>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                                    </svg>
                                    <span>{{ $jobPosting['language'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $jobPosting['timezone'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-500 mb-2">Owner</p>
                            <div class="flex items-center gap-3">
                                @if(!empty($jobPosting['owner']['avatar']))
                                    <img src="{{ asset('storage/profiles/' . $jobPosting['owner']['avatar']) }}"
                                         alt="{{ $jobPosting['owner']['name'] }}"
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                                        @php
                                            $nameParts = explode(' ', $jobPosting['owner']['name']);
                                            $initials = strtoupper(substr($nameParts[0], 0, 1) . (count($nameParts) > 1 ? substr($nameParts[count($nameParts)-1], 0, 1) : ''));
                                        @endphp
                                        <span class="text-white text-sm font-bold">{{ $initials }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $jobPosting['owner']['name'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $jobPosting['owner']['role'] }}</p>
                                </div>
                                <button class="ml-auto text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-500">Created</p>
                            <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($jobPosting['created_at'])->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- job performance -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Job Performance</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="stat-item text-center p-3 rounded-lg hover:bg-gray-50">
                            <div class="flex justify-center mb-2">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['applications_received'] }}</p>
                            <p class="text-xs text-gray-500">Applications Received</p>
                        </div>

                        <div class="stat-item text-center p-3 rounded-lg hover:bg-gray-50">
                            <div class="flex justify-center mb-2">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['total_views']) }}</p>
                            <p class="text-xs text-gray-500">Total Views</p>
                        </div>

                        <div class="stat-item text-center p-3 rounded-lg hover:bg-gray-50">
                            <div class="flex justify-center mb-2">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['shortlisted'] }}</p>
                            <p class="text-xs text-gray-500">Shortlisted</p>
                        </div>

                        <div class="stat-item text-center p-3 rounded-lg hover:bg-gray-50">
                            <div class="flex justify-center mb-2">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['offers_extended'] }}</p>
                            <p class="text-xs text-gray-500">Offers Extended</p>
                        </div>

                        <div class="stat-item text-center p-3 rounded-lg hover:bg-gray-50">
                            <div class="flex justify-center mb-2">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['messaged'] }}</p>
                            <p class="text-xs text-gray-500">Messaged</p>
                        </div>

                        <div class="stat-item text-center p-3 rounded-lg hover:bg-gray-50">
                            <div class="flex justify-center mb-2">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['hired'] }}</p>
                            <p class="text-xs text-gray-500">Hired</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- share modal -->
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
                                class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700">
                            Copy Link
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

    <!-- invite modal -->
    <div x-show="inviteModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         @keydown.escape.window="inviteModalOpen = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" @click="inviteModalOpen = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Invite From Network</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" x-model="inviteEmail" placeholder="Enter email address"
                               class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Personal Message (Optional)</label>
                        <textarea x-model="inviteMessage" rows="3" placeholder="Add a personal message..."
                                  class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button @click="sendInvite()"
                                class="flex-1 px-4 py-2 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700">
                            Send Invite
                        </button>
                        <button @click="inviteModalOpen = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function jobShowPage() {
    return {
        activeTab: 'overview',
        allowGuestApplications: {{ $jobPosting['allow_guest_applications'] ? 'true' : 'false' }},
        shareModalOpen: false,
        inviteModalOpen: false,
        inviteEmail: '',
        inviteMessage: '',
        shareUrl: '{{ $jobPosting['share_url'] }}',

        copyShareUrl() {
            navigator.clipboard.writeText(this.shareUrl).then(() => {
                // TO DO: tampilkan notifikasi sukses
                alert('Link berhasil disalin!');
            });
        },

        // TO DO: implementasi boost job ke backend
        boostJob() {
            // TO DO: panggil API untuk boost job
            alert('Fitur Boost Job akan segera tersedia!');
        },

        // TO DO: implementasi send invite ke backend
        async sendInvite() {
            if (!this.inviteEmail) {
                alert('Masukkan alamat email!');
                return;
            }

            // TO DO: kirim invite via API
            console.log('Sending invite to:', this.inviteEmail);
            this.inviteModalOpen = false;
            this.inviteEmail = '';
            this.inviteMessage = '';
            alert('Undangan berhasil dikirim!');
        }
    }
}
</script>
@endpush
