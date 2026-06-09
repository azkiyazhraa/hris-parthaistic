<div id="employee-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- MODAL BOX -->
    <div
        class="bg-white w-full max-w-2xl rounded-xl shadow-xl
               max-h-[90vh] overflow-y-auto
               p-5 md:p-8
               transition-all duration-300">

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <h2 class="text-lg md:text-2xl font-semibold text-blue-900">
                Create employee
            </h2>

            <button data-modal-hide="employee-modal" class="text-gray-400 hover:text-gray-600 text-xl">
                &times;
            </button>
        </div>

        <!-- FORM -->
        <form method="POST" action="{{ route('admin.karyawan.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">

            {{-- DATA UTAMA --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Main Data</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                        <input type="text" name="nik" id="nik"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="nama_lengkap" id="nama_lengkap" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="kata_sandi" id="kata_sandi"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-xs text-gray-500 mt-1">Leave blank when editing if you don't want to change the password.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                        <input type="file" name="foto_profil" id="foto_profil"
                            class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>

            {{-- PEKERJAAN --}}
            <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Job Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select name="role" id="role"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="karyawan">Employee</option>
                            <option value="hr">HR</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="Permanent">Permanent</option>
                            <option value="Contract">Contract</option>
                            <option value="Outsource">Outsource</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Join Date</label>
                        <input type="date" name="tanggal_bergabung" id="tanggal_bergabung"
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
                        <input type="text" name="nomor_telepon" id="nomor_telepon"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                        <input type="text" name="npwp" id="npwp"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="alamat" id="alamat" rows="3"
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birthr</label>
                        <input type="date" name="tanggal_lahir" class="w-full border rounded-lg px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select name="jenis_kelamin" class="w-full border rounded-lg px-3 py-2">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Education</label>
                        <input type="text" name="pendidikan_terakhir" class="w-full border rounded-lg px-3 py-2">
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
                        <input type="number" name="tahun_lulus" class="w-full border rounded-lg px-3 py-2">
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
                    class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>

                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>
