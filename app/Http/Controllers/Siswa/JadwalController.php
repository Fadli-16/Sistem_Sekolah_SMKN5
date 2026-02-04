<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Laboratorium;
use App\Models\Labor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Jadwal Laboratorium';
        $header = 'Jadwal Penggunaan Laboratorium';
        
        $laborList = Labor::orderBy('nama_labor', 'asc')->get();
        $selectedLabor = $request->input('labor', '');
        $selectedDate = $request->input('date', '');
        
        // Build query with filters
        $query = Laboratorium::query();
        
        // Apply laboratory filter
        if (!empty($selectedLabor)) {
            $query->where('labor', $selectedLabor);
        }
        
        // Apply date filter
        if (!empty($selectedDate)) {
            $query->whereDate('start', Carbon::parse($selectedDate));
        } else {
            // If no date filter, show from yesterday onwards
            $query->whereDate('start', '>=', Carbon::now()->subDays(1));
        }
        
        // Get schedules
        $jadwal = $query->orderBy('start', 'asc')->get();
        
        return view('siswa.main.jadwal.index', compact(
            'title', 
            'header', 
            'jadwal', 
            'laborList', 
            'selectedLabor',
            'selectedDate'
        ));
    }
}