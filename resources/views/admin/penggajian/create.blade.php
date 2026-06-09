@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">

        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">
                    Create New Payroll
                </h1>
                <p class="text-gray-700/80 text-sm">
                    Input employee salary components for a specific period.
                </p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow p-6">
            <form method="POST" action="{{ route('admin.penggajian.store') }}" id="gajiForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Employee *</label>
                        <select name="karyawan_id" id="karyawan_id" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">Search Employee</option>
                            @foreach ($karyawans as $karyawan)
                                <option value="{{ $karyawan->id }}" data-nip="{{ $karyawan->nip }}"
                                    data-email="{{ $karyawan->email }}" data-role="{{ $karyawan->role }}">
                                    {{ $karyawan->nama_lengkap }} ({{ $karyawan->nip }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Month *</label>
                        <select name="bulan" id="bulan" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">Select Month</option>
                            @foreach ($bulan as $b)
                                <option value="{{ $b }}" {{ old('bulan') == $b ? 'selected' : '' }}>
                                    {{ ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][$b - 1] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Year *</label>
                        <select name="tahun" id="tahun" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">Select Year</option>
                            @foreach ($tahun as $t)
                                <option value="{{ $t }}" {{ old('tahun') == $t ? 'selected' : '' }}>
                                    {{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Salary Component</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Basic Salary *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="gaji_pokok" id="gaji_pokok" value="0" required
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Transport</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="transport_allowance" id="transport_allowance" value="0"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Meal</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="meal_allowance" id="meal_allowance" value="0"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Internet</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="internet_allowance" id="internet_allowance" value="0"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Position Allowance</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="position_allowance" id="position_allowance" value="0"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Incentive</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="incentive" id="incentive" value="0"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Deductions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tax (PPh 21)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="tax" id="tax" value="0"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">BPJS Health</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="bpjs_kesehatan" id="bpjs_kesehatan" value="0"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">BPJS Employment</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="bpjs_ketenagakerjaan" id="bpjs_ketenagakerjaan"
                                    value="0" class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Late/Absent Deduction</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="late_absent_deduction" id="late_absent_deduction"
                                    value="0" class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Loan Deduction</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2">Rp</span>
                                <input type="text" name="loan_deduction" id="loan_deduction" value="0"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Total Earnings</label>
                                <div class="text-xl font-bold text-green-600" id="total_earnings_display">Rp 0</div>
                                <input type="hidden" id="total_earnings" name="total_earnings">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Total Deductions</label>
                                <div class="text-xl font-bold text-red-600" id="total_deductions_display">Rp 0</div>
                                <input type="hidden" id="total_deductions" name="total_deductions">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Net Salary</label>
                                <div class="text-xl font-bold text-blue-600" id="net_salary_display">Rp 0</div>
                                <input type="hidden" id="net_salary" name="net_salary">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Status *</label>
                            <select name="status" class="w-full border rounded-lg px-3 py-2">
                                <option value="draft">Draft</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Payment Method</label>
                            <select name="metode_pembayaran" class="w-full border rounded-lg px-3 py-2">
                                <option value="">Select Method</option>
                                <option value="transfer">Bank Transfer</option>
                                <option value="tunai">Cash</option>
                                <option value="cek">Check</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Bank Name</label>
                            <input type="text" name="nama_bank" class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Account Number</label>
                            <input type="text" name="nomor_rekening" class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Payment Date</label>
                            <input type="date" name="tanggal_pembayaran" class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                            <textarea name="catatan" rows="3" class="w-full border rounded-lg px-3 py-2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.penggajian.index') }}"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancel</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Save</button>
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
            if (!value || value === '0') return 0;
            return parseInt(value.replace(/\./g, '')) || 0;
        }

        function calculateTotal() {
            let gajiPokok = parseRupiah(document.getElementById('gaji_pokok').value);
            let transport = parseRupiah(document.getElementById('transport_allowance').value);
            let meal = parseRupiah(document.getElementById('meal_allowance').value);
            let internet = parseRupiah(document.getElementById('internet_allowance').value);
            let position = parseRupiah(document.getElementById('position_allowance').value);
            let incentive = parseRupiah(document.getElementById('incentive').value);
            let tax = parseRupiah(document.getElementById('tax').value);
            let bpjsKes = parseRupiah(document.getElementById('bpjs_kesehatan').value);
            let bpjsTK = parseRupiah(document.getElementById('bpjs_ketenagakerjaan').value);
            let lateAbsent = parseRupiah(document.getElementById('late_absent_deduction').value);
            let loan = parseRupiah(document.getElementById('loan_deduction').value);

            let totalEarnings = gajiPokok + transport + meal + internet + position + incentive;
            let totalDeductions = tax + bpjsKes + bpjsTK + lateAbsent + loan;
            let netSalary = Math.max(0, totalEarnings - totalDeductions);

            document.getElementById('total_earnings').value = totalEarnings;
            document.getElementById('total_earnings_display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(
                totalEarnings);
            document.getElementById('total_deductions').value = totalDeductions;
            document.getElementById('total_deductions_display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(
                totalDeductions);
            document.getElementById('net_salary').value = netSalary;
            document.getElementById('net_salary_display').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(
                netSalary);
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
    </script>
@endsection
