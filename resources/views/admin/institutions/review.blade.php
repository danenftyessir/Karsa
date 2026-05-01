@extends('layouts.app')

@section('title', 'Review Institution - ' . $institution->name)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Review Institution</h1>
                <p class="text-gray-600 mt-2">Complete review and verification</p>
            </div>
            <a href="{{ route('admin.institutions.manual-review') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition">
                ← Back to List
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Institution Info & AI Results -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Institution Info Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center mb-4">
                    <img src="{{ $institution->logo_url }}" alt="{{ $institution->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-indigo-200">
                    <div class="ml-4">
                        <h2 class="text-xl font-bold text-gray-900">{{ $institution->name }}</h2>
                        <p class="text-sm text-gray-500">{{ ucwords(str_replace('_', ' ', $institution->type)) }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium">{{ $institution->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phone:</span>
                        <span class="font-medium">{{ $institution->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">PIC:</span>
                        <span class="font-medium">{{ $institution->pic_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Position:</span>
                        <span class="font-medium">{{ $institution->pic_position }}</span>
                    </div>
                    <div class="border-t pt-3">
                        <span class="text-gray-600">Location:</span>
                        <p class="font-medium mt-1">{{ $institution->regency->name ?? 'N/A' }}, {{ $institution->province->name ?? 'N/A' }}</p>
                    </div>
                    <div class="border-t pt-3">
                        <span class="text-gray-600">Registered:</span>
                        <p class="font-medium mt-1">{{ $institution->created_at->format('d F Y, H:i') }}</p>
                        <p class="text-xs text-gray-400">{{ $institution->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <!-- AI Validation Results Card -->
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg shadow-md p-6 border border-purple-200">
                <h3 class="text-lg font-bold text-purple-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    AI Validation Results
                </h3>

                <!-- Overall Score -->
                <div class="bg-white rounded-lg p-4 mb-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-2">Overall AI Score</p>
                        <div class="text-5xl font-bold {{ $institution->ai_validation_score >= 85 ? 'text-green-600' : ($institution->ai_validation_score >= 80 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($institution->ai_validation_score, 1) }}
                        </div>
                        <p class="text-gray-500 text-sm mt-1">out of 100</p>
                    </div>
                </div>

                <!-- Status Badge -->
                @php
                    $statusConfig = [
                        'manual_review' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'label' => 'Manual Review Required'],
                        'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Approved'],
                        'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Rejected'],
                    ];
                    $status = $statusConfig[$institution->ai_validation_status] ?? $statusConfig['manual_review'];
                @endphp
                <div class="px-4 py-3 rounded-lg {{ $status['bg'] }} {{ $status['text'] }} text-center font-semibold">
                    {{ $status['label'] }}
                </div>

                <!-- AI Notes -->
                @if($institution->ai_validation_notes)
                <div class="mt-4">
                    <p class="text-sm font-semibold text-purple-900 mb-2">AI Analysis Notes:</p>
                    <div class="bg-white rounded-lg p-3 text-sm text-gray-700 max-h-48 overflow-y-auto">
                        {{ $institution->ai_validation_notes }}
                    </div>
                </div>
                @endif

                @if($institution->ai_validated_at)
                <p class="text-xs text-purple-700 mt-4">
                    Validated: {{ $institution->ai_validated_at->format('d M Y, H:i') }}
                </p>
                @endif
            </div>

            <!-- Action Buttons -->
            @if($institution->ai_validation_status !== 'approved' && $institution->ai_validation_status !== 'rejected')
            <div class="space-y-3">
                <button onclick="document.getElementById('approveModal').classList.remove('hidden')" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Approve Institution
                </button>
                <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Reject Institution
                </button>
            </div>
            @endif
        </div>

        <!-- Right Column - Documents -->
        <div class="lg:col-span-2">
            <!-- Documents Section -->
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Uploaded Documents</h2>
                    <p class="text-gray-600 text-sm mt-1">Review all documents carefully before making a decision</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- KTP Document -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                            </svg>
                            KTP Penanggung Jawab
                        </h3>
                        @if($institution->ktp_path)
                        <img src="{{ asset('storage/' . $institution->ktp_path) }}" alt="KTP" class="w-full h-auto rounded-lg border border-gray-300">
                        @else
                        <p class="text-red-500 text-sm">Not uploaded</p>
                        @endif
                    </div>

                    <!-- NPWP Document -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            NPWP Instansi
                        </h3>
                        @if($institution->npwp_path)
                        <img src="{{ asset('storage/' . $institution->npwp_path) }}" alt="NPWP" class="w-full h-auto rounded-lg border border-gray-300">
                        @else
                        <p class="text-red-500 text-sm">Not uploaded</p>
                        @endif
                    </div>

                    <!-- Verification Document -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            Dokumen Verifikasi (PDF)
                        </h3>
                        @if($institution->verification_document_path)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-300">
                            <p class="text-sm text-gray-600 mb-3">Dokumen Resmi (Surat Tugas / SK / Akta)</p>
                            <a href="{{ asset('storage/' . $institution->verification_document_path) }}" target="_blank" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Open PDF in New Tab
                            </a>
                        </div>
                        @else
                        <p class="text-red-500 text-sm">Not uploaded</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Validation Logs -->
            @if($validationLogs->count() > 0)
            <div class="bg-white rounded-lg shadow-md mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Validation History</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($validationLogs as $log)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-semibold text-gray-900">{{ ucwords(str_replace('_', ' ', $log->validation_type)) }}</span>
                                <span class="text-sm {{ $log->is_passed ? 'text-green-600' : 'text-red-600' }} font-medium">
                                    {{ $log->is_passed ? 'PASSED' : 'FAILED' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">
                                <p><strong>Score:</strong> {{ number_format($log->score, 1) }}/100</p>
                                @if($log->recommendation)
                                <p class="mt-2"><strong>Recommendation:</strong> {{ $log->recommendation }}</p>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 mt-2">{{ $log->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Approve Institution</h3>
        </div>
        <form method="POST" action="{{ route('admin.institutions.approve', $institution->id) }}">
            @csrf
            <div class="px-6 py-4">
                <p class="text-gray-600 mb-4">Are you sure you want to approve <strong>{{ $institution->name }}</strong>?</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes (Optional)</label>
                    <textarea name="admin_notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" placeholder="Additional notes..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('approveModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    Approve
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Reject Institution</h3>
        </div>
        <form method="POST" action="{{ route('admin.institutions.reject', $institution->id) }}">
            @csrf
            <div class="px-6 py-4">
                <p class="text-gray-600 mb-4">Please provide a reason for rejecting <strong>{{ $institution->name }}</strong>:</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Reason <span class="text-red-500">*</span></label>
                    <textarea name="rejection_reason" rows="4" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Explain why this institution is being rejected..."></textarea>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
