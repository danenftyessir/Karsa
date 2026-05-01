<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Company Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk fitur company/perusahaan di Lapangku
    |
    */

    /**
     * Maksimal job postings per company
     * Untuk free plan, bisa dibatasi. Untuk premium plan, unlimited
     */
    'max_job_postings_per_company' => env('COMPANY_MAX_JOB_POSTINGS', 10),

    /**
     * Status options untuk job applications
     */
    'application_status_options' => [
        'new' => 'Lamaran Baru',
        'reviewing' => 'Sedang Ditinjau',
        'shortlisted' => 'Masuk Shortlist',
        'interview' => 'Interview',
        'offer' => 'Penawaran Kerja',
        'rejected' => 'Ditolak',
        'hired' => 'Diterima',
    ],

    /**
     * Company size options
     */
    'company_sizes' => [
        '1-10' => '1-10 Karyawan',
        '11-50' => '11-50 Karyawan',
        '51-200' => '51-200 Karyawan',
        '201-500' => '201-500 Karyawan',
        '501-1000' => '501-1000 Karyawan',
        '1000+' => '1000+ Karyawan',
    ],

    /**
     * Industry options
     */
    'industries' => [
        'Teknologi Informasi',
        'E-Commerce',
        'Fintech',
        'Healthcare',
        'Pendidikan',
        'Manufaktur',
        'Retail',
        'Hospitality',
        'Konstruksi',
        'Logistik',
        'Media & Entertainment',
        'Real Estate',
        'Pertanian',
        'Energi',
        'Otomotif',
        'Telekomunikasi',
        'Perbankan',
        'Konsultan',
        'Lainnya',
    ],

    /**
     * Employment types
     */
    'employment_types' => [
        'full-time' => 'Full Time',
        'part-time' => 'Part Time',
        'contract' => 'Kontrak',
        'internship' => 'Magang',
        'freelance' => 'Freelance',
    ],

    /**
     * Experience levels
     */
    'experience_levels' => [
        'entry' => 'Entry Level',
        'junior' => 'Junior',
        'mid' => 'Mid Level',
        'senior' => 'Senior',
        'lead' => 'Lead/Manager',
    ],

    /**
     * Work location types
     */
    'work_locations' => [
        'onsite' => 'Onsite',
        'remote' => 'Remote',
        'hybrid' => 'Hybrid',
    ],

    /**
     * Logo upload configuration
     */
    'logo' => [
        'max_size' => 2048, // KB
        'allowed_mimes' => ['jpeg', 'jpg', 'png', 'webp'],
        'dimensions' => [
            'max_width' => 800,
            'max_height' => 800,
        ],
    ],

    /**
     * Job posting configuration
     */
    'job_posting' => [
        'default_deadline_days' => 30, // default deadline 30 hari dari sekarang
        'max_applicants_default' => 100,
        'featured_price' => 500000, // Harga untuk featured job posting (dalam IDR)
    ],

    /**
     * Verification status
     */
    'verification_statuses' => [
        'pending' => 'Menunggu Verifikasi',
        'verified' => 'Terverifikasi',
        'rejected' => 'Ditolak',
    ],

    /**
     * Notification settings
     */
    'notifications' => [
        'new_application_email' => env('COMPANY_NEW_APPLICATION_EMAIL', true),
        'new_application_realtime' => env('COMPANY_NEW_APPLICATION_REALTIME', true),
    ],
];
