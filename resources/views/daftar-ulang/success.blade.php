@extends('ppdb.layouts.main')

@section('css')
<style>
    .success-section {
        background-color: #f8fafc;
        padding: 5rem 0;
    }
    
    .success-container {
        max-width: 650px;
        margin: 0 auto;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(26, 42, 58, 0.1);
        overflow: hidden;
        text-align: center;
        padding: 3rem 2rem;
        animation: fadeInUp 0.6s ease;
    }
    
    .success-icon {
        width: 100px;
        height: 100px;
        background-color: rgba(46, 204, 113, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }
    
    .success-icon i {
        font-size: 45px;
        color: #2ecc71;
    }
    
    .success-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1a2a3a;
        margin-bottom: 1rem;
    }
    
    .success-text {
        color: #6c757d;
        font-size: 1.1rem;
        margin-bottom: 2rem;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .home-btn {
        background-color: #4ecdc4;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
    }
    
    .home-btn:hover {
        background-color: #3bafa6;
        transform: translateY(-3px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        color: white;
    }
    
    .notice-box {
        margin-top: 2rem;
        padding: 1rem;
        background-color: rgba(52, 152, 219, 0.1);
        border-radius: 8px;
        border-left: 4px solid #3498db;
    }
    
    .notice-title {
        font-weight: 600;
        color: #3498db;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .notice-text {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 768px) {
        .success-container {
            padding: 2rem 1.5rem;
        }
        
        .success-title {
            font-size: 1.75rem;
        }
    }
</style>
@endsection

@section('content')
<section class="success-section">
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="bi bi-check-lg"></i>
            </div>
            
            <h1 class="success-title">Pendaftaran Berhasil!</h1>
            
            <p class="success-text">
                Formulir daftar ulang Anda telah berhasil dikirim. Admin akan memverifikasi 
                data Anda dan memberikan informasi selanjutnya melalui email.
            </p>
            
            <a href="{{ route('dashboard') }}" class="home-btn">
                <i class="bi bi-house-door"></i> Kembali ke Beranda
            </a>
            
            <div class="notice-box">
                <div class="notice-title">
                    <i class="bi bi-envelope-check"></i> Periksa Email Anda
                </div>
                <p class="notice-text">
                    Silahkan periksa email Anda secara berkala untuk mendapatkan informasi status pendaftaran.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection