@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">Add Performance Assesment</h1>
                <p class="text-gray-700/80 text-sm">Fill in the form to add employee performance assessment</p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-6" style="border: 2px solid #e0eaff;">
            <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4">
                <p class="text-blue-700 text-sm">
                    <strong>Auto Calculation Information :</strong><br>
                    - KPI Score = Average of (Productivity + Discipline + Quality + Teamwork)<br>
                    - Attendance Rate = Same as KPI Score<br>
                    - Performance Score = Weighted calculation of all components
                </p>
            </div>

            <form method="POST" action="{{ route('admin.performa.store') }}" id="performaForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Employee *</label>
                        <select name="karyawan_id" id="karyawan_id" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">Search Employee</option>
                            @foreach($karyawans as $karyawan)
                                <option value="{{ $karyawan->id }}" {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->nama_lengkap }} ({{ $karyawan->nip }})
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Bulan *</label>
                        <select name="bulan" id="bulan" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">Select Months</option>
                            @foreach($bulan as $b)
                                @php
    $bulanNama = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'];
                                @endphp
                                <option value="{{ $b }}" {{ old('bulan', $currentMonth) == $b ? 'selected' : '' }}>{{ $bulanNama[$b] }}</option>
                            @endforeach
                        </select>
                        @error('bulan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Tahun *</label>
                        <select name="tahun" id="tahun" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">Select Year</option>
                            @foreach($tahun as $t)
                                <option value="{{ $t }}" {{ old('tahun', $currentYear) == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                        @error('tahun')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance Assesment (0-100)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Quality *</label>
                            <input type="number" id="quality" name="quality" value="{{ old('quality') }}" min="0" max="100" required
                                class="w-full border rounded-lg px-3 py-2" onchange="calculateAll()" onkeyup="calculateAll()">
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1"><div id="quality_bar" class="bg-green-600 rounded-full h-2" style="width: 0%"></div></div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Productivity *</label>
                            <input type="number" id="productivity" name="productivity" value="{{ old('productivity') }}" min="0" max="100" required
                                class="w-full border rounded-lg px-3 py-2" onchange="calculateAll()" onkeyup="calculateAll()">
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1"><div id="productivity_bar" class="bg-yellow-600 rounded-full h-2" style="width: 0%"></div></div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Teamwork *</label>
                            <input type="number" id="teamwork" name="teamwork" value="{{ old('teamwork') }}" min="0" max="100" required
                                class="w-full border rounded-lg px-3 py-2" onchange="calculateAll()" onkeyup="calculateAll()">
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1"><div id="teamwork_bar" class="bg-purple-600 rounded-full h-2" style="width: 0%"></div></div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Discipline *</label>
                            <input type="number" id="discipline" name="discipline" value="{{ old('discipline') }}" min="0" max="100" required
                                class="w-full border rounded-lg px-3 py-2" onchange="calculateAll()" onkeyup="calculateAll()">
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-1"><div id="discipline_bar" class="bg-indigo-600 rounded-full h-2" style="width: 0%"></div></div>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Auto calculation Result</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">KPI Score (Automatic)</label>
                            <div class="text-3xl font-bold text-blue-600" id="kpi_score_display">0</div>
                            <input type="hidden" id="kpi_score" name="kpi_score" value="0">
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2"><div id="kpi_bar" class="bg-blue-600 rounded-full h-2" style="width: 0%"></div></div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Attendance Rate (Automatic)</label>
                            <div class="text-3xl font-bold text-green-600" id="attendance_rate_display">0</div>
                            <input type="hidden" id="attendance_rate" name="attendance_rate" value="0">
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2"><div id="attendance_bar" class="bg-green-600 rounded-full h-2" style="width: 0%"></div></div>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Total Performance Score</label>
                                <div class="text-3xl font-bold text-purple-600" id="performance_score_display">0</div>
                                <input type="hidden" id="performance_score" name="performance_score">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Rating</label>
                                <div id="rating_display" class="text-lg font-semibold">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Notes (Optional)</label>
                    <textarea name="catatan" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('catatan') }}</textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.performa.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">Cancel</a>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">Save Assesment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function calculateAll() {
        let quality = parseInt(document.getElementById('quality').value) || 0;
        let productivity = parseInt(document.getElementById('productivity').value) || 0;
        let teamwork = parseInt(document.getElementById('teamwork').value) || 0;
        let discipline = parseInt(document.getElementById('discipline').value) || 0;

        document.getElementById('quality_bar').style.width = quality + '%';
        document.getElementById('productivity_bar').style.width = productivity + '%';
        document.getElementById('teamwork_bar').style.width = teamwork + '%';
        document.getElementById('discipline_bar').style.width = discipline + '%';

        let kpiScore = Math.round((quality + productivity + teamwork + discipline) / 4);
        let attendanceRate = kpiScore;

        document.getElementById('kpi_score').value = kpiScore;
        document.getElementById('kpi_score_display').innerText = kpiScore;
        document.getElementById('kpi_bar').style.width = kpiScore + '%';
        document.getElementById('attendance_rate').value = attendanceRate;
        document.getElementById('attendance_rate_display').innerText = attendanceRate;
        document.getElementById('attendance_bar').style.width = attendanceRate + '%';

        let total = Math.round((attendanceRate * 0.15) + (quality * 0.20) + (productivity * 0.20) + (teamwork * 0.15) + (discipline * 0.15) + (kpiScore * 0.15));

        document.getElementById('performance_score').value = total;
        document.getElementById('performance_score_display').innerText = total;

        let rating = '', ratingColor = '';
        if (total >= 90) { rating = 'Excellent (A)'; ratingColor = 'green'; }
        else if (total >= 75) { rating = 'Good (B)'; ratingColor = 'blue'; }
        else if (total >= 60) { rating = 'Fair (C)'; ratingColor = 'yellow'; }
        else if (total >= 50) { rating = 'Poor (D)'; ratingColor = 'orange'; }
        else { rating = 'Very Poor (E)'; ratingColor = 'red'; }

        document.getElementById('rating_display').innerHTML = '<span class="bg-'+ratingColor+'-100 text-'+ratingColor+'-800 py-1 px-3 rounded-full text-xs">'+rating+'</span>';
    }
    calculateAll();
    </script>
@endsection