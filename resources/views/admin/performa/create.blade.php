@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">Add Performance Assesment</h1>
                <p class="text-sm text-gray-700/80">Fill in the form to add employee performance assessment</p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        @if (session('error'))
            <div class="px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded-lg">{{ session('error') }}</div>
        @endif

        <div class="p-6 bg-white shadow-lg rounded-2xl" style="border: 2px solid #e0eaff;">
            <div class="p-4 mb-4 border-l-4 border-blue-500 bg-blue-50">
                <p class="text-sm text-blue-700">
                    <strong>Auto Calculation Information :</strong><br>
                    - KPI Score = Average of (Quality + Productivity + Teamwork + Discipline)<br>
                    - Task Score = (Tasks Completed ÷ Monthly Target) × 100<br>
                    - <strong>Performance Score = (KPI Score × 50%) + (Task Score × 50%)</strong><br>
                    - Attendance Rate is recorded for reference but not used in scoring<br>
                    - Rating: ≥ 90 = Excellent (A), 75-89 = Good (B), 60-74 = Fair (C), 50-59 = Poor (D), &lt; 50 = Very Poor (E)
                </p>
                <div class="mt-2 pt-2 border-t border-blue-200">
                    <p class="text-xs text-blue-600 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#0052CC] flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M21 0H3C1.343 0 0 1.343 0 3v18c0 1.656 1.343 3 3 3h18c1.656 0 3-1.344 3-3V3c0-1.657-1.344-3-3-3zM10.44 18.18c0 .795-.645 1.44-1.44 1.44H4.56c-.795 0-1.44-.645-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44H9c.795 0 1.44.645 1.44 1.44v12.36zm10.44-7.08c0 .794-.645 1.44-1.44 1.44H15c-.795 0-1.44-.646-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44h4.44c.795 0 1.44.645 1.44 1.44v5.28z"/>
                        </svg>
                        When Trello is connected, the Sync button will auto-fill Task Done — manual input is always available.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.performa.store') }}" id="performaForm">
                @csrf

                <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Employee *</label>
                        <select name="karyawan_id" id="karyawan_id" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="">Search Employee</option>
                            @foreach ($karyawans as $karyawan)
                                <option value="{{ $karyawan->id }}"
                                    {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->nama_lengkap }} ({{ $karyawan->nip }})
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Month *</label>
                        <select name="bulan" id="bulan" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="">Select Months</option>
                            @foreach ($bulan as $b)
                                @php
                                    $bulanNama = [
                                        1 => 'January',
                                        2 => 'February',
                                        3 => 'March',
                                        4 => 'April',
                                        5 => 'May',
                                        6 => 'June',
                                        7 => 'July',
                                        8 => 'August',
                                        9 => 'September',
                                        10 => 'October',
                                        11 => 'November',
                                        12 => 'December',
                                    ];
                                @endphp
                                <option value="{{ $b }}"
                                    {{ old('bulan', $currentMonth) == $b ? 'selected' : '' }}>{{ $bulanNama[$b] }}</option>
                            @endforeach
                        </select>
                        @error('bulan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Year *</label>
                        <select name="tahun" id="tahun" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="">Select Year</option>
                            @foreach ($tahun as $t)
                                <option value="{{ $t }}"
                                    {{ old('tahun', $currentYear) == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                        @error('tahun')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-6 mb-6 border-t">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Performance Assesment (0-100)</h3>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Quality *</label>
                            <input type="number" id="quality" name="quality" value="{{ old('quality') }}" min="0"
                                max="100" required class="w-full px-3 py-2 border rounded-lg" oninput="clampKPI(this);calculateAll()"
                                onchange="calculateAll()">
                            <div class="w-full h-2 mt-1 bg-gray-200 rounded-full">
                                <div id="quality_bar" class="h-2 bg-green-600 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Productivity *</label>
                            <input type="number" id="productivity" name="productivity" value="{{ old('productivity') }}"
                                min="0" max="100" required class="w-full px-3 py-2 border rounded-lg"
                                oninput="clampKPI(this);calculateAll()" onchange="calculateAll()">
                            <div class="w-full h-2 mt-1 bg-gray-200 rounded-full">
                                <div id="productivity_bar" class="h-2 bg-yellow-600 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Teamwork *</label>
                            <input type="number" id="teamwork" name="teamwork" value="{{ old('teamwork') }}"
                                min="0" max="100" required class="w-full px-3 py-2 border rounded-lg"
                                oninput="clampKPI(this);calculateAll()" onchange="calculateAll()">
                            <div class="w-full h-2 mt-1 bg-gray-200 rounded-full">
                                <div id="teamwork_bar" class="h-2 bg-purple-600 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Discipline *</label>
                            <input type="number" id="discipline" name="discipline" value="{{ old('discipline') }}"
                                min="0" max="100" required class="w-full px-3 py-2 border rounded-lg"
                                oninput="clampKPI(this);calculateAll()" onchange="calculateAll()">
                            <div class="w-full h-2 mt-1 bg-gray-200 rounded-full">
                                <div id="discipline_bar" class="h-2 bg-indigo-600 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TASK COMPLETION SECTION --}}
                <div class="pt-6 mb-6 border-t">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Task Completion</h3>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 bg-[#0052CC] rounded flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 0H3C1.343 0 0 1.343 0 3v18c0 1.656 1.343 3 3 3h18c1.656 0 3-1.344 3-3V3c0-1.657-1.344-3-3-3zM10.44 18.18c0 .795-.645 1.44-1.44 1.44H4.56c-.795 0-1.44-.645-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44H9c.795 0 1.44.645 1.44 1.44v12.36zm10.44-7.08c0 .794-.645 1.44-1.44 1.44H15c-.795 0-1.44-.646-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44h4.44c.795 0 1.44.645 1.44 1.44v5.28z"/>
                                </svg>
                            </div>
                            @if($trackerConfigured)
                                <span class="text-xs text-green-700 bg-green-100 px-2 py-0.5 rounded-full font-medium">Tracker — Connected</span>
                            @else
                                <span class="text-xs text-yellow-700 bg-yellow-100 px-2 py-0.5 rounded-full font-medium">Tracker — Not Connected</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-4">
                        <input type="hidden" name="task_source" id="task_source" value="manual">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-700">
                                    Tasks Completed
                                    <span id="trelloBadge" class="hidden ml-1 text-[10px] bg-[#0052CC] text-white px-1.5 py-0.5 rounded font-medium">Trello</span>
                                </label>
                                <input type="number" id="task_done" name="task_done" value="0" min="0"
                                    class="w-full px-3 py-2 border rounded-lg bg-white"
                                    onchange="calculateAll()" onkeyup="calculateAll()">
                                <p class="text-xs text-gray-400 mt-1" id="taskDoneHint">Enter manually, or use Sync button to auto-fill from Tracker</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-700">Monthly Target <span class="text-red-500">*</span></label>
                                <input type="number" id="task_target" name="task_target" value="{{ old('task_target') }}" min="1" required
                                    class="w-full px-3 py-2 border rounded-lg bg-white"
                                    onchange="calculateAll()" onkeyup="calculateAll()">
                                <p class="text-xs text-gray-400 mt-1">Set task target for this employee</p>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-700">Task Score <span class="font-normal text-gray-400">(auto)</span></label>
                                <div class="p-3 bg-white rounded-lg border border-gray-200">
                                    <div class="text-3xl font-bold text-teal-600" id="task_score_display">0</div>
                                    <input type="hidden" id="task_score" name="task_score" value="0">
                                    <div class="w-full h-2 mt-2 bg-gray-200 rounded-full">
                                        <div id="task_bar" class="h-2 bg-teal-500 rounded-full transition-all" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-col sm:flex-row items-start sm:items-center gap-3 pt-3 border-t border-gray-200">
                            @if($trackerConfigured)
                                <button type="button" id="syncSingleBtn" onclick="syncSingleFromTracker()"
                                    class="flex items-center gap-2 px-3 py-2 bg-[#0052CC] hover:bg-[#0041a3] text-white rounded-lg text-xs font-medium transition border border-transparent">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Sync from Tracker
                                </button>
                                <span id="syncSingleStatus" class="text-xs text-gray-400">Pilih karyawan terlebih dahulu, lalu klik Sync.</span>
                            @else
                                <button type="button" disabled title="Konfigurasi TRACKER_API_EMAIL & TRACKER_API_PASSWORD di .env terlebih dahulu"
                                    class="flex items-center gap-2 px-3 py-2 bg-white text-gray-400 rounded-lg text-xs font-medium cursor-not-allowed border border-gray-200">
                                    <svg class="w-4 h-4 text-[#0052CC] opacity-40" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M21 0H3C1.343 0 0 1.343 0 3v18c0 1.656 1.343 3 3 3h18c1.656 0 3-1.344 3-3V3c0-1.657-1.344-3-3-3zM10.44 18.18c0 .795-.645 1.44-1.44 1.44H4.56c-.795 0-1.44-.645-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44H9c.795 0 1.44.645 1.44 1.44v12.36zm10.44-7.08c0 .794-.645 1.44-1.44 1.44H15c-.795 0-1.44-.646-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44h4.44c.795 0 1.44.645 1.44 1.44v5.28z"/>
                                    </svg>
                                    Sync from Tracker
                                </button>
                                <p class="text-xs text-gray-400">Tambahkan TRACKER_API_EMAIL & TRACKER_API_PASSWORD di .env untuk mengaktifkan sync.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-6 mb-6 border-t">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Auto calculation Result</h3>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="p-4 rounded-lg bg-blue-50">
                            <label class="block mb-2 text-sm font-bold text-gray-700">KPI Score (Automatic)</label>
                            <div class="text-3xl font-bold text-blue-600" id="kpi_score_display">0</div>
                            <input type="hidden" id="kpi_score" name="kpi_score" value="0">
                            <div class="w-full h-2 mt-2 bg-gray-200 rounded-full">
                                <div id="kpi_bar" class="h-2 bg-blue-600 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        <div class="p-4 rounded-lg bg-green-50">
                            <label class="block mb-2 text-sm font-bold text-gray-700">Attendance Rate (From Attendance
                                Data)</label>
                            <div class="flex items-center gap-2">
                                <div class="text-3xl font-bold text-green-600" id="attendance_rate_display">0</div>
                                <span id="attendance_loading"
                                    class="hidden text-xs text-gray-400 animate-pulse">Loading…</span>
                            </div>
                            <input type="hidden" id="attendance_rate" name="attendance_rate" value="0">
                            <div class="w-full h-2 mt-2 bg-gray-200 rounded-full">
                                <div id="attendance_bar" class="h-2 bg-green-600 rounded-full" style="width: 0%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-400">Select employee, month & year to fetch</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 mb-6 border-t">
                    <div class="p-4 rounded-lg bg-purple-50">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-700">Total Performance Score</label>
                                <div class="text-3xl font-bold text-purple-600" id="performance_score_display">0</div>
                                <input type="hidden" id="performance_score" name="performance_score">
                            </div>
                            <div>
                                <label class="block mb-2 text-sm font-bold text-gray-700">Rating</label>
                                <div id="rating_display" class="text-lg font-semibold">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 mb-6 border-t">
                    <label class="block mb-2 text-sm font-bold text-gray-700">Notes (Optional)</label>
                    <textarea name="catatan" rows="3" class="w-full px-3 py-2 border rounded-lg">{{ old('catatan') }}</textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.performa.index') }}"
                        class="px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">Cancel</a>
                    <button type="submit"
                        class="px-4 py-2 text-white transition bg-green-600 rounded-lg hover:bg-green-700">Save
                        Assesment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let realAttendanceRate = 0;

        function clampKPI(el) {
            const v = parseInt(el.value);
            if (!isNaN(v)) el.value = Math.min(100, Math.max(0, v));
        }

        function calculateAll() {
            ['quality', 'productivity', 'teamwork', 'discipline'].forEach(id => clampKPI(document.getElementById(id)));

            const quality = parseInt(document.getElementById('quality').value) || 0;
            const productivity = parseInt(document.getElementById('productivity').value) || 0;
            const teamwork = parseInt(document.getElementById('teamwork').value) || 0;
            const discipline = parseInt(document.getElementById('discipline').value) || 0;

            document.getElementById('quality_bar').style.width = quality + '%';
            document.getElementById('productivity_bar').style.width = productivity + '%';
            document.getElementById('teamwork_bar').style.width = teamwork + '%';
            document.getElementById('discipline_bar').style.width = discipline + '%';

            // KPI Score = average of 4 components
            const kpiScore = Math.round((quality + productivity + teamwork + discipline) / 4);
            document.getElementById('kpi_score').value = kpiScore;
            document.getElementById('kpi_score_display').innerText = kpiScore;
            document.getElementById('kpi_bar').style.width = kpiScore + '%';

            // Attendance Rate = from real data (fetched from backend)
            document.getElementById('attendance_rate').value = realAttendanceRate;
            document.getElementById('attendance_rate_display').innerText = realAttendanceRate;
            document.getElementById('attendance_bar').style.width = realAttendanceRate + '%';

            // Task Score (preview only — not included in performance formula yet)
            const taskDone = parseInt(document.getElementById('task_done').value) || 0;
            const taskTarget = parseInt(document.getElementById('task_target').value) || 20;
            const taskScore = Math.min(100, Math.round((taskDone / taskTarget) * 100));
            document.getElementById('task_score').value = taskScore;
            document.getElementById('task_score_display').innerText = taskScore;
            document.getElementById('task_bar').style.width = taskScore + '%';

            // Performance Score = (KPI Score × 50%) + (Task Score × 50%)
            const total = Math.round((kpiScore * 0.5) + (taskScore * 0.5));

            document.getElementById('performance_score').value = total;
            document.getElementById('performance_score_display').innerText = total;

            let rating = '',
                ratingColor = '';
            if (total >= 90) {
                rating = 'Excellent (A)';
                ratingColor = 'green';
            } else if (total >= 75) {
                rating = 'Good (B)';
                ratingColor = 'blue';
            } else if (total >= 60) {
                rating = 'Fair (C)';
                ratingColor = 'yellow';
            } else if (total >= 50) {
                rating = 'Poor (D)';
                ratingColor = 'orange';
            } else {
                rating = 'Very Poor (E)';
                ratingColor = 'red';
            }

            document.getElementById('rating_display').innerHTML =
                '<span class="bg-' + ratingColor + '-100 text-' + ratingColor + '-800 py-1 px-3 rounded-full text-xs">' +
                rating + '</span>';
        }

        async function fetchAttendanceRate() {
            const karyawanId = document.getElementById('karyawan_id').value;
            const bulan = document.getElementById('bulan').value;
            const tahun = document.getElementById('tahun').value;

            if (!karyawanId || !bulan || !tahun) {
                realAttendanceRate = 0;
                calculateAll();
                return;
            }

            document.getElementById('attendance_loading').classList.remove('hidden');
            document.getElementById('attendance_rate_display').innerText = '—';

            try {
                const resp = await fetch(
                    `{{ route('admin.performa.attendance-rate') }}?karyawan_id=${karyawanId}&bulan=${bulan}&tahun=${tahun}`
                );
                const data = await resp.json();
                realAttendanceRate = data.rate ?? 0;
            } catch (e) {
                realAttendanceRate = 0;
            }

            document.getElementById('attendance_loading').classList.add('hidden');
            calculateAll();
        }

        document.getElementById('karyawan_id').addEventListener('change', function () {
            fetchAttendanceRate();
            // reset sync state saat ganti karyawan
            resetSyncState();
        });
        document.getElementById('bulan').addEventListener('change', fetchAttendanceRate);
        document.getElementById('tahun').addEventListener('change', fetchAttendanceRate);

        @if($trackerConfigured)
        function resetSyncState() {
            document.getElementById('task_source').value = 'manual';
            document.getElementById('trelloBadge').classList.add('hidden');
            document.getElementById('taskDoneHint').textContent = 'Enter manually, or use Sync button to auto-fill from Tracker';
            const btn = document.getElementById('syncSingleBtn');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Sync from Tracker`;
                btn.className = 'flex items-center gap-2 px-3 py-2 bg-[#0052CC] hover:bg-[#0041a3] text-white rounded-lg text-xs font-medium transition border border-transparent';
            }
            document.getElementById('syncSingleStatus').textContent = 'Pilih karyawan terlebih dahulu, lalu klik Sync.';
            document.getElementById('syncSingleStatus').className = 'text-xs text-gray-400';
        }

        async function syncSingleFromTracker() {
            const karyawanId = document.getElementById('karyawan_id').value;
            if (!karyawanId) {
                document.getElementById('syncSingleStatus').textContent = 'Pilih karyawan terlebih dahulu.';
                document.getElementById('syncSingleStatus').className = 'text-xs text-red-500';
                return;
            }

            const btn = document.getElementById('syncSingleBtn');
            const status = document.getElementById('syncSingleStatus');
            btn.disabled = true;
            btn.innerHTML = `<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Syncing...`;
            status.textContent = 'Menghubungi Dashboard Tracker...';
            status.className = 'text-xs text-gray-400';

            const bulan = document.getElementById('bulan').value;
                const tahun = document.getElementById('tahun').value;
                if (!bulan || !tahun) {
                    status.textContent = 'Pilih bulan dan tahun terlebih dahulu.';
                    status.className = 'text-xs text-red-500';
                    btn.disabled = false;
                    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Sync from Tracker`;
                    return;
                }

            try {
                const resp = await fetch(`{{ url('admin/performa/sync-tracker') }}/${karyawanId}?bulan=${bulan}&tahun=${tahun}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await resp.json();

                if (!data.success) {
                    status.textContent = data.message;
                    status.className = 'text-xs text-red-500';
                    btn.disabled = false;
                    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Retry`;
                    return;
                }

                document.getElementById('task_done').value = data.task_done;
                document.getElementById('task_source').value = 'trello';
                document.getElementById('trelloBadge').classList.remove('hidden');
                document.getElementById('taskDoneHint').textContent = `Diisi otomatis dari Tracker. Kamu bisa ubah manual.`;
                calculateAll();

                status.textContent = `Sync berhasil — ${data.task_done} task selesai dari Tracker.`;
                status.className = 'text-xs text-green-600 font-medium';
                btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Synced`;
                btn.className = 'flex items-center gap-2 px-3 py-2 bg-green-600 text-white rounded-lg text-xs font-medium border border-transparent';
                btn.disabled = false;
            } catch (e) {
                status.textContent = 'Koneksi ke Tracker gagal.';
                status.className = 'text-xs text-red-500';
                btn.disabled = false;
                btn.innerHTML = `Sync from Tracker`;
            }
        }
        @endif

        calculateAll();
    </script>
@endsection
