@extends('layouts.app')
@section('content')
<div class="container mx-auto py-4 space-y-4">
    <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-blue-900 mb-1">Bulk Performance Assesment</h1>
            <p class="text-gray-700/80 text-sm">Fill in assesment for multiple employees at once</p>
        </div>
        <div class="hidden md:block">
            <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6" style="border: 2px solid #e0eaff;">
        <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4">
            <p class="text-blue-700 text-sm">
                <strong>Information :</strong> KPI Score, Attendance Rate, and Performance will be calculated automatically.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.performa.bulk.store') }}" id="bulkForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Months *</label>
                    <select name="bulan" id="bulan" required class="w-full border rounded-lg px-3 py-2">
                        @for ($i = 1; $i <= 12; $i++)
                            @php $bulanNama = [1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December']; @endphp
                            <option value="{{ $i }}" {{ $currentMonth == $i ? 'selected' : '' }}>{{ $bulanNama[$i] }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Year *</label>
                    <select name="tahun" id="tahun" required class="w-full border rounded-lg px-3 py-2">
                        @for ($i = 2023; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ $currentYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-xs">
                            <th class="py-3 px-2 text-left">Karyawan</th>
                            <th class="py-3 px-2 text-center">Quality</th>
                            <th class="py-3 px-2 text-center">Productivity</th>
                            <th class="py-3 px-2 text-center">Teamwork</th>
                            <th class="py-3 px-2 text-center">Discipline</th>
                            <th class="py-3 px-2 text-center">KPI</th>
                            <th class="py-3 px-2 text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($karyawans as $index => $karyawan)
                            <tr class="border-b hover:bg-blue-50/40 transition">
                                <td class="py-2 px-2">
                                    <span class="font-medium">{{ $karyawan->nama_lengkap }}</span>
                                    <input type="hidden" name="performas[{{ $index }}][karyawan_id]" value="{{ $karyawan->id }}">
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <input type="number" name="performas[{{ $index }}][quality]" class="quality w-16 border rounded text-center py-1" min="0" max="100" value="0" onchange="calcRow(this)" onkeyup="calcRow(this)">
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <input type="number" name="performas[{{ $index }}][productivity]" class="productivity w-16 border rounded text-center py-1" min="0" max="100" value="0" onchange="calcRow(this)" onkeyup="calcRow(this)">
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <input type="number" name="performas[{{ $index }}][teamwork]" class="teamwork w-16 border rounded text-center py-1" min="0" max="100" value="0" onchange="calcRow(this)" onkeyup="calcRow(this)">
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <input type="number" name="performas[{{ $index }}][discipline]" class="discipline w-16 border rounded text-center py-1" min="0" max="100" value="0" onchange="calcRow(this)" onkeyup="calcRow(this)">
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <span class="kpi-display font-semibold text-blue-600">0</span>
                                </td>
                                <td class="py-2 px-2 text-center">
                                    <span class="total-display font-bold text-purple-600">0</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('admin.performa.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">Cancel</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">Save All</button>
            </div>
        </form>
    </div>
</div>

<script>
function calcRow(el) {
    const row = el.closest('tr');
    const q = parseInt(row.querySelector('.quality').value) || 0;
    const p = parseInt(row.querySelector('.productivity').value) || 0;
    const t = parseInt(row.querySelector('.teamwork').value) || 0;
    const d = parseInt(row.querySelector('.discipline').value) || 0;
    const kpi = Math.round((q + p + t + d) / 4);
    const total = Math.round((kpi * 0.15) + (q * 0.20) + (p * 0.20) + (t * 0.15) + (d * 0.15) + (kpi * 0.15));
    row.querySelector('.kpi-display').innerText = kpi;
    row.querySelector('.total-display').innerText = total;
}
</script>
@endsection