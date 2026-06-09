@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">

        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">
                    Attendance Management
                </h1>
                <p class="text-gray-700/80 text-sm">
                    Manage employee attendance and change day requests.
                </p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="py-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5">

                    <div class="p-5 border-b lg:border-b-0 border-r border-gray-100 text-center">
                        <p class="text-xs text-gray-400 mb-1 uppercase tracking-wide">Total</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            {{ $statistics['total'] ?? 0 }}
                        </p>
                    </div>

                    <div class="p-5 border-b lg:border-b-0 border-r border-gray-100 text-center">
                        <p class="text-xs text-green-600 mb-1 uppercase tracking-wide">Present</p>
                        <p class="text-2xl font-semibold text-green-600">
                            {{ $statistics['hadir'] ?? 0 }}
                        </p>
                    </div>

                    <div class="p-5 border-b lg:border-b-0 border-r border-gray-100 text-center">
                        <p class="text-xs text-yellow-600 mb-1 uppercase tracking-wide">Pending</p>
                        <p class="text-2xl font-semibold text-yellow-600">
                            {{ $statistics['pending'] ?? 0 }}
                        </p>
                    </div>

                    <div class="p-5 border-b lg:border-b-0 border-r border-gray-100 text-center">
                        <p class="text-xs text-purple-600 mb-1 uppercase tracking-wide">Permission</p>
                        <p class="text-2xl font-semibold text-purple-600">
                            {{ $statistics['izin'] ?? 0 }}
                        </p>
                    </div>

                    <div class="p-5 border-b lg:border-b-0 border-r border-gray-100 text-center">
                        <p class="text-xs text-red-600 mb-1 uppercase tracking-wide">Sick</p>
                        <p class="text-2xl font-semibold text-red-600">
                            {{ $statistics['sakit'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow p-4 md:p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-4">

                    <!-- TITLE -->
                    <div>
                        <span class="font-semibold text-gray-800 text-base">
                            Employees Attendance
                        </span>
                    </div>

                    <!-- FILTER AREA -->
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">

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
                                <option value="hadir">Present</option>
                                <option value="izin">Permission</option>
                                <option value="sakit">Sick</option>
                                <option value="alpha">Alpha</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                        <thead>
                            <tr class="text-gray-400 font-medium text-xs uppercase tracking-wide border-b">
                                <th class="text-left pb-3 whitespace-nowrap">Name</th>
                                <th class="text-left pb-3 whitespace-nowrap">Role</th>
                                <th class="text-left pb-3 whitespace-nowrap">Type</th>
                                <th class="text-left pb-3 whitespace-nowrap">Date</th>
                                <th class="text-left pb-3 whitespace-nowrap">Check-In</th>
                                <th class="text-left pb-3 whitespace-nowrap">Check-Out</th>
                                <th class="text-left pb-3 whitespace-nowrap">Status</th>
                                <th class="text-left pb-3 whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($absensi as $item)
                                <tr class="attendance-row border-b border-gray-100 hover:bg-blue-50/40 transition"
                                    data-search="{{ strtolower(($item->karyawan->nama_lengkap ?? '') . ' ' . ($item->karyawan->email ?? '') . ' ' . ($item->karyawan->nip ?? '')) }}"
                                    data-status="{{ strtolower($item->is_change_day ? $item->change_day_status : $item->status_kehadiran) }}">
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
                                            <span
                                                class="bg-yellow-200 text-yellow-800 text-xs px-2 py-0.5 rounded-full">Change
                                                Day</span>
                                        @else
                                            <span
                                                class="bg-blue-200 text-blue-800 text-xs px-2 py-0.5 rounded-full">Regular</span>
                                        @endif
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
                                                'hadir' => 'bg-green-100 text-green-800',
                                                'izin' => 'bg-blue-100 text-blue-800',
                                                'sakit' => 'bg-purple-100 text-purple-800',
                                                'alpha' => 'bg-red-100 text-red-800',
                                            ];

                                            $currentStatusClass =
                                                $statusStyles[$item->status_kehadiran] ?? 'bg-gray-100 text-gray-800';
                                        @endphp

                                        <form action="{{ route('admin.absensi.update-status-absensi', $item->id) }}"
                                            method="POST" class="inline-block" id="form-{{ $item->id }}">
                                            @csrf
                                            @method('PUT')
                                            <select name="status_kehadiran" onchange="updateStatus({{ $item->id }})"
                                                class="text-xs rounded-full py-1 px-3 border-0 focus:ring-2 focus:ring-blue-500 {{ $currentStatusClass }}">
                                                <option value="pending"
                                                    {{ $item->status_kehadiran == 'pending' ? 'selected' : '' }}>
                                                    Pending</option>
                                                <option value="hadir"
                                                    {{ $item->status_kehadiran == 'hadir' ? 'selected' : '' }}>Hadir
                                                </option>
                                                <option value="izin"
                                                    {{ $item->status_kehadiran == 'izin' ? 'selected' : '' }}>Izin
                                                </option>
                                                <option value="sakit"
                                                    {{ $item->status_kehadiran == 'sakit' ? 'selected' : '' }}>Sakit
                                                </option>
                                                <option value="alpha"
                                                    {{ $item->status_kehadiran == 'alpha' ? 'selected' : '' }}>Alpha
                                                </option>
                                            </select>
                                        </form>
                                    </td>

                                    <td class="py-3">
                                        <a class="text-blue-500 hover:text-blue-700 cursor-pointer"
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
                                <td colspan="8" class="text-center py-4 text-gray-400">
                                    Data Not Found
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div id="paginationContainer" class="mt-4 flex justify-end gap-1"></div>

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
                    <h3 class="text-lg font-semibold text-blue-900">Detail Attendance</h3>
                    <button onclick="closeDetailModal()"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">✕</button>
                </div>
                <div id="detailContent" class="mt-5 space-y-5"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateStatus(id) {
            Swal.fire({
                title: 'Ubah status absensi?',
                text: 'Perubahan akan langsung disimpan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, ubah',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg ml-2',
                    cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`form-${id}`).submit();
                }
            });
        }

        function updateChangeDayStatus(id) {
            Swal.fire({
                title: 'Ubah status change day?',
                text: 'Perubahan akan langsung disimpan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, ubah',
                cancelButtonText: 'Batal',
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
                        `<a href="/storage/${data.attachment}" target="_blank" class="text-blue-600 hover:underline text-xs">Lihat file</a>` :
                        '-';

                    const note = data.keterangan || '-';

                    let statusClass = 'bg-gray-100 text-gray-700';

                    switch ((data.status_kehadiran || '').toLowerCase()) {
                        case 'hadir':
                            statusClass = 'bg-emerald-100 text-emerald-700';
                            break;
                        case 'izin':
                            statusClass = 'bg-yellow-100 text-yellow-700';
                            break;
                        case 'sakit':
                            statusClass = 'bg-purple-100 text-purple-700';
                            break;
                        case 'change day pending':
                            statusClass = 'bg-yellow-100 text-yellow-700';
                            break;
                        case 'alpha':
                            statusClass = 'bg-red-100 text-red-700';
                            break;
                    }

                    const statusText = data.status_kehadiran ?
                        data.status_kehadiran.charAt(0).toUpperCase() + data.status_kehadiran.slice(1).toLowerCase() :
                        '-';

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

                        <div class="grid grid-cols-5 gap-3 text-xs">
                            <div>
                                <p class="text-gray-400 mb-1">Check In</p>
                                <p class="text-gray-700 font-medium">${checkIn}</p>
                            </div>

                            <div>
                                <p class="text-gray-400 mb-1">Check Out</p>
                                <p class="text-gray-700 font-medium">${checkOut}</p>
                            </div>

                            <div>
                                <p class="text-gray-400 mb-1">Working Hours</p>
                                <p class="text-gray-700 font-medium">${workingHours}</p>
                            </div>

                            <div>
                                <p class="text-gray-400 mb-1">Date</p>
                                <p class="text-gray-700 font-medium">${date}</p>
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
            const allRows = Array.from(document.querySelectorAll('.attendance-row'));
            const emptyRow = document.getElementById('emptySearchRow');
            const paginationContainer = document.getElementById('paginationContainer');

            const perPage = 10;
            let currentPage = 1;
            let filteredRows = [...allRows];

            function applyFilters() {
                const keyword = searchInput.value.toLowerCase().trim();
                const status = statusFilter.value.toLowerCase().trim();

                filteredRows = allRows.filter(row => {
                    const searchText = (row.dataset.search || '').toLowerCase();
                    const rowStatus = (row.dataset.status || '').toLowerCase();

                    const matchKeyword = !keyword || searchText.includes(keyword);
                    const matchStatus = !status || rowStatus === status;

                    return matchKeyword && matchStatus;
                });

                currentPage = 1;
                renderTable();
                renderPagination();
            }

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

            applyFilters();
        });
    </script>
@endpush
