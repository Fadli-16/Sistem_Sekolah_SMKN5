<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pendaftaran Mitra Magang SMK Negeri 5 Padang">
    <title>Pendaftaran Mitra Magang - SMK Negeri 5 Padang</title>
    <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1a2a3a;
            --primary-dark: #14212e;
            --secondary: #4ecdc4;
            --secondary-dark: #33b3aa;
            --secondary-light: rgba(78, 205, 196, 0.1);
            --text-dark: #2d3748;
            --text-muted: #64748b;
            --text-light: #f7fafc;
            --bg-light: #ffffff;
            --bg-gray: #f8fafc;
            --border-color: #e2e8f0;
            --radius: 6px;
            --radius-sm: 4px;
            --radius-lg: 12px;
            --shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
            --font-family: 'Poppins', sans-serif;
        }
        
        body {
            font-family: var(--font-family);
            background: linear-gradient(135deg, #f8fafc, #edf2f7);
            color: var(--text-dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        
        /* Form Section Styling */
        .form-section {
            width: 100%;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-container img {
            height: 70px;
            width: auto;
        }
        
        .page-header {
            margin-bottom: 2rem;
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
        
        .page-subtitle {
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Card Styling */
        .form-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 25px rgba(26, 42, 58, 0.15);
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
        
        .input-wrapper i, .select-wrapper i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 1rem;
            color: #64748b;
        }
        
        .form-control, .form-select {
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #4ecdc4;
            box-shadow: 0 0 0 3px rgba(78, 205, 196, 0.1);
            outline: none;
        }
        
        .upload-wrapper {
            border: 2px dashed #e2e8f0;
            padding: 1.5rem;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .upload-wrapper:hover {
            border-color: #4ecdc4;
            background-color: rgba(78, 205, 196, 0.05);
        }
        
        .upload-icon {
            font-size: 2rem;
            color: #4ecdc4;
            margin-bottom: 0.5rem;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #4ecdc4, #3bafa6);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(78, 205, 196, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(78, 205, 196, 0.3);
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
        
        .footer-text {
            text-align: center;
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-section">
            <div class="logo-container">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMK Negeri 5 Padang">
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="page-header">
                        <h1 class="page-title">Pendaftaran Mitra Magang</h1>
                        <p class="page-subtitle">Isi formulir di bawah ini untuk menjadi mitra magang SMK Negeri 5 Padang</p>
                    </div>
                    
                    <div class="form-card">
                        <div class="form-card-body">
                            <form method="POST" action="{{ route('magang.wakil_perusahaan.store') }}" enctype="multipart/form-data">                                @csrf
                                
                                <!-- Data Perusahaan -->
                                <h5 class="mb-4 pb-2 border-bottom fw-bold text-primary">
                                    <i class="bi bi-building me-2"></i> Data Perusahaan
                                </h5>
                                
                                <div class="form-group">
                                    <label for="nama_perusahaan" class="form-label required-field">
                                        <i class="bi bi-building"></i> Nama Perusahaan
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="bi bi-building-fill"></i>
                                        <input id="nama_perusahaan" type="text" class="form-control @error('nama_perusahaan') is-invalid @enderror" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}" required>
                                        @error('nama_perusahaan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="alamat" class="form-label required-field">
                                        <i class="bi bi-geo-alt"></i> Alamat Perusahaan
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        <input id="alamat" type="text" class="form-control @error('alamat') is-invalid @enderror" name="alamat" value="{{ old('alamat') }}" required>
                                        @error('alamat')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="no_perusahaan" class="form-label required-field">
                                        <i class="bi bi-telephone"></i> Nomor Telepon Perusahaan
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="bi bi-telephone-fill"></i>
                                        <input id="no_perusahaan" type="text" class="form-control @error('no_perusahaan') is-invalid @enderror" name="no_perusahaan" value="{{ old('no_perusahaan') }}" required>
                                        @error('no_perusahaan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Data Wakil Perusahaan -->
                                <h5 class="mb-4 mt-5 pb-2 border-bottom fw-bold text-primary">
                                    <i class="bi bi-person-badge me-2"></i> Data Wakil Perusahaan
                                </h5>

                                <div class="form-group">
                                    <label for="nama" class="form-label required-field">
                                        <i class="bi bi-person"></i> Nama Lengkap
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="bi bi-person-fill"></i>
                                        <input id="nama" type="text" class="form-control @error('nama') is-invalid @enderror" name="nama" value="{{ old('nama') }}" required>
                                        @error('nama')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-label required-field">
                                        <i class="bi bi-envelope"></i> Email
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="bi bi-envelope-fill"></i>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="bukti_lampiran" class="form-label required-field">
                                        <i class="bi bi-file-earmark-pdf"></i> Surat Pernyataan/Kerja Sama (PDF)
                                    </label>
                                    <div class="upload-wrapper" onclick="document.getElementById('bukti_lampiran').click();">
                                        <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                        <p class="mb-1">Klik untuk mengunggah atau seret file ke sini</p>
                                        <p class="small text-muted mb-0">Maksimal 5MB, format PDF</p>
                                        <input id="bukti_lampiran" type="file" name="bukti_lampiran" class="d-none @error('bukti_lampiran') is-invalid @enderror" accept="application/pdf" required>
                                        <p id="selected-file" class="mt-2 text-primary d-none"></p>
                                    </div>
                                    @error('bukti_lampiran')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password" class="form-label required-field">
                                        <i class="bi bi-lock"></i> Password
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="bi bi-lock-fill"></i>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password-confirm" class="form-label required-field">
                                        <i class="bi bi-shield-lock"></i> Konfirmasi Password
                                    </label>
                                    <div class="input-wrapper">
                                        <i class="bi bi-shield-lock-fill"></i>
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>

                                <button type="submit" class="submit-btn mt-4">
                                    <i class="bi bi-send"></i> Daftar Sekarang
                                </button>
                            </form>
                            
                            <a href="{{ route('dashboard') }}" class="back-link">
                                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                    
                    <div class="footer-text">
                        &copy; {{ date('Y') }} SMK Negeri 5 Padang. Hak Cipta Dilindungi.
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('bukti_lampiran').addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                const fileSize = (e.target.files[0].size / 1024 / 1024).toFixed(2);
                const selectedFileElem = document.getElementById('selected-file');
                
                selectedFileElem.textContent = `File terpilih: ${fileName} (${fileSize} MB)`;
                selectedFileElem.classList.remove('d-none');
            }
        });
        
        // Allow drag and drop file upload
        const dropArea = document.querySelector('.upload-wrapper');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight(e) {
            dropArea.classList.add('bg-light');
        }
        
        function unhighlight(e) {
            dropArea.classList.remove('bg-light');
        }
        
        dropArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                document.getElementById('bukti_lampiran').files = files;
                
                // Trigger the change event manually
                const event = new Event('change');
                document.getElementById('bukti_lampiran').dispatchEvent(event);
            }
        }
    </script>
</body>
</html>