@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">

        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">
                    Change Day
                </h1>
                <p class="text-sm text-gray-700/80">
                    Request and manage your change day with ease.
                </p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        {{-- CARD SUMMARY --}}
        <div class="p-4 my-4 bg-white shadow rounded-2xl md:p-6">
            <div class="grid grid-cols-1 divide-y sm:grid-cols-4 sm:divide-y-0 sm:divide-x">
                <!-- ITEM 1 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-lg text-blue-900">Total Requests (This Month)</span>
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-xl font-semibold text-blue-900 md:text-2xl">
                            {{ number_format($totalRequest, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>

                <!-- ITEM 2 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-xl text-blue-900">Approved</span>
                    <h2 class="text-xl font-semibold text-blue-900 md:text-2xl">
                        {{ number_format($approved, 0, ',', '.') }}
                    </h2>
                </div>

                <!-- ITEM 3 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-xl text-blue-900">Requested</span>
                    <h2 class="text-xl font-semibold text-blue-900 md:text-2xl">
                        {{ number_format($requested, 0, ',', '.') }}
                    </h2>
                </div>

                <!-- ITEM 4 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-xl text-blue-900">Rejected</span>
                    <h2 class="text-xl font-semibold text-blue-900 md:text-2xl">
                        {{ number_format($rejected, 0, ',', '.') }}
                    </h2>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="p-4 bg-white shadow rounded-2xl md:p-6">
            <div class="flex flex-col gap-4 mb-4 lg:flex-row lg:items-center lg:justify-between">

                <!-- FILTER -->
                <div class="flex flex-col w-full gap-3 sm:flex-row lg:w-auto">

                    <!-- STATUS -->
                    <div class="w-full sm:w-52">
                        <select id="filterStatus"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <!-- MONTH -->
                    <div class="w-full sm:w-52">
                        <select id="filterMonth"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
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

                </div>

                <!-- BUTTON -->
                <div class="w-full sm:w-auto">
                    <button data-modal-target="requestChangeDayModal" data-modal-toggle="requestChangeDayModal"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-xl shadow-sm transition focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                        <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
                            <th class="pb-3 text-left whitespace-nowrap">Request Date</th>
                            <th class="pb-3 text-left whitespace-nowrap">Original Date</th>
                            <th class="pb-3 text-left whitespace-nowrap">Requested Date</th>
                            <th class="pb-3 text-left whitespace-nowrap">Status</th>
                            <th class="pb-3 text-left whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody id="changeDayTable">
                        @foreach ($data as $item)
                            <tr class="transition border-b change-day-row hover:bg-gray-50"
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
                                    <div class="flex items-center gap-2">
                                        {{-- View --}}
                                        <a onclick="showDetail({{ $item->id }})" title="View Detail"
                                            class="text-blue-500 transition cursor-pointer hover:text-blue-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        @if (strtolower($item->change_day_status) === 'pending')
                                            {{-- Edit --}}
                                            <a onclick="openEditModal({{ $item->id }})" title="Edit Request"
                                                class="text-yellow-500 transition cursor-pointer hover:text-yellow-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            {{-- Delete --}}
                                            <form action="{{ route('changeday.cancel', $item->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" title="Cancel Request"
                                                    onclick="confirmDeleteChangeDay(this)"
                                                    class="text-red-400 transition hover:text-red-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            {{-- Locked --}}
                                            <span title="Cannot be modified" class="text-gray-300 cursor-not-allowed">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="emptyFilterRow" style="display: none;">
                            <td colspan="5" class="py-6 text-center text-gray-400">
                                Data Not Found
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div id="paginationContainer" class="flex flex-wrap items-center justify-end gap-2 mt-6">
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
                    <h3 class="text-lg font-semibold text-blue-900">Detail Change Day</h3>
                    <button onclick="closeDetailModal()"
                        class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100">✕</button>
                </div>
                <div id="detailContent" class="mt-5 space-y-5"></div>
            </div>
        </div>
    </div>

    {{-- MODAL REQUEST CHANGE DAY --}}
    @include('changeday.create')
    
    {{-- MODAL EDIT CHANGE DAY --}}
    <div id="editChangeDayModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
        <div class="relative w-full max-w-2xl">
            <div class="overflow-hidden bg-white shadow-2xl rounded-3xl">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800">Edit Change Day Request</h3>
                        <p class="mt-1 text-sm text-gray-500">Swap a regular work day off for working on a Sunday or
                            national holiday.</p>
                    </div>
                    <button onclick="closeEditModal()"
                        class="flex items-center justify-center flex-shrink-0 ml-4 transition rounded-full w-9 h-9 hover:bg-gray-100">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[500px]">
                    <form id="editChangeDayForm" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <!-- ORIGINAL DATE -->
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Original Date <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-400 mb-2">The regular work day (Mon–Sat) you want to take off</p>
                                <input type="text" id="editOriginalDate" name="original_date"
                                    placeholder="Select date" autocomplete="off" readonly
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                <p id="editOriginalDateError" class="mt-1.5 text-xs text-red-500 hidden"></p>
                            </div>

                            <!-- REQUESTED DATE -->
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Requested Date <span class="text-red-500">*</span>
                                </label>
                                <p class="text-xs text-gray-400 mb-2">The Sunday or national holiday you'll work instead, within 2 weeks</p>
                                <input type="text" id="editRequestedDate" name="requested_date"
                                    placeholder="Select original date first" autocomplete="off" readonly
                                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 text-gray-400 cursor-not-allowed">
                                <p id="editRequestedDateError" class="mt-1.5 text-xs text-red-500 hidden"></p>
                                <p id="editRequestedDateLabel" class="mt-1.5 text-xs text-blue-600 hidden"></p>
                            </div>
                        </div>

                        <!-- REASON -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Reason</label>
                            <textarea id="editReason" name="reason" rows="4"
                                placeholder="Explain the reason for requesting a change day..."
                                class="w-full px-4 py-3 text-sm border border-gray-300 shadow-sm resize-none rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="flex flex-col-reverse gap-3 mt-6 sm:flex-row sm:justify-end">
                            <button type="button" onclick="closeEditModal()"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                                Cancel
                            </button>
                            <button type="submit"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                        `<a href="/storage/${data.attachment}" target="_blank" class="text-xs text-blue-600 hover:underline">Lihat file</a>` :
                        '-';

                    const note      = data.change_day_alasan || '-';
                    const adminNote = data.change_day_catatan_admin || null;

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

                        ${adminNote ? `
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Notes from Admin</p>
                            <div class="px-3 py-2 text-xs text-gray-600 border border-gray-300 rounded-md bg-gray-50">
                                ${adminNote}
                            </div>
                        </div>
                        ` : ''}

                        <div>
                            <p class="mb-1 text-xs text-gray-500">Status</p>
                            <span class="inline-flex items-center px-3 py-1 rounded text-xs font-medium ${currentStatus.class}">
                                ${currentStatus.text}
                            </span>
                            ${
                                data.change_day_status === 'approved'
                                    ? `<p class="text-xs text-gray-500 mt-1">Last updated: ${approveDate || '-'} by ${approveBy || '-'}</p>`
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

        // ── Edit Modal ─────────────────────────────────────────────────────────
        function openEditModal(id) {
            fetch(`/changeday/${id}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('editChangeDayForm').action = `/changeday/${id}`;

                    const origVal = data.change_day_tanggal_awal?.substring(0, 10) || '';
                    const reqVal  = data.change_day_tanggal_akhir?.substring(0, 10) || '';

                    ['editOriginalDateError','editRequestedDateError','editRequestedDateLabel']
                        .forEach(elId => {
                            const el = document.getElementById(elId);
                            el.textContent = ''; el.classList.add('hidden');
                        });

                    // Set Original Date (silent — no onChange trigger)
                    fpEditOrig.setDate(origVal, false);

                    if (origVal) {
                        fpEditReq.set('minDate', addEditDays(origVal, -14));
                        fpEditReq.set('maxDate', addEditDays(origVal, 14));
                        fpEditReq.set('clickOpens', true);
                        editReqInput.classList.remove('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
                        editReqInput.classList.add('bg-white', 'text-gray-800', 'cursor-pointer');
                        editReqInput.placeholder = 'Select date';
                    }

                    // Set Requested Date (silent)
                    if (reqVal) {
                        fpEditReq.setDate(reqVal, false);
                        const lbl = document.getElementById('editRequestedDateLabel');
                        lbl.textContent = isEditHoliday(reqVal)
                            ? '🗓 ' + EDIT_HOLIDAY_MAP[reqVal]
                            : (isEditSunday(reqVal) ? '📅 Sunday' : '');
                        if (lbl.textContent) lbl.classList.remove('hidden');
                    }

                    document.getElementById('editReason').value = data.change_day_alasan || '';
                    document.getElementById('editChangeDayModal').classList.remove('hidden');
                })
                .catch(() => alert('Failed to load request data.'));
        }

        function closeEditModal() {
            document.getElementById('editChangeDayModal').classList.add('hidden');
            fpEditOrig.clear();
            resetEditReqDate();
        }

        // ── Delete Confirm ─────────────────────────────────────────────────────
        function confirmDeleteChangeDay(btn) {
            Swal.fire({
                title: 'Cancel this request?',
                text: 'This change day request will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it',
                cancelButtonText: 'Keep it',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white font-medium px-4 py-2 rounded-lg ml-2',
                    cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg',
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        }

        // ── Edit modal Flatpickr ────────────────────────────────────────────────
        const EDIT_HOLIDAY_MAP = {};

        (async function loadEditHolidays() {
            const year = new Date().getFullYear();
            async function fetchEditYear(y) {
                try {
                    const res = await fetch(`https://libur.deno.dev/api?year=${y}`);
                    const data = await res.json();
                    if (Array.isArray(data)) {
                        data.forEach(item => {
                            if (item.date) EDIT_HOLIDAY_MAP[item.date.substring(0, 10)] = item.name || 'National Holiday';
                        });
                    }
                } catch (e) {}
            }
            await Promise.all([fetchEditYear(year), fetchEditYear(year + 1)]);
            if (fpEditOrig) fpEditOrig.set('disable', editOrigDisableFn);
            if (fpEditReq)  fpEditReq.set('disable', editReqDisableFn);
        })();

        function parseEditLocal(str) {
            const [y, m, d] = str.split('-').map(Number);
            return new Date(y, m - 1, d);
        }
        function toEditYMD(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }
        function addEditDays(str, days) {
            const d = parseEditLocal(str);
            d.setDate(d.getDate() + days);
            return toEditYMD(d);
        }
        function isEditSunday(str)  { return parseEditLocal(str).getDay() === 0; }
        function isEditHoliday(str) { return Object.prototype.hasOwnProperty.call(EDIT_HOLIDAY_MAP, str); }

        function onEditDayCreate(dObj, dStr, fp, dayElem) {
            const d = dayElem.dateObj;
            if (!d) return;
            if (d.getDay() === 0) dayElem.classList.add('fp-sunday');
            const key = toEditYMD(d);
            if (EDIT_HOLIDAY_MAP[key]) {
                dayElem.classList.add('fp-holiday');
                dayElem.title = EDIT_HOLIDAY_MAP[key];
            }
        }

        const editReqInput = document.getElementById('editRequestedDate');

        const editOrigDisableFn = [function(date) {
            const str = toEditYMD(date);
            return isEditSunday(str) || isEditHoliday(str);
        }];
        const editReqDisableFn = [function(date) {
            const str = toEditYMD(date);
            return !isEditSunday(str) && !isEditHoliday(str);
        }];

        function resetEditReqDate() {
            fpEditReq.clear();
            fpEditReq.set('minDate', null);
            fpEditReq.set('maxDate', null);
            fpEditReq.set('clickOpens', false);
            editReqInput.classList.add('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
            editReqInput.classList.remove('bg-white', 'text-gray-800', 'cursor-pointer');
            editReqInput.placeholder = 'Select original date first';
            const lbl = document.getElementById('editRequestedDateLabel');
            lbl.textContent = ''; lbl.classList.add('hidden');
            const err = document.getElementById('editRequestedDateError');
            err.textContent = ''; err.classList.add('hidden');
        }

        let fpEditOrig = flatpickr('#editOriginalDate', {
            dateFormat: 'Y-m-d',
            allowInput: false,
            disable: editOrigDisableFn,
            onDayCreate: onEditDayCreate,
            onChange: function(selectedDates, dateStr) {
                document.getElementById('editOriginalDateError').textContent = '';
                document.getElementById('editOriginalDateError').classList.add('hidden');
                resetEditReqDate();
                if (!dateStr) return;
                fpEditReq.set('minDate', addEditDays(dateStr, -14));
                fpEditReq.set('maxDate', addEditDays(dateStr, 14));
                fpEditReq.set('clickOpens', true);
                editReqInput.classList.remove('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
                editReqInput.classList.add('bg-white', 'text-gray-800', 'cursor-pointer');
                editReqInput.placeholder = 'Select date';
            },
        });

        let fpEditReq = flatpickr('#editRequestedDate', {
            dateFormat: 'Y-m-d',
            allowInput: false,
            clickOpens: false,
            disable: editReqDisableFn,
            onDayCreate: onEditDayCreate,
            onChange: function(selectedDates, dateStr) {
                const errEl = document.getElementById('editRequestedDateError');
                const lbl   = document.getElementById('editRequestedDateLabel');
                errEl.textContent = ''; errEl.classList.add('hidden');
                lbl.textContent  = ''; lbl.classList.add('hidden');
                if (!dateStr) return;

                const origDates = fpEditOrig.selectedDates;
                if (origDates.length) {
                    const diff = Math.abs((parseEditLocal(dateStr) - origDates[0]) / 86400000);
                    if (diff > 7) {
                        errEl.textContent = 'Requested date must be within 2 weeks of the original date.';
                        errEl.classList.remove('hidden');
                        fpEditReq.clear();
                        return;
                    }
                }
                lbl.textContent = isEditHoliday(dateStr) ? '🗓 ' + EDIT_HOLIDAY_MAP[dateStr] : '📅 Sunday';
                lbl.classList.remove('hidden');
            },
        });

        document.getElementById('editChangeDayForm').addEventListener('submit', function(e) {
            const origVal = fpEditOrig.selectedDates[0] ? toEditYMD(fpEditOrig.selectedDates[0]) : '';
            const reqVal  = fpEditReq.selectedDates[0]  ? toEditYMD(fpEditReq.selectedDates[0])  : '';
            let valid = true;
            if (!origVal) {
                const el = document.getElementById('editOriginalDateError');
                el.textContent = 'Original date is required.'; el.classList.remove('hidden');
                valid = false;
            }
            if (!reqVal) {
                const el = document.getElementById('editRequestedDateError');
                el.textContent = 'Requested date is required.'; el.classList.remove('hidden');
                valid = false;
            }
            if (!valid) e.preventDefault();
        });

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
