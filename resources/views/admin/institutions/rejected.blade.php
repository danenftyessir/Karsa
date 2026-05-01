@extends('layouts.app')

@section('title', 'Rejected Institutions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Rejected Institutions</h1>
                <p class="text-gray-600 mt-2">Institutions that failed verification</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
    @endif

    <!-- Institutions List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-purple-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-purple-700 uppercase tracking-wider">Institution</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-purple-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-purple-700 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-purple-700 uppercase tracking-wider">AI Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-purple-700 uppercase tracking-wider">Registered</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-purple-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($institutions as $institution)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <img class="h-12 w-12 rounded-full object-cover border-2 border-purple-200" src="{{ $institution->logo_url }}" alt="{{ $institution->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $institution->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $institution->email }}</div>
                                    <div class="text-xs text-gray-400 mt-1">PIC: {{ $institution->pic_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucwords(str_replace('_', ' ', $institution->type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <div>{{ $institution->regency->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-400">{{ $institution->province->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-yellow-600">
                                    {{ number_format($institution->ai_validation_score, 1) }}
                                </span>
                                <span class="text-sm text-gray-500 ml-1">/100</span>
                            </div>
                            <div class="text-xs text-yellow-600 font-medium mt-1">
                                ⚠️ Borderline Case
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $institution->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $institution->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.institutions.review', $institution->id) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Review Documents
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="mt-4 text-lg font-medium">No institutions require manual review</p>
                            <p class="mt-2 text-sm">Institutions with AI score 80-84 will appear here</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($institutions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $institutions->links() }}
        </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-purple-50 border border-purple-200 rounded-lg p-6">
        <h3 class="text-purple-900 font-semibold text-lg mb-3">ℹ️ Manual Review Guidelines</h3>
        <ul class="text-purple-800 space-y-2 text-sm">
            <li class="flex items-start">
                <span class="text-purple-600 mr-2">•</span>
                <span><strong>Score 80-84:</strong> Borderline cases that require human judgment</span>
            </li>
            <li class="flex items-start">
                <span class="text-purple-600 mr-2">•</span>
                <span><strong>Review carefully:</strong> Check all documents (KTP, NPWP, Verification Doc)</span>
            </li>
            <li class="flex items-start">
                <span class="text-purple-600 mr-2">•</span>
                <span><strong>AI Notes:</strong> Read AI validation notes for specific concerns</span>
            </li>
            <li class="flex items-start">
                <span class="text-purple-600 mr-2">•</span>
                <span><strong>Final Decision:</strong> Approve if documents are legitimate, reject if suspicious</span>
            </li>
        </ul>
    </div>
</div>
@endsection
