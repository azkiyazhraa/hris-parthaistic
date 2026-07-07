<div id="absence-modal-{{ $type }}" tabindex="-1" aria-hidden="true"
    class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">

    <div class="relative w-full max-w-2xl">
        <div class="overflow-hidden bg-white shadow-2xl rounded-3xl">

            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-bold text-blue-900">Detail Attendance</h3>
                    <p class="mt-1 text-sm text-gray-500">Complete Attendance Information</p>
                </div>
                <button data-modal-hide="absence-modal-{{ $type }}"
                    class="flex items-center justify-center w-10 h-10 transition rounded-xl hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- CONTENT -->
            <div class="max-h-[75vh] overflow-y-auto">
                <form method="POST" action="{{ route('absensi.store') }}" enctype="multipart/form-data"
                    class="px-6 py-6 space-y-5">
                    @csrf

                    <!-- Attendance Type -->
                    <input type="hidden" name="jenis_absensi" value="checkin">

                    <!-- Check-in Time & Location -->
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 attendance-fields">
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-700">
                                Check-in Time
                            </label>
                            <input type="time" name="jam_masuk" id="jam_masuk" value="{{ date('H:i') }}"
                                class="w-full px-4 py-3 text-gray-700 transition border border-gray-300 shadow-sm cursor-not-allowed rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none"
                                readonly>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-semibold text-gray-700">
                                Location
                            </label>
                            <input type="text" name="lokasi_masuk" id="lokasi_masuk"
                                placeholder="Enter your location"
                                class="w-full px-4 py-3 text-gray-700 transition border border-gray-300 shadow-sm rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
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
