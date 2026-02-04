<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Models\Labor;
use Illuminate\Http\Request;

class InventarisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Inventaris Laboratorium';
        $header = 'Daftar Inventaris';
        
        $laborList = Labor::orderBy('nama_labor', 'asc')->get();
        $selectedLabor = request('labor', '');
        
        $inventaris = Inventaris::when($selectedLabor, function($query) use ($selectedLabor) {
                return $query->where('lokasi', $selectedLabor);
            })
            ->orderBy('nama_inventaris', 'asc')
            ->get();
        
        return view('siswa.main.inventaris.index', compact('title', 'header', 'inventaris', 'laborList', 'selectedLabor'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $title = 'Detail Inventaris';
        $header = 'Detail Inventaris';
        
        $item = Inventaris::findOrFail($id);
        
        return view('siswa.main.inventaris.show', compact('title', 'header', 'item'));
    }
}