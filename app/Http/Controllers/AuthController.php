<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function index()
    {
        $title = 'Login';
        $header = 'SMK Negeri 5 Padang';
        return view('auth.login', compact('title', 'header'));
    }

    public function authenticate(Request $request)
    {
        // Validasi input
        $request->validate([
            'nis_nip' => 'required|string',
            'password' => 'required',
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Harap centang verifikasi Captcha.'
        ]);

        // Verifikasi reCAPTCHA
        $recaptchaResponse = $request->input('g-recaptcha-response');
        $secretKey = config('services.recaptcha.secret_key');
        
        $verifyResponse = Http::withoutVerifying()->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $request->ip()
        ]);

        $responseData = $verifyResponse->json();
        
        if (!$responseData['success']) {
            return redirect()->back()->with('loginError', 'Verifikasi Captcha gagal. Silakan coba lagi.');
        }

        // Deteksi apakah input adalah email atau NIS/NIP
        $loginField = filter_var($request->nis_nip, FILTER_VALIDATE_EMAIL) ? 'email' : 'nis_nip';

        // Coba login dengan field yang sesuai
        if (Auth::attempt([$loginField => $request->nis_nip, 'password' => $request->password])) {
            $request->session()->regenerate();

            $from = $request->input('from', '');
            $intended = $request->session()->get('url.intended', '');
            $isLabRequest = str_contains($intended, 'lab') || str_contains($intended, 'laboratorium') || $from === 'laboratory';

            switch (Auth::user()->role) {
                case 'super_admin':
                    return redirect()->intended('/admin/manage');
                case 'admin_ppdb':
                    return redirect()->intended('/ppdb');
                case 'admin_lab':
                    return redirect()->intended('/admin/dashboard');
                case 'admin_sa':
                    return redirect()->intended('/sistem-akademik/dashboard');
                case 'admin_perpus':
                    return redirect()->intended('/perpustakaan/buku');
                case 'admin_magang':
                    return redirect()->intended('/magang/dashboard');
                case 'wakil_perusahaan':
                    return redirect()->route('magang.wakil_perusahaan.dashboard');
                case 'guru':
                    return redirect()->intended('/sistem-akademik/dashboard');
                case 'siswa':
                    if ($isLabRequest) {
                        return redirect()->route('siswa.labor.index');
                    } else {
                        return redirect()->intended('/sistem-akademik/dashboard');
                    }
                default:
                    return redirect()->intended('/');
            }
        }

        // Jika gagal login
        return redirect()->back()->with('loginError', 'Email / NIS / NIP atau password salah');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
