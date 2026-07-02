@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">

        {{-- HEADER --}}
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">Edit Leave Request</h1>
                <p class="text-gray-700/80 text-sm">Update your leave request data.</p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        {{-- ERROR --}}
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-6" style="border: 2px solid #e0eaff;">
            <form method="POST" action="{{ route('cuti.update', $cuti->id) }}" enctype="multipart/form-data"
                id="editLeaveForm">
                @csrf
                @method('PUT')

                <div class="space-y-6">

                    {{-- LEAVE TYPE --}}
                    @php
                        $originalType = $cuti->jenis_cuti;
                        $originalDays = $cuti->total_hari;
                        $labelMelahirkan = $karyawan->jenis_kelamin === 'P' ? 'Maternity' : 'Paternity';

                        // Effective available = sisa + original days IF same type (will be replaced)
                        // For other types, just sisa
                        $availTahunan   = $sisaTahunan   + ($originalType === 'tahunan'    ? $originalDays : 0);
                        $availMelahirkan= $sisaMelahirkan+ ($originalType === 'melahirkan' ? $originalDays : 0);
                        $availMenikah   = $sisaMenikah   + ($originalType === 'menikah'    ? $originalDays : 0);
                        $availDuka      = $sisaDuka      + ($originalType === 'duka'       ? $originalDays : 0);

                        // Cap at quota
                        $availTahunan    = min($availTahunan,    $kuotaTahunan);
                        $availMelahirkan = min($availMelahirkan, $kuotaMelahirkan);
                        $availMenikah    = min($availMenikah,    $kuotaMenikah);
                        $availDuka       = min($availDuka,       $kuotaDuka);
                    @endphp

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Leave Type <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_cuti" id="jenisCuti" required
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">

                            <option value="tahunan"
                                {{ old('jenis_cuti', $cuti->jenis_cuti) == 'tahunan' ? 'selected' : '' }}
                                {{ $availTahunan <= 0 && $cuti->jenis_cuti !== 'tahunan' ? 'disabled' : '' }}
                                data-quota="{{ $availTahunan }}">
                                Annual Leave — {{ $availTahunan }}/{{ $kuotaTahunan }} days available{{ $availTahunan <= 0 && $cuti->jenis_cuti !== 'tahunan' ? ' (Quota Exhausted)' : '' }}
                            </option>

                            <option value="melahirkan"
                                {{ old('jenis_cuti', $cuti->jenis_cuti) == 'melahirkan' ? 'selected' : '' }}
                                {{ $availMelahirkan <= 0 && $cuti->jenis_cuti !== 'melahirkan' ? 'disabled' : '' }}
                                data-quota="{{ $availMelahirkan }}">
                                {{ $labelMelahirkan }} Leave — {{ $availMelahirkan }}/{{ $kuotaMelahirkan }} days available{{ $availMelahirkan <= 0 && $cuti->jenis_cuti !== 'melahirkan' ? ' (Quota Exhausted)' : '' }}
                            </option>

                            <option value="menikah"
                                {{ old('jenis_cuti', $cuti->jenis_cuti) == 'menikah' ? 'selected' : '' }}
                                {{ $availMenikah <= 0 && $cuti->jenis_cuti !== 'menikah' ? 'disabled' : '' }}
                                data-quota="{{ $availMenikah }}">
                                Marriage Leave — {{ $availMenikah }}/{{ $kuotaMenikah }} days available{{ $availMenikah <= 0 && $cuti->jenis_cuti !== 'menikah' ? ' (Quota Exhausted)' : '' }}
                            </option>

                            <option value="duka"
                                {{ old('jenis_cuti', $cuti->jenis_cuti) == 'duka' ? 'selected' : '' }}
                                {{ $availDuka <= 0 && $cuti->jenis_cuti !== 'duka' ? 'disabled' : '' }}
                                data-quota="{{ $availDuka }}">
                                Bereavement Leave — {{ $availDuka }}/{{ $kuotaDuka }} days available{{ $availDuka <= 0 && $cuti->jenis_cuti !== 'duka' ? ' (Quota Exhausted)' : '' }}
                            </option>
                        </select>
                    </div>

                    {{-- DATES --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Start Date <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="editTanggalMulai" name="tanggal_mulai"
                                value="{{ old('tanggal_mulai', $cuti->tanggal_mulai->format('Y-m-d')) }}"
                                placeholder="Select date" autocomplete="off" required
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white cursor-pointer">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                End Date <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="editTanggalSelesai" name="tanggal_selesai"
                                value="{{ old('tanggal_selesai', $cuti->tanggal_selesai->format('Y-m-d')) }}"
                                placeholder="Select date" autocomplete="off" required
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white cursor-pointer">
                        </div>
                    </div>

                    {{-- DAYS COUNTER --}}
                    <div id="daysInfo" class="hidden px-4 py-3 rounded-xl text-sm border">
                        <span id="daysText"></span>
                    </div>

                    {{-- REASON --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Reason <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alasan" rows="4" required
                            placeholder="Write your leave request reason..."
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none resize-none">{{ old('alasan', $cuti->alasan) }}</textarea>
                    </div>

                    {{-- ATTACHMENT --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Attachment</label>
                        @if ($cuti->lampiran)
                            <div class="mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <a href="{{ Storage::url($cuti->lampiran) }}" target="_blank"
                                    class="text-blue-600 hover:underline text-sm">Current Attachment</a>
                                <span class="text-xs text-gray-400">(upload new file to replace)</span>
                            </div>
                        @endif
                        <input type="file" name="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-gray-400 mt-1">Supported: JPG, PNG, PDF, DOC (Max 5MB). Leave blank to keep current.</p>
                    </div>

                </div>

                {{-- ACTIONS --}}
                <div class="mt-8 flex justify-end gap-3">
                    <a href="{{ route('cuti.index') }}"
                        class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-sm transition">
                        Update Request
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const fpMulai   = flatpickr('#editTanggalMulai',   { dateFormat: 'Y-m-d', allowInput: false });
    const fpSelesai = flatpickr('#editTanggalSelesai', { dateFormat: 'Y-m-d', allowInput: false });

    const selectType = document.getElementById('jenisCuti');
    const daysInfo   = document.getElementById('daysInfo');
    const daysText   = document.getElementById('daysText');
    const submitBtn  = document.getElementById('submitBtn');

    function getMaxDays() {
        const opt = selectType.options[selectType.selectedIndex];
        return parseInt(opt?.dataset?.quota ?? 0, 10);
    }

    function countDays(start, end) {
        if (!start || !end) return 0;
        const a = new Date(start), b = new Date(end);
        if (b < a) return 0;
        return Math.round((b - a) / 86400000) + 1;
    }

    function update() {
        const start = document.getElementById('editTanggalMulai').value;
        const end   = document.getElementById('editTanggalSelesai').value;
        const days  = countDays(start, end);
        const max   = getMaxDays();

        if (!start || !end || days <= 0) {
            daysInfo.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            return;
        }

        daysInfo.classList.remove('hidden');

        if (days > max) {
            daysInfo.className = 'px-4 py-3 rounded-xl text-sm border bg-red-50 border-red-300 text-red-700';
            daysText.textContent = `${days} day(s) selected — exceeds available quota of ${max} day(s). Please shorten the date range.`;
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            daysInfo.className = 'px-4 py-3 rounded-xl text-sm border bg-green-50 border-green-300 text-green-700';
            daysText.textContent = `${days} day(s) selected — ${max - days} day(s) remaining after this request.`;
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }

    // Wire up flatpickr onChange
    fpMulai._input.addEventListener('change', update);
    fpSelesai._input.addEventListener('change', update);
    selectType.addEventListener('change', update);

    // Also sync minDate on start change
    document.getElementById('editTanggalMulai').addEventListener('change', function () {
        if (this.value) fpSelesai.set('minDate', this.value);
    });

    // Init on load
    update();
})();
</script>
@endpush
