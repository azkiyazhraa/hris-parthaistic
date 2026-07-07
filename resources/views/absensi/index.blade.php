@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">

        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">
                    Attendance
                </h1>
                <p class="text-sm text-gray-700/80">
                    Monitor Check-In, Check-Out and attendance history with ease.
                </p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        {{-- CARD SUMMARY --}}
        <div class="grid grid-cols-1 gap-5 mb-5 xl:grid-cols-3">
            <!-- ATTENDANCE STATUS -->
            <div class="p-5 bg-white border border-gray-100 shadow-sm xl:col-span-2 rounded-3xl md:p-7">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">
                            Today's Attendance
                        </h2>

                        <p class="mt-1 text-sm text-gray-400">
                            Real-time attendance status
                        </p>
                    </div>

                    <div class="flex items-center justify-center text-blue-600 h-11 w-11 rounded-2xl bg-blue-50">
                        ⏱️
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <!-- STATUS -->
                    <div class="p-5 bg-gray-50 rounded-2xl">
                        <p class="mb-2 text-sm text-gray-500">
                            Current Status
                        </p>
                        @php
                            $isCheckedIn = $absensiToday && $absensiToday->jam_masuk && !$absensiToday->jam_pulang;
                            $isOnBreak =
                                $absensiToday &&
                                $absensiToday->breaks
                                    ->where('break_start', '!=', null)
                                    ->where('break_end', null)
                                    ->first()
                                    ? true
                                    : false;
                        @endphp

                        <h2 class="text-2xl font-bold
                            @if (!$absensiToday) text-gray-400
                            @elseif ($absensiToday->status_kehadiran === 'change_day') text-indigo-600
                            @elseif ($absensiToday->status_kehadiran === 'leave') text-purple-600
                            @elseif ($isOnBreak) text-blue-500
                            @elseif ($absensiToday->jam_pulang) text-green-600
                            @else text-yellow-500 @endif"
                            id="attendanceStatus">

                            @if (!$absensiToday)
                                Not Checked In
                            @elseif ($absensiToday->status_kehadiran === 'change_day')
                                Change Day Off
                            @elseif ($absensiToday->status_kehadiran === 'leave')
                                On Leave
                            @elseif ($isOnBreak)
                                On Break
                            @elseif ($absensiToday->jam_pulang)
                                Finished
                            @else
                                Working
                            @endif

                        </h2>
                    </div>

                    <!-- CHECK IN -->
                    <div class="p-5 bg-gray-50 rounded-2xl">
                        <p class="mb-2 text-sm text-gray-500">
                            Check-In Time
                        </p>

                        <h2 class="text-2xl font-bold {{ $absensiToday && $absensiToday->jam_masuk ? 'text-blue-900' : 'text-gray-400' }} font-mono"
                            id="attendanceTime">
                            @if ($absensiToday && $absensiToday->jam_masuk)
                                {{ \Carbon\Carbon::parse($absensiToday->jam_masuk)->format('H:i:s') }}
                            @else
                                --:--
                            @endif
                        </h2>
                    </div>

                    <!-- WORKING HOURS -->
                    <div class="p-5 bg-gray-50 rounded-2xl">
                        <p class="mb-2 text-sm text-gray-500">
                            Working Hours
                        </p>

                        <h2 class="font-mono text-2xl font-bold text-blue-900" id="attendanceWorking">
                            @if ($absensiToday && $absensiToday->jam_pulang)
                                @php
                                    $hours = floor($absensiToday->total_jam_kerja);
                                    $minutes = floor(($absensiToday->total_jam_kerja - $hours) * 60);
                                    $seconds = floor((($absensiToday->total_jam_kerja - $hours) * 60 - $minutes) * 60);
                                    $formattedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                                @endphp
                                {{ $formattedTime }}
                            @else
                                00:00:00
                            @endif
                        </h2>
                    </div>
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="p-5 bg-white border border-gray-100 shadow-sm rounded-3xl md:p-7">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">
                            Attendance Summary
                        </h2>

                        <p class="mt-1 text-sm text-gray-400">
                            Monthly overview
                        </p>
                    </div>

                    <div class="flex items-center justify-center text-blue-600 h-11 w-11 rounded-2xl bg-blue-50">
                        📊
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- PRESENT -->
                    <div class="p-4 bg-green-50 rounded-2xl">
                        <p class="mb-1 text-sm text-gray-500">
                            Present
                        </p>

                        <h2 id="presentCount" class="text-3xl font-bold text-green-900">
                            {{ $monthAbcense->where('status_kehadiran', 'present')->count() }}
                        </h2>
                    </div>

                    <!-- CHANGE DAY -->
                    <div class="p-4 bg-indigo-50 rounded-2xl">
                        <p class="mb-1 text-sm text-gray-500">
                            Change Day
                        </p>

                        <h2 id="changeDayCount" class="text-3xl font-bold text-indigo-600">
                            {{ $monthAbcense->where('status_kehadiran', 'change_day')->count() }}
                        </h2>
                    </div>

                    <!-- LEAVE -->
                    <div class="p-4 bg-purple-50 rounded-2xl">
                        <p class="mb-1 text-sm text-gray-500">
                            Leave
                        </p>

                        <h2 id="leaveCount" class="text-3xl font-bold text-purple-600">
                            {{ $monthAbcense->where('status_kehadiran', 'leave')->count() }}
                        </h2>
                    </div>

                    <!-- PENDING -->
                    <div class="p-4 bg-yellow-50 rounded-2xl">
                        <p class="mb-1 text-sm text-gray-500">
                            Pending
                        </p>

                        <h2 id="pendingCount" class="text-3xl font-bold text-yellow-600">
                            {{ $monthAbcense->where('status_kehadiran', 'pending')->count() }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>


        <div class="p-4 bg-white shadow rounded-2xl md:p-6">

            <div class="flex flex-col gap-4 mb-4 md:flex-row md:items-center md:justify-between">

                <!-- TITLE -->
                <div>
                    <span class="font-semibold text-gray-800">
                        Employees Attendance
                    </span>
                </div>

                <!-- FILTERS -->
                <div class="flex flex-col w-full gap-3 sm:flex-row md:w-auto">

                    <!-- STATUS -->
                    <div class="w-full sm:w-52">
                        <select id="filterStatus"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">

                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="present">Present</option>
                            <option value="change_day">Change Day</option>
                            <option value="leave">Leave</option>
                        </select>
                    </div>

                    <!-- MONTH -->
                    <div class="w-full sm:w-52">
                        <select id="filterMonth"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">

                            <option value="">All Month</option>
                            <option value="january">January</option>
                            <option value="february">February</option>
                            <option value="march">March</option>
                            <option value="april">April</option>
                            <option value="may">May</option>
                            <option value="june">June</option>
                            <option value="july">July</option>
                            <option value="august">August</option>
                            <option value="september">September</option>
                            <option value="october">October</option>
                            <option value="november">November</option>
                            <option value="december">December</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                    <thead>
                        <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
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
                                data-status="{{ strtolower($item->status_kehadiran) }}"
                                data-month="{{ strtolower($item->tanggal?->format('F')) }}">

                                <td class="py-3">
                                    {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                                </td>

                                <td class="py-3">
                                    {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '-' }}
                                </td>

                                <td class="py-3">
                                    {{ $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : '-' }}
                                </td>

                                <td class="py-3">
                                    @php
                                        $badgeClass = match ($item->status_kehadiran) {
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'present' => 'bg-green-100 text-green-700',
                                            'change_day' => 'bg-blue-100 text-blue-700',
                                            'leave' => 'bg-purple-100 text-purple-700',
                                            default => 'bg-red-100 text-red-700',
                                        };
                                    @endphp

                                    @php
                                        $statusLabel = match ($item->status_kehadiran) {
                                            'pending' => 'Pending',
                                            'present' => 'Present',
                                            'change_day' => 'Change Day',
                                            'leave' => 'Leave',
                                            'absent' => 'Absent',
                                            default => ucfirst($item->status_kehadiran),
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $badgeClass }}">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="py-3">
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
                        <tr id="emptySearchRow" style="display:none;">
                            <td colspan="8" class="py-4 text-center text-gray-400">
                                No attendance records found.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div id="paginationContainer" class="flex items-center justify-end gap-2 mt-6">
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
        function showDetail(id) {
            fetch(`/absensi/${id}`)
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
                        case 'pending':
                            statusClass = 'bg-yellow-100 text-yellow-700';
                            break;
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
            // =========================
            // ELEMENTS
            // =========================
            const statusFilter = document.getElementById('filterStatus');
            const monthFilter = document.getElementById('filterMonth');

            const allRows = [...document.querySelectorAll('.attendance-row')];
            const emptyRow = document.getElementById('emptySearchRow');
            const paginationContainer = document.getElementById('paginationContainer');

            // =========================
            // STATE
            // =========================
            const perPage = 10;
            let currentPage = 1;
            let filteredRows = [];

            // =========================
            // FILTERING
            // =========================
            function filterRows() {
                const selectedStatus = statusFilter.value.toLowerCase();
                const selectedMonth = monthFilter.value.toLowerCase();

                filteredRows = allRows.filter(row => {
                    const status = row.dataset.status || '';
                    const month = row.dataset.month || '';

                    const matchStatus =
                        selectedStatus === '' || status === selectedStatus;

                    const matchMonth =
                        selectedMonth === '' || month === selectedMonth;

                    return (
                        matchStatus &&
                        matchMonth
                    );
                });

                currentPage = 1;
                renderTable();
                renderPagination();
            }

            // =========================
            // TABLE RENDER
            // =========================
            function renderTable() {
                allRows.forEach(row => {
                    row.style.display = 'none';
                });

                const start = (currentPage - 1) * perPage;
                const end = start + perPage;
                const paginatedRows = filteredRows.slice(start, end);

                paginatedRows.forEach(row => {
                    row.style.display = 'table-row';
                });

                if (emptyRow) {
                    emptyRow.style.display =
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
                const totalPages = Math.ceil(filteredRows.length / perPage);
                if (totalPages <= 1) return;

                // PREV BUTTON
                paginationContainer.appendChild(
                    createButton('«', currentPage - 1, currentPage === 1)
                );

                // PAGE BUTTONS
                for (let i = 1; i <= totalPages; i++) {
                    const btn = createButton(i, i);
                    if (i === currentPage) {
                        btn.classList.add(
                            'bg-blue-600',
                            'text-white',
                            'border-blue-600'
                        );
                    } else {
                        btn.classList.add(
                            'bg-white',
                            'text-gray-700',
                            'hover:bg-gray-50'
                        );
                    }

                    paginationContainer.appendChild(btn);
                }

                // NEXT BUTTON
                paginationContainer.appendChild(
                    createButton('»', currentPage + 1, currentPage === totalPages)
                );
            }

            // =========================
            // BUTTON HELPER
            // =========================
            function createButton(label, page, disabled = false) {
                const button = document.createElement('button');
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
            // EVENT LISTENERS
            // =========================
            statusFilter.addEventListener('change', filterRows);
            monthFilter.addEventListener('change', filterRows);

            // =========================
            // INITIAL
            // =========================
            filterRows();
        });

        // =========================
        // REAL-TIME WORKING HOURS & STATUS FOR ATTENDANCE PAGE
        // =========================
        document.addEventListener('DOMContentLoaded', function() {
            let syncInterval = null;
            let checkInTime = null;
            let breaksData = [];
            let isCurrentlyOnBreak = false;
            let displayUpdateInterval = null;

            @if ($absensiToday && !$absensiToday->jam_pulang)
                // Parse check-in time
                const checkInDate = "{{ $absensiToday->tanggal->format('Y-m-d') }}";
                const checkInTimeStr = "{{ $absensiToday->jam_masuk }}";
                checkInTime = new Date(`${checkInDate}T${checkInTimeStr}`);

                // Fungsi untuk menghitung total break duration dalam detik
                function calculateTotalBreakSeconds() {
                    let totalBreakSeconds = 0;
                    const now = new Date();

                    if (!breaksData || breaksData.length === 0) return 0;

                    breaksData.forEach(breakItem => {
                        if (breakItem.start) {
                            const breakStart = new Date(breakItem.start);
                            let breakEnd = null;

                            if (breakItem.end) {
                                breakEnd = new Date(breakItem.end);
                            } else if (breakItem.is_active) {
                                // Break sedang berlangsung, hitung sampai sekarang
                                breakEnd = now;
                            }

                            if (breakEnd && breakEnd > breakStart) {
                                totalBreakSeconds += Math.floor((breakEnd - breakStart) / 1000);
                            }
                        }
                    });

                    return totalBreakSeconds;
                }

                // Fungsi untuk menghitung working hours
                function calculateWorkingHours() {
                    const now = new Date();

                    // Total detik dari check-in sampai sekarang
                    const totalSeconds = Math.floor((now - checkInTime) / 1000);

                    if (totalSeconds < 0) return {
                        hours: 0,
                        minutes: 0,
                        seconds: 0,
                        formatted: '00:00:00'
                    };

                    // Kurangi dengan total break seconds
                    const breakSeconds = calculateTotalBreakSeconds();
                    const workingSeconds = Math.max(0, totalSeconds - breakSeconds);

                    const hours = Math.floor(workingSeconds / 3600);
                    const minutes = Math.floor((workingSeconds % 3600) / 60);
                    const seconds = workingSeconds % 60;

                    return {
                        hours: hours,
                        minutes: minutes,
                        seconds: seconds,
                        formatted: `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
                    };
                }

                // Fungsi untuk update display di halaman attendance
                function updateAttendanceDisplay() {
                    const workingElement = document.getElementById('attendanceWorking');
                    const statusElement = document.getElementById('attendanceStatus');

                    if (workingElement) {
                        const working = calculateWorkingHours();
                        workingElement.innerText = working.formatted;

                        // Update title attribute untuk tooltip
                        const breakSeconds = calculateTotalBreakSeconds();
                        if (breakSeconds > 0) {
                            const breakHours = Math.floor(breakSeconds / 3600);
                            const breakMinutes = Math.floor((breakSeconds % 3600) / 60);
                            const breakSecs = breakSeconds % 60;
                            workingElement.title =
                                `Break time: ${String(breakHours).padStart(2, '0')}:${String(breakMinutes).padStart(2, '0')}:${String(breakSecs).padStart(2, '0')}`;
                        } else {
                            workingElement.title = '';
                        }
                    }

                    // Update status if needed
                    if (statusElement) {
                        const shouldShowOnBreak = isCurrentlyOnBreak;
                        const currentText = statusElement.textContent;

                        if (shouldShowOnBreak && currentText !== 'On Break') {
                            statusElement.textContent = 'On Break';
                            statusElement.className = 'text-2xl font-bold text-blue-500';
                        } else if (!shouldShowOnBreak && currentText === 'On Break') {
                            statusElement.textContent = 'Working';
                            statusElement.className = 'text-2xl font-bold text-yellow-500';
                        } else if (!shouldShowOnBreak && !$absensiToday?.jam_pulang && currentText === 'Finished') {
                            // Do nothing
                        } else if (!shouldShowOnBreak && currentText !== 'Working' && currentText !== 'Finished' &&
                            currentText !== 'Not Checked In') {
                            statusElement.textContent = 'Working';
                            statusElement.className = 'text-2xl font-bold text-yellow-500';
                        }
                    }
                }

                // Fetch breaks data from server
                function fetchAttendanceBreaksData() {
                    $.ajax({
                        url: "{{ route('attendance.status') }}",
                        type: "GET",
                        dataType: 'json',
                        success: function(response) {
                            if (response.absensi && !response.absensi.jam_pulang) {
                                // Update breaks data
                                if (response.breaks && Array.isArray(response.breaks)) {
                                    breaksData = response.breaks;
                                }

                                // Update break status
                                isCurrentlyOnBreak = response.isOnBreak || false;

                                // Update check-in time if needed
                                if (response.absensi.jam_masuk && !checkInTime) {
                                    const serverCheckIn = new Date(
                                        `${response.absensi.tanggal}T${response.absensi.jam_masuk}`);
                                    if (!isNaN(serverCheckIn.getTime())) {
                                        checkInTime = serverCheckIn;
                                    }
                                }

                                // Update display
                                updateAttendanceDisplay();
                            } else if (response.absensi && response.absensi.jam_pulang) {
                                // User sudah checkout, stop intervals
                                if (displayUpdateInterval) {
                                    clearInterval(displayUpdateInterval);
                                    displayUpdateInterval = null;
                                }
                                if (syncInterval) {
                                    clearInterval(syncInterval);
                                    syncInterval = null;
                                }

                                // Update final working hours
                                const workingElement = document.getElementById('attendanceWorking');
                                if (workingElement && response.working_hours) {
                                    workingElement.innerText = response.working_hours;
                                }
                            }
                        },
                        error: function(error) {
                            console.error('Error fetching attendance status:', error);
                        }
                    });
                }

                // Initial sync
                fetchAttendanceBreaksData();

                // Update display setiap detik (1000 ms)
                displayUpdateInterval = setInterval(updateAttendanceDisplay, 1000);

                // Sync dengan server setiap 10 detik untuk mendapatkan data break terbaru
                syncInterval = setInterval(fetchAttendanceBreaksData, 10000);

                // Cleanup intervals saat page unload
                window.addEventListener('beforeunload', function() {
                    if (displayUpdateInterval) {
                        clearInterval(displayUpdateInterval);
                    }
                    if (syncInterval) {
                        clearInterval(syncInterval);
                    }
                });
            @endif
        });
    </script>
@endpush
