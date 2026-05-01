<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * controller untuk halaman about us
 */
class AboutController extends Controller
{
    /**
     * tampilkan halaman about us
     */
    public function index()
    {
        // data statistik platform
        $statistics = [
            'target_students' => '520,000+',
            'target_villages' => '83,436',
            'budget_savings' => 'Rp 540 Miliar',
            'repository_target' => '100,000+',
        ];

        // data tim pengembang
        $team = [
            [
                'name' => 'Danendra Shafi Athallah',
                'role' => 'Full Stack Developer',
                'institution' => 'Institut Teknologi Bandung',
            ],
            [
                'name' => 'Kenzie Raffa Ardhana',
                'role' => 'Backend Developer',
                'institution' => 'Institut Teknologi Bandung',
            ],
            [
                'name' => 'M. Abizzar Gamadrian',
                'role' => 'Frontend Developer',
                'institution' => 'Institut Teknologi Bandung',
            ],
        ];

        // fitur utama platform
        $features = [
            [
                'title' => 'Marketplace Masalah',
                'description' => 'Platform yang menghubungkan mahasiswa dengan masalah nyata yang dibutuhkan pemerintah daerah, meningkatkan relevansi program hingga 75%.',
                'icon' => 'search',
            ],
            [
                'title' => 'Impact Portfolio',
                'description' => 'Sistem validasi resmi dari pemerintah daerah yang menciptakan portofolio profesional terverifikasi untuk meningkatkan daya saing mahasiswa.',
                'icon' => 'award',
            ],
            [
                'title' => 'Knowledge Repository',
                'description' => 'Perpustakaan digital nasional yang mengubah hasil KKN menjadi sumber pembelajaran kolektif yang dapat diakses oleh seluruh masyarakat Indonesia.',
                'icon' => 'book',
            ],
        ];

        // sdgs yang didukung
        $sdgs = [
            [
                'number' => 4,
                'title' => 'Pendidikan Berkualitas',
                'description' => 'Menciptakan pengalaman belajar bermakna yang menghubungkan teori dengan praktik',
            ],
            [
                'number' => 11,
                'title' => 'Kota Dan Komunitas Berkelanjutan',
                'description' => 'Mendukung pengembangan daerah berbasis data dan riset',
            ],
            [
                'number' => 16,
                'title' => 'Institusi Yang Kuat',
                'description' => 'Memperkuat kapasitas pemerintahan lokal dalam pengambilan keputusan',
            ],
            [
                'number' => 17,
                'title' => 'Kemitraan',
                'description' => 'Menciptakan jembatan kolaborasi antara akademisi, pemerintah, dan masyarakat',
            ],
        ];

        return view('about.index', compact('statistics', 'team', 'features', 'sdgs'));
    }
}