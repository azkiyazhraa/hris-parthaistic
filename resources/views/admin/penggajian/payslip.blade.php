<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        @page {
            margin: 32px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            color: #111827;
        }

        .container {
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #1e3a8a;
            letter-spacing: .5px;
        }

        .header p {
            margin: 2px 0 0;
            font-size: 10px;
            color: #6b7280;
        }

        .employee-info {
            background: #f3f4f6;
            padding: 8px 10px;
            margin-bottom: 10px;
        }

        .employee-info table,
        .employee-info td {
            border: none !important;
        }

        .employee-info td {
            padding: 2px 0;
            font-size: 10px;
        }

        .section {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .section h3 {
            background: #1e3a8a;
            color: #fff;
            font-size: 10px;
            margin: 0 0 4px 0;
            padding: 5px 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 4px 6px;
            font-size: 9px;
            vertical-align: middle;
        }

        th {
            background: #f9fafb;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .total {
            background: #f3f4f6;
            font-weight: bold;
        }

        .net-salary td {
            font-size: 11px;
            font-weight: bold;
            padding: 6px;
        }

        .footer {
            margin-top: 8px;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="header">
            <h1>SLIP GAJI</h1>
            <p>Periode: {{ $penggajian->bulan_text }} {{ $penggajian->tahun }}</p>
        </div>

        <div class="employee-info">
            <table>
                <tr>
                    <td width="28%"><strong>Employee Name</strong></td>
                    <td>: {{ $penggajian->nama_karyawan }}</td>
                </tr>
                <tr>
                    <td><strong>NIP</strong></td>
                    <td>: {{ $karyawan->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Role</strong></td>
                    <td>: {{ ucfirst($karyawan->role ?? '-') }}</td>
                </tr>
                <tr>
                    <td><strong>Status</strong></td>
                    <td>: {{ strtoupper($penggajian->status) }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>Earnings</h3>

            <table>
                <colgroup>
                    <col style="width:62%">
                    <col style="width:38%">
                </colgroup>

                <tr>
                    <th>Component</th>
                    <th class="text-right">Jumlah</th>
                </tr>

                <tr>
                    <td>Basic Salary</td>
                    <td class="text-right">Rp {{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Transport Allowance</td>
                    <td class="text-right">Rp {{ number_format($penggajian->transport_allowance, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Meal Allowance</td>
                    <td class="text-right">Rp {{ number_format($penggajian->meal_allowance, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Internet Allowance</td>
                    <td class="text-right">Rp {{ number_format($penggajian->internet_allowance, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Position Allowance</td>
                    <td class="text-right">Rp {{ number_format($penggajian->position_allowance, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Incentive</td>
                    <td class="text-right">Rp {{ number_format($penggajian->incentive, 0, ',', '.') }}</td>
                </tr>

                <tr class="total">
                    <td>Total Earnings</td>
                    <td class="text-right">Rp {{ number_format($penggajian->total_earnings, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>Deductions</h3>

            <table>
                <colgroup>
                    <col style="width:62%">
                    <col style="width:38%">
                </colgroup>

                <tr>
                    <th>Component</th>
                    <th class="text-right">Jumlah</th>
                </tr>

                <tr>
                    <td>Tax (PPh 21)</td>
                    <td class="text-right">Rp {{ number_format($penggajian->tax, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>BPJS Health</td>
                    <td class="text-right">Rp {{ number_format($penggajian->bpjs_kesehatan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>BPJS Employment</td>
                    <td class="text-right">Rp {{ number_format($penggajian->bpjs_ketenagakerjaan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Late/Absent Deduction</td>
                    <td class="text-right">Rp {{ number_format($penggajian->late_absent_deduction, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Loan Deduction</td>
                    <td class="text-right">Rp {{ number_format($penggajian->loan_deduction, 0, ',', '.') }}</td>
                </tr>

                <tr class="total">
                    <td>Total Deductions</td>
                    <td class="text-right">Rp {{ number_format($penggajian->total_deductions, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <table>
                <colgroup>
                    <col style="width:62%">
                    <col style="width:38%">
                </colgroup>

                <tr class="total net-salary">
                    <td>NET SALARY</td>
                    <td class="text-right">Rp {{ number_format($penggajian->net_salary, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        @if ($penggajian->catatan)
            <div class="section">
                <h3>Catatan</h3>
                <p>{{ $penggajian->catatan }}</p>
            </div>
        @endif

        <div class="footer">
            Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}
        </div>

    </div>

</body>

</html>
