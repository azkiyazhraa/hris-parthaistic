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
                    - Attendance Rate = Fetched from real attendance data for the selected period<br>
                    - Performance Score = (KPI Score + Attendance Rate) / 2 <br>
                    - Rating is based on Performance Score: ≥ 90 = Excellent (A), 75-89 = Good (B), 60-74 = Fair (C), 50-59
                    = Poor (D), < 50=Very Poor (E) </p>
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
                        <label class="block mb-2 text-sm font-bold text-gray-700">Bulan *</label>
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
                        <label class="block mb-2 text-sm font-bold text-gray-700">Tahun *</label>
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
                                max="100" required class="w-full px-3 py-2 border rounded-lg" onchange="calculateAll()"
                                onkeyup="calculateAll()">
                            <div class="w-full h-2 mt-1 bg-gray-200 rounded-full">
                                <div id="quality_bar" class="h-2 bg-green-600 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Productivity *</label>
                            <input type="number" id="productivity" name="productivity" value="{{ old('productivity') }}"
                                min="0" max="100" required class="w-full px-3 py-2 border rounded-lg"
                                onchange="calculateAll()" onkeyup="calculateAll()">
                            <div class="w-full h-2 mt-1 bg-gray-200 rounded-full">
                                <div id="productivity_bar" class="h-2 bg-yellow-600 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Teamwork *</label>
                            <input type="number" id="teamwork" name="teamwork" value="{{ old('teamwork') }}"
                                min="0" max="100" required class="w-full px-3 py-2 border rounded-lg"
                                onchange="calculateAll()" onkeyup="calculateAll()">
                            <div class="w-full h-2 mt-1 bg-gray-200 rounded-full">
                                <div id="teamwork_bar" class="h-2 bg-purple-600 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Discipline *</label>
                            <input type="number" id="discipline" name="discipline" value="{{ old('discipline') }}"
                                min="0" max="100" required class="w-full px-3 py-2 border rounded-lg"
                                onchange="calculateAll()" onkeyup="calculateAll()">
                            <div class="w-full h-2 mt-1 bg-gray-200 rounded-full">
                                <div id="discipline_bar" class="h-2 bg-indigo-600 rounded-full" style="width: 0%"></div>
                            </div>
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

        function calculateAll() {
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

            // Performance Score = (KPI Score + Attendance Rate) / 2
            const total = Math.round((kpiScore + realAttendanceRate) / 2);

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

        document.getElementById('karyawan_id').addEventListener('change', fetchAttendanceRate);
        document.getElementById('bulan').addEventListener('change', fetchAttendanceRate);
        document.getElementById('tahun').addEventListener('change', fetchAttendanceRate);

        calculateAll();
    </script>
@endsection
