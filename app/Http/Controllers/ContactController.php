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
                'name' => 'Kenzie Raffa Ardhana',
                'role' => 'Project Manager',
                'description' => 'Mengkoordinasi keseluruhan proyek dan memastikan semua berjalan sesuai rencana.',
                'photo' => 'profile_18223127.jpg',
                'email' => 'kenzieraffa0709@gmail.com',
                'whatsapp' => '+62 878-8533-1404'
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
                'name' => 'M. Abizzar Gamadrian',
                'role' => 'Backend Developer & Technical Support',
                'description' => 'Mengembangkan sistem backend dan memberikan dukungan teknis untuk pengguna.',
                'photo' => 'profile_13523155.jpg',
                'email' => '13523155@std.stei.itb.ac.id',
                'whatsapp' => '+62 822-8930-9848'
            ],
        ];

        return view('contact.index', compact('teamMembers'));
    }
}