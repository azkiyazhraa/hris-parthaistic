@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">

        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">
                    Attendance Management
                </h1>
                <p class="text-sm text-gray-700/80">
                    Manage employee attendance and change day requests.
                </p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="py-4">
            <div class="mb-6 overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5">

                    <div class="p-5 text-center border-b border-r border-gray-100 lg:border-b-0">
                        <p class="mb-1 text-xs tracking-wide text-gray-400 uppercase">Total</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ $statistics['total'] ?? 0 }}
                        </p>
                    </div>

                    <div class="p-5 text-center border-b border-r border-gray-100 lg:border-b-0">
                        <p class="mb-1 text-xs tracking-wide text-green-600 uppercase">Present</p>
                        <p class="text-2xl font-semibold text-green-600">
                            {{ $statistics['present'] ?? 0 }}
                        </p>
                    </div>

                    <div class="p-5 text-center border-b border-r border-gray-100 lg:border-b-0">
                        <p class="mb-1 text-xs tracking-wide text-yellow-600 uppercase">Pending</p>
                        <p class="text-2xl font-semibold text-yellow-600">
                            {{ $statistics['pending'] ?? 0 }}
                        </p>
                    </div>

                    <div class="p-5 text-center border-b border-r border-gray-100 lg:border-b-0">
                        <p class="mb-1 text-xs tracking-wide text-blue-600 uppercase">Change Day</p>
                        <p class="text-2xl font-semibold text-blue-600">
                            {{ $statistics['change_day'] ?? 0 }}
                        </p>
                    </div>

                    <div class="p-5 text-center border-b border-r border-gray-100 lg:border-b-0">
                        <p class="mb-1 text-xs tracking-wide text-purple-600 uppercase">Leave</p>
                        <p class="text-2xl font-semibold text-purple-600">
                            {{ $statistics['leave'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white shadow rounded-2xl md:p-6">
                <div class="flex flex-col gap-4 mb-4 md:flex-row md:items-center md:justify-between">

                    <!-- TITLE -->
                    <div>
                        <span class="text-base font-semibold text-gray-800">
                            Employees Attendance
                        </span>
                    </div>

                    <!-- FILTER AREA -->
                    <div class="flex flex-col w-full gap-3 sm:flex-row md:w-auto">

                        <!-- SEARCH -->
                        <div class="w-full sm:flex-1 md:w-72">
                            <input type="text" id="searchAttendance" placeholder="Search Employees..."
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <!-- STATUS -->
                        <div class="w-full sm:w-52">
                            <select id="filterStatus"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">

                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="present">Present</option>
                                <option value="change_day">Change Day</option>
                                <option value="leave">Leave</option>
                                <option value="absent">Absent</option>
                            </select>
                        </div>

                        <!-- DATE FROM -->
                        <div class="w-full sm:w-44">
                            <input type="date" id="filterDateFrom"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                title="From date">
                        </div>

                        <!-- DATE TO -->
                        <div class="w-full sm:w-44">
                            <input type="date" id="filterDateTo"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                title="To date">
                        </div>

                        <!-- RESET DATE -->
                        <div class="w-full sm:w-auto">
                            <button id="resetDateFilter"
                                class="w-full sm:w-auto border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-500 hover:bg-gray-50 focus:outline-none whitespace-nowrap">
                                Reset Date
                            </button>
                        </div>

                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                        <thead>
                            <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
                                <th class="pb-3 text-left whitespace-nowrap">Name</th>
                                <th class="pb-3 text-left whitespace-nowrap">Role</th>
                                <th class="pb-3 text-left whitespace-nowrap">Date</th>
                                <th class="pb-3 text-left whitespace-nowrap">Check-In</th>
                                <th class="pb-3 text-left whitespace-nowrap">Check-Out</th>
                                <th class="pb-3 text-left whitespace-nowrap">Status</th>
                                <th class="pb-3 text-left whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($absensi as $item)
                                <tr class="transition border-b border-gray-100 attendance-row hover:bg-blue-50/40"
                                    data-search="{{ strtolower(($item->karyawan->nama_lengkap ?? '') . ' ' . ($item->karyawan->email ?? '') . ' ' . ($item->karyawan->nip ?? '')) }}"
                                    data-status="{{ strtolower($item->is_change_day ? $item->change_day_status : $item->status_kehadiran) }}"
                                    data-date="{{ $item->tanggal ? $item->tanggal->format('Y-m-d') : '' }}">
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
                                                    onerror="this.src='https://ui-avatars.com/api/?background=2563EB&color=fff&size=100&name={{ urlencode($item->nama_lengkap) }}'">
                                            </div>
                                            <div>
                                                <span
                                                    class="font-medium text-gray-800">{{ $item->karyawan->nama_lengkap }}</span><br>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3">
                                        @php
                                            $statusClass = match ($item->karyawan->role) {
                                                'admin' => 'bg-blue-100 text-blue-800',
                                                'hr' => 'bg-emerald-100 text-emerald-800',
                                                'karyawan' => 'bg-violet-100 text-violet-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp

                                        <span class="{{ $statusClass }} py-1 px-3 rounded-full text-xs font-medium">
                                            {{ ucfirst($item->karyawan->role) }}
                                        </span>
                                    </td>



                                    <td class="py-3">
                                        @if ($item->is_change_day)
                                            @if ($item->change_day_tanggal_awal && $item->change_day_tanggal_akhir)
                                                @if ($item->change_day_tanggal_awal->format('Y-m-d') == $item->change_day_tanggal_akhir->format('Y-m-d'))
                                                    {{ $item->change_day_tanggal_awal->format('d M Y') }}
                                                @else
                                                    {{ $item->change_day_tanggal_awal->format('d M Y') }} -
                                                    {{ $item->change_day_tanggal_akhir->format('d M Y') }}
                                                @endif
                                            @else
                                                {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                                            @endif
                                        @else
                                            {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                                        @endif
                                    </td>

                                    <td class="py-3">
                                        @if ($item->is_change_day)
                                            {{ $item->change_day_jam_mulai ? substr($item->change_day_jam_mulai, 0, 5) : '-' }}
                                        @else
                                            {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '-' }}
                                        @endif
                                    </td>

                                    <td class="py-3">
                                        @if ($item->is_change_day)
                                            {{ $item->change_day_jam_selesai ? substr($item->change_day_jam_selesai, 0, 5) : '-' }}
                                        @else
                                            {{ $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : '-' }}
                                        @endif
                                    </td>

                                    <td class="py-3">
                                        @php
                                            $statusStyles = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'present' => 'bg-green-100 text-green-800',
                                                'change_day' => 'bg-blue-100 text-blue-800',
                                                'leave' => 'bg-purple-100 text-purple-800',
                                                'absent' => 'bg-red-100 text-red-800',
                                            ];
                                            $currentStatusClass =
                                                $statusStyles[$item->status_kehadiran] ?? 'bg-gray-100 text-gray-800';
                                            $isToday = $item->tanggal && $item->tanggal->isToday();
                                            $statusLabel = match ($item->status_kehadiran) {
                                                'pending' => 'Pending',
                                                'present' => 'Present',
                                                'change_day' => 'Change Day',
                                                'leave' => 'Leave',
                                                'absent' => 'Absent',
                                                default => ucfirst($item->status_kehadiran),
                                            };
                                        @endphp

                                        @if ($isToday)
                                            <div title="Editable tomorrow">
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs rounded-full py-1 px-3 cursor-not-allowed opacity-75 {{ $currentStatusClass }}">
                                                    {{ $statusLabel }}
                                                    <svg class="w-3 h-3 opacity-60" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </span>
                                                <p class="text-xs text-gray-400 mt-0.5">Editable tomorrow</p>
                                            </div>
                                        @else
                                            <form action="{{ route('admin.absensi.update-status-absensi', $item->id) }}"
                                                method="POST" class="inline-block" id="form-{{ $item->id }}">
                                                @csrf
                                                @method('PUT')
                                                <select name="status_kehadiran"
                                                    data-current="{{ $item->status_kehadiran }}"
                                                    onchange="updateStatus({{ $item->id }}, this)"
                                                    class="text-xs rounded-full py-1 px-3 border-0 focus:ring-2 focus:ring-blue-500 {{ $currentStatusClass }}"
                                                    id="select-{{ $item->id }}">
                                                    <option value="pending"
                                                        {{ $item->status_kehadiran == 'pending' ? 'selected' : '' }}>
                                                        Pending</option>
                                                    <option value="present"
                                                        {{ $item->status_kehadiran == 'present' ? 'selected' : '' }}>
                                                        Present</option>
                                                    <option value="change_day"
                                                        {{ $item->status_kehadiran == 'change_day' ? 'selected' : '' }}>
                                                        Change Day</option>
                                                    <option value="leave"
                                                        {{ $item->status_kehadiran == 'leave' ? 'selected' : '' }}>Leave
                                                    </option>
                                                    <option value="absent"
                                                        {{ $item->status_kehadiran == 'absent' ? 'selected' : '' }}>Absent
                                                    </option>
                                                </select>
                                            </form>
                                        @endif
                                    </td>

                                    <td class="py-3">
                                        <a class="text-blue-500 cursor-pointer hover:text-blue-700"
                                            onclick="showDetail({{ $item->id }})">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr id="emptySearchRow" style="display:none;">
                                <td colspan="8" class="py-4 text-center text-gray-400">
                                    Data Not Found
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div id="paginationContainer" class="flex justify-end gap-1 mt-4"></div>

                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/40">
        <div class="relative w-full max-w-xl p-4">
            <div class="p-6 bg-white shadow-lg rounded-3xl">
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-blue-900">Detail Attendance</h3>
                    <button onclick="closeDetailModal()"
                        class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100">✕</button>
                </div>
                <div id="detailContent" class="mt-5 space-y-5"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const statusStyles = {
            pending: 'bg-yellow-100 text-yellow-800',
            present: 'bg-green-100 text-green-800',
            change_day: 'bg-blue-100 text-blue-800',
            leave: 'bg-purple-100 text-purple-800',
            absent: 'bg-red-100 text-red-800',
        };

        function updateStatus(id, selectEl) {
            const previousStatus = selectEl.dataset.current;
            const newStatus = selectEl.value;

            Swal.fire({
                title: 'Change absence status?',
                text: 'Changes will be saved immediately.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg ml-2',
                    cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg'
                }
            }).then((result) => {
                if (!result.isConfirmed) {
                    selectEl.value = previousStatus;
                    return;
                }

                fetch(`/admin/absensi/${id}/status-absensi`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            status_kehadiran: newStatus
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            // Update the select's data-current to the new value
                            selectEl.dataset.current = newStatus;

                            // Update the select's color class
                            const allStatusClasses = Object.values(statusStyles).join(' ').split(' ');
                            selectEl.classList.remove(...allStatusClasses);
                            if (statusStyles[newStatus]) {
                                selectEl.classList.add(...statusStyles[newStatus].split(' '));
                            }

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message ?? 'Status updated successfully',
                                showConfirmButton: false,
                                timer: 2500,
                                timerProgressBar: true,
                            });
                        } else {
                            selectEl.value = previousStatus;
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: data.message ?? 'An error occurred.',
                            });
                        }
                    })
                    .catch(() => {
                        selectEl.value = previousStatus;
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Error',
                            text: 'Could not reach the server.'
                        });
                    });
            });
        }

        function updateChangeDayStatus(id) {
            Swal.fire({
                title: 'Change status change day?',
                text: 'Changes will be saved immediately.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg ml-2',
                    cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`form-change-${id}`).submit();
                }
            });
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

                    const checkIn = data.jam_masuk ? data.jam_masuk.substring(0, 5) : '-';
                    const checkOut = data.jam_pulang ? data.jam_pulang.substring(0, 5) : '-';
                    const workingHours = data.total_jam_kerja ? `${data.total_jam_kerja} hr` : '-';

                    const date = data.tanggal ?
                        new Date(data.tanggal).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) :
                        '-';

                    const attachment = data.attachment ?
                        `<a href="/storage/${data.attachment}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat file</a>` :
                        '-';

                    const note = data.keterangan ?
                        data.keterangan.replace(/\n/g, '<br>') :
                        '-';

                    let statusClass = 'bg-gray-100 text-gray-700';

                    switch ((data.status_kehadiran || '').toLowerCase()) {
                        case 'present':
                            statusClass = 'bg-emerald-100 text-emerald-700';
                            break;
                        case 'change_day':
                            statusClass = 'bg-blue-100 text-blue-700';
                            break;
                        case 'leave':
                            statusClass = 'bg-purple-100 text-purple-700';
                            break;
                        case 'change day pending':
                            statusClass = 'bg-yellow-100 text-yellow-700';
                            break;
                        case 'absent':
                            statusClass = 'bg-red-100 text-red-700';
                            break;
                    }

                    const statusText = data.status_kehadiran ?
                        data.status_kehadiran.charAt(0).toUpperCase() + data.status_kehadiran.slice(1).toLowerCase() :
                        '-';

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

                        <div class="grid grid-cols-5 gap-3 text-xs">
                            <div>
                                <p class="mb-1 text-gray-400">Check In</p>
                                <p class="font-medium text-gray-700">${checkIn}</p>
                            </div>

                            <div>
                                <p class="mb-1 text-gray-400">Check Out</p>
                                <p class="font-medium text-gray-700">${checkOut}</p>
                            </div>

                            <div>
                                <p class="mb-1 text-gray-400">Working Hours</p>
                                <p class="font-medium text-gray-700">${workingHours}</p>
                            </div>

                            <div>
                                <p class="mb-1 text-gray-400">Date</p>
                                <p class="font-medium text-gray-700">${date}</p>
                            </div>

                            <div>
                                <p class="mb-1 text-gray-400">Attachment</p>
                                ${attachment}
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
                            <span class="inline-flex px-3 py-1 rounded-md text-xs font-medium ${statusClass}">
                                ${statusText}
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
            const searchInput = document.getElementById('searchAttendance');
            const statusFilter = document.getElementById('filterStatus');
            const dateFromInput = document.getElementById('filterDateFrom');
            const dateToInput = document.getElementById('filterDateTo');
            const resetDateBtn = document.getElementById('resetDateFilter');
            const allRows = Array.from(document.querySelectorAll('.attendance-row'));
            const emptyRow = document.getElementById('emptySearchRow');
            const paginationContainer = document.getElementById('paginationContainer');

            const perPage = 10;
            let currentPage = 1;
            let filteredRows = [...allRows];

            function applyFilters() {
                const keyword = searchInput.value.toLowerCase().trim();
                const status = statusFilter.value.toLowerCase().trim();
                const dateFrom = dateFromInput.value;
                const dateTo = dateToInput.value;

                filteredRows = allRows.filter(row => {
                    const searchText = (row.dataset.search || '').toLowerCase();
                    const rowStatus = (row.dataset.status || '').toLowerCase();
                    const rowDate = row.dataset.date || '';

                    const matchKeyword = !keyword || searchText.includes(keyword);
                    const matchStatus = !status || rowStatus === status;
                    const matchDateFrom = !dateFrom || rowDate >= dateFrom;
                    const matchDateTo = !dateTo || rowDate <= dateTo;

                    return matchKeyword && matchStatus && matchDateFrom && matchDateTo;
                });

                currentPage = 1;
                renderTable();
                renderPagination();
            }

            resetDateBtn.addEventListener('click', function() {
                dateFromInput.value = '';
                dateToInput.value = '';
                applyFilters();
            });

            function renderTable() {
                allRows.forEach(row => {
                    row.style.display = 'none';
                });

                const start = (currentPage - 1) * perPage;
                const end = start + perPage;

                filteredRows.slice(start, end).forEach(row => {
                    row.style.display = 'table-row';
                });

                if (emptyRow) {
                    emptyRow.style.display = filteredRows.length === 0 ?
                        'table-row' :
                        'none';
                }
            }

            function renderPagination() {
                paginationContainer.innerHTML = '';

                const totalPages = Math.ceil(filteredRows.length / perPage);

                if (totalPages <= 1) return;

                const prevBtn = document.createElement('button');
                prevBtn.innerHTML = '&laquo;';
                prevBtn.disabled = currentPage === 1;
                prevBtn.className = 'px-3 py-1 rounded border text-sm disabled:opacity-40';

                prevBtn.addEventListener('click', function() {
                    if (currentPage > 1) {
                        currentPage--;
                        renderTable();
                        renderPagination();
                    }
                });

                paginationContainer.appendChild(prevBtn);

                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement('button');

                    btn.textContent = i;
                    btn.className =
                        `px-3 py-1 rounded border text-sm ${
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

                const nextBtn = document.createElement('button');
                nextBtn.innerHTML = '&raquo;';
                nextBtn.disabled = currentPage === totalPages;
                nextBtn.className = 'px-3 py-1 rounded border text-sm disabled:opacity-40';

                nextBtn.addEventListener('click', function() {
                    if (currentPage < totalPages) {
                        currentPage++;
                        renderTable();
                        renderPagination();
                    }
                });

                paginationContainer.appendChild(nextBtn);
            }

            searchInput.addEventListener('input', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
            dateFromInput.addEventListener('change', applyFilters);
            dateToInput.addEventListener('change', applyFilters);

            // Default: show yesterday's records
            const yesterday = new Date();
            yesterday.setDate(yesterday.getDate() - 1);
            const yStr = yesterday.toISOString().split('T')[0];
            dateFromInput.value = yStr;
            dateToInput.value = yStr;

            applyFilters();
        });
    </script>
@endpush
