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
    // target maksimal gambar setelah kompresi (bytes)
    protected int $maxImageBytes = 500 * 1024; // 500 KB

    public function index()
    {
        $title  = 'Siswa';
        $header = 'Kelola Data Siswa';

        // Ambil semua siswa, beserta relasi 'user' dan 'kelas', urut berdasarkan nama user
        $students = Siswa::with(['user', 'kelas'])
            ->leftJoin('users', 'siswa.user_id', '=', 'users.id')
            ->select('siswa.*')
            ->orderByRaw("COALESCE(users.nama, '') asc")
            ->get();

        return view('sistem_akademik.siswa.index', compact('students', 'title', 'header'));
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
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'nis' => 'required|string|unique:siswa',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'required|string',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'agama' => 'nullable|string',
            'image' => 'nullable|image'
        ]);

        $kelas = Kelas::find($request->kelas_id);
        if (!$kelas) {
            return redirect()->back()->with('status', 'error')->with('message', 'Kelas tidak ditemukan.');
        }

        // create user
        $user = User::create([
            'nis_nip' => $request->nis,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
        ]);

        // create siswa
        $siswa = Siswa::create([
            'user_id' => $user->id,
            'nis' => $request->nis,
            'kelas_id' => $kelas->id,
            'kelas' => $kelas->nama_kelas ?? null,
            'jurusan' => $kelas->jurusan ?? null,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'jenis_kelamin' => $request->jenis_kelamin ?? null,
            'agama' => $request->agama ?? null,
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
            'nis' => 'required|string|unique:siswa,nis,' . $siswa->id,
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'required|string',
            'password' => 'nullable|min:6',
            'email' => 'required|email|unique:users,email,' . $siswa->user_id,
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'agama' => 'nullable|string',
            'image' => 'nullable|image'
        ]);

        $kelas = Kelas::find($request->kelas_id);
        if (!$kelas) {
            return redirect()->back()->with('status', 'error')->with('message', 'Kelas tidak ditemukan.');
        }

        // update user
        $siswa->user->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'nis_nip' => $request->nis,
            'password' => $request->filled('password') ? Hash::make($request->password) : $siswa->user->password,
        ]);

        // update siswa fields
        $siswa->update([
            'nis' => $request->nis,
            'kelas_id' => $kelas->id,
            'kelas' => $kelas->nama_kelas ?? null,
            'jurusan' => $kelas->jurusan ?? null,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'jenis_kelamin' => $request->jenis_kelamin ?? null,
            'agama' => $request->agama ?? null,
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

    public function profile()
    {
        $title = 'Profile';
        $user = auth()->user();

        $user->siswa; // trigger lazy load
        $user->guru;

        return view('sistem_akademik.profile', compact('user', 'title'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;

        $rules = [
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'no_hp' => 'required|string',
        ];
        if (in_array($role, ['super_admin', 'admin_sa'])) {
            $rules['jurusan'] = 'required|string|max:255';
        }

        $data = $request->validate($rules);

        if ($role === 'siswa') {
            $user->siswa->update($data);
        } elseif ($role === 'guru') {
            $user->guru->update($data);
        } else {
            // admin profile logic (if any)
        }

        return redirect()->route('sistem_akademik.profile')->with('status', 'success')->with('message', 'Profile berhasil diperbarui');
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
        $name = time() . '_' . $base . '.' . $ext;
        $tmpPath = $file->getRealPath();
        $destPath = $destDir . DIRECTORY_SEPARATOR . $name;

        // if already small -> move
        if ($file->getSize() !== null && $file->getSize() <= $this->maxImageBytes) {
            $file->move($destDir, $name);
            return $name;
        }

        // try compressing
        try {
            $ok = $this->compressImageIfNeeded($tmpPath, $destPath, $ext, $this->maxImageBytes);
            if ($ok && File::exists($destPath)) return $name;
        } catch (\Throwable $e) {
            Log::warning("compressImageIfNeeded failed for siswa upload: " . $e->getMessage());
        }

        // fallback: move original
        try {
            $file->move($destDir, $name);
            Log::warning("Image compression fallback used for siswa upload: {$name}");
            return $name;
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
