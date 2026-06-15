@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4">
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <!-- TEXT -->
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">
                    My Profile
                </h1>
                <p class="text-gray-700/80 text-sm">
                    Manage and update your personal and employment information.
                </p>
            </div>

            <!-- IMAGE / ILLUSTRATION -->
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        @if (session('success'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card bg-white shadow p-4 rounded-lg border border-default mt-5">
            <div class="mb-4 border-b border-default">
                <ul class="flex flex-wrap text-sm font-medium justify-between text-center w-full" id="default-tab"
                    data-tabs-toggle="#default-tab-content" role="tablist">
                    <li class="me-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 rounded-t-base text-blue-600 border-blue-600"
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
                    <div class="grid md:grid-cols-3 gap-8">
                        <!-- LEFT CONTENT - EDIT FORM -->
                        <div class="md:col-span-2 space-y-8">
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                                id="profileForm">
                                @csrf
                                @method('PATCH')

                                <!-- PERSONAL INFO -->
                                <div>
                                    <h4 class="text-lg font-semibold text-blue-900 mb-4">
                                        Personal Info
                                    </h4>

                                    <div class="grid md:grid-cols-2 gap-x-10 gap-y-4 text-sm">
                                        <!-- LEFT COLUMN -->
                                        <div class="space-y-4">
                                            <div>
                                                <label class="text-gray-400 block mb-1">First Name <span
                                                        class="text-red-500">*</span></label>
                                                <input type="text" name="nama_depan"
                                                    value="{{ old('nama_depan', $karyawan->nama_depan ?? (explode(' ', $karyawan->nama_lengkap)[0] ?? '')) }}"
                                                    required
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                                @error('nama_depan')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="text-gray-400 block mb-1">NIP</label>
                                                <input type="text" value="{{ $karyawan->nip }}" readonly
                                                    class="w-full border rounded-lg px-3 py-2 bg-gray-100">
                                            </div>

                                            <div>
                                                <label class="text-gray-400 block mb-1">Address</label>
                                                <textarea name="alamat" rows="3"
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">{{ old('alamat', $karyawan->alamat) }}</textarea>
                                            </div>

                                            <div>
                                                <label class="text-gray-400 block mb-1">Place of Birth</label>
                                                <input type="text" name="tempat_lahir"
                                                    value="{{ old('tempat_lahir', $karyawan->tempat_lahir) }}"
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                            </div>

                                            <div>
                                                <label class="text-gray-400 block mb-1">Gender</label>
                                                <select name="jenis_kelamin"
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
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
                                                <label class="text-gray-400 block mb-1">Marital Status</label>
                                                <select name="status_pernikahan"
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
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
                                                <label class="text-gray-400 block mb-1">Last Name <span
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
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                                @error('nama_belakang')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="text-gray-400 block mb-1">Email <span
                                                        class="text-red-500">*</span></label>
                                                <input type="email" name="email"
                                                    value="{{ old('email', $karyawan->email) }}" required
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                                @error('email')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="text-gray-400 block mb-1">Phone Number</label>
                                                <input type="text" name="nomor_telepon"
                                                    value="{{ old('nomor_telepon', $karyawan->nomor_telepon) }}"
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                            </div>

                                            <div>
                                                <label class="text-gray-400 block mb-1">NIK</label>
                                                <input type="text" name="nik"
                                                    value="{{ old('nik', $karyawan->nik) }}"
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                                @error('nik')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div>
                                                <label class="text-gray-400 block mb-1">NPWP</label>
                                                <input type="text" name="npwp"
                                                    value="{{ old('npwp', $karyawan->npwp) }}"
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                            </div>

                                            <div>
                                                <label class="text-gray-400 block mb-1">Date of Birth</label>
                                                <input type="date" name="tanggal_lahir"
                                                    value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir ? $karyawan->tanggal_lahir->format('Y-m-d') : '') }}"
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                            </div>

                                            <div>
                                                <label class="text-gray-400 block mb-1">Religion</label>
                                                <select name="agama"
                                                    class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
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

                                <!-- EMPLOYMENT INFO -->
                                <div>
                                    <h4 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                                        Employment Info
                                    </h4>

                                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <label class="text-gray-400 block mb-1">Position</label>
                                            <input type="text"
                                                value="{{ $karyawan->jabatan_display ?? ucfirst($karyawan->role) }}"
                                                readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100">
                                        </div>

                                        <div>
                                            <label class="text-gray-400 block mb-1">Status</label>
                                            <input type="text" value="{{ $karyawan->status ?? 'Permanent' }}" readonly
                                                class="w-full border rounded-lg px-3 py-2 bg-gray-100">
                                        </div>

                                        <div>
                                            <label class="text-gray-400 block mb-1">Date of Joining</label>
                                            <input type="text"
                                                value="{{ $karyawan->tanggal_bergabung ? $karyawan->tanggal_bergabung->format('d M Y') : '-' }}"
                                                readonly class="w-full border rounded-lg px-3 py-2 bg-gray-100">
                                        </div>

                                        <div>
                                            <label class="text-gray-400 block mb-1">Bank Name</label>
                                            <input type="text" name="nama_bank"
                                                value="{{ old('nama_bank', $karyawan->nama_bank ?? 'BSI') }}"
                                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label class="text-gray-400 block mb-1">Account Number</label>
                                            <input type="text" name="nomor_rekening"
                                                value="{{ old('nomor_rekening', $karyawan->nomor_rekening) }}"
                                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                        </div>
                                    </div>
                                </div>

                                <!-- EDUCATION INFO -->
                                <div>
                                    <h4 class="text-lg font-semibold text-blue-900 mb-4">Education</h4>

                                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <label class="text-gray-400 block mb-1">Highest Education</label>
                                            <select name="pendidikan_terakhir"
                                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
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
                                            <label class="text-gray-400 block mb-1">University</label>
                                            <input type="text" name="universitas"
                                                value="{{ old('universitas', $karyawan->universitas) }}"
                                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label class="text-gray-400 block mb-1">Major</label>
                                            <input type="text" name="jurusan"
                                                value="{{ old('jurusan', $karyawan->jurusan) }}"
                                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label class="text-gray-400 block mb-1">Graduation Year</label>
                                            <input type="number" name="tahun_lulus"
                                                value="{{ old('tahun_lulus', $karyawan->tahun_lulus) }}"
                                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500"
                                                min="1900" max="2099">
                                        </div>
                                    </div>
                                </div>

                                <!-- EMERGENCY CONTACT -->
                                <div>
                                    <h4 class="text-lg font-semibold text-blue-900 mb-4">Emergency Contact</h4>

                                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <label class="text-gray-400 block mb-1">Emergency Contact Name</label>
                                            <input type="text" name="nama_kontak_darurat"
                                                value="{{ old('nama_kontak_darurat', $karyawan->nama_kontak_darurat) }}"
                                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                        </div>

                                        <div>
                                            <label class="text-gray-400 block mb-1">Emergency Contact Phone</label>
                                            <input type="text" name="telepon_kontak_darurat"
                                                value="{{ old('telepon_kontak_darurat', $karyawan->telepon_kontak_darurat) }}"
                                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex gap-3">
                                    <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition">
                                        Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- RIGHT AVATAR -->
                        <div class="flex flex-col justify-start items-center gap-4">
                            @php
                                $fotoProfil = $karyawan->foto_profil;
                                $fotoUrl =
                                    $fotoProfil && Storage::disk('public')->exists($fotoProfil)
                                        ? Storage::url($fotoProfil)
                                        : 'https://ui-avatars.com/api/?background=0D8F81&color=fff&size=200&name=' .
                                            urlencode($karyawan->nama_lengkap);
                            @endphp

                            <div
                                class="w-40 h-40 md:w-52 md:h-52 rounded-full border-[6px] border-blue-900 flex items-center justify-center overflow-hidden bg-gray-100">
                                <img src="{{ $fotoUrl }}" class="w-full h-full object-cover" alt="Profile Photo">
                            </div>

                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                                id="photoForm">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="nama_depan"
                                    value="{{ $karyawan->nama_depan ?? (explode(' ', $karyawan->nama_lengkap)[0] ?? '') }}">
                                <input type="hidden" name="nama_belakang"
                                    value="{{ $karyawan->nama_belakang ?? ($namaBelakang ?? '') }}">
                                <input type="hidden" name="email" value="{{ $karyawan->email }}">
                                <label
                                    class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition inline-block">
                                    <i class="mr-1">📷</i> Upload Photo
                                    <input type="file" name="foto_profil" accept="image/*" class="hidden"
                                        onchange="uploadPhoto()">
                                </label>
                                <p class="text-xs text-gray-500 mt-2">Max 2MB, JPG/PNG</p>
                            </form>
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
        <div class="card bg-white shadow p-4 rounded-lg border border-default mt-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Reset Password</h3>

            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Current Password</label>
                        <input type="password" name="current_password" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                        <input type="password" name="new_password" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @error('new_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Function to handle photo upload separately
        function uploadPhoto() {
            const form = document.getElementById('photoForm');
            const formData = new FormData(form);

            // Show loading indicator
            const btn = document.querySelector('#photoForm label');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="mr-1">⏳</i> Uploading...';
            btn.classList.add('opacity-50');

            fetch('{{ route('profile.update') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to upload photo'
                    });
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('opacity-50');
                });
        }

        // Initialize tabs
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('[data-tabs-target]');
            const tabContents = document.querySelectorAll('[role="tabpanel"]');

            function activateTab(targetId) {
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                    content.classList.remove('block');
                });

                const selectedContent = document.querySelector(targetId);
                if (selectedContent) {
                    selectedContent.classList.remove('hidden');
                    selectedContent.classList.add('block');
                }

                tabs.forEach(tab => {
                    const tabTarget = tab.getAttribute('data-tabs-target');
                    if (tabTarget === targetId) {
                        tab.classList.add('text-blue-600', 'border-blue-600');
                        tab.classList.remove('hover:text-fg-brand', 'hover:border-brand');
                        tab.setAttribute('aria-selected', 'true');
                    } else {
                        tab.classList.remove('text-blue-600', 'border-blue-600');
                        tab.classList.add('hover:text-fg-brand', 'hover:border-brand');
                        tab.setAttribute('aria-selected', 'false');
                    }
                });
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('data-tabs-target');
                    activateTab(targetId);
                });
            });
        });
    </script>
@endpush
