@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">

        {{-- CARD HEADER --}}
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">My Performance</h1>
                <p class="text-gray-700/80 text-sm">Monitor your performance and productivity.</p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="bg-white rounded-2xl shadow-md">
            <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-200">
                {{-- Average Score --}}
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-gray-400 text-sm font-medium">Average Score (All Time)</p>
                    <div class="flex items-end gap-2 mt-1">
                        <h2 class="text-xl font-semibold text-[#0B0F6D] leading-none">
                            {{ round($averageScore ?? 0) }}
                        </h2>
                    </div>
                </div>

                {{-- Latest Score --}}
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-gray-400 text-sm font-medium">Latest Score</p>
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

                {{-- Latest Rating --}}
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-gray-400 text-sm font-medium">Latest Rating</p>
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
        <div class="bg-white rounded-2xl shadow-lg p-6" style="border: 2px solid #e0eaff;">
            <h3 class="text-lg font-semibold text-blue-900 mb-4"> Quarterly Performance Chart ({{ date('Y') }})</h3>
            <div class="h-64">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-lg w-full p-6" style="border: 2px solid #e0eaff;">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                    <thead>
                        <tr class="text-gray-400 font-medium text-xs uppercase tracking-wide border-b">
                            <th class="text-left pb-3 whitespace-nowrap">Period</th>
                            <th class="text-left pb-3 whitespace-nowrap">Quarter</th>
                            <th class="text-left pb-3 whitespace-nowrap">Quality</th>
                            <th class="text-left pb-3 whitespace-nowrap">Productivity</th>
                            <th class="text-left pb-3 whitespace-nowrap">Teamwork</th>
                            <th class="text-left pb-3 whitespace-nowrap">Discipline</th>
                            <th class="text-left pb-3 whitespace-nowrap">KPI Score</th>
                            <th class="text-left pb-3 whitespace-nowrap">Attendance</th>
                            <th class="text-left pb-3 whitespace-nowrap">Total Score</th>
                            <th class="text-left pb-3 whitespace-nowrap">Status</th>
                            <th class="text-left pb-3 whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($performas as $item)
                            <tr class="border-t border-gray-100 hover:bg-blue-50/40 transition">
                                <td class="py-3 pl-2">
                                    <span class="font-medium text-gray-800">{{ $item->bulan_text }}
                                        {{ $item->tahun }}</span>
                                </td>
                                <td class="py-3">
                                    <span
                                        class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">{{ $item->quarter }}</span>
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
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-green-600 rounded-full h-1.5"
                                            style="width: {{ $item->attendance_rate }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $item->attendance_rate }}%</span>
                                </td>
                                <td class="py-3 font-bold text-lg text-purple-600">{{ $item->performance_score }}</td>
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
                                <td colspan="11" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-500">No Performance Data Yet</h3>
                                        <p class="text-gray-400 text-sm">Your performance assessments will appear here once
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
    <div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            {{-- Modal Header --}}
            <div class="sticky top-0 bg-white z-10 flex justify-between items-center border-b p-6">
                <h3 class="text-lg font-semibold text-blue-900">
                    Detail Performance - <span id="modalPeriod"></span>
                </h3>
                <button onclick="closeDetailModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">&times;</button>
            </div>

            {{-- Modal Content --}}
            <div class="p-6" id="modalContent">
                <div class="flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
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
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
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
                            `<p class="text-center text-red-500 py-8">${data.error}</p>`;
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
                                 class="w-32 h-32 rounded-full border-4 border-blue-900 object-cover">`;
                    } else {
                        const initial = (data.nama_karyawan || '?').charAt(0).toUpperCase();
                        photoHtml = `
                            <div class="w-32 h-32 rounded-full border-4 border-blue-900 flex items-center justify-center bg-blue-100">
                                <span class="text-3xl font-bold text-blue-900">${initial}</span>
                            </div>`;
                    }

                    // Build catatan HTML
                    let catatanHtml = '';
                    if (data.catatan && data.catatan.trim() !== '') {
                        catatanHtml = `
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Admin Notes</h3>
                                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                                    <p class="text-gray-700 text-sm">${data.catatan}</p>
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
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-2">
                                <h2 class="text-lg font-bold text-blue-900 mb-4">Employee's Info</h2>
                                <div class="grid grid-cols-2 gap-y-3 gap-x-6">
                                    <div><label class="block text-xs font-semibold text-blue-900">Name</label><p class="text-gray-600 text-sm">${data.nama_karyawan || '-'}</p></div>
                                    <div><label class="block text-xs font-semibold text-blue-900">Email</label><p class="text-gray-600 text-sm">${data.email || '-'}</p></div>
                                    <div><label class="block text-xs font-semibold text-blue-900">Phone</label><p class="text-gray-600 text-sm">${data.phone || '-'}</p></div>
                                    <div><label class="block text-xs font-semibold text-blue-900">Role</label><p class="text-gray-600 text-sm capitalize">${data.role || '-'}</p></div>
                                    <div><label class="block text-xs font-semibold text-blue-900">Join Date</label><p class="text-gray-600 text-sm">${data.join_date_formatted || '-'}</p></div>
                                </div>
                            </div>
                            <div class="flex justify-center items-start">
                                ${photoHtml}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                            <div>
                                <h2 class="text-lg font-bold text-blue-900 mb-4">Attendance Summary</h2>
                                <div class="space-y-2">
                                    <div>
                                        <p class="font-semibold text-blue-900">Attendance Rate</p>
                                        <p class="text-sky-500 font-semibold">${data.attendance_rate || 0}%</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-blue-900">Present</p>
                                        <p class="text-emerald-500">${presentCount} Days</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-blue-900">Late</p>
                                        <p class="text-yellow-500">${lateCount} Days</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-blue-900">Absent</p>
                                        <p class="text-red-500">${absentCount} Days</p>
                                    </div>
                                </div>

                                <div class="mt-8">
                                    <h2 class="text-lg font-bold text-blue-900 mb-3">Performance Score</h2>
                                    <h3 class="text-2xl font-semibold ${statusColor}">${statusLabel}</h3>
                                    <div class="flex items-end gap-2 mt-2">
                                        <span class="text-2xl font-bold text-blue-900">${data.performance_score || 0}</span>
                                        <span class="text-xs text-gray-400">/ 100</span>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <h2 class="text-lg font-bold text-blue-900 mb-4">KPI Breakdown</h2>
                                <div class="rounded-xl border border-gray-200 overflow-hidden">
                                    <div class="grid grid-cols-2 bg-gray-100 text-gray-500 text-sm font-medium px-5 py-3">
                                        <div>KPI</div><div>Score</div>
                                    </div>
                                    <div class="divide-y divide-gray-200 text-blue-900 text-sm">
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

                        ${catatanHtml}
                    `;

                    document.getElementById('modalContent').innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalContent').innerHTML =
                        `<p class="text-center text-red-500 py-8">Failed to load data. Please try again.</p>`;
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
