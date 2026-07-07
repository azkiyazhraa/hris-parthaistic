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

            {{-- ROW 1: Stats --}}
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 mb-4 border-b border-gray-100">

                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-xl bg-blue-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Year</p>
                        <p class="text-lg font-bold text-blue-900">{{ date('Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-6 sm:gap-10">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $approvedCount }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Approved</p>
                    </div>
                    <div class="hidden w-px h-8 bg-gray-200 sm:block"></div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-yellow-500">{{ $pendingCount }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Requested</p>
                    </div>
                    <div class="hidden w-px h-8 bg-gray-200 sm:block"></div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-red-500">{{ $rejectedCount }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Rejected</p>
                    </div>
                </div>

            </div>

            {{-- ROW 2: Quota per leave type --}}
            @php
                $quotaItems = [
                    [
                        'label' => 'Annual Leave',
                        'kuota' => $kuotaTahunan,
                        'sisa' => $sisaTahunan,
                        'color' => 'blue',
                    ],
                    [
                        'label' => $karyawan->jenis_kelamin === 'P' ? 'Maternity Leave' : 'Paternity Leave',
                        'kuota' => $kuotaMelahirkan,
                        'sisa' => $sisaMelahirkan,
                        'color' => 'pink',
                    ],
                    [
                        'label' => 'Marriage Leave',
                        'kuota' => $kuotaMenikah,
                        'sisa' => $sisaMenikah,
                        'color' => 'yellow',
                    ],
                    [
                        'label' => 'Bereavement Leave',
                        'kuota' => $kuotaDuka,
                        'sisa' => $sisaDuka,
                        'color' => 'purple',
                    ],
                ];

                $palette = [
                    'blue' => [
                        'text' => 'text-blue-600',
                        'bar' => 'bg-blue-500',
                        'track' => 'bg-blue-100',
                        'badge' => 'bg-blue-50 text-blue-700',
                    ],
                    'pink' => [
                        'text' => 'text-pink-600',
                        'bar' => 'bg-pink-500',
                        'track' => 'bg-pink-100',
                        'badge' => 'bg-pink-50 text-pink-700',
                    ],
                    'yellow' => [
                        'text' => 'text-yellow-600',
                        'bar' => 'bg-yellow-400',
                        'track' => 'bg-yellow-100',
                        'badge' => 'bg-yellow-50 text-yellow-700',
                    ],
                    'purple' => [
                        'text' => 'text-purple-600',
                        'bar' => 'bg-purple-500',
                        'track' => 'bg-purple-100',
                        'badge' => 'bg-purple-50 text-purple-700',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-4">
                @foreach ($quotaItems as $q)
                    @php
                        $used = $q['kuota'] - $q['sisa'];
                        $pct = $q['kuota'] > 0 ? min(100, round(($used / $q['kuota']) * 100)) : 0;
                        $c = $palette[$q['color']];
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="pr-1 text-xs font-semibold text-gray-600 truncate">{{ $q['label'] }}</p>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full shrink-0 {{ $c['badge'] }}">
                                {{ $q['sisa'] }} left
                            </span>
                        </div>
                        <div class="w-full h-1.5 rounded-full {{ $c['track'] }}">
                            <div class="h-1.5 rounded-full {{ $c['bar'] }}" style="width: {{ $pct }}%"></div>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">{{ $used }} / {{ $q['kuota'] }} days used</p>
                    </div>
                @endforeach
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
                            <option value="tahunan">Annual Leave</option>
                            <option value="melahirkan">Maternity / Paternity Leave</option>
                            <option value="menikah">Marriage Leave</option>
                            <option value="duka">Bereavement Leave</option>
                        </select>
                    </div>

                </div>

                <!-- BUTTON -->
                @php
                    $allQuotasExhausted =
                        $sisaTahunan <= 0 && $sisaMelahirkan <= 0 && $sisaMenikah <= 0 && $sisaDuka <= 0;
                @endphp
                <div class="w-full sm:w-auto">
                    @if ($allQuotasExhausted)
                        <button type="button" disabled title="All leave quotas have been exhausted for this year"
                            class="w-full sm:w-auto flex items-center gap-2 bg-gray-200 text-gray-400 cursor-not-allowed px-5 py-2.5 rounded-xl text-sm font-medium select-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            Request Leave
                        </button>
                        <p class="mt-1 text-xs text-center text-red-500 sm:text-right">All quotas exhausted</p>
                    @else
                        <button type="button" data-modal-target="requestLeaveModal" data-modal-toggle="requestLeaveModal"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium shadow-sm transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Request Leave
                        </button>
                    @endif
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
                                'tahunan' => ['label' => 'Annual Leave', 'key' => 'tahunan'],
                                'melahirkan' => ['label' => 'Maternity / Paternity Leave', 'key' => 'melahirkan'],
                                'menikah' => ['label' => 'Marriage Leave', 'key' => 'menikah'],
                                'duka' => ['label' => 'Bereavement Leave', 'key' => 'duka'],
                                // legacy values kept for historical records
                                'sakit' => ['label' => 'Sick Leave', 'key' => 'sakit'],
                                'penting' => ['label' => 'Emergency Leave', 'key' => 'penting'],
                                'ibadah' => ['label' => 'Religious Leave', 'key' => 'ibadah'],
                                'lainnya' => ['label' => 'Other Leave', 'key' => 'lainnya'],
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
                                    <div class="flex items-center gap-2">
                                        {{-- View --}}
                                        <a onclick="showDetail({{ $item->id }})" title="View Detail"
                                            class="text-blue-500 transition cursor-pointer hover:text-blue-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        @if (strtolower($item->status) === 'pending')
                                            {{-- Edit --}}
                                            <a href="{{ route('cuti.edit', $item->id) }}" title="Edit Request"
                                                class="text-yellow-500 transition hover:text-yellow-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            {{-- Delete --}}
                                            <form action="{{ route('cuti.destroy', $item->id) }}" method="POST"
                                                class="inline delete-form-cuti">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" title="Cancel Request"
                                                    onclick="confirmDeleteCuti(this)"
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
        const leaveQuotas = {
            'tahunan': {
                label: 'Annual Leave',
                total: {{ $kuotaTahunan }},
                remaining: {{ $sisaTahunan }},
                color: '#3b82f6',
                trackColor: '#dbeafe',
            },
            'melahirkan': {
                label: '{{ $karyawan->jenis_kelamin === 'P' ? 'Maternity Leave' : 'Paternity Leave' }}',
                total: {{ $kuotaMelahirkan }},
                remaining: {{ $sisaMelahirkan }},
                color: '#ec4899',
                trackColor: '#fce7f3',
            },
            'menikah': {
                label: 'Marriage Leave',
                total: {{ $kuotaMenikah }},
                remaining: {{ $sisaMenikah }},
                color: '#eab308',
                trackColor: '#fef9c3',
            },
            'duka': {
                label: 'Bereavement Leave',
                total: {{ $kuotaDuka }},
                remaining: {{ $sisaDuka }},
                color: '#a855f7',
                trackColor: '#f3e8ff',
            },
        };

        function showDetail(id) {
            fetch(`/cuti/${id}`)
                .then(response => response.json())
                .then(data => {
                    const nama = data.karyawan?.nama_lengkap || data.nama_karyawan || '-';
                    const position = data.karyawan?.jabatan || '-';
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
                        }) : '-';
                    const endDate = data.tanggal_selesai ?
                        new Date(data.tanggal_selesai).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) : '-';

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
                        text: data.jenis_cuti || '-'
                    };
                    const leave = leaveType.text;

                    const reason = data.alasan ?
                        data.alasan.replace(/\n/g, '<br>') :
                        '-';

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

                    const notes = data.catatan ?
                        data.catatan.replace(/\n/g, '<br>') :
                        '-';

                    // Quota block
                    const quota = leaveQuotas[(data.jenis_cuti || '').toLowerCase()];
                    const quotaBlock = quota ? (() => {
                        const used = quota.total - quota.remaining;
                        const pct = quota.total > 0 ? Math.min(100, Math.round((used / quota.total) * 100)) : 0;
                        return `
                            <div class="shrink-0 text-right min-w-[110px]">
                                <p class="text-xs text-gray-400 mb-0.5">Remaining Quota</p>
                                <p class="text-2xl font-bold" style="color:${quota.color}">${quota.remaining}</p>
                                <p class="text-xs text-gray-400">/ ${quota.total} days</p>
                                <div class="mt-2 w-full h-1.5 rounded-full" style="background:${quota.trackColor}">
                                    <div class="h-1.5 rounded-full" style="width:${pct}%;background:${quota.color}"></div>
                                </div>
                                <p class="text-xs mt-0.5" style="color:${quota.color}">${used} used</p>
                            </div>`;
                    })() : '';

                    const content = `
                    <div class="space-y-5">

                        <div class="pb-4 border-b">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="overflow-hidden border-2 border-blue-700 rounded-full w-14 h-14 shrink-0">
                                        <img
                                            src="${foto}"
                                            class="object-cover w-full h-full"
                                            onerror="this.src='https://ui-avatars.com/api/?background=1E3A8A&color=fff&size=100&name=${encodeURIComponent(nama)}'">
                                    </div>
                                    <div>
                                        <h3 class="text-base font-semibold text-slate-900">${nama}</h3>
                                        <p class="text-xs text-gray-500 capitalize">${position}</p>
                                        <p class="text-xs text-gray-400">${email}</p>
                                        <p class="text-xs text-gray-400">${phone}</p>
                                    </div>
                                </div>
                                ${quotaBlock}
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

        function confirmDeleteCuti(btn) {
            Swal.fire({
                title: 'Cancel this request?',
                text: 'This leave request will be permanently removed.',
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
