@extends('layouts.app')

@section('content')
<div class="container py-4 mx-auto space-y-4">
    <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
        <div>
            <h1 class="mb-1 text-2xl font-bold text-blue-900">Edit Payroll</h1>
            <p class="text-sm text-gray-700/80">{{ $penggajian->bulan_text }} {{ $penggajian->tahun }}</p>
        </div>
        <div class="hidden md:block">
            <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
        </div>
    </div>

    @if (session('error'))
    <div class="px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="p-6 bg-white shadow-lg rounded-2xl" style="border: 2px solid #e0eaff;">
        <form method="POST" action="{{ route('admin.penggajian.update', $penggajian->id) }}" id="gajiForm">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Period</h3>
                <div class="flex items-center gap-4">
                    <label class="text-gray-700">This Month</label>
                    <input type="hidden" name="bulan" value="{{ $penggajian->bulan }}">
                    <input type="hidden" name="tahun" value="{{ $penggajian->tahun }}">
                    <input type="hidden" name="karyawan_id" value="{{ $penggajian->karyawan_id }}">
                </div>
            </div>

            <div class="mb-6">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Employee's Info</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-1 text-sm text-gray-500">Name</label>
                        <p class="font-medium text-gray-800">{{ $penggajian->nama_karyawan }}</p>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm text-gray-500">Employee's ID</label>
                        <p class="font-medium text-gray-800">{{ $penggajian->karyawan->nip ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm text-gray-500">Department</label>
                        <p class="font-medium text-gray-800">{{ ucfirst($penggajian->karyawan->role ?? '-') }}</p>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm text-gray-500">Position</label>
                        <p class="font-medium text-gray-800">{{ ucfirst($penggajian->karyawan->role ?? '-') }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-6 mb-6 border-t border-gray-200">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Earnings</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Base Salary</label>
                        <div class="relative">
                            <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                            <input type="text" name="gaji_pokok" id="gaji_pokok"
                                value="{{ old('gaji_pokok', number_format($penggajian->gaji_pokok, 0, ',', '.')) }}"
                                required class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Allowance: Transport</label>
                        <div class="relative">
                            <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                            <input type="text" name="transport_allowance" id="transport_allowance"
                                value="{{ old('transport_allowance', number_format($penggajian->transport_allowance, 0, ',', '.')) }}"
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Allowance: Meal</label>
                        <div class="relative">
                            <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                            <input type="text" name="meal_allowance" id="meal_allowance"
                                value="{{ old('meal_allowance', number_format($penggajian->meal_allowance, 0, ',', '.')) }}"
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Allowance: Internet</label>
                        <div class="relative">
                            <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                            <input type="text" name="internet_allowance" id="internet_allowance"
                                value="{{ old('internet_allowance', number_format($penggajian->internet_allowance, 0, ',', '.')) }}"
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Allowance: Position</label>
                        <div class="relative">
                            <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                            <input type="text" name="position_allowance" id="position_allowance"
                                value="{{ old('position_allowance', number_format($penggajian->position_allowance, 0, ',', '.')) }}"
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Incentive</label>
                        <div class="relative">
                            <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                            <input type="text" name="incentive" id="incentive"
                                value="{{ old('incentive', number_format($penggajian->incentive, 0, ',', '.')) }}"
                                class="w-full py-2 pl-10 pr-3 border rounded-lg"
                                onkeyup="formatRupiah(this); calculateTotal()">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-6 mb-6 border-t border-gray-200">
                <h3 class="mb-4 text-lg font-semibold text-gray-800">Deduction</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Tax (PPh 21)</label>
                        <div class="relative">
                            <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                            <input type="text" name="tax" id="tax"
                                value="{{ old('tax', number_format($penggajian->tax, 0, ',', '.')) }}"
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
                                <input type="checkbox" id="use_bpjs" onchange="toggleBPJS()" class="rounded"
                                    {{ old('bpjs_ketenagakerjaan', $penggajian->bpjs_ketenagakerjaan) > 0 ? 'checked' : '' }}>
                                <span class="text-xs text-gray-600">
                                    Apply BPJS Employment
                                </span>
                            </label>
                        </div>

                        <div class="relative">
                            <span class="absolute left-3 top-2">Rp</span>
                            <input type="text" name="bpjs_ketenagakerjaan" id="bpjs_ketenagakerjaan"
                                value="{{ old('bpjs_ketenagakerjaan', number_format($penggajian->bpjs_ketenagakerjaan, 0, ',', '.')) }}"
                                class="w-full py-2 pl-10 pr-3 bg-gray-100 border rounded-lg cursor-not-allowed"
                                onkeyup="formatRupiah(this); calculateTotal()" readonly>
                        </div>
                    </div>
                    {{-- <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">BPJS Health</label>
                            <div class="relative">
                                <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                                <input type="text" name="bpjs_kesehatan" id="bpjs_kesehatan"
                                    value="{{ old('bpjs_kesehatan', number_format($penggajian->bpjs_kesehatan, 0, ',', '.')) }}"
                    class="w-full py-2 pl-10 pr-3 border rounded-lg"
                    onkeyup="formatRupiah(this); calculateTotal()">
                </div>
            </div> --}}
            {{-- <div>
                            <label class="block mb-2 text-sm font-bold text-gray-700">BPJS Employment</label>
                            <div class="relative">
                                <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                                <input type="text" name="bpjs_ketenagakerjaan" id="bpjs_ketenagakerjaan"
                                    value="{{ old('bpjs_ketenagakerjaan', number_format($penggajian->bpjs_ketenagakerjaan, 0, ',', '.')) }}"
            class="w-full py-2 pl-10 pr-3 border rounded-lg"
            onkeyup="formatRupiah(this); calculateTotal()">
    </div>
</div> --}}
<div>
    <label class="block mb-2 text-sm font-bold text-gray-700">Late/Absent</label>
    <div class="relative">
        <span class="absolute text-gray-500 left-3 top-2">Rp</span>
        <input type="text" name="late_absent_deduction" id="late_absent_deduction"
            value="{{ old('late_absent_deduction', number_format($penggajian->late_absent_deduction, 0, ',', '.')) }}"
            class="w-full py-2 pl-10 pr-3 border rounded-lg"
            onkeyup="formatRupiah(this); calculateTotal()">
    </div>
</div>
<div>
    <label class="block mb-2 text-sm font-bold text-gray-700">Loan</label>
    <div class="relative">
        <span class="absolute text-gray-500 left-3 top-2">Rp</span>
        <input type="text" name="loan_deduction" id="loan_deduction"
            value="{{ old('loan_deduction', number_format($penggajian->loan_deduction, 0, ',', '.')) }}"
            class="w-full py-2 pl-10 pr-3 border rounded-lg"
            onkeyup="formatRupiah(this); calculateTotal()">
    </div>
</div>
</div>
</div>

<div class="pt-6 mb-6 border-t border-gray-200">
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
                <div class="text-2xl font-bold text-blue-600" id="net_salary_display">Rp 0</div>
                <input type="hidden" id="net_salary" name="net_salary">
            </div>
        </div>
    </div>
</div>

<div class="pt-6 mb-6 border-t border-gray-200">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <label class="block mb-2 text-sm font-bold text-gray-700">Status</label>
            <select name="status" class="w-full px-3 py-2 border rounded-lg">
                <option value="pending" {{ $penggajian->status == 'pending' ? 'selected' : '' }}>Pending
                </option>
                <option value="approved" {{ $penggajian->status == 'approved' ? 'selected' : '' }}>
                    Approved</option>
                <option value="paid" {{ $penggajian->status == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>
        <div>
            <label class="block mb-2 text-sm font-bold text-gray-700">Payment Method</label>
            <select name="metode_pembayaran" class="w-full px-3 py-2 border rounded-lg">
                <option value="">Pilih Metode</option>
                <option value="transfer"
                    {{ $penggajian->metode_pembayaran == 'transfer' ? 'selected' : '' }}>Bank Transfer
                </option>
                <option value="tunai" {{ $penggajian->metode_pembayaran == 'tunai' ? 'selected' : '' }}>
                    Cash</option>
                <option value="cek" {{ $penggajian->metode_pembayaran == 'cek' ? 'selected' : '' }}>
                    Check
                </option>
            </select>
        </div>
        <div>
            <label class="block mb-2 text-sm font-bold text-gray-700">Bank Name</label>
            <input type="text" name="nama_bank"
                value="{{ old('nama_bank', $penggajian->nama_bank) }}"
                class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
            <label class="block mb-2 text-sm font-bold text-gray-700">Account Number</label>
            <input type="text" name="nomor_rekening"
                value="{{ old('nomor_rekening', $penggajian->nomor_rekening) }}"
                class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
            <label class="block mb-2 text-sm font-bold text-gray-700">Payment Date</label>
            <input type="date" name="tanggal_pembayaran"
                value="{{ old('tanggal_pembayaran', $penggajian->tanggal_pembayaran ? $penggajian->tanggal_pembayaran->format('Y-m-d') : '') }}"
                class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div class="md:col-span-2">
            <label class="block mb-2 text-sm font-bold text-gray-700">Notes</label>
            <textarea name="catatan" rows="3" class="w-full px-3 py-2 border rounded-lg">{{ old('catatan', $penggajian->catatan) }}</textarea>
        </div>
    </div>
</div>

<div class="flex justify-end pt-4 space-x-2 border-t">
    <a href="{{ route('admin.penggajian.index') }}"
        class="px-4 py-2 text-white transition bg-gray-500 rounded-lg hover:bg-gray-600">Cancel</a>
    <button type="submit"
        class="px-4 py-2 text-white transition bg-blue-600 rounded-lg hover:bg-blue-700">Update
        Payroll</button>
</div>
</form>
</div>
</div>
@endsection

@push('scripts')
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
        if (!value || value === '0') return 0;
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
            }
        });
        calculateTotal();
    });
</script>
@endpush