<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LulusDaftarUlangMail;
use App\Mail\TidakLulusDaftarUlangMail;
use App\Models\DaftarUlangSiswa;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminPpdbController extends Controller
{
    public function daftarUlangIndex()
    {
        $title = 'Kelola Daftar Ulang';
        $header = 'Kelola Daftar Ulang Siswa';
        
        $pendaftaran = DaftarUlangSiswa::orderBy('created_at', 'desc')->get();
        
        return view('ppdb.daftar-ulang.index', compact('title', 'header', 'pendaftaran'));
    }
    
    public function approveDaftarUlang($id)
    {
        $pendaftaran = DaftarUlangSiswa::findOrFail($id);
        
        // Only process if status is pending
        if ($pendaftaran->status != 'pending') {
            return redirect()->back()
                ->with('status', 'error')
                ->with('title', 'Gagal')
                ->with('message', 'Status pendaftaran sudah diubah sebelumnya.');
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Get original password (non-hashed) for email
            $originalPassword = 'Password yang Anda masukkan saat pendaftaran';
            
            // Create user account
            $user = User::create([
                'nama' => $pendaftaran->name,
                'email' => $pendaftaran->email,
                'password' => $pendaftaran->password, // Password already hashed
                'role' => 'siswa',
            ]);
            
            // Get a default class or first available class
            $kelas = Kelas::first();
            
            if (!$kelas) {
                // Jika tidak ada kelas, buat kelas default berdasarkan jurusan
                $kelas = Kelas::create([
                    'nama_kelas' => 'X',
                    'jurusan' => $pendaftaran->major,
                    'tahun_ajaran' => date('Y') . '/' . (date('Y') + 1),
                ]);
            }
            
            // Create student record with non-null required fields
            Siswa::create([
                'user_id' => $user->id,
                'nisn' => 'S' . date('Y') . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'kelas_id' => $kelas->id,
                'kelas' => $kelas->nama_kelas,
                'jurusan' => $pendaftaran->major,
                'jenis_kelamin' => $pendaftaran->jenis_kelamin,
                'tanggal_lahir' => $pendaftaran->tanggal_lahir,
                'alamat' => $pendaftaran->alamat,
                'no_hp' => $pendaftaran->no_hp,
            ]);
            
            // Update daftar ulang status
            $pendaftaran->status = 'approved';
            $pendaftaran->save();
            
            // Send email notification
            try {
                Mail::to($pendaftaran->email)
                    ->send(new LulusDaftarUlangMail(
                        $pendaftaran->name,
                        $pendaftaran->email,
                        $originalPassword
                    ));
                    
                $emailStatus = 'Email notifikasi berhasil dikirim ke alamat ' . $pendaftaran->email;
            } catch (\Exception $e) {
                Log::error('Error sending approval email: ' . $e->getMessage());
                $emailStatus = 'Pendaftaran disetujui tetapi gagal mengirim email notifikasi: ' . $e->getMessage();
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('status', 'success')
                ->with('title', 'Berhasil')
                ->with('message', 'Pendaftaran berhasil disetujui dan akun siswa telah dibuat. ' . $emailStatus);
                
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('status', 'error')
                ->with('title', 'Gagal')
                ->with('message', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function rejectDaftarUlang(Request $request, $id)
    {
        $pendaftaran = DaftarUlangSiswa::findOrFail($id);
        
        // Only process if status is pending
        if ($pendaftaran->status != 'pending') {
            return redirect()->back()
                ->with('status', 'error')
                ->with('title', 'Gagal')
                ->with('message', 'Status pendaftaran sudah diubah sebelumnya.');
        }
        
        $pendaftaran->status = 'rejected';
        $pendaftaran->notes = $request->notes;
        $pendaftaran->save();
        
        // Send email notification
        try {
            Mail::to($pendaftaran->email)
                ->send(new TidakLulusDaftarUlangMail(
                    $pendaftaran->name,
                    $pendaftaran->notes
                ));
                
            $emailStatus = 'Email notifikasi berhasil dikirim ke alamat ' . $pendaftaran->email;
        } catch (\Exception $e) {
            Log::error('Error sending rejection email: ' . $e->getMessage());
            $emailStatus = 'Pendaftaran ditolak tetapi gagal mengirim email notifikasi: ' . $e->getMessage();
        }
        
        return redirect()->back()
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Pendaftaran telah ditolak. ' . $emailStatus);
    }
}