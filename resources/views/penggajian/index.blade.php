@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-4 space-y-4">
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">Payroll</h1>
                <p class="text-gray-700/80 text-sm">View your salary details, allowances, and payment history.</p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-4 md:p-6 my-4">
            <div class="grid grid-cols-1 sm:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x">
                <!-- ITEM 1 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-blue-900 text-xl">Year</span>
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-xl md:text-2xl font-semibold text-blue-900" id="periode">
                            {{ date('Y') }}
                        </h2>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 text-blue-900">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                        </svg>
                    </div>
                </div>

                <!-- ITEM 2 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-blue-900 text-xl">Total Salary</span>
                    <h2 class="text-xl md:text-2xl font-semibold text-blue-900" id="total_salary">
                        Rp {{ number_format($totalSalary, 0, ',', '.') }}
                    </h2>
                </div>

                <!-- ITEM 3 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-blue-900 text-xl">Deducation</span>
                    <h2 class="text-xl md:text-2xl font-semibold text-blue-900" id="deduction">
                        Rp {{ number_format($totalDeducations, 0, ',', '.') }}
                    </h2>
                </div>

                <!-- ITEM 4 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-blue-900 text-xl">Allowance</span>
                    <h2 class="text-xl md:text-2xl font-semibold text-blue-900" id="allowance">
                        Rp {{ number_format($totalAllowances, 0, ',', '.') }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6" style="border: 2px solid #e0eaff;">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5">

                <!-- TITLE -->
                <div>
                    <h2 class="text-lg font-semibold text-blue-900">
                        Payroll History
                    </h2>
                </div>

                <!-- FILTER -->
                <div class="w-full sm:w-44">
                    <select id="filterYear"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">

                        <option value="">All Year</option>

                        @foreach (range(now()->year - 4, now()->year) as $year)
                            <option value="{{ $year }}">
                                {{ $year }}
                            </option>
                        @endforeach

                    </select>
                </div>

            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                    <thead>
                        <tr class="text-gray-400 font-medium text-xs uppercase tracking-wide border-b">
                            <th class="text-left pb-3 whitespace-nowrap">Year</th>
                            <th class="text-left pb-3 whitespace-nowrap">Base Salary</th>
                            <th class="text-left pb-3 whitespace-nowrap">Total Earnings</th>
                            <th class="text-left pb-3 whitespace-nowrap">Total Deductions</th>
                            <th class="text-left pb-3 whitespace-nowrap">Net Salary</th>
                            <th class="text-left pb-3 whitespace-nowrap">Status</th>
                            <th class="text-left pb-3 whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penggajian as $item)
                            <tr class="payroll-row border-b border-gray-100 hover:bg-blue-50/40 transition"
                                data-year="{{ $item->tahun }}">
                                <td class="py-3 text-gray-700">{{ $item->bulan_text }} {{ $item->tahun }}</td>
                                <td class="py-3 text-gray-700">Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}</td>
                                <td class="py-3 text-green-600">Rp {{ number_format($item->total_earnings, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-red-600">Rp {{ number_format($item->total_deductions, 0, ',', '.') }}
                                </td>
                                <td class="py-3 font-semibold text-blue-600">Rp
                                    {{ number_format($item->net_salary, 0, ',', '.') }}</td>
                                <td class="py-3">{!! $item->status_badge !!}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="javascript:void(0)" onclick="showDetail({{ $item->id }})"
                                            class="text-blue-500 hover:text-blue-700" title="Detail">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('penggajian.download', $item->id) }}"
                                            class="text-purple-500 hover:text-purple-700" title="Download">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p>No payslip data available</P>
                                        <P></P>Check back later or contact HR if you think this is a mistake.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="paginationContainer" class="mt-5 flex items-center justify-end gap-2"></div>
            </div>
        </div>
    </div>


    <!-- Detail Modal -->
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 flex justify-center items-center bg-black/40">
        <div class="relative w-full max-w-xl p-4">
            <div class="bg-white rounded-3xl shadow-lg p-6">
                <div class="flex justify-between items-center border-b pb-4">
                    <h3 class="text-lg font-semibold text-blue-900">Detail Payroll</h3>
                    <button onclick="closeDetailModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">✕</button>
                </div>
                <div id="detailContent" class="max-h-[80vh] overflow-y-auto p-4"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showDetail(id) {
            fetch(`/penggajian/${id}`)
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
                        <div class="flex items-center justify-between mb-6 border-b pb-4">
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

                        <div class="flex justify-between items-start gap-6 mb-6">
                            <div class="flex-1">
                                <h3 class="text-base font-bold text-gray-900 mb-4">Employee Info</h3>

                                <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                                <div>
                                    <p class="text-gray-400 text-xs">Name</p>
                                    <p class="font-semibold text-gray-800">${name}</p>
                                </div>

                                <div>
                                    <p class="text-gray-400 text-xs">Employee ID</p>
                                    <p class="font-semibold text-gray-800">${nip}</p>
                                </div>

                                <div>
                                    <p class="text-gray-400 text-xs">Department</p>
                                    <p class="font-semibold text-gray-800 capitalize">${role}</p>
                                </div>

                                <div>
                                    <p class="text-gray-400 text-xs">Email</p>
                                    <p class="font-semibold text-gray-800">${email}</p>
                                </div>
                                </div>
                            </div>

                            <div class="w-20 h-20 rounded-full border-4 border-blue-900 overflow-hidden bg-gray-100 shrink-0">
                                <img
                                src="${foto}"
                                class="w-full h-full object-cover"
                                onerror="
                                    this.src =
                                    'https://ui-avatars.com/api/?background=1E3A8A&color=fff&size=100&name=${encodeURIComponent(nama)}'
                                "
                                />
                            </div>
                        </div>

                        <div class="border-t border-gray-100 my-5"></div>

                        <div class="grid grid-cols-2 gap-8 mb-6">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 mb-3">Earnings</h3>

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

                                <div class="flex justify-between pt-2 border-t font-semibold">
                                    <span>Total Earnings</span>
                                    <span>${formatRupiah(data.total_earnings)}</span>
                                </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-gray-900 mb-3">Deduction</h3>

                                <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tax (PPh 21)</span>
                                    <span class="text-gray-800">${formatRupiah(data.tax)}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">BPJS Kesehatan</span>
                                    <span class="text-gray-800">${formatRupiah(data.bpjs_kesehatan)}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-gray-600">BPJS Ketenagakerjaan</span>
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

                                <div class="flex justify-between pt-2 border-t font-semibold">
                                    <span>Total Deduction</span>
                                    <span>${formatRupiah(data.total_deductions)}</span>
                                </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 my-5"></div>

                        <div class="flex items-center justify-between">
                            <span class="text-base font-bold text-gray-900">Net Salary</span>
                            <span class="text-xl font-bold text-blue-900">
                                ${formatRupiah(data.net_salary)}
                            </span>
                        </div>

                        ${ data.catatan ? `
                                                                                                                                             <div class="mt-5 p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
                                                                                                                                                <p class="text-sm font-semibold text-yellow-800">Catatan</p>
                                                                                                                                                <p class="text-sm text-yellow-700">${data.catatan}</p>
                                                                                                                                            </div>
                                                                                                                                            ` : '' }
                    `;

                    document.getElementById('detailContent').innerHTML = content;
                    document.getElementById('detailModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error(error);
                    alert('Gagal memuat detail data');
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {

            // =========================
            // ELEMENTS
            // =========================
            const yearFilter =
                document.getElementById('filterYear');

            const rows = [...document.querySelectorAll('.payroll-row')];

            const paginationContainer =
                document.getElementById('paginationContainer');

            // =========================
            // STATE
            // =========================
            const perPage = 5;

            let currentPage = 1;

            let filteredRows = [...rows];

            // =========================
            // FILTER
            // =========================
            function applyFilter() {

                const selectedYear =
                    yearFilter.value;

                filteredRows = rows.filter(row => {

                    const rowYear =
                        row.dataset.year || '';

                    return (
                        selectedYear === '' ||
                        rowYear === selectedYear
                    );
                });

                currentPage = 1;

                renderTable();

                renderPagination();
            }

            // =========================
            // TABLE
            // =========================
            function renderTable() {

                rows.forEach(row => {
                    row.style.display = 'none';
                });

                const start =
                    (currentPage - 1) * perPage;

                const end =
                    start + perPage;

                filteredRows
                    .slice(start, end)
                    .forEach(row => {
                        row.style.display = 'table-row';
                    });
            }

            // =========================
            // PAGINATION
            // =========================
            function renderPagination() {

                paginationContainer.innerHTML = '';

                const totalPages =
                    Math.ceil(filteredRows.length / perPage);

                if (totalPages <= 1) return;

                // PREV
                paginationContainer.appendChild(
                    createButton('«', currentPage - 1, currentPage === 1)
                );

                // NUMBER
                for (let i = 1; i <= totalPages; i++) {

                    const button =
                        createButton(i, i);

                    if (i === currentPage) {

                        button.classList.add(
                            'bg-blue-600',
                            'text-white',
                            'border-blue-600'
                        );

                    } else {

                        button.classList.add(
                            'bg-white',
                            'text-gray-700',
                            'hover:bg-gray-50'
                        );
                    }

                    paginationContainer.appendChild(button);
                }

                // NEXT
                paginationContainer.appendChild(
                    createButton('»', currentPage + 1, currentPage === totalPages)
                );
            }

            // =========================
            // BUTTON
            // =========================
            function createButton(label, page, disabled = false) {

                const button =
                    document.createElement('button');

                button.innerHTML = label;

                button.disabled = disabled;

                button.className = `
                    min-w-[36px]
                    h-9
                    px-3
                    rounded-lg
                    border
                    text-sm
                    transition
                    disabled:opacity-40
                    disabled:cursor-not-allowed
                `;

                button.addEventListener('click', function() {

                    if (disabled) return;

                    currentPage = page;

                    renderTable();

                    renderPagination();
                });

                return button;
            }

            // =========================
            // EVENT
            // =========================
            yearFilter.addEventListener('change', applyFilter);

            // =========================
            // INIT
            // =========================
            applyFilter();

        });
    </script>
@endpush
