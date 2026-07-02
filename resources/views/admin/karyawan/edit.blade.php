<div id="employee-modal-edit-{{ $item->id }}" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- MODAL BOX -->
    <div
        class="bg-white w-full max-w-2xl rounded-xl shadow-xl max-h-[90vh] overflow-y-auto p-5 md:p-8 transition-all duration-300">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <h2 class="text-lg md:text-2xl font-semibold text-blue-900">Edit employee</h2>
            <button data-modal-hide="employee-modal-edit-{{ $item->id }}"
                class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.karyawan.update', $item->id) }}" enctype="multipart/form-data"
            class="space-y-6">
            @csrf
            @method('PUT')

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3">
                    <p class="text-sm font-semibold text-red-700 mb-1">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li class="text-sm text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- DATA UTAMA --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Main Data</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                        <input type="text" value="{{ $item->nip }}" readonly
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK <span class="text-red-500">*</span></label>
                        <input type="text" name="nik" value="{{ old('nik', $item->nik) }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span
                                class="text-red-500">*</span></label>
                        @php
                            $firstName = old('nama_depan', explode(' ', $item->nama_lengkap)[0] ?? $item->nama_lengkap);
                        @endphp
                        <input type="text" name="nama_depan" required value="{{ $firstName }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span
                                class="text-red-500">*</span></label>
                        @php
                            $namaParts = explode(' ', $item->nama_lengkap);
                            $lastName = old('nama_belakang', count($namaParts) > 1 ? implode(' ', array_slice($namaParts, 1)) : '');
                        @endphp
                        <input type="text" name="nama_belakang" required value="{{ $lastName }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" required value="{{ old('email', $item->email) }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="kata_sandi"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-xs text-gray-500 mt-1">Leave blank if you don't want to change the password.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                        <input type="file" name="foto_profil" accept="image/*"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @if ($item->foto_profil)
                            <p class="text-xs text-gray-500 mt-1">Current photo: {{ basename($item->foto_profil) }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- PEKERJAAN --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Job Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role <span
                                class="text-red-500">*</span></label>
                        <select name="role" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="karyawan" {{ old('role', $item->role) === 'karyawan' ? 'selected' : '' }}>Employee</option>
                            <option value="hr" {{ old('role', $item->role) === 'hr' ? 'selected' : '' }}>HR</option>
                            <option value="admin" {{ old('role', $item->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Position <span class="text-red-500">*</span></label>
                        <select name="jabatan" id="jabatanSelectEdit{{ $item->id }}"
                            onchange="toggleJabatanLainnyaEdit({{ $item->id }})" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Select Position</option>
                            @php
                                $jabatanOptions = [
                                    'Chief Executive Officer',
                                    'Chief Operating Officer',
                                    'Creative Writer',
                                    'Finance',
                                    'Business Development',
                                    'Videographer',
                                    'Video Editor',
                                    'Social Media Manager',
                                    'lainnya',
                                ];
                                $oldJabatan = old('jabatan', $item->jabatan);
                            @endphp
                            @foreach ($jabatanOptions as $opt)
                                <option value="{{ $opt }}" {{ $oldJabatan === $opt ? 'selected' : '' }}>
                                    {{ $opt === 'lainnya' ? 'Lainnya' : $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="jabatanLainnyaFieldEdit{{ $item->id }}"
                        style="display:{{ old('jabatan', $item->jabatan) === 'lainnya' ? 'block' : 'none' }};">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Custom Position</label>
                        <input type="text" name="jabatan_lainnya"
                            value="{{ old('jabatan_lainnya', $item->jabatan_lainnya) }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Enter custom position">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" id="statusSelectEdit{{ $item->id }}"
                            onchange="toggleEndDateEdit({{ $item->id }})" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <optgroup label="Active (Can Login)">
                                <option value="Full-time" {{ old('status', $item->status) === 'Full-time' ? 'selected' : '' }}>
                                    ✅ Full-time</option>
                                <option value="Contract" {{ old('status', $item->status) === 'Contract' ? 'selected' : '' }}>
                                    ✅ Contract</option>
                                <option value="Internship" {{ old('status', $item->status) === 'Internship' ? 'selected' : '' }}>
                                    ✅ Internship</option>
                            </optgroup>
                            <optgroup label="Suspended (Cannot Login)">
                                <option value="Resigned" {{ old('status', $item->status) === 'Resigned' ? 'selected' : '' }}>
                                    🔒 Resigned</option>
                                <option value="Contract Ended" {{ old('status', $item->status) === 'Contract Ended' ? 'selected' : '' }}>
                                    🔒 Contract Ended</option>
                                <option value="Internship Completed" {{ old('status', $item->status) === 'Internship Completed' ? 'selected' : '' }}>
                                    🔒 Internship Completed</option>
                                <option value="Terminated" {{ old('status', $item->status) === 'Terminated' ? 'selected' : '' }}>
                                    🔒 Terminated</option>
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Join Date <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_bergabung"
                            value="{{ old('tanggal_bergabung', $item->tanggal_bergabung ? $item->tanggal_bergabung->format('Y-m-d') : '') }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            required>
                    </div>

                    @php
                        $inactiveStatuses = ['Resigned', 'Contract Ended', 'Internship Completed', 'Terminated'];
                        $isInactive = in_array(old('status', $item->status), $inactiveStatuses);
                    @endphp

                    {{-- WARNING SUSPEND --}}
                    <div id="warningInactive{{ $item->id }}"
                        class="md:col-span-2 bg-red-50 border border-red-200 rounded-lg p-4"
                        style="display:{{ $isInactive ? 'block' : 'none' }};">
                        <div class="flex items-start gap-3">
                            <div class="text-red-500 text-xl">⚠️</div>
                            <div>
                                <p class="text-sm font-semibold text-red-800">Warning: Suspending Employee</p>
                                <p class="text-xs text-red-600 mt-1">
                                    Changing status to "<span
                                        id="statusTextEdit{{ $item->id }}">{{ old('status', $item->status) }}</span>" will:
                                </p>
                                <ul class="text-xs text-red-600 mt-1 list-disc list-inside">
                                    <li>Suspend this employee's account immediately</li>
                                    <li>Employee will <strong>NOT be able to login</strong></li>
                                    <li>Set end date automatically (if empty)</li>
                                    <li>Calculate total working days automatically</li>
                                </ul>
                                <p class="text-xs text-green-600 mt-2">
                                    ✅ To reactivate, change status back to Full-time, Contract, or Internship.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div id="endDateFieldEdit{{ $item->id }}"
                        style="display:{{ $isInactive ? 'block' : 'none' }};">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            End Date
                            <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-400">(auto-filled if empty)</span>
                        </label>
                        <input type="date" name="end_date"
                            value="{{ old('end_date', $item->end_date ? $item->end_date->format('Y-m-d') : '') }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div id="reasonResignedFieldEdit{{ $item->id }}"
                        style="display:{{ $isInactive ? 'block' : 'none' }};">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <textarea name="reason_resigned" rows="2"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Reason for leaving...">{{ old('reason_resigned', $item->reason_resigned) }}</textarea>
                    </div>

                    {{-- Tampilkan total hari kerja jika status inactive --}}
                    @if ($isInactive && $item->total_hari_kerja > 0)
                        <div class="md:col-span-2 bg-blue-50 rounded-lg p-3 border border-blue-200">
                            <label class="block text-sm font-medium text-blue-700 mb-1">📊 Total Working Days</label>
                            <p class="text-lg font-semibold text-blue-800">
                                {{ number_format($item->total_hari_kerja) }} days
                                <span
                                    class="text-sm font-normal text-blue-500">({{ $item->total_hari_kerja_formatted }})</span>
                            </p>
                            <p class="text-xs text-blue-400 mt-1">
                                Calculated automatically:
                                {{ $item->tanggal_bergabung ? $item->tanggal_bergabung->format('d M Y') : '-' }} →
                                {{ $item->end_date ? $item->end_date->format('d M Y') : '-' }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- BANK INFO --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Bank Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                        <input type="text" name="nama_bank" value="BSI" readonly
                            class="w-full border rounded-lg px-3 py-2 bg-gray-100 text-gray-700 cursor-not-allowed focus:outline-none">
                        <p class="text-xs text-gray-500 mt-1">Default bank: BSI (cannot be changed)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening', $item->nomor_rekening) }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            {{-- KONTAK --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" name="nomor_telepon" value="{{ old('nomor_telepon', $item->nomor_telepon) }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                        <input type="text" name="npwp" value="{{ old('npwp', $item->npwp) }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                        <textarea name="alamat" rows="3" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('alamat', $item->alamat) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- DATA PRIBADI --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Personal Data</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Place of Birth <span class="text-red-500">*</span></label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $item->tempat_lahir) }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_lahir" required
                            value="{{ old('tanggal_lahir', $item->tanggal_lahir ? $item->tanggal_lahir->format('Y-m-d') : '') }}"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                        <select name="jenis_kelamin" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">Select</option>
                            <option value="L" {{ old('jenis_kelamin', $item->jenis_kelamin) === 'L' ? 'selected' : '' }}>Male</option>
                            <option value="P" {{ old('jenis_kelamin', $item->jenis_kelamin) === 'P' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Religion <span class="text-red-500">*</span></label>
                        <input type="text" name="agama" value="{{ old('agama', $item->agama) }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status <span class="text-red-500">*</span></label>
                        <select name="status_pernikahan" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Select Marital Status</option>
                            <option value="Single" {{ old('status_pernikahan', $item->status_pernikahan) === 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('status_pernikahan', $item->status_pernikahan) === 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Divorced" {{ old('status_pernikahan', $item->status_pernikahan) === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- PENDIDIKAN --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Education</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Education <span class="text-red-500">*</span></label>
                        <select name="pendidikan_terakhir" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Select Education</option>
                            @php
                                $eduOptions = ['SMP', 'SMA/MA', 'SMK', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2'];
                                $currentEdu = old('pendidikan_terakhir', $item->pendidikan_terakhir_new ?? $item->pendidikan_terakhir);
                            @endphp
                            @foreach ($eduOptions as $opt)
                                <option value="{{ $opt }}" {{ $currentEdu === $opt ? 'selected' : '' }}>
                                    {{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">University/School <span class="text-red-500">*</span></label>
                        <input type="text" name="universitas" value="{{ old('universitas', $item->universitas) }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Major <span class="text-red-500">*</span></label>
                        <input type="text" name="jurusan" value="{{ old('jurusan', $item->jurusan) }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Graduation Year <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun_lulus" value="{{ old('tahun_lulus', $item->tahun_lulus) }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            min="1900" max="2099" step="1">
                    </div>
                </div>
            </div>

            {{-- KONTAK DARURAT --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_kontak_darurat" value="{{ old('nama_kontak_darurat', $item->nama_kontak_darurat) }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Phone <span class="text-red-500">*</span></label>
                        <input type="text" name="telepon_kontak_darurat" value="{{ old('telepon_kontak_darurat', $item->telepon_kontak_darurat) }}" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            {{-- INTEGRASI --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2 flex items-center gap-2">
                    Integrations
                    <span class="text-xs font-normal text-gray-400">(optional)</span>
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#0052CC]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 0H3C1.343 0 0 1.343 0 3v18c0 1.656 1.343 3 3 3h18c1.656 0 3-1.344 3-3V3c0-1.657-1.344-3-3-3zM10.44 18.18c0 .795-.645 1.44-1.44 1.44H4.56c-.795 0-1.44-.645-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44H9c.795 0 1.44.645 1.44 1.44v12.36zm10.44-7.08c0 .794-.645 1.44-1.44 1.44H15c-.795 0-1.44-.646-1.44-1.44V5.82c0-.795.645-1.44 1.44-1.44h4.44c.795 0 1.44.645 1.44 1.44v5.28z"/>
                            </svg>
                            Tracker / Trello Email
                        </label>
                        <input type="email" name="tracker_email"
                            value="{{ old('tracker_email', $item->tracker_email) }}"
                            placeholder="Email yang digunakan di Dashboard Tracker (jika berbeda)"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-xs text-gray-400 mt-1">
                            Isi hanya jika email di Dashboard Activity Tracker berbeda dengan email utama di atas.
                            Digunakan untuk mencocokkan data task dari Trello saat sync performa.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" data-modal-hide="employee-modal-edit-{{ $item->id }}"
                    class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleJabatanLainnyaEdit(id) {
        const select = document.getElementById('jabatanSelectEdit' + id);
        const lainnyaField = document.getElementById('jabatanLainnyaFieldEdit' + id);
        if (select.value === 'lainnya') {
            lainnyaField.style.display = 'block';
        } else {
            lainnyaField.style.display = 'none';
        }
    }

    function toggleEndDateEdit(id) {
        const statusSelect = document.getElementById('statusSelectEdit' + id);
        const endDateField = document.getElementById('endDateFieldEdit' + id);
        const reasonField = document.getElementById('reasonResignedFieldEdit' + id);
        const warningBox = document.getElementById('warningInactive' + id);
        const statusText = document.getElementById('statusTextEdit' + id);
        const inactiveStatuses = ['Resigned', 'Contract Ended', 'Internship Completed', 'Terminated'];

        if (inactiveStatuses.includes(statusSelect.value)) {
            endDateField.style.display = 'block';
            reasonField.style.display = 'block';
            warningBox.style.display = 'block';
            if (statusText) statusText.textContent = statusSelect.options[statusSelect.selectedIndex].text.replace(
                '🔒 ', '');
        } else {
            endDateField.style.display = 'none';
            reasonField.style.display = 'none';
            warningBox.style.display = 'none';
        }
    }

    // Trigger on load
    document.addEventListener('DOMContentLoaded', function() {
        @foreach ($karyawans as $item)
            toggleEndDateEdit({{ $item->id }});
        @endforeach
    });
</script>
