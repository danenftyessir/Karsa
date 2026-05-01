<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * tampilkan halaman contact
     */
    public function index()
    {
        // data tim untuk ditampilkan di halaman contact
        $teamMembers = [
            [
                'name' => 'Ardell Aghna Mahendra',
                'role' => 'Frontend Developer',
                'description' => 'Mengkoordinasi keseluruhan proyek sesuai dengan alur dan memastikan semua UI berjalan sesuai rencana.',
                'photo' => 'profile_18223127.jpg',
                'email' => '13523151@std.stei.itb.ac.id',
                'whatsapp' => '+62 878-8843-9192'
            ],
            [
                'name' => 'Danendra Shafi Athallah',
                'role' => 'Full Stack Developer',
                'description' => 'Mengembangkan frontend dan backend untuk memastikan sistem berjalan optimal.',
                'photo' => 'profile_13523136.jpg',
                'email' => 'danendra1967@gmail.com',
                'whatsapp' => '+62 812-2027-7660'
            ],
            [
                'name' => 'Muhammad Raihaan Perdana',
                'role' => 'Backend Developer & Technical Support',
                'description' => 'Mengembangkan sistem backend dan memberikan dukungan teknis untuk pengguna.',
                'photo' => 'profile_13523124.jpg',
                'email' => '13523124@std.stei.itb.ac.id',
                'whatsapp' => '+62 812-7445-6443'
            ],
        ];

        return view('contact.index', compact('teamMembers'));
    }
}