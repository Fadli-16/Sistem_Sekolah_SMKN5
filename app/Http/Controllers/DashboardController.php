<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Dashboard';
        $header = 'Sistem Informasi SMK Padang';

        // Hanya tampilkan status publish dan kategori informasi & prestasi
        $query = \App\Models\Berita::where('status', 'publish')
            ->whereIn('kategori', ['informasi', 'prestasi']);

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('isi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori')) {
            if (in_array($request->kategori, ['informasi', 'prestasi'])) {
                $query->where('kategori', $request->kategori);
            }
        }
        
        $sharedBerita = null;
        if ($request->filled('berita_id')) {
            $sharedBerita = \App\Models\Berita::where('status', 'publish')->find($request->berita_id);
        }

        $berita = $query->latest()->paginate(3)->withQueryString();

        $kepsek = \App\Models\Guru::with('user')->where('status', 'kepala sekolah')->first();
        $koordinators = \App\Models\Guru::with('user')->where('status', 'koordinator')->get();
        $kepala_bidangs = \App\Models\Guru::with('user')->where('status', 'kepala bidang')->get();
        $wakas = \App\Models\Guru::with('user')->where('status', 'wakil kepala')->get();
        $bendaharas = \App\Models\Guru::with('user')->where('status', 'bendahara')->get();
        $kajurs = \App\Models\Guru::with('user')->where('status', 'kepala jurusan')->get();
        $kabengs = \App\Models\Guru::with('user')->where('status', 'kepala bengkel')->get();

        return view('home.index', compact('title', 'header', 'berita', 'sharedBerita', 'kepsek', 'koordinators', 'kepala_bidangs', 'wakas', 'bendaharas', 'kajurs', 'kabengs'));
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