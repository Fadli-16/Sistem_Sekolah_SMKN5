<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Selamat, Anda Lulus Daftar Ulang!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4ecdc4;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .credentials {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #4ecdc4;
            border-radius: 3px;
        }
        .btn {
            display: inline-block;
            background-color: #4ecdc4;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Selamat, Anda Lulus Daftar Ulang!</h1>
    </div>
    
    <div class="content">
        <p>Halo <strong>{{ $name }}</strong>,</p>
        
        <p>Kami dengan senang hati menginformasikan bahwa Anda telah <strong>LULUS</strong> proses daftar ulang di SMK Negeri 5 Padang. Selamat bergabung sebagai bagian dari keluarga besar kami!</p>
        
        <p>Anda dapat mengakses sistem informasi sekolah dengan menggunakan kredensial berikut:</p>
        
        <div class="credentials">
            <p><strong>Email:</strong> {{ $email }}</p>
            <p><strong>Password:</strong> <em>Password yang Anda masukkan saat mendaftar</em></p>
        </div>
        
        <p>Jika Anda memiliki pertanyaan atau memerlukan bantuan, jangan ragu untuk menghubungi kami.</p>
        
        <p>Sekali lagi, selamat bergabung dengan SMK Negeri 5 Padang!</p>
        
        <p>Salam,<br>
        Tim Administrasi<br>
        SMK Negeri 5 Padang</p>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim secara otomatis, mohon jangan membalas email ini.</p>
        <p>&copy; {{ date('Y') }} SMK Negeri 5 Padang. All rights reserved.</p>
    </div>
</body>
</html>