<!-- Main modal -->
<div id="detailModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex justify-center items-center bg-black/40">

    <div class="relative w-full max-w-xl p-4">

        <!-- MODAL CARD -->
        <div class="bg-white rounded-3xl shadow-lg p-6">

            <!-- HEADER -->
            <div class="flex justify-between items-center border-b pb-4">
                <h3 class="text-lg font-semibold text-blue-900">
                    Detail Attendance
                </h3>

                <button onclick="closeDetailModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100">
                    ✕
                </button>
            </div>

            <!-- CONTENT -->
            <div id="detailContent" class="mt-5 space-y-5">
                <!-- Content will be filled by JavaScript -->
            </div>

        </div>
    </div>
</div>