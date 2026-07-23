<section class="teachers-section py-5">
    <div class="container">
        <div class="section-title text-center mb-5" data-aos="fade-up">
            <h2>Tenaga Pendidik</h2>
            <p>Mengenal lebih dekat para pendidik profesional kami yang berdedikasi untuk memberikan pendidikan terbaik</p>
        </div>

        <!-- Principal Card -->
        <div class="mb-5" data-aos="fade-up" data-aos-delay="100">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="principal-card p-4 p-md-5 rounded-4 shadow">
                        <div class="row align-items-center">
                            <div class="col-md-4 mb-4 mb-md-0 text-center">
                                <div class="principal-img-wrapper position-relative mx-auto">
                                    <div class="principal-img-border"></div>
                                    <div class="principal-img rounded-circle overflow-hidden mx-auto">
                                        @php
                                            $kepsekImg = ($kepsek && $kepsek->image) ? asset('assets/profile/' . ltrim($kepsek->image,'/')) : asset('assets/images/kepsek.jpg');
                                            $kepsekName = $kepsek->user->nama ?? 'Rizka Fauzi Yosfi, S.Pd., S.T., M.Kom';
                                        @endphp
                                        <img src="{{ $kepsekImg }}" loading="lazy" alt="Kepala Sekolah" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="principal-title-line me-3"></div>
                                    <h3 class="principal-name m-0">{{ $kepsekName }}</h3>
                                </div>
                                <p class="principal-title fw-semibold mb-3">Kepala Sekolah</p>
                                <p class="principal-quote fst-italic mb-4">"Pendidikan adalah kunci untuk membuka pintu kesuksesan. Kami berkomitmen untuk mengembangkan potensi setiap siswa kami menjadi insan yang cerdas, terampil, dan berakhlak mulia."</p>
                                <div class="principal-credentials">
                                    <span class="credential-badge me-2 mb-2">S2 ilmu komputer</span>
                                    <span class="credential-badge me-2 mb-2">20+ Tahun Pengalaman</span>
                                </div>
                                <a href="{{ url('/profil-kepsek') }}" class="btn btn-primary">Lihat Profil Kepala Sekolah</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<!-- Department Heads Section -->
@if($wakas->count() > 0)
<h3 class="department-heading text-center mb-4 mt-5" data-aos="fade-up" data-aos-delay="200">Wakil Kepala</h3>
<div class="teachers-grid mb-5">
    @foreach($wakas as $index => $guru)
    @php
        $img = $guru->image ? asset('assets/profile/' . ltrim($guru->image,'/')) : asset('assets/images/profile.png');
        $delay = 300 + ($index * 100);
        $jabatan = trim('Wakil Kepala ' . ($guru->spesialisasi ?: ''));
    @endphp
    <div class="teacher-card" data-aos="zoom-in-up" data-aos-delay="{{ $delay }}">
        <div class="teacher-img-wrapper">
            <div class="teacher-img">
                <img src="{{ $img }}" loading="lazy" alt="{{ $guru->user->nama ?? '' }}">
            </div>
        </div>
        <div class="teacher-info">
            <h4 class="teacher-name">{{ $guru->user->nama ?? '' }}</h4>
            <span class="teacher-badge">{{ $jabatan }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

@if($bendaharas->count() > 0)
<h3 class="department-heading text-center mb-4 mt-5" data-aos="fade-up" data-aos-delay="200">Keuangan / Bendahara</h3>
<div class="teachers-grid mb-5">
    @foreach($bendaharas as $index => $guru)
    @php
        $img = $guru->image ? asset('assets/profile/' . ltrim($guru->image,'/')) : asset('assets/images/profile.png');
        $delay = 300 + ($index * 100);
        $jabatan = trim('Bendahara ' . ($guru->spesialisasi ?: ''));
    @endphp
    <div class="teacher-card" data-aos="zoom-in-up" data-aos-delay="{{ $delay }}">
        <div class="teacher-img-wrapper">
            <div class="teacher-img">
                <img src="{{ $img }}" loading="lazy" alt="{{ $guru->user->nama ?? '' }}">
            </div>
        </div>
        <div class="teacher-info">
            <h4 class="teacher-name">{{ $guru->user->nama ?? '' }}</h4>
            <span class="teacher-badge">{{ $jabatan }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

@if($kajurs->count() > 0)
<h3 class="department-heading text-center mb-4 mt-5" data-aos="fade-up" data-aos-delay="200">Kepala Jurusan</h3>
<div class="teachers-grid mb-5">
    @foreach($kajurs as $index => $guru)
    @php
        $img = $guru->image ? asset('assets/profile/' . ltrim($guru->image,'/')) : asset('assets/images/profile.png');
        $delay = 300 + ($index * 100);
        $jabatan = trim('Kepala Jurusan ' . ($guru->spesialisasi ?: $guru->jurusan));
    @endphp
    <div class="teacher-card" data-aos="zoom-in-up" data-aos-delay="{{ $delay }}">
        <div class="teacher-img-wrapper">
            <div class="teacher-img">
                <img src="{{ $img }}" loading="lazy" alt="{{ $guru->user->nama ?? '' }}">
            </div>
        </div>
        <div class="teacher-info">
            <h4 class="teacher-name">{{ $guru->user->nama ?? '' }}</h4>
            <span class="teacher-badge">{{ $jabatan }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

@if($kabengs->count() > 0)
<h3 class="department-heading text-center mb-4 mt-5" data-aos="fade-up" data-aos-delay="200">Kepala Bengkel / Teknisi</h3>
<div class="teachers-grid">
    @foreach($kabengs as $index => $guru)
    @php
        $img = $guru->image ? asset('assets/profile/' . ltrim($guru->image,'/')) : asset('assets/images/profile.png');
        $delay = 300 + ($index * 100);
        $jabatan = trim('Kepala Bengkel ' . ($guru->spesialisasi ?: ''));
    @endphp
    <div class="teacher-card" data-aos="zoom-in-up" data-aos-delay="{{ $delay }}">
        <div class="teacher-img-wrapper">
            <div class="teacher-img">
                <img src="{{ $img }}" loading="lazy" alt="{{ $guru->user->nama ?? '' }}">
            </div>
        </div>
        <div class="teacher-info">
            <h4 class="teacher-name">{{ $guru->user->nama ?? '' }}</h4>
            <span class="teacher-badge">{{ $jabatan }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif


</section>

<style>
    /* General Section Styling */
    .teachers-section {
        background-color: #f9fbfd;
        position: relative;
        overflow: hidden;
    }
    
    .teachers-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(#e0e8f5 1px, transparent 1px);
        background-size: 20px 20px;
        opacity: 0.4;
        z-index: 0;
    }
    
    .teachers-section .container {
        position: relative;
        z-index: 1;
    }
    
    .section-title h2 {
        font-weight: 700;
        margin-bottom: 0.75rem;
        color: #004080;
        position: relative;
        display: inline-block;
    }
    
    .section-title h2::after {
        content: '';
        display: block;
        width: 70px;
        height: 3px;
        background: linear-gradient(to right, #004080, #4e92df);
        margin: 0.5rem auto 0;
        border-radius: 2px;
    }
    
    .department-heading {
        font-size: 1.4rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 2rem;
        position: relative;
    }
    
    .department-heading::before,
    .department-heading::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 60px;
        height: 1px;
        background-color: rgba(0, 64, 128, 0.2);
    }
    
    .department-heading::before {
        left: calc(50% - 120px);
    }
    
    .department-heading::after {
        right: calc(50% - 120px);
    }
    
    /* Principal Card Styling */
    .principal-card {
        background: linear-gradient(135deg, #ffffff, #f8f9fa);
        transition: all 0.35s ease;
        border: 1px solid rgba(0, 64, 128, 0.07);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important;
    }
    
    .principal-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 15px 35px rgba(0, 64, 128, 0.1) !important;
    }
    
    .principal-img-wrapper {
        display: inline-block;
    }
    
    .principal-img-border {
        position: absolute;
        top: -8px;
        left: -8px;
        right: -8px;
        bottom: -8px;
        border: 2px dashed rgba(0, 64, 128, 0.3);
        border-radius: 50%;
        animation: rotate 15s linear infinite;
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .principal-img {
        width: 180px;
        height: 180px;
        border: 5px solid white;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }
    
    .principal-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .principal-name {
        font-size: 1.6rem;
        font-weight: 700;
        color: #004080;
    }
    
    .principal-title-line {
        width: 40px;
        height: 3px;
        background: linear-gradient(to right, #004080, #4e92df);
        border-radius: 2px;
    }
    
    .principal-title {
        color: #4e92df;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .principal-quote {
        color: #556575;
        font-size: 1rem;
        line-height: 1.6;
        border-left: 3px solid rgba(0, 64, 128, 0.2);
        padding-left: 1rem;
    }
    
    .credential-badge {
        display: inline-block;
        background: linear-gradient(135deg, rgba(0, 64, 128, 0.08), rgba(78, 146, 223, 0.08));
        color: #004080;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 0.4rem 0.8rem;
        border-radius: 30px;
        border: 1px solid rgba(0, 64, 128, 0.12);
        transition: all 0.3s ease;
    }
    
    .credential-badge:hover {
        background: linear-gradient(135deg, rgba(0, 64, 128, 0.12), rgba(78, 146, 223, 0.12));
        transform: translateY(-2px);
    }
    
    /* Teachers Grid Styling */
    .teachers-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1.8rem;
        margin-bottom: 2.5rem;
    }
    
    .teacher-card {
        width: 220px;
        flex-grow: 1;
        max-width: 260px;
        background-color: white;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.35s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
        z-index: 1;
    }
    
    .teacher-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 64, 128, 0.05), rgba(78, 146, 223, 0.05));
        z-index: -1;
        opacity: 0;
        transition: opacity 0.35s ease;
    }
    
    .teacher-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
    }
    
    .teacher-card:hover::before {
        opacity: 1;
    }
    
    .teacher-img-wrapper {
        padding: 1.5rem 1.5rem 0.75rem;
        display: flex;
        justify-content: center;
    }
    
    .teacher-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        background-color: #f5f5f5;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.35s ease;
        border: 4px solid white;
    }
    
    .teacher-card:hover .teacher-img {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }
    
    .teacher-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .teacher-card:hover .teacher-img img {
        transform: scale(1.1);
    }
    
    .teacher-info {
        padding: 0.75rem 1.5rem 1.5rem;
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .teacher-name {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2c3e50;
        transition: color 0.3s ease;
    }
    
    .teacher-card:hover .teacher-name {
        color: #004080;
    }
    
    .teacher-badge {
        font-size: 0.8rem;
        background: linear-gradient(135deg, rgba(0, 64, 128, 0.08), rgba(78, 146, 223, 0.08));
        color: #004080;
        padding: 0.35rem 0.85rem;
        border-radius: 30px;
        display: inline-block;
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 64, 128, 0.05);
    }
    
    .teacher-card:hover .teacher-badge {
        background: linear-gradient(135deg, rgba(0, 64, 128, 0.12), rgba(78, 146, 223, 0.12));
        padding: 0.35rem 1rem;
    }
    
    /* View All Button */
    .btn-view-all {
        display: inline-flex;
        align-items: center;
        padding: 0.8rem 1.8rem;
        background: transparent;
        color: #004080;
        font-weight: 600;
        border: 2px solid rgba(0, 64, 128, 0.2);
        border-radius: 50px;
        transition: all 0.35s ease;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }
    
    .btn-view-all::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 0%;
        height: 100%;
        background: linear-gradient(to right, #004080, #4e92df);
        transition: all 0.35s ease;
        z-index: -1;
    }
    
    .btn-view-all:hover {
        color: white;
        border-color: #004080;
        box-shadow: 0 5px 15px rgba(0, 64, 128, 0.2);
    }
    
    .btn-view-all:hover::before {
        width: 100%;
    }
    
    .btn-view-all i {
        transition: transform 0.35s ease;
    }
    
    .btn-view-all:hover i {
        transform: translateX(3px);
    }
    
    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .principal-img {
            width: 160px;
            height: 160px;
        }
        
        .principal-name {
            font-size: 1.4rem;
        }
        
        .teachers-grid {
            gap: 1.5rem;
        }
        
        .teacher-card {
            width: 190px;
            max-width: 220px;
        }

        .teacher-img {
            width: 110px;
            height: 110px;
        }
    }
    
    @media (max-width: 768px) {
        .principal-img {
            width: 140px;
            height: 140px;
        }
        
        .principal-img-border {
            top: -6px;
            left: -6px;
            right: -6px;
            bottom: -6px;
        }
        
        .principal-name {
            font-size: 1.3rem;
        }
        
        .department-heading::before,
        .department-heading::after {
            width: 40px;
        }
        
        .department-heading::before {
            left: calc(50% - 80px);
        }
        
        .department-heading::after {
            right: calc(50% - 80px);
        }
        
        .teachers-grid {
            gap: 1.2rem;
        }
        
        .teacher-card {
            width: 160px;
            max-width: 190px;
        }

        .teacher-img {
            width: 100px;
            height: 100px;
        }
        
        .teacher-img-wrapper {
            padding: 1.2rem 1.2rem 0.6rem;
        }
        
        .teacher-info {
            padding: 0.6rem 1.2rem 1.2rem;
        }
        
        .teacher-name {
            font-size: 1rem;
        }
        
        .teacher-badge {
            font-size: 0.75rem;
            padding: 0.3rem 0.7rem;
        }
    }
    
    @media (max-width: 576px) {
        .principal-img {
            width: 120px;
            height: 120px;
            border-width: 4px;
        }
        
        .principal-card {
            padding: 1.25rem !important;
        }
        
        .teachers-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .teacher-card {
            border-radius: 10px;
        }
        
        .teacher-img {
            width: 90px;
            height: 90px;
            border-width: 3px;
        }
        
        .teacher-img-wrapper {
            padding: 1rem 1rem 0.5rem;
        }
        
        .teacher-info {
            padding: 0.5rem 1rem 1rem;
        }
        
        .teacher-name {
            font-size: 0.9rem;
            margin-bottom: 0.35rem;
        }
        
        .teacher-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.6rem;
        }
        
        .btn-view-all {
            padding: 0.7rem 1.5rem;
            font-size: 0.9rem;
        }
    }
</style>
