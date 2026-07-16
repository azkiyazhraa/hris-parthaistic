@extends('layouts.app')
@section('content')
<div class="container py-4 mx-auto space-y-4">

    <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
        <div>
            <h1 class="mb-1 text-2xl font-bold text-blue-900">
                Create New Payroll
            </h1>
            <p class="text-sm text-gray-700/80">
                Input employee salary components for a specific period.
            </p>
        </div>
        <div class="hidden md:block">
            <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
        </div>
    </div>

    @if (session('error'))
    <div class="px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded">
        {{ session('error') }}
    </div>
    @endif

    <div class="p-6 bg-white shadow rounded-2xl">
        <form method="POST" action="{{ route('admin.penggajian.store') }}" id="gajiForm">
            @csrf

            <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2">
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">Employee *</label>
                    <select name="karyawan_id" id="karyawan_id" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Search Employee</option>
                        @foreach ($karyawans as $karyawan)
                        <option value="{{ $karyawan->id }}" data-nip="{{ $karyawan->nip }}"
                            data-email="{{ $karyawan->email }}" data-role="{{ $karyawan->role }}"
                            {{ old('karyawan_id') == $karyawan->id ? 'selected' : '' }}>
                            {{ $karyawan->nama_lengkap }} ({{ $karyawan->nip }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-bold text-gray-700">Month *</label>
                    <select name="bulan" id="bulan" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Select Month</option>
                        @foreach ($bulan as $b)
                        <option value="{{ $b }}" {{ old('bulan') == $b ? 'selected' : '' }}>
                            {{ ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][$b - 1] }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-6 mb-6 border-t">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Salary Component</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Basic Salary *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2">Rp</span>
                            <input type="text" name="gaji_pokok" id="gaji_pokok" value="0" required
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Transport</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2">Rp</span>
                            <input type="text" name="transport_allowance" id="transport_allowance" value="0"
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Meal</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2">Rp</span>
                            <input type="text" name="meal_allowance" id="meal_allowance" value="0"
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Internet</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2">Rp</span>
                            <input type="text" name="internet_allowance" id="internet_allowance" value="0"
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Position Allowance</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2">Rp</span>
                            <input type="text" name="position_allowance" id="position_allowance" value="0"
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Incentive</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2">Rp</span>
                            <input type="text" name="incentive" id="incentive" value="0"
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 mb-6 border-t">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Deductions</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Tax (PPh 21)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2">Rp</span>
                            <input type="text" name="tax" id="tax" value="0"
                                class="w-full py-2 pl-10 pr-3 bg-gray-100 border rounded-lg cursor-not-allowed"
                                onkeyup="formatRupiah(this); calculateTotal()" readonly>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-bold text-gray-700">
                                BPJS Employment
                            </label>

                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="use_bpjs" onchange="toggleBPJS()" class="rounded">
                                <span class="text-xs text-gray-600">
                                    Apply BPJS Employment
                                </span>
                            </label>
                        </div>

                        <div class="relative">
                            <span class="absolute left-3 top-2">Rp</span>
                            <input type="text" name="bpjs_ketenagakerjaan" id="bpjs_ketenagakerjaan"
                                value="0"
                                class="w-full py-2 pl-10 pr-3 bg-gray-100 border rounded-lg cursor-not-allowed"
                                onkeyup="formatRupiah(this); calculateTotal()" readonly>
                        </div>
                    </div>
                    {{-- <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">BPJS Health</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="bpjs_kesehatan" id="bpjs_kesehatan" value="0"
                                    class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div> --}}
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Late/Absent Deduction</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2">Rp</span>
                            <input type="text" name="late_absent_deduction" id="late_absent_deduction"
                                value="0" class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Loan Deduction</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2">Rp</span>
                            <input type="text" name="loan_deduction" id="loan_deduction" value="0"
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 mb-6 border-t">
                <div class="p-4 rounded-lg bg-blue-50">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Total Earnings</label>
                            <div class="text-xl font-bold text-green-600" id="total_earnings_display">Rp 0</div>
                            <input type="hidden" id="total_earnings" name="total_earnings">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Total Deductions</label>
                            <div class="text-xl font-bold text-red-600" id="total_deductions_display">Rp 0</div>
                            <input type="hidden" id="total_deductions" name="total_deductions">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">Net Salary</label>
                            <div class="text-xl font-bold text-blue-600" id="net_salary_display">Rp 0</div>
                            <input type="hidden" id="net_salary" name="net_salary">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 mb-6 border-t">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Payment Information</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Status *</label>
                        <select name="status" class="w-full px-3 py-2 border rounded-lg">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Payment Method</label>
                        <select name="metode_pembayaran" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">Select Method</option>
                            <option value="transfer">Bank Transfer</option>
                            <option value="tunai">Cash</option>
                            <option value="cek">Check</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Bank Name</label>
                        <input type="text" id="nama_bank" name="nama_bank"
                            class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Account Number</label>
                        <input type="text" id="nomor_rekening" name="nomor_rekening"
                            class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Payment Date</label>
                        <input type="date" name="tanggal_pembayaran" class="w-full px-3 py-2 border rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Notes</label>
                        <textarea name="catatan" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.penggajian.index') }}"
                    class="px-4 py-2 text-white bg-gray-500 rounded-lg">Cancel</a>
                <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    function formatRupiah(element) {
        let value = element.value.replace(/[^,\d]/g, '');
        value = value.replace(/\D/g, '');
        value = value.replace(/^0+(?=\d)/, '');
        if (value === '') value = '0';
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        element.value = value;
    }

    function parseRupiah(value) {
        if (!value || value == '0') return 0;
        return parseInt(value.replace(/\./g, '')) || 0;
    }

    function calculateTotal() {

        const formatIDR = (value) =>
            new Intl.NumberFormat('id-ID').format(Math.round(value));

        const gajiPokok = parseRupiah(document.getElementById('gaji_pokok').value);
        const transport = parseRupiah(document.getElementById('transport_allowance').value);
        const meal = parseRupiah(document.getElementById('meal_allowance').value);
        const internet = parseRupiah(document.getElementById('internet_allowance').value);
        const position = parseRupiah(document.getElementById('position_allowance').value);
        const incentive = parseRupiah(document.getElementById('incentive').value);

        const totalEarnings =
            gajiPokok +
            transport +
            meal +
            internet +
            position +
            incentive;

        // PPh 21
        const taxPph = totalEarnings >= 5000000 ?
            totalEarnings * 0.05 :
            0;

        // Ambil dari input BPJS
        const bpjsTK = parseRupiah(
            document.getElementById('bpjs_ketenagakerjaan').value
        );

        const lateAbsent = parseRupiah(
            document.getElementById('late_absent_deduction').value
        );

        const loan = parseRupiah(
            document.getElementById('loan_deduction').value
        );

        const totalDeductions =
            taxPph +
            bpjsTK +
            lateAbsent +
            loan;

        const netSalary = Math.max(
            0,
            totalEarnings - totalDeductions
        );

        // Auto isi pajak
        document.getElementById('tax').value = formatIDR(taxPph);

        // Summary
        document.getElementById('total_earnings').value = totalEarnings;
        document.getElementById('total_deductions').value = totalDeductions;
        document.getElementById('net_salary').value = netSalary;

        document.getElementById('total_earnings_display').innerText =
            'Rp ' + formatIDR(totalEarnings);

        document.getElementById('total_deductions_display').innerText =
            'Rp ' + formatIDR(totalDeductions);

        document.getElementById('net_salary_display').innerText =
            'Rp ' + formatIDR(netSalary);
    }

    function getTotalEarnings() {
        return (
            parseRupiah(document.getElementById('gaji_pokok').value) +
            parseRupiah(document.getElementById('transport_allowance').value) +
            parseRupiah(document.getElementById('meal_allowance').value) +
            parseRupiah(document.getElementById('internet_allowance').value) +
            parseRupiah(document.getElementById('position_allowance').value) +
            parseRupiah(document.getElementById('incentive').value)
        );
    }

    function toggleBPJS() {

        const checkbox = document.getElementById('use_bpjs');
        const bpjsInput = document.getElementById('bpjs_ketenagakerjaan');

        if (checkbox.checked) {

            const bpjsValue = getTotalEarnings() * 0.03;

            bpjsInput.value = new Intl.NumberFormat('id-ID')
                .format(Math.round(bpjsValue));

            bpjsInput.readOnly = true;

        } else {

            bpjsInput.value = '0';
            bpjsInput.readOnly = false;

        }

        calculateTotal();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputs = ['gaji_pokok', 'transport_allowance', 'meal_allowance', 'internet_allowance',
            'position_allowance', 'incentive', 'tax', 'bpjs_kesehatan', 'bpjs_ketenagakerjaan',
            'late_absent_deduction', 'loan_deduction'
        ];
        inputs.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('keyup', () => {
                    formatRupiah(element);
                    calculateTotal();
                });
                if (element.value === '0') element.value = '0';
            }
        });
        calculateTotal();
    });

    document.getElementById('karyawan_id').addEventListener('change', function() {

        const karyawanId = this.value;

        if (!karyawanId) {
            document.getElementById('nama_bank').value = '';
            document.getElementById('nomor_rekening').value = '';
            return;
        }

        fetch(`/admin/karyawan/detail/${karyawanId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('nama_bank').value = data.nama_bank || '';
                document.getElementById('nomor_rekening').value = data.nomor_rekening || '';
            })
            .catch(error => {
                console.error(error);
            });
    });
</script>
@endsection