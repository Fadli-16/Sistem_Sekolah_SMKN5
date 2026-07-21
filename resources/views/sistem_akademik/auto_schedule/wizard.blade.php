@extends('sistem_akademik.layouts.main')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Auto-Scheduling Jadwal Pelajaran (AI Powered)</h5>
            <span class="badge bg-light text-primary"><i class="fas fa-robot"></i> Model: {{ $aiModelName ?? 'Gemini Pro' }}</span>
        </div>
        <div class="card-body">
            
            <!-- Step 1: Pilih Jurusan & Kelas -->
            <datalist id="existing-rooms">
                @foreach($existingRooms ?? [] as $r)
                    <option value="{{ $r }}"></option>
                @endforeach
            </datalist>

            <div id="step-1" class="wizard-step">
                <h6 class="mb-3">Tahap 1a: Pilih Jurusan & Kelas</h6>
                <div class="mb-3">
                    <label>Pilih Jurusan</label>
                    <select id="jurusan-select" class="form-select">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 d-none" id="kelas-container">
                    <label>Pilih Kelas (Bisa lebih dari 1)</label>
                    <div id="kelas-checkboxes" class="row ms-1 mt-2">
                        <!-- Checkboxes dipopulate via JS -->
                    </div>
                </div>
                <button id="btn-next-1" class="btn btn-primary" disabled>Lanjut Pilih Mapel</button>
            </div>

            <!-- Step 1b: Pilih Mapel -->
            <div id="step-1b" class="wizard-step d-none">
                <h6 class="mb-3">Tahap 1b: Pilih Mata Pelajaran</h6>
                <div class="mb-3">
                    <p class="text-muted">Pilih mata pelajaran yang ingin di-generate jadwalnya untuk kelas-kelas yang telah dipilih.</p>
                    <div class="form-check mb-3 border-bottom pb-2">
                        <input class="form-check-input" type="checkbox" id="check-all-mapel" checked>
                        <label class="form-check-label fw-bold" for="check-all-mapel">Pilih Semua Mapel</label>
                    </div>
                    <div class="ms-1">
                        <div class="mb-4">
                            <h6 class="text-primary mb-3 border-bottom pb-2"><i class="fas fa-book"></i> Kelompok Mapel Umum</h6>
                            <div class="row" id="mapel-umum-container"></div>
                        </div>
                        <div class="mb-2">
                            <h6 class="text-info mb-3 border-bottom pb-2"><i class="fas fa-laptop-code"></i> Kelompok Mapel Jurusan</h6>
                            <div class="row" id="mapel-jurusan-container"></div>
                        </div>
                    </div>
                </div>
                <button id="btn-back-1b" class="btn btn-secondary me-2">Kembali</button>
                <button id="btn-next-1b" class="btn btn-primary">Lanjut Pengaturan Ruangan</button>
            </div>

            <!-- Step 1c: Pengaturan Ruangan & Guru -->
            <div id="step-1c" class="wizard-step d-none">
                <h6 class="mb-3">Tahap 1c: Pengaturan Ruangan & Guru</h6>
                
                <div class="mb-4">
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-door-open"></i> 1. Pengaturan Ruangan Khusus</h6>
                    <p class="text-muted small">Tentukan ruangan khusus untuk mata pelajaran yang membutuhkannya (misal: Lapangan, Lab Komputer). Kosongkan jika ingin menggunakan ruangan kelas reguler.</p>
                    <div id="ruangan-override-container" class="row ms-1"></div>
                </div>

                <div class="mb-4 d-none" id="distribusi-guru-section">
                    <h6 class="text-primary border-bottom pb-2"><i class="fas fa-chalkboard-teacher"></i> 2. Distribusi Guru (Mapel Paralel)</h6>
                    <p class="text-muted small">Anda memilih lebih dari satu guru untuk mata pelajaran yang sama. Tentukan guru mana yang akan mengajar di masing-masing kelas berikut:</p>
                    <div id="distribusi-guru-container" class="ms-1"></div>
                </div>

                <button id="btn-back-1c" class="btn btn-secondary me-2">Kembali</button>
                <button id="btn-next-1c" class="btn btn-primary">Lanjut ke Validasi</button>
            </div>

            <!-- Step 2: Validasi Data & Opsi -->
            <div id="step-2" class="wizard-step d-none">
                <h6 class="mb-3">Tahap 2: Validasi Data</h6>
                <div id="validation-loading" class="alert alert-info">Memeriksa kelengkapan data (Guru, Ruangan, JP)...</div>
                <div id="validation-result" class="d-none"></div>

                <div id="options-container" class="mt-4 d-none">
                    <h6 class="mb-3">Opsi Optimasi (Soft Constraints)</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="opt-umum" checked>
                        <label class="form-check-label" for="opt-umum">
                            Prioritaskan Mapel Umum di Awal Minggu (Senin/Selasa)
                        </label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="opt-jurusan" checked>
                        <label class="form-check-label" for="opt-jurusan">
                            Prioritaskan Mapel Jurusan di Tengah-Akhir Minggu
                        </label>
                    </div>
                    
                    <div class="mt-4">
                        <button id="btn-back-2" class="btn btn-secondary me-2">Kembali</button>
                        <button id="btn-generate" class="btn btn-success">Mulai Generate Jadwal</button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Proses Generation -->
            <div id="step-3" class="wizard-step d-none text-center py-5">
                <h5 class="mb-4">Sistem sedang memproses algoritma dan menyusun jadwal...</h5>
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Mohon tunggu, proses ini memakan waktu beberapa saat tergantung jumlah kelas.</p>
                <div id="generate-status" class="mt-2 fw-bold text-info">Status: Pending...</div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let selectedJurusan = '';
    
    // --- State Management ---
    function saveState() {
        if ($('#step-3').is(':visible')) return; // Jangan simpan state loading
        let state = {
            jurusan: selectedJurusan,
            kelasIds: $('.kelas-check:checked').map(function() { return $(this).val(); }).get(),
            mapelIds: $('.mapel-check:checked').map(function() { return $(this).val(); }).get(),
            mapelRooms: {},
            step: $('.wizard-step:not(.d-none)').attr('id')
        };
        $('.mapel-room-input').each(function() {
            if ($(this).val().trim() !== '') {
                state.mapelRooms[$(this).data('id')] = $(this).val();
            }
        });
        sessionStorage.setItem('wizardState', JSON.stringify(state));
    }

    $(window).on('beforeunload', function() {
        saveState();
    });

    function restoreState() {
        let stateStr = sessionStorage.getItem('wizardState');
        if (!stateStr) return;
        let state = JSON.parse(stateStr);
        if (!state.jurusan) return;

        selectedJurusan = state.jurusan;
        $('#jurusan-select').val(state.jurusan);

        $.get("{{ route('sistem_akademik.auto-schedule.get-classes') }}", { jurusan: selectedJurusan }, function(data) {
            let html = '';
            window.kelasMap = {};
            data.forEach(function(k) {
                window.kelasMap[k.id] = k;
                let checked = state.kelasIds.includes(k.id.toString()) ? 'checked' : '';
                html += `
                    <div class="col-md-3 mb-2">
                        <div class="form-check">
                            <input class="form-check-input kelas-check" type="checkbox" value="${k.id}" id="k_${k.id}" ${checked}>
                            <label class="form-check-label" for="k_${k.id}">${k.nama_kelas}</label>
                        </div>
                    </div>
                `;
            });
            $('#kelas-checkboxes').html(html);
            $('#kelas-container').removeClass('d-none');
            $('#btn-next-1').prop('disabled', false);

            if (state.step === 'step-1') return;

            $.get("{{ route('sistem_akademik.auto-schedule.get-mapel') }}", { jurusan: selectedJurusan }, function(data) {
                data.sort((a, b) => {
                    let c = a.nama_mata_pelajaran.localeCompare(b.nama_mata_pelajaran);
                    if (c !== 0) return c;
                    let gA = a.guru ? a.guru.nama : '';
                    let gB = b.guru ? b.guru.nama : '';
                    return gA.localeCompare(gB);
                });
                window.guruMap = {};
                let htmlUmum = '';
                let htmlJurusan = '';

                data.forEach(function(m) {
                    let kategori = m.kategori_penjadwalan || ((m.jurusan && m.jurusan.toLowerCase() === 'umum') ? 'Umum' : 'Jurusan');
                    if (m.guru_id) window.guruMap[m.guru_id] = { name: m.guru.nama, existingJp: m.guru_existing_jp };
                    
                    let isChecked = state.mapelIds.includes(m.id.toString()) ? 'checked' : '';
                    let cardHtml = `
                        <div class="COL_CLASS mb-2">
                            <div class="mapel-container rounded p-2 border bg-white" id="container_m_${m.id}" data-name="${m.nama_mata_pelajaran}">
                                <div class="form-check">
                                    <input class="form-check-input mapel-check" type="checkbox" value="${m.id}" id="m_${m.id}" data-name="${m.nama_mata_pelajaran}" data-jp="${m.jp}" data-guru-id="${m.guru_id || ''}" ${isChecked}>
                                    <label class="form-check-label" for="m_${m.id}">
                                        <strong>${m.nama_mata_pelajaran}</strong> 
                                        <span class="badge bg-secondary ms-1">${m.jp} JP</span>
                                        <br><small class="text-muted guru-info" id="guru_info_${m.guru_id}">Guru: ${m.guru ? m.guru.nama : 'Belum diatur'} (Beban: ${m.guru_existing_jp} JP)</small>
                                        ${kategori.toLowerCase() !== 'umum' && m.jp > 6 ? '<br><small class="text-warning">(Akan dipecah 2 hari)</small>' : ''}
                                    </label>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    if (kategori.toLowerCase() === 'umum') {
                        htmlUmum += cardHtml.replace('COL_CLASS', 'col-md-4');
                    } else {
                        htmlJurusan += cardHtml.replace('COL_CLASS', 'col-md-4');
                    }
                });

                if(htmlUmum === '') htmlUmum = '<p class="text-muted small">Tidak ada mapel umum.</p>';
                if(htmlJurusan === '') htmlJurusan = '<p class="text-muted small">Tidak ada mapel jurusan.</p>';
                $('#mapel-umum-container').html(htmlUmum);
                $('#mapel-jurusan-container').html(htmlJurusan);
                updateGuruUI();

                updateGuruUI();

                // Check disabled statuses
                updateGuruUI();

                $('#step-1').addClass('d-none');
                $('#step-1b').removeClass('d-none');

                if (state.step === 'step-1c' || state.step === 'step-2') {
                    // Restore step 1c
                    let htmlRooms = '';
                    let defRooms = [];
                    $('.kelas-check:checked').each(function() {
                        let k = window.kelasMap[$(this).val()];
                        if (k && k.ruangan) defRooms.push(k.ruangan);
                    });
                    let defRoomStr = defRooms.length > 0 ? defRooms.join(', ') : 'Kelas Reguler';

                    $('.mapel-check:checked').each(function() {
                        let mId = $(this).val();
                        let mName = $(this).data('name');
                        let savedVal = state.mapelRooms[mId] || '';
                        htmlRooms += `
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold">${mName}</label>
                                <input type="text" class="form-control mapel-room-input" list="existing-rooms" data-id="${mId}" value="${savedVal}" placeholder="Ruangan Default: ${defRoomStr}">
                            </div>
                        `;
                    });
                    $('#ruangan-override-container').html(htmlRooms);
                    $('#step-1b').addClass('d-none');
                    $('#step-1c').removeClass('d-none');

                    if (state.step === 'step-2') {
                        $('#btn-next-1c').click(); // Auto trigger validation
                    }
                }
            });
        });
    }

    restoreState();
    // --- End State Management ---

    // Pilih Jurusan
    $('#jurusan-select').change(function() {
        selectedJurusan = $(this).val();
        if(selectedJurusan) {
            // Fetch Kelas
            $.get("{{ route('sistem_akademik.auto-schedule.get-classes') }}", { jurusan: selectedJurusan }, function(data) {
                let html = '';
                window.kelasMap = {};
                data.forEach(function(k) {
                    window.kelasMap[k.id] = k;
                    html += `
                        <div class="col-md-3 mb-2">
                            <div class="form-check">
                                <input class="form-check-input kelas-check" type="checkbox" value="${k.id}" id="k_${k.id}" checked>
                                <label class="form-check-label" for="k_${k.id}">${k.nama_kelas}</label>
                            </div>
                        </div>
                    `;
                });
                $('#kelas-checkboxes').html(html);
                $('#kelas-container').removeClass('d-none');
                $('#btn-next-1').prop('disabled', false);
            });
        } else {
            $('#kelas-container').addClass('d-none');
            $('#btn-next-1').prop('disabled', true);
        }
    });

    // Lanjut ke Step 1b (Pilih Mapel)
    $('#btn-next-1').click(function() {
        let kelasIds = $('.kelas-check:checked').map(function() { return $(this).val(); }).get();
        if(kelasIds.length === 0) {
            Swal.fire('Error', 'Pilih minimal 1 kelas!', 'error'); return;
        }

        let missingRooms = [];
        $('.kelas-check:checked').each(function() {
            let kId = $(this).val();
            let k = window.kelasMap[kId];
            if (!k.ruangan || k.ruangan.trim() === '') {
                missingRooms.push(k.nama_kelas);
            }
        });
        
        if (missingRooms.length > 0) {
            Swal.fire('Peringatan', `Kelas berikut belum memiliki ruangan reguler yang diatur: ${missingRooms.join(', ')}. Silakan atur terlebih dahulu di menu Master Data Kelas sebelum mengatur jadwal.`, 'warning');
            return;
        }

        $('#step-1').addClass('d-none');
        $('#step-1b').removeClass('d-none');

        $.get("{{ route('sistem_akademik.auto-schedule.get-mapel') }}", { jurusan: selectedJurusan }, function(data) {
            // Sort data alphabetically by Mapel Name, then Guru Name
            data.sort(function(a, b) {
                let c = a.nama_mata_pelajaran.localeCompare(b.nama_mata_pelajaran);
                if (c !== 0) return c;
                let gA = a.guru ? a.guru.nama : '';
                let gB = b.guru ? b.guru.nama : '';
                return gA.localeCompare(gB);
            });

            window.guruMap = {}; // Untuk menyimpan data guru

            let htmlUmum = '';
            let htmlJurusan = '';

            data.forEach(function(m) {
                let kategori = m.kategori_penjadwalan;
                if (!kategori) {
                    kategori = (m.jurusan && m.jurusan.toLowerCase() === 'umum') ? 'Umum' : 'Jurusan';
                }

                if (m.guru_id) {
                    window.guruMap[m.guru_id] = { name: m.guru.nama, existingJp: m.guru_existing_jp };
                }

                let badgeClass = 'bg-secondary';
                let cardHtml = `
                    <div class="COL_CLASS mb-2">
                        <div class="mapel-container rounded p-2 border bg-white" id="container_m_${m.id}" data-name="${m.nama_mata_pelajaran}">
                            <div class="form-check">
                                <input class="form-check-input mapel-check" type="checkbox" value="${m.id}" id="m_${m.id}" data-name="${m.nama_mata_pelajaran}" data-jp="${m.jp}" data-guru-id="${m.guru_id || ''}">
                                <label class="form-check-label" for="m_${m.id}">
                                    <strong>${m.nama_mata_pelajaran}</strong> 
                                    <span class="badge ${badgeClass} ms-1">${m.jp} JP</span>
                                    <br><small class="text-muted guru-info" id="guru_info_${m.guru_id}">Guru: ${m.guru ? m.guru.nama : 'Belum diatur'} (Beban: ${m.guru_existing_jp} JP)</small>
                                    ${kategori.toLowerCase() !== 'umum' && m.jp > 6 ? '<br><small class="text-warning">(Akan dipecah 2 hari)</small>' : ''}
                                </label>
                            </div>
                        </div>
                    </div>
                `;

                if (kategori.toLowerCase() === 'umum') {
                    htmlUmum += cardHtml.replace('COL_CLASS', 'col-md-4');
                } else {
                    htmlJurusan += cardHtml.replace('COL_CLASS', 'col-md-4');
                }
            });

            if(htmlUmum === '') htmlUmum = '<p class="text-muted small">Tidak ada mapel umum.</p>';
            if(htmlJurusan === '') htmlJurusan = '<p class="text-muted small">Tidak ada mapel jurusan.</p>';

            $('#mapel-umum-container').html(htmlUmum);
            $('#mapel-jurusan-container').html(htmlJurusan);
        });
    });

    function getSelectedKelasCount() {
        return $('.kelas-check:checked').length;
    }

    function updateGuruUI() {
        let kelasCount = getSelectedKelasCount();
        
        // Reset
        for(let g in window.guruMap) {
            window.guruMap[g].projectedJp = window.guruMap[g].existingJp;
        }

        // Add
        $('.mapel-check:checked').each(function() {
            let guruId = $(this).data('guru-id');
            let jp = parseInt($(this).data('jp'));
            let name = $(this).data('name');
            let sameNameCount = $(`.mapel-check:checked[data-name="${name}"]`).length || 1;
            
            if(guruId && window.guruMap[guruId]) {
                // Approximate load: (kelasCount / sameNameCount)
                let approxClasses = Math.ceil(kelasCount / sameNameCount);
                window.guruMap[guruId].projectedJp += (jp * approxClasses);
            }
        });

        // UI
        $('.guru-info').each(function() {
            let guruId = $(this).attr('id').replace('guru_info_', '');
            if (guruId && window.guruMap[guruId]) {
                let total = window.guruMap[guruId].projectedJp;
                let text = `Guru: ${window.guruMap[guruId].name} (Beban: ${total} JP)`;
                let container = $(this).closest('.mapel-container');
                
                if (total > 40) {
                    text += ' <span class="text-danger fw-bold">[OVERLOAD > 40]</span>';
                    container.addClass('bg-danger text-white').removeClass('bg-warning');
                } else if (total >= 32) {
                    text += ' <span class="text-warning fw-bold">[WARNING >= 32]</span>';
                    container.addClass('bg-warning text-dark').removeClass('bg-danger');
                } else {
                    container.removeClass('bg-warning bg-danger text-white text-dark');
                }
                $(this).html(text);
            }
        });

        // Limit duplicate checkbox based on kelasCount
        let mapelNames = [];
        $('.mapel-check').each(function() {
            let n = $(this).data('name');
            if(!mapelNames.includes(n)) mapelNames.push(n);
        });

        mapelNames.forEach(n => {
            let checkedCount = $(`.mapel-check:checked[data-name="${n}"]`).length;
            if (checkedCount >= kelasCount) {
                $(`.mapel-check:not(:checked)[data-name="${n}"]`).prop('disabled', true).closest('.form-check').addClass('text-muted');
            } else {
                $(`.mapel-check:not(:checked)[data-name="${n}"]`).prop('disabled', false).closest('.form-check').removeClass('text-muted');
            }
        });
    }

    $(document).on('change', '.mapel-check', function(e) {
        let isChecked = $(this).is(':checked');
        let guruId = $(this).data('guru-id');
        let name = $(this).data('name');
        
        updateGuruUI();
        
        if (isChecked && guruId && window.guruMap[guruId] && window.guruMap[guruId].projectedJp > 40) {
            $(this).prop('checked', false);
            updateGuruUI();
            Swal.fire('Beban Kerja Berlebih!', 'Guru ini sudah mencapai batas maksimal (40 JP)!', 'error');
            return;
        }

        if (isChecked && guruId && window.guruMap[guruId] && window.guruMap[guruId].projectedJp >= 32) {
            Swal.fire({
                icon: 'warning',
                title: 'Beban Kerja Tinggi',
                text: `Guru ini mencapai ${window.guruMap[guruId].projectedJp} JP/minggu.`,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        }
    });

    $('#check-all-mapel').change(function() {
        let checkAll = $(this).is(':checked');
        
        if (checkAll) {
            $('.mapel-check:not(:disabled)').each(function() {
                if(!$(this).is(':checked')) {
                    $(this).prop('checked', true);
                    updateGuruUI();
                    
                    let guruId = $(this).data('guru-id');
                    if (guruId && window.guruMap[guruId] && window.guruMap[guruId].projectedJp > 40) {
                        $(this).prop('checked', false);
                        updateGuruUI();
                    }
                }
            });
        } else {
            $('.mapel-check:not(:disabled)').prop('checked', false);
            updateGuruUI();
        }
    });

    $('#btn-back-1b').click(function() {
        $('#step-1b').addClass('d-none');
        $('#step-1').removeClass('d-none');
    });

    // Lanjut ke Step 1c (Pengaturan Ruangan)
    $('#btn-next-1b').click(function() {
        let mapelIds = $('.mapel-check:checked').map(function() { return $(this).val(); }).get();
        if(mapelIds.length === 0) {
            Swal.fire('Error', 'Pilih minimal 1 mata pelajaran!', 'error'); return;
        }

        $('#step-1b').addClass('d-none');
        $('#step-1c').removeClass('d-none');

        let defRooms = [];
        $('.kelas-check:checked').each(function() {
            let k = window.kelasMap[$(this).val()];
            if (k && k.ruangan) defRooms.push(k.ruangan);
        });
        let defRoomStr = defRooms.length > 0 ? defRooms.join(', ') : 'Kelas Reguler';

        let htmlRooms = '';
        // Group unique mapel names so we don't show multiple room inputs for duplicate mapels
        let seenMapelNamesForRoom = [];
        $('.mapel-check:checked').each(function() {
            let mId = $(this).val();
            let mName = $(this).data('name');
            if (!seenMapelNamesForRoom.includes(mName)) {
                seenMapelNamesForRoom.push(mName);
                // For room override, we only need to map by mapel ID of the first one, 
                // but actually if they are duplicate mapel names, we should store the room override by NAME or apply it to ALL ids of that name.
                // We'll use a special class `mapel-room-input` and store `data-name` instead of `data-id` for easier processing.
                htmlRooms += `
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">${mName}</label>
                        <input type="text" class="form-control mapel-room-input" list="existing-rooms" data-name="${mName}" placeholder="Ruangan Default: ${defRoomStr}">
                    </div>
                `;
            }
        });
        $('#ruangan-override-container').html(htmlRooms);

        // Render Distribusi Guru
        let htmlDistribusi = '';
        let hasDistribusi = false;
        
        let mapelNames = [];
        let duplicateNames = [];
        $('.mapel-check:checked').each(function() {
            let n = $(this).data('name');
            if(!mapelNames.includes(n)) mapelNames.push(n);
            else if(!duplicateNames.includes(n)) duplicateNames.push(n);
        });

        duplicateNames.forEach(n => {
            hasDistribusi = true;
            let optionsHtml = '';
            let teachersForThisMapel = [];
            
            $(`.mapel-check:checked[data-name="${n}"]`).each(function() {
                let mId = $(this).val();
                let gId = $(this).data('guru-id');
                let gName = window.guruMap[gId] ? window.guruMap[gId].name : 'Belum Diatur';
                teachersForThisMapel.push({ id: mId, name: gName });
            });

            htmlDistribusi += `<div class="mb-3 p-3 bg-light rounded border border-light">
                <h6 class="fw-bold text-dark">${n}</h6>
                <div class="row">`;
            
            let i = 0;
            $('.kelas-check:checked').each(function() {
                let kId = $(this).val();
                let kName = window.kelasMap[kId].nama_kelas;
                
                // Default round robin
                let defaultTeacher = teachersForThisMapel[i % teachersForThisMapel.length];
                
                // create select
                let selectHtml = `<select class="form-select form-select-sm mapel-dist-select" data-mapel-name="${n}" data-kelas-id="${kId}">`;
                teachersForThisMapel.forEach(t => {
                    let sel = (t.id == defaultTeacher.id) ? 'selected' : '';
                    selectHtml += `<option value="${t.id}" ${sel}>${t.name}</option>`;
                });
                selectHtml += `</select>`;

                htmlDistribusi += `
                    <div class="col-md-4 mb-2">
                        <label class="small fw-bold text-secondary">${kName}</label>
                        ${selectHtml}
                    </div>
                `;
                i++;
            });
            htmlDistribusi += `</div></div>`;
        });

        if (hasDistribusi) {
            $('#distribusi-guru-container').html(htmlDistribusi);
            $('#distribusi-guru-section').removeClass('d-none');
        } else {
            $('#distribusi-guru-section').addClass('d-none');
            $('#distribusi-guru-container').html('');
        }
    });

    $('#btn-back-1c').click(function() {
        $('#step-1c').addClass('d-none');
        $('#step-1b').removeClass('d-none');
    });

    // Lanjut ke Step 2 (Validasi)
    $('#btn-next-1c').click(function() {
        let kelasIds = $('.kelas-check:checked').map(function() { return $(this).val(); }).get();
        let mapelIds = $('.mapel-check:checked').map(function() { return $(this).val(); }).get();
        
        if(mapelIds.length === 0) {
            alert("Pilih minimal 1 mata pelajaran!"); return;
        }

        $('#step-1c').addClass('d-none');
        $('#step-2').removeClass('d-none');
        $('#options-container').addClass('d-none');
        $('#validation-result').addClass('d-none');
        $('#validation-loading').removeClass('d-none');

        // Validasi
        $.post("{{ route('sistem_akademik.auto-schedule.validate') }}", {
            _token: "{{ csrf_token() }}",
            jurusan: selectedJurusan,
            kelas_ids: kelasIds,
            mapel_ids: mapelIds
        }, function(res) {
            $('#validation-loading').addClass('d-none');
            $('#validation-result').removeClass('d-none');

            if(res.success) {
                $('#validation-result').html('<div class="alert alert-success">Semua data valid. Siap untuk generate jadwal.</div>');
                $('#options-container').removeClass('d-none');
                $('#btn-generate').prop('disabled', false);
            } else {
                let errHtml = '<div class="alert alert-danger"><strong>Ditemukan Masalah Data:</strong><ul>';
                res.errors.forEach(e => errHtml += `<li>${e}</li>`);
                errHtml += '</ul>Silakan perbaiki data di menu Master Data lalu kembali lagi ke halaman ini.</div>';
                $('#validation-result').html(errHtml);
                $('#options-container').removeClass('d-none');
                $('#btn-generate').prop('disabled', true);
            }
        });
    });

    $('#btn-back-2').click(function() {
        $('#step-2').addClass('d-none');
        $('#step-1c').removeClass('d-none');
    });

    let pollInterval;
    $('#btn-generate').click(function() {
        let kelasIds = $('.kelas-check:checked').map(function() { return $(this).val(); }).get();
        let mapelIds = $('.mapel-check:checked').map(function() { return $(this).val(); }).get();
        
        let mapelRooms = {};
        $('.mapel-room-input').each(function() {
            let mName = $(this).data('name');
            let room = $(this).val().trim();
            if (room !== '') {
                // Apply this room to all mapels with this name
                $(`.mapel-check:checked[data-name="${mName}"]`).each(function() {
                    mapelRooms[$(this).val()] = room;
                });
            }
        });

        let mapelDistributions = {};
        $('.mapel-dist-select').each(function() {
            let mName = $(this).data('mapel-name');
            let kId = $(this).data('kelas-id');
            let selectedMapelId = $(this).val();
            if (!mapelDistributions[mName]) mapelDistributions[mName] = {};
            mapelDistributions[mName][kId] = selectedMapelId;
        });
        
        $('#step-2').addClass('d-none');
        $('#step-3').removeClass('d-none');
        sessionStorage.removeItem('wizardState'); // Bersihkan state jika sudah mulai generate

        $.post("{{ route('sistem_akademik.auto-schedule.generate') }}", {
            _token: "{{ csrf_token() }}",
            jurusan: selectedJurusan,
            kelas_ids: kelasIds,
            mapel_ids: mapelIds,
            mapel_rooms: mapelRooms,
            mapel_distributions: mapelDistributions,
            opt_umum: $('#opt-umum').is(':checked'),
            opt_jurusan: $('#opt-jurusan').is(':checked')
        }, function(res) {
            if(res.success) {
                let genId = res.generation_id;
                
                // Mulai polling status
                pollInterval = setInterval(function() {
                    $.get("{{ url('sistem-akademik/auto-schedule/status') }}/" + genId, function(stRes) {
                        $('#generate-status').text('Status: ' + stRes.status.toUpperCase());
                        if(stRes.status === 'completed' || stRes.status === 'failed') {
                            clearInterval(pollInterval);
                            window.location.href = "{{ url('sistem-akademik/auto-schedule/result') }}/" + genId;
                        }
                    });
                }, 3000); // Tiap 3 detik
            }
        });
    });
});
</script>
@endsection
