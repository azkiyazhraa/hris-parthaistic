<!-- REQUEST CHANGE DAY MODAL -->
<div id="requestChangeDayModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div class="relative w-full max-w-2xl">
        <!-- MODAL CONTENT -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <!-- HEADER -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">Request Change Day</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Swap a regular work day off for working on a Sunday or national holiday.
                    </p>
                </div>

                <button data-modal-hide="requestChangeDayModal"
                    class="w-9 h-9 flex-shrink-0 flex items-center justify-center rounded-full hover:bg-gray-100 transition ml-4">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- BODY -->
            <div class="p-6 overflow-y-auto max-h-[500px]">
                <form action="{{ route('changeday.request') }}" method="POST" enctype="multipart/form-data" id="changeDayForm">
                    @csrf

                    <!-- HOLIDAY LOADING NOTICE -->
                    <div id="holidayLoadingNotice" class="flex items-center gap-2 mb-4 text-xs text-gray-400">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        Loading national holiday data...
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- ORIGINAL DATE -->
                        <div>
                            <label for="originalDate" class="block text-sm font-medium text-gray-700 mb-1">
                                Original Date <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-400 mb-2">The regular work day (Mon–Sat) you want to take off</p>

                            <input type="text" id="originalDate" name="original_date"
                                placeholder="Select date" autocomplete="off"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer">

                            <p id="originalDateError" class="mt-1.5 text-xs text-red-500 hidden"></p>
                        </div>

                        <!-- REQUESTED DATE -->
                        <div>
                            <label for="requestedDate" class="block text-sm font-medium text-gray-700 mb-1">
                                Requested Date <span class="text-red-500">*</span>
                            </label>
                            <p class="text-xs text-gray-400 mb-2">The Sunday or national holiday you'll work instead, within 1 week</p>

                            <input type="text" id="requestedDate" name="requested_date"
                                placeholder="Select original date first" autocomplete="off"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 text-gray-400 cursor-not-allowed">

                            <p id="requestedDateError" class="mt-1.5 text-xs text-red-500 hidden"></p>
                            <p id="requestedDateLabel" class="mt-1.5 text-xs text-blue-600 hidden"></p>
                        </div>

                    </div>

                    <!-- REASON -->
                    <div class="mt-5">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason <span class="text-red-500">*</span>
                        </label>

                        <textarea id="reason" name="reason" rows="4" required
                            placeholder="Explain the reason for requesting a change day..."
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm shadow-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        <p id="reasonError" class="mt-1.5 text-xs text-red-500 hidden"></p>
                    </div>

                    <!-- FILE -->
                    <div class="mt-5">
                        <label for="fileAttachment" class="block text-sm font-medium text-gray-700 mb-2">
                            File Attachment
                        </label>

                        <input type="file" id="fileAttachment" name="attachment"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100">

                        <p class="mt-2 text-xs text-gray-500">Supported formats: JPG, PNG, PDF (Max 5MB)</p>
                    </div>

                    <!-- FOOTER -->
                    <div class="flex flex-col-reverse gap-3 mt-8 sm:flex-row sm:justify-end">
                        <button type="button" data-modal-hide="requestChangeDayModal"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                            Close
                        </button>

                        <button type="submit" id="changeDaySubmitBtn"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition">
                            Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    // ── Holiday store ──────────────────────────────────────────────────────────
    const HOLIDAY_MAP = {};

    async function fetchYear(year) {
        const res  = await fetch(`https://libur.deno.dev/api?year=${year}`);
        const data = await res.json();
        if (Array.isArray(data)) {
            data.forEach(item => {
                if (item.date) HOLIDAY_MAP[item.date.substring(0, 10)] = item.name || 'National Holiday';
            });
        }
    }

    async function loadHolidays() {
        const year = new Date().getFullYear();
        try {
            await Promise.all([fetchYear(year), fetchYear(year + 1)]);
        } catch (e) {
            console.warn('Holiday API unavailable — Sunday validation only:', e);
        } finally {
            const notice = document.getElementById('holidayLoadingNotice');
            if (notice) notice.remove();
        }
        // Refresh both pickers: re-color days and re-evaluate disable rules
        if (fpOrig) { fpOrig.set('disable', origDisableFn); }
        if (fpReq)  { fpReq.set('disable', reqDisableFn); }
    }

    // ── Date helpers ───────────────────────────────────────────────────────────
    function parseLocal(str) {
        const [y, m, d] = str.split('-').map(Number);
        return new Date(y, m - 1, d);
    }

    function toYMD(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function addDays(str, days) {
        const d = parseLocal(str);
        d.setDate(d.getDate() + days);
        return toYMD(d);
    }

    function isSunday(str)         { return parseLocal(str).getDay() === 0; }
    function isHoliday(str)        { return Object.prototype.hasOwnProperty.call(HOLIDAY_MAP, str); }
    function isRegularWorkday(str) { return !isSunday(str) && !isHoliday(str); }

    // ── Flatpickr disable rules ────────────────────────────────────────────────
    // Original Date: block Sundays & national holidays
    const origDisableFn = [function(date) {
        const str = toYMD(date);
        return isSunday(str) || isHoliday(str);
    }];
    // Requested Date: block regular workdays (only allow Sundays & holidays)
    const reqDisableFn = [function(date) {
        const str = toYMD(date);
        return !isSunday(str) && !isHoliday(str);
    }];

    // ── DOM refs ───────────────────────────────────────────────────────────────
    const origInput   = document.getElementById('originalDate');
    const reqInput    = document.getElementById('requestedDate');
    const origError   = document.getElementById('originalDateError');
    const reqError    = document.getElementById('requestedDateError');
    const reqLabel    = document.getElementById('requestedDateLabel');
    const reasonInput = document.getElementById('reason');
    const reasonError = document.getElementById('reasonError');

    function showError(el, msg) { el.textContent = msg; el.classList.remove('hidden'); }
    function clearMsg(el)       { el.textContent = ''; el.classList.add('hidden'); }

    // ── Flatpickr: onDayCreate for coloring ───────────────────────────────────
    function onDayCreate(dObj, dStr, fp, dayElem) {
        const d = dayElem.dateObj;
        if (!d) return;
        if (d.getDay() === 0) dayElem.classList.add('fp-sunday');
        const key = toYMD(d);
        if (HOLIDAY_MAP[key]) {
            dayElem.classList.add('fp-holiday');
            dayElem.title = HOLIDAY_MAP[key];
        }
    }

    // ── Flatpickr instances ────────────────────────────────────────────────────
    let fpOrig = flatpickr('#originalDate', {
        dateFormat: 'Y-m-d',
        allowInput: false,
        disable: origDisableFn,
        onDayCreate,
        onChange: function (selectedDates, dateStr) {
            clearMsg(origError);
            resetRequestedDate();
            if (!dateStr) return;

            // Unlock requested date within ±7 days
            fpReq.set('minDate', addDays(dateStr, -7));
            fpReq.set('maxDate', addDays(dateStr, 7));
            fpReq.set('clickOpens', true);
            reqInput.classList.remove('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
            reqInput.classList.add('bg-white', 'text-gray-800', 'cursor-pointer');
            reqInput.placeholder = 'Select date';
        },
    });

    function resetRequestedDate() {
        fpReq.clear();
        fpReq.set('minDate', null);
        fpReq.set('maxDate', null);
        fpReq.set('clickOpens', false);
        reqInput.classList.add('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
        reqInput.classList.remove('bg-white', 'text-gray-800', 'cursor-pointer');
        reqInput.placeholder = 'Select original date first';
        clearMsg(reqError);
        clearMsg(reqLabel);
    }

    let fpReq = flatpickr('#requestedDate', {
        dateFormat: 'Y-m-d',
        allowInput: false,
        clickOpens: false,
        disable: reqDisableFn,
        onDayCreate,
        onChange: function (selectedDates, dateStr) {
            clearMsg(reqError);
            clearMsg(reqLabel);
            if (!dateStr) return;

            const origVal = origInput.value;
            if (origVal) {
                const diffDays = Math.abs((parseLocal(dateStr) - parseLocal(origVal)) / 86400000);
                if (diffDays > 7) {
                    showError(reqError, 'Requested date must be within 1 week of the original date.');
                    fpReq.clear();
                    return;
                }
            }

            reqLabel.textContent = isHoliday(dateStr) ? '🗓 ' + HOLIDAY_MAP[dateStr] : '📅 Sunday';
            reqLabel.classList.remove('hidden');
        },
    });

    // ── Submit guard ───────────────────────────────────────────────────────────
    document.getElementById('changeDayForm').addEventListener('submit', function (e) {
        let valid = true;

        if (!origInput.value || !isRegularWorkday(origInput.value)) {
            showError(origError, 'Original date must be a regular work day (Mon–Sat, not a holiday).');
            valid = false;
        }

        if (!reqInput.value) {
            showError(reqError, 'Please select a requested date.');
            valid = false;
        } else if (!isSunday(reqInput.value) && !isHoliday(reqInput.value)) {
            showError(reqError, 'Requested date must be a Sunday or national holiday.');
            valid = false;
        } else if (origInput.value) {
            const diffDays = Math.abs((parseLocal(reqInput.value) - parseLocal(origInput.value)) / 86400000);
            if (diffDays > 7) {
                showError(reqError, 'Requested date must be within 1 week of the original date.');
                valid = false;
            }
        }

        if (!reasonInput.value.trim()) {
            showError(reasonError, 'Reason is required.');
            valid = false;
        } else {
            clearMsg(reasonError);
        }

        if (!valid) e.preventDefault();
    });

    // ── Boot ──────────────────────────────────────────────────────────────────
    loadHolidays();
})();
</script>
@endpush
