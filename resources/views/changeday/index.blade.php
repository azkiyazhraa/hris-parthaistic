@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">

        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">
                    Change Day
                </h1>
                <p class="text-gray-700/80 text-sm">
                    Request and manage your change day with ease.
                </p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        {{-- CARD SUMMARY --}}
        <div class="bg-white rounded-2xl shadow p-4 md:p-6 my-4">
            <div class="grid grid-cols-1 sm:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x">
                <!-- ITEM 1 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-blue-900 text-lg">Total Requests (This Month)</span>
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-xl md:text-2xl font-semibold text-blue-900">
                            {{ number_format($totalRequest, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>

                <!-- ITEM 2 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-blue-900 text-xl">Approved</span>
                    <h2 class="text-xl md:text-2xl font-semibold text-blue-900">
                        {{ number_format($approved, 0, ',', '.') }}
                    </h2>
                </div>

                <!-- ITEM 3 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-blue-900 text-xl">Requested</span>
                    <h2 class="text-xl md:text-2xl font-semibold text-blue-900">
                        {{ number_format($requested, 0, ',', '.') }}
                    </h2>
                </div>

                <!-- ITEM 4 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-blue-900 text-xl">Rejected</span>
                    <h2 class="text-xl md:text-2xl font-semibold text-blue-900">
                        {{ number_format($rejected, 0, ',', '.') }}
                    </h2>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow p-4 md:p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <!-- LEFT -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Change Day Requests
                    </h2>

                    <p class="text-sm text-gray-500">
                        Manage employee change day submissions
                    </p>
                </div>

                <!-- RIGHT -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <!-- FILTER STATUS -->
                    <div class="relative w-full sm:w-44">
                        <select id="filterStatus"
                            class="w-full appearance-none border border-gray-300 bg-white rounded-xl px-4 py-2.5 pr-10 text-sm text-gray-700 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- FILTER MONTH -->
                    <div class="relative w-full sm:w-44">
                        <select id="filterMonth"
                            class="w-full appearance-none border border-gray-300 bg-white rounded-xl px-4 py-2.5 pr-10 text-sm text-gray-700 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            <option value="">All Month</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>

                    <!-- BUTTON -->
                    <button data-modal-target="requestChangeDayModal" data-modal-toggle="requestChangeDayModal"
                        class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl shadow-sm transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Request Change Day
                    </button>

                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                    <thead>
                        <tr class="text-gray-400 font-medium text-xs uppercase tracking-wide border-b">
                            <th class="text-left pb-3 whitespace-nowrap">Request Date</th>
                            <th class="text-left pb-3 whitespace-nowrap">Original Date</th>
                            <th class="text-left pb-3 whitespace-nowrap">Requested Date</th>
                            <th class="text-left pb-3 whitespace-nowrap">Status</th>
                            <th class="text-left pb-3 whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody id="changeDayTable">
                        @foreach ($data as $item)
                            <tr class="change-day-row border-b hover:bg-gray-50 transition"
                                data-status="{{ strtolower($item->change_day_status) }}"
                                data-month="{{ $item->created_at->format('n') }}">
                                <td class="py-3">{{ $item->created_at->format('d F Y') }}</td>
                                <td class="py-3">{{ $item->change_day_tanggal_awal->format('d F Y') }}</td>
                                <td class="py-3">{{ $item->change_day_tanggal_akhir->format('d F Y') }}</td>
                                <td class="py-3">
                                    @php
                                        $statusClass = match ($item->change_day_status) {
                                            'approved' => 'bg-green-100 text-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp

                                    <span class="{{ $statusClass }} py-1 px-3 rounded-full text-xs font-medium">
                                        {{ ucfirst($item->change_day_status) }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <a onclick="showDetail({{ $item->id }})"
                                        class="text-blue-500 hover:text-blue-700 cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="emptyFilterRow" style="display: none;">
                            <td colspan="5" class="text-center py-6 text-gray-400">
                                Data Not Found
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div id="paginationContainer" class="mt-6 flex items-center justify-end gap-2 flex-wrap">
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 flex justify-center items-center bg-black/40">
        <div class="relative w-full max-w-xl p-4">
            <div class="bg-white rounded-3xl shadow-lg p-6">
                <div class="flex justify-between items-center border-b pb-4">
                    <h3 class="text-lg font-semibold text-blue-900">Detail Change Day</h3>
                    <button onclick="closeDetailModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">✕</button>
                </div>
                <div id="detailContent" class="mt-5 space-y-5"></div>
            </div>
        </div>
    </div>

    {{-- MODAL REQUEST CHANGE DAY --}}
    @include('changeday.create')
@endsection

@push('scripts')
    <script>
        function showDetail(id) {
            fetch(`/changeday/${id}`)
                .then(response => response.json())
                .then(data => {
                    const nama = data.karyawan?.nama_lengkap || data.nama_karyawan || '-';
                    const role = data.karyawan?.role || '-';
                    const email = data.karyawan?.email || '-';
                    const phone = data.karyawan?.nomor_telepon || '-';

                    const foto = data.karyawan?.foto_profil ?
                        `/storage/${data.karyawan.foto_profil}` :
                        `https://ui-avatars.com/api/?background=1E3A8A&color=fff&size=100&name=${encodeURIComponent(nama)}`;

                    const attachment = data.attachment ?
                        `<a href="/storage/${data.attachment}" target="_blank" class="text-blue-600 hover:underline text-xs">Lihat file</a>` :
                        '-';

                    const note = data.change_day_alasan || '-';

                    const requestDate = new Date(data.created_at).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    const originalDate = new Date(data.change_day_tanggal_awal).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    const requestChange = new Date(data.change_day_tanggal_akhir).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    const statusMap = {
                        approved: {
                            text: 'Approved',
                            class: 'bg-green-100 text-green-800'
                        },
                        rejected: {
                            text: 'Rejected',
                            class: 'bg-red-100 text-red-800'
                        },
                        pending: {
                            text: 'Requested',
                            class: 'bg-yellow-100 text-yellow-800'
                        }
                    };

                    const currentStatus = statusMap[data.change_day_status] || statusMap.pending;

                    const approveDate = data.change_day_disetujui_pada ? new Date(data.change_day_disetujui_pada)
                        .toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) : null;

                    const approveBy = data.change_day_disetujui_oleh ? data.disetujui_oleh
                        ?.nama_lengkap || '-' : null;

                    const content = `
                    <div class="space-y-5">

                        <div class="border-b pb-4">
                            <div class="flex items-start gap-4">
                                <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-blue-700 shrink-0">
                                    <img
                                        src="${foto}"
                                        class="w-full h-full object-cover"
                                        onerror="this.src='https://ui-avatars.com/api/?background=1E3A8A&color=fff&size=100&name=${encodeURIComponent(nama)}'">
                                </div>

                                <div>
                                    <h3 class="text-base font-semibold text-slate-900">${nama}</h3>
                                    <p class="text-xs text-gray-500 capitalize">${role}</p>
                                    <p class="text-xs text-gray-400">${email}</p>
                                    <p class="text-xs text-gray-400">${phone}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-4 gap-3 text-xs">
                            <div>
                                <p class="text-gray-400 mb-1">Request Date</p>
                                <p class="text-gray-700 font-medium">${requestDate}</p>
                            </div>

                            <div>
                                <p class="text-gray-400 mb-1">Original Schedule</p>
                                <p class="text-gray-700 font-medium">${originalDate}</p>
                            </div>

                            <div>
                                <p class="text-gray-400 mb-1">Requested Change</p>
                                <p class="text-gray-700 font-medium">${requestChange}</p>
                            </div>

                            <div>
                                <p class="text-gray-400 mb-1">Attachment</p>
                                ${attachment}
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Reason</p>
                            <div class="border border-blue-500 rounded-md px-3 py-2 text-xs text-gray-600">
                                ${note}
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded text-xs font-medium ${currentStatus.class}">
                                ${currentStatus.text}
                            </span>
                            ${
                                data.change_day_status === 'approved'
                                    ? `
                                                                                                                                    <p class="text-xs text-gray-500 mt-1">
                                                                                                                                        Last updated: ${approveDate || '-'} by ${approveBy || '-'}
                                                                                                                                    </p>
                                                                                                                                `
                                : ''
                            }
                        </div>

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

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {

            const filterStatus = document.getElementById('filterStatus');
            const filterMonth = document.getElementById('filterMonth');

            const rows = Array.from(document.querySelectorAll('.change-day-row'));

            const emptyFilterRow = document.getElementById('emptyFilterRow');
            const paginationContainer = document.getElementById('paginationContainer');

            const perPage = 10;

            let currentPage = 1;
            let filteredRows = [...rows];

            // =========================
            // APPLY FILTER
            // =========================
            function applyFilters() {

                const status = filterStatus.value.toLowerCase();
                const month = filterMonth.value;

                filteredRows = rows.filter(row => {

                    const rowStatus = row.dataset.status;
                    const rowMonth = row.dataset.month;

                    const matchStatus = !status || rowStatus === status;

                    const matchMonth = !month || rowMonth === month;

                    return matchStatus && matchMonth;
                });

                currentPage = 1;

                renderTable();
                renderPagination();
            }

            // =========================
            // RENDER TABLE
            // =========================
            function renderTable() {

                rows.forEach(row => {
                    row.style.display = 'none';
                });

                const start = (currentPage - 1) * perPage;
                const end = start + perPage;

                filteredRows
                    .slice(start, end)
                    .forEach(row => {
                        row.style.display = 'table-row';
                    });

                if (emptyFilterRow) {
                    emptyFilterRow.style.display =
                        filteredRows.length === 0 ?
                        'table-row' :
                        'none';
                }
            }

            // =========================
            // PAGINATION
            // =========================
            function renderPagination() {

                paginationContainer.innerHTML = '';

                const totalPages =
                    Math.ceil(filteredRows.length / perPage);

                if (totalPages <= 1) return;

                // PREV BUTTON
                const prevBtn = document.createElement('button');

                prevBtn.innerHTML = '&laquo;';

                prevBtn.disabled = currentPage === 1;

                prevBtn.className = `
                    px-3 py-1.5 rounded-lg border text-sm
                    ${currentPage === 1
                        ? 'opacity-40 cursor-not-allowed'
                        : 'hover:bg-gray-100'}
                `;

                prevBtn.addEventListener('click', function() {

                    if (currentPage > 1) {

                        currentPage--;

                        renderTable();
                        renderPagination();
                    }
                });

                paginationContainer.appendChild(prevBtn);

                // PAGE BUTTONS
                for (let i = 1; i <= totalPages; i++) {

                    const btn = document.createElement('button');

                    btn.textContent = i;

                    btn.className = `
                        px-3 py-1.5 rounded-lg border text-sm transition
                        ${i === currentPage
                            ? 'bg-blue-600 text-white border-blue-600'
                            : 'bg-white hover:bg-gray-100'}
                    `;

                    btn.addEventListener('click', function() {

                        currentPage = i;

                        renderTable();
                        renderPagination();
                    });

                    paginationContainer.appendChild(btn);
                }

                // NEXT BUTTON
                const nextBtn = document.createElement('button');

                nextBtn.innerHTML = '&raquo;';

                nextBtn.disabled = currentPage === totalPages;

                nextBtn.className = `
                    px-3 py-1.5 rounded-lg border text-sm
                    ${currentPage === totalPages
                        ? 'opacity-40 cursor-not-allowed'
                        : 'hover:bg-gray-100'}
                `;

                nextBtn.addEventListener('click', function() {

                    if (currentPage < totalPages) {

                        currentPage++;

                        renderTable();
                        renderPagination();
                    }
                });

                paginationContainer.appendChild(nextBtn);
            }

            // =========================
            // EVENTS
            // =========================
            filterStatus.addEventListener('change', applyFilters);
            filterMonth.addEventListener('change', applyFilters);

            // INITIAL
            applyFilters();

        });
    </script>
@endpush
