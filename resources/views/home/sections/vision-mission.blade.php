<section class="vision-mission-section py-4 bg-light">
    <div class="container">
        <div class="section-title text-center mb-4">
            <h2>Visi & Misi</h2>
            <p>Landasan dan arahan yang menjadi pedoman kami dalam menjalankan pendidikan</p>
        </div>
        
        <div class="vm-container">
            <div class="vm-card">
                <div class="vm-header" data-bs-toggle="collapse" data-bs-target="#visionContent" aria-expanded="true">
                    <div class="vm-icon">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <h3>Visi</h3>
                    <i class="bi bi-chevron-down toggle-icon"></i>
                </div>
                <div id="visionContent" class="vm-content collapse show">
                    <p class="vision-text">"Menjadi lembaga pendidikan kejuruan unggul yang menghasilkan lulusan berkarakter, berdaya saing global, berbudaya lingkungan, dan mampu berkontribusi dalam pembangunan masyarakat."</p>
                </div>
            </div>
            
            <div class="vm-card mt-3">
                <div class="vm-header" data-bs-toggle="collapse" data-bs-target="#missionContent" aria-expanded="false">
                    <div class="vm-icon">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h3>Misi</h3>
                    <i class="bi bi-chevron-down toggle-icon"></i>
                </div>
                <div id="missionContent" class="vm-content collapse">
                    <div class="mission-grid">
                        <div class="mission-item">
                            <span class="mission-number">1</span>
                            <p>Menyelenggarakan pendidikan kejuruan berkualitas sesuai tuntutan industri dan usaha</p>
                        </div>
                        <div class="mission-item">
                            <span class="mission-number">2</span>
                            <p>Membekali peserta didik dengan kompetensi dan sertifikasi sesuai program keahlian</p>
                        </div>
                        <div class="mission-item">
                            <span class="mission-number">3</span>
                            <p>Mengembangkan karakter peserta didik yang religius, jujur, disiplin, dan bertanggung jawab</p>
                        </div>
                        <div class="mission-item">
                            <span class="mission-number">4</span>
                            <p>Menerapkan sistem manajemen mutu untuk penjaminan dan peningkatan kualitas pendidikan</p>
                        </div>
                        <div class="mission-item">
                            <span class="mission-number">5</span>
                            <p>Meningkatkan kualitas SDM melalui pengembangan profesionalisme pendidik</p>
                        </div>
                        <div class="mission-item">
                            <span class="mission-number">6</span>
                            <p>Mewujudkan lingkungan sekolah yang bersih, sehat, dan ramah lingkungan</p>
                        </div>
                        <div class="mission-item">
                            <span class="mission-number">7</span>
                            <p>Memperluas kerja sama dengan dunia usaha, industri, dan lembaga pendidikan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <button class="btn btn-sm btn-outline-primary" id="expandAllBtn">
                <i class="bi bi-eye-fill me-1"></i> Lihat Semua
            </button>
        </div>
    </div>
</section>

<style>
    .vision-mission-section {
        background-color: var(--bg-light);
    }
    
    .vm-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .vm-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .vm-card:hover {
        box-shadow: var(--shadow);
    }
    
    .vm-header {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        cursor: pointer;
        background-color: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .vm-header h3 {
        margin: 0;
        font-size: 1.25rem;
        flex-grow: 1;
        margin-left: 15px;
    }
    
    .vm-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    
    .vm-icon i {
        font-size: 1.2rem;
    }
    
    .vm-card:first-child .vm-icon {
        background-color: rgba(0, 64, 128, 0.1);
        color: var(--primary);
    }
    
    .vm-card:last-child .vm-icon {
        background-color: rgba(255, 107, 53, 0.1);
        color: var(--secondary);
    }
    
    .toggle-icon {
        transition: transform 0.3s ease;
    }
    
    .vm-header[aria-expanded="true"] .toggle-icon {
        transform: rotate(180deg);
    }
    
    .vm-content {
        padding: 20px;
    }
    
    .vision-text {
        font-weight: 500;
        line-height: 1.6;
        margin-bottom: 0;
        text-align: center;
    }
    
    .mission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
    }
    
    .mission-item {
        display: flex;
        align-items: flex-start;
        background-color: rgba(255, 255, 255, 0.6);
        padding: 12px;
        border-radius: 8px;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .mission-number {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        background-color: var(--secondary);
        color: white;
        border-radius: 50%;
        font-weight: bold;
        font-size: 0.8rem;
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .mission-item p {
        margin: 0;
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    @media (max-width: 768px) {
        .mission-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const expandAllBtn = document.getElementById('expandAllBtn');
        const visionContent = document.getElementById('visionContent');
        const missionContent = document.getElementById('missionContent');
        
        expandAllBtn.addEventListener('click', function() {
            if (missionContent.classList.contains('show')) {
                visionContent.classList.add('show');
                missionContent.classList.remove('show');
                expandAllBtn.innerHTML = '<i class="bi bi-eye-fill me-1"></i> Lihat Semua';
            } else {
                visionContent.classList.add('show');
                missionContent.classList.add('show');
                expandAllBtn.innerHTML = '<i class="bi bi-eye-slash-fill me-1"></i> Sembunyikan';
            }
        });
    });
</script>