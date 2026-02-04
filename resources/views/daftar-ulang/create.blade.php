@extends('ppdb.layouts.main')

@section('css')
<style>
    /* Menggunakan tema perpustakaan */
    .form-section {
        background-color: #f8fafc;
        padding: 3rem 0;
    }
    
    .page-header {
        margin-bottom: 2.5rem;
        text-align: center;
    }
    
    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #1a2a3a;
        margin-bottom: 0.75rem;
        position: relative;
        display: inline-block;
    }
    
    .page-title::after {
        content: '';
        display: block;
        width: 70px;
        height: 3px;
        background: linear-gradient(to right, #3bafa6, #4ecdc4);
        margin: 0.5rem auto 0;
        border-radius: 2px;
    }
    
    .form-card {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(26, 42, 58, 0.1);
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        animation: fadeIn 0.6s ease;
    }
    
    .form-card-body {
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #1a2a3a;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-label i {
        color: #4ecdc4;
    }
    
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    
    .input-wrapper, .select-wrapper {
        position: relative;
    }
    
    .form-control, .form-select {
        padding: 0.75rem 1rem;
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        font-size: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #4ecdc4;
        box-shadow: 0 0 0 0.2rem rgba(78, 205, 196, 0.25);
    }
    
    .form-text {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .invalid-feedback:before {
        content: "\F33E";
        font-family: "bootstrap-icons";
    }
    
    .alert-danger ul {
        margin-bottom: 0;
    }
    
    .submit-btn {
        background-color: #4ecdc4;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
    }
    
    .submit-btn:hover {
        background-color: #3bafa6;
        transform: translateY(-3px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }
    
    .back-link {
        color: #6c757d;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        font-size: 0.9rem;
        margin-top: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .back-link:hover {
        color: #4ecdc4;
        transform: translateX(-3px);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 768px) {
        .form-card-body {
            padding: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<section class="form-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 col-12">
                <div class="page-header">
                    <h1 class="page-title">Formulir Daftar Ulang</h1>
                    <p class="text-muted">Silahkan lengkapi formulir berikut untuk melakukan daftar ulang di SMK Negeri 5 Padang</p>
                </div>
                
                <div class="form-card">
                    <div class="form-card-body">
                        @if ($errors->any())
                        <div class="alert alert-danger mb-4">
                            <div class="d-flex">
                                <div class="me-2">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading mb-1">Terdapat kesalahan pada formulir</h5>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <form action="{{ route('daftar-ulang.store') }}" method="POST">
                            @csrf
                            
                            <!-- Nama Lengkap -->
                            <div class="form-group">
                                <label for="name" class="form-label required-field">
                                    <i class="bi bi-person"></i> Nama Lengkap
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="Masukkan nama lengkap Anda" required>
                                </div>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div class="form-group">
                                <label for="email" class="form-label required-field">
                                    <i class="bi bi-envelope"></i> Email
                                </label>
                                <div class="input-wrapper">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" 
                                           placeholder="Masukkan alamat email Anda" required>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Email ini akan digunakan untuk login ke sistem
                                </div>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Jurusan -->
                            <div class="form-group">
                                <label for="major" class="form-label required-field">
                                    <i class="bi bi-mortarboard"></i> Jurusan
                                </label>
                                <div class="select-wrapper">
                                    <select class="form-select @error('major') is-invalid @enderror" 
                                            id="major" name="major" required>
                                        <option value="" selected disabled>-- Pilih Jurusan --</option>
                                        @foreach($majors as $major)
                                        <option value="{{ $major }}" {{ old('major') == $major ? 'selected' : '' }}>
                                            {{ $major }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('major')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Jenis Kelamin -->
                            <div class="form-group">
                                <label for="jenis_kelamin" class="form-label required-field">
                                    <i class="bi bi-gender-ambiguous"></i> Jenis Kelamin
                                </label>
                                <div class="select-wrapper">
                                    <select class="form-select @error('jenis_kelamin') is-invalid @enderror" 
                                            id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="" selected disabled>-- Pilih Jenis Kelamin --</option>
                                        <option value="Pria" {{ old('jenis_kelamin') == 'Pria' ? 'selected' : '' }}>Pria</option>
                                        <option value="Wanita" {{ old('jenis_kelamin') == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                                    </select>
                                </div>
                                @error('jenis_kelamin')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Tanggal Lahir -->
                            <div class="form-group">
                                <label for="tanggal_lahir" class="form-label required-field">
                                    <i class="bi bi-calendar-date"></i> Tanggal Lahir
                                </label>
                                <div class="input-wrapper">
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror" 
                                           id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required>
                                </div>
                                @error('tanggal_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Alamat -->
                            <div class="form-group">
                                <label for="alamat" class="form-label required-field">
                                    <i class="bi bi-geo-alt"></i> Alamat
                                </label>
                                <div class="input-wrapper">
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                              id="alamat" name="alamat" rows="3" 
                                              placeholder="Masukkan alamat lengkap Anda" required>{{ old('alamat') }}</textarea>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Minimal 10 karakter
                                </div>
                                @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- No HP -->
                            <div class="form-group">
                                <label for="no_hp" class="form-label required-field">
                                    <i class="bi bi-phone"></i> Nomor HP
                                </label>
                                <div class="input-wrapper">
                                    <input type="tel" class="form-control @error('no_hp') is-invalid @enderror" 
                                           id="no_hp" name="no_hp" value="{{ old('no_hp') }}" 
                                           placeholder="Masukkan nomor HP Anda" required>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Nomor HP aktif yang dapat dihubungi
                                </div>
                                @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Password -->
                            <div class="form-group">
                                <label for="password" class="form-label required-field">
                                    <i class="bi bi-lock"></i> Password
                                </label>
                                <div class="input-wrapper">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" required>
                                </div>
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Minimal 8 karakter
                                </div>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Konfirmasi Password -->
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label required-field">
                                    <i class="bi bi-lock-fill"></i> Konfirmasi Password
                                </label>
                                <div class="input-wrapper">
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                <i class="bi bi-send"></i> Kirim Pendaftaran
                            </button>
                        </form>
                        
                        <a href="{{ route('dashboard') }}" class="back-link">
                            <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection