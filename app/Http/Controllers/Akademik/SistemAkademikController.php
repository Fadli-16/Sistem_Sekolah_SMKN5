<?php

namespace App\Http\Controllers\Akademik;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;

class SistemAkademikController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Berita::query();

        // SEARCH
        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%')
                ->orWhere('isi', 'like', '%' . $request->search . '%');
        }

        // FILTER KATEGORI / ORDER
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'terbaru':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'terlama':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'informasi':
                case 'prestasi':
                case 'pemberitahuan':
                    $query->where('kategori', $request->filter);
                    break;
            }
        } else {
            $query->latest();
        }

        // FILTER TANGGAL
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to . ' 23:59:59'
            ]);
        }

        $berita = $query->latest()->paginate(4)->appends($request->query());

        return view('sistem_akademik.dashboard', compact('berita'));
    }
}
