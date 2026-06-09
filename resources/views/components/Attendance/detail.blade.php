<div id="default-modal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex justify-center items-center bg-black/40">

    <div class="relative w-full max-w-xl p-4">

        <!-- MODAL CARD -->
        <div class="bg-white rounded-3xl shadow-lg p-6">

            <!-- HEADER -->
            <div class="flex justify-between items-center border-b pb-4">
                <h3 class="text-lg font-semibold text-blue-900">
                    Detail Attendance
                </h3>

                <button data-modal-hide="default-modal"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">
                    ✕
                </button>
            </div>

            <!-- CONTENT -->
            <div class="mt-5 space-y-5">

                <!-- PROFILE -->
                <div class="flex items-center gap-4">
                    <img src="https://randomuser.me/api/portraits/women/2.jpg"
                        class="w-16 h-16 rounded-full border-4 border-blue-900 p-1">

                    <div>
                        <h2 class="text-md font-semibold text-blue-900">
                            Olivia Watson
                        </h2>
                        <p class="text-sm text-gray-500">Human Resource</p>
                        <p class="text-xs text-gray-400">oliviawatson@bookmail.com</p>
                        <p class="text-xs text-gray-400">+62 812 3000 4321</p>
                    </div>
                </div>

                <!-- INFO GRID -->
                <div class="grid grid-cols-4 gap-4 text-sm">

                    <div>
                        <p class="text-gray-400">Check In</p>
                        <p class="font-medium text-gray-700">08.00 WIB</p>
                    </div>

                    <div>
                        <p class="text-gray-400">Check Out</p>
                        <p class="font-medium text-gray-700">17.00 WIB</p>
                    </div>

                    <div>
                        <p class="text-gray-400">Date</p>
                        <p class="font-medium text-gray-700">28 Feb 2026</p>
                    </div>

                    <div>
                        <p class="text-gray-400">Attachment</p>
                        <a href="#" class="text-blue-600 text-sm hover:underline">
                            Photo.jpg
                        </a>
                    </div>

                </div>

                <!-- NOTES -->
                <div>
                    <p class="text-sm text-gray-400 mb-1">Notes</p>
                    <div class="bg-gray-100 text-gray-500 text-sm rounded-lg px-3 py-2">
                        Progress is on track as planned.
                    </div>
                </div>

                <!-- STATUS -->
                <div>
                    <p class="text-sm text-gray-400 mb-1">Status</p>
                    <span class="bg-green-500 text-white text-xs px-3 py-1 rounded">
                        On Time
                    </span>
                </div>

            </div>

        </div>
    </div>
</div>
