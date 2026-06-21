<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class BeritaController extends Controller
{
    // MAX target file size after compression (bytes)
    protected int $maxImageBytes = 500 * 1024; // 500 KB

    /**
     * Display a listing of the resource with search, filter and pagination.
     */
    public function index(Request $request)
    {
        $title = 'Berita';
        $header = 'Daftar Berita';
        $query = Berita::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('isi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('filter')) {
            if (in_array($request->filter, ['informasi', 'prestasi', 'pemberitahuan'])) {
                $query->where('kategori', $request->filter);
            }
        }

        if ($request->filled('status')) {
            if (in_array($request->status, ['publish', 'draft'])) {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('filter') && $request->filter === 'terlama') {
            $query->oldest();
        } else {
            $query->latest();
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59',
            ]);
        }

        $berita = $query->get();

        return view('sistem_akademik.berita.index', compact('berita', 'title', 'header'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Berita';
        $header = 'Tambah Berita Terbaru';
        $berita = null;
        return view('sistem_akademik.berita.createOrEdit', compact('title', 'header', 'berita'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar|max:8192',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|string|in:informasi,prestasi,pemberitahuan',
            'status' => 'required|in:publish,draft',
        ]);

        $foto = null;
        $fileName = null;

        if ($request->hasFile('foto')) {
            try {
                $foto = $this->saveUploadedImage($request->file('foto'));
            } catch (\Throwable $e) {
                Log::warning('Berita image upload/store failed: ' . $e->getMessage());
            }
        }

        if ($request->hasFile('file')) {
            $f2 = $request->file('file');
            $fileName = time() . '_file_' . Str::slug(pathinfo($f2->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $f2->getClientOriginalExtension();
            $f2->move(public_path('file'), $fileName);
        }

        $berita = new Berita();
        $berita->user_id = Auth::id();
        $berita->judul = $request->judul;
        $berita->isi = $request->isi;
        $berita->kategori = $request->kategori;
        $berita->status = $request->status;
        $berita->foto = $foto;
        $berita->file = $fileName;
        $berita->save();

        return redirect()->route('sistem_akademik.berita.index')
            ->with('status', 'success')
            ->with('message', 'Data berhasil ditambah');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $title = 'Berita';
        $header = 'Detail Berita';
        $berita = Berita::findOrFail($id);

        // Catat bahwa user telah membaca berita ini
        if (Auth::check()) {
            $berita->readers()->syncWithoutDetaching([Auth::id()]);
        }

        return view('sistem_akademik.berita.show', compact('title', 'header', 'berita'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $title = 'Berita';
        $header = 'Edit Berita Terbaru';
        $berita = Berita::findOrFail($id);
        return view('sistem_akademik.berita.createOrEdit', compact('title', 'header', 'berita'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar|max:8192',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|string|in:informasi,prestasi,pemberitahuan',
            'status' => 'required|in:publish,draft',
            'remove_file' => 'nullable|in:1',
        ]);

        $berita = Berita::findOrFail($id);

        // Handle new foto upload
        if ($request->hasFile('foto')) {
            try {
                $filename = $this->saveUploadedImage($request->file('foto'));

                if ($filename) {
                    if ($berita->foto && file_exists(public_path('assets/berita/' . $berita->foto))) {
                        @unlink(public_path('assets/berita/' . $berita->foto));
                    }
                    $berita->foto = $filename;
                }
            } catch (\Throwable $e) {
                Log::warning('Berita image upload/update failed: ' . $e->getMessage());
            }
        }

        // Handle file upload / removal
        if ($request->hasFile('file')) {
            $f2 = $request->file('file');
            $fileName = time() . '_file_' . Str::slug(pathinfo($f2->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $f2->getClientOriginalExtension();
            $f2->move(public_path('file'), $fileName);

            if ($berita->file && file_exists(public_path('file/' . $berita->file))) {
                @unlink(public_path('file/' . $berita->file));
            }

            $berita->file = $fileName;
        } elseif ($request->filled('remove_file') && $request->remove_file == '1') {
            // user requests to remove existing file
            if ($berita->file && file_exists(public_path('file/' . $berita->file))) {
                @unlink(public_path('file/' . $berita->file));
            }
            $berita->file = null;
        }

        $berita->judul = $request->judul;
        $berita->isi = $request->isi;
        $berita->kategori = $request->kategori;
        $berita->status = $request->status;
        $berita->save();

        return redirect()->route('sistem_akademik.berita.index')
            ->with('status', 'success')
            ->with('message', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);

        if ($berita->foto && file_exists(public_path('assets/berita/' . $berita->foto))) {
            @unlink(public_path('assets/berita/' . $berita->foto));
        }
        if ($berita->file && file_exists(public_path('file/' . $berita->file))) {
            @unlink(public_path('file/' . $berita->file));
        }

        $berita->delete();

        return redirect()->route('sistem_akademik.berita.index')
            ->with('status', 'success')
            ->with('message', 'Data berhasil dihapus');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih']);
        }

        try {
            $beritas = Berita::whereIn('id', $ids)->get();
            foreach ($beritas as $berita) {
                if ($berita->foto && file_exists(public_path('assets/berita/' . $berita->foto))) {
                    @unlink(public_path('assets/berita/' . $berita->foto));
                }
                if ($berita->file && file_exists(public_path('file/' . $berita->file))) {
                    @unlink(public_path('file/' . $berita->file));
                }
                $berita->delete();
            }
            return response()->json(['success' => true, 'message' => count($ids) . ' data berita berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    protected function saveUploadedImage(UploadedFile $file): ?string
    {
        $destDir = public_path('assets/berita');
        if (! File::exists($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $orig = $file->getClientOriginalName();
        $ext = strtolower($file->getClientOriginalExtension()) ?: 'jpg';
        $base = pathinfo($orig, PATHINFO_FILENAME);
        $base = Str::slug(substr($base, 0, 50), '_');
        $filename = time() . '_' . $base . '.' . $ext;
        $tmpPath = $file->getRealPath();
        $destPath = public_path('assets/berita/' . $filename);

        if ($file->getSize() !== null && $file->getSize() <= $this->maxImageBytes) {
            $file->move(public_path('assets/berita'), $filename);
            return $filename;
        }

        try {
            $ok = $this->compressImageIfNeeded($tmpPath, $destPath, $ext, $this->maxImageBytes);
            if ($ok && File::exists($destPath)) {
                return $filename;
            }
        } catch (\Throwable $e) {
            Log::warning("compressImageIfNeeded failed: " . $e->getMessage());
        }

        try {
            $file->move(public_path('assets/berita'), $filename);
            Log::warning("Image compression fallback used for uploaded file: {$filename}");
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
    public function toggleStatus($id)
    {
        $berita = Berita::findOrFail($id);
        $berita->status = $berita->status === 'publish' ? 'draft' : 'publish';
        $berita->save();

        return response()->json([
            'success' => true,
            'new_status' => $berita->status,
            'message' => 'Status berita berhasil diubah menjadi ' . ucfirst($berita->status)
        ]);
    }
}
