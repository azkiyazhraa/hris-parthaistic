@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">

        {{-- CARD HEADER --}}
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">
                    Performance
                </h1>
                <p class="text-sm text-gray-700/80">
                    Monitor employee productivity and task performance.
                </p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        @if (session('success'))
            <div class="px-4 py-3 text-green-700 bg-green-100 border border-green-400 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- CARD SUMMARY --}}
        <div class="bg-white shadow-md rounded-2xl">
            <div class="grid grid-cols-1 divide-y divide-gray-200 sm:grid-cols-4 sm:divide-y-0 sm:divide-x">

                <!-- ITEM 1 - Average Score -->
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-sm font-medium text-gray-400">Average Score (All Time)</p>
                    <div class="flex items-end gap-2 mt-1">
                        <h2 class="text-xl font-semibold text-[#0B0F6D] leading-none">
                            {{ $averageScore }}
                        </h2>
                    </div>
                </div>

                <!-- ITEM 2 - Total Employees -->
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-sm font-medium text-gray-400">Total Employees</p>
                    <div class="flex items-end gap-2 mt-1">
                        <h2 class="text-xl font-semibold text-[#0B0F6D] leading-none">
                            {{ $karyawans->count() }}
                        </h2>
                    </div>
                </div>

                <!-- ITEM 3 - Average Attendance -->
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-sm font-medium text-gray-400">Average Attendance</p>
                    <div class="flex items-end gap-2 mt-1">
                        <h2 class="text-xl font-semibold text-[#0B0F6D] leading-none">
                            {{ $averageAttendance }}%
                        </h2>
                    </div>
                </div>

                <!-- ITEM 4 - Top Performer -->
                @php
                    $bulanNamaTop = [
                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                        5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
                        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
                    ];
                @endphp
                <div class="flex flex-col justify-center px-5 py-4">
                    @if ($topPerformer)
                        @php $initial = substr($topPerformer->nama_lengkap, 0, 1); @endphp

                        <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">
                            &#9733; Top Performer
                            <span class="ml-1 font-normal normal-case text-blue-400">
                                {{ $bulanNamaTop[$topPerformerBulan] ?? '' }} {{ $topPerformerTahun }}
                            </span>
                        </p>

                        <div class="flex items-center gap-3">
                            <div class="relative flex-shrink-0">
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-100 to-cyan-100 flex items-center justify-center border-2 border-[#0B0F6D]">
                                    @if ($topPerformer->foto_profil)
                                        <img src="{{ Storage::url($topPerformer->foto_profil) }}" alt="profile"
                                            class="object-cover w-11 h-11 rounded-full">
                                    @else
                                        <span class="text-base font-bold text-[#0B0F6D]">{{ strtoupper($initial) }}</span>
                                    @endif
                                </div>
                                <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-yellow-400 rounded-full flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 .587l3.668 7.431L24 9.75l-6 5.847 1.416 8.253L12 19.771l-7.416 4.079L6 15.597 0 9.75l8.332-1.732z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-[#0B0F6D] leading-tight truncate">{{ $topPerformer->nama_lengkap }}</h3>
                                <p class="text-xs text-gray-400 capitalize">{{ $topPerformer->role ?? '-' }}</p>
                            </div>
                            <div class="flex-shrink-0 text-right">
                                <span class="text-xl font-bold text-[#0B0F6D]">{{ $topScore }}</span>
                                <p class="text-[10px] text-gray-400 leading-none">/ 100</p>
                            </div>
                        </div>
                    @else
                        <p class="text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">&#9733; Top Performer</p>
                        <p class="text-sm text-gray-400">No data yet</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS + FILTER --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex gap-2">
                <a href="{{ route('admin.performa.create') }}"
                    class="px-4 py-2 text-sm text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">
                    + Add Assessment
                </a>
                <a href="{{ route('admin.performa.bulk') }}"
                    class="px-4 py-2 text-sm text-white transition bg-green-600 rounded-lg hover:bg-green-700">
                    Bulk Assessment
                </a>
            </div>

            {{-- FILTER FORM --}}
            <form id="filterForm" method="GET" action="{{ route('admin.performa.index') }}" class="flex flex-wrap items-center gap-2">
                @php
                    $bulanNamaFilter = [
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                    ];
                @endphp
                <input type="hidden" name="per_page" id="filterPerPage" value="{{ $perPage }}">
                <select name="filter_bulan"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Months</option>
                    @foreach ($bulanNamaFilter as $num => $name)
                        <option value="{{ $num }}" {{ $filterBulan == $num ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                <select name="filter_tahun"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Years</option>
                    @for ($y = 2023; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $filterTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit"
                    class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                    Filter
                </button>
                @if ($filterBulan || $filterTahun)
                    <a href="{{ route('admin.performa.index') }}"
                        class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- TRELLO INTEGRATION STATUS --}}
        <div
            class="flex flex-col items-start gap-3 p-4 bg-white border border-blue-200 border-dashed shadow rounded-2xl sm:flex-row sm:items-center sm:gap-4">
            <div class="flex items-center flex-1 min-w-0 gap-3">
                <div class="w-9 h-9 bg-[#0052CC] rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M21 0H3C1.343 0 0 1.343 0 3v18c0 1.656 1.343 3 3 3h18c1.656 0 3-1.344 3-3V3c0-1.657-1.344-3-3-3zM10.44 18.18c0 .795-.645 1.44-1.44 1.44H4.56c-.795 0-1.44-.645-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44H9c.795 0 1.44.645 1.44 1.44v12.36zm10.44-7.08c0 .794-.645 1.44-1.44 1.44H15c-.795 0-1.44-.646-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44h4.44c.795 0 1.44.645 1.44 1.44v5.28z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm font-semibold text-gray-700">Trello Integration</p>
                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full font-medium">Not
                            Connected</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">Connect Trello to automatically sync employee task completion
                        data for performance evaluation.</p>
                </div>
            </div>
            <button type="button" disabled title="Trello API configuration will be available in the next update"
                class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-medium rounded-lg cursor-not-allowed flex-shrink-0 border border-gray-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Configure
            </button>
        </div>

        {{-- TABLE --}}
        <div class="w-full p-6 bg-white shadow-lg rounded-2xl" style="border: 2px solid #e0eaff;">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
                            <th class="pb-3 pl-2 text-left">Name</th>
                            <th class="pb-3 text-left">Month</th>
                            <th class="pb-3 text-left">Task Completed</th>
                            <th class="pb-3 text-left">Attendance Rate</th>
                            <th class="pb-3 text-left">KPI Score</th>
                            <th class="pb-3 text-left">Final Score</th>
                            <th class="pb-3 text-left">Status</th>
                            <th class="pb-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody id="performaTableBody">
                        @include('admin.performa._rows')
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between gap-3 pt-4 mt-2 border-t border-gray-100">
                {{-- PER PAGE --}}
                <select id="perPageSelect"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach ([10, 15, 20, 50, 100] as $opt)
                        <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>{{ $opt }} / page</option>
                    @endforeach
                </select>

                <div id="performaPagination">
                    @if ($paginator && $paginator->hasPages())
                        {{ $paginator->appends(request()->query())->links('vendor.pagination.simple-blue') }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL MODAL --}}
    <div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 z-10 flex items-center justify-between p-6 bg-white border-b">
                <h3 class="text-lg font-semibold text-blue-900">
                    Detail Performance - <span id="modalEmployeeName"></span>
                </h3>
                <button onclick="closeDetailModal()"
                    class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100">&times;</button>
            </div>
            <div class="p-6" id="modalContent">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>

    <style>
        #detailModal.show {
            display: flex;
        }

        #detailModal {
            display: none;
        }
    </style>

    <script>
        const tableBody     = document.getElementById('performaTableBody');
        const paginationEl  = document.getElementById('performaPagination');
        const perPageSelect = document.getElementById('perPageSelect');
        const filterPerPage = document.getElementById('filterPerPage');

        function loadTable(url) {
            tableBody.style.opacity = '0.4';
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                tableBody.innerHTML = data.rows;
                paginationEl.innerHTML = data.pagination;
                tableBody.style.opacity = '1';
                bindPaginationLinks();
                history.pushState(null, '', url);
            })
            .catch(() => { tableBody.style.opacity = '1'; });
        }

        function bindPaginationLinks() {
            paginationEl.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    loadTable(this.href);
                });
            });
        }

        perPageSelect.addEventListener('change', function () {
            filterPerPage.value = this.value;
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page');
            loadTable(url.toString());
        });

        bindPaginationLinks();

        function openDetailModal(karyawanId) {
            document.getElementById('detailModal').classList.add('show');
            document.getElementById('modalContent').innerHTML = `
        <div class="flex justify-center py-8">
            <div class="w-8 h-8 border-b-2 border-blue-600 rounded-full animate-spin"></div>
        </div>`;

            fetch(`/admin/performa/${karyawanId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        document.getElementById('modalContent').innerHTML =
                            `<p class="py-8 text-center text-red-500">${data.error}</p>`;
                        return;
                    }

                    document.getElementById('modalEmployeeName').innerText = data.info.name;

                    // Build photo HTML
                    let photoHtml = '';
                    if (data.info.foto_profil) {
                        photoHtml = `
                    <img src="/storage/${data.info.foto_profil}"
                         alt="Profile"
                         class="object-cover w-32 h-32 border-4 border-blue-900 rounded-full">`;
                    } else {
                        const initial = (data.info.name || '?').charAt(0).toUpperCase();
                        photoHtml = `
                    <div class="flex items-center justify-center w-32 h-32 bg-blue-100 border-4 border-blue-900 rounded-full">
                        <span class="text-3xl font-bold text-blue-900">${initial}</span>
                    </div>`;
                    }

                    // Build catatan HTML
                    let catatanHtml = '';
                    if (data.catatan && data.catatan.trim() !== '') {
                        catatanHtml = `
                    <div class="mt-6">
                        <h3 class="mb-2 text-lg font-semibold text-gray-800">Admin Notes</h3>
                        <div class="p-4 border-l-4 border-yellow-500 rounded bg-yellow-50">
                            <p class="text-sm text-gray-700">${data.catatan}</p>
                        </div>
                    </div>`;
                    }

                    const statusColor = data.status_performance == 'Excellent' ? 'text-emerald-400' :
                        data.status_performance == 'Good' ? 'text-sky-400' :
                        data.status_performance == 'Average' ? 'text-yellow-400' : 'text-red-400';

                    let html = `
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <h2 class="mb-4 text-lg font-bold text-blue-900">Employee's Info</h2>
                        <div class="grid grid-cols-2 gap-y-3 gap-x-6">
                            <div><label class="block text-xs font-semibold text-blue-900">Name</label><p class="text-sm text-gray-600">${data.info.name}</p></div>
                            <div><label class="block text-xs font-semibold text-blue-900">Email</label><p class="text-sm text-gray-600">${data.info.email}</p></div>
                            <div><label class="block text-xs font-semibold text-blue-900">Phone</label><p class="text-sm text-gray-600">${data.info.phone || '-'}</p></div>
                            <div><label class="block text-xs font-semibold text-blue-900">Role</label><p class="text-sm text-gray-600 capitalize">${data.info.role || '-'}</p></div>
                            <div><label class="block text-xs font-semibold text-blue-900">Join Date</label><p class="text-sm text-gray-600">${data.info.join_date || '-'}</p></div>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        ${photoHtml}
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-6 mt-6 lg:grid-cols-3">
                    <div>
                        <h2 class="mb-4 text-lg font-bold text-blue-900">Attendance Summary</h2>
                        <div class="space-y-2">
                            <div><p class="font-semibold text-blue-900">Attendance Rate</p><p class="font-semibold text-sky-500">${data.attendance_summary.attendance_rate}%</p></div>
                            <div><p class="font-semibold text-blue-900">Present</p><p class="text-emerald-500">${data.attendance_summary.present} Days</p></div>
                            <div><p class="font-semibold text-blue-900">Absent</p><p class="text-red-500">${data.attendance_summary.absent} Days</p></div>
                        </div>
                        <div class="mt-8">
                            <h2 class="mb-3 text-lg font-bold text-blue-900">Performance Score</h2>
                            <h3 class="text-2xl font-semibold ${statusColor}">${data.status_performance}</h3>
                            <div class="flex items-end gap-2 mt-2">
                                <span class="text-2xl font-bold text-blue-900">${data.performance_score}</span>
                                <span class="text-xs text-gray-400">/ 100</span>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <h2 class="mb-4 text-lg font-bold text-blue-900">KPI Breakdown</h2>
                        <div class="overflow-hidden border border-gray-200 rounded-xl">
                            <div class="grid grid-cols-2 px-5 py-3 text-sm font-medium text-gray-500 bg-gray-100">
                                <div>KPI</div><div>Score</div>
                            </div>
                            <div class="text-sm text-blue-900 divide-y divide-gray-200">
                                <div class="grid grid-cols-2 px-5 py-4"><div>Quality</div><div>${data.kpi.quality}</div></div>
                                <div class="grid grid-cols-2 px-5 py-4"><div>Productivity</div><div>${data.kpi.productivity}</div></div>
                                <div class="grid grid-cols-2 px-5 py-4"><div>Teamwork</div><div>${data.kpi.teamwork}</div></div>
                                <div class="grid grid-cols-2 px-5 py-4"><div>Discipline</div><div>${data.kpi.discipline}</div></div>
                                <div class="grid grid-cols-2 px-5 py-4 font-bold bg-blue-50"><div>KPI Score</div><div class="text-blue-600">${data.kpi.kpi_score}</div></div>
                                <div class="grid grid-cols-2 px-5 py-4 font-bold bg-purple-50"><div>Total Performance Score</div><div class="text-lg text-purple-600">${data.performance_score}</div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <h2 class="flex items-center gap-2 mb-3 text-lg font-bold text-blue-900">
                        Task Summary
                        ${data.task_source === 'trello'
                            ? `<span class="inline-flex items-center gap-1 text-sm font-normal text-[#0052CC]">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M21 0H3C1.343 0 0 1.343 0 3v18c0 1.656 1.343 3 3 3h18c1.656 0 3-1.344 3-3V3c0-1.657-1.344-3-3-3zM10.44 18.18c0 .795-.645 1.44-1.44 1.44H4.56c-.795 0-1.44-.645-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44H9c.795 0 1.44.645 1.44 1.44v12.36zm10.44-7.08c0 .794-.645 1.44-1.44 1.44H15c-.795 0-1.44-.646-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44h4.44c.795 0 1.44.645 1.44 1.44v5.28z"/></svg>
                                Via Trello</span>`
                            : `<span class="text-sm font-normal text-gray-400">Manual Input</span>`
                        }
                    </h2>
                    <div class="p-4 border border-gray-200 rounded-xl bg-gray-50">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="p-3 text-center bg-white border border-gray-200 rounded-lg">
                                <div class="text-2xl font-bold text-[#0052CC]">${data.task_done ?? 0}</div>
                                <div class="mt-1 text-xs text-gray-500">Tasks Done</div>
                            </div>
                            <div class="p-3 text-center bg-white border border-gray-200 rounded-lg">
                                <div class="text-2xl font-bold text-gray-500">${data.task_target ?? 1}</div>
                                <div class="mt-1 text-xs text-gray-500">Monthly Target</div>
                            </div>
                            <div class="p-3 text-center bg-white border border-gray-200 rounded-lg">
                                <div class="text-2xl font-bold text-teal-600">${data.task_score ?? 0}</div>
                                <div class="mt-1 text-xs text-gray-500">Task Score</div>
                            </div>
                        </div>
                        <div class="pt-3 mt-3 border-t border-gray-200">
                            <p class="text-xs text-gray-400">Formula: <span class="font-medium text-gray-500">Performance Score = (KPI × 50%) + (Task Score × 50%)</span></p>
                        </div>
                    </div>
                </div>
                ${catatanHtml}
            `;

                    document.getElementById('modalContent').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalContent').innerHTML =
                        `<p class="py-8 text-center text-red-500">Failed to load data. Please try again.</p>`;
                });
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('show');
        }

        // Close modal when clicking outside
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });
    </script>
@endsection
