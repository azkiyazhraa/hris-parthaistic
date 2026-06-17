<div id="requestLeaveModal" tabindex="-1" aria-hidden="true"
    class="fixed inset-0 z-50 flex items-center justify-center hidden bg-black/40">
    <div class="relative w-full max-w-xl p-4">
        <div class="p-6 bg-white shadow-lg rounded-3xl">
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-blue-900">Request Leave</h3>
                <button data-modal-hide="requestLeaveModal"
                    class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100">✕</button>
            </div>
            <div class="max-h-[80vh] overflow-y-auto p-4">
                <form method="POST" action="{{ route('cuti.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- JENIS CUTI -->
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">
                            Type of Leave
                            <span class="text-red-500">*</span>
                        </label>

                        <select name="jenis_cuti" required
                            class="w-full px-4 py-3 text-sm transition border border-gray-300 outline-none rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Leave Type</option>

                            <option value="tahunan" {{ old('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>
                                Annual Leave (Remaining: {{ $sisaTahunan }} days)
                            </option>

                            <option value="melahirkan" {{ old('jenis_cuti') == 'melahirkan' ? 'selected' : '' }}>
                                Maternity Leave
                            </option>
                        </select>
                    </div>

                    <!-- TANGGAL -->
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-700">
                                Start Date
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                                class="w-full px-4 py-3 text-sm transition border border-gray-300 outline-none rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-700">
                                End Date
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required
                                class="w-full px-4 py-3 text-sm transition border border-gray-300 outline-none rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- ALASAN -->
                    <div>
                        <label class="block mb-2 text-sm font-semibold text-gray-700">
                            Reason
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea name="alasan" rows="5" required placeholder="Write your leave request reason..."
                            class="w-full px-4 py-3 text-sm transition border border-gray-300 outline-none resize-none rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('alasan') }}</textarea>
                    </div>

                    <!-- FOOTER -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t">

                        <button type="button" data-modal-hide="requestLeaveModal"
                            class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                            Cancel
                        </button>

                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium shadow-md transition">
                            Submit Request
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
