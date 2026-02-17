<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class GuruController extends Controller
{
    // MAX target file size after compression (bytes)
    protected int $maxImageBytes = 500 * 1024; // 500 KB

    public function index()
    {
        $title = 'Data Guru';
        $header = 'Data Guru & Tendik';
        // urut berdasarkan nama user (jika tidak ada fallback ke id)
        $gurus = Guru::with('user')
            ->leftJoin('users', 'guru.user_id', '=', 'users.id')
            ->select('guru.*')
            ->orderByRaw("COALESCE(users.nama, '') asc")
            ->get();

        return view('sistem_akademik.guru.index', compact('gurus', 'title', 'header'));
    }

    public function create()
    {
        $title = 'Guru';
        $header = 'Tambah Data Guru';
        return view('sistem_akademik.guru.createOrEdit', compact('title', 'header'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|min:6',
            'nip'           => 'required|string|unique:guru',
            'kelas'         => 'required|string',
            'jurusan'       => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama'         => 'required|string',
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required',
            'no_hp'         => 'required',
            'image'         => 'nullable|image' // actual size handled below
        ]);

        // Create user with role 'guru'
        $user = User::create([
            'nis_nip'  => $request->nip,
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'guru',
        ]);

        // Create guru record
        $guru = Guru::create([
            'user_id'       => $user->id,
            'nip'           => $request->nip,
            'kelas'         => $request->kelas,
            'jurusan'       => $request->jurusan,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat'        => $request->alamat,
            'no_hp'         => $request->no_hp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama'         => $request->agama,
        ]);

        // handle image upload (if any)
        if ($request->hasFile('image')) {
            try {
                $savedName = $this->saveUploadedImage($request->file('image'));
                if ($savedName) {
                    $guru->image = $savedName;
                    $guru->save();
                }
            } catch (\Throwable $e) {
                Log::warning('Guru image upload/store failed: ' . $e->getMessage());
                // don't block creation — inform user optionally
                return redirect()->route('sistem_akademik.guru.index')
                    ->with('status', 'warning')
                    ->with('message', 'Guru ditambahkan, tetapi unggah foto gagal: ' . $e->getMessage());
            }
        }

        return redirect()->route('sistem_akademik.guru.index')
            ->with('status', 'success')
            ->with('message', 'Guru berhasil ditambahkan');
    }

    public function edit(Guru $guru)
    {
        $guru->load('user');
        $title = 'Guru';
        $header = 'Edit Data Guru';
        return view('sistem_akademik.guru.createOrEdit', compact('guru', 'title', 'header'));
    }

    public function update(Request $request, Guru $guru)
    {
        $request->validate([
            'nama'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $guru->user_id,
            'nip'           => 'required|string|unique:guru,nip,' . $guru->id,
            'kelas'         => 'required|string',
            'jurusan'       => 'required|string',
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required',
            'no_hp'         => 'required',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama'         => 'required|string',
            'image'         => 'nullable|image'
        ]);

        // Update user
        $guru->user->update([
            'nama'  => $request->nama,
            'email' => $request->email,
            'nis_nip' => $request->nip,
        ]);

        // Update guru
        $guru->update([
            'nip'           => $request->nip,
            'kelas'         => $request->kelas,
            'jurusan'       => $request->jurusan,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat'        => $request->alamat,
            'no_hp'         => $request->no_hp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama'         => $request->agama,
        ]);

        // handle image upload (if any)
        if ($request->hasFile('image')) {
            try {
                // save new file
                $savedName = $this->saveUploadedImage($request->file('image'));

                if ($savedName) {
                    // delete old file if exists
                    if (!empty($guru->image)) {
                        $oldPath = public_path('assets/profile/' . $guru->image);
                        if (File::exists($oldPath)) {
                            try {
                                File::delete($oldPath);
                            } catch (\Throwable $e) {
                                Log::warning("Failed to delete old guru image: {$oldPath}. error: " . $e->getMessage());
                            }
                        }
                    }

                    $guru->image = $savedName;
                    $guru->save();
                }
            } catch (\Throwable $e) {
                Log::warning('Guru image upload/update failed: ' . $e->getMessage());
                return redirect()->route('sistem_akademik.guru.index')
                    ->with('status', 'warning')
                    ->with('message', 'Perubahan disimpan, tetapi unggah foto gagal: ' . $e->getMessage());
            }
        }

        return redirect()->route('sistem_akademik.guru.index')
            ->with('status', 'success')
            ->with('message', 'Data guru berhasil diubah');
    }

    public function destroy(Guru $guru)
    {
        // delete profile image file if exists
        if (!empty($guru->image)) {
            $path = public_path('assets/profile/' . $guru->image);
            if (File::exists($path)) {
                try {
                    File::delete($path);
                } catch (\Throwable $e) {
                    Log::warning("Failed to delete guru image on destroy: {$path}. error: " . $e->getMessage());
                }
            }
        }

        // delete user (cascade may remove guru record depending on FK; here we remove user)
        $guru->user()->delete();

        return redirect()->route('sistem_akademik.guru.index')
            ->with('status', 'success')
            ->with('message', 'Data guru berhasil dihapus');
    }

    public function profile()
    {
        $title = 'Profile Guru';
        $guru = auth()->user()->guru; // Assuming authentication is implemented
        return view('sistem_akademik.guru.profile', compact('guru', 'title'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $guru = $user->guru;

        $request->validate([
            'tanggal_lahir' => 'required|date',
            'alamat'        => 'required|string',
            'no_hp'         => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama'         => 'required|string',
        ]);

        $guru->update([
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat'        => $request->alamat,
            'no_hp'         => $request->no_hp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama'         => $request->agama,
        ]);

        return redirect()->route('sistem_akademik.profile')
            ->with('status', 'success')
            ->with('message', 'Profile berhasil diperbarui');
    }

    /**
     * Save uploaded image to public/assets/profile and compress if needed.
     * Returns saved filename or null on failure (throws exceptions on critical errors).
     */
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

        // if upload already small enough -> move
        if ($file->getSize() !== null && $file->getSize() <= $this->maxImageBytes) {
            $file->move($destDir, $name);
            return $name;
        }

        // try compressing
        try {
            $ok = $this->compressImageIfNeeded($tmpPath, $destPath, $ext, $this->maxImageBytes);
            if ($ok && File::exists($destPath)) {
                return $name;
            }
        } catch (\Throwable $e) {
            Log::warning("compressImageIfNeeded failed: " . $e->getMessage());
        }

        // fallback: move original (best effort)
        try {
            $file->move($destDir, $name);
            Log::warning("Image compression fallback used for uploaded file: {$name}");
            return $name;
        } catch (\Throwable $e) {
            Log::error("Failed moving uploaded image after compression fallback: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Try compressing source image and write to destination path.
     * Returns true if success and resulting file <= $maxBytes.
     *
     * Strategy:
     *  - create image resource from string
     *  - for JPEG/WebP: loop quality downwards
     *  - for PNG: try imagepng compression levels, then fallback to JPEG conversion loop
     */
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

        // helper to safely destroy
        $destroyImage = function ($im) {
            if (! $im) return;
            if (is_resource($im) || (class_exists('GdImage') && $im instanceof \GdImage)) {
                // imagedestroy exists in all PHP versions; call if valid
                @imagedestroy($im);
            }
        };

        $success = false;
        $mime = getimagesize($sourcePath)['mime'] ?? null;

        if ($mime === 'image/jpeg' || $mime === 'image/jpg' || $mime === 'image/webp') {
            // start quality high -> lower until threshold or minimum
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

        // --- PNG branch: try PNG compression, then fallback to JPEG conversion ---
        if (! $success && $mime === 'image/png') {
            // try PNG compression levels 6..9 (9 most compression)
            for ($level = 6; $level <= 9; $level++) {
                @imagepng($img, $destinationPath, $level);
                clearstatcache(true, $destinationPath);
                if (file_exists($destinationPath) && filesize($destinationPath) <= $maxBytes) {
                    $success = true;
                    break;
                }
            }

            // fallback: convert to JPEG and try quality loop
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
        // cleanup
        $destroyImage($img);

        return $success;
    }
}
