<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil - Karsa</title>
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
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .header .icon {
            font-size: 64px;
            margin-bottom: 10px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 20px;
        }
        .message {
            font-size: 15px;
            color: #4a5568;
            margin-bottom: 25px;
            line-height: 1.8;
        }
        .info-box {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin: 0 0 15px;
            color: #2d3748;
            font-size: 16px;
        }
        .info-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #2d3748;
            width: 180px;
            flex-shrink: 0;
        }
        .info-value {
            color: #4a5568;
            flex: 1;
        }
        .status-badge {
            display: inline-block;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            margin: 15px 0;
            box-shadow: 0 2px 8px rgba(251, 191, 36, 0.3);
        }
        .steps {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .steps h3 {
            margin: 0 0 15px;
            color: #1e40af;
            font-size: 16px;
            font-weight: 600;
        }
        .step-item {
            display: flex;
            margin-bottom: 15px;
            align-items: start;
        }
        .step-number {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            margin-right: 12px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
        }
        .step-text {
            flex: 1;
            color: #1e3a8a;
            font-size: 14px;
            padding-top: 5px;
        }
        .step-text strong {
            color: #1e40af;
        }
        .ai-badge {
            background: linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
            margin: 10px 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .footer {
            background: #f7fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 5px 0;
            color: #718096;
            font-size: 13px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 25px 0;
        }
        .highlight {
            color: #667eea;
            font-weight: 600;
        }
        .warning-box {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
        .feature-box {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .feature-box h3 {
            margin: 0 0 10px;
            color: #16a34a;
            font-size: 16px;
            font-weight: 600;
        }
        .feature-box ul {
            margin: 0;
            padding-left: 20px;
            color: #166534;
        }
        .feature-box li {
            margin: 6px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">🎉</div>
            <h1>Selamat Datang di Karsa!</h1>
            <p>Karya Untuk Bangsa</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Halo, {{ $institution->name }}! 👋
            </div>

            <div class="message">
                Terima kasih telah mendaftar di <strong>Karsa</strong>, platform digital yang menghubungkan mahasiswa dengan instansi untuk program Kuliah Kerja Nyata berkelanjutan.
                Pendaftaran Anda telah <strong>berhasil diterima</strong>!
            </div>

            <div class="status-badge">
                🤖 Status: Sedang Divalidasi AI
            </div>

            <div class="ai-badge">
                ✨ AI-Powered Verification System
            </div>

            <div class="info-box">
                <h3>📋 Detail Registrasi Anda</h3>
                <div class="info-row">
                    <div class="info-label">Nama Instansi:</div>
                    <div class="info-value">{{ $institution->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Jenis Instansi:</div>
                    <div class="info-value">{{ ucwords(str_replace('_', ' ', $institution->type)) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value">{{ $institution->email }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Lokasi:</div>
                    <div class="info-value">{{ $institution->regency->name ?? 'N/A' }}, {{ $institution->province->name ?? 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">PIC:</div>
                    <div class="info-value">{{ $institution->pic_name }} ({{ $institution->pic_position }})</div>
                </div>
            </div>

            <div class="message">
                Akun Anda saat ini sedang dalam <span class="highlight">proses validasi otomatis</span> menggunakan sistem AI canggih kami.
                Sistem akan menganalisis dokumen verifikasi Anda (KTP, NPWP, dan Dokumen Resmi) untuk memastikan keamanan dan kredibilitas.
            </div>

            <div class="steps">
                <h3>🔄 Proses Verifikasi AI:</h3>
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-text">
                        <strong>Analisis Dokumen (30 detik - 2 menit)</strong><br>
                        AI kami akan menganalisis KTP, NPWP, dan dokumen verifikasi Anda secara detail
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-text">
                        <strong>Scoring & Validation</strong><br>
                        Sistem memberikan skor berdasarkan kualitas, autentikasi, dan kelengkapan dokumen
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-text">
                        <strong>Email Hasil (5-10 menit)</strong><br>
                        Anda akan menerima email approval atau rejection dengan penjelasan detail
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">4</div>
                    <div class="step-text">
                        <strong>Akses Platform</strong><br>
                        Jika disetujui, Anda dapat langsung login dan posting proyek KKN
                    </div>
                </div>
            </div>

            <div class="feature-box">
                <h3>✅ Apa yang Divalidasi AI?</h3>
                <ul>
                    <li><strong>KTP Penanggung Jawab:</strong> Kejelasan foto, validitas NIK, kelengkapan data</li>
                    <li><strong>NPWP Instansi:</strong> Format nomor, kejelasan dokumen, kesesuaian nama</li>
                    <li><strong>Dokumen Verifikasi:</strong> Keabsahan dokumen, tanda tangan, cap resmi, konten relevan</li>
                </ul>
            </div>

            <div class="warning-box">
                <p>
                    <strong>⚠️ Penting:</strong> Proses validasi AI biasanya selesai dalam <strong>5-10 menit</strong>.
                    Cek email Anda secara berkala untuk hasil verifikasi. Jika dokumen ditolak, Anda dapat mendaftar ulang dengan dokumen yang diperbaiki.
                </p>
            </div>

            <div class="divider"></div>

            <div class="message">
                <strong>Ada pertanyaan?</strong><br>
                Jangan ragu untuk menghubungi tim support kami melalui halaman
                <a href="{{ route('contact') }}" class="highlight">Contact Us</a>.
            </div>

            <div style="text-align: center; margin-top: 30px;">
                <a href="{{ route('home') }}" class="cta-button">
                    Kunjungi Website Karsa
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Karsa - Karya Untuk Bangsa</strong></p>
            <p>Platform Digital untuk Kuliah Kerja Nyata Berkelanjutan</p>
            <p style="margin-top: 15px;">
                <a href="{{ route('home') }}">Website</a> |
                <a href="{{ route('about') }}">Tentang Kami</a> |
                <a href="{{ route('contact') }}">Kontak</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #a0aec0;">
                © {{ date('Y') }} Karsa. Hak cipta dilindungi undang-undang.
            </p>
            <p style="font-size: 12px; color: #a0aec0;">
                Email ini dikirim otomatis, mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>
