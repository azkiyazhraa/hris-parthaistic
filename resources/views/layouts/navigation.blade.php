<nav x-data="{ open: false, dropdownOpen: false }" class="bg-white/90 backdrop-blur-md shadow-md border-b border-gray-100 sticky top-0 z-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo -->
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    @auth
                        @if (auth()->user()->isAdmin() || auth()->user()->isHR())
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                                HRIS Management
                            </a>
                        @else
                            <a href="{{ route('karyawan.dashboard') }}"
                                class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                                HRIS Management
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                            HRIS Management
                        </a>
                    @endauth
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex md:ml-10 md:space-x-2 items-center">
                    @auth
                        @if (auth()->user()->isAdmin() || auth()->user()->isHR())
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Dashboard
                            </x-nav-link>

                            <x-nav-link :href="route('admin.karyawan')" :active="request()->routeIs('admin.karyawan')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Employee
                            </x-nav-link>

                            <x-nav-link :href="route('admin.absensi.index')" :active="request()->routeIs('admin.absensi.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Attendance
                            </x-nav-link>

                            <x-nav-link :href="route('admin.cuti.index')" :active="request()->routeIs('admin.cuti.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Leave
                            </x-nav-link>

                            <x-nav-link :href="route('admin.penggajian.index')" :active="request()->routeIs('admin.penggajian.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Payroll
                            </x-nav-link>

                            <!-- Menu Edit Gaji Saya - dengan pengecekan route exist -->
                            @php
                                $hasOwnSalaryRoute = Route::has('admin.penggajian.own.index');
                                $hasOwnCutiRoute = Route::has('admin.cuti.own.index');
                            @endphp

                            @if ($hasOwnSalaryRoute)
                                <x-nav-link :href="route('admin.penggajian.own.index')" :active="request()->routeIs('admin.penggajian.own.*')"
                                    class="px-3 py-2 rounded-lg text-sm font-medium">
                                    Edit My Salary 
                                </x-nav-link>
                            @endif

                            @if ($hasOwnCutiRoute)
                                <x-nav-link :href="route('admin.cuti.own.index')" :active="request()->routeIs('admin.cuti.own.*')"
                                    class="px-3 py-2 rounded-lg text-sm font-medium">
                                    My Leave
                                </x-nav-link>
                            @endif

                            <x-nav-link :href="route('admin.performa.index')" :active="request()->routeIs('admin.performa.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Performance
                            </x-nav-link>

                            <x-nav-link :href="route('admin.pengumuman.index')" :active="request()->routeIs('admin.pengumuman.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Announcement
                            </x-nav-link>
                        @else
                            <x-nav-link :href="route('karyawan.dashboard')" :active="request()->routeIs('karyawan.dashboard')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Dashboard
                            </x-nav-link>

                            <x-nav-link :href="route('absensi.index')" :active="request()->routeIs('absensi.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Attendance
                            </x-nav-link>

                            <x-nav-link :href="route('cuti.index')" :active="request()->routeIs('cuti.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Leave
                            </x-nav-link>

                            <x-nav-link :href="route('penggajian.index')" :active="request()->routeIs('penggajian.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Pay Slip
                            </x-nav-link>

                            <x-nav-link :href="route('performa.index')" :active="request()->routeIs('performa.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Performance
                            </x-nav-link>

                            <x-nav-link :href="route('pengumuman.index')" :active="request()->routeIs('pengumuman.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium">
                                Announcement
                            </x-nav-link>

                            <x-nav-link :href="route('notifikasi.index')" :active="request()->routeIs('notifikasi.*')"
                                class="px-3 py-2 rounded-lg text-sm font-medium relative">
                                Notification
                                <span id="notif-badge"
                                    class="absolute -top-1 -right-3 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 hidden animate-pulse"></span>
                            </x-nav-link>
                        @endif

                        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="px-3 py-2 rounded-lg text-sm font-medium">
                            Profile
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Right Section -->
            <div class="hidden md:flex items-center space-x-3">
                @auth
                    <div class="relative" x-data="{ dropdownOpen: false }">
                        <img class="h-9 w-9 rounded-full object-cover border"
                            src="{{ auth()->user()->foto_profil
                                ? Storage::url(auth()->user()->foto_profil)
                                : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->nama_lengkap) . '&background=0D8ABC&color=fff' }}"
                            alt="{{ auth()->user()->nama_lengkap }}"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama_lengkap) }}&background=0D8ABC&color=fff'">

                        <!-- Dropdown -->
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            class="absolute right-0 mt-3 w-60 bg-white rounded-xl shadow-xl py-2 z-50">

                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-blue-50">
                                My Profile 
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 text-red-600">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>

            <!-- Mobile Button -->
            <div class="md:hidden">
                <button @click="open = !open" class="p-2 rounded-lg hover:bg-blue-50 text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" @click.away="open = false" class="md:hidden border-t">
        <div class="px-4 py-3 space-y-1">
            @auth
                @if (auth()->user()->isAdmin() || auth()->user()->isHR())
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.karyawan')" :active="request()->routeIs('admin.karyawan')">
                        Employee
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.absensi.index')" :active="request()->routeIs('admin.absensi.*')">
                        Attendance
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.cuti.index')" :active="request()->routeIs('admin.cuti.*')">
                        Leave
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.penggajian.index')" :active="request()->routeIs('admin.penggajian.*')">
                        Payroll
                    </x-responsive-nav-link>
                    @if (Route::has('admin.penggajian.own.index'))
                        <x-responsive-nav-link :href="route('admin.penggajian.own.index')" :active="request()->routeIs('admin.penggajian.own.*')">
                            Edit My Salary
                        </x-responsive-nav-link>
                    @endif
                    @if (Route::has('admin.cuti.own.index'))
                        <x-responsive-nav-link :href="route('admin.cuti.own.index')" :active="request()->routeIs('admin.cuti.own.*')">
                            My Leave
                        </x-responsive-nav-link>
                    @endif
                    <x-responsive-nav-link :href="route('admin.performa.index')" :active="request()->routeIs('admin.performa.*')">
                        Performance
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.pengumuman.index')" :active="request()->routeIs('admin.pengumuman.*')">
                        Announcement
                    </x-responsive-nav-link>
                @else
                    <x-responsive-nav-link :href="route('karyawan.dashboard')" :active="request()->routeIs('karyawan.dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('absensi.index')" :active="request()->routeIs('absensi.*')">
                        Attendance
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('cuti.index')" :active="request()->routeIs('cuti.*')">
                        Leave
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('penggajian.index')" :active="request()->routeIs('penggajian.*')">
                        Pay Slip
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('performa.index')" :active="request()->routeIs('performa.*')">
                        Performance
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('pengumuman.index')" :active="request()->routeIs('pengumuman.*')">
                        Announcement
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('notifikasi.index')" :active="request()->routeIs('notifikasi.*')">
                        Notification
                    </x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">
                    Profile
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Logout
                    </x-responsive-nav-link>
                </form>
            @endauth
        </div>
    </div>
</nav>
