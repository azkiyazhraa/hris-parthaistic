@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">
        <!-- Header Card -->
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">Leave</h1>
                <p class="text-sm text-gray-700/80">Request and manage your leave with ease.</p>
            </div>
            <!-- IMAGE / ILLUSTRATION -->
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="p-4 my-4 bg-white shadow rounded-2xl md:p-6">
            <div class="grid grid-cols-1 divide-y sm:grid-cols-4 sm:divide-y-0 sm:divide-x">
                <!-- ITEM 1 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-xl text-blue-900">Year</span>
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-xl font-semibold text-blue-900 md:text-2xl" id="periode">
                            {{ date('Y') }}
                        </h2>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="text-blue-900 size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h2.25m-9 2.25h4.5m.002-2.25h.005v.006H12v-.006Zm-.001 4.5h.006v.006h-.006v-.005Zm-2.25.001h.005v.006H9.75v-.006Zm-2.25 0h.005v.005h-.006v-.005Zm6.75-2.247h.005v.005h-.005v-.005Zm0 2.247h.006v.006h-.006v-.006Zm2.25-2.248h.006V15H16.5v-.005Z" />
                        </svg>
                    </div>
                </div>

                <!-- ITEM 2 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-xl text-blue-900">Approved</span>
                    <h2 class="text-xl font-semibold text-blue-900 md:text-2xl" id="total_salary">
                        {{ number_format($approvedCount, 0, ',', '.') }}
                    </h2>
                </div>

                <!-- ITEM 3 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-xl text-blue-900">Requested</span>
                    <h2 class="text-xl font-semibold text-blue-900 md:text-2xl" id="deduction">
                        {{ number_format($pendingCount, 0, ',', '.') }}
                    </h2>
                </div>

                <!-- ITEM 4 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-xl text-blue-900">Rejected</span>
                    <h2 class="text-xl font-semibold text-blue-900 md:text-2xl" id="allowance">
                        {{ number_format($rejectedCount, 0, ',', '.') }}
                    </h2>
                </div>
            </div>
        </div>

        <div class="p-6 bg-white shadow-lg rounded-2xl" style="border: 2px solid #e0eaff;">
            <div class="flex flex-col gap-4 mb-4 lg:flex-row lg:items-center lg:justify-between">

                <!-- FILTER -->
                <div class="flex flex-col w-full gap-3 sm:flex-row lg:w-auto">

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
                            <option value="Annual">Annual Leave</option>
                            <option value="Sick">Sick Leave</option>
                            <option value="Parental">Parental Leave</option>
                            <option value="Personal">Personal Leave</option>
                            <option value="Ibadah">Religious Leave</option>
                            <option value="Other">Other Leave</option>
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

            </div>

            @include('cuti.create')

            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                    <thead>
                        <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
                            <th class="pb-3 text-left whitespace-nowrap">Leave Type</th>
                            <th class="pb-3 text-left whitespace-nowrap">Start Date</th>
                            <th class="pb-3 text-left whitespace-nowrap">End Date</th>
                            <th class="pb-3 text-left whitespace-nowrap">Days</th>
                            <th class="pb-3 text-left whitespace-nowrap">Status</th>
                            <th class="pb-3 text-left whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $leaveTypeMap = [
                                'tahunan' => ['label' => 'Annual Leave', 'key' => 'annual'],
                                'sakit' => ['label' => 'Sick Leave', 'key' => 'sick'],
                                'melahirkan' => ['label' => 'Parental Leave', 'key' => 'parental'],
                                'penting' => ['label' => 'Personal Leave', 'key' => 'personal'],
                                'ibadah' => ['label' => 'Religious Leave', 'key' => 'ibadah'],
                                'lainnya' => ['label' => 'Other Leave', 'key' => 'other'],
                            ];
                        @endphp
                        @forelse($cuti as $item)
                            @php
                                $leaveInfo = $leaveTypeMap[strtolower($item->jenis_cuti)] ?? [
                                    'label' => ucfirst($item->jenis_cuti),
                                    'key' => strtolower($item->jenis_cuti),
                                ];
                            @endphp
                            <tr class="transition border-b border-gray-100 leave-row hover:bg-blue-50/40"
                                data-status="{{ strtolower($item->status) }}" data-type="{{ $leaveInfo['key'] }}">
                                <td class="py-3 text-gray-700">{{ $leaveInfo['label'] }}</td>
                                <td class="py-3 text-gray-700">{{ date('d M Y', strtotime($item->tanggal_mulai)) }}</td>
                                <td class="py-3 text-gray-700">{{ date('d M Y', strtotime($item->tanggal_selesai)) }}</td>
                                <td class="py-3 text-gray-700">{{ $item->total_hari }}</td>
                                <td class="py-3">
                                    @php
                                        $statusMap = [
                                            'pending' => [
                                                'label' => 'Pending',
                                                'badge' => 'bg-yellow-100 text-yellow-800',
                                            ],
                                            'disetujui' => [
                                                'label' => 'Approved',
                                                'badge' => 'bg-green-100 text-green-800',
                                            ],
                                            'ditolak' => ['label' => 'Rejected', 'badge' => 'bg-red-100 text-red-800'],
                                        ];
                                        $statusInfo = $statusMap[strtolower($item->status)] ?? [
                                            'label' => ucfirst($item->status),
                                            'badge' => 'bg-gray-100 text-gray-700',
                                        ];
                                    @endphp

                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusInfo['badge'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <a onclick="showDetail({{ $item->id }})"
                                        class="text-sm text-blue-600 transition cursor-pointer hover:text-blue-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">
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
        class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/40">
        <div class="relative w-full max-w-xl p-4">
            <div class="p-6 bg-white shadow-lg rounded-3xl">
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-blue-900">Detail Leave</h3>
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


                    const textLeave = {
                        'tahunan': {
                            text: 'Annual',
                        },
                        'melahirkan': {
                            text: 'Maternity',
                        },
                    };
                    const leaveType = textLeave[(data.jenis_cuti || '').toLowerCase()] || {
                        text: data.jenis_cuti || '-',
                    };
                    const leave = leaveType.text;

                    const reason = data.alasan || '-';

                    const statusMap = {
                        'pending': {
                            text: 'Pending',
                            cls: 'bg-yellow-100 text-yellow-700'
                        },
                        'disetujui': {
                            text: 'Approved',
                            cls: 'bg-green-100 text-green-700'
                        },
                        'ditolak': {
                            text: 'Rejected',
                            cls: 'bg-red-100 text-red-700'
                        },
                    };
                    const statusInfo = statusMap[(data.status || '').toLowerCase()] || {
                        text: data.status || '-',
                        cls: 'bg-gray-100 text-gray-700'
                    };
                    const statusClass = statusInfo.cls;
                    const statusText = statusInfo.text;

                    const notes = data.catatan;

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
                            <p class="mb-1 text-xs text-gray-500">Reason</p>
                            <div class="px-3 py-2 text-xs text-gray-600 border border-blue-500 rounded-md">
                                ${reason}
                            </div>
                        </div>

                        ${notes ? `
                            <div>
                                <p class="mb-1 text-xs text-gray-500">Notes</p>
                                <div class="px-3 py-2 text-xs text-gray-600 border rounded-md border-success-300">
                                    ${notes}
                                </div>
                            </div>
                        ` : ''}

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
