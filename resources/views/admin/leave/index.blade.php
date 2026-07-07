@extends('layouts.app')
@section('content')
    <div class="container px-2 py-4 mx-auto space-y-4 sm:px-4">

        {{-- CARD HEADER --}}
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">
                    Leave Management
                </h1>
                <p class="text-sm text-gray-700/80">
                    Manage employee leave requests.
                </p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        {{-- CARD SUMMARY --}}
        <div class="p-4 bg-white shadow rounded-2xl md:p-6">
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="flex flex-col gap-1 px-2 py-2 text-center sm:text-left">
                    <p class="text-xs text-gray-400 sm:text-sm">Total Request (This Month)</p>
                    <h2 class="text-xl font-semibold text-blue-900 md:text-2xl">
                        {{ $totalRequest }}
                    </h2>
                </div>
                <div class="flex flex-col gap-1 px-2 py-2 text-center sm:text-left">
                    <p class="text-xs text-gray-400 sm:text-sm">Approved</p>
                    <h2 class="text-xl font-semibold text-green-600 md:text-2xl">
                        {{ $approved }}
                    </h2>
                </div>
                <div class="flex flex-col gap-1 px-2 py-2 text-center sm:text-left">
                    <p class="text-xs text-gray-400 sm:text-sm">Requested</p>
                    <h2 class="text-xl font-semibold text-yellow-600 md:text-2xl">
                        {{ $requested }}
                    </h2>
                </div>
                <div class="flex flex-col gap-1 px-2 py-2 text-center sm:text-left">
                    <p class="text-xs text-gray-400 sm:text-sm">Rejected</p>
                    <h2 class="text-xl font-semibold text-red-600 md:text-2xl">
                        {{ $rejected }}
                    </h2>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="w-full p-4 bg-white shadow-lg rounded-2xl sm:p-6" style="border: 2px solid #e0eaff;">
            <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">

                <!-- TITLE -->
                <div>
                    <h2 class="text-lg font-semibold text-blue-900">
                        Leave
                    </h2>
                </div>

                <!-- FILTER -->
                <div class="flex flex-col w-full gap-3 sm:flex-row md:w-auto">

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
                        <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
                            <th class="pb-3 text-left whitespace-nowrap">Employee</th>
                            <th class="pb-3 text-left whitespace-nowrap">Leave Type</th>
                            <th class="pb-3 text-left whitespace-nowrap">Start Date</th>
                            <th class="pb-3 text-left whitespace-nowrap">End Date</th>
                            <th class="pb-3 text-left whitespace-nowrap">Days</th>
                            <th class="pb-3 text-left whitespace-nowrap">Status</th>
                            <th class="pb-3 text-left whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody id="leaveTableBody">
                        @foreach ($dataLeave as $item)
                            <tr class="transition border-t border-gray-100 leave-row hover:bg-blue-50/40"
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

                                <td class="px-2 py-3 text-gray-700 whitespace-nowrap">
                                    {{ match ($item->jenis_cuti) {
                                        'tahunan' => 'Annual Leave',
                                        'melahirkan' => $item->karyawan?->jenis_kelamin === 'P' ? 'Maternity Leave' : 'Paternity Leave',
                                        'menikah' => 'Marriage Leave',
                                        'duka' => 'Bereavement Leave',
                                        default => ucfirst($item->jenis_cuti) . ' Leave',
                                    } }}
                                </td>

                                <td class="px-2 py-3 text-gray-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                </td>

                                <td class="px-2 py-3 text-gray-700 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                                </td>

                                <td class="px-2 py-3 font-semibold text-center text-gray-700 whitespace-nowrap">
                                    {{ $item->total_hari }}
                                </td>

                                <td class="px-2 py-3 whitespace-nowrap">
                                    <form action="{{ route('admin.leave.update-status', $item->id) }}" method="POST"
                                        class="inline-block" id="leave-form-{{ $item->id }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="catatan" id="leave-note-{{ $item->id }}">
                                        <select name="status" data-current-status="{{ $item->status }}"
                                            onchange="updateLeaveStatus({{ $item->id }}, this)"
                                            class="text-xs rounded-full py-1 px-3 border-0 focus:ring-2 focus:ring-blue-500 cursor-pointer
                                            @if ($item->status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($item->status == 'disetujui') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="disetujui" {{ $item->status == 'disetujui' ? 'selected' : '' }}>
                                                Approved</option>
                                            <option value="ditolak" {{ $item->status == 'ditolak' ? 'selected' : '' }}>
                                                Rejected</option>
                                        </select>
                                    </form>
                                </td>

                                <td class="py-3 pl-2 pr-4 sm:pr-2 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a class="p-1 text-blue-500 transition rounded-full hover:text-blue-700 hover:bg-blue-50"
                                            title="Detail" onclick="showDetail({{ $item->id }})">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="filterEmptyRow" style="display: none;">
                            <td colspan="7" class="py-8 text-center text-gray-400">
                                Data Not Found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="paginationContainer" class="flex flex-wrap items-center justify-end gap-2 mt-6">
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/40">
        <div class="relative w-full max-w-xl p-4">
            <div class="p-6 bg-white shadow-lg rounded-3xl">
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-blue-900">Leave Detail</h3>
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
        async function updateLeaveStatus(id, selectElement) {
            const selectedStatus = selectElement.value;
            const currentStatus = selectElement.dataset.currentStatus;

            const labelMap = {
                disetujui: 'Approved',
                ditolak: 'Rejected',
                pending: 'Pending'
            };

            // Warning when reverting from approved to rejected
            if (selectedStatus === 'ditolak' && currentStatus === 'disetujui') {
                const {
                    isConfirmed
                } = await Swal.fire({
                    title: 'Revert approval?',
                    html: `<p class="text-sm text-gray-600">This request was already <strong>approved</strong>. Rejecting it may affect the employee's leave quota.</p>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, reject it',
                    cancelButtonText: 'Keep approved',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg mx-1',
                        cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg mx-1',
                    },
                });
                if (!isConfirmed) {
                    location.reload();
                    return;
                }
            }

            // Notes modal
            const {
                value: inputNote,
                isConfirmed
            } = await Swal.fire({
                title: `Change status to ${labelMap[selectedStatus] || selectedStatus}?`,
                input: 'textarea',
                inputLabel: selectedStatus === 'ditolak' ? 'Rejection reason' : 'Note (optional)',
                inputPlaceholder: selectedStatus === 'ditolak' ? 'Enter rejection reason...' : 'Add a note...',
                inputAttributes: {
                    'aria-label': 'Note'
                },
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg mx-1',
                    cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg mx-1',
                },
                inputValidator: (value) => {
                    if (selectedStatus === 'ditolak' && !value) return 'Rejection reason is required';
                },
            });

            if (!isConfirmed) {
                location.reload();
                return;
            }

            document.getElementById(`leave-note-${id}`).value = inputNote || '';
            document.getElementById(`leave-form-${id}`).submit();
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
                        `<a href="/storage/${data.lampiran}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat file</a>` :
                        '-';

                    const note = data.alasan ?
                        data.alasan.replace(/\n/g, '<br>') :
                        '-';

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

                    const textLeave = {
                        'tahunan': {
                            text: 'Annual Leave'
                        },
                        'melahirkan': {
                            text: 'Maternity / Paternity Leave'
                        },
                        'menikah': {
                            text: 'Marriage Leave'
                        },
                        'duka': {
                            text: 'Bereavement Leave'
                        },
                    };
                    const leaveType = textLeave[(data.jenis_cuti || '').toLowerCase()] || {
                        text: data.jenis_cuti || '-',
                    };
                    const leave = leaveType.text;

                    const content = `
                    <div class="space-y-5">

                        <div class="pb-4 border-b">
                            <div class="flex items-start gap-4">
                                <div class="w-16 h-16 overflow-hidden border-2 border-blue-700 rounded-full shrink-0">
                                    <img
                                        src="${foto}"
                                        class="object-cover w-full h-full"
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

                        <div class="grid grid-cols-3 gap-3 text-xs">
                            <div>
                                <p class="mb-1 text-gray-400">Start Date</p>
                                <p class="font-medium text-gray-700">${startDate}</p>
                            </div>

                            <div>
                                <p class="mb-1 text-gray-400">End Date</p>
                                <p class="font-medium text-gray-700">${endDate}</p>
                            </div>

                            <div>
                                <p class="mb-1 text-gray-400">Leave Type</p>
                                <p class="font-medium text-gray-700">${leave}</p>
                            </div>
                        </div>

                        <div>
                            <p class="mb-1 text-xs text-gray-500">Notes</p>
                            <div class="px-3 py-2 text-xs text-gray-600 border border-blue-500 rounded-md">
                                ${note}
                            </div>
                        </div>

                        <div>
                            <p class="mb-1 text-xs text-gray-500">Status</p>
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
