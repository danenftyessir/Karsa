@extends('layouts.app')

@section('title', 'Upload Dokumen Verifikasi')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Verifikasi Dokumen Institusi</h1>
            <p class="text-gray-600 mt-2">Upload dokumen untuk verifikasi otomatis menggunakan AI</p>
        </div>

        {{-- Success/Error Messages --}}
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- Institution Status Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Status Verifikasi</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $institution->name }}</p>
                </div>
                <div class="text-right">
                    @php
                        $statusConfig = [
                            'pending_verification' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Menunggu Verifikasi'],
                            'needs_review' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'label' => 'Perlu Review Manual'],
                            'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'label' => 'Ditolak'],
                            'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Aktif - Terverifikasi'],
                        ];
                        $status = $statusConfig[$institution->verification_status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Belum Diverifikasi'];
                    @endphp
                    <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $status['bg'] }} {{ $status['text'] }}">
                        {{ $status['label'] }}
                    </span>
                    @if($institution->verification_score)
                    <p class="text-sm text-gray-600 mt-2">Score: {{ $institution->verification_score }}/100</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Upload Form --}}
        <form action="{{ route('institution.verification.upload.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Upload Dokumen</h3>

                <div class="space-y-6">
                    {{-- Official Letter --}}
                    @php
                        $officialLetterDoc = $existingDocuments->where('document_type', 'official_letter')->first();
                    @endphp
                    <div class="border-b border-gray-200 pb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Surat Pengantar / SK Institusi <span class="text-red-500">*</span>
                        </label>
                        <p class="text-sm text-gray-600 mb-3">Upload surat resmi dari institusi (PDF, max 5MB)</p>
                        <input type="file" name="official_letter" accept="application/pdf"
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                        @if($officialLetterDoc)
                        <p class="text-sm text-green-600 mt-2">
                            ✓ Sudah diupload: {{ $officialLetterDoc->file_name }}
                            @if($officialLetterDoc->ai_status)
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ ucfirst($officialLetterDoc->ai_status) }}</span>
                            @endif
                        </p>
                        @endif
                    </div>

                    {{-- Logo --}}
                    @php
                        $logoDoc = $existingDocuments->where('document_type', 'logo')->first();
                    @endphp
                    <div class="border-b border-gray-200 pb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Logo Institusi
                        </label>
                        <p class="text-sm text-gray-600 mb-3">Upload logo institusi (JPG/PNG, max 2MB)</p>
                        <input type="file" name="logo" accept="image/jpeg,image/png"
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                        @if($logoDoc)
                        <p class="text-sm text-green-600 mt-2">
                            ✓ Sudah diupload: {{ $logoDoc->file_name }}
                            @if($logoDoc->ai_status)
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ ucfirst($logoDoc->ai_status) }}</span>
                            @endif
                        </p>
                        @endif
                    </div>

                    {{-- PIC Identity --}}
                    @php
                        $picDoc = $existingDocuments->where('document_type', 'pic_identity')->first();
                    @endphp
                    <div class="border-b border-gray-200 pb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            KTP Penanggung Jawab
                        </label>
                        <p class="text-sm text-gray-600 mb-3">Upload foto KTP PIC (JPG/PNG, max 2MB)</p>
                        <input type="file" name="pic_identity" accept="image/jpeg,image/png"
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                        @if($picDoc)
                        <p class="text-sm text-green-600 mt-2">
                            ✓ Sudah diupload: {{ $picDoc->file_name }}
                            @if($picDoc->ai_status)
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ ucfirst($picDoc->ai_status) }}</span>
                            @endif
                        </p>
                        @endif
                    </div>

                    {{-- NPWP --}}
                    @php
                        $npwpDoc = $existingDocuments->where('document_type', 'npwp')->first();
                    @endphp
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            NPWP (Opsional)
                        </label>
                        <p class="text-sm text-gray-600 mb-3">Upload NPWP institusi (PDF/JPG/PNG, max 2MB)</p>
                        <input type="file" name="npwp" accept="application/pdf,image/jpeg,image/png"
                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                        @if($npwpDoc)
                        <p class="text-sm text-green-600 mt-2">
                            ✓ Sudah diupload: {{ $npwpDoc->file_name }}
                            @if($npwpDoc->ai_status)
                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ ucfirst($npwpDoc->ai_status) }}</span>
                            @endif
                        </p>
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition-colors">
                        Upload Dokumen
                    </button>
                    <a href="{{ route('institution.dashboard') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </form>

        {{-- Trigger AI Verification --}}
        @if($existingDocuments->isNotEmpty())
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg shadow-sm border border-indigo-200 p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Mulai Verifikasi AI</h3>
                    <p class="text-gray-700 mb-4">
                        Dokumen Anda sudah diupload. Klik tombol di bawah untuk memulai verifikasi otomatis menggunakan AI.
                        Proses ini akan menganalisis keaslian dan kelengkapan dokumen Anda.
                    </p>
                    <form action="{{ route('institution.verification.trigger') }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:from-indigo-700 hover:to-purple-700 font-semibold transition-all shadow-lg">
                            🤖 Mulai Verifikasi AI
                        </button>
                    </form>
                    <a href="{{ route('institution.verification.status') }}" class="ml-3 px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-indigo-50 font-semibold transition-colors border border-indigo-200 inline-block">
                        Lihat Status Verifikasi
                    </a>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
