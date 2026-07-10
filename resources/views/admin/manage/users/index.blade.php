@extends('admin.layouts.superadmin')

@section('title', 'User Management - Super Admin Dashboard')

@section('page-title', 'User Management')

@section('styles')
<style>
    .role-badge {
        padding: 0.35rem 0.7rem;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .role-super-admin {
        background-color: rgba(156, 39, 176, 0.1);
        color: #9c27b0;
    }

    .role-admin {
        background-color: rgba(25, 118, 210, 0.1);
        color: #1976d2;
    }
    .role-wakil {
        background-color: rgba(25, 118, 210, 0.1);
        color: #ed650a;
    }

    .role-guru {
        background-color: rgba(46, 125, 50, 0.1);
        color: #2e7d32;
    }

    .role-siswa {
        background-color: rgba(245, 124, 0, 0.1);
        color: #f57c00;
    }

    .sa-datatable thead th {
        font-weight: 600;
        padding: 1rem;
        background-color: rgba(52, 152, 219, 0.05);
    }

    .sa-datatable tbody td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
    }

    .sa-datatable.table-striped>tbody>tr:nth-of-type(odd)>* {
        background-color: rgba(249, 249, 249, 0.7);
    }

    .sa-search-input {
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        padding: 0.5rem 1rem;
        padding-right: 2.5rem;
    }

    .sa-search-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--sa-text-muted);
    }

    .dataTables_filter {
        position: relative;
        margin-bottom: 1rem;
    }

    .dataTables_length select {
        border-radius: 8px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        padding: 0.25rem 0.5rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 6px;
        border: none !important;
        background: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background-color: var(--sa-secondary) !important;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: var(--sa-secondary-light) !important;
        color: var(--sa-secondary) !important;
    }

    /* Action buttons styling */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: flex-start;
        align-items: center;
        min-width: 90px;
        /* Ensure minimum width for the action buttons */
    }

    .action-buttons .sa-btn-sm {
        padding: 0.35rem 0.5rem;
        font-size: 0.8rem;
    }
</style>
@endsection

@section('content')
<div class="sa-page-header">
    <h1 class="sa-page-header-title">User Management</h1>
    <p class="sa-page-header-subtitle">Manage all system users and their roles</p>
</div>

<div class="sa-card sa-mb-5">
    <div class="sa-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="sa-card-header-title">All Users</h5>
        <div class="d-flex gap-2 flex-wrap">
            <button id="bulkDeleteBtn" class="sa-btn sa-btn-danger" disabled>
                <i class="bi bi-trash"></i> Delete Selected
            </button>

            {{-- Export Guru --}}
            <button type="button" class="sa-btn sa-btn-success"
                data-bs-toggle="modal" data-bs-target="#exportGuruModal">
                <i class="bi bi-mortarboard"></i> Export Guru
            </button>

            {{-- Export Siswa --}}
            <button type="button" class="sa-btn sa-btn-success"
                data-bs-toggle="modal" data-bs-target="#exportSiswaModal">
                <i class="bi bi-people"></i> Export Siswa
            </button>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.manage.users.import') }}" class="sa-btn sa-btn-secondary">
                <i class="bi bi-file-earmark-arrow-up"></i> Import Users
            </a>
            <a href="{{ route('admin.manage.users.create') }}" class="sa-btn sa-btn-primary">
                <i class="bi bi-plus-circle"></i> Add New User
            </a>
        </div>
    </div>
    <div class="sa-card-body">
        <form id="bulkDeleteForm"
            action="{{ route('admin.manage.users.bulkDestroy') }}"
            method="POST">
            @csrf
            @method('DELETE')

            <div class="table-responsive">
                <table class="table sa-datatable" id="usersTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>No</th>
                            <th>NIS / NIP</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $user)
                        <tr>
                            <td>
                                <input type="checkbox" name="selected_users[]" class="select-row" value="{{ $user->id }}">
                            </td>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $user->nis_nip }}</td>
                            <td>{{ $user->nama }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php
                                $roleClass = '';
                                $roleDisplay = '';

                                switch($user->role) {
                                case 'super_admin':
                                $roleClass = 'role-super-admin';
                                $roleDisplay = 'Super Admin';
                                break;
                                case 'admin_ppdb':
                                $roleClass = 'role-admin';
                                $roleDisplay = 'Admin PPDB';
                                break;
                                case 'admin_sa':
                                $roleClass = 'role-admin';
                                $roleDisplay = 'Admin Sistem Akademik';
                                break;
                                case 'admin_perpus':
                                $roleClass = 'role-admin';
                                $roleDisplay = 'Admin Perpustakaan';
                                break;
                                case 'admin_lab':
                                $roleClass = 'role-admin';
                                $roleDisplay = 'Admin Laboratorium';
                                break;
                                case 'admin_magang':
                                $roleClass = 'role-admin';
                                $roleDisplay = 'Admin Magang';
                                break;
                                case 'wakil_perusahaan':
                                $roleClass = 'role-wakil';
                                $roleDisplay = 'Wakil Perusahaan';
                                break;
                                case 'guru':
                                $roleClass = 'role-guru';
                                $roleDisplay = 'Guru';
                                break;
                                case 'siswa':
                                $roleClass = 'role-siswa';
                                $roleDisplay = 'Siswa';
                                break;
                                }
                                @endphp

                                <span class="role-badge {{ $roleClass }}">
                                    {{ $roleDisplay }}
                                </span>
                            </td>
                            <td>{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.manage.users.edit', $user->id) }}" class="sa-btn sa-btn-secondary sa-btn-sm" title="Edit User">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.manage.users.destroy', $user->id) }}" method="POST" id="deleteForm{{ $user->id }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="SuperAdmin.confirmDelete('{{ $user->id }}')" class="sa-btn sa-btn-danger sa-btn-sm" title="Delete User">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Check if DataTable is already initialized
        if (!$.fn.DataTable.isDataTable('#usersTable')) {
            // Initialize DataTable only if not already initialized
            $('#usersTable').DataTable({
                responsive: {
                    details: false // Disable the expand/collapse details function
                },
                "columnDefs": [{
                        "orderable": false,
                        "targets": 7
                    }, // Disable sorting on action column
                    {
                        "responsivePriority": 1,
                        "targets": [0, 1, 7]
                    } // Prioritize Number, Name, and Actions columns
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Search users...",
                    lengthMenu: "_MENU_ users per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ users",
                    infoEmpty: "Showing 0 to 0 of 0 users",
                    infoFiltered: "(filtered from _MAX_ total users)",
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>'
                    }
                }
            });
        }

        // Add the search icon to the search input (same as before)
        const searchInput = $('#usersTable').closest('.dataTables_wrapper').find('.dataTables_filter input');
        searchInput.addClass('sa-search-input');

        if ($('#usersTable').closest('.dataTables_wrapper').find('.sa-search-icon').length === 0) {
            $('#usersTable').closest('.dataTables_wrapper').find('.dataTables_filter label').append('<i class="bi bi-search sa-search-icon"></i>');
        }

        // Toggle all checkboxes
        $('#selectAll').on('change', function() {
            const checked = $(this).is(':checked');
            $('.select-row').prop('checked', checked).trigger('change');
        });

        // Enable/disable bulk delete button
        $('.select-row').on('change', function() {
            const anyChecked = $('.select-row:checked').length > 0;
            $('#bulkDeleteBtn').prop('disabled', !anyChecked);
        });

        // When bulk delete clicked, confirm then submit form
        $('#bulkDeleteBtn').on('click', function() {
            Swal.fire({
                title: "Yakin ingin menghapus semua user yang dipilih?",
                text: "Operasi ini tidak bisa dibatalkan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc3545",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#bulkDeleteForm').submit();
                }
            });
        });

        // Dynamic kelas filter berdasarkan jurusan (Export Siswa modal)
        $('#siswaJurusanFilter').on('change', function() {
            const jurusan = $(this).val();
            $('#siswaKelasFilter option').each(function() {
                if (!$(this).val()) return; // skip placeholder
                const optJurusan = $(this).data('jurusan');
                if (!jurusan || optJurusan === jurusan) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            $('#siswaKelasFilter').val('');
        });

        // SweetAlert untuk Export Siswa + Peminatan
        $('#btnExportSiswa').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: "Sertakan Data Peminatan?",
                text: "Apakah Anda juga ingin mengekspor data peminatan siswa (Minat, Jurusan, Jenis Pekerjaan, Link Raport, dll) bersamaan dengan data biodata?",
                icon: "question",
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonColor: "#198754",
                denyButtonColor: "#0dcaf0",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, sertakan",
                denyButtonText: "Tidak, biodata saja",
                cancelButtonText: "Batal",
            }).then((result) => {
                const form = $('#exportSiswaForm');
                // Remove existing hidden input if any
                form.find('input[name="with_peminatan"]').remove();

                if (result.isConfirmed) {
                    form.append('<input type="hidden" name="with_peminatan" value="1">');
                    form.submit();
                    $('#exportSiswaModal').modal('hide');
                } else if (result.isDenied) {
                    form.append('<input type="hidden" name="with_peminatan" value="0">');
                    form.submit();
                    $('#exportSiswaModal').modal('hide');
                }
            });
        });
    });
</script>

{{-- ===== MODAL: Export Guru ===== --}}
<div class="modal fade" id="exportGuruModal" tabindex="-1" aria-labelledby="exportGuruModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportGuruModalLabel">
                    <i class="bi bi-mortarboard me-2 text-success"></i> Export Data Guru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exportGuruForm" action="{{ route('admin.manage.users.export.guru') }}" method="GET" target="_blank">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Filter Jurusan</label>
                        <select name="jurusan" class="form-select">
                            <option value="">-- Semua Jurusan --</option>
                            @foreach($jurusanGuruList as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kosongkan untuk mengexport semua guru.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Format File</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" value="xlsx" id="guruFormatXlsx" checked>
                                <label class="form-check-label" for="guruFormatXlsx">Excel (.xlsx)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" value="csv" id="guruFormatCsv">
                                <label class="form-check-label" for="guruFormatCsv">CSV (.csv)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="sa-btn sa-btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="exportGuruForm" class="sa-btn sa-btn-success">
                    <i class="bi bi-download me-1"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===== MODAL: Export Siswa ===== --}}
<div class="modal fade" id="exportSiswaModal" tabindex="-1" aria-labelledby="exportSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportSiswaModalLabel">
                    <i class="bi bi-people me-2 text-success"></i> Export Data Siswa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="exportSiswaForm" action="{{ route('admin.manage.users.export.siswa') }}" method="GET" target="_blank">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Filter Jurusan</label>
                        <select name="jurusan" id="siswaJurusanFilter" class="form-select">
                            <option value="">-- Semua Jurusan --</option>
                            @foreach($jurusanSiswaList as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Filter Kelas</label>
                        <select name="kelas_id" id="siswaKelasFilter" class="form-select">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" data-jurusan="{{ $kelas->jurusan }}">
                                    {{ $kelas->nama_kelas }} ({{ $kelas->jurusan }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih jurusan dulu untuk memfilter kelas.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Format File</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" value="xlsx" id="siswaFormatXlsx" checked>
                                <label class="form-check-label" for="siswaFormatXlsx">Excel (.xlsx)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" value="csv" id="siswaFormatCsv">
                                <label class="form-check-label" for="siswaFormatCsv">CSV (.csv)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="sa-btn sa-btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnExportSiswa" class="sa-btn sa-btn-success">
                    <i class="bi bi-download me-1"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>

@endsection