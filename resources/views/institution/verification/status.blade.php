@extends('layouts.app')

@section('title', 'Status Verifikasi Dokumen')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Status Verifikasi Dokumen</h1>
                    <p class="text-gray-600 mt-2">Hasil verifikasi AI untuk {{ $institution->name }}</p>
                </div>
                <a href="{{ route('institution.verification.upload') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition-colors">
                    Upload Dokumen Baru
                </a>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Overall Status Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Status Keseluruhan</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Status Verifikasi</p>
                    @php
                        $statusConfig = [
                            'pending_verification' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu Verifikasi'],
                            'needs_review' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'Perlu Review Manual'],
                            'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Ditolak'],
                            'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Aktif - Terverifikasi'],
                        ];
                        $status = $statusConfig[$institution->verification_status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Belum Diverifikasi'];
                    @endphp
                    <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold {{ $status['bg'] }} {{ $status['text'] }}">
                        {{ $status['label'] }}
                    </span>
                </div>

                <div>
                    <p class="text-sm text-gray-600 mb-1">Verification Score</p>
                    <div class="flex items-center gap-2">
                        <span class="text-3xl font-bold {{ $institution->verification_score >= 85 ? 'text-green-600' : ($institution->verification_score >= 60 ? 'text-orange-600' : 'text-red-600') }}">
                            {{ $institution->verification_score ?? 0 }}
                        </span>
                        <span class="text-gray-500">/100</span>
                    </div>
                </div>

                <div>
                    <p class="text-sm text-gray-600 mb-1">Confidence Level</p>
                    <div class="flex items-center gap-2">
                        <span class="text-3xl font-bold text-blue-600">
                            {{ number_format(($institution->verification_confidence ?? 0) * 100, 0) }}%
                        </span>
                    </div>
                </div>
            </div>

            @if($institution->verified_at)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    Terakhir diverifikasi: {{ $institution->verified_at->format('d M Y, H:i') }} WIB
                </p>
            </div>
            @endif
        </div>

        {{-- Documents List --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Detail Dokumen</h2>

            @if($documents->isEmpty())
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-600">Belum ada dokumen yang diupload</p>
                <a href="{{ route('institution.verification.upload') }}" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    Upload Dokumen
                </a>
            </div>
            @else
            <div class="space-y-6">
                @foreach($documents as $doc)
                <div class="border border-gray-200 rounded-lg p-6 {{ $doc->ai_status === 'rejected' ? 'border-red-300 bg-red-50' : ($doc->ai_status === 'needs_review' ? 'border-orange-300 bg-orange-50' : 'bg-white') }}">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">
                                    @switch($doc->document_type)
                                        @case('official_letter') Surat Pengantar / SK @break
                                        @case('logo') Logo Institusi @break
                                        @case('pic_identity') KTP Penanggung Jawab @break
                                        @case('npwp') NPWP @break
                                        @default {{ ucfirst($doc->document_type) }}
                                    @endswitch
                                </h3>
                                @if($doc->ai_status)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $doc->ai_status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $doc->ai_status === 'needs_review' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $doc->ai_status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $doc->ai_status)) }}
                                </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">{{ $doc->file_name }}</p>
                        </div>

                        @if($doc->ai_score)
                        <div class="text-right">
                            <p class="text-sm text-gray-600">AI Score</p>
                            <p class="text-2xl font-bold {{ $doc->ai_score >= 80 ? 'text-green-600' : ($doc->ai_score >= 60 ? 'text-orange-600' : 'text-red-600') }}">
                                {{ $doc->ai_score }}
                            </p>
                        </div>
                        @endif
                    </div>

                    {{-- AI Analysis Result --}}
                    @if($doc->ai_processed_at)
                    <div class="border-t border-gray-300 pt-4 mt-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Hasil Analisis AI</h4>

                        {{-- AI Reasoning --}}
                        @if($doc->ai_reasoning)
                        <div class="bg-white rounded p-3 mb-3">
                            <p class="text-sm text-gray-700">{{ $doc->ai_reasoning }}</p>
                        </div>
                        @endif

                        {{-- AI Flags --}}
                        @if($doc->ai_flags && count($doc->ai_flags) > 0)
                        <div class="space-y-2 mb-3">
                            <p class="text-sm font-semibold text-gray-700">Temuan:</p>
                            @foreach($doc->ai_flags as $flag)
                            <div class="flex items-start gap-2 text-sm">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold
                                    {{ $flag['type'] === 'error' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $flag['type'] === 'warning' ? 'bg-orange-100 text-orange-800' : '' }}
                                    {{ $flag['type'] === 'info' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($flag['type']) }}
                                </span>
                                <span class="text-gray-700">{{ $flag['message'] }}</span>
                                <span class="text-gray-500">(Severity: {{ $flag['severity'] }}/10)</span>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- AI Extracted Data --}}
                        @if($doc->ai_extracted_data && count($doc->ai_extracted_data) > 0)
                        <div class="mt-3">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Data yang Diekstrak:</p>
                            <div class="bg-gray-100 rounded p-3">
                                @foreach($doc->ai_extracted_data as $key => $value)
                                <div class="text-sm mb-1">
                                    <span class="font-semibold text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                    <span class="text-gray-600">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <p class="text-xs text-gray-500 mt-3">
                            Diproses: {{ $doc->ai_processed_at->format('d M Y, H:i') }} WIB
                        </p>
                    </div>
                    @else
                    <div class="border-t border-gray-300 pt-4 mt-4">
                        <p class="text-sm text-gray-600">Belum diverifikasi AI</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="mt-8 flex gap-4">
            <a href="{{ route('institution.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition-colors">
                Kembali ke Dashboard
            </a>
            @if($documents->isEmpty() || $institution->verification_status === 'rejected')
            <a href="{{ route('institution.verification.upload') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition-colors">
                Upload Dokumen Baru
            </a>
            @endif
        </div>

    </div>
</div>
@endsection
