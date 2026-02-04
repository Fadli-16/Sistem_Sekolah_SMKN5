<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Akademik\SiswaController;
use App\Http\Controllers\Akademik\BeritaController;
use App\Http\Controllers\Akademik\SistemAkademikController;
use App\Http\Controllers\Akademik\KelasController;
use App\Http\Controllers\Akademik\CourseController;
use App\Http\Controllers\Akademik\PeminatanController;
use App\Http\Controllers\Akademik\GuruController;
use App\Http\Controllers\Akademik\MataPelajaranController;
use App\Http\Controllers\Akademik\ProfileController;

use App\Http\Controllers\BukuController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PpdbController;

use App\Http\Controllers\MagangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\LaboratoriumController;
use App\Http\Controllers\Admin\KerusakanController as AdminKerusakanController;
use App\Http\Controllers\Admin\InventarisController as AdminInventarisController;
use App\Http\Controllers\Admin\LaboratoriumController as AdminLaboratoriumController;

use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\DaftarUlangController;
use App\Http\Controllers\Admin\AdminPpdbController;

use App\Http\Controllers\WakilPerusahaanController;
use App\Http\Controllers\Admin\AdminWakilPerusahaanController;
use App\Http\Controllers\WakilPerusahaanDashboardController;
use App\Http\Controllers\WakilPerusahaanOpeningsController;
use App\Http\Controllers\Admin\JadwalLaboratoriumController;
use App\Http\Controllers\Admin\LaborCrudController;
use App\Http\Controllers\Siswa\LaborController;
use App\Http\Controllers\Siswa\JadwalController;
use App\Http\Controllers\Siswa\InventarisController as SiswaInventarisController;
use App\Http\Controllers\Siswa\LaporanController;
use App\Http\Controllers\WakilPerusahaanInternsController;
use App\Http\Controllers\Siswa\MagangLaporanController;
use App\Http\Controllers\WakilPerusahaanReportsController;
use App\Http\Controllers\Mitra\PenilaianController;
use App\Http\Controllers\Admin\NilaiAkhirController;

use App\Http\Controllers\UserController;

use App\Http\Controllers\WakilController;
use App\Http\Controllers\PengajuanJudulController;
use App\Http\Controllers\PengajuanJudulSiswaController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Auth routes
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']);

// Super Admin Routes
Route::prefix('admin/manage')->name('admin.manage.')->middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/', [SuperAdminController::class, 'index'])->name('index');

    // Export users to CSV
    Route::get('users/export', [UserController::class, 'export'])
        ->name('users.export');

    // import CSV
    Route::get('users/import', [UserController::class, 'showImportForm'])
        ->name('users.import');
    Route::post('users/import', [UserController::class, 'import'])
        ->name('users.import.post');

    // User management routes
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users');
    Route::get('/users/create', [SuperAdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [SuperAdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [SuperAdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [SuperAdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/bulk-delete', [SuperAdminController::class, 'bulkDestroy'])->name('users.bulkDestroy');
    Route::delete('/users/{user}', [SuperAdminController::class, 'destroyUser'])->name('users.destroy');
});

// PPDB Routes - Make registration accessible to all users
Route::prefix('ppdb')->name('ppdb.')->group(function () {
    // Public routes accessible by anyone (including guests)
    Route::get('/', [PpdbController::class, 'index'])->name('index');
    Route::get('/create', [PpdbController::class, 'create'])->name('create');
    Route::post('/store', [PpdbController::class, 'store'])->name('store');

    // Admin-only routes requiring admin_ppdb role
    Route::middleware(['auth', 'role:super_admin,admin_ppdb'])->group(function () {
        Route::get('/laporan', [PpdbController::class, 'laporan'])->name('laporan');
        Route::get('/{calonSiswa}/edit', [PpdbController::class, 'edit'])->name('edit');
        Route::put('/{calonSiswa}', [PpdbController::class, 'update'])->name('update');
        Route::delete('/{calonSiswa}', [PpdbController::class, 'destroy'])->name('destroy');
        Route::post('/kirim-email-kelulusan/{calonSiswa}', [PpdbController::class, 'sendEmailKelulusan'])->name('emailkelulusan');
    });
});

// Daftar Ulang Routes
Route::get('/daftar-ulang', [DaftarUlangController::class, 'create'])->name('daftar-ulang.create');
Route::post('/daftar-ulang', [DaftarUlangController::class, 'store'])->name('daftar-ulang.store');
Route::get('/daftar-ulang/success', [DaftarUlangController::class, 'success'])->name('daftar-ulang.success');

// Admin PPDB Routes
Route::prefix('admin/ppdb')->name('admin.ppdb.')->middleware(['auth', 'role:admin_ppdb'])->group(function () {
    Route::get('/daftar-ulang', [AdminPpdbController::class, 'daftarUlangIndex'])->name('daftar-ulang.index');
    Route::put('/daftar-ulang/{id}/approve', [AdminPpdbController::class, 'approveDaftarUlang'])->name('daftar-ulang.approve');
    Route::put('/daftar-ulang/{id}/reject', [AdminPpdbController::class, 'rejectDaftarUlang'])->name('daftar-ulang.reject');
});

// Sistem Akademik - Consolidate routes
Route::prefix('sistem-akademik')
    ->name('sistem_akademik.')
    ->middleware(['auth'])
    ->group(function () {

        Route::get('/', [SistemAkademikController::class, 'index'])->name('index');
        Route::get('/dashboard', [App\Http\Controllers\Akademik\SistemAkademikController::class, 'index'])->name('dashboard');

        /*
    |--------------------------------------------------------------------------
    | BERITA - AKSES UMUM (SEMUA USER LOGIN)
    |--------------------------------------------------------------------------
    */
        Route::get('berita', [BeritaController::class, 'index'])->name('berita.index');
        /*
    |--------------------------------------------------------------------------
    | BERITA - KHUSUS ADMIN
    |--------------------------------------------------------------------------
    */
        Route::middleware(['role:super_admin,admin_sa'])->group(function () {
            Route::get('berita/create', [BeritaController::class, 'create'])->name('berita.create');
            Route::post('berita', [BeritaController::class, 'store'])->name('berita.store');
            Route::get('berita/{berita}/edit', [BeritaController::class, 'edit'])->name('berita.edit');
            Route::put('berita/{berita}', [BeritaController::class, 'update'])->name('berita.update');
            Route::delete('berita/{berita}', [BeritaController::class, 'destroy'])->name('berita.destroy');
        });
    
        Route::get('berita/{berita}', [BeritaController::class, 'show'])->name('berita.show');
        /*
    |--------------------------------------------------------------------------
    | ADMIN MODULE
    |--------------------------------------------------------------------------
    */
        Route::middleware(['role:super_admin,admin_sa'])->group(function () {
            Route::resource('kelas', KelasController::class);
            Route::resource('guru', GuruController::class);
            Route::resource('siswa', SiswaController::class);

            Route::get('/get-students-by-jurusan', [CourseController::class, 'getStudentsByJurusan'])
                ->name('get-students-by-jurusan');
        });

    /*
    |--------------------------------------------------------------------------
    | PROFILE & LAINNYA
    |--------------------------------------------------------------------------
    */
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('updateProfile'); 
        Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('updatePhoto'); 
        Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('updatePassword');

        Route::resource('mataPelajaran', MataPelajaranController::class);
        Route::resource('peminatan', PeminatanController::class);
        Route::resource('course', CourseController::class);
    });

// Perpustakaan Routes - Split into public and admin routes
Route::prefix('perpustakaan')->name('perpustakaan.')->group(function () {
    // Public routes for viewing books - accessible by all users
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index')->middleware(['auth']);
    Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
    Route::get('/buku/{buku}', [BukuController::class, 'show'])->name('buku.show')->middleware(['auth']);
    Route::get('/buku/{buku}/pdf', [BukuController::class, 'showPdf'])->name('buku.pdf')->middleware(['auth']);

    // Student and teacher specific routes
    Route::middleware(['auth', 'role:super_admin,admin_perpus,guru,siswa'])->group(function () {
        Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
        Route::get('/peminjaman/history', [PeminjamanController::class, 'history'])->name('peminjaman.history');
    });

    // Admin-only routes
    Route::middleware(['auth', 'role:super_admin,admin_perpus'])->group(function () {
        Route::resource('buku', BukuController::class)->except(['index', 'show']);
        Route::resource('peminjaman', PeminjamanController::class)->except(['create', 'store']);
        Route::resource('kategori', KategoriController::class);
    });
});

// Laboratorium Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super_admin,admin_lab'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/kelola-inventaris', [AdminInventarisController::class, 'index'])->name('kelola.inv');
    Route::post('/kelola-inventaris', [AdminInventarisController::class, 'store'])->name('kelola.inv.post');
    Route::get('/kelola-peminjaman', [AdminInventarisController::class, 'pinjam'])->name('kelola.inv.show');
    Route::get('/kelola-peminjaman/status/{id}', [AdminInventarisController::class, 'status'])->name('kelola.inv.status');
    Route::post('/kelola-peminjaman/status/{id}', [AdminInventarisController::class, 'statusUpdate'])->name('kelola.inv.status.update');
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('kelola/lab', LaboratoriumController::class);
    });

    // New routes for Jadwal CRUD
    Route::resource('jadwal', JadwalLaboratoriumController::class);

    // New routes for Inventaris CRUD
    Route::resource('inventaris', AdminInventarisController::class);

    // New routes for Laboratorium CRUD
    Route::resource('labor', LaborCrudController::class);

    Route::get('/kelola-laboratorium', [AdminLaboratoriumController::class, 'index'])->name('kelola.lab');
    Route::post('/kelola-laboratorium', [AdminLaboratoriumController::class, 'store'])->name('kelola.lab.post');
    Route::get('/kelola/laporan', [App\Http\Controllers\Admin\KerusakanController::class, 'index'])->name('kelola.laporan');
    Route::put('/kelola/laporan/{id}', [App\Http\Controllers\Admin\KerusakanController::class, 'update'])->name('kelola.laporan.update');
});

// Magang Routes - Allow students to view and apply
Route::prefix('magang')->name('magang.')->group(function () {
    // Public routes for viewing
    Route::get('/dashboard', [MagangController::class, 'dashboard'])->name('dashboard')->middleware(['auth']);

    // Routes for students to apply
    Route::middleware(['auth', 'role:super_admin,admin_magang,siswa'])->group(function () {
        Route::get('/magang/create', [MagangController::class, 'create'])->name('magang.create');
        Route::post('/magang', [MagangController::class, 'store'])->name('magang.store');
        Route::post('/magang/apply', [MagangController::class, 'apply'])->name('apply');
    });

    // Admin-only routes
    Route::middleware(['auth', 'role:super_admin,admin_magang'])->group(function () {
        Route::get('/magang', [MagangController::class, 'index'])->name('magang.index');
        Route::get('/magang/{magang}/edit', [MagangController::class, 'edit'])->name('magang.edit');
        Route::put('/magang/{magang}', [MagangController::class, 'update'])->name('magang.update');
        Route::delete('/magang/{magang}', [MagangController::class, 'destroy'])->name('magang.destroy');
        Route::resource('perusahaan', PerusahaanController::class);
    });
});

// Routes untuk pendaftaran Mitra Magang
Route::get('/daftar-mitra-magang', [WakilPerusahaanController::class, 'showRegistrationForm'])->name('magang.wakil_perusahaan.register');
Route::post('/daftar-mitra-magang', [WakilPerusahaanController::class, 'register'])->name('magang.wakil_perusahaan.store');
Route::get('/daftar-mitra-magang/success', [WakilPerusahaanController::class, 'showSuccessPage'])->name('magang.wakil_perusahaan.success');
Route::get('/profile/edit', [WakilPerusahaanController::class, 'editProfile'])->name('magang.wakil_perusahaan.profile.edit');
Route::put('/profile', [WakilPerusahaanController::class, 'updateProfile'])->name('magang.wakil_perusahaan.profile.update');

// Routes untuk Admin mengelola pendaftaran Mitra Magang
Route::prefix('admin/magang')->name('admin.magang.')->middleware(['auth', 'role:super_admin,admin_magang'])->group(function () {
    Route::get('/wakil-perusahaan', [AdminWakilPerusahaanController::class, 'index'])->name('wakil_perusahaan.index');
    Route::put('/wakil-perusahaan/{id}/approve', [AdminWakilPerusahaanController::class, 'approve'])->name('wakil_perusahaan.approve');
    Route::put('/wakil-perusahaan/{id}/reject', [AdminWakilPerusahaanController::class, 'reject'])->name('wakil_perusahaan.reject');
});

// Routes untuk Wakil Perusahaan setelah login
Route::prefix('magang/wakil_perusahaan')->name('magang.wakil_perusahaan.')->middleware(['auth', 'role:wakil_perusahaan'])->group(function () {
    Route::get('/dashboard', [WakilPerusahaanDashboardController::class, 'index'])->name('dashboard');
    Route::get('penilaian/create', [App\Http\Controllers\Mitra\PenilaianController::class, 'create'])->name('magang.wakil_perusahaan.penilaian.create');
    Route::get('/openings', [WakilPerusahaanOpeningsController::class, 'index'])->name('openings.index');
    Route::get('/openings/create', [WakilPerusahaanOpeningsController::class, 'create'])->name('openings.create');
    Route::post('/openings', [WakilPerusahaanOpeningsController::class, 'store'])->name('openings.store');
    Route::get('/openings/{id}/edit', [WakilPerusahaanOpeningsController::class, 'edit'])->name('openings.edit');
    Route::put('/openings/{id}', [WakilPerusahaanOpeningsController::class, 'update'])->name('openings.update');
    Route::delete('/openings/{id}', [WakilPerusahaanOpeningsController::class, 'destroy'])->name('openings.destroy');
    Route::get('/openings/{id}/applicants', [WakilPerusahaanOpeningsController::class, 'showApplicants'])->name('openings.applicants');

    // Other routes for Wakil Perusahaan
    Route::get('/interns', [WakilPerusahaanInternsController::class, 'index'])->name('interns');
    Route::get('/interns/{id}', [WakilPerusahaanInternsController::class, 'show'])->name('interns.show');
    Route::put('/interns/{id}/approve', [WakilPerusahaanInternsController::class, 'approve'])->name('interns.approve');
    Route::put('/interns/{id}/reject', [WakilPerusahaanInternsController::class, 'reject'])->name('interns.reject');
    Route::get('/profile', [WakilPerusahaanController::class, 'profile'])->name('profile');
});

// Group untuk routes yang bisa diakses wakil_perusahaan & admin_magang
Route::middleware(['auth', 'role:wakil_perusahaan,admin_magang'])
    ->prefix('magang/wakil_perusahaan')
    ->name('magang.wakil_perusahaan.')
    ->group(function () {
        Route::get('/reports', [WakilPerusahaanReportsController::class, 'index'])->name('reports');
        Route::get('/reports/{id}', [WakilPerusahaanReportsController::class, 'show'])->name('reports.show');
        Route::put('/reports/{id}/review', [WakilPerusahaanReportsController::class, 'review'])->name('reports.review');
    });


// For the home page laboratory link
Route::get('/laboratorium', function () {
    if (Auth::check() && Auth::user()->role == 'siswa') {
        return redirect()->route('siswa.labor.index');
    }
    // For guests, redirect to login with source parameter
    if (!Auth::check()) {
        return redirect()->route('login', ['from' => 'laboratory']);
    }
    return redirect()->route('lab.dashboard');
})->name('laboratorium.link');

// Redirects for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/lab/dashboard', function () {
        if (Auth::user()->role == 'siswa') {
            return redirect()->route('siswa.labor.index');
        }
        return redirect()->route('admin.dashboard');
    })->name('lab.dashboard');

    Route::get('/lab/jadwal', function () {
        return redirect()->route('admin.jadwal.index');
    })->name('lab.jadwal');

    Route::get('/lab/index', function () {
        return redirect()->route('admin.labor.index');
    })->name('lab.index');

    Route::get('/inv/index', function () {
        return redirect()->route('admin.inventaris.index');
    })->name('inv.index');

    Route::get('/inv/laporan', function () {
        return redirect()->route('admin.kelola.laporan');
    })->name('inv.laporan');
});

// Add public (unauthenticated) routes with different names
Route::middleware('guest')->group(function () {
    Route::get('/laboratorium', [LaboratoriumController::class, 'dashboard'])->name('laboratorium.public');
    // Other public lab routes if needed
});

// Siswa routes for laboratory management
Route::prefix('siswa')->name('siswa.')->middleware(['auth', 'role:siswa'])->group(function () {
    // Laboratory routes
    Route::get('/labor', [LaborController::class, 'index'])->name('labor.index');
    Route::get('/labor/{id}', [LaborController::class, 'show'])->name('labor.show');

    // Laboratory schedule routes
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('jadwal', \App\Http\Controllers\Admin\JadwalController::class);
    });

    // Inventory routes
    Route::get('/inventaris', [SiswaInventarisController::class, 'index'])->name('inventaris.index');
    Route::get('/inventaris/{id}', [SiswaInventarisController::class, 'show'])->name('inventaris.show');

    // Damage report routes
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/create', [LaporanController::class, 'create'])->name('laporan.create');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{id}', [LaporanController::class, 'show'])->name('laporan.show');
});

// Student Report Routes
Route::prefix('magang/siswa')->name('magang.siswa.')->middleware(['auth', 'role:siswa'])->group(function () {
    Route::resource('laporan', \App\Http\Controllers\Siswa\MagangLaporanController::class);
});

// Student Dashboard Route
Route::get('/siswa/dashboard', [App\Http\Controllers\Siswa\DashboardController::class, 'index'])
    ->name('siswa.dashboard')
    ->middleware(['auth', 'role:siswa']);

Route::resource('kategori', KategoriController::class)->middleware(['auth']);
Route::prefix('perpustakaan')->name('perpustakaan.')->group(function () {
    Route::resource('kategori', KategoriController::class);
});

Route::middleware('role:mitra')->group(function () {
    Route::resource('penilaian', PenilaianController::class);
});

Route::middleware('role:guru')->group(function () {
    Route::get('laporan/create', [Guru\LaporanController::class, 'create']);
    Route::post('laporan/store', [Guru\LaporanController::class, 'store']);
});

Route::middleware('role:siswa')->group(function () {
    Route::get('nilai', [Siswa\NilaiController::class, 'index']);
    Route::get('nilai/download', [Siswa\NilaiController::class, 'download']);
});


Route::prefix('magang/wakil_perusahaan')->middleware('auth', 'role:wakil_perusahaan')->group(function () {
    Route::get('/penilaian', [PenilaianController::class, 'index'])->name('magang.wakil_perusahaan.penilaian.index');
    Route::get('/penilaian/create', [PenilaianController::class, 'create'])->name('magang.wakil_perusahaan.penilaian.create');
    Route::post('/penilaian', [PenilaianController::class, 'store'])->name('penilaian.store');
    Route::get('/penilaian/{id}/edit', [PenilaianController::class, 'edit'])->name('magang.wakil_perusahaan.penilaian.edit');
    Route::put('/penilaian/{id}', [PenilaianController::class, 'update'])->name('magang.wakil_perusahaan.penilaian.update');
    Route::get('/penilaian/{id}', [PenilaianController::class, 'show'])->name('magang.wakil_perusahaan.penilaian.show');
});

Route::get('/magang', [MagangController::class, 'index'])->name('magang.magang.index');
Route::put('/profile/foto', [\App\Http\Controllers\Magang\ProfileController::class, 'updateFoto'])->name('magang.profile.updateFoto');


Route::prefix('magang/wakil_perusahaan')->middleware(['auth', 'role:admin_magang'])->group(function () {
    Route::get('/nilaiakhir', [NilaiAkhirController::class, 'index'])->name('magang.wakil_perusahaan.nilaiakhir.index');
    Route::get('/nilaiakhir/create', [NilaiAkhirController::class, 'create'])->name('magang.wakil_perusahaan.nilaiakhir.create');
    Route::post('/nilaiakhir', [NilaiAkhirController::class, 'store'])->name('nilai_akhir.store');
});


Route::get('/admin/magang/wakil_perusahaan/nilai-akhir', [NilaiAkhirController::class, 'index'])->name('magang.admin.wakil_perusahaan.nilai-akhir.index');

Route::get('/profil-kepsek', function () {
    return view('home.sections.profilkepsek');
});

Route::middleware(['auth', 'role:super_admin,admin_magang'])->prefix('magang/perusahaan')->name('magang.perusahaan.')->group(function () {
    Route::get('/', [WakilPerusahaanController::class, 'index'])->name('index');
    Route::get('/create', [WakilPerusahaanController::class, 'create'])->name('create');
    Route::post('/', [WakilPerusahaanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [WakilPerusahaanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [WakilPerusahaanController::class, 'update'])->name('update');
    Route::delete('/{id}', [WakilPerusahaanController::class, 'destroy'])->name('destroy');
});


Route::prefix('magang/perusahaan')->name('magang.perusahaan.')->group(function () {
    Route::get('/', [WakilController::class, 'index'])->name('index');
    Route::get('/create', [WakilController::class, 'create'])->name('create');
    Route::post('/', [WakilController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [WakilController::class, 'edit'])->name('edit');
    Route::put('/{id}', [WakilController::class, 'update'])->name('update');
    Route::delete('/{id}', [WakilController::class, 'destroy'])->name('destroy');
});

Route::resource('magang/perusahaan', WakilController::class)->names('magang.perusahaan');

Route::put('admin/magang/wakil_perusahaan/{id}/approve', [WakilPerusahaanController::class, 'approve'])->name('admin.magang.wakil_perusahaan.approve');



Route::prefix('magang/admin')->middleware(['auth', 'role:admin_magang'])->group(function () {
    Route::get('/pengajuan-judul', [PengajuanJudulController::class, 'index'])->name('magang.admin.pengajuan_judul.index');
    Route::post('/pengajuan-judul/{id}/review', [PengajuanJudulController::class, 'review'])->name('admin.pengajuan-judul.review');
    Route::get('/pengajuan-judul/export-pdf', [PengajuanJudulController::class, 'exportPdf'])->name('admin.pengajuan-judul.export-pdf');
});

Route::get('magang/wakil-perusahaan/profile', [WakilPerusahaanController::class, 'profile'])
    ->name('magang.wakil_perusahaan.profile')
    ->middleware('auth');


Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('magang/pengajuan-judul', [PengajuanJudulSiswaController::class, 'index'])->name('magang.pengajuan_judul.indexsiswa');
    Route::get('magang/pengajuan-judul/create', [PengajuanJudulSiswaController::class, 'create'])->name('magang.pengajuan_judul.create');
    Route::post('magang/pengajuan-judul', [PengajuanJudulSiswaController::class, 'store'])->name('pengajuan-judul.store');
});

Route::get('/nilai-akhir/export/', [NilaiAkhirController::class, 'exportPdf'])->name('magang.wakil_perusahaan.nilaiakhir.export');
