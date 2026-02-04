<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Labor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class LaborCrudController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Kelola Laboratorium';
        $header = 'Daftar Laboratorium';
        
        $labor = Labor::orderBy('nama_labor', 'asc')->get();
        
        return view('admin.main.labor.index', compact('title', 'header', 'labor'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = 'Tambah Laboratorium';
        $header = 'Tambah Laboratorium Baru';
        
        return view('admin.main.labor.create', compact('title', 'header'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nama_labor' => 'required|string|max:255',
        'kode' => 'nullable|string|max:50|unique:labor,kode',
        'penanggung_jawab' => 'nullable|string|max:255',
        'teknisi' => 'nullable|string|max:255',
        'deskripsi' => 'nullable|string',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('status', 'error')
            ->with('title', 'Gagal')
            ->with('message', 'Validasi gagal, mohon periksa form kembali');
    }

    $data = $request->all();

    if ($request->hasFile('foto')) {
        $file = $request->file('foto');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/labor_foto', $filename);
        $data['foto'] = $filename;
    }

    Labor::create($data);

    return redirect()->route('admin.labor.index')
        ->with('status', 'success')
        ->with('title', 'Berhasil')
        ->with('message', 'Laboratorium berhasil ditambahkan');
}


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $title = 'Edit Laboratorium';
        $header = 'Edit Laboratorium';
        
        $labor = Labor::findOrFail($id);
        
        return view('admin.main.labor.edit', compact('title', 'header', 'labor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
{
    $labor = Labor::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'nama_labor' => 'required|string|max:255',
        'kode' => 'nullable|string|max:50|unique:labor,kode,' . $labor->id,
        'penanggung_jawab' => 'nullable|string|max:255',
        'teknisi' => 'nullable|string|max:255',
        'deskripsi' => 'nullable|string',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput()
            ->with('status', 'error')
            ->with('title', 'Gagal')
            ->with('message', 'Validasi gagal, mohon periksa form kembali');
    }

    $data = $request->all();

    if ($request->hasFile('foto')) {
        if ($labor->foto && Storage::exists('public/labor_foto/' . $labor->foto)) {
            Storage::delete('public/labor_foto/' . $labor->foto);
        }

        $file = $request->file('foto');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/labor_foto', $filename);
        $data['foto'] = $filename;
    }

    $labor->update($data);

    return redirect()->route('admin.labor.index')
        ->with('status', 'success')
        ->with('title', 'Berhasil')
        ->with('message', 'Laboratorium berhasil diperbarui');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $labor = Labor::findOrFail($id);
        
        // Check if laboratory is in use before deleting
        // Note: Add your own logic if needed to check relationships
        
        $labor->delete();
        
        return redirect()->route('admin.labor.index')
            ->with('status', 'success')
            ->with('title', 'Berhasil')
            ->with('message', 'Laboratorium berhasil dihapus');
    }
}