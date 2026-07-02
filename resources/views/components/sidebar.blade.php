<div class="flex items-center justify-between p-4 md:hidden bg-blue-800 text-white">
    <!-- LEFT: HAMBURGER -->
    <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar"
        type="button" class="inline-flex items-center p-2 text-sm text-white rounded-lg md:hidden">

        <span class="sr-only">Open sidebar</span>

        <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="2" stroke="currentColor">

            <path stroke-linecap="round" d="M5 7h14M5 12h14M5 17h10" />
        </svg>
    </button>

    @php
        $type = 'mobile';
    @endphp
    @auth
        @if (Auth::user()->role == 'karyawan')
            @php
                $todaySpecialStatus = $absensiToday?->status_kehadiran;
                $isTodayOff = in_array($todaySpecialStatus, ['change_day', 'leave']);
            @endphp

            @if ($isTodayOff)
                {{-- Day off (change day / leave) — no check-in or check-out --}}
                <span class="text-xs font-bold px-2 py-1 rounded-lg
                    {{ $todaySpecialStatus === 'change_day' ? 'bg-indigo-500 text-white' : 'bg-purple-500 text-white' }}">
                    {{ $todaySpecialStatus === 'change_day' ? 'Change Day Off' : 'On Leave' }}
                </span>
            @elseif ($absensiToday && !$absensiToday->jam_pulang)
                <button id="checkout-btn-sidebar"
                    data-modal-target="absence-modal-checkout-{{ $type }}-{{ $absensiToday->id }}"
                    data-modal-toggle="absence-modal-checkout-{{ $type }}-{{ $absensiToday->id }}"
                    class="text-xs font-bold bg-gray-400 text-white px-2 py-1 rounded-lg cursor-not-allowed"
                    disabled>
                    Check-Out
                </button>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const checkIn = new Date(
                            "{{ date('Y-m-d', strtotime($absensiToday->tanggal)) }}T{{ $absensiToday->jam_masuk }}"
                        );
                        const btn = document.getElementById('checkout-btn-sidebar');
                        function updateSidebarBtn() {
                            const elapsed = (new Date() - checkIn) / 1000;
                            if (elapsed >= 7 * 3600) {
                                btn.disabled = false;
                                btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                                btn.classList.add('bg-red-500', 'hover:bg-red-600');
                            } else {
                                btn.disabled = true;
                                btn.classList.add('bg-gray-400', 'cursor-not-allowed');
                                btn.classList.remove('bg-red-500', 'hover:bg-red-600');
                            }
                        }
                        updateSidebarBtn();
                        setInterval(updateSidebarBtn, 1000);
                    });
                </script>
                @include('absensi.checkout')
            @elseif (!$absensiToday)
                <button data-modal-target="absence-modal-{{ $type }}"
                    data-modal-toggle="absence-modal-{{ $type }}"
                    class="text-xs font-bold bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded-lg">
                    Check-In
                </button>
            @endif
            @include('absensi.create')
        @endif
    @endauth

    <!-- RIGHT: USER DROPDOWN -->
    <div class="relative">
        @php
            $fotoProfil = auth()->user()->foto_profil;
            $fotoUrl =
                $fotoProfil && Storage::disk('public')->exists($fotoProfil)
                    ? Storage::url($fotoProfil)
                    : 'https://ui-avatars.com/api/?background=2563EB&color=fff&name=' .
                        urlencode(auth()->user()->nama_lengkap);
        @endphp

        <!-- TRIGGER -->
        <button id="mobileUserMenuButton" data-dropdown-toggle="mobileUserDropdown"
            class="flex items-center gap-2 rounded-xl px-2 py-1 hover:bg-white/10 transition">
            <div class="text-right">
                <p class="text-xs font-semibold text-white leading-tight">
                    {{ auth()->user()->nama_lengkap }}
                </p>

                <p class="text-[10px] text-blue-100 capitalize">
                    {{ auth()->user()->role == 'admin' ? 'Administrator' : (auth()->user()->role == 'hr' ? 'HR Manager' : 'Employee') }}
                </p>
            </div>
            <img src="{{ $fotoUrl }}" class="w-9 h-9 rounded-full border-2 border-white object-cover">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-4 h-4 text-white">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        <!-- DROPDOWN -->
        <div id="mobileUserDropdown"
            class="hidden absolute right-0 mt-3 w-52 bg-white rounded-2xl shadow-xl overflow-hidden z-50">
            <!-- USER INFO -->
            <div class="px-4 py-3 border-b">
                <p class="text-sm font-semibold text-gray-800">
                    {{ auth()->user()->nama_lengkap }}
                </p>

                <p class="text-xs text-gray-500 truncate">
                    {{ auth()->user()->email }}
                </p>
            </div>

            <!-- MENU -->
            <div class="py-2">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.5 12a7.5 7.5 0 1115 0 7.5 7.5 0 01-15 0zm7.5-4.5v4.5l3 3" />
                    </svg>

                    Profile
                </a>
            </div>

            <!-- LOGOUT -->
            <div class="border-t py-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit"
                        class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15m-3-3h9m0 0l-3-3m3 3l-3 3" />
                        </svg>

                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<aside id="default-sidebar" data-drawer-backdrop="false"
    class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform
    -translate-x-full md:translate-x-0"
    aria-label="Sidebar">
    <div class="h-full px-4 py-6 overflow-y-auto bg-blue-800 text-white">
        <a href="/dashboard" class="flex items-center gap-2 ps-2.5 mb-5">
            <img src="{{ asset('assets/image/logo-partharis-white.png') }}" alt="Partharis Logo" class="h-8 w-auto">
            <span class="self-center text-lg text-white font-bold whitespace-nowrap">PARTHARIS</span>
        </a>
        <ul class="space-y-2 font-medium">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('admin.dashboard') || request()->routeIs('karyawan.dashboard') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>

            @if (Auth::user()->role == 'karyawan')
                <li>
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('profile.edit') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span class="ms-3">Profile</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('absensi.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('absensi.*') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        <span class="ms-3">Attendance</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('penggajian.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('penggajian.*') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                        <span class="ms-3">Payroll</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('cuti.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('cuti.*') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                        <span class="ms-3">Leave</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('changeday.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('changeday.*') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                        </svg>
                        <span class="ms-3">Change Day</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('performa.index') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('admin.performa.*') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                        <span class="ms-3">Performance</span>
                    </a>
                </li>
            @endif

            {{-- Role ADMIN/HR --}}
            @if (Auth::user()->role == 'hr' || Auth::user()->role == 'admin')
                <li>
                    <a href="{{ route('admin.karyawan') }}"
                        class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('admin.karyawan') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        <span class="ms-3">Employees</span>
                    </a>
                </li>
            @endif

            @auth
                @if (auth()->user()->isAdmin() || auth()->user()->isHR())
                    <li>
                        <a href="{{ route('admin.absensi.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('admin.absensi.*') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>
                            <span class="ms-3">Attendance</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.penggajian.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('admin.penggajian.*') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                            </svg>
                            <span class="ms-3">Payroll</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.leave.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('admin.leave.*') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            <span class="ms-3">Leave</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.changeday.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('admin.changeday.*') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                            </svg>
                            <span class="ms-3">Change Day</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.performa.index') }}"
                            class="flex items-center gap-3 px-4 py-2 rounded-full md:rounded-r-none md:w-[auto] {{ request()->routeIs('admin.performa.*') ? 'bg-gray-100 text-blue-800 font-semibold' : 'hover:bg-blue-700' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                            </svg>
                            <span class="ms-3">Performance</span>
                        </a>
                    </li>
                @endif
            @endauth
        </ul>
    </div>
</aside>
