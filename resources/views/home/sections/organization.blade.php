<section class="organization-section py-5 bg-light">
    <div class="container-fluid">
      <div class="section-title text-center mb-5">
        <h2>Struktur Organisasi</h2>
        <p>Struktur Kepemimpinan Sekolah SMKN 5 Padang</p>
      </div>
  
      <div class="org-chart-container">
        <div class="org-chart">
  
          <!-- Level 1: Kepsek -->
          <div class="org-level level">
            <div class="org-box org-box-primary">
              <div class="org-content">
                <h5>Kepala Sekolah</h5>
                <p>{{ $kepsek->user->nama ?? 'Rizka Fauzi Yosfi, S.Pd, S.T, M.Kom' }}</p>
              </div>
            </div>
          </div>
  
          <!-- Level 2: Koordinator & Kepala Bidang -->
          @if($koordinators->count() > 0 || $kepala_bidangs->count() > 0)
          <div class="org-level level">
            @foreach($koordinators as $koor)
            <div class="org-box org-box-secondary">
              <div class="org-content">
                <h5>Koordinator {{ $koor->spesialisasi ? $koor->spesialisasi : '' }}</h5>
                <p>{{ $koor->user->nama ?? '-' }}</p>
              </div>
            </div>
            @endforeach
            @foreach($kepala_bidangs as $kabid)
            <div class="org-box org-box-secondary">
              <div class="org-content">
                <h5>Kepala Bidang {{ $kabid->spesialisasi ? $kabid->spesialisasi : '' }}</h5>
                <p>{{ $kabid->user->nama ?? '-' }}</p>
              </div>
            </div>
            @endforeach
          </div>
          @endif
  
          <!-- Level 3: Wakil Kepala -->
          @if($wakas->count() > 0)
          <div class="org-level level">
            @foreach($wakas as $waka)
            <div class="org-box">
              <h5>Wakil Kepala {{ $waka->spesialisasi ? $waka->spesialisasi : '' }}</h5>
              <p>{{ $waka->user->nama ?? '-' }}</p>
            </div>
            @endforeach
          </div>
          @endif
  
          <!-- Level 4: Bendahara -->
          @if($bendaharas->count() > 0)
          <div class="org-level level">
            @foreach($bendaharas as $bend)
            <div class="org-box">
              <h5>Bendahara {{ $bend->spesialisasi ? $bend->spesialisasi : '' }}</h5>
              <p>{{ $bend->user->nama ?? '-' }}</p>
            </div>
            @endforeach
          </div>
          @endif

          <!-- Level 5: Kepala Jurusan -->
          @if($kajurs->count() > 0)
          <div class="org-level level">
            @foreach($kajurs as $kajur)
            <div class="org-box org-box-accent">
              <h5>Kepala Jurusan {{ $kajur->spesialisasi ?: $kajur->jurusan }}</h5>
              <p>{{ $kajur->user->nama ?? '-' }}</p>
            </div>
            @endforeach
          </div>
          @endif

          <!-- Level 6: Kepala Bengkel -->
          @if($kabengs->count() > 0)
          <div class="org-level level">
            @foreach($kabengs as $kabeng)
            <div class="org-box">
              <h5>Kepala Bengkel {{ $kabeng->spesialisasi ? $kabeng->spesialisasi : '' }}</h5>
              <p>{{ $kabeng->user->nama ?? '-' }}</p>
            </div>
            @endforeach
          </div>
          @endif
  
        </div>
      </div>
    </div>
  </section>
    
    <style>
      .org-chart-container {
        overflow-x: auto;
        padding: 20px;
      }
    
      .org-chart {
        display: flex;
        flex-direction: column;
        gap: 30px;
        align-items: center;
        width: 100%;
      }
    
      .org-level.level {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
      }
    
      .org-box {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 15px;
        text-align: center;
        width: 200px;
        border-top: 4px solid var(--primary, #007BFF);
        transition: 0.3s;
      }
    
      .org-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      }
    
      .org-box-primary {
        border-color: #004080;
        background-color: #e6f0ff;
      }
    
      .org-box-secondary {
        border-color: #FF6B35;
        background-color: #fff2ec;
      }
    
      .org-box-accent {
        border-color: #2ecc71;
        background-color: #ecf9f0;
      }
    
      .org-content h5 {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 5px;
      }
    
      .org-content p {
        font-size: 0.85rem;
        margin: 0;
      }
    </style>