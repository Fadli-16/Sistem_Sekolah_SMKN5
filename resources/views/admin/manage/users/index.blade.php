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
    <div class="sa-card-header d-flex justify-content-between align-items-center">
        <h5 class="sa-card-header-title">All Users</h5>
        <div class="d-flex gap-2">
            <button id="bulkDeleteBtn" class="sa-btn sa-btn-danger" disabled>
                <i class="bi bi-trash"></i> Delete Selected
            </button>
            <button type="button" class="sa-btn sa-btn-success dropdown-toggle"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-file-earmark-arrow-down"></i> Export Users
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item"
                        href="{{ route('admin.manage.users.export', ['format' => 'xlsx']) }}">
                        Excel (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item"
                        href="{{ route('admin.manage.users.export', ['format' => 'csv']) }}">
                        CSV (.csv)
                    </a>
                </li>
            </ul>
        </div>

        <a href="{{ route('admin.manage.users.import') }}" class="sa-btn sa-btn-secondary">
            <i class="bi bi-file-earmark-arrow-up"></i> Import Users
        </a>

        <a href="{{ route('admin.manage.users.create') }}" class="sa-btn sa-btn-primary">
            <i class="bi bi-plus-circle"></i> Add New User
        </a>
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
    });
</script>
@endsection