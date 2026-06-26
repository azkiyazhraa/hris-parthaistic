<!-- Employee Detail Modal -->
<div id="default-modal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white border border-default rounded-lg shadow-sm p-4 md:p-6">

            <!-- Modal header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-heading">Employee Detail Admin</h3>
                <button type="button"
                    class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-md text-sm w-9 h-9 inline-flex justify-center items-center"
                    data-modal-hide="default-modal">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>

            <!-- Tabs -->
            <div class="mb-4 border-b border-default">
                <ul class="flex flex-wrap text-sm font-medium text-center w-full" role="tablist">
                    <li class="me-2" role="presentation">
                        <button id="emp-tab-overview"
                            class="inline-block p-4 border-b-2 rounded-t-base text-blue-600 border-blue-600"
                            type="button" role="tab" aria-selected="true"
                            onclick="switchEmpTab('overview')">Overview</button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button id="emp-tab-attendance"
                            class="inline-block p-4 border-b-2 border-transparent rounded-t-base hover:text-blue-600 hover:border-blue-600 text-gray-500"
                            type="button" role="tab" aria-selected="false"
                            onclick="switchEmpTab('attendance')">Attendance &amp; Leave</button>
                    </li>
                    <li class="me-2" role="presentation">
                        <button id="emp-tab-performance"
                            class="inline-block p-4 border-b-2 border-transparent rounded-t-base hover:text-blue-600 hover:border-blue-600 text-gray-500"
                            type="button" role="tab" aria-selected="false"
                            onclick="switchEmpTab('performance')">Performance</button>
                    </li>
                </ul>
            </div>

            <!-- Tab Panels -->
            <div>

                <!-- ── OVERVIEW ─────────────────────────────────────────────── -->
                <div id="emp-panel-overview" role="tabpanel" class="block">
                    <div class="grid md:grid-cols-3 gap-8">
                        <!-- LEFT -->
                        <div class="md:col-span-2 space-y-8">
                            <!-- Personal Info -->
                            <div>
                                <h4 class="text-lg font-semibold text-blue-900 mb-4">Personal Info</h4>
                                <div class="grid md:grid-cols-2 gap-x-10 gap-y-4 text-sm">
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Full Name</p>
                                            <p class="font-medium text-gray-800" id="detail_nama_lengkap">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">NIP</p>
                                            <p class="font-medium text-gray-800" id="detail_nip">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Address</p>
                                            <p class="font-medium text-gray-800" id="detail_alamat">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Place of Birth</p>
                                            <p class="font-medium text-gray-800" id="detail_tempat_lahir">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Date of Birth</p>
                                            <p class="font-medium text-gray-800" id="detail_tanggal_lahir">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Gender</p>
                                            <p class="font-medium text-gray-800" id="detail_jenis_kelamin">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Religion</p>
                                            <p class="font-medium text-gray-800" id="detail_agama">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Marital Status</p>
                                            <p class="font-medium text-gray-800" id="detail_status_pernikahan">-</p>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Email</p>
                                            <p class="font-medium text-gray-800" id="detail_email">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Phone Number</p>
                                            <p class="font-medium text-gray-800" id="detail_nomor_telepon">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">NIK</p>
                                            <p class="font-medium text-gray-800" id="detail_nik">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">NPWP</p>
                                            <p class="font-medium text-gray-800" id="detail_npwp">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Bank Name</p>
                                            <p class="font-medium text-gray-800" id="detail_nama_bank">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Account Number</p>
                                            <p class="font-medium text-gray-800" id="detail_nomor_rekening">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Emergency Contact</p>
                                            <p class="font-medium text-gray-800" id="detail_nama_kontak_darurat">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-xs uppercase tracking-wider">Emergency Phone</p>
                                            <p class="font-medium text-gray-800" id="detail_telepon_kontak_darurat">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Employment Info -->
                            <div>
                                <h4 class="text-lg font-semibold text-blue-900 mb-4">Employment Info</h4>
                                <div class="grid md:grid-cols-2 gap-4 text-sm">
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-400 text-xs uppercase tracking-wider block">Role</span>
                                        <span class="font-medium text-gray-800" id="detail_role">-</span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-400 text-xs uppercase tracking-wider block">Position</span>
                                        <span class="font-medium text-gray-800" id="detail_jabatan">-</span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-400 text-xs uppercase tracking-wider block">Join Date</span>
                                        <span class="font-medium text-gray-800" id="detail_tanggal_bergabung">-</span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-400 text-xs uppercase tracking-wider block">Status</span>
                                        <span class="font-medium" id="detail_status">-</span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-400 text-xs uppercase tracking-wider block">End Date</span>
                                        <span class="font-medium text-gray-800" id="detail_end_date">-</span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-400 text-xs uppercase tracking-wider block">Total Working Days</span>
                                        <span class="font-medium text-gray-800" id="detail_total_hari_kerja">-</span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg col-span-2">
                                        <span class="text-gray-400 text-xs uppercase tracking-wider block">Last Education</span>
                                        <span class="font-medium text-gray-800" id="detail_pendidikan_terakhir">-</span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-400 text-xs uppercase tracking-wider block">University</span>
                                        <span class="font-medium text-gray-800" id="detail_universitas">-</span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-400 text-xs uppercase tracking-wider block">Major</span>
                                        <span class="font-medium text-gray-800" id="detail_jurusan">-</span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-400 text-xs uppercase tracking-wider block">Graduation Year</span>
                                        <span class="font-medium text-gray-800" id="detail_tahun_lulus">-</span>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg">
                                        <span class="text-gray-400 text-xs uppercase tracking-wider block">Reason Resigned</span>
                                        <span class="font-medium text-gray-800" id="detail_reason_resigned">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Avatar -->
                        <div class="flex flex-col justify-start items-center gap-4">
                            <div class="w-40 h-40 md:w-52 md:h-52 rounded-full border-[6px] border-blue-900 overflow-hidden bg-gray-100">
                                <img id="detail_foto_profil" src="" alt="Profile Photo" class="w-full h-full object-cover">
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-400">Profile Photo</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── ATTENDANCE & LEAVE ────────────────────────────────────── -->
                <div id="emp-panel-attendance" role="tabpanel" class="hidden">
                    <!-- Loading spinner -->
                    <div id="emp-attendance-loading" class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <p class="text-gray-500 mt-2 text-sm">Loading...</p>
                    </div>

                    <!-- Content -->
                    <div id="emp-attendance-content" class="hidden space-y-5">

                        <!-- Stat Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white shadow-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-blue-100 text-xs">Attendance Rate</p>
                                        <p class="text-2xl font-bold mt-1" id="emp-attendance-rate">-</p>
                                    </div>
                                    <div class="bg-white/20 rounded-full p-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs mt-2 text-blue-100">All Time</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white shadow-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-green-100 text-xs">Present</p>
                                        <p class="text-2xl font-bold mt-1" id="emp-present-count">-</p>
                                    </div>
                                    <div class="bg-white/20 rounded-full p-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs mt-2 text-green-100">This Month</p>
                            </div>
                            <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white shadow-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-yellow-100 text-xs">Late</p>
                                        <p class="text-2xl font-bold mt-1" id="emp-late-count">-</p>
                                    </div>
                                    <div class="bg-white/20 rounded-full p-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs mt-2 text-yellow-100">This Month</p>
                            </div>
                            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-red-100 text-xs">Absent</p>
                                        <p class="text-2xl font-bold mt-1" id="emp-absent-count">-</p>
                                    </div>
                                    <div class="bg-white/20 rounded-full p-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs mt-2 text-red-100">This Month</p>
                            </div>
                        </div>

                        <!-- Recent Attendance Table -->
                        <div class="bg-white rounded-xl shadow-md overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-800">Recent Attendance</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check In</th>
                                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check Out</th>
                                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="emp-attendance-tbody" class="bg-white divide-y divide-gray-200"></tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Leave Summary Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="bg-white border-l-4 border-blue-500 rounded-lg shadow-md p-4">
                                <p class="text-gray-500 text-xs">Annual Leave</p>
                                <p class="text-xl font-bold text-gray-800 mt-1">
                                    <span id="emp-annual-used">0</span>/<span id="emp-annual-quota">12</span>
                                    <span class="text-sm font-normal"> days</span>
                                </p>
                            </div>
                            <div class="bg-white border-l-4 border-green-500 rounded-lg shadow-md p-4">
                                <p class="text-gray-500 text-xs">Sick Leave</p>
                                <p class="text-xl font-bold text-gray-800 mt-1">
                                    <span id="emp-sick-used">0</span>/<span id="emp-sick-quota">12</span>
                                    <span class="text-sm font-normal"> days</span>
                                </p>
                            </div>
                            <div class="bg-white border-l-4 border-yellow-500 rounded-lg shadow-md p-4">
                                <p class="text-gray-500 text-xs">Emergency Leave</p>
                                <p class="text-xl font-bold text-gray-800 mt-1">
                                    <span id="emp-emergency-used">0</span>/<span id="emp-emergency-quota">12</span>
                                    <span class="text-sm font-normal"> days</span>
                                </p>
                            </div>
                            <div class="bg-white border-l-4 border-purple-500 rounded-lg shadow-md p-4">
                                <p class="text-gray-500 text-xs">Other Leave</p>
                                <p class="text-xl font-bold text-gray-800 mt-1">
                                    <span id="emp-other-used">0</span>/<span id="emp-other-quota">12</span>
                                    <span class="text-sm font-normal"> days</span>
                                </p>
                            </div>
                        </div>

                        <!-- Leave Requests Table -->
                        <div class="bg-white rounded-xl shadow-md overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-800">Leave Requests</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date Range</th>
                                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days</th>
                                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="emp-leave-tbody" class="bg-white divide-y divide-gray-200"></tbody>
                                </table>
                            </div>
                        </div>

                    </div><!-- /emp-attendance-content -->
                </div><!-- /emp-panel-attendance -->

                <!-- ── PERFORMANCE ────────────────────────────────────────────── -->
                <div id="emp-panel-performance" role="tabpanel" class="hidden">
                    <!-- Loading spinner -->
                    <div id="emp-performance-loading" class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                        <p class="text-gray-500 mt-2 text-sm">Loading...</p>
                    </div>

                    <!-- Content -->
                    <div id="emp-performance-content" class="hidden space-y-5">

                        <!-- Score Banner -->
                        <div class="bg-gradient-to-r from-purple-500 to-purple-700 rounded-xl p-5 text-white shadow-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-purple-200 text-xs">Latest Performance Score</p>
                                    <p class="text-4xl font-bold mt-1" id="emp-perf-score">0</p>
                                    <div class="flex items-center mt-2 gap-1">
                                        <span class="font-semibold text-sm" id="emp-perf-change">+0</span>
                                        <span class="text-purple-200 text-xs">from last month</span>
                                    </div>
                                </div>
                                <div class="bg-white/20 rounded-lg px-4 py-2 text-right">
                                    <p class="text-xs text-purple-200">Rating</p>
                                    <p class="text-base font-bold" id="emp-perf-rating">No Data</p>
                                </div>
                            </div>
                        </div>

                        <!-- KPI Breakdown -->
                        <div class="bg-white rounded-xl shadow-md p-5">
                            <h3 class="text-sm font-semibold text-gray-800 mb-4">Latest KPI Score</h3>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-600 text-sm w-24 shrink-0">Quality</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" id="emp-quality-bar" style="width:0%"></div>
                                    </div>
                                    <span class="font-semibold text-sm w-8 text-right" id="emp-quality">0</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-600 text-sm w-24 shrink-0">Productivity</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full transition-all duration-500" id="emp-productivity-bar" style="width:0%"></div>
                                    </div>
                                    <span class="font-semibold text-sm w-8 text-right" id="emp-productivity">0</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-600 text-sm w-24 shrink-0">Teamwork</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-500 h-2 rounded-full transition-all duration-500" id="emp-teamwork-bar" style="width:0%"></div>
                                    </div>
                                    <span class="font-semibold text-sm w-8 text-right" id="emp-teamwork">0</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-gray-600 text-sm w-24 shrink-0">Discipline</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-500 h-2 rounded-full transition-all duration-500" id="emp-discipline-bar" style="width:0%"></div>
                                    </div>
                                    <span class="font-semibold text-sm w-8 text-right" id="emp-discipline">0</span>
                                </div>
                                <div class="border-t pt-3">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-gray-800 text-sm">KPI Score</span>
                                        <span class="font-bold text-lg text-blue-600" id="emp-kpi-score">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Chart -->
                        <div class="bg-white rounded-xl shadow-md p-5">
                            <h3 class="text-sm font-semibold text-gray-800 mb-4">Performance Score History</h3>
                            <div id="emp-perf-chart" style="min-height:220px"></div>
                        </div>

                    </div><!-- /emp-performance-content -->
                </div><!-- /emp-panel-performance -->

            </div><!-- /tab panels -->
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ── Pre-load karyawan data (overview only) ──────────────────────────────
    let karyawanData = {};
    @foreach ($karyawans as $karyawan)
        karyawanData[{{ $karyawan->id }}] = {
            id: {{ $karyawan->id }},
            nip: "{{ $karyawan->nip ?? '-' }}",
            nama_lengkap: "{{ addslashes($karyawan->nama_lengkap ?? '-') }}",
            email: "{{ $karyawan->email ?? '-' }}",
            role: "{{ $karyawan->role ?? '-' }}",
            jabatan: "{{ addslashes($karyawan->jabatan_display ?? $karyawan->jabatan ?? '-') }}",
            status: "{{ $karyawan->status ?? 'Active' }}",
            status_class: "{{ $karyawan->status_class ?? 'bg-gray-100 text-gray-800' }}",
            status_badge: "{{ $karyawan->status_badge ?? $karyawan->status ?? '-' }}",
            nomor_telepon: "{{ $karyawan->nomor_telepon ?? '-' }}",
            alamat: "{{ addslashes($karyawan->alamat ?? '-') }}",
            nik: "{{ $karyawan->nik ?? '-' }}",
            npwp: "{{ $karyawan->npwp ?? '-' }}",
            tempat_lahir: "{{ $karyawan->tempat_lahir ?? '-' }}",
            tanggal_lahir: "{{ $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('d F Y') : '-' }}",
            jenis_kelamin: "{{ $karyawan->jenis_kelamin == 'L' ? 'Male' : ($karyawan->jenis_kelamin == 'P' ? 'Female' : '-') }}",
            agama: "{{ $karyawan->agama ?? '-' }}",
            status_pernikahan: "{{ $karyawan->status_pernikahan ?? '-' }}",
            pendidikan_terakhir: "{{ $karyawan->pendidikan_terakhir ?? '-' }}",
            universitas: "{{ $karyawan->universitas ?? '-' }}",
            jurusan: "{{ $karyawan->jurusan ?? '-' }}",
            tahun_lulus: "{{ $karyawan->tahun_lulus ?? '-' }}",
            nama_kontak_darurat: "{{ $karyawan->nama_kontak_darurat ?? '-' }}",
            telepon_kontak_darurat: "{{ $karyawan->telepon_kontak_darurat ?? '-' }}",
            tanggal_bergabung: "{{ $karyawan->tanggal_bergabung ? $karyawan->tanggal_bergabung->format('d F Y') : '-' }}",
            end_date: "{{ $karyawan->end_date ? $karyawan->end_date->format('d F Y') : '-' }}",
            total_hari_kerja: "{{ $karyawan->total_hari_kerja > 0 ? number_format($karyawan->total_hari_kerja) . ' days' : '-' }}",
            reason_resigned: "{{ $karyawan->reason_resigned ?? '-' }}",
            nama_bank: "{{ $karyawan->nama_bank ?? '-' }}",
            nomor_rekening: "{{ $karyawan->nomor_rekening ?? '-' }}",
            foto_profil: "{{ $karyawan->foto_profil && Storage::disk('public')->exists($karyawan->foto_profil) ? Storage::url($karyawan->foto_profil) : '' }}"
        };
    @endforeach

    // ── State ────────────────────────────────────────────────────────────────
    let currentEmployeeId  = null;
    let empDetailCache     = {};
    let empPerfChart       = null;

    // ── Tab switching ────────────────────────────────────────────────────────
    const EMP_TABS = {
        overview:    { tab: 'emp-tab-overview',    panel: 'emp-panel-overview' },
        attendance:  { tab: 'emp-tab-attendance',  panel: 'emp-panel-attendance' },
        performance: { tab: 'emp-tab-performance', panel: 'emp-panel-performance' },
    };

    function switchEmpTab(name) {
        Object.entries(EMP_TABS).forEach(([key, { tab, panel }]) => {
            const btn   = document.getElementById(tab);
            const pane  = document.getElementById(panel);
            const active = key === name;

            pane.classList.toggle('hidden', !active);
            pane.classList.toggle('block', active);
            btn.classList.toggle('text-blue-600', active);
            btn.classList.toggle('border-blue-600', active);
            btn.classList.toggle('text-gray-500', !active);
            btn.classList.toggle('border-transparent', !active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        if (name === 'attendance') loadEmpAttendance();
        if (name === 'performance') loadEmpPerformance();
    }

    // ── Open modal ───────────────────────────────────────────────────────────
    function showEmployeeDetail(id) {
        currentEmployeeId = id;
        const data = karyawanData[id];
        if (!data) {
            console.error('Employee data not found for ID:', id);
            return;
        }

        // Populate overview
        document.getElementById('detail_nama_lengkap').innerText         = data.nama_lengkap;
        document.getElementById('detail_nip').innerText                  = data.nip;
        document.getElementById('detail_alamat').innerText               = data.alamat;
        document.getElementById('detail_tempat_lahir').innerText         = data.tempat_lahir;
        document.getElementById('detail_tanggal_lahir').innerText        = data.tanggal_lahir;
        document.getElementById('detail_jenis_kelamin').innerText        = data.jenis_kelamin;
        document.getElementById('detail_agama').innerText                = data.agama;
        document.getElementById('detail_status_pernikahan').innerText    = data.status_pernikahan;
        document.getElementById('detail_email').innerText                = data.email;
        document.getElementById('detail_nomor_telepon').innerText        = data.nomor_telepon;
        document.getElementById('detail_nik').innerText                  = data.nik;
        document.getElementById('detail_npwp').innerText                 = data.npwp;
        document.getElementById('detail_nama_bank').innerText            = data.nama_bank;
        document.getElementById('detail_nomor_rekening').innerText       = data.nomor_rekening;
        document.getElementById('detail_nama_kontak_darurat').innerText  = data.nama_kontak_darurat;
        document.getElementById('detail_telepon_kontak_darurat').innerText = data.telepon_kontak_darurat;
        document.getElementById('detail_role').innerText                 = data.role.toUpperCase();
        document.getElementById('detail_jabatan').innerText              = data.jabatan;
        document.getElementById('detail_tanggal_bergabung').innerText    = data.tanggal_bergabung;
        document.getElementById('detail_end_date').innerText             = data.end_date;
        document.getElementById('detail_total_hari_kerja').innerText     = data.total_hari_kerja;
        document.getElementById('detail_reason_resigned').innerText      = data.reason_resigned;
        document.getElementById('detail_pendidikan_terakhir').innerText  = data.pendidikan_terakhir;
        document.getElementById('detail_universitas').innerText          = data.universitas;
        document.getElementById('detail_jurusan').innerText              = data.jurusan;
        document.getElementById('detail_tahun_lulus').innerText          = data.tahun_lulus;

        // Status with badge
        const statusEl = document.getElementById('detail_status');
        statusEl.innerHTML = `<span class="px-3 py-1 text-xs rounded-full ${data.status_class}">${data.status_badge}</span>`;

        // Foto profil
        const foto = document.getElementById('detail_foto_profil');
        foto.src = data.foto_profil
            ? data.foto_profil
            : `https://ui-avatars.com/api/?background=0D8F81&color=fff&size=200&name=${encodeURIComponent(data.nama_lengkap)}`;
        foto.onerror = function() {
            this.src = `https://ui-avatars.com/api/?background=0D8F81&color=fff&size=200&name=${encodeURIComponent(data.nama_lengkap)}`;
        };

        // Reset other tabs to loading state
        document.getElementById('emp-attendance-loading').classList.remove('hidden');
        document.getElementById('emp-attendance-content').classList.add('hidden');
        document.getElementById('emp-performance-loading').classList.remove('hidden');
        document.getElementById('emp-performance-content').classList.add('hidden');

        // Always go back to Overview when opening a new employee
        switchEmpTab('overview');
    }

    // ── AJAX loader ──────────────────────────────────────────────────────────
    function fetchEmpDetail(id, callback) {
        if (empDetailCache[id]) {
            callback(empDetailCache[id]);
            return;
        }
        fetch(`/admin/karyawan/${id}/employee-detail`)
            .then(r => {
                if (!r.ok) throw new Error('Network response was not ok');
                return r.json();
            })
            .then(data => {
                empDetailCache[id] = data;
                callback(data);
            })
            .catch(err => {
                console.error('Failed to load employee detail:', err);
                // Show error in UI
                document.getElementById('emp-attendance-loading').classList.add('hidden');
                document.getElementById('emp-attendance-content').classList.remove('hidden');
                document.getElementById('emp-attendance-tbody').innerHTML =
                    `<tr><td colspan="4" class="px-5 py-8 text-center text-red-400">Failed to load data</td></tr>`;
            });
    }

    function loadEmpAttendance() {
        if (!currentEmployeeId) return;
        if (empDetailCache[currentEmployeeId]) {
            renderAttendanceTab(empDetailCache[currentEmployeeId]);
            return;
        }
        fetchEmpDetail(currentEmployeeId, renderAttendanceTab);
    }

    function loadEmpPerformance() {
        if (!currentEmployeeId) return;
        if (empDetailCache[currentEmployeeId]) {
            renderPerformanceTab(empDetailCache[currentEmployeeId]);
            return;
        }
        fetchEmpDetail(currentEmployeeId, renderPerformanceTab);
    }

    // ── Render: Attendance & Leave ────────────────────────────────────────────
    function renderAttendanceTab(data) {
        const a = data.attendance;
        const l = data.leave;

        document.getElementById('emp-attendance-rate').textContent  = a.rate + '%';
        document.getElementById('emp-present-count').textContent     = a.present;
        document.getElementById('emp-late-count').textContent        = a.late || 0;
        document.getElementById('emp-absent-count').textContent      = a.absent;

        // Recent attendance rows
        const atBody = document.getElementById('emp-attendance-tbody');
        const statusMap = {
            hadir:  ['bg-green-100 text-green-800',   'Present'],
            masuk:  ['bg-green-100 text-green-800',   'Present'],
            present:['bg-green-100 text-green-800',   'Present'],
            izin:   ['bg-blue-100 text-blue-800',     'Permit'],
            permit: ['bg-blue-100 text-blue-800',     'Permit'],
            sakit:  ['bg-purple-100 text-purple-800', 'Sick'],
            sick:   ['bg-purple-100 text-purple-800', 'Sick'],
            alpha:  ['bg-red-100 text-red-800',       'Absent'],
            absent: ['bg-red-100 text-red-800',       'Absent'],
            pending:['bg-yellow-100 text-yellow-800', 'Pending'],
        };
        if (!a.recent || !a.recent.length) {
            atBody.innerHTML = `<tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">No attendance records found</td></tr>`;
        } else {
            atBody.innerHTML = a.recent.map(row => {
                const statusKey = row.status || 'pending';
                const [cls, txt] = statusMap[statusKey.toLowerCase()] ?? ['bg-gray-100 text-gray-800', statusKey];
                return `<tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">${row.tanggal || '-'}</td>
                    <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-600">${row.jam_masuk || '-'}</td>
                    <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-600">${row.jam_pulang || '-'}</td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full ${cls}">${txt}</span>
                    </td>
                </tr>`;
            }).join('');
        }

        // Leave quotas
        document.getElementById('emp-annual-used').textContent    = l.annual_used || 0;
        document.getElementById('emp-annual-quota').textContent   = l.annual_quota || 12;
        document.getElementById('emp-sick-used').textContent      = l.sick_used || 0;
        document.getElementById('emp-sick-quota').textContent     = l.sick_quota || 12;
        document.getElementById('emp-emergency-used').textContent = l.emergency_used || 0;
        document.getElementById('emp-emergency-quota').textContent= l.emergency_quota || 12;
        document.getElementById('emp-other-used').textContent     = l.other_used || 0;
        document.getElementById('emp-other-quota').textContent    = l.other_quota || 12;

        // Leave request rows
        const lvBody = document.getElementById('emp-leave-tbody');
        const leaveStatusMap = {
            disetujui: ['bg-green-100 text-green-800',   'Approved'],
            approved:  ['bg-green-100 text-green-800',   'Approved'],
            ditolak:   ['bg-red-100 text-red-800',       'Rejected'],
            rejected:  ['bg-red-100 text-red-800',       'Rejected'],
            pending:   ['bg-yellow-100 text-yellow-800', 'Requested'],
        };
        if (!l.requests || !l.requests.length) {
            lvBody.innerHTML = `<tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">No leave requests found</td></tr>`;
        } else {
            lvBody.innerHTML = l.requests.map(row => {
                const [cls, txt] = leaveStatusMap[row.status] ?? ['bg-gray-100 text-gray-800', row.status];
                return `<tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-900">${row.tanggal_mulai || '-'} – ${row.tanggal_selesai || '-'}</td>
                    <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-600">${row.jenis_cuti || '-'}</td>
                    <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-600">${row.total_hari || 0}</td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded-full ${cls}">${txt}</span>
                    </td>
                </tr>`;
            }).join('');
        }

        document.getElementById('emp-attendance-loading').classList.add('hidden');
        document.getElementById('emp-attendance-content').classList.remove('hidden');
    }

    // ── Render: Performance ───────────────────────────────────────────────────
    function renderPerformanceTab(data) {
        const p = data.performance;

        document.getElementById('emp-perf-score').textContent  = p.latest_score || 0;
        document.getElementById('emp-perf-rating').textContent = p.rating_label || 'No Data';

        const changeEl = document.getElementById('emp-perf-change');
        const changeVal = p.change || 0;
        changeEl.textContent = (changeVal >= 0 ? '+' : '') + changeVal;
        changeEl.className   = 'font-semibold text-sm ' + (changeVal >= 0 ? 'text-green-300' : 'text-red-300');

        // KPI bars
        ['quality','productivity','teamwork','discipline'].forEach(k => {
            const val = p[k] || 0;
            document.getElementById(`emp-${k}`).textContent = val;
            document.getElementById(`emp-${k}-bar`).style.width = Math.min(val, 100) + '%';
        });
        document.getElementById('emp-kpi-score').textContent = p.kpi_score || 0;

        // ApexChart
        if (empPerfChart) { empPerfChart.destroy(); empPerfChart = null; }

        const chartData = p.history || { months: [], scores: [] };

        empPerfChart = new ApexCharts(document.getElementById('emp-perf-chart'), {
            chart: {
                type: 'area',
                height: 220,
                toolbar: { show: false },
                sparkline: { enabled: false },
            },
            series: [{ name: 'Performance Score', data: chartData.scores || [] }],
            xaxis: {
                categories: chartData.months || [],
                labels: { style: { colors: '#6b7280', fontSize: '11px' } },
            },
            yaxis: {
                min: 0,
                max: 100,
                labels: { style: { colors: '#6b7280', fontSize: '11px' } },
            },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#8B5CF6'],
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.02 },
            },
            markers: { size: 4, colors: ['#8B5CF6'], strokeColors: '#fff', strokeWidth: 2 },
            dataLabels: { enabled: false },
            grid: { borderColor: '#e5e7eb', strokeDashArray: 4 },
            tooltip: { theme: 'light' },
        });
        empPerfChart.render();

        document.getElementById('emp-performance-loading').classList.add('hidden');
        document.getElementById('emp-performance-content').classList.remove('hidden');
    }
</script>
@endpush
