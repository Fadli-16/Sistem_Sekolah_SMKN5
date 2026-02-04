<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WakilPerusahaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WakilPerusahaanAccepted;
use App\Mail\WakilPerusahaanRejected;

class AdminWakilPerusahaanController extends Controller
{
    public function index()
    {
        $applicants = WakilPerusahaan::all();
        $title = 'Kelola Mitra Magang';
        $header = 'Kelola Pendaftaran Mitra Magang';
        
        return view('magang.admin.wakil_perusahaan.index', compact('applicants', 'title', 'header'));
    }
    
    public function approve($id)
    {
        $applicant = WakilPerusahaan::findOrFail($id);
        $applicant->status = 'Accepted';
        $applicant->save();
        
        // Create user account with wakil_perusahaan role
        $user = User::create([
            'nama' => $applicant->nama,
            'email' => $applicant->email,
            'password' => $applicant->password, // Already hashed during registration
            'role' => 'wakil_perusahaan',
        ]);
        
        // Send email notification
        Mail::to($applicant->email)->send(new WakilPerusahaanAccepted($applicant));
        
        return redirect()->back()->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Pendaftaran berhasil disetujui dan akun telah dibuat.');
    }
    
    public function reject($id)
    {
        $applicant = WakilPerusahaan::findOrFail($id);
        $applicant->status = 'Rejected';
        $applicant->save();
        
        // Send email notification
        Mail::to($applicant->email)->send(new WakilPerusahaanRejected($applicant));
        
        return redirect()->back()->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Pendaftaran ditolak dan email notifikasi telah dikirim.');
    }
}