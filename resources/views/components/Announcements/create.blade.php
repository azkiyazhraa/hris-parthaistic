<div id="announcement-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <!-- MODAL -->
    <div class="relative bg-white w-full max-w-xl rounded-xl shadow-2xl overflow-hidden animate-fadeIn">
        <!-- HEADER -->
        <div class="flex items-center justify-between px-6 md:px-8 py-5 border-b">
            <div>
                <h2 class="text-xl md:text-2xl font-semibold">
                    Create Announcement
                </h2>
                <p class="text-gray-500 text-sm mt-1">
                    Make a new announcement for employees
                </p>
            </div>

            <button data-modal-hide="announcement-modal"
                class="w-10 h-10 rounded-full flex items-center justify-center transition">
                ✕
            </button>
        </div>

        <!-- CONTENT -->
        <div class="max-h-[80vh] overflow-y-auto px-6 md:px-8">

            <form method="POST" action="{{ route('admin.pengumuman.store') }}" enctype="multipart/form-data"
                class="space-y-8">
                @csrf

                <!-- INFORMASI PENGUMUMAN -->
                <div class="bg-gray-50 rounded-2xl border border-gray-100 p-5">
                    <div class="mb-5">
                        <h3 class="text-base font-semibold text-gray-800">
                            Announcement Information
                        </h3>
                        <p class="text-sm text-gray-500">
                            Fill in the details of the announcement to be displayed
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- JUDUL -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Title
                            </label>

                            <input type="text" name="judul" value="{{ old('judul') }}" required
                                placeholder="Enter the title of the announcement..."
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            @error('judul')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- KONTEN -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Content
                            </label>

                            <textarea name="konten" rows="8" required placeholder="Write the announcement content here..."
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('konten') }}</textarea>

                            @error('konten')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- PENGATURAN -->
                <div class="bg-gray-50 rounded-2xl border border-gray-100 p-5">

                    <div class="mb-5">
                        <h3 class="text-base font-semibold text-gray-800">
                            Setting
                        </h3>

                        <p class="text-sm text-gray-500">
                            Set categories, target role, and publication schedules
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <!-- KATEGORI -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Categories
                            </label>

                            <select name="kategori"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Category</option>
                                <option value="umum" {{ old('kategori') == 'umum' ? 'selected' : '' }}>
                                    General
                                </option>
                                <option value="kebijakan" {{ old('kategori') == 'kebijakan' ? 'selected' : '' }}>
                                    Policy
                                </option>
                                <option value="pengumuman" {{ old('kategori') == 'pengumuman' ? 'selected' : '' }}>
                                    Announcement
                                </option>
                                <option value="event" {{ old('kategori') == 'event' ? 'selected' : '' }}>
                                    Event
                                </option>
                                <option value="penting" {{ old('kategori') == 'penting' ? 'selected' : '' }}>
                                    Important
                                </option>
                                <option value="crew_call" {{ old('kategori') == 'crew_call' ? 'selected' : '' }}>
                                    Crew Call
                                </option>
                            </select>
                        </div>

                        <!-- TARGET ROLE -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Target Role
                            </label>

                            <select name="target_role"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="all" {{ old('target_role') == 'all' ? 'selected' : '' }}>
                                    All Role
                                </option>
                                <option value="admin" {{ old('target_role') == 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>
                                <option value="hr" {{ old('target_role') == 'hr' ? 'selected' : '' }}>
                                    HR
                                </option>
                                <option value="karyawan" {{ old('target_role') == 'karyawan' ? 'selected' : '' }}>
                                    Employees
                                </option>
                            </select>
                        </div>

                        <!-- TANGGAL TERBIT -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Publish Date
                            </label>

                            <input type="datetime-local" name="tanggal_terbit" value="{{ old('tanggal_terbit') }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            <p class="text-xs text-gray-500 mt-2">
                                Leave blank if you want it published immediately
                            </p>
                        </div>

                        <!-- BERLAKU HINGGA -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Valid Until
                            </label>

                            <input type="date" name="tanggal_berlaku_hingga" value="{{ old('tanggal_berlaku_hingga') }}"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            @error('tanggal_berlaku_hingga')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror

                            <p class="text-xs text-gray-500 mt-2">
                                Leave blank if the announcement should stay valid indefinitely
                            </p>
                        </div>

                    </div>
                </div>

                <!-- LAMPIRAN -->
                <div class="bg-gray-50 rounded-2xl border border-gray-100 p-5">

                    <div class="mb-5">
                        <h3 class="text-base font-semibold text-gray-800">
                            Attachment
                        </h3>

                        <p class="text-sm text-gray-500">
                            Upload announcement supporting file
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Attachment File
                        </label>

                        <input type="file" name="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full rounded-xl border border-dashed border-gray-300 bg-white px-4 py-3 text-sm
                            file:mr-4 file:rounded-lg file:border-0
                            file:bg-blue-100 file:px-4 file:py-2
                            file:text-blue-700 hover:file:bg-blue-200">

                        <p class="text-xs text-gray-500 mt-2">
                            Maximum 5MB • PDF, DOC, DOCX, JPG, JPEG, PNG
                        </p>
                    </div>

                </div>

                <!-- STATUS -->
                <div class="flex items-center justify-between rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4">
                    <div>
                        <h4 class="text-sm font-semibold text-blue-900">
                            Publish Announcement
                        </h4>
                        <p class="text-xs text-blue-700 mt-1">
                            Enable to publish the announcement immediately
                        </p>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="status" value="1" {{ old('status') ? 'checked' : '' }}
                            class="sr-only peer">

                        <div class="w-11 h-6 bg-gray-300 rounded-full peer
                            peer-checked:bg-blue-600
                            after:content-['']
                            after:absolute
                            after:top-[2px]
                            after:left-[2px]
                            after:bg-white
                            after:border-gray-300
                            after:border
                            after:rounded-full
                            after:h-5
                            after:w-5
                            after:transition-all
                            peer-checked:after:translate-x-full">
                        </div>
                    </label>
                </div>

                <!-- FOOTER -->
                <div class="bg-white py-5 border-t flex justify-end gap-3">

                    <button type="button" data-modal-hide="announcement-modal"
                        class="px-3 py-2 text-sm rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                        Cancel
                    </button>

                    <button type="submit"
                        class="px-3 py-2 text-sm rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium shadow-md transition">
                        Save Announcement
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>