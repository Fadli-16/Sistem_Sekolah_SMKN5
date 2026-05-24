@extends('siswa.layouts.main')

@section('css')
<style>
    .inventaris-card {
        border-radius: var(--radius);
        background-color: #fff;
        box-shadow: var(--shadow);
        transition: transform 0.3s ease;
        height: 100%;
    }
    
    .inventaris-card:hover {
        transform: translateY(-5px);
    }
    
    .inventaris-image {
        height: 200px;
        object-fit: cover;
        width: 100%;
        border-top-left-radius: var(--radius);
        border-top-right-radius: var(--radius);
    }
    
    .inventaris-content {
        padding: 1.5rem;
    }
    
    .inventaris-category {
        display: inline-block;
        font-size: 0.8rem;
        background: var(--secondary-light);
        color: var(--secondary);
        padding: 0.35rem 0.65rem;
        border-radius: var(--radius-sm);
        margin-bottom: 0.75rem;
    }
    
    .inventaris-title {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--primary);
    }
    
    .inventaris-status {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        border-radius: var(--radius-sm);
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .status-available {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    
    .status-unavailable {
        background-color: #f8d7da;
        color: #842029;
    }
    
    .inventaris-detail {
        margin-top: 1rem;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .detail-item {
        display: flex;
        align-items: center;
    }
    
    .detail-icon {
        width: 32px;
        height: 32px;
        background-color: var(--secondary-light);
        color: var(--secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 0.75rem;
    }
    
    .detail-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0;
    }
    
    .detail-value {
        font-size: 0.95rem;
        font-weight: 500;
        margin-top: 0;
        margin-bottom: 0;
    }
    
    .empty-icon {
        font-size: 3rem;
        color: var(--text-muted);
        margin-bottom: 1rem;
    }
    
    .filter-section {
        background-color: #f8f9fa;
        border-radius: var(--radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .view-switch {
        display: flex;
        gap: 0.5rem;
    }
    
    .view-btn {
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius);
        background-color: #fff;
        border: 1px solid rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: var(--transition);
    }
    
    .view-btn.active {
        background-color: var(--secondary);
        color: #fff;
        border-color: var(--secondary);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Inventaris Laboratorium</h1>
            <p class="text-muted">Daftar peralatan dan perlengkapan yang tersedia di laboratorium</p>
        </div>
    </div>
    
    <div class="filter-section">
        <div class="row align-items-end">
            <div class="col-md-3 mb-3 mb-md-0">
                <label for="filterKategori" class="form-label">Kategori</label>
                <select class="form-select" id="filterKategori">
                    <option value="">Semua Kategori</option>
                    @php
                        $categories = \App\Models\Inventaris::select('kategori')->distinct()->pluck('kategori');
                    @endphp
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <label for="filterLokasi" class="form-label">Lokasi</label>
                <select class="form-select" id="filterLokasi">
                    <option value="">Semua Lokasi</option>
                    @php
                        $locations = \App\Models\Inventaris::select('lokasi')->distinct()->pluck('lokasi');
                    @endphp
                    @foreach($locations as $location)
                        <option value="{{ $location }}">{{ $location }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-3 mb-md-0">
                <label for="filterStatus" class="form-label">Status</label>
                <select class="form-select" id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Tidak Tersedia">Tidak Tersedia</option>
                </select>
            </div>
            <div class="col-md-3 d-flex justify-content-md-end align-items-center">
                <div class="view-switch">
                    <button type="button" class="view-btn active" id="gridView" data-bs-toggle="tooltip" title="Grid View">
                        <i class="bi bi-grid"></i>
                    </button>
                    <button type="button" class="view-btn" id="listView" data-bs-toggle="tooltip" title="List View">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grid View -->
    <div class="row g-4" id="gridViewContainer">
        @forelse($inventaris ?? [] as $item)
        <div class="col-lg-4 col-md-6 inventaris-item" 
             data-kategori="{{ $item->kategori }}" 
             data-lokasi="{{ $item->lokasi }}" 
             data-status="{{ $item->status }}">
            <div class="inventaris-card">
                @if($item->gambar)
                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama_inventaris }}" class="inventaris-image">
                @else
                    <div class="inventaris-image d-flex align-items-center justify-content-center bg-light">
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    </div>
                @endif
                
                <div class="inventaris-content">
                    <span class="inventaris-category">{{ $item->kategori }}</span>
                    <h3 class="inventaris-title">{{ $item->nama_inventaris }}</h3>
                    
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="inventaris-status {{ $item->status == 'Tersedia' ? 'status-available' : 'status-unavailable' }}">
                            {{ $item->status }}
                        </span>
                        <span class="text-muted">{{ $item->jumlah }} Unit</span>
                    </div>
                    
                    <div class="inventaris-detail">
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <p class="detail-label">Lokasi</p>
                                <p class="detail-value">{{ $item->lokasi }}</p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div>
                                <p class="detail-label">Kondisi</p>
                                <p class="detail-value">{{ $item->kondisi ?? 'Baik' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('siswa.inventaris.show', $item->id) }}" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-box-seam empty-icon"></i>
            <h5>Belum Ada Inventaris</h5>
            <p class="text-muted">Belum ada data inventaris yang tersedia</p>
        </div>
        @endforelse
    </div>
    
    <!-- List View -->
    <div class="card shadow-sm d-none" id="listViewContainer">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="inventarisTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Kondisi</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventaris ?? [] as $item)
                        <tr class="inventaris-item" 
                            data-kategori="{{ $item->kategori }}" 
                            data-lokasi="{{ $item->lokasi }}" 
                            data-status="{{ $item->status }}">
                            <td>{{ $item->nama_inventaris }}</td>
                            <td>{{ $item->kategori }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>{{ $item->kondisi ?? 'Baik' }}</td>
                            <td>{{ $item->lokasi }}</td>
                            <td>
                                <span class="badge {{ $item->status == 'Tersedia' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('siswa.inventaris.show', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="bi bi-box-seam empty-icon d-block"></i>
                                <h5>Belum Ada Inventaris</h5>
                                <p class="text-muted">Belum ada data inventaris yang tersedia</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Initialize DataTable for list view
const table = $('#inventarisTable').DataTable({
    responsive: true,
    language: {
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Tidak ada data yang ditampilkan",
        infoFiltered: "(difilter dari _MAX_ total data)",
        paginate: {
            first: "Pertama",
            last: "Terakhir",
            next: "Selanjutnya",
            previous: "Sebelumnya"
        },
    },
    // Add this to fix the column sorting issue
    columnDefs: [
        { targets: 0, data: 'nama_inventaris', name: 'nama_inventaris' },
        { targets: 1, data: 'kategori', name: 'kategori' },
        { targets: 2, data: 'jumlah', name: 'jumlah' },
        { targets: 3, data: 'kondisi', name: 'kondisi' },
        { targets: 4, data: 'lokasi', name: 'lokasi' },
        { targets: 5, data: 'status', name: 'status' },
        { targets: 6, sortable: false, searchable: false }
    ]
});
        
        // View switching
        $('#gridView').click(function() {
            $('#gridViewContainer').removeClass('d-none');
            $('#listViewContainer').addClass('d-none');
            $(this).addClass('active');
            $('#listView').removeClass('active');
        });
        
        $('#listView').click(function() {
            $('#listViewContainer').removeClass('d-none');
            $('#gridViewContainer').addClass('d-none');
            $(this).addClass('active');
            $('#gridView').removeClass('active');
            // Adjust DataTable columns
            table.columns.adjust().responsive.recalc();
        });
        
        // Filter functionality
        function applyFilters() {
            const kategori = $('#filterKategori').val();
            const lokasi = $('#filterLokasi').val();
            const status = $('#filterStatus').val();
            
            $('.inventaris-item').each(function() {
                let show = true;
                
                if (kategori && $(this).data('kategori') !== kategori) show = false;
                if (lokasi && $(this).data('lokasi') !== lokasi) show = false;
                if (status && $(this).data('status') !== status) show = false;
                
                $(this).toggle(show);
            });
            
            // If in list view, update DataTable
            if (!$('#listViewContainer').hasClass('d-none')) {
                table.draw();
            }
        }
        
        $('#filterKategori, #filterLokasi, #filterStatus').change(applyFilters);
        
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endsection