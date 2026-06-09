@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4 px-2 sm:px-4">

        {{-- CARD HEADER --}}
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">
                    Leave Management
                </h1>
                <p class="text-gray-700/80 text-sm">
                    Manage employee leave requests.
                </p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        {{-- CARD SUMMARY --}}
        <div class="bg-white rounded-2xl shadow p-4 md:p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="flex flex-col gap-1 px-2 py-2 text-center sm:text-left">
                    <p class="text-gray-400 text-xs sm:text-sm">Total Request (This Month)</p>
                    <h2 class="text-xl md:text-2xl font-semibold text-blue-900">
                        {{ $totalRequest }}
                    </h2>
                </div>
                <div class="flex flex-col gap-1 px-2 py-2 text-center sm:text-left">
                    <p class="text-gray-400 text-xs sm:text-sm">Approved</p>
                    <h2 class="text-xl md:text-2xl font-semibold text-green-600">
                        {{ $approved }}
                    </h2>
                </div>
                <div class="flex flex-col gap-1 px-2 py-2 text-center sm:text-left">
                    <p class="text-gray-400 text-xs sm:text-sm">Requested</p>
                    <h2 class="text-xl md:text-2xl font-semibold text-yellow-600">
                        {{ $requested }}
                    </h2>
                </div>
                <div class="flex flex-col gap-1 px-2 py-2 text-center sm:text-left">
                    <p class="text-gray-400 text-xs sm:text-sm">Rejected</p>
                    <h2 class="text-xl md:text-2xl font-semibold text-red-600">
                        {{ $rejected }}
                    </h2>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-lg w-full p-4 sm:p-6" style="border: 2px solid #e0eaff;">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

                <!-- TITLE -->
                <div>
                    <h2 class="text-lg font-semibold text-blue-900">
                        Leave
                    </h2>
                </div>

                <!-- FILTER -->
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">

                    <!-- STATUS -->
                    <div class="w-full sm:w-48">
                        <select id="filter_status" name="status"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">

                            <option value="">All Status</option>

                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                Requested
                            </option>

                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>
                                Approved
                            </option>

                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                Rejected
                            </option>
                        </select>
                    </div>

                    <!-- MONTH -->
                    <div class="w-full sm:w-48">
                        <select id="filter_month"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">

                            <option value="">All Months</option>

                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>

                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                    <thead>
                        <tr class="text-gray-400 font-medium text-xs uppercase tracking-wide border-b">
                            <th class="text-left pb-3 whitespace-nowrap">Employee</th>
                            <th class="text-left pb-3 whitespace-nowrap">Leave Type</th>
                            <th class="text-left pb-3 whitespace-nowrap">Start Date</th>
                            <th class="text-left pb-3 whitespace-nowrap">End Date</th>
                            <th class="text-left pb-3 whitespace-nowrap">Days</th>
                            <th class="text-left pb-3 whitespace-nowrap">Status</th>
                            <th class="text-left pb-3 whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody id="leaveTableBody">
                        @foreach ($dataLeave as $item)
                            <tr class="leave-row border-t border-gray-100 hover:bg-blue-50/40 transition"
                                data-status="{{ strtolower($item->status) }}"
                                data-month="{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('n') }}">
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        @php
    $fotoUrl = $item->karyawan->foto_profil
        ? Storage::url($item->karyawan->foto_profil)
        : 'https://ui-avatars.com/api/?background=2563EB&color=fff&size=100&name=' .
        urlencode($item->karyawan->nama_lengkap);
                                        @endphp

                                        <div
                                            class="w-9 h-9 rounded-full bg-blue-100 border border-blue-200 overflow-hidden shrink-0">
                                            <img src="{{ $fotoUrl }}" alt="{{ $item->karyawan->nama_lengkap }}"
                                                class="w-full h-full object-cover" loading="lazy"
                                                onerror="this.src='https://ui-avatars.com/api/?background=2563EB&color=fff&size=100&name={{ urlencode($item->karyawan->nama_lengkap) }}'">
                                        </div>
                                        <div>
                                            <span
                                                class="font-medium text-gray-800">{{ $item->karyawan->nama_lengkap }}</span><br>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3 px-2 text-gray-700 whitespace-nowrap">
                                    <span class="capitalize">{{ $item->jenis_cuti_label ?? $item->jenis_cuti }}</span>
                                </td>

                                <td class="py-3 px-2 text-gray-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                </td>

                                <td class="py-3 px-2 text-gray-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                </td>

                                <td class="py-3 px-2 text-gray-700 font-semibold text-center whitespace-nowrap">
                                    {{ $item->total_hari }}
                                </td>

                                <td class="py-3 px-2 whitespace-nowrap">
                                    {!! $item->status_badge !!}
                                </td>

                                <td class="py-3 pr-4 sm:pr-2 pl-2 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a class="text-blue-500 hover:text-blue-700 p-1 rounded-full hover:bg-blue-50 transition"
                                            title="Detail" onclick="showDetail({{ $item->id }})">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @if ($item->status == 'pending')
                                            <button
                                                onclick="openStatusModal({{ $item->id }}, '{{ $item->nama_karyawan }}')"
                                                class="text-green-500 hover:text-green-700 p-1 rounded-full hover:bg-green-50 transition"
                                                title="Approve/Reject">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="filterEmptyRow" style="display: none;">
                            <td colspan="7" class="text-center py-8 text-gray-400">
                                Data Not Found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="paginationContainer" class="mt-6 flex items-center justify-end gap-2 flex-wrap">
            </div>
        </div>
    </div>

    {{-- MODAL APPROVE/REJECT --}}
    <div id="statusModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-blue-900">Update Leave Status</h3>
                <button onclick="closeStatusModal()"
                    class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="modalCutiId">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">Employee: <span id="modalNamaKaryawan"
                            class="font-semibold"></span></p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="status"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="disetujui">✅ Approve</option>
                        <option value="ditolak">❌ Reject</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Notes (Optional)</label>
                    <textarea name="catatan" rows="3"
                        class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Tambahkan catatan untuk karyawan..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeStatusModal()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">Cancel</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 flex justify-center items-center bg-black/40">
        <div class="relative w-full max-w-xl p-4">
            <div class="bg-white rounded-3xl shadow-lg p-6">
                <div class="flex justify-between items-center border-b pb-4">
                    <h3 class="text-lg font-semibold text-blue-900">Leave Detail</h3>
                    <button onclick="closeDetailModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">✕</button>
                </div>
                <div id="detailContent" class="max-h-[80vh] overflow-y-auto p-4"></div>
            </div>
        </div>
    </div>
@endsection


@push('styles')
    <style>
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        #statusModal.show {
            display: flex;
        }

        #statusModal {
            display: none;
        }

        /* Better table responsiveness */
        @media (max-width: 640px) {
            .container {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
        }
    </style>
@endpush


@push('scripts')
    <script>
        function openStatusModal(id, nama) {
            document.getElementById('modalCutiId').value = id;
            document.getElementById('modalNamaKaryawan').innerText = nama;
            document.getElementById('statusForm').action = "{{ url('admin/leave') }}/" + id + "/status";
            document.getElementById('statusModal').classList.add('show');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.remove('show');
        }

        function showDetail(id) {
            fetch(`/admin/leave/${id}`)
                .then(response => response.json())
                .then(data => {
                    const nama = data.karyawan?.nama_lengkap || data.nama_karyawan || '-';
                    const role = data.karyawan?.role || '-';
                    const email = data.karyawan?.email || '-';
                    const phone = data.karyawan?.nomor_telepon || '-';

                    const foto = data.karyawan?.foto_profil ?
                        `/storage/${data.karyawan.foto_profil}` :
                        `https://ui-avatars.com/api/?background=1E3A8A&color=fff&size=100&name=${encodeURIComponent(nama)}`;

                    const attachment = data.lampiran ?
                        `<a href="/storage/${data.lampiran}" target="_blank" class="text-blue-600 hover:underline text-xs">Lihat file</a>` :
                        '-';

                    const note = data.alasan || '-';

                    const startDate = new Date(data.tanggal_mulai).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    const endDate = new Date(data.tanggal_selesai).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });

                    const status = data.status === 'disetujui' ? 'Approved' :
                        data.status === 'ditolak' ? 'Rejected' : 'Requested';

                    const leaveType = data.jenis_cuti || '-';

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
                                    <h3 class="text-base font-semibold text-slate-900">${name}</h3>
                                    <p class="text-xs text-gray-500 capitalize">${role}</p>
                                    <p class="text-xs text-gray-400">${email}</p>
                                    <p class="text-xs text-gray-400">${phone}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-4 gap-3 text-xs">
                            <div>
                                <p class="text-gray-400 mb-1">Start Date</p>
                                <p class="text-gray-700 font-medium">${startDate}</p>
                            </div>

                            <div>
                                <p class="text-gray-400 mb-1">End Date</p>
                                <p class="text-gray-700 font-medium">${endDate}</p>
                            </div>

                            <div>
                                <p class="text-gray-400 mb-1">Leave Type</p>
                                <p class="text-gray-700 font-medium">${leaveType}</p>
                            </div>

                            <div>
                                <p class="text-gray-400 mb-1">Attachment</p>
                                ${attachment}
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Notes</p>
                            <div class="border border-blue-500 rounded-md px-3 py-2 text-xs text-gray-600">
                                ${note}
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Status</p>
                            <span class="inline-flex px-3 py-1 rounded-md text-xs font-medium ${data.status === 'disetujui' ? 'bg-green-100 text-green-800' : data.status === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'}">
                                ${status}
                            </span>
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

        // Close modal when clicking outside
        document.getElementById('statusModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatusModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {

            const filterStatus = document.getElementById('filter_status');
            const filterMonth = document.getElementById('filter_month');

            const rows = Array.from(document.querySelectorAll('.leave-row'));

            const paginationContainer =
                document.getElementById('paginationContainer');

            const filterEmptyRow =
                document.getElementById('filterEmptyRow');

            const perPage = 10;

            let currentPage = 1;
            let filteredRows = [...rows];

            // =========================
            // APPLY FILTER
            // =========================
            function applyFilters() {

                const status =
                    filterStatus.value.toLowerCase();

                const month =
                    filterMonth.value;

                filteredRows = rows.filter(row => {

                    const rowStatus =
                        row.dataset.status;

                    const rowMonth =
                        row.dataset.month;

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

                const start =
                    (currentPage - 1) * perPage;

                const end =
                    start + perPage;

                filteredRows
                    .slice(start, end)
                    .forEach(row => {
                        row.style.display = 'table-row';
                    });

                if (filterEmptyRow) {

                    filterEmptyRow.style.display =
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
                const prevBtn =
                    document.createElement('button');

                prevBtn.innerHTML = '&laquo;';

                prevBtn.disabled =
                    currentPage === 1;

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

                    const btn =
                        document.createElement('button');

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
                const nextBtn =
                    document.createElement('button');

                nextBtn.innerHTML = '&raquo;';

                nextBtn.disabled =
                    currentPage === totalPages;

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
