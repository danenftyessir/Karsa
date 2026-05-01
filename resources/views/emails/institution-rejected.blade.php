<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Perlu Diperbaiki</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header .icon {
            font-size: 64px;
            margin-bottom: 15px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .message {
            font-size: 15px;
            line-height: 1.8;
            color: #4b5563;
            margin-bottom: 25px;
        }
        .info-box {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin: 0 0 15px 0;
            color: #dc2626;
            font-size: 16px;
            font-weight: 600;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #fee2e2;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #991b1b;
            width: 180px;
            flex-shrink: 0;
        }
        .info-value {
            color: #b91c1c;
            flex: 1;
        }
        .score-box {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 25px 0;
        }
        .score-box .score {
            font-size: 48px;
            font-weight: 700;
            color: #dc2626;
            margin: 10px 0;
        }
        .score-box .label {
            font-size: 14px;
            color: #991b1b;
            font-weight: 600;
        }
        .issues-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .issues-box h3 {
            margin: 0 0 15px 0;
            color: #b45309;
            font-size: 16px;
            font-weight: 600;
        }
        .issues-box ul {
            margin: 0;
            padding-left: 20px;
            color: #92400e;
        }
        .issues-box li {
            margin: 10px 0;
            line-height: 1.6;
        }
        .action-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .action-box h3 {
            margin: 0 0 15px 0;
            color: #1e40af;
            font-size: 16px;
            font-weight: 600;
        }
        .action-box ol {
            margin: 0;
            padding-left: 20px;
            color: #1e3a8a;
        }
        .action-box li {
            margin: 8px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #6b7280;
        }
        .footer a {
            color: #3b82f6;
            text-decoration: none;
        }
        .requirements {
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .requirements h3 {
            margin: 0 0 15px 0;
            color: #16a34a;
            font-size: 16px;
            font-weight: 600;
        }
        .requirements ul {
            margin: 0;
            padding-left: 20px;
            color: #166534;
        }
        .requirements li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">⚠️</div>
            <h1>Verifikasi Perlu Diperbaiki</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.95;">Dokumen Anda Memerlukan Perbaikan</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Yth. {{ $institution->name }},</p>

            <p class="message">
                Terima kasih telah melakukan pendaftaran di <strong>Karsa - Karya Untuk Bangsa</strong>.
                Setelah melalui proses validasi AI yang komprehensif, kami perlu memberitahukan bahwa
                <strong>dokumen verifikasi Anda belum memenuhi standar yang diperlukan</strong> untuk dapat disetujui secara otomatis.
            </p>

            <!-- Score Box -->
            <div class="score-box">
                <div class="label">SKOR VALIDASI AI</div>
                <div class="score">{{ number_format($institution->ai_validation_score, 1) }}/100</div>
                <div class="label">⚠️ Di Bawah Standar Minimum (85/100)</div>
            </div>

            <!-- Institution Info -->
            <div class="info-box">
                <h3>📋 Informasi Pendaftaran</h3>
                <div class="info-row">
                    <div class="info-label">Nama Instansi:</div>
                    <div class="info-value">{{ $institution->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jenis Instansi:</div>
                    <div class="info-value">{{ ucwords(str_replace('_', ' ', $institution->type)) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email Resmi:</div>
                    <div class="info-value">{{ $institution->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status Verifikasi:</div>
                    <div class="info-value"><strong>DITOLAK</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Validasi:</div>
                    <div class="info-value">{{ $institution->ai_validated_at?->format('d F Y, H:i') ?? now()->format('d F Y, H:i') }} WIB</div>
                </div>
            </div>

            <!-- Issues Found -->
            @if($institution->ai_validation_notes)
            <div class="issues-box">
                <h3>🔍 Alasan Penolakan:</h3>
                <p style="margin: 0 0 15px 0; color: #92400e;">{{ $institution->ai_validation_notes }}</p>
            </div>
            @endif

            <!-- Common Issues -->
            <div class="issues-box">
                <h3>❌ Masalah Umum yang Sering Ditemukan:</h3>
                <ul>
                    <li><strong>KTP:</strong> Foto blur, tidak jelas, atau tidak lengkap</li>
                    <li><strong>NPWP:</strong> Nomor tidak terbaca, nama tidak sesuai, atau fotocopy berkualitas buruk</li>
                    <li><strong>Dokumen Verifikasi:</strong> Tidak ada tanda tangan, cap tidak jelas, format tidak resmi, atau konten tidak relevan</li>
                    <li><strong>Autentikasi:</strong> Dokumen terindikasi palsu, dimanipulasi, atau generic template</li>
                </ul>
            </div>

            <!-- Requirements -->
            <div class="requirements">
                <h3>✅ Persyaratan Dokumen yang Valid:</h3>
                <ul>
                    <li><strong>KTP Penanggung Jawab:</strong> Foto jelas, semua data terbaca, tidak blur, e-KTP asli</li>
                    <li><strong>NPWP Instansi:</strong> Semua angka terbaca jelas, nama sesuai instansi, dokumen asli</li>
                    <li><strong>Dokumen Verifikasi (PDF):</strong>
                        <ul style="margin-top: 8px;">
                            <li>Surat Tugas / SK Pengangkatan (untuk pemerintahan)</li>
                            <li>Akta Pendirian (untuk NGO/Organisasi)</li>
                            <li>Surat Pengesahan dari Dinas terkait</li>
                            <li>Harus memiliki: Header resmi, tanda tangan, cap/stempel, nomor surat</li>
                        </ul>
                    </li>
                </ul>
            </div>

            <!-- Action Steps -->
            <div class="action-box">
                <h3>📝 Langkah yang Perlu Dilakukan:</h3>
                <ol>
                    <li><strong>Perbaiki dokumen</strong> yang bermasalah sesuai dengan persyaratan di atas</li>
                    <li><strong>Pastikan semua dokumen:</strong>
                        <ul style="margin-top: 8px;">
                            <li>Foto/scan berkualitas tinggi dan jelas</li>
                            <li>Tidak blur atau terpotong</li>
                            <li>Merupakan dokumen asli (bukan fotocopy dari fotocopy)</li>
                            <li>Memiliki tanda tangan dan cap yang jelas (untuk dokumen resmi)</li>
                        </ul>
                    </li>
                    <li><strong>Daftar ulang</strong> dengan dokumen yang sudah diperbaiki</li>
                    <li><strong>Hubungi support</strong> jika memerlukan bantuan lebih lanjut</li>
                </ol>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{ route('register.institution') }}" class="cta-button">
                    🔄 Daftar Ulang Sekarang
                </a>
            </div>

            <p class="message" style="margin-top: 30px;">
                Kami memahami bahwa proses verifikasi ini mungkin terasa ketat, namun ini dilakukan untuk
                <strong>memastikan kredibilitas dan keamanan</strong> semua pihak yang terlibat di platform Karsa.
            </p>

            <p class="message" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                Jika Anda memiliki pertanyaan atau memerlukan bantuan dalam mempersiapkan dokumen,
                silakan hubungi tim support kami. Kami siap membantu!
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Karsa - Karya Untuk Bangsa</strong></p>
            <p>Platform Kolaborasi KKN Digital</p>
            <p style="margin-top: 15px;">
                <a href="{{ route('home') }}">Website</a> |
                <a href="{{ route('contact') }}">Kontak</a> |
                <a href="{{ route('about') }}">Tentang Kami</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #9ca3af;">
                Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>
