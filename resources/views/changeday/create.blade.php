<!-- REQUEST CHANGE DAY MODAL -->
<div id="requestChangeDayModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div class="relative w-full max-w-2xl">
        <!-- MODAL CONTENT -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">
                        Request Change Day
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Submit a request to change your working schedule
                    </p>
                </div>

                <button data-modal-hide="requestChangeDayModal"
                    class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- BODY -->
            <div class="p-6 overflow-y-auto max-h-[400px]">
                <form action="{{ route('changeday.request') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- ORIGINAL DATE -->
                        <div>
                            <label for="originalDate" class="block text-sm font-medium text-gray-700 mb-2">
                                Original Date
                            </label>

                            <input type="date" id="originalDate" name="original_date"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- ORIGINAL START TIME -->
                        <div>
                            <label for="originalStartTime" class="block text-sm font-medium text-gray-700 mb-2">
                                Original Start Time
                            </label>

                            <input type="time" id="originalStartTime" name="original_start_time"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- ORIGINAL END TIME -->
                        <div>
                            <label for="originalEndTime" class="block text-sm font-medium text-gray-700 mb-2">
                                Original End Time
                            </label>

                            <input type="time" id="originalEndTime" name="original_end_time"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-5">
                        <!-- REQUESTED DATE -->
                        <div>
                            <label for="requestedDate" class="block text-sm font-medium text-gray-700 mb-2">
                                Requested Date
                            </label>

                            <input type="date" id="requestedDate" name="requested_date"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- REQUESTED START TIME -->
                        <div>
                            <label for="requestedStartTime" class="block text-sm font-medium text-gray-700 mb-2">
                                Requested Start Time
                            </label>

                            <input type="time" id="requestedStartTime" name="requested_start_time"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- REQUESTED END TIME -->
                        <div>
                            <label for="requestedEndTime" class="block text-sm font-medium text-gray-700 mb-2">
                                Requested End Time
                            </label>

                            <input type="time" id="requestedEndTime" name="requested_end_time"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- REASON -->
                    <div class="mt-5">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason
                        </label>

                        <textarea id="reason" name="reason" rows="4" placeholder="Explain the reason for requesting a change day..."
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm shadow-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <!-- FILE -->
                    <div class="mt-5">
                        <label for="fileAttachment" class="block text-sm font-medium text-gray-700 mb-2">
                            File Attachment
                        </label>

                        <input type="file" id="fileAttachment" name="attachment"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100">

                        <p class="text-xs text-gray-500 mt-2">
                            Supported formats: JPG, PNG, PDF (Max 5MB)
                        </p>
                    </div>

                    <!-- FOOTER -->
                    <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                        <!-- CLOSE BUTTON -->
                        <button type="button" data-modal-hide="requestChangeDayModal"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                            Close
                        </button>

                        <!-- SUBMIT BUTTON -->
                        <button type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
