<?php

namespace App\Http\Controllers;

use App\Models\AbsensiKaryawan;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BreakController extends Controller
{
    public function start($absensiId)
    {
        $karyawanId = Auth::id();
        
        // Cek apakah sudah ada break yang aktif hari ini
        $activeBreak = BreakTime::where('karyawan_id', $karyawanId)
            ->whereDate('created_at', now()->toDateString())
            ->whereNotNull('break_start')
            ->whereNull('break_end')
            ->first();
            
        if ($activeBreak) {
            return redirect()->back()->with('error', 'You are already on a break!');
        }
        
        // Cek apakah absensi milik user yang login
        $absensi = AbsensiKaryawan::where('id', $absensiId)
            ->where('karyawan_id', $karyawanId)
            ->first();
            
        if (!$absensi) {
            return redirect()->back()->with('error', 'Attendance record not found!');
        }
        
        if ($absensi->jam_pulang) {
            return redirect()->back()->with('error', 'You have already checked out!');
        }
        
        // Create break
        BreakTime::create([
            'absensi_id' => $absensiId,
            'karyawan_id' => $karyawanId,
            'break_start' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Break started - Working hours paused');
    }
    
    public function end($absensiId)
    {
        $karyawanId = Auth::id();
        
        // Cari break yang aktif
        $break = BreakTime::where('absensi_id', $absensiId)
            ->where('karyawan_id', $karyawanId)
            ->whereDate('created_at', now()->toDateString())
            ->whereNotNull('break_start')
            ->whereNull('break_end')
            ->first();
            
        if (!$break) {
            return redirect()->back()->with('error', 'No active break found!');
        }
        
        $breakEnd = now();
        
        // Update break end
        $break->update([
            'break_end' => $breakEnd,
        ]);
        
        // Hitung dan update total jam kerja di absensi
        $absensi = AbsensiKaryawan::find($absensiId);
        if ($absensi) {
            $totalJamKerja = $absensi->calculateTotalWorkingHours();
            $absensi->update([
                'total_jam_kerja' => $totalJamKerja
            ]);
        }
        
        return redirect()->back()->with('success', 'Break ended - Working hours resumed');
    }
}