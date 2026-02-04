<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        $header = 'Sistem Informasi SMK Padang';
        return view('home.index', compact('title', 'header'));
    }

    public function labor()
    {
        $title = 'Laboratorium';
        $header = 'Sistem Informasi Laboratorium SMK';
        return view('dashboard.main.index', compact('title', 'header'));
    }

    public function admin()
    {
        $title = 'Dashboard';
    
    $user = Auth::user();
    
    if ($user->role == 'siswa' && (request()->segment(1) == 'lab' || request()->is('admin/dashboard'))) {
        // Show only lab-specific content for students
        return view('admin.main.index', compact('title'));
    }
    
    // Normal admin view for admin users
    return view('admin.main.index', compact('title'));
    }
}