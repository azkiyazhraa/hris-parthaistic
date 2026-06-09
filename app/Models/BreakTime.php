<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BreakTime extends Model
{
    protected $fillable = [
        'absensi_id',
        'karyawan_id',
        'break_start',
        'break_end',
    ];

    protected $casts = [
        'break_start' => 'datetime',
        'break_end' => 'datetime',
    ];

    public function absensi()
    {
        return $this->belongsTo(AbsensiKaryawan::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Hitung durasi break dalam menit
     */
    public function getDurationInMinutesAttribute()
    {
        if (!$this->break_start) {
            return 0;
        }

        $end = $this->break_end ?? Carbon::now();
        return $this->break_start->diffInMinutes($end);
    }

    /**
     * Hitung durasi break dalam format jam:menit:detik
     */
    public function getDurationFormattedAttribute()
    {
        $minutes = $this->getDurationInMinutesAttribute();
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        return sprintf('%02d:%02d:00', $hours, $remainingMinutes);
    }
}