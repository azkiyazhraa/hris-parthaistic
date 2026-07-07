<div id="employee-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4">
    <!-- MODAL BOX -->
    <div
        class="bg-white w-full max-w-2xl rounded-xl shadow-xl max-h-[90vh] overflow-y-auto p-5 md:p-8 transition-all duration-300">

        <!-- HEADER -->
        <div class="flex items-center justify-between mb-4 md:mb-6">
            <h2 class="text-lg font-semibold text-blue-900 md:text-2xl">Create employee</h2>
            <button data-modal-hide="employee-modal" class="text-xl text-gray-400 hover:text-gray-600">&times;</button>
        </div>

        <!-- FORM -->
        <form method="POST" action="{{ route('admin.karyawan.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="px-4 py-3 border border-red-200 rounded-lg bg-red-50">
                    <p class="mb-1 text-sm font-semibold text-red-700">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- DATA UTAMA --}}
            <div>
                <h3 class="pb-2 mb-3 text-sm font-semibold text-gray-700 border-b">Main Data</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">NIK <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nik" value="{{ old('nik') }}" required
                            class="w-full px-3 py-2 border rounded-lg number-only focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="hidden mt-1 text-xs text-red-600 input-error">
                            Field must contain numbers only.
                        </p>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">First Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_depan" required value="{{ old('nama_depan') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Last Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_belakang" required value="{{ old('nama_belakang') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" required value="{{ old('email') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="kata_sandi" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Profile Photo</label>
                        <input type="file" name="foto_profil" accept="image/*"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            {{-- PEKERJAAN --}}
            <div>
                <h3 class="pb-2 mb-3 text-sm font-semibold text-gray-700 border-b">Job Information</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Role <span
                                class="text-red-500">*</span></label>
                        <select name="role" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="karyawan" {{ old('role') == 'karyawan' ? 'selected' : '' }}>Employee</option>
                            <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Position <span
                                class="text-red-500">*</span></label>
                        <select name="jabatan" id="jabatanSelect" onchange="toggleJabatanLainnya()" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Select Position</option>
                            <option value="Chief Executive Officer"
                                {{ old('jabatan') == 'Chief Executive Officer' ? 'selected' : '' }}>Chief Executive
                                Officer</option>
                            <option value="Chief Operating Officer"
                                {{ old('jabatan') == 'Chief Operating Officer' ? 'selected' : '' }}>Chief Operating
                                Officer</option>
                            <option value="Creative Writer"
                                {{ old('jabatan') == 'Creative Writer' ? 'selected' : '' }}>Creative Writer</option>
                            <option value="Finance" {{ old('jabatan') == 'Finance' ? 'selected' : '' }}>Finance
                            </option>
                            <option value="Business Development"
                                {{ old('jabatan') == 'Business Development' ? 'selected' : '' }}>Business Development
                            </option>
                            <option value="Videographer" {{ old('jabatan') == 'Videographer' ? 'selected' : '' }}>
                                Videographer</option>
                            <option value="Video Editor" {{ old('jabatan') == 'Video Editor' ? 'selected' : '' }}>Video
                                Editor</option>
                            <option value="Social Media Manager"
                                {{ old('jabatan') == 'Social Media Manager' ? 'selected' : '' }}>Social Media Manager
                            </option>
                            <option value="lainnya" {{ old('jabatan') == 'lainnya' ? 'selected' : '' }}>Lainnya
                            </option>
                        </select>
                    </div>

                    <div id="jabatanLainnyaField"
                        style="display:{{ old('jabatan') == 'lainnya' ? 'block' : 'none' }};">
                        <label class="block mb-1 text-sm font-medium text-gray-700">Custom Position <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="jabatan_lainnya" value="{{ old('jabatan_lainnya') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Enter custom position">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" id="statusSelect" onchange="toggleEndDate()" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Select Status</option>
                            <optgroup label="Active">
                                <option value="Full-time" {{ old('status') == 'Full-time' ? 'selected' : '' }}>
                                    Full-time</option>
                                <option value="Contract" {{ old('status') == 'Contract' ? 'selected' : '' }}>Contract
                                </option>
                                <option value="Internship" {{ old('status') == 'Internship' ? 'selected' : '' }}>
                                    Internship</option>
                            </optgroup>
                            <optgroup label="Inactive">
                                <option value="Resigned" {{ old('status') == 'Resigned' ? 'selected' : '' }}>Resigned
                                </option>
                                <option value="Contract Ended"
                                    {{ old('status') == 'Contract Ended' ? 'selected' : '' }}>Contract Ended</option>
                                <option value="Internship Completed"
                                    {{ old('status') == 'Internship Completed' ? 'selected' : '' }}>Internship
                                    Completed</option>
                                <option value="Terminated" {{ old('status') == 'Terminated' ? 'selected' : '' }}>
                                    Terminated</option>
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Join Date <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_bergabung" id="tanggal_bergabung" required
                            value="{{ old('tanggal_bergabung') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div id="endDateField"
                        style="display:{{ in_array(old('status'), ['Resigned', 'Contract Ended', 'Internship Completed', 'Terminated']) ? 'block' : 'none' }};">
                        <label class="block mb-1 text-sm font-medium text-gray-700">End Date <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div id="reasonResignedField"
                        style="display:{{ in_array(old('status'), ['Resigned', 'Contract Ended', 'Internship Completed', 'Terminated']) ? 'block' : 'none' }};">
                        <label class="block mb-1 text-sm font-medium text-gray-700">Reason</label>
                        <textarea name="reason_resigned" rows="2"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Reason for leaving...">{{ old('reason_resigned') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- BANK INFO --}}
            <div>
                <h3 class="pb-2 mb-3 text-sm font-semibold text-gray-700 border-b">Bank Information</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Bank Name</label>
                        <input type="text" name="nama_bank" value="BSI" readonly
                            class="w-full px-3 py-2 text-gray-700 bg-gray-100 border rounded-lg cursor-not-allowed focus:outline-none">
                        <p class="mt-1 text-xs text-gray-500">Default bank: BSI (cannot be changed)</p>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Account Number <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening') }}" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none number-only">
                        <p class="hidden mt-1 text-xs text-red-600 input-error">
                            Field must contain numbers only.
                        </p>
                    </div>
                </div>
            </div>

            {{-- KONTAK --}}
            <div>
                <h3 class="pb-2 mb-3 text-sm font-semibold text-gray-700 border-b">Contact</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Phone Number <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon') }}" required
                            placeholder="08xxxxxxxxxx"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none number-only">
                        <p class="hidden mt-1 text-xs text-red-600 input-error">
                            Field must contain numbers only.
                        </p>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">NPWP</label>
                        <input type="text" name="npwp" value="{{ old('npwp') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none number-only">
                        <p class="hidden mt-1 text-xs text-red-600 input-error">
                            Field must contain numbers only.
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="hasPassport" class="text-blue-600 rounded">
                            <span class="text-sm font-medium text-gray-700">
                                Employee has a passport
                            </span>
                        </label>
                    </div>

                    <div id="passportSection" class="hidden md:col-span-2">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Passport Number <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="passportNumber" name="nomor_paspor"
                                    value="{{ old('nomor_paspor') }}"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                    Passport Validity Period <span class="text-red-500">*</span>
                                </label>

                                <input type="date" id="passportExpiry" name="paspor_berlaku_hingga"
                                    value="{{ old('paspor_berlaku_hingga') }}"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                    {{-- <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Passport Number</label>
                        <input type="text" name="nomor_paspor" value="{{ old('nomor_paspor') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Passport Validity Period</label>
                        <input type="date" name="paspor_berlaku_hingga"
                            value="{{ old('paspor_berlaku_hingga') }}"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div> --}}
                    <div class="md:col-span-2">
                        <label class="block mb-1 text-sm font-medium text-gray-700">Address <span
                                class="text-red-500">*</span></label>
                        <textarea name="alamat" rows="3" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('alamat') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- DATA PRIBADI --}}
            <div>
                <h3 class="pb-2 mb-3 text-sm font-semibold text-gray-700 border-b">Personal Data</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Place of Birth <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Date of Birth <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Gender <span
                                class="text-red-500">*</span></label>
                        <select name="jenis_kelamin" required class="w-full px-3 py-2 border rounded-lg">
                            <option value="">Select</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Male</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Religion <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="agama" value="{{ old('agama') }}" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Marital Status <span
                                class="text-red-500">*</span></label>
                        <select name="status_pernikahan" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Select Marital Status</option>
                            <option value="Single" {{ old('status_pernikahan') === 'Single' ? 'selected' : '' }}>
                                Single</option>
                            <option value="Married" {{ old('status_pernikahan') === 'Married' ? 'selected' : '' }}>
                                Married</option>
                            <option value="Divorced" {{ old('status_pernikahan') === 'Divorced' ? 'selected' : '' }}>
                                Divorced</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- PENDIDIKAN --}}
            <div>
                <h3 class="pb-2 mb-3 text-sm font-semibold text-gray-700 border-b">Education</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Last Education <span
                                class="text-red-500">*</span></label>
                        <select name="pendidikan_terakhir"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required>
                            <option value="">Select Education</option>
                            <option value="SMP" {{ old('pendidikan_terakhir') == 'SMP' ? 'selected' : '' }}>SMP
                            </option>
                            <option value="SMA/MA" {{ old('pendidikan_terakhir') == 'SMA/MA' ? 'selected' : '' }}>
                                SMA/MA</option>
                            <option value="SMK" {{ old('pendidikan_terakhir') == 'SMK' ? 'selected' : '' }}>SMK
                            </option>
                            <option value="D1" {{ old('pendidikan_terakhir') == 'D1' ? 'selected' : '' }}>D1
                            </option>
                            <option value="D2" {{ old('pendidikan_terakhir') == 'D2' ? 'selected' : '' }}>D2
                            </option>
                            <option value="D3" {{ old('pendidikan_terakhir') == 'D3' ? 'selected' : '' }}>D3
                            </option>
                            <option value="D4" {{ old('pendidikan_terakhir') == 'D4' ? 'selected' : '' }}>D4
                            </option>
                            <option value="S1" {{ old('pendidikan_terakhir') == 'S1' ? 'selected' : '' }}>S1
                            </option>
                            <option value="S2" {{ old('pendidikan_terakhir') == 'S2' ? 'selected' : '' }}>S2
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">University/School <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="universitas" value="{{ old('universitas') }}" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Major <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="jurusan" value="{{ old('jurusan') }}" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Graduation Year <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="tahun_lulus" value="{{ old('tahun_lulus') }}" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            min="1900" max="2099" step="1">
                    </div>
                </div>
            </div>

            {{-- KONTAK DARURAT --}}
            <div>
                <h3 class="pb-2 mb-3 text-sm font-semibold text-gray-700 border-b">Emergency Contact</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Emergency Contact Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_kontak_darurat" value="{{ old('nama_kontak_darurat') }}"
                            required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">Emergency Contact Phone <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="telepon_kontak_darurat"
                            value="{{ old('telepon_kontak_darurat') }}" required
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            {{-- INTEGRASI --}}
            <div>
                <h3 class="flex items-center gap-2 pb-2 mb-3 text-sm font-semibold text-gray-700 border-b">
                    Integrations
                    <span class="text-xs font-normal text-gray-400">(optional)</span>
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#0052CC]" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M21 0H3C1.343 0 0 1.343 0 3v18c0 1.656 1.343 3 3 3h18c1.656 0 3-1.344 3-3V3c0-1.657-1.344-3-3-3zM10.44 18.18c0 .795-.645 1.44-1.44 1.44H4.56c-.795 0-1.44-.645-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44H9c.795 0 1.44.645 1.44 1.44v12.36zm10.44-7.08c0 .794-.645 1.44-1.44 1.44H15c-.795 0-1.44-.646-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44h4.44c.795 0 1.44.645 1.44 1.44v5.28z" />
                            </svg>
                            Tracker / Trello Email
                        </label>
                        <input type="email" name="tracker_email" value="{{ old('tracker_email') }}"
                            placeholder="Email used in Dashboard Tracker"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="mt-1 text-xs text-gray-400">
                            Make sure to enter Trello email correctly, as it can only be entered once and cannot be
                            edited.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" data-modal-hide="employee-modal"
                    class="px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-50">Cancel</button>
                <button id="submitBtn" type="submit"
                    class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const hasPassport = document.getElementById('hasPassport');
    const passportSection = document.getElementById('passportSection');
    const passportNumber = document.getElementById('passportNumber');
    const passportExpiry = document.getElementById('passportExpiry');

    function togglePassport() {
        if (hasPassport.checked) {
            passportSection.classList.remove('hidden');

            passportNumber.required = true;
            passportExpiry.required = true;
        } else {
            passportSection.classList.add('hidden');

            passportNumber.required = false;
            passportExpiry.required = false;

            passportNumber.value = '';
            passportExpiry.value = '';
        }
    }

    hasPassport.addEventListener('change', togglePassport);

    togglePassport();

    function toggleJabatanLainnya() {
        const select = document.getElementById('jabatanSelect');
        const lainnyaField = document.getElementById('jabatanLainnyaField');
        if (select.value === 'lainnya') {
            lainnyaField.style.display = 'block';
        } else {
            lainnyaField.style.display = 'none';
        }
    }

    function toggleEndDate() {
        const statusSelect = document.getElementById('statusSelect');
        const endDateField = document.getElementById('endDateField');
        const reasonField = document.getElementById('reasonResignedField');
        const inactiveStatuses = ['Resigned', 'Contract Ended', 'Internship Completed', 'Terminated'];

        if (inactiveStatuses.includes(statusSelect.value)) {
            endDateField.style.display = 'block';
            reasonField.style.display = 'block';
        } else {
            endDateField.style.display = 'none';
            reasonField.style.display = 'none';
            document.getElementById('end_date').value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleJabatanLainnya();
        toggleEndDate();

        @if ($errors->any())
            // Auto-open modal when there are validation errors
            const modal = document.getElementById('employee-modal');
            if (modal) modal.classList.remove('hidden');
        @endif
    });
</script>
