<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HRIS Management') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- TRIX EDITOR --}}
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>

    {{-- DATATABLES --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    {{-- SWEETALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- FLATPICKR --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Flatpickr: color Sundays and national holidays red */
        .flatpickr-day.fp-sunday  { color: #ef4444 !important; }
        .flatpickr-day.fp-holiday { color: #ef4444 !important; font-weight: 600; }
        .flatpickr-day.fp-holiday::after {
            content: '•';
            display: block;
            font-size: 8px;
            color: #ef4444;
            line-height: 0;
            margin-top: 2px;
        }
        /* Sunday + holiday selected states */
        .flatpickr-day.fp-sunday.selected,
        .flatpickr-day.fp-holiday.selected  { color: #fff !important; }
    </style>

    {{-- STYLES --}}
    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-100 md:bg-blue-800">

    @include('components.sidebar')

    <div class="flex flex-col h-screen p-4 bg-gray-100 sidebar-offset md:rounded-tl-2xl md:rounded-bl-2xl">

        <div class="flex items-center justify-between hidden md:flex">
            <!-- LEFT (HAMBURGER) -->
            <button class="p-2 rounded-lg hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6 text-gray-700">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                </svg>
            </button>

            <!-- RIGHT SIDE -->
            <div class="flex items-center gap-4">
                @php
                    $type = 'desktop';
                @endphp
                @auth
                    @if (Auth::user()->role == 'karyawan')
                        <div class="flex items-center gap-2">
                            <!-- Check in - Check out -->
                            @if ($absensiToday && !$absensiToday->jam_pulang)
                                <button id="checkout-btn"
                                    data-modal-target="absence-modal-checkout-{{ $type }}-{{ optional($absensiToday)->id }}"
                                    data-modal-toggle="absence-modal-checkout-{{ $type }}-{{ optional($absensiToday)->id }}"
                                    class="px-2 py-1 text-xs font-bold text-white bg-gray-400 rounded-lg cursor-not-allowed"
                                    disabled>
                                    Check-Out
                                </button>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {

                                        const checkIn = new Date(
                                            "{{ date('Y-m-d', strtotime($absensiToday->tanggal)) }}T{{ $absensiToday->jam_masuk }}"
                                        );

                                        const btn = document.getElementById('checkout-btn');

                                        function updateTimer() {
                                            const now = new Date();
                                            const diff = now - checkIn;

                                            if (diff < 0) return;

                                            const totalSeconds = Math.floor(diff / 1000);

                                            if (totalSeconds >= 7 * 3600) {
                                                btn.disabled = false;
                                                btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                                                btn.classList.add('bg-red-500', 'hover:bg-red-600');
                                            } else {
                                                btn.disabled = true;
                                                btn.classList.add('bg-gray-400', 'cursor-not-allowed');
                                                btn.classList.remove('bg-red-500', 'hover:bg-red-600');
                                            }
                                        }

                                        updateTimer();
                                        setInterval(updateTimer, 1000);
                                    });
                                </script>

                                @include('absensi.checkout')
                            @elseif (!$absensiToday)
                                <button data-modal-target="absence-modal-{{ $type }}"
                                    data-modal-toggle="absence-modal-{{ $type }}"
                                    class="px-2 py-1 text-xs font-bold text-white bg-green-500 rounded-lg hover:bg-green-600">
                                    Check-In
                                </button>
                            @endif
                            <!-- Break -->
                            @php
                                $isCheckedIn = $absensiToday && $absensiToday->jam_masuk && !$absensiToday->jam_pulang;

                                $activeBreak = null;

                                if ($absensiToday) {
                                    $activeBreak = \App\Models\BreakTime::where('absensi_id', $absensiToday->id)
                                        ->whereNull('break_end')
                                        ->latest()
                                        ->first();
                                }
                            @endphp

                            {{-- BREAK BUTTON --}}
                            @if ($absensiToday)

                                @if ($activeBreak)
                                    {{-- END BREAK --}}
                                    <form action="{{ route('break.end', $absensiToday->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs font-bold bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded-lg
                                    {{ !$isCheckedIn ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            {{ !$isCheckedIn ? 'disabled' : '' }}>
                                            End Break
                                        </button>
                                    </form>
                                @else
                                    {{-- START BREAK --}}
                                    <form action="{{ route('break.start', $absensiToday->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="text-xs font-bold bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded-lg
                                    {{ !$isCheckedIn ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            {{ !$isCheckedIn ? 'disabled' : '' }}>
                                            Break
                                        </button>
                                    </form>
                                @endif
                            @else
                                {{-- BELUM CHECK-IN --}}
                                <button
                                    class="px-2 py-1 text-xs font-bold bg-yellow-500 rounded-lg opacity-50 cursor-not-allowed"
                                    disabled>
                                    Break
                                </button>
                            @endif

                        </div>

                        @include('absensi.create')
                    @endif
                @endauth

                <div id="clock" class="px-2 py-1 text-sm font-semibold text-white bg-green-500 rounded-lg"></div>

                <!-- NOTIFICATION -->
                <div class="relative">
                    <!-- TRIGGER -->
                    <button id="notificationButton" data-dropdown-toggle="notificationDropdown"
                        class="relative p-2 transition rounded-xl hover:bg-gray-100">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 0 1-5.714 0M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7" />
                        </svg>

                        <!-- DOT -->
                        <span id="notif-dot"
                            class="absolute hidden w-2 h-2 bg-red-500 rounded-full top-2 right-2 animate-pulse">
                        </span>
                    </button>

                    <!-- DROPDOWN -->
                    <div id="notificationDropdown"
                        class="hidden absolute right-0 mt-3 w-[340px] bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden z-50">

                        <!-- HEADER -->
                        <div class="flex items-center justify-between px-4 py-3 border-b">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800">
                                    Notifications
                                </h3>

                                <p id="notif-count" class="text-xs text-gray-400">0</p>
                            </div>

                            <button type="submit" id="markAllRead" class="text-xs text-blue-600 hover:text-blue-700">
                                Mark all read
                            </button>
                        </div>

                        <!-- LIST -->
                        <div id="notif-container" class="max-h-[350px] overflow-y-auto"></div>

                        <!-- FOOTER -->
                        <div class="px-4 py-3 text-center border-t">
                            <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                View all notifications
                            </a>
                        </div>
                    </div>
                </div>

                <!-- PROFILE -->
                <div class="relative">
                    <!-- TRIGGER -->
                    <div id="userMenuButton" data-dropdown-toggle="userDropdown"
                        class="flex items-center gap-2 cursor-pointer">

                        @php
                            $fotoProfil = auth()->user()->foto_profil;
                            $fotoUrl =
                                $fotoProfil && Storage::disk('public')->exists($fotoProfil)
                                    ? Storage::url($fotoProfil)
                                    : 'https://ui-avatars.com/api/?background=0D8F81&color=fff&name=' .
                                        urlencode(auth()->user()->nama_lengkap);
                        @endphp

                        <img src="{{ $fotoUrl }}" class="object-cover border rounded-full w-9 h-9">

                        <div class="hidden text-left md:block">
                            <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->nama_lengkap }}</p>
                            <p class="text-xs text-gray-500">
                                {{ auth()->user()->role == 'karyawan' ? 'Employee' : ucfirst(auth()->user()->role) }}
                            </p>
                        </div>

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </div>

                    <!-- DROPDOWN -->
                    <div id="userDropdown"
                        class="absolute right-0 z-50 hidden w-48 mt-2 bg-white divide-y divide-gray-100 shadow rounded-xl">

                        <div class="px-4 py-3">
                            <p class="text-sm text-gray-900">{{ auth()->user()->nama_lengkap }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>

                        <ul class="py-2 text-sm text-gray-700">
                            <li>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100">
                                    Profile
                                </a>
                            </li>
                        </ul>

                        <div class="py-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full px-4 py-2 text-left text-red-600 hover:bg-red-50">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 mt-4 md:overflow-y-auto">
            @yield('content')
        </div>

        {{-- FOOTER --}}
        <footer
            class="pb-3 mt-2 text-xs text-center text-gray-500 md:mt-4 md:flex md:items-center md:justify-between md:pb-0 md:text-left">

            <p>&copy; 2026 PARTHARIS. All rights reserved.</p>

            <p class="hidden md:block">
                Version 1.0.0
            </p>
        </footer>
    </div>

    {{-- APEX CHART --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    {{-- JAVASCRIPT FOR FLOWBITE --}}
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>

    {{-- FLATPICKR --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- JavaScript --}}
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ==============================
            // DATATABLE INIT
            // ==============================
            if (window.$ && $('#myTable').length) {
                $('#myTable').DataTable({
                    responsive: true,
                    autoWidth: false,
                    pageLength: 10,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                        paginate: {
                            next: "Next",
                            previous: "Prev"
                        }
                    }
                });
            }

            // ==============================
            // SWEETALERT DELETE
            // ==============================
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    let form = this.closest('form');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Data will be deleted permanently!",
                        icon: 'warning',
                        showCancelButton: true,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg',
                            cancelButton: 'bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded-lg ml-2'
                        },
                        confirmButtonText: 'Yes, delete!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // ==============================
            // SWEETALERT SESSION
            // ==============================
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: @json(session('success')),
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: @json(session('error')),
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Failed',
                    html: `
                        <ul style="text-align:left;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    `
                });
            @endif

            // ==============================
            // CLOCK
            // ==============================
            function updateClock() {
                const now = new Date();
                const clock = document.getElementById('clock');

                if (clock) {
                    let hours = now.getHours().toString().padStart(2, '0');
                    let minutes = now.getMinutes().toString().padStart(2, '0');
                    let seconds = now.getSeconds().toString().padStart(2, '0');

                    clock.innerText = `${hours}:${minutes}:${seconds}`;
                }
            }

            updateClock();
            setInterval(updateClock, 1000);

            // ==============================
            // NOTIFICATION COUNT (AUTH ONLY)
            // ==============================
            @auth

            function updateNotifCount() {
                fetch('{{ route('notifikasi.unread-count') }}')
                    .then(res => res.json())
                    .then(data => {
                        const badge = document.getElementById('notif-badge');
                        if (!badge) return;

                        if (data.count > 0) {
                            badge.textContent = data.count;
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    })
                    .catch(() => console.log('Notif error'));
            }

            updateNotifCount();
            setInterval(updateNotifCount, 30000);
        @endauth

        });
    </script>

    {{-- tarik data notifikasi --}}
    <script>
        async function getNotifikasi() {
            try {
                const response = await fetch('/notifikasi');
                const data = await response.json();

                const notifContainer = document.getElementById('notif-container');
                const notifCount = document.getElementById('notif-count');
                const notifDot = document.getElementById('notif-dot');

                notifCount.innerText =
                    `${data.unreadCount} unread notifications`;

                // tampil/sembunyikan dot
                if (data.unreadCount > 0) {
                    notifDot.classList.remove('hidden');
                } else {
                    notifDot.classList.add('hidden');
                }

                if (data.notifikasi.length === 0) {
                    notifContainer.innerHTML = `
                        <div class="p-6 text-center text-gray-400">
                            No notifications
                        </div>
                    `;
                    return;
                }

                notifContainer.innerHTML = '';

                data.notifikasi.forEach(item => {

                    let color = 'blue';
                    let icon = `
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6v6l4 2" />
                    `;

                    let createdAt = new Date(item.created_at);

                    if (item.type === 'warning') {
                        color = 'yellow';
                    }

                    if (item.type === 'danger') {
                        color = 'red';
                    }

                    notifContainer.innerHTML += `
                    <a href="#"
                        class="flex gap-3 px-4 py-3 transition border-b border-gray-100 hover:bg-gray-50">

                        <div class="w-10 h-10 rounded-full
                            bg-${color}-100 text-${color}-700
                            flex items-center justify-center shrink-0">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="w-5 h-5">
                                ${icon}
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-700">
                                ${item.judul}
                            </p>

                            <p class="mt-1 text-xs text-gray-400">
                                ${createdAt.toLocaleString()}
                            </p>
                        </div>
                    </a>
                `;
                });
            } catch (error) {
                console.error('Error fetching notifications:', error);
            }
        }

        getNotifikasi();
        setInterval(getNotifikasi, 10000);


        // MARK ALL AS READ
        document.getElementById('markAllRead').addEventListener('click', () => {
            fetch('/notifikasi/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        getNotifikasi();
                    }
                })
                .catch(error => console.error('Error marking all notifications as read:', error));
        });
    </script>

    {{-- sidebar --}}
    <script>
        setInterval(() => {
            const sidebar = document.getElementById('default-sidebar');

            // hanya hapus backdrop kalau sidebar SUDAH tertutup
            if (sidebar.classList.contains('-translate-x-full')) {
                document.querySelectorAll('div').forEach(el => {
                    const cls = el.className;
                    if (
                        typeof cls === 'string' &&
                        cls.includes('fixed') &&
                        cls.includes('inset-0') &&
                        cls.includes('z-30')
                    ) {
                        el.remove();
                    }
                });
            }

        }, 100);
    </script>
</body>

</html>
