<div class="space-y-6">
    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Attendance Rate Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-blue-100 text-sm">Attendance Rate</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($attendanceRate, 1) }}%</p>
                </div>
                <div class="bg-white/20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex justify-between text-sm">
                <span>All Time</span>
                <span>{{ $presentCount }} Present</span>
            </div>
        </div>

        <!-- Present Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-green-100 text-sm">Present</p>
                    <p class="text-3xl font-bold mt-1">{{ $presentCount }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Late Card -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-yellow-100 text-sm">Late</p>
                    <p class="text-3xl font-bold mt-1">{{ $lateCount }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Absent Card -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-red-100 text-sm">Absent</p>
                    <p class="text-3xl font-bold mt-1">{{ $absentCount }}</p>
                </div>
                <div class="bg-white/20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Recent Attendance</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentAttendances as $attendance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($attendance->tanggal)->format('d F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $attendance->jam_masuk ? \Carbon\Carbon::parse($attendance->jam_masuk)->format('h.i A') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $attendance->jam_pulang ? \Carbon\Carbon::parse($attendance->jam_pulang)->format('h.i A') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = match($attendance->status_kehadiran) {
                                        'hadir', 'masuk' => 'bg-green-100 text-green-800',
                                        'izin' => 'bg-blue-100 text-blue-800',
                                        'sakit' => 'bg-purple-100 text-purple-800',
                                        'alpha' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                    $statusText = match($attendance->status_kehadiran) {
                                        'hadir', 'masuk' => 'On Time',
                                        'izin' => 'Permit',
                                        'sakit' => 'Sick',
                                        'alpha' => 'Absent',
                                        default => ucfirst($attendance->status_kehadiran),
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No attendance records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Leave Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Annual Leave Card -->
        <div class="bg-white border-l-4 border-blue-500 rounded-lg shadow-md p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Annual Leave</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $annualLeaveUsed }}/{{ $annualLeaveQuota }} days</p>
                </div>
                <div class="bg-blue-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Sick Leave Card -->
        <div class="bg-white border-l-4 border-green-500 rounded-lg shadow-md p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Sick Leave</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $sickLeaveUsed }}/{{ $sickLeaveQuota }} days</p>
                </div>
                <div class="bg-green-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Emergency Leave Card -->
        <div class="bg-white border-l-4 border-yellow-500 rounded-lg shadow-md p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Emergency Leave</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $emergencyLeaveUsed }}/{{ $emergencyLeaveQuota }} days</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Other Leave Card -->
        <div class="bg-white border-l-4 border-purple-500 rounded-lg shadow-md p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Other Leave</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $otherLeaveUsed }}/{{ $otherLeaveQuota }} days</p>
                </div>
                <div class="bg-purple-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Leave Requests</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($leaveRequests as $leave)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($leave->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($leave->tanggal_selesai)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @php
                                    $typeLabels = [
                                        'tahunan' => 'Annual Leave',
                                        'sakit' => 'Sick Leave',
                                        'penting' => 'Emergency Leave',
                                        'lainnya' => 'Other Leave',
                                    ];
                                @endphp
                                {{ $typeLabels[$leave->jenis_cuti] ?? ucfirst($leave->jenis_cuti) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $leave->total_hari }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = match($leave->status) {
                                        'disetujui', 'approved' => 'bg-green-100 text-green-800',
                                        'ditolak', 'rejected' => 'bg-red-100 text-red-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                    $statusText = match($leave->status) {
                                        'disetujui', 'approved' => 'Approved',
                                        'ditolak', 'rejected' => 'Rejected',
                                        'pending' => 'Requested',
                                        default => ucfirst($leave->status),
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No leave requests found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
