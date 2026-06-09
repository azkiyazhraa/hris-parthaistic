@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-4 space-y-4">
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">Edit Payroll</h1>
                <p class="text-gray-700/80 text-sm">{{ $penggajian->bulan_text }} {{ $penggajian->tahun }}</p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-6" style="border: 2px solid #e0eaff;">
            <form method="POST" action="{{ route('admin.penggajian.update', $penggajian->id) }}" id="gajiForm">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Period</h3>
                    <div class="flex items-center gap-4">
                        <label class="text-gray-700">This Month</label>
                        <input type="hidden" name="bulan" value="{{ $penggajian->bulan }}">
                        <input type="hidden" name="tahun" value="{{ $penggajian->tahun }}">
                        <input type="hidden" name="karyawan_id" value="{{ $penggajian->karyawan_id }}">
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Employee's Info</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-500 text-sm mb-1">Name</label>
                            <p class="text-gray-800 font-medium">{{ $penggajian->nama_karyawan }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-sm mb-1">Employee's ID</label>
                            <p class="text-gray-800 font-medium">{{ $penggajian->karyawan->nip ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-sm mb-1">Department</label>
                            <p class="text-gray-800 font-medium">{{ ucfirst($penggajian->karyawan->role ?? '-') }}</p>
                        </div>
                        <div>
                            <label class="block text-gray-500 text-sm mb-1">Position</label>
                            <p class="text-gray-800 font-medium">{{ ucfirst($penggajian->karyawan->role ?? '-') }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Earnings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Base Salary</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="text" name="gaji_pokok" id="gaji_pokok"
                                    value="{{ old('gaji_pokok', number_format($penggajian->gaji_pokok, 0, ',', '.')) }}"
                                    required class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Allowance: Transport</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="text" name="transport_allowance" id="transport_allowance"
                                    value="{{ old('transport_allowance', number_format($penggajian->transport_allowance, 0, ',', '.')) }}"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Allowance: Meal</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="text" name="meal_allowance" id="meal_allowance"
                                    value="{{ old('meal_allowance', number_format($penggajian->meal_allowance, 0, ',', '.')) }}"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Allowance: Internet</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="text" name="internet_allowance" id="internet_allowance"
                                    value="{{ old('internet_allowance', number_format($penggajian->internet_allowance, 0, ',', '.')) }}"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Allowance: Position</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="text" name="position_allowance" id="position_allowance"
                                    value="{{ old('position_allowance', number_format($penggajian->position_allowance, 0, ',', '.')) }}"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Incentive</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="text" name="incentive" id="incentive"
                                    value="{{ old('incentive', number_format($penggajian->incentive, 0, ',', '.')) }}"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Deduction</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tax (PPh 21)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="text" name="tax" id="tax"
                                    value="{{ old('tax', number_format($penggajian->tax, 0, ',', '.')) }}"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">BPJS Health</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="text" name="bpjs_kesehatan" id="bpjs_kesehatan"
                                    value="{{ old('bpjs_kesehatan', number_format($penggajian->bpjs_kesehatan, 0, ',', '.')) }}"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">BPJS Employment</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="text" name="bpjs_ketenagakerjaan" id="bpjs_ketenagakerjaan"
                                    value="{{ old('bpjs_ketenagakerjaan', number_format($penggajian->bpjs_ketenagakerjaan, 0, ',', '.')) }}"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Late/Absent</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="text" name="late_absent_deduction" id="late_absent_deduction"
                                    value="{{ old('late_absent_deduction', number_format($penggajian->late_absent_deduction, 0, ',', '.')) }}"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Loan</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                                <input type="text" name="loan_deduction" id="loan_deduction"
                                    value="{{ old('loan_deduction', number_format($penggajian->loan_deduction, 0, ',', '.')) }}"
                                    class="w-full pl-10 pr-3 py-2 border rounded-lg"
                                    onkeyup="formatRupiah(this); calculateTotal()">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6 mb-6">
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
                                <div class="text-2xl font-bold text-blue-600" id="net_salary_display">Rp 0</div>
                                <input type="hidden" id="net_salary" name="net_salary">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                            <select name="status" class="w-full border rounded-lg px-3 py-2">
                                <option value="draft" {{ $penggajian->status == 'draft' ? 'selected' : '' }}>Draft
                                </option>
                                <option value="pending" {{ $penggajian->status == 'pending' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="approved" {{ $penggajian->status == 'approved' ? 'selected' : '' }}>
                                    Approved</option>
                                <option value="paid" {{ $penggajian->status == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Payment Method</label>
                            <select name="metode_pembayaran" class="w-full border rounded-lg px-3 py-2">
                                <option value="">Pilih Metode</option>
                                <option value="transfer"
                                    {{ $penggajian->metode_pembayaran == 'transfer' ? 'selected' : '' }}>Bank Transfer
                                </option>
                                <option value="tunai" {{ $penggajian->metode_pembayaran == 'tunai' ? 'selected' : '' }}>
                                    Cash</option>
                                <option value="cek" {{ $penggajian->metode_pembayaran == 'cek' ? 'selected' : '' }}>Check
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Bank Name</label>
                            <input type="text" name="nama_bank"
                                value="{{ old('nama_bank', $penggajian->nama_bank) }}"
                                class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Account Number</label>
                            <input type="text" name="nomor_rekening"
                                value="{{ old('nomor_rekening', $penggajian->nomor_rekening) }}"
                                class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Payment Date</label>
                            <input type="date" name="tanggal_pembayaran"
                                value="{{ old('tanggal_pembayaran', $penggajian->tanggal_pembayaran ? $penggajian->tanggal_pembayaran->format('Y-m-d') : '') }}"
                                class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                            <textarea name="catatan" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('catatan', $penggajian->catatan) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t">
                    <a href="{{ route('admin.penggajian.index') }}"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</a>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Update
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
                }
            });
            calculateTotal();
        });
    </script>
@endpush
