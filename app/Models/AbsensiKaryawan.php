<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AbsensiKaryawan extends Model
{
    use HasFactory;

    protected $table = 'absensi_karyawans';

    protected $fillable = [
        'karyawan_id',
        'nama_karyawan',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'lokasi_masuk',
        'lokasi_pulang',
        'total_jam_kerja',
        'status_kehadiran',
        'keterangan',
        'attachment',
        'is_change_day',
        'change_day_tanggal_awal',
        'change_day_tanggal_akhir',
        'change_day_jam_mulai',
        'change_day_jam_selesai',
        'change_day_alasan',
        'change_day_status',
        'change_day_catatan_admin',
        'change_day_disetujui_pada',
        'change_day_disetujui_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'change_day_tanggal_awal' => 'date',
        'change_day_tanggal_akhir' => 'date',
        'change_day_disetujui_pada' => 'datetime',
        'total_jam_kerja' => 'decimal:2',
        'is_change_day' => 'boolean',
    ];

    // Status constants untuk Change Day
    const CHANGE_DAY_PENDING = 'pending';
    const CHANGE_DAY_APPROVED = 'approved';
    const CHANGE_DAY_REJECTED = 'rejected';

    // Status constants for regular attendance
    const STATUS_PENDING = 'pending';
    const STATUS_PRESENT = 'present';
    const STATUS_PERMIT  = 'permit';
    const STATUS_SICK    = 'sick';
    const STATUS_ABSENT  = 'absent';

    // Keep old names as aliases for backward compatibility during transition
    const STATUS_HADIR = 'present';
    const STATUS_IZIN  = 'permit';
    const STATUS_SAKIT = 'sick';
    const STATUS_ALPHA = 'absent';
    const STATUS_MASUK = 'present';

    public function getChangeDayStatusBadgeAttribute()
    {
        return match($this->change_day_status) {
            self::CHANGE_DAY_PENDING  => '<span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs">Pending</span>',
            self::CHANGE_DAY_APPROVED => '<span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">Approved</span>',
            self::CHANGE_DAY_REJECTED => '<span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs">Rejected</span>',
            default => '<span class="bg-gray-200 text-gray-800 py-1 px-3 rounded-full text-xs">' . ucfirst($this->change_day_status ?? '') . '</span>',
        };
    }

    public function getStatusKehadiranBadgeAttribute()
    {
        $colors = [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_PRESENT => 'green',
            self::STATUS_PERMIT  => 'blue',
            self::STATUS_SICK    => 'purple',
            self::STATUS_ABSENT  => 'red',
        ];
        $labels = [
            self::STATUS_PENDING => 'PENDING',
            self::STATUS_PRESENT => 'PRESENT',
            self::STATUS_PERMIT  => 'PERMIT',
            self::STATUS_SICK    => 'SICK',
            self::STATUS_ABSENT  => 'ABSENT',
        ];
        $color = $colors[$this->status_kehadiran] ?? 'gray';
        $text  = $labels[$this->status_kehadiran] ?? strtoupper($this->status_kehadiran);

        return "<span class='bg-{$color}-200 text-{$color}-800 py-1 px-3 rounded-full text-xs'>{$text}</span>";
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(Karyawan::class, 'change_day_disetujui_oleh');
    }

    /**
     * Hitung total jam kerja dengan mempertimbangkan break
     */
    public function calculateTotalWorkingHours()
    {
        if (!$this->jam_masuk) {
            return 0;
        }

        $start = Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->jam_masuk);
        
        // Jika sudah checkout
        if ($this->jam_pulang) {
            $end = Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->jam_pulang);
            
            // Handle jika checkout melewati tengah malam
            if ($end < $start) {
                $end->addDay();
            }
        } else {
            // Jika belum checkout, gunakan waktu sekarang
            $end = Carbon::now();
        }

        // Total menit bekerja
        $totalMinutes = $start->diffInMinutes($end);

        // Kurangi dengan total durasi break yang sudah selesai
        $totalBreakMinutes = $this->breaks()
            ->whereNotNull('break_start')
            ->whereNotNull('break_end')
            ->get()
            ->sum(function ($break) {
                return $break->break_start->diffInMinutes($break->break_end);
            });

        $workingMinutes = max(0, $totalMinutes - $totalBreakMinutes);
        
        // Konversi ke jam dengan 2 desimal
        return round($workingMinutes / 60, 2);
    }

    /**
     * Hitung working hours realtime (untuk display saat sedang bekerja)
     * Memperhitungkan break yang sedang berlangsung
     */
    public function getCurrentWorkingHours()
    {
        if (!$this->jam_masuk) {
            return '00:00:00';
        }

        // Jika sudah pulang, gunakan total jam kerja yang tersimpan
        if ($this->jam_pulang) {
            $hours = floor($this->total_jam_kerja ?? 0);
            $minutes = round(($this->total_jam_kerja - $hours) * 60);
            return sprintf('%02d:%02d:00', $hours, $minutes);
        }

        $start = Carbon::parse($this->tanggal->format('Y-m-d') . ' ' . $this->jam_masuk);
        $now = Carbon::now();

        // Total menit dari jam masuk sampai sekarang
        $totalMinutes = $start->diffInMinutes($now);

        // Hitung total durasi break (termasuk yang sedang berlangsung, kita anggap belum selesai)
        $breaks = $this->breaks()->whereNotNull('break_start')->get();
        
        $totalBreakMinutes = 0;
        foreach ($breaks as $break) {
            if ($break->break_end) {
                // Break sudah selesai
                $totalBreakMinutes += $break->break_start->diffInMinutes($break->break_end);
            } else {
                // Break sedang berlangsung, hitung sampai sekarang
                $totalBreakMinutes += $break->break_start->diffInMinutes($now);
            }
        }

        $workingMinutes = max(0, $totalMinutes - $totalBreakMinutes);
        
        $hours = floor($workingMinutes / 60);
        $minutes = $workingMinutes % 60;
        $seconds = 0;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    /**
     * Format working hours untuk display
     */
    public function getFormattedWorkingHoursAttribute()
    {
        if ($this->jam_pulang && $this->total_jam_kerja) {
            $hours = floor($this->total_jam_kerja);
            $minutes = round(($this->total_jam_kerja - $hours) * 60);
            return sprintf('%02d:%02d:00', $hours, $minutes);
        }
        
        return $this->getCurrentWorkingHours();
    }

    public function hitungTotalJamChangeDay()
    {
        if (!$this->is_change_day || !$this->change_day_jam_mulai || !$this->change_day_jam_selesai) {
            return 0;
        }

        try {
            $tanggalMulai = $this->change_day_tanggal_awal ? $this->change_day_tanggal_awal->format('Y-m-d') : date('Y-m-d');
            
            $jamMulaiStr = $this->change_day_jam_mulai;
            if (strpos($jamMulaiStr, ' ') !== false) {
                $jamMulaiStr = substr($jamMulaiStr, 11, 5);
            }
            
            $jamSelesaiStr = $this->change_day_jam_selesai;
            if (strpos($jamSelesaiStr, ' ') !== false) {
                $jamSelesaiStr = substr($jamSelesaiStr, 11, 5);
            }
            
            $jamMulai = Carbon::parse($tanggalMulai . ' ' . $jamMulaiStr);
            $jamSelesai = Carbon::parse($tanggalMulai . ' ' . $jamSelesaiStr);

            if ($jamSelesai < $jamMulai) {
                $jamSelesai->addDay();
            }

            $selisihMenit = $jamMulai->diffInMinutes($jamSelesai);
            return round($selisihMenit / 60, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    public function breaks()
    {
        return $this->hasMany(BreakTime::class, 'absensi_id');
    }
}