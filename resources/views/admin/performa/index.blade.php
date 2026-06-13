@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">

        {{-- CARD HEADER --}}
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">
                    Performance
                </h1>
                <p class="text-gray-700/80 text-sm">
                    Monitor employee productivity and task performance.
                </p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- CARD SUMMARY --}}
        <div class="bg-white rounded-2xl shadow-md">
            <div class="grid grid-cols-1 sm:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-gray-200">

                <!-- ITEM 1 - Average Score -->
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-gray-400 text-sm font-medium">Average Score (All Time)</p>
                    <div class="flex items-end gap-2 mt-1">
                        <h2 class="text-xl font-semibold text-[#0B0F6D] leading-none">
                            {{ $averageScore }}
                        </h2>
                    </div>
                </div>

                <!-- ITEM 2 - Total Employees -->
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-gray-400 text-sm font-medium">Total Employees</p>
                    <div class="flex items-end gap-2 mt-1">
                        <h2 class="text-xl font-semibold text-[#0B0F6D] leading-none">
                            {{ $karyawans->count() }}
                        </h2>
                    </div>
                </div>

                <!-- ITEM 3 - Average Attendance -->
                <div class="flex flex-col justify-center px-5 py-4">
                    <p class="text-gray-400 text-sm font-medium">Average Attendance</p>
                    <div class="flex items-end gap-2 mt-1">
                        <h2 class="text-xl font-semibold text-[#0B0F6D] leading-none">
                            {{ $averageScore }}%
                        </h2>
                    </div>
                </div>

                <!-- ITEM 4 - Top Performer -->
                <div class="flex items-center gap-3 px-5 py-4">
                    @if ($topPerformer)
                        @php
                            $initial = substr($topPerformer->nama_lengkap, 0, 1);
                        @endphp
                        <div
                            class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center border-2 border-[#0B0F6D]">
                            @if ($topPerformer->foto_profil)
                                <img src="{{ Storage::url($topPerformer->foto_profil) }}" alt="profile"
                                    class="w-12 h-12 rounded-full object-cover">
                            @else
                                <span class="text-lg font-bold text-[#0B0F6D]">{{ strtoupper($initial) }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-400 text-sm font-medium">Top Performer</p>
                            <h3 class="text-[#0B0F6D] font-semibold leading-tight">{{ $topPerformer->nama_lengkap }}</h3>
                            <p class="text-xs text-gray-400">Score: {{ $topScore }}</p>
                        </div>
                        <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 .587l3.668 7.431L24 9.75l-6 5.847 1.416 8.253L12 19.771l-7.416 4.079L6 15.597 0 9.75l8.332-1.732z" />
                        </svg>
                    @else
                        <div class="flex-1">
                            <p class="text-gray-400 text-sm font-medium">Top Performer</p>
                            <h3 class="text-[#0B0F6D] font-semibold leading-tight">-</h3>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex gap-2">
            <a href="{{ route('admin.performa.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                + Add Assessment
            </a>
            <a href="{{ route('admin.performa.bulk') }}"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                Bulk Assessment
            </a>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-lg w-full p-6" style="border: 2px solid #e0eaff;">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-gray-400 font-medium text-xs uppercase tracking-wide border-b">
                            <th class="text-left pb-3 pl-2">Name</th>
                            <th class="text-left pb-3">Month</th>
                            <th class="text-left pb-3">Task Done</th>
                            <th class="text-left pb-3">Attendance Rate</th>
                            <th class="text-left pb-3">KPI Score</th>
                            <th class="text-left pb-3">Final Score</th>
                            <th class="text-left pb-3">Status</th>
                            <th class="text-left pb-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($performances as $item)
                            <tr class="border-t border-gray-100 hover:bg-blue-50/40 transition">
                                <td class="py-3 pl-2">
                                    <div class="flex items-center gap-3">
                                        @php $initial = substr($item->info->name, 0, 1); @endphp
                                        <div
                                            class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center border-2 border-blue-300 overflow-hidden">
                                            @if (isset($item->info->foto_profil) && $item->info->foto_profil)
                                                <img src="{{ Storage::url($item->info->foto_profil) }}" alt="profile"
                                                    class="w-9 h-9 rounded-full object-cover">
                                            @else
                                                <span
                                                    class="text-sm font-bold text-blue-500">{{ strtoupper($initial) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-800">{{ $item->info->name }}</span>
                                            <br>
                                            <small class="text-gray-400 capitalize">{{ $item->info->role ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-gray-600 whitespace-nowrap">
                                    @php
                                        $monthNames = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'];
                                    @endphp
                                    {{ $monthNames[$item->bulan] ?? '-' }} {{ $item->tahun }}
                                </td>
                                <td class="py-3 text-gray-700">{{ $item->task_done }}</td>
                                <td class="py-3 text-gray-700">{{ $item->attendance_summary->attendance_rate }}%</td>
                                <td class="py-3 text-gray-700 font-semibold">{{ $item->kpi->kpi_score }}</td>
                                <td class="py-3">
                                    <span class="font-bold text-purple-700 text-base">{{ $item->performance_score }}</span>
                                    <span class="text-gray-400 text-xs">/ 100</span>
                                </td>
                                <td class="py-3">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold
                                        @if ($item->status_performance == 'Excellent') bg-green-100 text-green-700
                                        @elseif($item->status_performance == 'Good') bg-blue-100 text-blue-700
                                        @elseif($item->status_performance == 'Average') bg-yellow-100 text-yellow-700
                                        @elseif($item->status_performance == 'Poor') bg-orange-100 text-orange-700
                                        @elseif($item->status_performance == 'Very Poor') bg-red-100 text-red-700
                                        @else bg-gray-100 text-gray-700 @endif
                                    ">
                                        {{ $item->status_performance }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        @if ($item->id)
                                            <button onclick="openDetailModal({{ $item->karyawan_id }})"
                                                class="text-blue-500 hover:text-blue-700" title="Detail">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <a href="{{ route('admin.performa.edit', $item->id) }}"
                                                class="text-yellow-500 hover:text-yellow-700" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.performa.destroy', $item->id) }}" method="POST"
                                                class="inline" onsubmit="return confirm('Hapus penilaian ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700"
                                                    title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-xs">No data</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-500">No Performance Data Yet</h3>
                                        <p class="text-gray-400 text-sm">Start by adding your first employee performance
                                            assessment.</p>
                                        <div class="flex gap-2 mt-2">
                                            <a href="{{ route('admin.performa.create') }}"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">+
                                                Add Assessment</a>
                                            <a href="{{ route('admin.performa.bulk') }}"
                                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">Bulk
                                                Assessment</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- DETAIL MODAL --}}
    <div id="detailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white z-10 flex justify-between items-center border-b p-6">
                <h3 class="text-lg font-semibold text-blue-900">
                    Detail Performance - <span id="modalEmployeeName"></span>
                </h3>
                <button onclick="closeDetailModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">&times;</button>
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
        function openDetailModal(karyawanId) {
            document.getElementById('detailModal').classList.add('show');
            document.getElementById('modalContent').innerHTML = `
        <div class="flex justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
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
                            `<p class="text-center text-red-500 py-8">${data.error}</p>`;
                        return;
                    }

                    document.getElementById('modalEmployeeName').innerText = data.info.name;

                    // Build photo HTML
                    let photoHtml = '';
                    if (data.info.foto_profil) {
                        photoHtml = `
                    <img src="/storage/${data.info.foto_profil}" 
                         alt="Profile" 
                         class="w-32 h-32 rounded-full border-4 border-blue-900 object-cover">`;
                    } else {
                        const initial = (data.info.name || '?').charAt(0).toUpperCase();
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

                    const statusColor = data.status_performance == 'Excellent' ? 'text-emerald-400' :
                        data.status_performance == 'Good' ? 'text-sky-400' :
                        data.status_performance == 'Average' ? 'text-yellow-400' : 'text-red-400';

                    let html = `
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <h2 class="text-lg font-bold text-blue-900 mb-4">Employee's Info</h2>
                        <div class="grid grid-cols-2 gap-y-3 gap-x-6">
                            <div><label class="block text-xs font-semibold text-blue-900">Name</label><p class="text-gray-600 text-sm">${data.info.name}</p></div>
                            <div><label class="block text-xs font-semibold text-blue-900">Email</label><p class="text-gray-600 text-sm">${data.info.email}</p></div>
                            <div><label class="block text-xs font-semibold text-blue-900">Phone</label><p class="text-gray-600 text-sm">${data.info.phone || '-'}</p></div>
                            <div><label class="block text-xs font-semibold text-blue-900">Role</label><p class="text-gray-600 text-sm capitalize">${data.info.role || '-'}</p></div>
                            <div><label class="block text-xs font-semibold text-blue-900">Join Date</label><p class="text-gray-600 text-sm">${data.info.join_date || '-'}</p></div>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        ${photoHtml}
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                    <div>
                        <h2 class="text-lg font-bold text-blue-900 mb-4">Attendance Summary</h2>
                        <div class="space-y-2">
                            <div><p class="font-semibold text-blue-900">Attendance Rate</p><p class="text-sky-500 font-semibold">${data.attendance_summary.attendance_rate}%</p></div>
                            <div><p class="font-semibold text-blue-900">Present</p><p class="text-emerald-500">${data.attendance_summary.present} Days</p></div>
                            <div><p class="font-semibold text-blue-900">Absent</p><p class="text-red-500">${data.attendance_summary.absent} Days</p></div>
                        </div>
                        <div class="mt-8">
                            <h2 class="text-lg font-bold text-blue-900 mb-3">Performance Score</h2>
                            <h3 class="text-2xl font-semibold ${statusColor}">${data.status_performance}</h3>
                            <div class="flex items-end gap-2 mt-2">
                                <span class="text-2xl font-bold text-blue-900">${data.performance_score}</span>
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
    </script>
@endsection