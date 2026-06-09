<div id="absence-modal-{{ $type }}" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div class="relative w-full max-w-2xl">
        <!-- MODAL -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-fadeIn">
            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-bold text-blue-900">
                        Detail Attendance
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Complete Attendance Information
                    </p>
                </div>

                <button data-modal-hide="absence-modal-{{ $type }}"
                    class="w-10 h-10 rounded-xl hover:bg-gray-100 flex items-center justify-center transition">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- CONTENT -->
            <div class="max-h-[75vh] overflow-y-auto custom-scrollbar">
                <form method="POST" action="{{ route('absensi.store') }}" enctype="multipart/form-data"
                    class="px-6 md:px-6 py-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-5">
                        <!-- Jenis Absensi -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Attendance Type <span class="text-red-500">*</span>
                            </label>

                            <select name="jenis_absensi" id="jenis_absensi" required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition">
                                <option value="masuk">Present</option>
                                <option value="izin">Leave</option>
                                <option value="sakit">Sick</option>
                            </select>
                        </div>

                        <!-- Regular Fields -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Jam Masuk -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Check-in Time
                                </label>

                                <input type="time" name="jam_masuk" id="jam_masuk" value="{{ date('H:i') }}"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition">
                            </div>

                            <!-- Lokasi -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Location
                                </label>

                                <input type="text" name="lokasi_masuk" id="lokasi_masuk"
                                    placeholder="Masukkan lokasi Anda"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition">
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Description
                            </label>

                            <textarea name="keterangan" rows="4" placeholder="Alasan izin/sakit atau keterangan tambahan..."
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition resize-none">{{ old('keterangan') }}</textarea>
                        </div>

                        <!-- Attachment -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Attachment (Photo / Document)
                            </label>

                            <div
                                class="border-2 border-dashed border-gray-300 rounded-2xl p-5 hover:border-blue-400 transition">
                                <input type="file" name="attachment" accept="image/jpeg,image/png,image/jpg"
                                    class="w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4
                                       file:rounded-lg file:border-0
                                       file:text-sm file:font-medium
                                       file:bg-blue-50 file:text-blue-700
                                       hover:file:bg-blue-100">
                            </div>

                            <p class="text-xs text-gray-500 mt-2">
                                Upload image / photo as proof. Maximum 2 MB (JPG, JPEG, PNG)
                            </p>
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-3 mt-8 border-t pt-5">

                        <button type="button" data-modal-hide="absence-modal-{{ $type }}"
                            class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition">
                            Cancel
                        </button>

                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md transition">
                            Save Attendance
                        </button>

                    </div>
                </form>
            </div>

        </div>

    </div>
</div>
