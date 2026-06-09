@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">
        <!-- Header Card -->
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">Leave</h1>
                <p class="text-gray-700/80 text-sm">Request and manage your leave with ease.</p>
            </div>
            <!-- IMAGE / ILLUSTRATION -->
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
                    <span class="text-blue-900 text-xl">Approved</span>
                    <h2 class="text-xl md:text-2xl font-semibold text-blue-900" id="total_salary">
                        {{ number_format($approvedCount, 0, ',', '.') }}
                    </h2>
                </div>

                <!-- ITEM 3 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-blue-900 text-xl">Requested</span>
                    <h2 class="text-xl md:text-2xl font-semibold text-blue-900" id="deduction">
                        {{ number_format($pendingCount, 0, ',', '.') }}
                    </h2>
                </div>

                <!-- ITEM 4 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-blue-900 text-xl">Rejected</span>
                    <h2 class="text-xl md:text-2xl font-semibold text-blue-900" id="allowance">
                        {{ number_format($rejectedCount, 0, ',', '.') }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6" style="border: 2px solid #e0eaff;">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">

                <!-- FILTER -->
                <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">

                    <!-- STATUS -->
                    <div class="w-full sm:w-52">
                        <select id="filterStatus"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">

                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="disetujui">Approved</option>
                            <option value="ditolak">Rejected</option>
                        </select>
                    </div>

                    <!-- TYPE -->
                    <div class="w-full sm:w-56">
                        <select id="filterType"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">

                            <option value="">All Leave Types</option>
                            <option value="tahunan">Annual Leave</option>
                            <option value="sakit">Sick Leave</option>
                            <option value="melahirkan">Parental Leave</option>
                            <option value="penting">Personal Leave</option>
                            <option value="ibadah">Religious Leave</option>
                            <option value="lainnya">Other Leave</option>
                        </select>
                    </div>

                </div>

                <!-- BUTTON -->
                <div class="w-full sm:w-auto">
                    <button type="button" data-modal-target="requestLeaveModal" data-modal-toggle="requestLeaveModal"
                        class="w-full sm:w-auto bg-blue-900 hover:bg-blue-800 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition">

                        Request Leave
                    </button>
                </div>

                @include('cuti.create')

            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                    <thead>
                        <tr class="text-gray-400 font-medium text-xs uppercase tracking-wide border-b">
                            <th class="text-left pb-3 whitespace-nowrap">Leave Type</th>
                            <th class="text-left pb-3 whitespace-nowrap">Start Date</th>
                            <th class="text-left pb-3 whitespace-nowrap">End Date</th>
                            <th class="text-left pb-3 whitespace-nowrap">Days</th>
                            <th class="text-left pb-3 whitespace-nowrap">Status</th>
                            <th class="text-left pb-3 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cuti as $item)
                            <tr class="leave-row border-b border-gray-100 hover:bg-blue-50/40 transition"
                                data-status="{{ strtolower($item->status) }}"
                                data-type="{{ strtolower($item->jenis_cuti) }}">
                                <td class="py-3 text-gray-700">{{ $item->jenis_cuti }}</td>
                                <td class="py-3 text-gray-700">{{ date('d M Y', strtotime($item->tanggal_mulai)) }}</td>
                                <td class="py-3 text-gray-700">{{ date('d M Y', strtotime($item->tanggal_selesai)) }}</td>
                                <td class="py-3 text-gray-700">{{ $item->total_hari }}</td>
                                <td class="py-3">
                                    @php
    $badge = match ($item->status) {
        'pending' => 'bg-yellow-100 text-yellow-800',
        'disetujui' => 'bg-green-100 text-green-800',
        'ditolak' => 'bg-red-100 text-red-800',
    };
                                    @endphp

                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <a onclick="showDetail({{ $item->id }})"
                                        class="text-blue-600 hover:text-blue-800 text-sm transition cursor-pointer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-gray-500">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p>No leave applications yet</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 flex justify-center items-center bg-black/40">
        <div class="relative w-full max-w-xl p-4">
            <div class="bg-white rounded-3xl shadow-lg p-6">
                <div class="flex justify-between items-center border-b pb-4">
                    <h3 class="text-lg font-semibold text-blue-900">Detail Leave</h3>
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
        function showDetail(id) {
            fetch(`/cuti/${id}`)
                .then(response => response.json())
                .then(data => {
                    const nama = data.karyawan?.nama_lengkap || data.nama_karyawan || '-';
                    const role = data.karyawan?.role || '-';
                    const email = data.karyawan?.email || '-';
                    const phone = data.karyawan?.nomor_telepon || '-';

                    const foto = data.karyawan?.foto_profil ?
                        `/storage/${data.karyawan.foto_profil}` :
                        `https://ui-avatars.com/api/?background=1E3A8A&color=fff&size=100&name=${encodeURIComponent(nama)}`;


                    const startDate = data.tanggal_mulai ?
                        new Date(data.tanggal_mulai).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) :
                        '-';
                    const endDate = data.tanggal_selesai ?
                        new Date(data.tanggal_selesai).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) :
                        '-';

                    const leaveType = data.jenis_cuti ?
                        data.jenis_cuti.charAt(0).toUpperCase() + data.jenis_cuti.slice(1) :
                        '-';

                    const lampiran = data.lampiran ?
                        `<a href="/storage/${data.lampiran}" target="_blank" class="text-blue-600 hover:underline text-xs">Lihat file</a>` :
                        '-';

                    const reason = data.alasan || '-';

                    let statusClass = 'bg-gray-100 text-gray-700';

                    switch ((data.status || '').toLowerCase()) {
                        case 'pending':
                            statusClass = 'bg-pending-100 text-pending-700';
                            break;
                        case 'disetujui':
                            statusClass = 'bg-green-100 text-green-700';
                            break;
                        case 'ditolak':
                            statusClass = 'bg-red-100 text-red-700';
                            break;
                    }

                    const statusText = data.status ?
                        data.status.charAt(0).toUpperCase() + data.status.slice(1).toLowerCase() :
                        '-';

                    const notes = data.catatan;

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
                                <p class="text-gray-400 mb-1">Lampiran</p>
                                ${lampiran}
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">Reason</p>
                            <div class="border border-blue-500 rounded-md px-3 py-2 text-xs text-gray-600">
                                ${reason}
                            </div>
                        </div>

                        ${notes ? `
                                                                                    <div>
                                                                                        <p class="text-xs text-gray-500 mb-1">Notes</p>
                                                                                        <div class="border border-success-300 rounded-md px-3 py-2 text-xs text-gray-600">
                                                                                            ${notes}
                                                                                        </div>
                                                                                    </div>
                                                                                ` : ''}

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

            const filterStatus = document.getElementById('filterStatus');
            const filterType = document.getElementById('filterType');

            const rows = document.querySelectorAll('.leave-row');

            function applyFilter() {

                const status = filterStatus.value.toLowerCase();
                const type = filterType.value.toLowerCase();

                rows.forEach(row => {

                    const rowStatus = row.dataset.status;
                    const rowType = row.dataset.type;

                    const matchStatus = !status || rowStatus === status;
                    const matchType = !type || rowType === type;

                    if (matchStatus && matchType) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            filterStatus.addEventListener('change', applyFilter);
            filterType.addEventListener('change', applyFilter);

        });
    </script>
@endpush
