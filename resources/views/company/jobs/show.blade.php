@extends('layouts.app')

@section('title', $jobPosting['title'] . ' - ' . $company->name)

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .avatar-stack img,
    .avatar-stack div {
        transition: transform 0.15s ease;
    }

    .avatar-stack img:hover,
    .avatar-stack div:hover {
        transform: scale(1.1);
        z-index: 10;
    }

    @media (prefers-reduced-motion: reduce) {
        .stat-card,
        .avatar-stack img,
        .avatar-stack div {
            transition: none;
        }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50" x-data="jobShowPage()">

    {{-- breadcrumb & header --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-6">
            <nav class="flex items-center gap-2 text-sm mb-4">
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

            <div class="flex items-start justify-between gap-6">
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $jobPosting['title'] }}</h1>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Active
                        </span>
                        <span>Posted {{ \Carbon\Carbon::parse($jobPosting['created_at'])->diffForHumans() }}</span>
                        <span>•</span>
                        <span>{{ number_format($statistics['total_views'] ?? 0) }} views</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('company.jobs.edit', $jobPosting['id']) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <button @click="shareModalOpen = true"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        Share
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- performance stats --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-6">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-6">
                <div class="stat-card text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['applications_received'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600 mt-1">Applications</p>
                </div>
                <div class="stat-card text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['shortlisted'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600 mt-1">Shortlisted</p>
                </div>
                <div class="stat-card text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['messaged'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600 mt-1">Messaged</p>
                </div>
                <div class="stat-card text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['offers_extended'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600 mt-1">Offers</p>
                </div>
                <div class="stat-card text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['hired'] ?? 0 }}</p>
                    <p class="text-sm text-gray-600 mt-1">Hired</p>
                </div>
                <div class="stat-card text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($statistics['total_views'] ?? 0) }}</p>
                    <p class="text-sm text-gray-600 mt-1">Views</p>
                </div>
            </div>
        </div>
    </div>

    {{-- main content --}}
    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- left column --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- applicants quick view --}}
                @if(count($recentApplicants) > 0)
                <section>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Recent Applicants</h2>
                        <a href="{{ route('company.applications.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            View all →
                        </a>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center avatar-stack">
                                @foreach($recentApplicants as $index => $applicant)
                                    @if($index < 5)
                                        @if(!empty($applicant['avatar']))
                                            <img src="{{ asset('storage/profiles/' . $applicant['avatar']) }}"
                                                 alt="{{ $applicant['name'] }}"
                                                 class="w-12 h-12 rounded-full border-2 border-white object-cover {{ $index > 0 ? '-ml-3' : '' }}"
                                                 style="z-index: {{ count($recentApplicants) - $index }}">
                                        @else
                                            <div class="w-12 h-12 rounded-full border-2 border-white bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center {{ $index > 0 ? '-ml-3' : '' }}"
                                                 style="z-index: {{ count($recentApplicants) - $index }}">
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
                                    <div class="w-12 h-12 rounded-full border-2 border-white bg-gray-200 flex items-center justify-center -ml-3"
                                         style="z-index: 0">
                                        <span class="text-gray-600 text-sm font-medium">+{{ count($recentApplicants) - 5 }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
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
                        </div>

                        <a href="{{ route('company.applications.index') }}"
                           class="block w-full py-2.5 text-center border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Review All Applicants
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
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Responsibilities</h3>
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
                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Share This Job</h2>
                    <div class="flex items-center gap-3">
                        <input type="text"
                               value="{{ $jobPosting['share_url'] }}"
                               readonly
                               class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 bg-gray-50 text-sm text-gray-600 focus:outline-none">
                        <button @click="copyShareUrl()"
                                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                            Copy Link
                        </button>
                    </div>
                </section>

            </div>

            {{-- right column (sidebar) --}}
            <div class="space-y-6">

                {{-- job details --}}
                <section class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Job Details</h3>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Budget</p>
                            <p class="font-semibold text-gray-900">{{ $jobPosting['budget'] }}</p>
                            <p class="text-sm text-gray-600">{{ $jobPosting['budget_type'] }}</p>
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-sm text-gray-500 mb-1">Delivery Time</p>
                            <p class="font-semibold text-gray-900">{{ $jobPosting['delivery_time'] }}</p>
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-sm text-gray-500 mb-1">Individual Hires</p>
                            <p class="font-semibold text-gray-900">{{ $jobPosting['individual_hires'] }}</p>
                        </div>

                        @if(!empty($jobPosting['tags']) && is_array($jobPosting['tags']))
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-sm text-gray-500 mb-2">Skills Required</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($jobPosting['tags'] as $tag)
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-md text-xs font-medium">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="border-t border-gray-100 pt-4">
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
                    </div>
                </section>

                {{-- owner info --}}
                @if(!empty($jobPosting['owner']) && !empty($jobPosting['owner']['name']))
                <section class="bg-white rounded-xl border border-gray-200 p-6">
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
                            <p class="text-sm text-gray-600">{{ $jobPosting['owner']['role'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </section>
                @endif

                {{-- actions --}}
                <section class="space-y-3">
                    <button @click="inviteModalOpen = true"
                            class="w-full py-2.5 px-4 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Invite Candidates
                    </button>

                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Guest Applications</p>
                            <p class="text-xs text-gray-500">Allow non-users to apply</p>
                        </div>
                        <button @click="allowGuestApplications = !allowGuestApplications"
                                :class="allowGuestApplications ? 'bg-blue-600' : 'bg-gray-200'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                            <span :class="allowGuestApplications ? 'translate-x-6' : 'translate-x-1'"
                                  class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                        </button>
                    </div>
                </section>

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
                        <input type="email" x-model="inviteEmail" placeholder="candidate@example.com"
                               class="w-full rounded-lg border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message (Optional)</label>
                        <textarea x-model="inviteMessage" rows="3" placeholder="Add a personal message..."
                                  class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button @click="sendInvite()"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
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
        allowGuestApplications: {{ $jobPosting['allow_guest_applications'] ? 'true' : 'false' }},
        shareModalOpen: false,
        inviteModalOpen: false,
        inviteEmail: '',
        inviteMessage: '',
        shareUrl: '{{ $jobPosting['share_url'] }}',

        copyShareUrl() {
            navigator.clipboard.writeText(this.shareUrl).then(() => {
                alert('Link copied to clipboard!');
            }).catch(() => {
                alert('Failed to copy link');
            });
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
        }
    }
}
</script>
@endpush
