@extends('layouts.app')
@section('content')
    <div class="container px-2 py-4 mx-auto space-y-4 sm:px-4">

        {{-- CARD HEADER --}}
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">
                    Change Day
                </h1>
                <p class="text-sm text-gray-700/80">
                    Employee change day management system.
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
                    <h2 class="text-xl font-semibold text-blue-900 md:text-2xl">{{ $totalRequest }}</h2>
                </div>
                <div class="flex flex-col gap-1 px-2 py-2 text-center sm:text-left">
                    <p class="text-xs text-gray-400 sm:text-sm">Approved</p>
                    <h2 class="text-xl font-semibold text-green-600 md:text-2xl">{{ $approved }}</h2>
                </div>
                <div class="flex flex-col gap-1 px-2 py-2 text-center sm:text-left">
                    <p class="text-xs text-gray-400 sm:text-sm">Requested</p>
                    <h2 class="text-xl font-semibold text-yellow-600 md:text-2xl">{{ $requested }}</h2>
                </div>
                <div class="flex flex-col gap-1 px-2 py-2 text-center sm:text-left">
                    <p class="text-xs text-gray-400 sm:text-sm">Rejected</p>
                    <h2 class="text-xl font-semibold text-red-600 md:text-2xl">{{ $rejected }}</h2>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="w-full p-4 bg-white shadow-lg rounded-2xl sm:p-6" style="border: 2px solid #e0eaff;">
            <div class="flex flex-col items-start justify-between gap-3 mb-6 sm:flex-row sm:items-center">
                <div class="flex gap-2">
                    <button
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 transition border border-gray-200 rounded-lg hover:bg-gray-50">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        This month
                    </button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <select id="filter_status" class="border rounded-lg px-3 py-1.5 text-sm">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>

            <div class="-mx-4 overflow-x-auto sm:mx-0">
                <table class="w-full text-sm min-w-[800px]">
                    <thead>
                        <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
                            <th class="py-3 pl-4 pr-2 text-left sm:pl-2">Employee</th>
                            <th class="px-2 py-3 text-left">Role</th>
                            <th class="px-2 py-3 text-left">Request Date</th>
                            <th class="px-2 py-3 text-left">Original Schedule</th>
                            <th class="px-2 py-3 text-left">Requested Schedule</th>
                            <th class="px-2 py-3 text-left">Status</th>
                            <th class="py-3 pl-2 pr-4 text-center sm:pr-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr class="transition border-t border-gray-100 change-day-row hover:bg-blue-50/40"
                                data-status="{{ strtolower($item->change_day_status) }}">
                                <td class="py-3 pl-4 pr-2 sm:pl-2">
                                    <div class="flex items-center gap-3">
                                        {{-- Avatar / Foto Profil --}}
                                        <div class="flex-shrink-0">
                                            @php
                                                $fotoUrl =
                                                    $item->karyawan &&
                                                    $item->karyawan->foto_profil &&
                                                    \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                        $item->karyawan->foto_profil,
                                                    )
                                                        ? \Illuminate\Support\Facades\Storage::url(
                                                            $item->karyawan->foto_profil,
                                                        )
                                                        : 'https://ui-avatars.com/api/?background=0D8F81&color=fff&size=100&name=' .
                                                            urlencode($item->nama_karyawan);
                                            @endphp
                                            <img src="{{ $fotoUrl }}"
                                                class="object-cover w-10 h-10 border-2 border-blue-300 rounded-full"
                                                alt="{{ $item->nama_karyawan }}">
                                        </div>

                                        {{-- Employee Info --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-800 truncate">{{ $item->nama_karyawan }}</div>
                                            <div class="text-xs text-gray-400 truncate">{{ $item->karyawan->email ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-3 text-gray-700 whitespace-nowrap">
                                    <span class="capitalize">{{ $item->karyawan->role ?? '-' }}</span>
                                </td>
                                <td class="px-2 py-3 text-gray-700 whitespace-nowrap">
                                    {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-2 py-3 text-gray-700 whitespace-nowrap">
                                    {{ $item->change_day_tanggal_awal ? $item->change_day_tanggal_awal->format('d M Y') : '-' }}
                                </td>
                                <td class="px-2 py-3 font-semibold text-gray-700 whitespace-nowrap">
                                    {{ $item->change_day_tanggal_akhir ? $item->change_day_tanggal_akhir->format('d M Y') : '-' }}
                                </td>
                                <td class="px-2 py-3 whitespace-nowrap">
                                    <form action="{{ route('admin.absensi.update-status-change-day', $item->id) }}"
                                        method="POST" class="inline-block" id="form-{{ $item->id }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="is_change_day" value="1">
                                        <input type="hidden" name="change_day_note" id="note-input-{{ $item->id }}">
                                        <select name="change_day_status"
                                            data-current-status="{{ $item->change_day_status }}"
                                            onchange="updateChangeDayStatus({{ $item->id }}, this)"
                                            class="text-xs rounded-full py-1 px-3 border-0 focus:ring-2 focus:ring-blue-500 cursor-pointer
                                            @if ($item->change_day_status == 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($item->change_day_status == 'approved') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">

                                            <option value="pending"
                                                {{ $item->change_day_status == 'pending' ? 'selected' : '' }}>
                                                Pending
                                            </option>

                                            <option value="approved"
                                                {{ $item->change_day_status == 'approved' ? 'selected' : '' }}>
                                                Approved
                                            </option>

                                            <option value="rejected"
                                                {{ $item->change_day_status == 'rejected' ? 'selected' : '' }}>
                                                Rejected
                                            </option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <a class="text-blue-500 cursor-pointer hover:text-blue-700"
                                        onclick="showDetail({{ $item->id }})">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="paginationContainer" class="flex flex-wrap items-center justify-end gap-2 mt-6">
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/40">
        <div class="relative w-full max-w-xl mx-4">
            <div class="p-6 bg-white shadow-lg rounded-3xl">
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-blue-900">Detail Change Day</h3>
                    <button onclick="closeDetailModal()"
                        class="flex items-center justify-center w-8 h-8 text-xl rounded-full hover:bg-gray-100">
                        ✕
                    </button>
                </div>
                <div id="detailContent" class="mt-5 space-y-4 max-h-[70vh] overflow-y-auto"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function updateChangeDayStatus(id, selectElement) {
            const selectedStatus = selectElement.value;
            const currentStatus = selectElement.dataset.currentStatus || selectElement.getAttribute(
                'data-current-status');
            let note = '';

            // Extra warning when reverting from approved to rejected
            if (selectedStatus === 'rejected' && currentStatus === 'approved') {
                const {
                    isConfirmed: warnConfirmed
                } = await Swal.fire({
                    title: 'Revert approval?',
                    html: `<p class="text-sm text-gray-600">This request was already <strong>approved</strong>. Rejecting it will also <strong>remove the related attendance records</strong> that were created for this change day.</p>`,
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
                if (!warnConfirmed) {
                    location.reload();
                    return;
                }
            }

            // INPUT NOTE
            const {
                value: inputNote,
                isConfirmed
            } = await Swal.fire({
                title: `Change status to ${selectedStatus}?`,
                input: 'textarea',
                inputLabel: selectedStatus === 'rejected' ?
                    'Rejection reason' : 'Note (optional)',
                inputPlaceholder: selectedStatus === 'rejected' ?
                    'Enter rejection reason...' : 'Add a note...',
                inputAttributes: {
                    'aria-label': 'Type your note here'
                },
                showCancelButton: true,
                confirmButtonText: 'Save',
                cancelButtonText: 'Cancel',
                reverseButtons: true,

                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg mx-1',
                    cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg mx-1'
                },

                buttonsStyling: false,
                inputValidator: (value) => {
                    if (selectedStatus === 'rejected' && !value) {
                        return 'Rejection reason is required';
                    }
                }
            });

            if (!isConfirmed) {
                location.reload();
                return;
            }

            note = inputNote || '';
            document.getElementById(`note-input-${id}`).value = note;
            document.getElementById(`form-${id}`).submit();
        }

        function showDetail(id) {
            fetch(`/admin/absensi/${id}`)
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
                        `<a href="/storage/${data.attachment}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat file</a>` :
                        '-';

                    const note = data.change_day_alasan ?
                        data.change_day_alasan.replace(/\n/g, '<br>') :
                        '-';

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

                        <div class="pb-4 border-b">
                            <div class="flex items-start gap-4">
                                <div class="w-16 h-16 overflow-hidden border-2 border-blue-700 rounded-full shrink-0">
                                    <img
                                        src="${foto}"
                                        class="object-cover w-full h-full"
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
                                <p class="mb-1 text-gray-400">Request Date</p>
                                <p class="font-medium text-gray-700">${requestDate}</p>
                            </div>

                            <div>
                                <p class="mb-1 text-gray-400">Original Schedule</p>
                                <p class="font-medium text-gray-700">${originalDate}</p>
                            </div>

                            <div>
                                <p class="mb-1 text-gray-400">Requested Change</p>
                                <p class="font-medium text-gray-700">${requestChange}</p>
                            </div>

                            <div>
                                <p class="mb-1 text-gray-400">Attachment</p>
                                ${attachment}
                            </div>
                        </div>

                        <div>
                            <p class="mb-1 text-xs text-gray-500">Reason</p>
                            <div class="px-3 py-2 text-xs text-gray-600 border border-blue-500 rounded-md">
                                ${note}
                            </div>
                        </div>

                        <div>
                            <p class="mb-1 text-xs text-gray-500">Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded text-xs font-medium ${currentStatus.class}">
                                ${currentStatus.text}
                            </span>
                            ${
                                data.change_day_status === 'approved'
                                    ? `
                                                                                                                                                                                                            <p class="mt-1 text-xs text-gray-500">
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
                    alert('Failed to load data. Please try again.');
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('detailModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {

            const statusFilter = document.getElementById('filter_status');
            const rows = Array.from(document.querySelectorAll('.change-day-row'));
            const emptyRow = document.getElementById('emptyRow');
            const paginationContainer = document.getElementById('paginationContainer');

            const perPage = 10;

            let currentPage = 1;
            let filteredRows = [...rows];

            function applyFilters() {

                const status = statusFilter.value.toLowerCase();

                filteredRows = rows.filter(row => {

                    const rowStatus = row.dataset.status.toLowerCase();

                    const matchStatus = !status || rowStatus === status;

                    return matchStatus;
                });

                currentPage = 1;

                renderTable();
                renderPagination();
            }

            function renderTable() {

                rows.forEach(row => {
                    row.style.display = 'none';
                });

                const start = (currentPage - 1) * perPage;
                const end = start + perPage;

                filteredRows.slice(start, end).forEach(row => {
                    row.style.display = 'table-row';
                });

                if (emptyRow) {
                    emptyRow.style.display =
                        filteredRows.length === 0 ?
                        'table-row' :
                        'none';
                }
            }

            function renderPagination() {

                paginationContainer.innerHTML = '';

                const totalPages = Math.ceil(filteredRows.length / perPage);

                if (totalPages <= 1) return;

                // PREV BUTTON
                const prevBtn = document.createElement('button');

                prevBtn.innerHTML = '&laquo;';
                prevBtn.disabled = currentPage === 1;

                prevBtn.className =
                    'px-3 py-1.5 rounded-lg border text-sm bg-white hover:bg-gray-50 disabled:opacity-40';

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

                    btn.className =
                        `px-3 py-1.5 rounded-lg border text-sm transition ${
                        i === currentPage
                            ? 'bg-blue-600 text-white border-blue-600'
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                    }`;

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

                nextBtn.className =
                    'px-3 py-1.5 rounded-lg border text-sm bg-white hover:bg-gray-50 disabled:opacity-40';

                nextBtn.addEventListener('click', function() {

                    if (currentPage < totalPages) {

                        currentPage++;

                        renderTable();
                        renderPagination();
                    }
                });

                paginationContainer.appendChild(nextBtn);
            }

            // EVENT
            statusFilter.addEventListener('change', applyFilters);

            // INIT
            applyFilters();
        });
    </script>
@endpush
