<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Favicon icon -->
  <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">

  <title>Email Pemberitahuan PPDB — Sistem Informasi PPDB SMK</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('vendors/fontawesome/css/all.min.css') }}">
  <!-- Theme style -->
  <link href="{{ asset('/template-admin/assets/css/app-saas.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

  
  {{-- Biar nggak masuk ke SEO Google Start --}}
  <meta name="googlebot-news" content="noindex,nofollow" />
  <meta name="googlebot" content="noindex,nofollow">
  <meta name="robots" content="noindex,nofollow">
  {{-- Biar nggak masuk ke SEO Google End --}}

  <style>
    body {
      font-family: 'Public Sans', sans-serif;
    }

    footer a {
      display: block;
      text-decoration: underline;
    }
  </style>

</head>

<body>
  <div style="max-width:53.5rem;padding: 5%;background-color: #F6F6F6;border-radius: 1rem;margin: auto;">
    <header style="margin-top: 1.5rem !important;">
      <a href="{{ url('/') }}" target="_blank">
        <img src="{{ asset('assets/images/logo.png') }}" alt="*Gambar Logo Perusahaan" style="width: 7rem;">
      </a>
    </header>
    <main style="margin-top: 1.5rem !important;">
      <p style="margin-top: 1.5rem !important;font-size: 20px;">{!!$message_custom!!}</p>
    </main>
    <footer>
      <table style="margin-top: 6rem;">
        <tr>
          <td style="width:8rem;" valign="top">
            <a href="{{ url('/') }}" target="_blank">
              <img src="{{ asset('assets/images/logo.png') }}" alt="*Gambar Logo Perusahaan" style="width: 80%;">
            </a>
          </td>
          <td>
            <span>
              <div style="font-weight: bold;font-size: 19px;">Ada Pertanyaan? Langsung saja kirim pertanyaan Anda di</div>
              <div style="font-size: 20px;">
                <img src="{{asset("assets/icon/whatsapp-icon.png")}}" alt="*Icon WhatsApp" width="28">
                <span style="margin-left:0.5rem;">{{$bantuan_wa}}</span>
              </div>
              <div style="font-size: 20px;">
                <img src="{{asset("assets/icon/headset-icon.png")}}" width="28" alt="*Icon Headline">
                <span style="margin-left:0.7rem;">
                  <a href="#" style="color:#000; text-decoration:none;cursor:default; display: inline-block!important;">{{$bantuan_email}}</a>
                </span>
              </div>
            </span>
          </td>
        </tr>
      </table>
    </footer>
  </div>
</body>
</html>
