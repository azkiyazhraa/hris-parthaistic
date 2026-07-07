<!DOCTYPE html>
<html lang="en">

@php
    $logoPath = public_path('assets/image/logo.png');
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
    $footerPath = public_path('assets/image/kop-fix.png');

    if (file_exists($footerPath)) {
        $footerData = base64_encode(file_get_contents($footerPath));
        $footerType = pathinfo($footerPath, PATHINFO_EXTENSION);
    }

    $signature = public_path('assets/image/ttd-nina.png');
    $signaturePath = base64_encode(file_get_contents($signature));

    $stample = public_path('assets/image/cap-parthaistic.png');
    $stampPath = base64_encode(file_get_contents($stample));

@endphp

<head>
    <meta charset="UTF-8">
    <title>Payslip</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        @page {
            margin: 10px 25px;
        }

        body {
            position: relative;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            line-height: 1.2;
            color: #000;
            min-height: 100vh;
            margin: 0 !important;
            padding: 0 !important;
        }

        .payslip-container {
            position: relative;
            z-index: 0;
            background-color: #ffffff;
            padding: 0 5px;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .full-width-footer {
            position: fixed;
            top: -10px;
            left: -25px;
            width: calc(100% + 50px);
            height: calc(100% + 20px);
            z-index: 10;
            pointer-events: none;
            object-fit: cover;
            margin: 0;
            padding: 0;
        }

        .divider {
            border-bottom: 1.5px solid #1a1a60;
            margin: 10px 0;
        }

        .invoice-meta {
            text-align: right;
            font-size: 11px;
            color: #374151;
            margin-bottom: 4px;
        }

        .employee-info {
            line-height: 1.3;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .employee-info strong {
            font-weight: 600;
        }

        @media print {
            @page {
                margin: 10px 25px;
            }

            body {
                padding: 0 !important;
                background-color: white;
                margin: 0 !important;
            }

            .payslip-container {
                box-shadow: none;
                padding: 15px;
            }

            .full-width-footer {
                opacity: 0.08;
                top: -10px;
                left: -25px;
                width: calc(100% + 50px);
                height: calc(100% + 20px);
            }
        }

        .section h3 {
            background: #1e3a8a;
            color: white;
            padding: 8px 10px;
            font-size: 11px;
            margin: 0;
            border-radius: 4px 4px 0 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #1e40af;
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 6px;
        }

        td {
            padding: 6px;
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
        }

        .text-right {
            text-align: right;
        }

        .total {
            background: #eef2ff;
            font-weight: bold;
        }
    </style>
</head>

<body>

    @if (isset($footerData) && isset($footerType))
        <img src="data:image/{{ $footerType }};base64,{{ $footerData }}" alt="Footer" class="full-width-footer">
    @endif

    <div class="payslip-container">
        <!-- Header -->
        <table width="100%" style="border-collapse: collapse; border:none; margin-bottom: 10px; margin-top: 24px;">
            <tr>
                <td style="width: 12%; vertical-align: middle; border:none; padding-right: 5px;">
                    <img src="data:image/{{ $logoType }};base64,{{ $logoData }}" alt="Logo"
                        style="width:80px;height:80px;object-fit:cover;">
                </td>

                <td style="width: 88%; vertical-align: middle; border:none; padding-left: 5px;">
                    <div style="font-size: 24px; font-weight: bold; color: #000; margin: 0; line-height: 1.2;">
                        Parthaistic Digital Agency
                    </div>

                    <div style="font-size: 16px; color: #555; margin-top: 2px;">
                        Your Digital Creator
                    </div>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align: center; border:none; padding-top: 15px;">
                    <div style="font-size: 20px; font-weight: bold; color: #1e40af;">
                        Payslip
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider"></div>
        <div class="divider"></div>

        <!-- Meta -->
        <div class="invoice-meta">
            Period: {{ $penggajian->bulan_text }} {{ $penggajian->tahun }}
        </div>

        <!-- Client Info -->
        <div class="employee-info">
            <strong>Name:</strong> {{ $penggajian->nama_karyawan ?? '-' }}<br>
            <strong>NIP:</strong> {{ $karyawan->nip ?? '-' }}<br>
            <strong>Department:</strong> {{ $karyawan->jabatan }}<br>
            <strong>Position:</strong> {{ $karyawan->role == 'karyawan' ? 'Employee' : '-' }}<br>
        </div>

        <table width="100%" style="border:none; margin-top:10px;">
            <tr>
                <!-- EARNINGS -->
                <td width="50%" style="vertical-align:top; border:none; padding-right:8px;">
                    <h3>Earnings</h3>

                    <table>
                        <thead>
                            <tr>
                                <th>Component</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Basic Salary</td>
                                <td class="text-right">Rp {{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr>
                                <td>Transport Allowance</td>
                                <td class="text-right">Rp
                                    {{ number_format($penggajian->transport_allowance, 0, ',', '.') }}</td>
                            </tr>

                            <tr>
                                <td>Meal Allowance</td>
                                <td class="text-right">Rp {{ number_format($penggajian->meal_allowance, 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr>
                                <td>Internet Allowance</td>
                                <td class="text-right">Rp
                                    {{ number_format($penggajian->internet_allowance, 0, ',', '.') }}</td>
                            </tr>

                            <tr>
                                <td>Position Allowance</td>
                                <td class="text-right">Rp
                                    {{ number_format($penggajian->position_allowance, 0, ',', '.') }}</td>
                            </tr>

                            <tr>
                                <td>Incentive</td>
                                <td class="text-right">Rp {{ number_format($penggajian->incentive, 0, ',', '.') }}</td>
                            </tr>

                            <tr class="total">
                                <td><strong>Total Earnings</strong></td>
                                <td class="text-right">
                                    <strong>Rp {{ number_format($penggajian->total_earnings, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                <!-- DEDUCTIONS -->
                <td width="50%" style="vertical-align:top; border:none; padding-left:8px;">
                    <h3>Deductions</h3>

                    <table>
                        <thead>
                            <tr>
                                <th>Component</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Tax (PPh 21)</td>
                                <td class="text-right">Rp {{ number_format($penggajian->tax, 0, ',', '.') }}</td>
                            </tr>

                            <tr>
                                <td>BPJS Employment</td>
                                <td class="text-right">Rp
                                    {{ number_format($penggajian->bpjs_ketenagakerjaan, 0, ',', '.') }}</td>
                            </tr>

                            <tr>
                                <td>Late / Absent Deduction</td>
                                <td class="text-right">Rp
                                    {{ number_format($penggajian->late_absent_deduction, 0, ',', '.') }}</td>
                            </tr>

                            <tr>
                                <td>Loan Deduction</td>
                                <td class="text-right">Rp {{ number_format($penggajian->loan_deduction, 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr class="total">
                                <td><strong>Total Deductions</strong></td>
                                <td class="text-right">
                                    <strong>Rp {{ number_format($penggajian->total_deductions, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <div
            style="
                margin-top:15px;
                background-color:#1e40af;
                color:white;
                padding:12px 16px;
                border-radius:6px;
                margin-bottom:15px;
            ">
            <table style="border:none; width:100%;">
                <tr>
                    <td style="border:none; font-size:14px; font-weight:bold;">
                        NET SALARY
                    </td>

                    <td
                        style="
                            border:none;
                            text-align:right;
                            font-size:18px;
                            font-weight:bold;
                        ">
                        Rp {{ number_format($penggajian->net_salary, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- WORK ATTENDANCE --}}
        @php
            $attendancePct = $totalHariKerja > 0
                ? round(($totalMasuk / $totalHariKerja) * 100)
                : 0;
        @endphp
        <div style="margin-top:10px; margin-bottom:15px; padding:10px 16px; background-color:#f0f9ff; border:1px solid #bae6fd; border-radius:6px;">
            <table style="border:none; width:100%;">
                <tr>
                    <td style="border:none; font-size:11px; color:#374151; vertical-align:middle;">
                        <strong>Work Attendance</strong>
                        <span style="font-size:10px; color:#6b7280; margin-left:6px;">
                            ({{ $penggajian->bulan_text }} {{ $penggajian->tahun }}, Mon–Sat excl. holidays)
                        </span>
                    </td>
                    <td style="border:none; text-align:right; vertical-align:middle;">
                        <span style="font-size:16px; font-weight:bold; color:#0369a1;">
                            {{ $totalMasuk }} / {{ $totalHariKerja }}
                        </span>
                        <span style="font-size:10px; color:#6b7280; margin-left:4px;">days</span>
                        <span style="
                            margin-left:8px;
                            font-size:10px;
                            font-weight:600;
                            padding:2px 7px;
                            border-radius:20px;
                            background-color:{{ $attendancePct >= 90 ? '#dcfce7' : ($attendancePct >= 75 ? '#fef9c3' : '#fee2e2') }};
                            color:{{ $attendancePct >= 90 ? '#15803d' : ($attendancePct >= 75 ? '#a16207' : '#b91c1c') }};
                        ">{{ $attendancePct }}%</span>
                    </td>
                </tr>
            </table>
        </div>

        @if ($penggajian->catatan)
            <div style="border: 1px solid #1e40af; border-radius: 6px; padding: 8px 16px; max-height: 40px; overflow: hidden;">
                <h3 style="margin:0 0 2px 0;">Notes</h3>
                <p style="margin:0;">{{ \Illuminate\Support\Str::limit($penggajian->catatan, 130) }}</p>
            </div>
        @endif

    </div>

    {{-- SIGNATURE & STAMP — fixed near the bottom so it always sits clear of the
         footer artwork's baked-in contact block, regardless of how tall the
         content above it (e.g. Notes) is. Keeps the payslip on a single page. --}}
    <div style="position: fixed; bottom: 200px; right: 25px; width: 200px; text-align: center; z-index: 20;">
        <h3 style="margin-bottom:2px;">Depok, {{ \Carbon\Carbon::now()->locale('en')->isoFormat('D MMMM YYYY') }}</h3>

        <div style="position: relative; width:140px; height:85px; margin:0 auto;">

            {{-- Stamp --}}
            <img src="data:image/{{ $logoType }};base64,{{ $stampPath }}" alt="Stamp"
                style="
            position:absolute;
            left:12px;
            top:5px;
            width:75px;
            height:75px;
            opacity:0.8;
        ">

            {{-- Signature --}}
            <img src="data:image/{{ $logoType }};base64,{{ $signaturePath }}" alt="Signature"
                style="
            position:absolute;
            left:35px;
            top:15px;
            width:120px;
            height:auto;
            z-index:10;
        ">
        </div>

        <div style="margin-top:4px;">
            <strong>Nina Sakinah</strong>
            <br>
            <span style="font-size:12px;">Chief Operating Officer</span>
        </div>
    </div>
</body>

</html>
