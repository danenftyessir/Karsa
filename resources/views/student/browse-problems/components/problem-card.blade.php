{{-- problem card component untuk grid view --}}
<a href="{{ route('student.browse-problems.show', $problem->id) }}" 
   class="problem-card group bg-white rounded-xl shadow-sm hover:shadow-xl border border-gray-100 overflow-hidden transition-all duration-300 hover:-translate-y-1 flex flex-col h-full">

    {{-- image --}}
    <div class="relative h-48 bg-gradient-to-br from-blue-100 to-blue-50 overflow-hidden flex-shrink-0">
        @if($problem->images && $problem->images->count() > 0)
            @php
                $imagePath = $problem->images->first()->image_path;
                $imageUrl = supabase_url($imagePath);
            @endphp
            <img src="{{ $imageUrl }}"
                 alt="{{ $problem->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                 onerror="console.error('Image failed to load:', '{{ $imageUrl }}', 'Path:', '{{ $imagePath }}'); this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center\'><svg class=\'w-20 h-20 text-blue-300\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z\'/></svg></div>';">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-20 h-20 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
            </div>
        @endif
    </div>

    {{-- content --}}
    <div class="p-5 flex flex-col flex-1">
        
        {{-- institution info --}}
        <div class="flex items-center gap-3 mb-4 flex-shrink-0">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold flex-shrink-0 overflow-hidden">
                @if($problem->institution && $problem->institution->logo_path)
                    <img src="{{ supabase_url($problem->institution->logo_path) }}" 
                         alt="{{ $problem->institution->name }}"
                         class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($problem->institution->name ?? 'I', 0, 1)) }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-gray-900 truncate">{{ $problem->institution->name ?? 'Instansi' }}</p>
                <p class="text-xs text-gray-500 truncate">{{ $problem->institution->type ?? '' }}</p>
            </div>
        </div>

        {{-- problem title --}}
        <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2 h-14 group-hover:text-blue-600 transition-colors flex-shrink-0">
            {{ $problem->title }}
        </h3>

        {{-- description --}}
        <p class="text-gray-600 text-sm mb-4 line-clamp-2 h-10 flex-shrink-0">
            {{ $problem->description }}
        </p>

        {{-- metadata --}}
        <div class="flex items-center gap-4 text-xs text-gray-600 mb-4 flex-shrink-0">
            <div class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="line-clamp-1">{{ $problem->regency->name ?? $problem->location_regency }}, {{ $problem->province->name ?? $problem->location_province }}</span>
            </div>
        </div>
        
        {{-- sdg categories - gunakan helper sdg_label() --}}
        <div class="flex flex-wrap gap-2 mb-4 h-16 content-start overflow-hidden flex-shrink-0">
            @php
                $sdgCategories = $problem->sdg_categories ?? [];                
                // jika masih string (JSON), decode dulu
                if (is_string($sdgCategories)) {
                    $sdgCategories = json_decode($sdgCategories, true) ?? [];
                }
                
                // pastikan hasilnya array
                if (!is_array($sdgCategories)) {
                    $sdgCategories = [];
                }
            @endphp
            
            @foreach(array_slice($sdgCategories, 0, 3) as $sdg)
            <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded h-fit">
                {{ sdg_label($sdg) }}
            </span>
            @endforeach
            
            @if(count($sdgCategories) > 3)
            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded h-fit">
                +{{ count($sdgCategories) - 3 }} Lainnya
            </span>
            @endif
        </div>

        {{-- stats --}}
        <div class="flex items-center justify-between text-xs text-gray-600 mb-4 pb-4 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>{{ $problem->required_students }} Mahasiswa</span>
            </div>
            <div class="flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ $problem->duration_months }} Bulan</span>
            </div>
        </div>

        {{-- deadline & difficulty --}}
        <div class="flex items-center justify-between mb-4 flex-shrink-0">
            <div>
                <p class="text-xs text-gray-500 mb-1">Deadline Aplikasi</p>
                <p class="text-sm font-semibold text-gray-900">{{ $problem->application_deadline->format('d M Y') }}</p>
            </div>
            <span class="px-3 py-1 text-xs font-semibold rounded-full
                {{ $problem->difficulty_level === 'beginner' ? 'bg-green-100 text-green-700' : '' }}
                {{ $problem->difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $problem->difficulty_level === 'advanced' ? 'bg-red-100 text-red-700' : '' }}">
                {{ ucfirst($problem->difficulty_level) }}
            </span>
        </div>

        {{-- cta button --}}
        <div class="mt-auto">
            <button class="w-full px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold rounded-lg transition-all shadow-md hover:shadow-lg">
                Lihat Detail Proyek
            </button>
        </div>
    </div>
</a>