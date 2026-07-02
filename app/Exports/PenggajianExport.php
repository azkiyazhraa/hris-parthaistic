<?php

namespace App\Exports;

use App\Models\Penggajian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenggajianExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, ShouldAutoSize
{
    protected $bulan;
    protected $tahun;
    protected $status;

    public function __construct($bulan = null, $tahun = null, $status = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Penggajian::with('karyawan');

        if ($this->bulan && $this->tahun) {
            $query->where('bulan', $this->bulan)->where('tahun', $this->tahun);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Karyawan',
            'NIP',
            'Email',
            'Role',
            'Bulan',
            'Tahun',
            'Status',
            'Gaji Pokok',
            'Transport',
            'Meal',
            'Internet',
            'Position',
            'Incentive',
            'Total Earnings',
            'Tax',
            'BPJS Kesehatan',
            'BPJS Ketenagakerjaan',
            'Late/Absent',
            'Loan',
            'Total Deductions',
            'Net Salary',
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->nama_karyawan,
            $item->karyawan->nip ?? '-',
            $item->karyawan->email ?? '-',
            $item->karyawan->role ?? '-',
            $this->getBulanText($item->bulan),
            $item->tahun,
            $item->status,
            $this->formatNominal($item->gaji_pokok),
            $this->formatNominal($item->transport_allowance),
            $this->formatNominal($item->meal_allowance),
            $this->formatNominal($item->internet_allowance),
            $this->formatNominal($item->position_allowance),
            $this->formatNominal($item->incentive),
            $this->formatNominal($item->total_earnings),
            $this->formatNominal($item->tax),
            $this->formatNominal($item->bpjs_kesehatan),
            $this->formatNominal($item->bpjs_ketenagakerjaan),
            $this->formatNominal($item->late_absent_deduction),
            $this->formatNominal($item->loan_deduction),
            $this->formatNominal($item->total_deductions),
            $this->formatNominal($item->net_salary),
        ];
    }

    private function formatNominal($value)
    {
        if (is_null($value) || $value == 0) {
            return '-';
        }
        return (float) $value;
    }

    // Kolom I sampai V adalah kolom nominal (index 9-22 sesuai urutan heading)
    public function columnFormats(): array
    {
        $format = '#,##0';

        return [
            'I' => $format, // Gaji Pokok
            'J' => $format, // Transport
            'K' => $format, // Meal
            'L' => $format, // Internet
            'M' => $format, // Position
            'N' => $format, // Incentive
            'O' => $format, // Total Earnings
            'P' => $format, // Tax
            'Q' => $format, // BPJS Kesehatan
            'R' => $format, // BPJS Ketenagakerjaan
            'S' => $format, // Late/Absent
            'T' => $format, // Loan
            'U' => $format, // Total Deductions
            'V' => $format, // Net Salary
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('I2:V' . $sheet->getHighestRow())
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function getBulanText($bulan)
    {
        $bulanText = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        return $bulanText[$bulan] ?? $bulan;
    }
}
