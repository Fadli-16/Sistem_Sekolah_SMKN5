<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BeritaController extends Controller
{
    /**
     * Display a listing of the resource with search, filter and pagination.
     */
    public function index(Request $request)
    {
        $title = 'Berita';
        $header = 'Daftar Berita';
        $query = Berita::query()->latest();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('isi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('filter')) {

            if ($request->filter === 'terlama') {
                $query->orderBy('created_at', 'asc');
            }
            if (in_array($request->filter, ['informasi', 'prestasi', 'pemberitahuan'])) {
                $query->where('kategori', $request->filter);
            }
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59',
            ]);
        }

        $berita = $query->paginate(10)->appends($request->query());

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
        ]);

        $foto = null;
        $fileName = null;

        if ($request->hasFile('foto')) {
            $f = $request->file('foto');
            $foto = time() . '_' . Str::slug(pathinfo($f->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $f->getClientOriginalExtension();
            $f->move(public_path('assets/berita'), $foto);
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
            'remove_file' => 'nullable|in:1',
        ]);

        $berita = Berita::findOrFail($id);

        // Handle new foto upload
        if ($request->hasFile('foto')) {
            $f = $request->file('foto');
            $filename = time() . '_' . Str::slug(pathinfo($f->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $f->getClientOriginalExtension();
            $f->move(public_path('assets/berita'), $filename);

            if ($berita->foto && file_exists(public_path('assets/berita/' . $berita->foto))) {
                @unlink(public_path('assets/berita/' . $berita->foto));
            }

            $berita->foto = $filename;
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
}
