<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informasi Pendaftaran Ulang</title>
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
            background-color: #3498db;
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
        .notes {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #3498db;
            border-radius: 3px;
        }
        .btn {
            display: inline-block;
            background-color: #3498db;
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
        <h1>Informasi Pendaftaran Ulang</h1>
    </div>
    
    <div class="content">
        <p>Halo <strong>{{ $name }}</strong>,</p>
        
        <p>Terima kasih telah mendaftar ulang di SMK Negeri 5 Padang. Setelah melakukan evaluasi, dengan berat hati kami sampaikan bahwa pendaftaran ulang Anda belum dapat kami setujui saat ini.</p>
        
        @if($notes)
        <div class="notes">
            <p><strong>Catatan dari Admin:</strong></p>
            <p>{{ $notes }}</p>
        </div>
        @endif
        
        <p>Kami mendorong Anda untuk tetap semangat dan tidak menyerah dalam mengejar pendidikan. Ada banyak jalur pendidikan yang dapat Anda tempuh untuk mencapai cita-cita Anda.</p>
        
        <p>Jika Anda memiliki pertanyaan atau memerlukan klarifikasi lebih lanjut, silakan hubungi tim kami di nomor (0751) 123456 atau melalui email di info@smkn5padang.sch.id.</p>
        
        <p>Kami doakan yang terbaik untuk masa depan Anda.</p>
        
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