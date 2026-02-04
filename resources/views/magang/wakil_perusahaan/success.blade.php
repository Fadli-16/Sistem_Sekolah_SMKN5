<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pendaftaran Mitra Magang SMK Negeri 5 Padang">
    <title>Pendaftaran Berhasil - SMK Negeri 5 Padang</title>
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
        
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-container img {
            height: 70px;
            width: auto;
        }
        
        .success-container {
            background-color: white;
            border-radius: var(--radius-lg);
            padding: 3rem 2rem;
            box-shadow: 0 4px 25px rgba(26, 42, 58, 0.15);
            text-align: center;
            animation: fadeIn 0.6s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .success-icon {
            font-size: 5rem;
            color: var(--secondary);
            margin-bottom: 1.5rem;
            animation: scaleIn 0.5s ease;
        }
        
        @keyframes scaleIn {
            from { transform: scale(0.5); }
            to { transform: scale(1); }
        }
        
        .success-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .success-message {
            font-size: 1.1rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .home-btn {
            background: linear-gradient(135deg, #4ecdc4, #3bafa6);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: 0 4px 10px rgba(78, 205, 196, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .home-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(78, 205, 196, 0.3);
            color: white;
        }
        
        .footer-text {
            text-align: center;
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .success-container {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMK Negeri 5 Padang">
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="success-container">
                    <div class="success-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h2 class="success-title">Pendaftaran Berhasil!</h2>
                    <p class="success-message">
                        Terima kasih telah mendaftar sebagai Mitra Magang SMK Negeri 5 Padang. 
                        Pengajuan Anda sedang diproses dan akan segera ditinjau oleh tim kami. 
                        Kami akan mengirimkan email notifikasi kepada Anda mengenai status pendaftaran.
                    </p>
                    <a href="{{ route('dashboard') }}" class="home-btn">
                        <i class="bi bi-house-door"></i> Kembali ke Beranda
                    </a>
                </div>
                
                <div class="footer-text">
                    &copy; {{ date('Y') }} SMK Negeri 5 Padang. Hak Cipta Dilindungi.
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>