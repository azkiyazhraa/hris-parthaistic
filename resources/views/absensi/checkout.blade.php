<div id="absence-modal-checkout-{{ $type }}-{{ optional($absensiToday)->id }}" tabindex="-1" aria-hidden="true"
    class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">

    <div class="relative w-full max-w-2xl">
        <!-- MODAL -->
        <div class="overflow-hidden bg-white shadow-2xl rounded-3xl animate-fadeIn">
            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-bold text-blue-900">
                        Close attendance detail
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Complete close attendence information for today
                    </p>
                </div>

                <button data-modal-hide="absence-modal-checkout-{{ $type }}-{{ optional($absensiToday)->id }}"
                    class="flex items-center justify-center w-10 h-10 transition rounded-xl hover:bg-gray-100">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- CONTENT -->
            <div class="max-h-[75vh] overflow-y-auto custom-scrollbar p-6">
                <form method="POST" action="{{ route('absensi.pulang', optional($absensiToday)->id) }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Check-out Location <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="lokasi_pulang" id="lokasi_pulang" required
                            class="w-full px-4 py-3 text-gray-700 transition bg-white border border-gray-300 shadow-sm rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none"
                            placeholder="Enter check-out location">
                    </div>

                    <div class="mb-4">
                        <label class="block mb-2 text-sm font-bold text-gray-700">Explain what you did today</label>
                        <textarea name="keterangan" rows="3"
                            class="w-full px-4 py-3 text-sm text-gray-700 transition bg-white border border-gray-300 shadow-sm resize-none rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none"
                            placeholder="Add notes about today's work..." required></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-5 mt-8 border-t">
                        <button type="button"
                            data-modal-hide="absence-modal-checkout-{{ $type }}-{{ optional($absensiToday)->id }}"
                            class="px-5 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition">
                            Cancel
                        </button>

                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-md transition">
                            Save attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
