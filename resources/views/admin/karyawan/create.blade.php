<div id="employee-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- MODAL BOX -->
    <div
        class="bg-white w-full max-w-2xl rounded-xl shadow-xl max-h-[90vh] overflow-y-auto p-5 md:p-8 transition-all duration-300">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <h2 class="text-lg md:text-2xl font-semibold text-blue-900">Create employee</h2>
            <button data-modal-hide="employee-modal" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>

        <!-- FORM -->
        <form method="POST" action="{{ route('admin.karyawan.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- DATA UTAMA --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Main Data</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                        <input type="text" name="nik"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_depan" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_belakang" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="kata_sandi" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                        <input type="file" name="foto_profil" class="w-full border rounded-lg px-3 py-2">
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
                            <option value="karyawan">Employee</option>
                            <option value="hr">HR</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                        <select name="jabatan" id="jabatanSelect" onchange="toggleJabatanLainnya()"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Select Position</option>
                            <option value="Chief Executive Officer">Chief Executive Officer</option>
                            <option value="Chief Operating Officer">Chief Operating Officer</option>
                            <option value="Creative Writer">Creative Writer</option>
                            <option value="Finance">Finance</option>
                            <option value="Business Development">Business Development</option>
                            <option value="Videographer">Videographer</option>
                            <option value="Video Editor">Video Editor</option>
                            <option value="Social Media Manager">Social Media Manager</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div id="jabatanLainnyaField" style="display:none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Custom Position <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="jabatan_lainnya"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Enter custom position">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                class="text-red-500">*</span></label>
                        <select name="status" id="statusSelect" onchange="toggleEndDate()" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Select Status</option>
                            <optgroup label="Active">
                                <option value="Full-time">Full-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Internship">Internship</option>
                            </optgroup>
                            <optgroup label="Inactive">
                                <option value="Resigned">Resigned</option>
                                <option value="Contract Ended">Contract Ended</option>
                                <option value="Internship Completed">Internship Completed</option>
                                <option value="Terminated">Terminated</option>
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Join Date</label>
                        <input type="date" name="tanggal_bergabung" id="tanggal_bergabung"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div id="endDateField" style="display:none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="end_date" id="end_date"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div id="reasonResignedField" style="display:none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <textarea name="reason_resigned" rows="2"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Reason for leaving..."></textarea>
                    </div>
                </div>
            </div>

            {{-- BANK INFO --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Bank Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                        <input type="text" name="nama_bank" value="BSI"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                        <input type="text" name="nomor_rekening"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            {{-- KONTAK --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="nomor_telepon"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                        <input type="text" name="npwp"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="alamat" rows="3"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- DATA PRIBADI --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Personal Data</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Place of Birth</label>
                        <input type="text" name="tempat_lahir" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" name="tanggal_lahir" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select name="jenis_kelamin" class="w-full border rounded-lg px-3 py-2">
                            <option value="">Select</option>
                            <option value="L">Male</option>
                            <option value="P">Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Religion</label>
                        <input type="text" name="agama" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
                        <input type="text" name="status_pernikahan" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>

            {{-- PENDIDIKAN --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Education</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Education <span
                                class="text-red-500">*</span></label>
                        <select name="pendidikan_terakhir"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="">Select Education</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA/MA">SMA/MA</option>
                            <option value="SMK">SMK</option>
                            <option value="D1">D1</option>
                            <option value="D2">D2</option>
                            <option value="D3">D3</option>
                            <option value="D4">D4</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">University</label>
                        <input type="text" name="universitas" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Major</label>
                        <input type="text" name="jurusan" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Graduation Year</label>
                        <input type="number" name="tahun_lulus" class="w-full border rounded-lg px-3 py-2"
                            min="1900" max="2099" step="1">
                    </div>
                </div>
            </div>

            {{-- KONTAK DARURAT --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Emergency Contact</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
                        <input type="text" name="nama_kontak_darurat" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Phone</label>
                        <input type="text" name="telepon_kontak_darurat"
                            class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" data-modal-hide="employee-modal"
                    class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
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

    // Trigger on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleJabatanLainnya();
        toggleEndDate();
    });

    // Trigger on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleJabatanLainnya();
    });
</script>
