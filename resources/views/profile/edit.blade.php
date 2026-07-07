@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto">
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <!-- TEXT -->
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">
                    My Profile
                </h1>
                <p class="text-sm text-gray-700/80">
                    Manage and update your personal and employment information.
                </p>
            </div>

            <!-- IMAGE / ILLUSTRATION -->
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        @if (session('success'))
            <div class="relative px-4 py-3 mt-4 text-green-700 bg-green-100 border border-green-400 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="relative px-4 py-3 mt-4 text-red-700 bg-red-100 border border-red-400 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="p-4 mt-5 bg-white border rounded-lg shadow card border-default">
            <div class="mb-4 border-b border-default">
                <ul class="flex flex-wrap justify-between w-full text-sm font-medium text-center" id="default-tab"
                    data-tabs-toggle="#default-tab-content" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-base"
                            id="profile-tab" data-tabs-target="#profile" type="button" role="tab"
                            aria-controls="profile" aria-selected="true">Overview</button>
                    </li>

                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-base hover:text-fg-brand hover:border-brand"
                            id="dashboard-tab" data-tabs-target="#dashboard" type="button" role="tab"
                            aria-controls="dashboard" aria-selected="false">Attendance & Leave</button>
                    </li>

                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-base hover:text-fg-brand hover:border-brand"
                            id="performance-tab" data-tabs-target="#performance" type="button" role="tab"
                            aria-controls="performance" aria-selected="false">Performance</button>
                    </li>
                </ul>
            </div>
            <div id="default-tab-content">
                <div class="block" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="grid gap-8 md:grid-cols-3">
                        <!-- LEFT CONTENT - EDIT FORM -->
                        <div class="space-y-8 md:col-span-2">
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                                id="profileForm">
                                @csrf
                                @method('PATCH')

                                <!-- PERSONAL INFO -->
                                <div>
                                    <h4 class="mb-4 text-lg font-semibold text-blue-900">
                                        Personal Info
                                    </h4>

                                    <div class="grid text-sm md:grid-cols-2 gap-x-10 gap-y-4">
                                        <!-- LEFT COLUMN -->
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block mb-1 text-gray-400">First Name <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" name="nama_depan"
                                                    value="{{ old('nama_depan', $karyawan->nama_depan ?? (explode(' ', $karyawan->nama_lengkap)[0] ?? '')) }}"
                                                    required
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                                @error('nama_depan')
                                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-gray-400">NIP</label>
                                                <input type="text" value="{{ $karyawan->nip }}" readonly
                                                    class="w-full px-3 py-2 bg-gray-100 border rounded-lg">
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-gray-400">Address <span
                                                        class="text-red-500">*</span></label>
                                                <textarea name="alamat" rows="3"
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500" required>{{ old('alamat', $karyawan->alamat) }}</textarea>
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-gray-400">Place of Birth <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" name="tempat_lahir"
                                                    value="{{ old('tempat_lahir', $karyawan->tempat_lahir) }}"
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                                                    required>
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-gray-400">Gender <span
                                                        class="text-red-500">*</span></label>
                                                <select name="jenis_kelamin"
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                                    <option value="">Select</option>
                                                    <option value="L"
                                                        {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'L' ? 'selected' : '' }}>
                                                        Male</option>
                                                    <option value="P"
                                                        {{ old('jenis_kelamin', $karyawan->jenis_kelamin) == 'P' ? 'selected' : '' }}>
                                                        Female</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-gray-400">Marital Status <span
                                                        class="text-red-500">*</span></label>
                                                <select name="status_pernikahan"
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                                    <option value="">Select</option>
                                                    <option value="Single"
                                                        {{ old('status_pernikahan', $karyawan->status_pernikahan) == 'Single' ? 'selected' : '' }}>
                                                        Single</option>
                                                    <option value="Married"
                                                        {{ old('status_pernikahan', $karyawan->status_pernikahan) == 'Married' ? 'selected' : '' }}>
                                                        Married</option>
                                                    <option value="Divorced"
                                                        {{ old('status_pernikahan', $karyawan->status_pernikahan) == 'Divorced' ? 'selected' : '' }}>
                                                        Divorced</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- RIGHT COLUMN -->
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block mb-1 text-gray-400">Last Name <span
                                                        class="text-red-500">*</span></label>
                                                @php
                                                    $namaParts = explode(' ', $karyawan->nama_lengkap);
                                                    $namaBelakang =
                                                        count($namaParts) > 1
                                                            ? implode(' ', array_slice($namaParts, 1))
                                                            : '';
                                                @endphp
                                                <input type="text" name="nama_belakang"
                                                    value="{{ old('nama_belakang', $karyawan->nama_belakang ?? $namaBelakang) }}"
                                                    required
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                                @error('nama_belakang')
                                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-gray-400">Email <span
                                                        class="text-red-500">*</span></label>
                                                <input type="email" name="email"
                                                    value="{{ old('email', $karyawan->email) }}" required
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                                @error('email')
                                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-gray-400">Phone Number <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" name="nomor_telepon"
                                                    value="{{ old('nomor_telepon', $karyawan->nomor_telepon) }}"
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 number-only"
                                                    required>
                                                <p class="hidden mt-1 text-xs text-red-600 input-error">
                                                    Field must contain numbers only.
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-gray-400">NIK <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" name="nik"
                                                    value="{{ old('nik', $karyawan->nik) }}"
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 number-only"
                                                    required>
                                                @error('nik')
                                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                                @enderror
                                                <p class="hidden mt-1 text-xs text-red-600 input-error">
                                                    Field must contain numbers only.
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-gray-400">NPWP <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" name="npwp"
                                                    value="{{ old('npwp', $karyawan->npwp) }}"
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 number-only"
                                                    required>
                                                <p class="hidden mt-1 text-xs text-red-600 input-error">
                                                    Field must contain numbers only.
                                                </p>
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-gray-400">Date of Birth <span
                                                        class="text-red-500">*</span></label>
                                                <input type="date" name="tanggal_lahir"
                                                    value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('Y-m-d') : '') }}"
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                                                    required>
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-gray-400">Religion <span
                                                        class="text-red-500">*</span></label>
                                                <select name="agama"
                                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                                    <option value="">Select</option>
                                                    <option value="Islam"
                                                        {{ old('agama', $karyawan->agama) == 'Islam' ? 'selected' : '' }}>
                                                        Islam</option>
                                                    <option value="Kristen"
                                                        {{ old('agama', $karyawan->agama) == 'Kristen' ? 'selected' : '' }}>
                                                        Kristen</option>
                                                    <option value="Katolik"
                                                        {{ old('agama', $karyawan->agama) == 'Katolik' ? 'selected' : '' }}>
                                                        Katolik</option>
                                                    <option value="Hindu"
                                                        {{ old('agama', $karyawan->agama) == 'Hindu' ? 'selected' : '' }}>
                                                        Hindu</option>
                                                    <option value="Buddha"
                                                        {{ old('agama', $karyawan->agama) == 'Buddha' ? 'selected' : '' }}>
                                                        Buddha</option>
                                                    <option value="Konghucu"
                                                        {{ old('agama', $karyawan->agama) == 'Konghucu' ? 'selected' : '' }}>
                                                        Konghucu</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- PASSPORT --}}
                                <div class="my-4 mb-4">
                                    @php
                                        $hasPassport =
                                            old('nomor_paspor', $karyawan->nomor_paspor) ||
                                            old(
                                                'paspor_berlaku_hingga',
                                                optional($karyawan->paspor_berlaku_hingga)->format('Y-m-d'),
                                            );
                                    @endphp

                                    <div class="mb-4 md:col-span-2">
                                        <label class="inline-flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" id="hasPassport" {{ $hasPassport ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 rounded">

                                            <span class="text-sm font-medium text-gray-700">
                                                Employee has a passport
                                            </span>
                                        </label>
                                    </div>

                                    <div id="passportSection" class="{{ $hasPassport ? '' : 'hidden' }} md:col-span-2">
                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                                    Passport Number <span class="text-red-500">*</span>
                                                </label>

                                                <input id="passportNumber" type="text" name="nomor_paspor"
                                                    value="{{ old('nomor_paspor', $karyawan->nomor_paspor) }}"
                                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-gray-700">
                                                    Passport Validity Period <span class="text-red-500">*</span>
                                                </label>

                                                <input id="passportExpiry" type="date" name="paspor_berlaku_hingga"
                                                    value="{{ old('paspor_berlaku_hingga', optional($karyawan->paspor_berlaku_hingga)->format('Y-m-d')) }}"
                                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- EMPLOYMENT INFO -->
                                <div class="my-4 mb-4">
                                    <h4 class="flex items-center gap-2 mb-4 text-lg font-semibold text-blue-900">
                                        Employment Info
                                    </h4>

                                    <div class="grid gap-4 text-sm md:grid-cols-2">
                                        <div>
                                            <label class="block mb-1 text-gray-400">Position <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text"
                                                value="{{ $karyawan->jabatan_display ?? ucfirst($karyawan->role) }}"
                                                readonly class="w-full px-3 py-2 bg-gray-100 border rounded-lg">
                                        </div>

                                        <div>
                                            <label class="block mb-1 text-gray-400">Status <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" value="{{ $karyawan->status ?? 'Permanent' }}" readonly
                                                class="w-full px-3 py-2 bg-gray-100 border rounded-lg">
                                        </div>

                                        <div>
                                            <label class="block mb-1 text-gray-400">Date of Joining <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text"
                                                value="{{ $karyawan->tanggal_bergabung ? $karyawan->tanggal_bergabung->format('d M Y') : '-' }}"
                                                readonly class="w-full px-3 py-2 bg-gray-100 border rounded-lg">
                                        </div>

                                        <div>
                                            <label class="block mb-1 text-gray-400">Bank Name <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="nama_bank" value="BSI" readonly
                                                class="w-full px-3 py-2 text-gray-700 bg-gray-100 border rounded-lg cursor-not-allowed focus:outline-none">
                                            <p class="mt-1 text-xs text-gray-500">Default bank: BSI</p>
                                        </div>

                                        <div>
                                            <label class="block mb-1 text-gray-400">Account Number <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="nomor_rekening"
                                                value="{{ old('nomor_rekening', $karyawan->nomor_rekening) }}"
                                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 number-only"
                                                required>
                                            <p class="hidden mt-1 text-xs text-red-600 input-error">
                                                Field must contain numbers only.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- EDUCATION INFO -->
                                <div>
                                    <h4 class="mb-4 text-lg font-semibold text-blue-900">Education </h4>

                                    <div class="grid gap-4 text-sm md:grid-cols-2">
                                        <div>
                                            <label class="block mb-1 text-gray-400">Highest Education <span
                                                    class="text-red-500">*</span></label>
                                            <select name="pendidikan_terakhir"
                                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                                                <option value="">Select</option>
                                                @php
                                                    $eduOptions = [
                                                        'SMP',
                                                        'SMA/MA',
                                                        'SMK',
                                                        'D1',
                                                        'D2',
                                                        'D3',
                                                        'D4',
                                                        'S1',
                                                        'S2',
                                                    ];
                                                    $currentEdu =
                                                        $karyawan->pendidikan_terakhir_new ??
                                                        $karyawan->pendidikan_terakhir;
                                                @endphp
                                                @foreach ($eduOptions as $opt)
                                                    <option value="{{ $opt }}"
                                                        {{ old('pendidikan_terakhir', $currentEdu) == $opt ? 'selected' : '' }}>
                                                        {{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block mb-1 text-gray-400">University <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="universitas"
                                                value="{{ old('universitas', $karyawan->universitas) }}"
                                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                                                required>
                                        </div>

                                        <div>
                                            <label class="block mb-1 text-gray-400">Major <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="jurusan"
                                                value="{{ old('jurusan', $karyawan->jurusan) }}"
                                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                                                required>
                                        </div>

                                        <div>
                                            <label class="block mb-1 text-gray-400">Graduation Year <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="tahun_lulus"
                                                value="{{ old('tahun_lulus', $karyawan->tahun_lulus) }}"
                                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 number-only"
                                                min="1900" max="2099" required>
                                            <p class="hidden mt-1 text-xs text-red-600 input-error">
                                                Field must contain numbers only.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- EMERGENCY CONTACT -->
                                <div>
                                    <h4 class="mb-4 text-lg font-semibold text-blue-900">Emergency Contact</h4>

                                    <div class="grid gap-4 text-sm md:grid-cols-2">
                                        <div>
                                            <label class="block mb-1 text-gray-400">Emergency Contact Name <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="nama_kontak_darurat"
                                                value="{{ old('nama_kontak_darurat', $karyawan->nama_kontak_darurat) }}"
                                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500"
                                                required>
                                        </div>

                                        <div>
                                            <label class="block mb-1 text-gray-400">Emergency Contact Phone <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="telepon_kontak_darurat"
                                                value="{{ old('telepon_kontak_darurat', $karyawan->telepon_kontak_darurat) }}"
                                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500 number-only"
                                                required>
                                            <p class="hidden mt-1 text-xs text-red-600 input-error">
                                                Field must contain numbers only.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-3 mt-6">
                                    <button id="submitBtn" type="submit"
                                        class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                        Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- RIGHT AVATAR -->
                        <div class="flex flex-col items-center justify-start gap-4">

                            @php
                                $fotoProfil = $karyawan->foto_profil;

                                $fotoUrl =
                                    $fotoProfil && Storage::disk('public')->exists($fotoProfil)
                                        ? Storage::url($fotoProfil)
                                        : 'https://ui-avatars.com/api/?background=0D8F81&color=fff&size=200&name=' .
                                            urlencode($karyawan->nama_lengkap);
                            @endphp

                            <div
                                class="w-40 h-40 md:w-52 md:h-52 rounded-full border-[6px] border-blue-900 overflow-hidden bg-gray-100">

                                <img id="previewPhoto" src="{{ $fotoUrl }}" class="object-cover w-full h-full">

                            </div>

                            <form id="photoForm" method="POST" action="{{ route('profile.photo.update') }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')
                                <input type="file" id="imageInput" accept="image/*" class="hidden">
                                <label for="imageInput"
                                    class="px-4 py-2 text-white bg-blue-600 rounded-lg cursor-pointer hover:bg-blue-700">
                                    📷 Upload Photo
                                </label>
                            </form>

                        </div>

                        {{-- MODAL --}}
                        <div id="cropModal" class="fixed inset-0 z-50 items-center justify-center hidden p-4 bg-black/70">
                            <div class="bg-white rounded-xl w-full max-w-xl max-h-[90vh] overflow-y-auto p-5">
                                <h2 class="mb-4 text-xl font-bold">Crop Profile Photo</h2>

                                <div class="w-full overflow-hidden bg-gray-100 rounded-lg h-72 md:h-96">
                                    <img id="cropImage" class="block max-w-full">
                                </div>

                                <div class="flex justify-end gap-2 mt-5">
                                    <button id="cancelCrop" type="button" class="px-4 py-2 bg-gray-300 rounded">
                                        Cancel
                                    </button>
                                    <button id="saveCrop" type="button"
                                        class="px-4 py-2 text-white bg-blue-600 rounded">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden p-4" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                    @include('profile.partials.attendance_leave_tab', [
                        'attendanceRate' => $attendanceRate ?? 0,
                        'presentCount' => $presentCount ?? 0,
                        'lateCount' => $lateCount ?? 0,
                        'absentCount' => $absentCount ?? 0,
                        'recentAttendances' => $recentAttendances ?? collect(),
                        'annualLeaveUsed' => $annualLeaveUsed ?? 0,
                        'annualLeaveQuota' => $annualLeaveQuota ?? 12,
                        'sickLeaveUsed' => $sickLeaveUsed ?? 0,
                        'sickLeaveQuota' => $sickLeaveQuota ?? 12,
                        'emergencyLeaveUsed' => $emergencyLeaveUsed ?? 0,
                        'emergencyLeaveQuota' => $emergencyLeaveQuota ?? 12,
                        'otherLeaveUsed' => $otherLeaveUsed ?? 0,
                        'otherLeaveQuota' => $otherLeaveQuota ?? 12,
                        'leaveRequests' => $leaveRequests ?? collect(),
                    ])
                </div>

                <div class="hidden p-4" id="performance" role="tabpanel" aria-labelledby="performance-tab">
                    @include('profile.partials.performance_tab', [
                        'latestPerformance' => $latestPerformance ?? null,
                        'performanceChange' => $performanceChange ?? 0,
                        'taskCompletionRate' => $taskCompletionRate ?? 0,
                        'todoTasks' => $todoTasks ?? 0,
                        'inProgressTasks' => $inProgressTasks ?? 0,
                        'doneTasks' => $doneTasks ?? 0,
                    ])
                </div>
            </div>
        </div>

        <!-- Change Password Section -->
        <div class="p-4 mt-5 bg-white border rounded-lg shadow card border-default">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">Reset Password</h3>

            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Current Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="current_password" required
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                        @error('current_password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">New Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="new_password" required
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                        @error('new_password')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-bold text-gray-700">Confirm New Password <span
                                class="text-red-500">*</span></label>
                        <input type="password" name="new_password_confirmation" required
                            class="w-full px-3 py-2 leading-tight text-gray-700 border rounded shadow appearance-none focus:outline-none focus:shadow-outline">
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="px-4 py-2 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let cropper;
        const imageInput = document.getElementById("imageInput");
        const cropModal = document.getElementById("cropModal");
        const cropImage = document.getElementById("cropImage");
        const preview = document.getElementById("previewPhoto");
        imageInput.addEventListener("change", (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (event) => {
                cropImage.src = event.target.result;
                cropModal.classList.remove("hidden");
                cropModal.classList.add("flex");
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 2,
                    dragMode: 'move',
                    autoCropArea: 1,
                    responsive: true,
                    background: false,
                    movable: true,
                    zoomable: true,
                    scalable: true,
                    rotatable: false,
                });
            }
            reader.readAsDataURL(file);
        });
        document.getElementById("cancelCrop").onclick = () => {
            cropModal.classList.add("hidden");
            cropModal.classList.remove("flex");
            cropper.destroy();
        }
        document.getElementById("saveCrop").onclick = () => {
            cropper.getCroppedCanvas({
                width: 600,
                height: 600,
                imageSmoothingQuality: 'high'
            }).toBlob((blob) => {
                const form = new FormData(document.getElementById("photoForm"));
                form.append("foto_profil", blob, "profile.jpg");

                const saveBtn = document.getElementById("saveCrop");
                saveBtn.disabled = true;
                saveBtn.textContent = "Saving...";

                fetch("{{ route('profile.photo.update') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "X-HTTP-Method-Override": "PATCH",
                            "Accept": "application/json"
                        },
                        body: form
                    })
                    .then(res => res.json())
                    .then(data => {
                        saveBtn.disabled = false;
                        saveBtn.textContent = "Save";

                        if (!data.success) {
                            alert(data.message || "Gagal upload foto");
                            return;
                        }

                        preview.src = data.foto_url ?? URL.createObjectURL(blob);
                        cropModal.classList.add("hidden");
                        cropModal.classList.remove("flex");
                        cropper.destroy();
                        window.location.reload(); // Reload the page to reflect the new photo
                    })
                    .catch(err => {
                        saveBtn.disabled = false;
                        saveBtn.textContent = "Save";
                        alert("Terjadi kesalahan, coba lagi.");
                        console.log(err);
                    });
            }, "image/jpeg", 0.95);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const hasPassport = document.getElementById('hasPassport');
            const passportSection = document.getElementById('passportSection');
            const passportNumber = document.getElementById('passportNumber');
            const passportExpiry = document.getElementById('passportExpiry');

            // Kalau halaman lain tidak punya elemen ini, hentikan saja
            if (!hasPassport || !passportSection || !passportNumber || !passportExpiry) {
                return;
            }

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

            // Sinkronkan tampilan awal
            togglePassport();
        });
    </script>
@endpush
