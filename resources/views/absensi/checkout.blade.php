<div id="absence-modal-checkout-{{ $type }}-{{ optional($absensiToday)->id }}" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div class="relative w-full max-w-2xl">
        <!-- MODAL -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-fadeIn">
            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-bold text-blue-900">
                        Close attendance detail 
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Complete close attendence information for today
                    </p>
                </div>

                <button data-modal-hide="absence-modal-checkout-{{ $type }}-{{ optional($absensiToday)->id }}"
                    class="w-10 h-10 rounded-xl hover:bg-gray-100 flex items-center justify-center transition">
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

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Jam Pulang</label>
                            <input type="time" name="jam_pulang" id="jam_pulang" required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition"
                                value="{{ date('H:i') }}">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi Pulang</label>
                            <input type="text" name="lokasi_pulang" id="lokasi_pulang" required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-gray-700 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition"
                                placeholder="Masukkan lokasi pulang">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-8 border-t pt-5">
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
