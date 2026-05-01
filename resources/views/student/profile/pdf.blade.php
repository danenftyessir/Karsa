{{-- resources/views/student/profile/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $student->first_name }} {{ $student->last_name }} - Resume</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px;
            background: #fff;
        }
        .header {
            display: flex;
            gap: 24px;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 2px solid #e5e7eb;
        }
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            font-weight: bold;
            flex-shrink: 0;
        }
        .avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; }
        .header-info h1 { font-size: 28px; color: #1f2937; margin-bottom: 4px; }
        .header-info .subtitle { font-size: 16px; color: #6b7280; margin-bottom: 8px; }
        .contact-info { display: flex; gap: 16px; flex-wrap: wrap; font-size: 14px; color: #4b5563; }
        .contact-info span { display: flex; align-items: center; gap: 4px; }
        .section { margin-bottom: 28px; }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .bio { color: #4b5563; line-height: 1.7; }
        .skills-list { display: flex; flex-wrap: wrap; gap: 8px; }
        .skill-tag {
            padding: 6px 12px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }
        .project-item {
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .project-item h4 { font-size: 16px; color: #1f2937; margin-bottom: 4px; }
        .project-item p { font-size: 14px; color: #6b7280; }
        .project-meta { font-size: 12px; color: #9ca3af; margin-top: 8px; }
        .education-item { margin-bottom: 12px; }
        .education-item h4 { font-size: 15px; color: #1f2937; }
        .education-item p { font-size: 14px; color: #6b7280; }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }
        @media print {
            body { padding: 20px; }
            .header { page-break-after: avoid; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    {{-- header --}}
    <div class="header">
        <div class="avatar">
            @if($student->profile_photo_path)
            <img src="{{ $student->profile_photo_url }}" alt="">
            @else
            {{ strtoupper(substr($student->first_name, 0, 1)) }}
            @endif
        </div>
        <div class="header-info">
            <h1>{{ $student->first_name }} {{ $student->last_name }}</h1>
            <div class="subtitle">
                @if($student->university)
                {{ $student->major ?? 'Mahasiswa' }} - {{ $student->university->name }}
                @else
                Mahasiswa
                @endif
            </div>
            <div class="contact-info">
                <span>{{ $user->email }}</span>
                @if($student->phone)
                <span>{{ $student->phone }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- bio --}}
    @if($student->bio)
    <div class="section">
        <div class="section-title">Tentang Saya</div>
        <p class="bio">{{ $student->bio }}</p>
    </div>
    @endif

    {{-- skills --}}
    @if($student->skills && count($student->skills) > 0)
    <div class="section">
        <div class="section-title">Keahlian</div>
        <div class="skills-list">
            @foreach($student->skills as $skill)
            <span class="skill-tag">{{ $skill }}</span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- education --}}
    @if($student->university)
    <div class="section">
        <div class="section-title">Pendidikan</div>
        <div class="education-item">
            <h4>{{ $student->university->name }}</h4>
            <p>{{ $student->major ?? 'Program Studi' }} @if($student->semester)&bull; Semester {{ $student->semester }}@endif</p>
        </div>
    </div>
    @endif

    {{-- projects/portfolio --}}
    @if(isset($projects) && $projects->count() > 0)
    <div class="section">
        <div class="section-title">Pengalaman Proyek</div>
        @foreach($projects->take(5) as $project)
        <div class="project-item">
            <h4>{{ $project->title }}</h4>
            <p>{{ Str::limit($project->description, 150) }}</p>
            <div class="project-meta">
                @if($project->institution){{ $project->institution->name }} &bull; @endif
                {{ $project->status ?? 'Selesai' }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- interests --}}
    @if($student->interests && count($student->interests) > 0)
    <div class="section">
        <div class="section-title">Minat</div>
        <div class="skills-list">
            @foreach($student->interests as $interest)
            <span class="skill-tag" style="background: #fef3c7; color: #92400e;">{{ $interest }}</span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- footer --}}
    <div class="footer">
        Dibuat dengan Lapangku &bull; {{ now()->format('d F Y') }}
    </div>
</body>
</html>
