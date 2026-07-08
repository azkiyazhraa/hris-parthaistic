@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">

        {{-- CARD HEADER --}}
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">My Performance</h1>
                <p class="text-sm text-gray-700/80">Monitor your performance and productivity.</p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="bg-white shadow-md rounded-2xl">
            <div class="grid grid-cols-1 divide-y divide-gray-200 sm:grid-cols-3 sm:divide-y-0 sm:divide-x">
                {{-- Average Score --}}
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-sm font-medium text-gray-400">Average Score (All Time)</p>
                    <div class="flex items-end gap-2 mt-1">
                        <h2 class="text-xl font-semibold text-[#0B0F6D] leading-none">
                            {{ round($averageScore ?? 0) }}
                        </h2>
                    </div>
                </div>

                {{-- Latest Score --}}
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-sm font-medium text-gray-400">Latest Score</p>
                    <div class="flex items-end gap-2 mt-1">
                        <h2 class="text-xl font-semibold text-[#0B0F6D] leading-none">
                            {{ $latestPerforma->performance_score ?? 0 }}
                        </h2>
                        @if ($latestPerforma)
                            <span class="text-xs text-gray-400">{{ $latestPerforma->bulan_text }}
                                {{ $latestPerforma->tahun }}</span>
                        @endif
                    </div>
                </div>

                {{-- Latest Status --}}
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-sm font-medium text-gray-400">Latest Status</p>
                    <div class="mt-1">
                        @if ($latestPerforma)
                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold
                            @if ($latestPerforma->performance_score >= 90) bg-green-100 text-green-700
                            @elseif($latestPerforma->performance_score >= 75) bg-blue-100 text-blue-700
                            @elseif($latestPerforma->performance_score >= 60) bg-yellow-100 text-yellow-700
                            @elseif($latestPerforma->performance_score >= 50) bg-orange-100 text-orange-700
                            @else bg-red-100 text-red-700 @endif
                        ">
                                {{ $latestPerforma->rating['label'] }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- PERFORMANCE CHART --}}
        <div class="p-6 bg-white shadow-lg rounded-2xl" style="border: 2px solid #e0eaff;">
            <h3 class="mb-4 text-lg font-semibold text-blue-900"> Quarterly Performance Chart ({{ date('Y') }})</h3>
            <div class="h-64">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="w-full p-6 bg-white shadow-lg rounded-2xl" style="border: 2px solid #e0eaff;">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                    <thead>
                        <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
                            <th class="pb-3 text-left whitespace-nowrap">Period</th>
                            <th class="pb-3 text-left whitespace-nowrap">Quarter</th>
                            <th class="pb-3 text-left whitespace-nowrap">Quality</th>
                            <th class="pb-3 text-left whitespace-nowrap">Productivity</th>
                            <th class="pb-3 text-left whitespace-nowrap">Teamwork</th>
                            <th class="pb-3 text-left whitespace-nowrap">Discipline</th>
                            <th class="pb-3 text-left whitespace-nowrap">KPI Score</th>
                            <th class="pb-3 text-left whitespace-nowrap">
                                <span class="flex items-center gap-1">
                                    Task Completed
                                    <svg class="w-3 h-3 text-[#0052CC] opacity-50" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M21 0H3C1.343 0 0 1.343 0 3v18c0 1.656 1.343 3 3 3h18c1.656 0 3-1.344 3-3V3c0-1.657-1.344-3-3-3zM10.44 18.18c0 .795-.645 1.44-1.44 1.44H4.56c-.795 0-1.44-.645-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44H9c.795 0 1.44.645 1.44 1.44v12.36zm10.44-7.08c0 .794-.645 1.44-1.44 1.44H15c-.795 0-1.44-.646-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44h4.44c.795 0 1.44.645 1.44 1.44v5.28z" />
                                    </svg>
                                </span>
                            </th>
                            <th class="pb-3 text-left whitespace-nowrap">Attendance</th>
                            <th class="pb-3 text-left whitespace-nowrap">Total Score</th>
                            <th class="pb-3 text-left whitespace-nowrap">Status</th>
                            <th class="pb-3 text-left whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($performas as $item)
                            <tr class="transition border-t border-gray-100 hover:bg-blue-50/40">
                                <td class="py-3 pl-2">
                                    <span class="font-medium text-gray-800">{{ $item->bulan_text }}
                                        {{ $item->tahun }}</span>
                                </td>
                                <td class="py-3">
                                    <span
                                        class="px-2 py-1 text-xs text-gray-700 bg-gray-200 rounded">{{ $item->quarter }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-green-600 rounded-full h-1.5" style="width: {{ $item->quality }}%">
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $item->quality }}%</span>
                                </td>
                                <td class="py-3">
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-yellow-600 rounded-full h-1.5"
                                            style="width: {{ $item->productivity }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $item->productivity }}%</span>
                                </td>
                                <td class="py-3">
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-purple-600 rounded-full h-1.5"
                                            style="width: {{ $item->teamwork }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $item->teamwork }}%</span>
                                </td>
                                <td class="py-3">
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-indigo-600 rounded-full h-1.5"
                                            style="width: {{ $item->discipline }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $item->discipline }}%</span>
                                </td>
                                <td class="py-3 font-semibold text-blue-600">{{ $item->kpi_score }}%</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3 h-3 text-[#0052CC] opacity-40" fill="currentColor"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M21 0H3C1.343 0 0 1.343 0 3v18c0 1.656 1.343 3 3 3h18c1.656 0 3-1.344 3-3V3c0-1.657-1.344-3-3-3zM10.44 18.18c0 .795-.645 1.44-1.44 1.44H4.56c-.795 0-1.44-.645-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44H9c.795 0 1.44.645 1.44 1.44v12.36zm10.44-7.08c0 .794-.645 1.44-1.44 1.44H15c-.795 0-1.44-.646-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44h4.44c.795 0 1.44.645 1.44 1.44v5.28z" />
                                        </svg>
                                        <span class="font-medium text-gray-700">{{ $item->task_done }}</span>
                                    </div>
                                    <span class="text-[10px] text-gray-400">score: {{ $item->task_score }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-green-600 rounded-full h-1.5"
                                            style="width: {{ $item->attendance_rate }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $item->attendance_rate }}%</span>
                                </td>
                                <td class="py-3 text-lg font-bold text-purple-600">{{ $item->performance_score }}</td>
                                <td class="py-3">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold
                                    @if ($item->performance_score >= 90) bg-green-100 text-green-700
                                    @elseif($item->performance_score >= 75) bg-blue-100 text-blue-700
                                    @elseif($item->performance_score >= 60) bg-yellow-100 text-yellow-700
                                    @elseif($item->performance_score >= 50) bg-orange-100 text-orange-700
                                    @else bg-red-100 text-red-700 @endif
                                ">
                                        {{ $item->rating['label'] }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <button onclick="openDetailModal({{ $item->id }})"
                                        class="text-blue-500 hover:text-blue-700" title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-500">No Performance Data Yet</h3>
                                        <p class="text-sm text-gray-400">Your performance assessments will appear here once
                                            available.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($performas->hasPages())
                <div class="mt-4">
                    {{ $performas->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- DETAIL MODAL --}}
    <div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            {{-- Modal Header --}}
            <div class="sticky top-0 z-10 flex items-center justify-between p-6 bg-white border-b">
                <h3 class="text-lg font-semibold text-blue-900">
                    Detail Performance - <span id="modalPeriod"></span>
                </h3>
                <button onclick="closeDetailModal()"
                    class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100">&times;</button>
            </div>

            {{-- Modal Content --}}
            <div class="p-6" id="modalContent">
                <div class="flex justify-center py-8">
                    <div class="w-8 h-8 border-b-2 border-blue-600 rounded-full animate-spin"></div>
                </div>
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

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ============ PERFORMANCE CHART ============
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const quarterlyData = @json($quarterlyData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Q1 (Jan-Mar)', 'Q2 (Apr-Jun)', 'Q3 (Jul-Sep)', 'Q4 (Oct-Dec)'],
                datasets: [{
                    label: 'Performance Score',
                    data: [
                        quarterlyData.Q1 || 0,
                        quarterlyData.Q2 || 0,
                        quarterlyData.Q3 || 0,
                        quarterlyData.Q4 || 0
                    ],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Score'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Score: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });

        // ============ DETAIL MODAL FUNCTIONS ============
        function openDetailModal(performaId) {
            document.getElementById('detailModal').classList.add('show');
            document.getElementById('modalContent').innerHTML = `
                <div class="flex justify-center py-8">
                    <div class="w-8 h-8 border-b-2 border-blue-600 rounded-full animate-spin"></div>
                </div>`;

            fetch(`/performa/${performaId}`, {
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

                    document.getElementById('modalPeriod').innerText =
                        (data.bulan_text || '') + ' ' + (data.tahun || '');

                    // Build photo HTML
                    let photoHtml = '';
                    if (data.foto_profil) {
                        photoHtml = `
                            <img src="/storage/${data.foto_profil}"
                                 alt="Profile"
                                 class="object-cover w-32 h-32 border-4 border-blue-900 rounded-full">`;
                    } else {
                        const initial = (data.nama_karyawan || '?').charAt(0).toUpperCase();
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

                    const statusLabel = getStatusLabel(data.performance_score);
                    const statusColor = getStatusColor(data.performance_score);

                    // Cek apakah attendance_summary ada (dari data absensi real)
                    const presentCount = data.attendance_summary ? data.attendance_summary.present : 0;
                    const lateCount = data.attendance_summary ? data.attendance_summary.late : 0;
                    const absentCount = data.attendance_summary ? data.attendance_summary.absent : 0;

                    const html = `
                        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <div class="lg:col-span-2">
                                <h2 class="mb-4 text-lg font-bold text-blue-900">Employee's Info</h2>
                                <div class="grid grid-cols-2 gap-y-3 gap-x-6">
                                    <div><label class="block text-xs font-semibold text-blue-900">Name</label><p class="text-sm text-gray-600">${data.nama_karyawan || '-'}</p></div>
                                    <div><label class="block text-xs font-semibold text-blue-900">Email</label><p class="text-sm text-gray-600">${data.email || '-'}</p></div>
                                    <div><label class="block text-xs font-semibold text-blue-900">Phone</label><p class="text-sm text-gray-600">${data.phone || '-'}</p></div>
                                    <div><label class="block text-xs font-semibold text-blue-900">Role</label><p class="text-sm text-gray-600 capitalize">${data.role || '-'}</p></div>
                                    <div><label class="block text-xs font-semibold text-blue-900">Join Date</label><p class="text-sm text-gray-600">${data.join_date_formatted || '-'}</p></div>
                                </div>
                            </div>
                            <div class="flex items-start justify-center">
                                ${photoHtml}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 mt-6 lg:grid-cols-3">
                            <div>
                                <h2 class="mb-4 text-lg font-bold text-blue-900">Attendance Summary</h2>
                                <div class="space-y-2">
                                    <div>
                                        <p class="font-semibold text-blue-900">Attendance Rate</p>
                                        <p class="font-semibold text-sky-500">${data.attendance_rate || 0}%</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-blue-900">Present</p>
                                        <p class="text-emerald-500">${presentCount} Days</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-blue-900">Absent</p>
                                        <p class="text-red-500">${absentCount} Days</p>
                                    </div>
                                </div>

                                <div class="mt-8">
                                    <h2 class="mb-3 text-lg font-bold text-blue-900">Performance Score</h2>
                                    <h3 class="text-2xl font-semibold ${statusColor}">${statusLabel}</h3>
                                    <div class="flex items-end gap-2 mt-2">
                                        <span class="text-2xl font-bold text-blue-900">${data.performance_score || 0}</span>
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
                                        <div class="grid grid-cols-2 px-5 py-3"><div>Quality</div><div class="font-semibold">${data.quality || 0}</div></div>
                                        <div class="grid grid-cols-2 px-5 py-3"><div>Productivity</div><div class="font-semibold">${data.productivity || 0}</div></div>
                                        <div class="grid grid-cols-2 px-5 py-3"><div>Teamwork</div><div class="font-semibold">${data.teamwork || 0}</div></div>
                                        <div class="grid grid-cols-2 px-5 py-3"><div>Discipline</div><div class="font-semibold">${data.discipline || 0}</div></div>
                                        <div class="grid grid-cols-2 px-5 py-3 font-bold bg-blue-50"><div>KPI Score</div><div class="text-blue-600">${data.kpi_score || 0}</div></div>
                                        <div class="grid grid-cols-2 px-5 py-3 font-bold bg-purple-50"><div>Total Performance Score</div><div class="text-lg text-purple-600">${data.performance_score || 0}</div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h2 class="flex items-center gap-2 mb-3 text-lg font-bold text-blue-900">
                                Task Summary
                                <svg class="w-4 h-4 text-[#0052CC]" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 0H3C1.343 0 0 1.343 0 3v18c0 1.656 1.343 3 3 3h18c1.656 0 3-1.344 3-3V3c0-1.657-1.344-3-3-3zM10.44 18.18c0 .795-.645 1.44-1.44 1.44H4.56c-.795 0-1.44-.645-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44H9c.795 0 1.44.645 1.44 1.44v12.36zm10.44-7.08c0 .794-.645 1.44-1.44 1.44H15c-.795 0-1.44-.646-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44h4.44c.795 0 1.44.645 1.44 1.44v5.28z"/>
                                </svg>
                                <span class="text-sm font-normal text-[#0052CC]">via Trello</span>
                            </h2>
                            <div class="p-4 border border-gray-200 rounded-xl bg-gray-50">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="flex-shrink-0 inline-block w-2 h-2 bg-yellow-400 rounded-full"></span>
                                    <span class="text-xs text-gray-500">Trello API not connected — entered manually by admin</span>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="p-3 text-center bg-white border border-gray-200 rounded-lg">
                                        <div class="text-2xl font-bold text-[#0052CC]">${data.task_done ?? 0}</div>
                                        <div class="mt-1 text-xs text-gray-500">Tasks Done</div>
                                    </div>
                                    <div class="p-3 text-center bg-white border border-gray-200 rounded-lg">
                                        <div class="text-2xl font-bold text-gray-500">${data.task_target ?? 0}</div>
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

        // Helper functions for status
        function getStatusLabel(score) {
            if (score >= 90) return 'Excellent';
            if (score >= 75) return 'Good';
            if (score >= 60) return 'Average';
            if (score >= 50) return 'Poor';
            return 'Very Poor';
        }

        function getStatusColor(score) {
            if (score >= 90) return 'text-emerald-400';
            if (score >= 75) return 'text-sky-400';
            if (score >= 60) return 'text-yellow-400';
            if (score >= 50) return 'text-orange-400';
            return 'text-red-400';
        }
    </script>
@endsection
