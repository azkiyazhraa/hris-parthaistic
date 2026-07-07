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
        <!-- <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
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
        </div> -->

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
                                {{ $attendance->tanggal ? \Carbon\Carbon::parse($attendance->tanggal)->format('d F Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $attendance->jam_masuk ? \Carbon\Carbon::parse($attendance->jam_masuk)->format('H.i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $attendance->jam_pulang ? \Carbon\Carbon::parse($attendance->jam_pulang)->format('H.i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = match($attendance->status_kehadiran) {
                                        'present'    => 'bg-green-100 text-green-800',
                                        'change_day' => 'bg-blue-100 text-blue-800',
                                        'leave'      => 'bg-purple-100 text-purple-800',
                                        'absent'     => 'bg-red-100 text-red-800',
                                        default      => 'bg-gray-100 text-gray-800',
                                    };
                                    $statusText = match($attendance->status_kehadiran) {
                                        'present'    => 'Present',
                                        'change_day' => 'Change Day',
                                        'leave'      => 'Leave',
                                        'absent'     => 'Absent',
                                        default      => ucfirst($attendance->status_kehadiran ?? 'pending'),
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
                    <p class="text-xs text-gray-400 mt-1">{{ max(0, $annualLeaveQuota - $annualLeaveUsed) }} days remaining</p>
                </div>
                <div class="bg-blue-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Maternity/Paternity Leave Card -->
        <div class="bg-white border-l-4 border-pink-500 rounded-lg shadow-md p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">{{ $maternityLeaveLabel }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $maternityLeaveUsed }}/{{ $maternityLeaveQuota }} days</p>
                    <p class="text-xs text-gray-400 mt-1">{{ max(0, $maternityLeaveQuota - $maternityLeaveUsed) }} days remaining</p>
                </div>
                <div class="bg-pink-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Marriage Leave Card -->
        <div class="bg-white border-l-4 border-yellow-500 rounded-lg shadow-md p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Marriage Leave</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $marriageLeaveUsed }}/{{ $marriageLeaveQuota }} days</p>
                    <p class="text-xs text-gray-400 mt-1">{{ max(0, $marriageLeaveQuota - $marriageLeaveUsed) }} days remaining</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Bereavement Leave Card -->
        <div class="bg-white border-l-4 border-purple-500 rounded-lg shadow-md p-4">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm">Bereavement Leave</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $bereavementLeaveUsed }}/{{ $bereavementLeaveQuota }} days</p>
                    <p class="text-xs text-gray-400 mt-1">{{ max(0, $bereavementLeaveQuota - $bereavementLeaveUsed) }} days remaining</p>
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
                                {{ $leave['tanggal_mulai'] }} - {{ $leave['tanggal_selesai'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $leave['jenis_cuti'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $leave['total_hari'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = match($leave['status']) {
                                        'disetujui', 'approved' => 'bg-green-100 text-green-800',
                                        'ditolak', 'rejected' => 'bg-red-100 text-red-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                    $statusText = match($leave['status']) {
                                        'disetujui', 'approved' => 'Approved',
                                        'ditolak', 'rejected' => 'Rejected',
                                        'pending' => 'Requested',
                                        default => ucfirst($leave['status']),
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
