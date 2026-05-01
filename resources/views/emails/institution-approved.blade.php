<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Berhasil</title>
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
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
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
            background-color: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin: 0 0 15px 0;
            color: #16a34a;
            font-size: 16px;
            font-weight: 600;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #dcfce7;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #166534;
            width: 180px;
            flex-shrink: 0;
        }
        .info-value {
            color: #15803d;
            flex: 1;
        }
        .score-box {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 25px 0;
        }
        .score-box .score {
            font-size: 48px;
            font-weight: 700;
            color: #16a34a;
            margin: 10px 0;
        }
        .score-box .label {
            font-size: 14px;
            color: #166534;
            font-weight: 600;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }
        .next-steps {
            background-color: #fefce8;
            border-left: 4px solid #eab308;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .next-steps h3 {
            margin: 0 0 15px 0;
            color: #a16207;
            font-size: 16px;
            font-weight: 600;
        }
        .next-steps ol {
            margin: 0;
            padding-left: 20px;
            color: #92400e;
        }
        .next-steps li {
            margin: 8px 0;
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
            color: #22c55e;
            text-decoration: none;
        }
        .logo {
            max-width: 120px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">✅</div>
            <h1>Verifikasi Berhasil!</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.95;">Akun Anda Telah Disetujui</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Yth. {{ $institution->name }},</p>

            <p class="message">
                Kami dengan senang hati mengabarkan bahwa <strong>akun instansi Anda telah berhasil diverifikasi</strong> oleh sistem AI kami.
                Setelah melalui proses validasi dokumen yang komprehensif, semua dokumen Anda telah memenuhi standar verifikasi yang ditetapkan.
            </p>

            <!-- Score Box -->
            <div class="score-box">
                <div class="label">SKOR VALIDASI AI</div>
                <div class="score">{{ number_format($institution->ai_validation_score, 1) }}/100</div>
                <div class="label">✨ Excellent - Memenuhi Standar Kualitas</div>
            </div>

            <!-- Institution Info -->
            <div class="info-box">
                <h3>📋 Informasi Akun Terverifikasi</h3>
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
                    <div class="info-label">Penanggung Jawab:</div>
                    <div class="info-value">{{ $institution->pic_name }} ({{ $institution->pic_position }})</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status Verifikasi:</div>
                    <div class="info-value"><strong>TERVERIFIKASI</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal Verifikasi:</div>
                    <div class="info-value">{{ $institution->ai_validated_at->format('d F Y, H:i') }} WIB</div>
                </div>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="cta-button">
                    🚀 Login Sekarang
                </a>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <h3>📝 Langkah Selanjutnya:</h3>
                <ol>
                    <li><strong>Login ke akun Anda</strong> menggunakan username dan password yang telah didaftarkan</li>
                    <li><strong>Lengkapi profil instansi</strong> dengan informasi tambahan yang diperlukan</li>
                    <li><strong>Mulai posting masalah/proyek KKN</strong> yang ingin diselesaikan mahasiswa</li>
                    <li><strong>Kelola aplikasi mahasiswa</strong> yang mendaftar di proyek Anda</li>
                    <li><strong>Pantau progres</strong> pelaksanaan KKN secara realtime</li>
                </ol>
            </div>

            <p class="message">
                Terima kasih telah bergabung dengan <strong>Karsa - Karya Untuk Bangsa</strong>.
                Kami berkomitmen untuk memfasilitasi kolaborasi yang produktif antara instansi dan mahasiswa KKN.
            </p>

            <p class="message" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                Jika Anda mengalami kesulitan dalam login atau memiliki pertanyaan, jangan ragu untuk menghubungi tim support kami.
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
