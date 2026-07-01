<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Performa extends Model
{
    use HasFactory;

    protected $table = 'performas';

    protected $fillable = [
        'karyawan_id',
        'nama_karyawan',
        'departemen',
        'position',
        'email',
        'phone',
        'join_date',
        'bulan',
        'tahun',
        'attendance_rate',
        'task_done',
        'task_target',
        'task_score',
        'quality',
        'productivity',
        'teamwork',
        'discipline',
        'kpi_score',
        'performance_score',
        'catatan',
        'quarter',
    ];

    protected $casts = [
        'join_date' => 'date',
        'attendance_rate' => 'integer',
        'task_done' => 'integer',
        'task_target' => 'integer',
        'task_score' => 'integer',
        'quality' => 'integer',
        'productivity' => 'integer',
        'teamwork' => 'integer',
        'discipline' => 'integer',
        'kpi_score' => 'integer',
        'performance_score' => 'integer',
    ];

    /**
     * Hitung Attendance Rate berdasarkan data AbsensiKaryawan
     * DIPERBAIKI: Mempertimbangkan join_date karyawan
     */
    public static function calculateAttendanceRateFromAbsensi($karyawanId, $bulan, $tahun)
    {
        $karyawan = Karyawan::find($karyawanId);
        
        // Ambil semua data absensi dalam bulan tersebut
        $absensi = AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Hitung total hari kerja yang relevan (mempertimbangkan join_date)
        $totalWorkingDays = self::getRelevantWorkingDays($karyawan, $bulan, $tahun);
        
        if ($totalWorkingDays == 0) {
            return 0; // Return 0 jika tidak ada hari kerja yang relevan
        }

        $hadir = $absensi->whereIn('status_kehadiran', ['present', 'pending'])->count();
        $izin  = $absensi->where('status_kehadiran', 'change_day')->count();
        $sakit = $absensi->where('status_kehadiran', 'leave')->count();

        $attendanceRate = round((($hadir + $izin + $sakit) / $totalWorkingDays) * 100);
        
        // Maksimal 100%
        return min($attendanceRate, 100);
    }

    /**
     * Hitung total hari kerja yang relevan untuk karyawan
     * Mempertimbangkan join_date dan akhir bulan
     */
    public static function getRelevantWorkingDays($karyawan, $bulan, $tahun)
    {
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();
        
        // Jika karyawan punya join_date dan join_date setelah awal bulan
        if ($karyawan && $karyawan->tanggal_bergabung) {
            $joinDate = Carbon::parse($karyawan->tanggal_bergabung);
            
            // Jika join_date di bulan yang sama, mulai hitung dari join_date
            if ($joinDate->year == $tahun && $joinDate->month == $bulan) {
                $startDate = $joinDate->copy();
            }
            
            // Jika join_date setelah bulan yang dihitung, return 0
            if ($joinDate->gt($endDate)) {
                return 0;
            }
        }
        
        // Hari ini (untuk membatasi sampai hari ini jika bulan berjalan)
        $today = Carbon::now();
        if ($endDate->gt($today) && $startDate->lte($today)) {
            $endDate = $today->copy();
        }
        
        $workingDays = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Senin (1) - Sabtu (6)
            if ($currentDate->dayOfWeek >= Carbon::MONDAY && $currentDate->dayOfWeek <= Carbon::SATURDAY) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    /**
     * Hitung jumlah keterlambatan dalam satu bulan
     */
    public static function calculateLateCount($karyawanId, $bulan, $tahun)
    {
        $jamMasukNormal = Carbon::parse('08:00:00'); // Jam masuk normal (bisa disesuaikan)
        
        $absensi = AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'present')
            ->whereNotNull('jam_masuk')
            ->get();

        $lateCount = 0;

        foreach ($absensi as $record) {
            if ($record->jam_masuk) {
                $jamMasuk = Carbon::parse($record->jam_masuk);
                
                // Jika jam masuk lebih dari jam normal, hitung sebagai terlambat
                if ($jamMasuk->format('H:i:s') > $jamMasukNormal->format('H:i:s')) {
                    $lateCount++;
                }
            }
        }

        return $lateCount;
    }

    /**
     * Hitung jumlah alpha (tidak hadir tanpa keterangan) dalam satu bulan
     * DIPERBAIKI: Hanya menghitung dari hari yang relevan
     */
    public static function calculateAbsentCount($karyawanId, $bulan, $tahun)
    {
        return AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('is_change_day', false)
            ->where('status_kehadiran', 'absent')
            ->count();
    }

    /**
     * Hitung jumlah hari hadir dalam satu bulan
     */
    public static function calculatePresentCount($karyawanId, $bulan, $tahun)
    {
        $absensi = AbsensiKaryawan::where('karyawan_id', $karyawanId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->where('status_kehadiran', 'present')
            ->count();

        return $absensi;
    }

    /**
     * Hitung total hari kerja dalam satu bulan (Senin-Jumat)
     * Full month version - tanpa mempertimbangkan join_date
     */
    public static function getWorkingDaysInMonth($bulan, $tahun)
    {
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $workingDays = 0;
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            // Senin (1) - Sabtu (6)
            if ($currentDate->dayOfWeek >= Carbon::MONDAY && $currentDate->dayOfWeek <= Carbon::SATURDAY) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return $workingDays;
    }

    // Calculate KPI Score from Quality, Productivity, Teamwork, Discipline
    public static function calculateKPIScore($quality, $productivity, $teamwork, $discipline)
    {
        return round(($quality + $productivity + $teamwork + $discipline) / 4);
    }

    // Calculate Attendance Rate (same as KPI Score) - KEEP FOR BACKWARD COMPATIBILITY
    public static function calculateAttendanceRate($kpiScore)
    {
        return $kpiScore;
    }

    // Calculate performance score: (KPI Score × 50%) + (Task Score × 50%)
    public static function calculatePerformanceScore($kpi_score, $task_score)
    {
        return round(($kpi_score * 0.5) + ($task_score * 0.5));
    }

    // Get rating based on performance score
    public function getRatingAttribute()
    {
        if ($this->performance_score >= 90) {
            return ['label' => 'Excellent (A)', 'color' => 'green'];
        } elseif ($this->performance_score >= 75) {
            return ['label' => 'Good (B)', 'color' => 'blue'];
        } elseif ($this->performance_score >= 60) {
            return ['label' => 'Fair (C)', 'color' => 'yellow'];
        } elseif ($this->performance_score >= 50) {
            return ['label' => 'Poor (D)', 'color' => 'orange'];
        } else {
            return ['label' => 'Very Poor (E)', 'color' => 'red'];
        }
    }

    // Get bulan text
    public function getBulanTextAttribute()
    {
        $bulan = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        return $bulan[$this->bulan] ?? '-';
    }

    // Get quarter text
    public function getQuarterTextAttribute()
    {
        return match($this->quarter) {
            'Q1' => 'Januari - Maret',
            'Q2' => 'April - Juni',
            'Q3' => 'Juli - September',
            'Q4' => 'Oktober - Desember',
            default => '-',
        };
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}