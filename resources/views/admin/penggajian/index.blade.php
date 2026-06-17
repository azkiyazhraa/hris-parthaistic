@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">

        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">Payroll</h1>
                <p class="text-sm text-gray-700/80">Manage employee salaries, deductions, and payments.</p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="p-4 bg-white shadow rounded-2xl md:p-6">
            <div class="grid grid-cols-1 divide-y sm:grid-cols-4 sm:divide-y-0 sm:divide-x">
                <div class="flex flex-col gap-1 px-4 py-2">
                    <p class="text-sm text-gray-400">Total Payroll</p>
                    <h2 class="text-xl font-semibold text-blue-900 md:text-2xl">Rp
                        {{ number_format($statistics['total_payroll'] ?? 0, 0, ',', '.') }}</h2>
                </div>
                <div class="flex flex-col gap-1 px-4 py-2">
                    <p class="text-sm text-gray-400">Pending</p>
                    <h2 class="text-xl font-semibold text-yellow-600 md:text-2xl">{{ $statistics['total_pending'] ?? 0 }}
                    </h2>
                </div>
                <div class="flex flex-col gap-1 px-4 py-2">
                    <p class="text-sm text-gray-400">Approved</p>
                    <h2 class="text-xl font-semibold text-blue-600 md:text-2xl">{{ $statistics['total_approved'] ?? 0 }}
                    </h2>
                </div>
                <div class="flex flex-col gap-1 px-4 py-2">
                    <p class="text-sm text-gray-400">Paid</p>
                    <h2 class="text-xl font-semibold text-green-600 md:text-2xl">{{ $statistics['total_paid'] ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white shadow-lg rounded-2xl" style="border: 2px solid #e0eaff;">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
                <div class="flex gap-2">
                    <a href="{{ route('admin.penggajian.create') }}"
                        class="px-4 py-2 text-sm text-white transition bg-blue-800 rounded-lg hover:bg-blue-900">+ Generate
                        Payroll</a>
                    <a href="{{ route('admin.penggajian.export') }}"
                        class="px-4 py-2 text-sm text-white transition bg-green-600 rounded-lg hover:bg-green-700">Export
                        Report</a>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select id="filter_bulan" class="px-3 py-1 text-sm border rounded-lg">
                        <option value="">All Month</option>
                        @foreach (range(1, 12) as $b)
                            <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                                {{ ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][$b - 1] }}
                            </option>
                        @endforeach
                    </select>
                    <select id="filter_tahun" class="px-3 py-1 text-sm border rounded-lg">
                        <option value="">All Year</option>
                        @foreach (range(date('Y') - 2, date('Y')) as $t)
                            <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>
                                {{ $t }}</option>
                        @endforeach
                    </select>
                    <select id="filter_status" class="px-3 py-1 text-sm border rounded-lg">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                    <button onclick="applyFilters()"
                        class="px-3 py-1 text-sm text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">Filter</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                    <thead>
                        <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
                            <th class="pb-3 text-left whitespace-nowrap">Employee</th>
                            <th class="pb-3 text-left whitespace-nowrap">Period</th>
                            <th class="pb-3 text-left whitespace-nowrap">Base Salary</th>
                            <th class="pb-3 text-left whitespace-nowrap">Total Earnings</th>
                            <th class="pb-3 text-left whitespace-nowrap">Total Deductions</th>
                            <th class="pb-3 text-left whitespace-nowrap">Net Salary</th>
                            <th class="pb-3 text-left whitespace-nowrap">Status</th>
                            <th class="pb-3 text-left whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penggajian as $item)
                            <tr class="transition border-b border-gray-100 hover:bg-blue-50/40">
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $fotoUrl = $item->karyawan->foto_profil
                                                ? Storage::url($item->karyawan->foto_profil)
                                                : 'https://ui-avatars.com/api/?background=2563EB&color=fff&size=100&name=' .
                                                    urlencode($item->karyawan->nama_lengkap);
                                        @endphp

                                        <div
                                            class="overflow-hidden bg-blue-100 border border-blue-200 rounded-full w-9 h-9 shrink-0">
                                            <img src="{{ $fotoUrl }}" alt="{{ $item->karyawan->nama_lengkap }}"
                                                class="object-cover w-full h-full" loading="lazy"
                                                onerror="this.src='https://ui-avatars.com/api/?background=2563EB&color=fff&size=100&name={{ urlencode($item->karyawan->nama_lengkap) }}'">
                                        </div>
                                        <div>
                                            <span
                                                class="font-medium text-gray-800">{{ $item->karyawan->nama_lengkap }}</span><br>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 text-gray-700">
                                    {{ $item->bulan_text }} {{ $item->tahun }}
                                </td>

                                <td class="py-3 text-gray-700">
                                    Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}
                                </td>

                                <td class="py-3 text-green-600">
                                    Rp {{ number_format($item->total_earnings, 0, ',', '.') }}
                                </td>

                                <td class="py-3 text-red-600">
                                    Rp {{ number_format($item->total_deductions, 0, ',', '.') }}
                                </td>

                                <td class="py-3 font-semibold text-blue-600">Rp
                                    {{ number_format($item->net_salary, 0, ',', '.') }}
                                </td>

                                <td class="py-3">
                                    {!! $item->status_badge !!}
                                </td>

                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <a onclick="showDetail({{ $item->id }})"
                                            class="text-blue-500 cursor-pointer hover:text-blue-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.penggajian.edit', $item->id) }}"
                                            class="text-yellow-500 cursor-pointer hover:text-yellow-700" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-4 text-center text-gray-500">No payroll data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $penggajian->links() }}</div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/40">
        <div class="relative w-full max-w-2xl p-4">
            <div class="p-6 bg-white shadow-lg rounded-3xl">
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-blue-900">Payroll Detail</h3>
                    <button onclick="closeDetailModal()"
                        class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100">✕</button>
                </div>
                <div id="detailContent" class="max-h-[80vh] overflow-y-auto p-4"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function applyFilters() {
            let bulan = document.getElementById('filter_bulan').value;
            let tahun = document.getElementById('filter_tahun').value;
            let status = document.getElementById('filter_status').value;
            let url = new URL(window.location.href);
            if (bulan) url.searchParams.set('bulan', bulan);
            else url.searchParams.delete('bulan');
            if (tahun) url.searchParams.set('tahun', tahun);
            else url.searchParams.delete('tahun');
            if (status) url.searchParams.set('status', status);
            else url.searchParams.delete('status');
            window.location.href = url.toString();
        }

        function showDetail(id) {
            fetch(`/admin/penggajian/${id}`)
                .then(response => response.json())
                .then(data => {
                    const karyawan = data.karyawan || {};

                    const nama = karyawan.nama_lengkap || data.nama_karyawan || '-';
                    const nip = karyawan.nip || '-';
                    const role = karyawan.role || '-';
                    const email = karyawan.email || '-';

                    const foto = karyawan.foto_profil ?
                        `/storage/${karyawan.foto_profil}` :
                        `https://ui-avatars.com/api/?background=1E3A8A&color=fff&size=100&name=${encodeURIComponent(nama)}`;

                    const formatRupiah = (value) => {
                        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
                    };

                    const statusClass = {
                        draft: 'bg-yellow-100 text-yellow-800',
                        pending: 'bg-orange-100 text-orange-800',
                        approved: 'bg-blue-100 text-blue-800',
                        paid: 'bg-green-100 text-green-800'
                    };

                    const badgeClass = statusClass[data.status] || 'bg-gray-100 text-gray-700';

                    const sendPaySlip = `/admin/penggajian/${data.id}/send-payslip`;
                    const downloadUrl = `/admin/penggajian/${data.id}/download`;

                    const bulanNama = {
                        1: 'January',
                        2: 'February',
                        3: 'March',
                        4: 'April',
                        5: 'May',
                        6: 'June',
                        7: 'July',
                        8: 'August',
                        9: 'September',
                        10: 'October',
                        11: 'November',
                        12: 'December'
                    };

                    const bulanText = bulanNama[Number(data.bulan)] || '-';

                    const content = `
                        <div class="flex items-center justify-between pb-4 mb-6 border-b">
                            <div>
                                <p class="text-xs text-gray-400">Period</p>
                                <p class="text-sm font-semibold text-gray-800">
                                ${bulanText} ${data.tahun || ''}
                                </p>
                            </div>

                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold capitalize ${badgeClass}">
                                ${data.status || '-'}
                            </span>
                        </div>

                        <div class="flex items-start justify-between gap-6 mb-6">
                            <div class="flex-1">
                                <h3 class="mb-4 text-base font-bold text-gray-900">Employee Info</h3>

                                <div class="grid grid-cols-2 text-sm gap-x-8 gap-y-3">
                                <div>
                                    <p class="text-xs text-gray-400">Name</p>
                                    <p class="font-semibold text-gray-800">${nama}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400">Employee ID</p>
                                    <p class="font-semibold text-gray-800">${nip}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400">Department</p>
                                    <p class="font-semibold text-gray-800 capitalize">${role}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400">Email</p>
                                    <p class="font-semibold text-gray-800">${email}</p>
                                </div>
                                </div>
                            </div>

                            <div class="w-20 h-20 overflow-hidden bg-gray-100 border-4 border-blue-900 rounded-full shrink-0">
                                <img
                                src="${foto}"
                                class="object-cover w-full h-full"
                                onerror="
                                    this.src =
                                    'https://ui-avatars.com/api/?background=1E3A8A&color=fff&size=100&name=${encodeURIComponent(nama)}'
                                "
                                />
                            </div>
                        </div>

                        <div class="my-5 border-t border-gray-100"></div>

                        <div class="grid grid-cols-2 gap-8 mb-6">
                            <div>
                                <h3 class="mb-3 text-base font-bold text-gray-900">Earnings</h3>

                                <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Base Salary</span>
                                    <span class="text-gray-800">${formatRupiah(data.gaji_pokok)}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">Transport</span>
                                    <span class="text-gray-800"
                                    >${formatRupiah(data.transport_allowance)}</span
                                    >
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">Meal</span>
                                    <span class="text-gray-800">${formatRupiah(data.meal_allowance)}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">Internet</span>
                                    <span class="text-gray-800"
                                    >${formatRupiah(data.internet_allowance)}</span
                                    >
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">Position</span>
                                    <span class="text-gray-800"
                                    >${formatRupiah(data.position_allowance)}</span
                                    >
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">Incentive</span>
                                    <span class="text-gray-800">${formatRupiah(data.incentive)}</span>
                                </div>

                                <div class="flex justify-between pt-2 font-semibold border-t">
                                    <span>Total Earnings</span>
                                    <span>${formatRupiah(data.total_earnings)}</span>
                                </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="mb-3 text-base font-bold text-gray-900">Deduction</h3>

                                <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tax (PPh 21)</span>
                                    <span class="text-gray-800">${formatRupiah(data.tax)}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">BPJS Employment</span>
                                    <span class="text-gray-800"
                                    >${formatRupiah(data.bpjs_ketenagakerjaan)}</span
                                    >
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">Late / Absent</span>
                                    <span class="text-gray-800"
                                    >${formatRupiah(data.late_absent_deduction)}</span
                                    >
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">Loan</span>
                                    <span class="text-gray-800">${formatRupiah(data.loan_deduction)}</span>
                                </div>

                                <div class="flex justify-between pt-2 font-semibold border-t">
                                    <span>Total Deduction</span>
                                    <span>${formatRupiah(data.total_deductions)}</span>
                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="my-5 border-t border-gray-100"></div>

                        <div class="flex items-center justify-between">
                            <span class="text-base font-bold text-gray-900">Net Salary</span>
                            <span class="text-xl font-bold text-blue-900">
                                ${formatRupiah(data.net_salary)}
                            </span>
                        </div>

                        ${ data.catatan ? `
                                                                                                     <div class="p-3 mt-5 border-l-4 border-yellow-500 rounded bg-yellow-50">
                                                                                                        <p class="text-sm font-semibold text-yellow-800">Catatan</p>
                                                                                                        <p class="text-sm text-yellow-700">${data.catatan}</p>
                                                                                                    </div>
                                                                                                    ` : '' }

                        <div class="flex items-center gap-3 mt-6">
                            <a
                                href="${sendPaySlip}"
                                onclick="return confirmSendPayslip(event)"
                                class="flex items-center gap-1 px-3 py-1 text-sm text-white transition bg-blue-800 rounded-lg hover:bg-blue-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                                </svg>
                                Send Payslip
                            </a>
                            <a
                                href="${downloadUrl}"
                                class="flex items-center gap-1 px-3 py-1 text-sm text-white transition bg-blue-400 rounded-lg hover:bg-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                />
                                </svg>
                                Download Payslip
                            </a>
                        </div>
                    `;

                    document.getElementById('detailContent').innerHTML = content;
                    document.getElementById('detailModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error(error);
                    alert('Gagal memuat detail data');
                });
        }

        function confirmSendPayslip(event) {
            event.preventDefault();

            const url = event.currentTarget.getAttribute('href');

            Swal.fire({
                title: 'Send payslip?',
                text: 'Slip salary will be sent to employee email.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, send',
                cancelButtonText: 'No, cancel',
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-2xl',
                    actions: 'gap-2',
                    confirmButton: 'px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-medium transition',
                    cancelButton: 'px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium transition'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });

            return false;
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }
    </script>
@endpush
