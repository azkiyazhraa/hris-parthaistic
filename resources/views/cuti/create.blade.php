<div id="requestLeaveModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex justify-center items-center bg-black/40">
    <div class="relative w-full max-w-xl p-4">
        <div class="bg-white rounded-3xl shadow-lg p-6">
            <div class="flex justify-between items-center border-b pb-4">
                <h3 class="text-lg font-semibold text-blue-900">Request Leave</h3>
                <button data-modal-hide="requestLeaveModal"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">✕</button>
            </div>
            <div class="max-h-[80vh] overflow-y-auto p-4">
                <form method="POST" action="{{ route('cuti.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <!-- JENIS CUTI -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Type of Leave
                            <span class="text-red-500">*</span>
                        </label>

                        <select name="jenis_cuti" required
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            <option value="">Select Leave Type</option>
                        
                            <option value="tahunan" {{ old('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>
                                Annual Leave (Remaining: {{ $sisaTahunan }} days)
                            </option>
                        
                            <option value="sakit" {{ old('jenis_cuti') == 'sakit' ? 'selected' : '' }}>
                                Sick Leave
                            </option>
                        
                            <option value="melahirkan" {{ old('jenis_cuti') == 'melahirkan' ? 'selected' : '' }}>
                                Maternity Leave
                            </option>
                        
                            <option value="penting" {{ old('jenis_cuti') == 'penting' ? 'selected' : '' }}>
                                Personal Leave
                            </option>
                        
                            <option value="ibadah" {{ old('jenis_cuti') == 'ibadah' ? 'selected' : '' }}>
                                Religious Leave
                            </option>
                        
                            <option value="lainnya" {{ old('jenis_cuti') == 'lainnya' ? 'selected' : '' }}>
                                Other Leave
                            </option>
                        </select>
                    </div>

                    <!-- TANGGAL -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Start Date
        <span class="text-red-500">*</span>
    </label>

    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        End Date
        <span class="text-red-500">*</span>
    </label>

                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" required
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>
                    </div>

                    <!-- ALASAN -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Reason
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea name="alasan" rows="5" required placeholder="Write your leave request reason..."
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">{{ old('alasan') }}</textarea>
                    </div>

                    <!-- FILE -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Attachment
                            <span class="text-gray-400 text-xs">(Opsional)</span>
                        </label>

                        <input type="file" name="lampiran"
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        <p class="text-xs text-gray-500 mt-3">
                            Maximum size 5MB • PDF, DOC, DOCX, JPG, JPEG, PNG
                        </p>
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
