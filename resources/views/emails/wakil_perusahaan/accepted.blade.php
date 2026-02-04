<!DOCTYPE html>
<html>
<head>
    <title>Pendaftaran Mitra Magang Disetujui</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background-color: #1a2a3a;
            padding: 20px;
            text-align: center;
            color: white;
        }
        
        .logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .content {
            padding: 30px;
            color: #555;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        
        .message {
            margin-bottom: 25px;
        }
        
        .credentials {
            background-color: #f2f8ff;
            border-left: 4px solid #4ecdc4;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 4px;
        }
        
        .credentials p {
            margin: 5px 0;
        }
        
        .button {
            display: inline-block;
            background-color: #4ecdc4;
            color: white;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: bold;
            margin: 15px 0;
        }
        
        .footer {
            background-color: #f2f2f2;
            padding: 15px;
            text-align: center;
            font-size: 13px;
            color: #777;
        }
        
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMK" class="logo">
            <h1>Pendaftaran Mitra Magang Disetujui</h1>
        </div>
        
        <div class="content">
            <p class="greeting">Halo, {{ $applicant->nama }}!</p>
            
            <div class="message">
                <p>Selamat! Pengajuan pendaftaran Anda sebagai Mitra Magang untuk {{ $applicant->nama_perusahaan }} pada program magang SMK Negeri 5 Padang telah <strong>disetujui</strong>.</p>
                
                <p>Kami sangat senang menyambut Anda sebagai bagian dari program magang kami dan kami berharap dapat menjalin kerja sama yang bermanfaat bagi siswa-siswi kami serta bagi perusahaan Anda.</p>
            </div>
            
            <div class="credentials">
                <p><strong>Informasi Akun:</strong></p>
                <p><strong>Email:</strong> {{ $applicant->email }}</p>
                <p><strong>Password:</strong> <em>Password yang Anda masukkan saat pendaftaran</em></p>
            </div>
            
            <p>Anda sekarang dapat mengakses dashboard Mitra Magang untuk mulai mengelola program magang dari perusahaan Anda. Silakan login menggunakan kredensial di atas melalui tombol berikut:</p>
            
            <div style="text-align: center;">
                <a href="{{ route('login') }}" class="button">Login ke Dashboard</a>
            </div>
            
            <p>Jika Anda memiliki pertanyaan atau memerlukan bantuan, jangan ragu untuk menghubungi tim kami di <a href="mailto:magang@smkn5padang.sch.id">magang@smkn5padang.sch.id</a> atau telepon (0751) 123456.</p>
            
            <p>Terima kasih atas partisipasi Anda dalam program magang SMK Negeri 5 Padang.</p>
            
            <p>Salam,<br>
            Tim Magang<br>
            SMK Negeri 5 Padang</p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon jangan membalas email ini.</p>
            <p>&copy; {{ date('Y') }} SMK Negeri 5 Padang. All rights reserved.</p>
        </div>
    </div>
</body>
</html>