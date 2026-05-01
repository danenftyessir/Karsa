<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * tampilkan homepage
     */
    public function index()
    {
        // TODO: load statistics untuk homepage
        $stats = [
            'total_projects' => 150,
            'total_students' => 1200,
            'total_institutions' => 85,
            'completed_projects' => 120,
        ];

        return view('home.index', compact('stats'));
    }
}