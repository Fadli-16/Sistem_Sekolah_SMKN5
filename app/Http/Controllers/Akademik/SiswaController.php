<?php

namespace App\Http\Controllers\Akademik;

use App\Models\Kelas;
use App\Models\User;
use App\Models\Siswa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class SiswaController extends Controller
{
    // target maksimal gambar setelah kompresi (bytes) 500 kb
    protected int $maxImageBytes = 500 * 1024;

    public function index(Request $request)
    {
        $title  = 'Siswa';
        $header = 'Kelola Data Siswa';

        if ($request->has('reset')) {
            session()->forget('siswa_filters');
            return redirect()->route('sistem_akademik.siswa.index');
        }

        if ($request->hasAny(['jurusan', 'kelas_id', 'tahun_masuk'])) {
            $filters = array_filter($request->only(['jurusan', 'kelas_id', 'tahun_masuk']), fn($val) => !is_null($val));
            session()->put('siswa_filters', $filters);
        } else {
            $filters = session('siswa_filters', []);
            if (!empty($filters)) {
                $request->merge($filters);
            }
        }

        // Base Query
        $query = Siswa::with(['user', 'kelas', 'kelasData'])
            ->leftJoin('users', 'siswa.user_id', '=', 'users.id')
            ->select('siswa.*');

        // Filter Jurusan (dari tabel kelas)
        if ($request->filled('jurusan')) {
            $query->whereHas('kelasData', function ($q) use ($request) {
                $q->where('jurusan', $request->jurusan);
            });
        }

        // Filter Kelas
        if ($request->filled('kelas_id')) {
            $query->where('siswa.kelas_id', $request->kelas_id);
        }

        // Filter Tahun Masuk
        if ($request->filled('tahun_masuk')) {
            $query->where('siswa.tahun_masuk', $request->tahun_masuk);
        }

        $students = $query->orderByRaw("COALESCE(users.nama, '') asc")->get();
        
        // Data pendukung filter
        $kelasQuery = Kelas::orderBy('nama_kelas');
        if ($request->filled('jurusan')) {
            $kelasQuery->where('jurusan', $request->jurusan);
        }
        $kelas = $kelasQuery->get();
        $jurusanList = Kelas::select('jurusan')->distinct()->whereNotNull('jurusan')->orderBy('jurusan')->get();
        $tahunMasukList = Siswa::select('tahun_masuk')->distinct()->whereNotNull('tahun_masuk')->orderBy('tahun_masuk', 'desc')->get();

        return view('sistem_akademik.siswa.index', compact('students', 'title', 'header', 'kelas', 'jurusanList', 'tahunMasukList'));
    }

    public function create()
    {
        $title = 'Siswa';
        $header = 'Tambah Data Siswa';
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('sistem_akademik.siswa.createOrEdit', compact('title', 'header', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users',
            'password' => 'nullable|min:6',
            'nis' => 'required|string|max:20|unique:siswa',
            'kelas_id' => 'required|exists:kelas,id',
            'tempat_lahir'  => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'tahun_masuk' => 'nullable|string|max:4',
            'no_hp' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'agama' => 'nullable|string',
            'image' => 'nullable|image'
        ]);

        $kelas = Kelas::find($request->kelas_id);
        if (!$kelas) {
            return redirect()->back()->with('status', 'error')->with('message', 'Kelas tidak ditemukan.');
        }

        $email = $request->email ?: strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->nama)) . '@gmail.com';
        // pastikan unik
        while(User::where('email', $email)->exists()){
            $email = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->nama)) . rand(10,9999) . '@gmail.com';
        }

        $password = $request->password ? Hash::make($request->password) : Hash::make('user123');

        // create user
        $user = User::create([
            'nis_nip' => $request->nis,
            'nama' => $request->nama,
            'email' => $email,
            'password' => $password,
            'role' => 'siswa',
        ]);

        // create siswa
        $siswa = Siswa::create([
            'user_id' => $user->id,
            'nis' => $request->nis,
            'kelas_id' => $kelas->id,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat ?? '-',
            'tahun_masuk' => $request->tahun_masuk ?? date('Y'),
            'no_hp' => $request->no_hp ?? '-',
            'jenis_kelamin' => $request->jenis_kelamin ?? 'Laki-laki',
            'agama' => $request->agama ?? '-',
        ]);

        // handle image if provided
        if ($request->hasFile('image')) {
            try {
                $saved = $this->saveUploadedImage($request->file('image'));
                if ($saved) {
                    $siswa->image = $saved;
                    $siswa->save();
                }
            } catch (\Throwable $e) {
                Log::warning("Siswa image upload/store failed: " . $e->getMessage());
                // don't block creation, but inform user
                return redirect()->route('sistem_akademik.siswa.index')
                    ->with('status', 'warning')
                    ->with('message', 'Siswa dibuat, namun unggah foto gagal: ' . $e->getMessage());
            }
        }

        return redirect()->route('sistem_akademik.siswa.index')
            ->with('status', 'success')
            ->with('message', 'Data siswa berhasil ditambahkan');
    }

    public function edit(Siswa $siswa)
    {
        $siswa->load('user');
        $title = 'Siswa';
        $header = 'Edit Data Siswa';
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('sistem_akademik.siswa.createOrEdit', compact('siswa', 'title', 'header', 'kelas'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:siswa,nis,' . $siswa->id,
            'kelas_id' => 'required|exists:kelas,id',
            'tempat_lahir'  => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'password' => 'nullable|min:6',
            'email' => 'nullable|email|unique:users,email,' . $siswa->user_id,
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'agama' => 'nullable|string',
            'image' => 'nullable|image'
        ]);

        $kelas = Kelas::find($request->kelas_id);
        if (!$kelas) {
            return redirect()->back()->with('status', 'error')->with('message', 'Kelas tidak ditemukan.');
        }

        $email = $request->email ?: $siswa->user->email;

        // update user
        $siswa->user->update([
            'nama' => $request->nama,
            'email' => $email,
            'nis_nip' => $request->nis,
            'password' => $request->filled('password') ? Hash::make($request->password) : $siswa->user->password,
        ]);

        // update siswa fields
        $siswa->update([
            'nis' => $request->nis,
            'kelas_id' => $kelas->id,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat ?? '-',
            'no_hp' => $request->no_hp ?? '-',
            'jenis_kelamin' => $request->jenis_kelamin ?? 'Laki-laki',
            'agama' => $request->agama ?? '-',
        ]);

        // replace image if uploaded
        if ($request->hasFile('image')) {
            try {
                $saved = $this->saveUploadedImage($request->file('image'));
                if ($saved) {
                    // delete old
                    if (!empty($siswa->image)) {
                        $oldPath = public_path('assets/profile/' . $siswa->image);
                        if (File::exists($oldPath)) {
                            try {
                                File::delete($oldPath);
                            } catch (\Throwable $e) {
                                Log::warning("Failed to delete old siswa image: {$oldPath}. error: " . $e->getMessage());
                            }
                        }
                    }
                    $siswa->image = $saved;
                    $siswa->save();
                }
            } catch (\Throwable $e) {
                Log::warning("Siswa image upload/update failed: " . $e->getMessage());
                return redirect()->route('sistem_akademik.siswa.index')
                    ->with('status', 'warning')
                    ->with('message', 'Perubahan disimpan, tetapi unggah foto gagal: ' . $e->getMessage());
            }
        }

        return redirect()->route('sistem_akademik.siswa.index')
            ->with('status', 'success')
            ->with('message', 'Data siswa berhasil diubah');
    }

    public function destroy(Siswa $siswa)
    {
        // delete profile image file if exists
        if (!empty($siswa->image)) {
            $path = public_path('assets/profile/' . $siswa->image);
            if (File::exists($path)) {
                try {
                    File::delete($path);
                } catch (\Throwable $e) {
                    Log::warning("Failed to delete siswa image on destroy: {$path}. error: " . $e->getMessage());
                }
            }
        }

        // delete related user (cascade)
        $siswa->user()->delete();

        return redirect()->route('sistem_akademik.siswa.index')
            ->with('status', 'success')
            ->with('message', 'Data siswa berhasil dihapus');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        try {
            $students = Siswa::whereIn('id', $ids)->get();
            foreach ($students as $siswa) {
                if (!empty($siswa->image)) {
                    $path = public_path('assets/profile/' . $siswa->image);
                    if (File::exists($path)) {
                        @File::delete($path);
                    }
                }
                $siswa->user()->delete();
                $siswa->delete();
            }
            return response()->json(['success' => true, 'message' => count($ids) . ' data siswa berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /* -------------------- image helper (sama pola guru) -------------------- */

    protected function saveUploadedImage(UploadedFile $file): ?string
    {
        $destDir = public_path('assets/profile');
        if (! File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $orig = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $base = pathinfo($orig, PATHINFO_FILENAME);
        $base = Str::slug(substr($base, 0, 50), '_');
        $filename = time() . '_' . $base . '.' . $ext;
        $tmpPath = $file->getRealPath();
        $destPath = public_path('assets/profile/' . $filename);

        // if already small -> move
        if ($file->getSize() !== null && $file->getSize() <= $this->maxImageBytes) {
            $file->move(public_path('assets/profile'), $filename);
            return $filename;
        }

        // try compressing
        try {
            $ok = $this->compressImageIfNeeded($tmpPath, $destPath, $ext, $this->maxImageBytes);
            if ($ok && File::exists($destPath)) {
                return $filename;
            }
        } catch (\Throwable $e) {
            Log::warning("compressImageIfNeeded failed for siswa upload: " . $e->getMessage());
        }

        // fallback: move original
        try {
            $file->move(public_path('assets/profile'), $filename);
            Log::warning("Image compression fallback used for siswa upload: {$filename}");
            return $filename;
        } catch (\Throwable $e) {
            Log::error("Failed moving uploaded image after compression fallback: " . $e->getMessage());
            throw $e;
        }
    }

    protected function compressImageIfNeeded(string $sourcePath, string $destinationPath, string $ext, int $maxBytes): bool
    {
        if (! file_exists($sourcePath)) {
            throw new \InvalidArgumentException("Source file not found: {$sourcePath}");
        }

        $contents = @file_get_contents($sourcePath);
        if ($contents === false) {
            throw new \RuntimeException("Unable to read uploaded file");
        }

        $img = @imagecreatefromstring($contents);
        if ($img === false) {
            throw new \RuntimeException("Unsupported image format or corrupt image");
        }

        // Optionally scale down image if it's too large to ensure we hit < 500kb
        $width = imagesx($img);
        $height = imagesy($img);
        if ($width > 1200 || $height > 1200) {
            $ratio = $width / $height;
            if ($width > $height) {
                $newWidth = 1200;
                $newHeight = 1200 / $ratio;
            } else {
                $newHeight = 1200;
                $newWidth = 1200 * $ratio;
            }
            $tmpImg = imagecreatetruecolor((int)$newWidth, (int)$newHeight);
            
            // preserve transparency for PNG/WebP
            $mime = getimagesize($sourcePath)['mime'] ?? null;
            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagealphablending($tmpImg, false);
                imagesavealpha($tmpImg, true);
                $transparent = imagecolorallocatealpha($tmpImg, 255, 255, 255, 127);
                imagefilledrectangle($tmpImg, 0, 0, (int)$newWidth, (int)$newHeight, $transparent);
            }
            
            imagecopyresampled($tmpImg, $img, 0, 0, 0, 0, (int)$newWidth, (int)$newHeight, $width, $height);
            imagedestroy($img);
            $img = $tmpImg;
        }

        $destroyImage = function ($im) {
            if (! $im) return;
            if (is_resource($im) || (class_exists('GdImage') && $im instanceof \GdImage)) {
                @imagedestroy($im);
            }
        };

        $success = false;
        $mime = getimagesize($sourcePath)['mime'] ?? null;

        if ($mime === 'image/jpeg' || $mime === 'image/jpg' || $mime === 'image/webp') {
            $quality = 90;
            while ($quality >= 40) {
                if ($mime === 'image/webp' && function_exists('imagewebp')) {
                    @imagewebp($img, $destinationPath, $quality);
                } else {
                    @imagejpeg($img, $destinationPath, $quality);
                }
                clearstatcache(true, $destinationPath);
                if (file_exists($destinationPath) && filesize($destinationPath) <= $maxBytes) {
                    $success = true;
                    break;
                }
                $quality -= 10;
            }
        }

        if (! $success && $mime === 'image/png') {
            for ($level = 6; $level <= 9; $level++) {
                @imagepng($img, $destinationPath, $level);
                clearstatcache(true, $destinationPath);
                if (file_exists($destinationPath) && filesize($destinationPath) <= $maxBytes) {
                    $success = true;
                    break;
                }
            }
            if (! $success) {
                $quality = 90;
                while ($quality >= 40) {
                    @imagejpeg($img, $destinationPath, $quality);
                    clearstatcache(true, $destinationPath);
                    if (file_exists($destinationPath) && filesize($destinationPath) <= $maxBytes) {
                        $success = true;
                        break;
                    }
                    $quality -= 10;
                }
            }
        }

        if (! $success && ! in_array($mime, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])) {
            $quality = 85;
            while ($quality >= 40) {
                @imagejpeg($img, $destinationPath, $quality);
                clearstatcache(true, $destinationPath);
                if (file_exists($destinationPath) && filesize($destinationPath) <= $maxBytes) {
                    $success = true;
                    break;
                }
                $quality -= 10;
            }
        }

        $destroyImage($img);

        return $success;
    }
}
