<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\Penggajian;
use App\Models\PengajuanCuti;
use App\Models\AbsensiKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Get calendar events for a specific month
     */
    public function getEvents(Request $request)
    {
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);
        $userId = Auth::id();
        $userRole = Auth::user()->role;

        $events = [];

        // Get Pengumuman events (for admin/HR view)
        if (in_array($userRole, ['admin', 'hr'])) {
            $pengumumanEvents = $this->getPengumumanEvents($year, $month);
            $events = array_merge($events, $pengumumanEvents);
        }

        // Get Penggajian events (for current user)
        $penggajianEvents = $this->getPenggajianEvents($year, $month, $userId);
        $events = array_merge($events, $penggajianEvents);

        // Get Cuti events (for current user)
        $cutiEvents = $this->getCutiEvents($year, $month, $userId);
        $events = array_merge($events, $cutiEvents);

        // Get Absensi events (for current user)
        $absensiEvents = $this->getAbsensiEvents($year, $month, $userId);
        $events = array_merge($events, $absensiEvents);

        return response()->json([
            'success' => true,
            'events' => $events,
            'year' => $year,
            'month' => $month
        ]);
    }

    /**
     * Get single event detail
     */
    public function getEventDetail(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');

        $data = null;

        switch ($type) {
            case 'pengumuman':
                $data = Pengumuman::find($id);
                if ($data) {
                    $data->type = 'pengumuman';
                    $data->type_label = 'Pengumuman';
                    $data->icon = '📢';
                    $data->color = 'blue';
                }
                break;
            case 'penggajian':
                $data = Penggajian::find($id);
                if ($data) {
                    $data->type = 'penggajian';
                    $data->type_label = 'Penggajian';
                    $data->icon = '💰';
                    $data->color = 'green';
                }
                break;
            case 'cuti':
                $data = PengajuanCuti::find($id);
                if ($data) {
                    $data->type = 'cuti';
                    $data->type_label = 'Cuti';
                    $data->icon = '📋';
                    $data->color = 'yellow';
                    $data->status_badge = $data->getStatusBadgeAttribute();
                    $data->jenis_cuti_label = $data->getJenisCutiLabelAttribute();
                }
                break;
            case 'absensi':
                $data = AbsensiKaryawan::with('karyawan')->find($id);
                if ($data) {
                    $data->type = 'absensi';
                    $data->type_label = 'Absensi';
                    $data->icon = '⏰';
                    $data->color = 'purple';
                }
                break;
        }

        if (!$data) {
            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get Pengumuman events
     */
    private function getPengumumanEvents($year, $month)
    {
        $events = [];
        $user = Auth::user();

        $pengumuman = Pengumuman::where('status', true)
            ->whereYear('tanggal_terbit', $year)
            ->whereMonth('tanggal_terbit', $month)
            ->get();

        foreach ($pengumuman as $item) {
            // Check target role
            $targetRole = [];
            $targetDept = [];

            if ($item->target_role) {
                $targetRole = is_array($item->target_role) ? $item->target_role : json_decode($item->target_role, true) ?? [];
            }
            if ($item->target_departemen) {
                $targetDept = is_array($item->target_departemen) ? $item->target_departemen : json_decode($item->target_departemen, true) ?? [];
            }

            $roleMatch = empty($targetRole) || in_array($user->role, $targetRole);
            $deptMatch = empty($targetDept) || in_array($user->departemen, $targetDept);

            if ($roleMatch && $deptMatch) {
                $events[] = [
                    'id' => $item->id,
                    'type' => 'pengumuman',
                    'title' => $item->judul,
                    'date' => $item->tanggal_terbit->format('Y-m-d'),
                    'start_date' => $item->tanggal_terbit->format('Y-m-d'),
                    'end_date' => $item->tanggal_berlaku_hingga ? $item->tanggal_berlaku_hingga->format('Y-m-d') : $item->tanggal_terbit->format('Y-m-d'),
                    'description' => strip_tags(substr($item->konten, 0, 100)),
                    'icon' => '📢',
                    'color' => 'blue',
                    'status' => 'published'
                ];
            }
        }

        return $events;
    }

    /**
     * Get Penggajian events
     */
    private function getPenggajianEvents($year, $month, $userId)
    {
        $events = [];

        $penggajian = Penggajian::where('karyawan_id', $userId)
            ->where('tahun', $year)
            ->where('bulan', $month)
            ->whereIn('status', ['approved', 'paid'])
            ->get();

        foreach ($penggajian as $item) {
            $eventDate = $item->tanggal_pembayaran ?? Carbon::create($year, $month, 15);

            $events[] = [
                'id' => $item->id,
                'type' => 'penggajian',
                'title' => 'Salary - ' . $item->getBulanTextAttribute() . ' ' . $item->tahun,
                'date' => $eventDate->format('Y-m-d'),
                'start_date' => $eventDate->format('Y-m-d'),
                'end_date' => $eventDate->format('Y-m-d'),
                'description' => 'Status: ' . ucfirst($item->status) . ' | Net Salary: Rp ' . number_format($item->net_salary, 0, ',', '.'),
                'icon' => '💰',
                'color' => 'green',
                'status' => $item->status,
                'amount' => $item->net_salary
            ];
        }

        return $events;
    }

    /**
     * Get Cuti events
     */
    private function getCutiEvents($year, $month, $userId)
    {
        $events = [];

        $cuti = PengajuanCuti::where('karyawan_id', $userId)
            ->where(function($q) use ($year, $month) {
                $q->whereYear('tanggal_mulai', $year)
                  ->whereMonth('tanggal_mulai', $month)
                  ->orWhere(function($q2) use ($year, $month) {
                      $q2->whereYear('tanggal_selesai', $year)
                        ->whereMonth('tanggal_selesai', $month);
                  });
            })
            ->get();

        foreach ($cuti as $item) {
            $currentDate = $item->tanggal_mulai->copy();
            $endDate = $item->tanggal_selesai;

            $color = 'yellow';
            $icon = '📋';

            if (in_array($item->status, ['approved', 'disetujui'])) {
                $color = 'green';
                $icon = '✅';
            } elseif (in_array($item->status, ['rejected', 'ditolak'])) {
                $color = 'red';
                $icon = '❌';
            }

            $events[] = [
                'id' => $item->id,
                'type' => 'cuti',
                'title' => $item->getJenisCutiLabelAttribute(),
                'date' => $item->tanggal_mulai->format('Y-m-d'),
                'start_date' => $item->tanggal_mulai->format('Y-m-d'),
                'end_date' => $item->tanggal_selesai->format('Y-m-d'),
                'description' => $item->alasan ?: 'Leave request',
                'icon' => $icon,
                'color' => $color,
                'status' => $item->status,
                'total_days' => $item->total_hari
            ];
        }

        return $events;
    }

    /**
     * Get Absensi events
     */
    private function getAbsensiEvents($year, $month, $userId)
    {
        $events = [];

        $absensi = AbsensiKaryawan::where('karyawan_id', $userId)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('is_change_day', false)
            ->get();

        foreach ($absensi as $item) {
            $statusColor = 'gray';
            $statusIcon = '⏰';
            $statusText = ucfirst($item->status_kehadiran);

            switch ($item->status_kehadiran) {
                case 'present':
                    $statusColor = 'green';
                    $statusIcon = '✅';
                    $statusText = 'Present';
                    break;
                case 'permit':
                    $statusColor = 'blue';
                    $statusIcon = '📝';
                    $statusText = 'Permit';
                    break;
                case 'sick':
                    $statusColor = 'purple';
                    $statusIcon = '🤒';
                    $statusText = 'Sick';
                    break;
                case 'absent':
                    $statusColor = 'red';
                    $statusIcon = '❌';
                    $statusText = 'Absent';
                    break;
            }

            $events[] = [
                'id' => $item->id,
                'type' => 'absensi',
                'title' => 'Attendance: ' . $statusText,
                'date' => $item->tanggal->format('Y-m-d'),
                'start_date' => $item->tanggal->format('Y-m-d'),
                'end_date' => $item->tanggal->format('Y-m-d'),
                'description' => 'Check In: ' . ($item->jam_masuk ? Carbon::parse($item->jam_masuk)->format('H:i') : '-') . ' | Check Out: ' . ($item->jam_pulang ? Carbon::parse($item->jam_pulang)->format('H:i') : '-'),
                'icon' => $statusIcon,
                'color' => $statusColor,
                'status' => $item->status_kehadiran,
                'check_in' => $item->jam_masuk,
                'check_out' => $item->jam_pulang
            ];
        }

        return $events;
    }
}
