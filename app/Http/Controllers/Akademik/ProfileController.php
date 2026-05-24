<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\AdminProfile;

/**
 * ProfileController handles show/update of profile for all roles.
 */
class ProfileController extends Controller
{
    // MAX target file size after compression (bytes)
    protected int $maxImageBytes = 500 * 1024; // 500 KB
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show profile page for authenticated user.
     */
    public function show()
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load(['siswa', 'guru', 'adminProfile']);

        return view('sistem_akademik.profile', [
            'user' => $user,
            'title' => 'Profile'
        ]);
    }

    /**
     * Update basic profile fields.
     */
    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $rules = [
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string|max:2000',
            'no_hp' => 'nullable|string|max:50',
            'agama' => 'nullable|string|max:100',
            'jurusan' => 'nullable|string|max:255',
        ];

        $data = $request->validate($rules);
        $isRestricted = (bool) ($user->siswa || $user->guru);

        if ($isRestricted) {
            unset($data['nama'], $data['jurusan']);
        }

        $updateUser = [];
        if (isset($data['nama'])) $updateUser['nama'] = $data['nama'];
        if (isset($data['email'])) $updateUser['email'] = $data['email'];

        if (!empty($updateUser)) {
            $user->update($updateUser);
        } else {
        }

        $allowedProfileFields = ['tanggal_lahir', 'jenis_kelamin', 'alamat', 'no_hp', 'agama', 'jurusan'];
        if ($isRestricted) {
            $allowedProfileFields = array_diff($allowedProfileFields, ['jurusan']);
        }

        $profilePayload = [];
        foreach ($allowedProfileFields as $f) {
            if (array_key_exists($f, $data)) {
                $profilePayload[$f] = $data[$f];
            }
        }

        // filter out nulls so we don't overwrite with null
        $filtered = array_filter($profilePayload, function ($v) {
            return $v !== null && $v !== '';
        });

        if ($user->siswa) {
            $user->siswa->update($filtered);
        } elseif ($user->guru) {
            $user->guru->update($filtered);
        } else {
            AdminProfile::updateOrCreate(['user_id' => $user->id], $filtered);
        }

        return back()->with('status', 'success')->with('message', 'Profil berhasil diperbarui.');
    }

    /**
     * Update profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $user = $request->user();

        $model = null;
        if ($user->relationLoaded('siswa') || $user->siswa) {
            $model = $user->siswa;
            $field = 'image';
        } elseif ($user->relationLoaded('guru') || $user->guru) {
            $model = $user->guru;
            $field = 'image';
        } elseif ($user->relationLoaded('adminProfile') || $user->adminProfile) {
            $model = $user->adminProfile;
            $field = 'image';
        } else {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Tidak ada profil terkait untuk menyimpan foto.'], 422)
                : redirect()->back()->with('status', 'error')->with('message', 'Tidak ada profil terkait.');
        }

        // handle image upload with compression
        try {
            $savedName = $this->saveUploadedImage($request->file('image'));
            if ($savedName) {
                // delete old file if exists
                if (!empty($model->{$field})) {
                    $old = public_path('assets/profile/' . $model->{$field});
                    if (File::exists($old)) {
                        @unlink($old);
                    }
                }
                $model->{$field} = $savedName;
                $model->save();
                $url = asset('assets/profile/' . $savedName);

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => true, 'file' => $savedName, 'url' => $url]);
                }

                return redirect()->back()->with('status', 'success')->with('message', 'Foto profil berhasil diperbarui.');
            }
        } catch (\Throwable $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Upload gagal: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('status', 'error')->with('message', 'Unggah foto gagal: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Gagal memproses gambar'], 500);
        }
        return redirect()->back()->with('status', 'error')->with('message', 'Gagal memproses gambar profil.');
    }

    /**
     * Delete profile photo.
     */
    public function deletePhoto(Request $request)
    {
        $user = $request->user();
        
        $model = null;
        if ($user->relationLoaded('siswa') || $user->siswa) {
            $model = $user->siswa;
            $field = 'image';
        } elseif ($user->relationLoaded('guru') || $user->guru) {
            $model = $user->guru;
            $field = 'image';
        } elseif ($user->relationLoaded('adminProfile') || $user->adminProfile) {
            $model = $user->adminProfile;
            $field = 'image';
        } else {
            return $request->wantsJson() 
                ? response()->json(['success' => false, 'message' => 'Tidak ada profil terkait.'], 422)
                : redirect()->back()->with('status', 'error')->with('message', 'Tidak ada profil terkait.');
        }

        if (!empty($model->{$field})) {
            $old = public_path('assets/profile/' . $model->{$field});
            if (File::exists($old)) {
                @unlink($old);
            }
            $model->{$field} = null;
            $model->save();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Foto profil berhasil dihapus.']);
        }

        return redirect()->back()->with('status', 'success')->with('message', 'Foto profil berhasil dihapus.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed|min:5',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('status', 'success')->with('message', 'Password berhasil diperbarui.');
    }

    /* -------------------- image helper -------------------- */

    protected function saveUploadedImage(\Illuminate\Http\UploadedFile $file): ?string
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
            \Illuminate\Support\Facades\Log::warning("compressImageIfNeeded failed for profile upload: " . $e->getMessage());
        }

        // fallback: move original
        try {
            $file->move(public_path('assets/profile'), $filename);
            \Illuminate\Support\Facades\Log::warning("Image compression fallback used for profile upload: {$filename}");
            return $filename;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed moving uploaded image after compression fallback: " . $e->getMessage());
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
