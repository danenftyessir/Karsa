@extends('layouts.app')

@section('title', 'Bandingkan Talenta')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900" style="font-family: 'Space Grotesk', sans-serif;">
                    Perbandingan Talenta
                </h1>
                <p class="text-sm text-gray-500 mt-1">Bandingkan {{ $talents->count() }} talenta secara detail</p>
            </div>
            <a href="{{ route('company.talents.index') }}"
               class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                Kembali ke Pencarian
            </a>
        </div>

        {{-- Comparison Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 w-48">Kriteria</th>
                            @foreach($talents as $talent)
                            <th class="px-6 py-4 text-center border-l border-gray-100">
                                <div class="flex flex-col items-center gap-2">
                                    <img src="{{ $talent->avatar ?? 'default-avatar.jpg' }}"
                                         alt="{{ $talent->name }}"
                                         class="w-16 h-16 rounded-full object-cover"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($talent->name) }}&background=6366F1&color=fff'">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $talent->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $talent->profile->headline ?? 'No Title' }}</p>
                                    </div>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        {{-- Location --}}
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-700">Lokasi</td>
                            @foreach($talents as $talent)
                            <td class="px-6 py-4 text-center border-l border-gray-100">
                                <span class="text-sm text-gray-600">{{ $talent->profile->location ?? 'N/A' }}</span>
                            </td>
                            @endforeach
                        </tr>

                        {{-- Email Verified --}}
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-700">Status Verifikasi</td>
                            @foreach($talents as $talent)
                            <td class="px-6 py-4 text-center border-l border-gray-100">
                                @if($talent->email_verified_at)
                                <span class="inline-flex items-center gap-1 text-sm text-green-600 font-medium">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Terverifikasi
                                </span>
                                @else
                                <span class="text-sm text-gray-400">Belum Verifikasi</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        {{-- Skills --}}
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-700">Keahlian</td>
                            @foreach($talents as $talent)
                            <td class="px-6 py-4 border-l border-gray-100">
                                <div class="flex flex-wrap gap-1.5 justify-center">
                                    @if(is_array($talent->profile->skills ?? null))
                                        @foreach(array_slice($talent->profile->skills, 0, 5) as $skill)
                                        <span class="px-2 py-1 bg-violet-50 text-violet-700 text-xs rounded-full">{{ $skill }}</span>
                                        @endforeach
                                        @if(count($talent->profile->skills) > 5)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">+{{ count($talent->profile->skills) - 5 }}</span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400">N/A</span>
                                    @endif
                                </div>
                            </td>
                            @endforeach
                        </tr>

                        {{-- Projects Count --}}
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-700">Jumlah Proyek</td>
                            @foreach($talents as $talent)
                            <td class="px-6 py-4 text-center border-l border-gray-100">
                                <span class="text-lg font-semibold text-gray-900">{{ $talent->profile->projects_count ?? 0 }}</span>
                            </td>
                            @endforeach
                        </tr>

                        {{-- Bio --}}
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-700">Bio</td>
                            @foreach($talents as $talent)
                            <td class="px-6 py-4 border-l border-gray-100">
                                <p class="text-sm text-gray-600 text-left">{{ $talent->profile->bio ?? 'No bio available' }}</p>
                            </td>
                            @endforeach
                        </tr>

                        {{-- Actions --}}
                        <tr>
                            <td class="px-6 py-4 font-medium text-gray-700">Aksi</td>
                            @foreach($talents as $talent)
                            <td class="px-6 py-4 text-center border-l border-gray-100">
                                <div class="flex flex-col gap-2">
                                    <a href="{{ route('profile.public', $talent->username) }}"
                                       class="px-4 py-2 bg-violet-600 text-white text-sm font-semibold rounded-lg hover:bg-violet-700 transition-colors">
                                        Lihat Profil
                                    </a>
                                    <button onclick="saveTalent({{ $talent->id }})"
                                            class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                                        Simpan
                                    </button>
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

<script>
async function saveTalent(talentId) {
    try {
        const response = await fetch(`/company/talents/${talentId}/toggle-save`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            window.showNotification(data.message, data.action === 'saved' ? 'success' : 'info');
        }
    } catch (error) {
        console.error('Error toggling save:', error);
        window.showNotification('Terjadi kesalahan', 'error');
    }
}
</script>
@endsection
