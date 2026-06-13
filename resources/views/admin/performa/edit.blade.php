@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">Edit Performance Assessment</h1>
                <p class="text-gray-700/80 text-sm">{{ $performa->nama_karyawan }} - {{ $performa->bulan_text }}
                    {{ $performa->tahun }}
                </p>
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
                    - KPI Score = Average of (Quality + Productivity + Teamwork + Discipline)<br>
                    - Attendance Rate = Saved from real attendance data at time of creation<br>
                    - Performance Score = (KPI Score + Attendance Rate) / 2
                </p>
            </div>

            <form method="POST" action="{{ route('admin.performa.update', $performa->id) }}" id="performaForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Employee *</label>
                        <select name="karyawan_id" id="karyawan_id" required class="w-full border rounded-lg px-3 py-2">
                            @foreach($karyawans as $karyawan)
                                <option value="{{ $karyawan->id }}" {{ old('karyawan_id', $performa->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->nama_lengkap }} ({{ $karyawan->nip }})
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Month *</label>
                        <select name="bulan" id="bulan" required class="w-full border rounded-lg px-3 py-2">
                            @foreach($bulan as $b)
                                @php $bulanNama = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December']; @endphp
                                <option value="{{ $b }}" {{ old('bulan', $performa->bulan) == $b ? 'selected' : '' }}>
                                    {{ $bulanNama[$b] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Year *</label>
                        <select name="tahun" id="tahun" required class="w-full border rounded-lg px-3 py-2">
                            @foreach($tahun as $t)
                                <option value="{{ $t }}" {{ old('tahun', $performa->tahun) == $t ? 'selected' : '' }}>{{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Assessment Components (0-100)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @php $fields = ['quality' => 'green', 'productivity' => 'yellow', 'teamwork' => 'purple', 'discipline' => 'indigo']; @endphp
                        @foreach($fields as $field => $color)
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">{{ ucfirst($field) }} *</label>
                                <input type="number" id="{{ $field }}" name="{{ $field }}"
                                    value="{{ old($field, $performa->$field) }}" min="0" max="100" required
                                    class="w-full border rounded-lg px-3 py-2" onchange="calculateAll()"
                                    onkeyup="calculateAll()">
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div id="{{ $field }}_bar" class="bg-{{ $color }}-600 rounded-full h-2"
                                        style="width: {{ $performa->$field }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Auto Calculation Result</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">KPI Score</label>
                            <div class="text-3xl font-bold text-blue-600" id="kpi_score_display">{{ $performa->kpi_score }}
                            </div>
                            <input type="hidden" id="kpi_score" name="kpi_score" value="{{ $performa->kpi_score }}">
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Attendance Rate</label>
                            <div class="text-3xl font-bold text-green-600" id="attendance_rate_display">
                                {{ $performa->attendance_rate }}
                            </div>
                            <input type="hidden" id="attendance_rate" name="attendance_rate"
                                value="{{ $performa->attendance_rate }}">
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Total Performance Score</label>
                                <div class="text-3xl font-bold text-purple-600" id="performance_score_display">
                                    {{ $performa->performance_score }}
                                </div>
                                <input type="hidden" id="performance_score" name="performance_score"
                                    value="{{ $performa->performance_score }}">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Rating</label>
                                <div id="rating_display" class="text-lg font-semibold">
                                    <span
                                        class="bg-{{ $performa->rating['color'] }}-100 text-{{ $performa->rating['color'] }}-800 py-1 px-3 rounded-full text-xs">{{ $performa->rating['label'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Notes (Optional)</label>
                    <textarea name="catatan" rows="3"
                        class="w-full border rounded-lg px-3 py-2">{{ old('catatan', $performa->catatan) }}</textarea>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.performa.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">Batal</a>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">Update
                        Penilaian</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const savedAttendanceRate = {{ $performa->attendance_rate ?? 0 }};

        function calculateAll() {
            const quality      = parseInt(document.getElementById('quality').value)      || 0;
            const productivity = parseInt(document.getElementById('productivity').value) || 0;
            const teamwork     = parseInt(document.getElementById('teamwork').value)     || 0;
            const discipline   = parseInt(document.getElementById('discipline').value)   || 0;

            ['quality', 'productivity', 'teamwork', 'discipline'].forEach(f => {
                document.getElementById(f + '_bar').style.width = document.getElementById(f).value + '%';
            });

            const kpiScore = Math.round((quality + productivity + teamwork + discipline) / 4);
            document.getElementById('kpi_score').value          = kpiScore;
            document.getElementById('kpi_score_display').innerText = kpiScore;

            // Attendance rate stays as saved — not recalculated from KPI
            const attendanceRate = savedAttendanceRate;

            const total = Math.round((kpiScore + attendanceRate) / 2);
            document.getElementById('performance_score').value          = total;
            document.getElementById('performance_score_display').innerText = total;

            let rating = '', ratingColor = '';
            if      (total >= 90) { rating = 'Excellent (A)'; ratingColor = 'green';  }
            else if (total >= 75) { rating = 'Good (B)';      ratingColor = 'blue';   }
            else if (total >= 60) { rating = 'Fair (C)';      ratingColor = 'yellow'; }
            else if (total >= 50) { rating = 'Poor (D)';      ratingColor = 'orange'; }
            else                  { rating = 'Very Poor (E)'; ratingColor = 'red';    }

            document.getElementById('rating_display').innerHTML =
                '<span class="bg-' + ratingColor + '-100 text-' + ratingColor + '-800 py-1 px-3 rounded-full text-xs">' + rating + '</span>';
        }
        calculateAll();
    </script>
@endsection