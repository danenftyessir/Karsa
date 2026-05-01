@extends('layouts.app')

@section('title', 'Admin Dashboard - Institution Review')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600 mt-2">Manage and review institution registrations</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Pending AI Validation -->
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg shadow-md p-6 border border-yellow-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-700 text-sm font-medium uppercase">Pending AI</p>
                    <p class="text-3xl font-bold text-yellow-900 mt-2">{{ $stats['pending_ai'] }}</p>
                </div>
                <div class="bg-yellow-500 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.institutions.pending') }}" class="text-yellow-700 hover:text-yellow-800 text-sm font-medium mt-4 inline-block">
                View All →
            </a>
        </div>

        <!-- Manual Review Required -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-md p-6 border border-purple-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-700 text-sm font-medium uppercase">Manual Review</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2">{{ $stats['manual_review'] }}</p>
                </div>
                <div class="bg-purple-500 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.institutions.manual-review') }}" class="text-purple-700 hover:text-purple-800 text-sm font-medium mt-4 inline-block">
                Review Now →
            </a>
        </div>

        <!-- Approved -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-700 text-sm font-medium uppercase">Approved</p>
                    <p class="text-3xl font-bold text-green-900 mt-2">{{ $stats['approved'] }}</p>
                </div>
                <div class="bg-green-500 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.institutions.approved') }}" class="text-green-700 hover:text-green-800 text-sm font-medium mt-4 inline-block">
                View All →
            </a>
        </div>

        <!-- Rejected -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow-md p-6 border border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-700 text-sm font-medium uppercase">Rejected</p>
                    <p class="text-3xl font-bold text-red-900 mt-2">{{ $stats['rejected'] }}</p>
                </div>
                <div class="bg-red-500 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.institutions.rejected') }}" class="text-red-700 hover:text-red-800 text-sm font-medium mt-4 inline-block">
                View All →
            </a>
        </div>

        <!-- Failed -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg shadow-md p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-700 text-sm font-medium uppercase">Failed</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['failed'] }}</p>
                </div>
                <div class="bg-gray-500 rounded-full p-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Institutions Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Recent Registrations</h2>
            <p class="text-gray-600 text-sm mt-1">Latest 10 institution registrations</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Institution</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AI Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentInstitutions as $institution)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $institution->logo_url }}" alt="{{ $institution->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $institution->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $institution->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucwords(str_replace('_', ' ', $institution->type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $institution->regency->name ?? 'N/A' }}, {{ $institution->province->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($institution->ai_validation_score)
                                <span class="text-sm font-medium {{ $institution->ai_validation_score >= 85 ? 'text-green-600' : ($institution->ai_validation_score >= 80 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($institution->ai_validation_score, 1) }}/100
                                </span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = [
                                    'pending_ai_validation' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Pending AI'],
                                    'manual_review' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Manual Review'],
                                    'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Approved'],
                                    'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Rejected'],
                                    'failed' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Failed'],
                                ];
                                $status = $statusConfig[$institution->ai_validation_status] ?? $statusConfig['pending_ai_validation'];
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status['bg'] }} {{ $status['text'] }}">
                                {{ $status['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $institution->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.institutions.review', $institution->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                Review →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="mt-4 text-lg font-medium">No institutions yet</p>
                            <p class="mt-2 text-sm">Institutions will appear here as they register</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
