@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto">
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <!-- TEXT -->
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">
                    Dashboard
                </h1>
                <p class="text-sm text-gray-700/80">
                    A Quick overview of your daily activity and important updates
                </p>
            </div>

            <!-- IMAGE / ILLUSTRATION -->
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="p-4 my-4 bg-white shadow rounded-2xl md:p-6">
            <div class="grid grid-cols-1 text-center divide-y sm:grid-cols-3 sm:divide-y-0 sm:divide-x">
                <!-- ITEM 1 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-xl font-semibold text-blue-900">Current Status</span>

                    @php
                        $isOnBreak = $isOnBreak ?? false;
                    @endphp

                    <h2 class="text-xl md:text-2xl font-semibold
                        @if (!$absensi) text-gray-400
                        @elseif ($isOnBreak)
                            text-blue-500
                        @elseif ($absensi->jam_pulang)
                            text-green-600
                        @else
                            text-yellow-600 @endif
                    "
                        id="statusText">

                        @if (!$absensi)
                            Not Checked In
                        @elseif ($isOnBreak)
                            On Break
                        @elseif ($absensi->jam_pulang)
                            Finished
                        @else
                            Working
                        @endif
                    </h2>
                </div>

                <!-- ITEM 2 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-xl font-semibold text-blue-900">Check-In Time</span>
                    <h2 class="text-xl md:text-2xl font-semibold {{ $absensi && $absensi->jam_masuk ? 'text-green-600' : 'text-gray-400' }}"
                        id="time">
                        {{ $absensi ? \Carbon\Carbon::parse($absensi->jam_masuk)->format('H:i:s') : 'Not Checked In' }}
                    </h2>
                </div>

                <!-- ITEM 3 -->
                <div class="flex flex-col gap-1 px-4 py-2">
                    <span class="text-xl font-semibold text-blue-900">Working Hours</span>
                    <h2 class="text-xl md:text-2xl font-semibold font-mono tracking-wider {{ $absensi && $absensi->jam_masuk ? 'text-gray-400' : 'text-gray-400' }}"
                        id="working">
                        @if ($absensi && $absensi->jam_pulang)
                            @php
                                $hours = floor($absensi->total_jam_kerja);
                                $minutes = floor(($absensi->total_jam_kerja - $hours) * 60);
                                $seconds = floor((($absensi->total_jam_kerja - $hours) * 60 - $minutes) * 60);
                                $formattedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                            @endphp
                            {{ $formattedTime }}
                        @else
                            00:00:00
                        @endif
                    </h2>
                </div>
            </div>
        </div>

        <div class="rounded-base">
            <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- CARD 1 -->
                <div class="p-5 bg-white shadow rounded-2xl">
                    <p class="mb-2 text-sm text-gray-500">Leave Record</p>

                    <h2 class="text-4xl font-bold text-blue-900">{{ $cutiTerpakai }} / {{ $totalCuti }}</h2>
                    <p class="mb-4 text-sm text-gray-500">Days Used</p>

                    <!-- PROGRESS -->
                    <div class="space-y-2 text-xs">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Annual Leave</span>
                                <span>{{ $totalCuti > 0 ? round(($terpakaiTahunan / $kuotaCutiTahunan) * 100) : 0 }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-blue-500 rounded-full"
                                    style="width: {{ $totalCuti > 0 ? round(($terpakaiTahunan / $kuotaCutiTahunan) * 100) : 0 }}%">
                                </div>
                            </div>
                            <small class="text-gray-400">{{ $terpakaiTahunan }} / {{ $kuotaCutiTahunan }}</small>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Sick Leave</span>
                                <span>{{ $totalCuti > 0 ? round(($terpakaiSakit / $kuotaCutiSakit) * 100) : 0 }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-blue-400 rounded-full"
                                    style="width: {{ $totalCuti > 0 ? round(($terpakaiSakit / $kuotaCutiSakit) * 100) : 0 }}%">
                                </div>
                            </div>
                            <small class="text-gray-400">{{ $terpakaiSakit }} / {{ $kuotaCutiSakit }}</small>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Emergency Leave</span>
                                <span>{{ $totalCuti > 0 ? round(($terpakaiKepentingan / $kuotaCutiKepentingan) * 100) : 0 }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-blue-400 rounded-full"
                                    style="width: {{ $totalCuti > 0 ? round(($terpakaiKepentingan / $kuotaCutiKepentingan) * 100) : 0 }}%">
                                </div>
                            </div>
                            <small class="text-gray-400">{{ $terpakaiKepentingan }} / {{ $kuotaCutiKepentingan }}</small>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Other Leave (Melahirkan)</span>
                                <span>{{ $totalCuti > 0 ? round(($terpakaiMelahirkan / $kuotaCutiMelahirkan) * 100) : 0 }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-blue-400 rounded-full"
                                    style="width: {{ $totalCuti > 0 ? round(($terpakaiMelahirkan / $kuotaCutiMelahirkan) * 100) : 0 }}%">
                                </div>
                            </div>
                            <small class="text-gray-400">{{ $terpakaiMelahirkan }} / {{ $kuotaCutiMelahirkan }}</small>
                        </div>
                    </div>
                </div>


                <!-- CARD 2 (DONUT) -->
                <div class="flex flex-col items-center justify-center p-5 bg-white shadow rounded-2xl">
                    <p class="mb-4 text-sm text-gray-500">Employee's Task Record</p>
                    <div id="donutChart"></div>
                </div>


                <!-- CARD 3 -->
                <div class="p-5 bg-white shadow rounded-2xl">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm text-gray-500">Announcements</p>
                    </div>

                    <div class="space-y-3 text-sm text-gray-600">
                        @foreach ($attachment as $item)
                            <div>
                                <a href="javascript:void(0)" onclick="showDetail({{ $item->id }})"
                                    class="cursor-pointer">
                                    <div class="flex justify-between text-sm">
                                        <span
                                            class="font-medium">{{ \Illuminate\Support\Str::limit($item->judul, 20, '...') }}</span>
                                        <span
                                            class="text-xs text-yellow-500">{{ $item->tanggal_terbit->format('d M Y') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400">
                                        {{ \Illuminate\Support\Str::words($item->konten, 10, '...') }}
                                    </p>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3 text-right">
                        <a href="{{ route('pengumuman.index') }}" class="text-xs text-cyan-500 hover:underline">View
                            All</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-base">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                <!-- LEFT: TABLE -->
                <div class="p-6 bg-white shadow md:col-span-2 rounded-2xl">

                    <!-- HEADER -->
                    <h2 class="mb-4 text-lg font-semibold text-gray-800">My Attendance</h2>

                    <!-- STATS AND FILTER YEAR -->
                    <div class="flex flex-col gap-4 mb-4 lg:flex-row lg:items-center lg:justify-between">

                        <!-- STATISTICS -->
                        <div class="grid grid-cols-4 gap-4 text-center">

                            <div class="flex items-end justify-center">
                                <p id="presentCount" class="text-2xl font-bold leading-none text-blue-900">
                                    0
                                </p>

                                <p class="text-xs text-gray-500">
                                    Present
                                </p>
                            </div>

                            <div class="flex items-end justify-center">
                                <p id="permissionCount" class="text-2xl font-bold leading-none text-blue-900">
                                    0
                                </p>

                                <p class="text-xs text-gray-500">
                                    Permission
                                </p>
                            </div>

                            <div class="flex items-end justify-center">
                                <p id="sickCount" class="text-2xl font-bold leading-none text-blue-900">
                                    0
                                </p>

                                <p class="text-xs text-gray-500">
                                    Sick
                                </p>
                            </div>

                            <div class="flex items-end justify-center">
                                <p id="pendingCount" class="text-2xl font-bold leading-none text-blue-900">
                                    0
                                </p>

                                <p class="text-xs text-gray-500">
                                    Pending
                                </p>
                            </div>

                        </div>

                        <!-- FILTER -->
                        <div class="w-full sm:w-52 lg:w-auto">

                            <select id="filterBulan"
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                                @foreach ($months as $key => $month)
                                    <option value="{{ $key }}" {{ now()->month == $key ? 'selected' : '' }}>
                                        {{ $month }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>

                    <!-- TABLE -->
                    <div class="relative mt-6">
                        <!-- LOADER -->
                        <div id="attendanceLoader"
                            class="absolute inset-0 z-10 flex flex-col items-center justify-center hidden bg-white/70 backdrop-blur-sm rounded-2xl">

                            <div class="w-8 h-8 border-b-2 border-blue-500 rounded-full animate-spin"></div>
                            <p class="mt-3 text-sm text-gray-500">
                                Loading attendance...
                            </p>
                        </div>

                        <!-- TABLE CONTAINER -->
                        <div class="overflow-hidden border border-gray-100 rounded-xl">
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <!-- HEAD -->
                                    <thead class="border-b border-gray-100 bg-gray-50">
                                        <tr class="text-left">
                                            <th
                                                class="px-6 py-4 text-xs font-semibold tracking-wide text-gray-500 uppercase">
                                                Date
                                            </th>
                                            <th
                                                class="px-6 py-4 text-xs font-semibold tracking-wide text-gray-500 uppercase">
                                                Check-In
                                            </th>
                                            <th
                                                class="px-6 py-4 text-xs font-semibold tracking-wide text-gray-500 uppercase">
                                                Check-Out
                                            </th>
                                            <th
                                                class="px-6 py-4 text-xs font-semibold tracking-wide text-gray-500 uppercase">
                                                Status
                                            </th>
                                        </tr>
                                    </thead>

                                    <!-- BODY -->
                                    <tbody id="attendanceTable" class="bg-white divide-y divide-gray-100">
                                        <!-- DYNAMIC CONTENT -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- PAGINATION -->
                        <div id="attendancePagination" class="flex items-center justify-between mt-4"></div>
                    </div>
                </div>

                <!-- RIGHT: CALENDAR -->
                <div class="p-6 bg-white shadow rounded-3xl">
                    <div id="calendarContainer"></div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL DETAIL PENGUMUMAN --}}
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">

        <div class="relative w-full max-w-2xl">
            <div class="overflow-hidden bg-white shadow-2xl rounded-3xl animate-fadeIn">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                    <div>
                        <h3 class="text-xl font-bold text-blue-900">Announcements Detail</h3>
                        <p class="mt-1 text-sm text-gray-500">Complete Announcements Information</p>
                    </div>
                    <button onclick="closeDetailModal()"
                        class="flex items-center justify-center w-10 h-10 transition rounded-xl hover:bg-gray-100">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="detailContent" class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar"></div>
            </div>
        </div>
    </div>

    {{-- EVENT DETAIL MODAL --}}
    @include('components.modals.event-detail-modal')
@endsection

@push('scripts')
    {{-- DONUT CHART --}}
    <script>
        var options = {
            chart: {
                type: 'donut',
                height: 250
            },
            series: [80, 10, 10],
            labels: ['Done', 'In Progress', 'To-Do'],
            colors: ['#06b6d4', '#4ade80', '#f43f5e'],
            legend: {
                position: 'bottom'
            },
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Progress',
                                formatter: function() {
                                    return '80%'
                                }
                            }
                        }
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#donutChart"), options);
        chart.render();
    </script>

    {{-- CALENDAR COMPONENT --}}
    <script>
        class CalendarComponent {
            constructor(containerId, options = {}) {
                this.container = document.getElementById(containerId);
                this.currentDate = new Date();
                this.selectedDate = null;
                this.events = [];
                this.onDateClick = options.onDateClick || null;
                this.onEventClick = options.onEventClick || null;

                if (this.container) {
                    this.init();
                }
            }

            init() {
                this.render();
                this.loadEvents();
            }

            async loadEvents() {
                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth() + 1;

                try {
                    const response = await fetch(`/calendar/events?year=${year}&month=${month}`);
                    const data = await response.json();

                    if (data.success) {
                        this.events = data.events;
                        this.renderEvents();
                    }
                } catch (error) {
                    console.error('Error loading events:', error);
                }
            }

            render() {
                if (!this.container) return;

                const year = this.currentDate.getFullYear();
                const month = this.currentDate.getMonth();

                const firstDay = new Date(year, month, 1).getDay();
                const lastDate = new Date(year, month + 1, 0).getDate();

                let html = `
                    <div class="flex items-center justify-between mb-4 calendar-header">
                        <h2 class="text-xl font-semibold text-gray-800">
                            ${this.currentDate.toLocaleString('default', { month: 'long', year: 'numeric' })}
                        </h2>
                        <div class="flex gap-2">
                            <button class="flex items-center justify-center w-8 h-8 transition bg-gray-100 rounded-lg calendar-prev hover:bg-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button class="flex items-center justify-center w-8 h-8 transition bg-gray-100 rounded-lg calendar-next hover:bg-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 gap-1 mb-2 calendar-weekdays">
                        <div class="py-2 text-xs font-medium text-center text-gray-400">Sun</div>
                        <div class="py-2 text-xs font-medium text-center text-gray-400">Mon</div>
                        <div class="py-2 text-xs font-medium text-center text-gray-400">Tue</div>
                        <div class="py-2 text-xs font-medium text-center text-gray-400">Wed</div>
                        <div class="py-2 text-xs font-medium text-center text-gray-400">Thu</div>
                        <div class="py-2 text-xs font-medium text-center text-gray-400">Fri</div>
                        <div class="py-2 text-xs font-medium text-center text-gray-400">Sat</div>
                    </div>
                    <div class="grid grid-cols-7 gap-1 calendar-days">
                `;

                // Empty cells for days before month starts
                for (let i = 0; i < firstDay; i++) {
                    html += `<div class="aspect-square"></div>`;
                }

                // Days of the month
                for (let day = 1; day <= lastDate; day++) {
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const isToday = this.isToday(year, month, day);
                    const dayEvents = this.getEventsForDate(dateStr);

                    html += `
                        <div class="calendar-day aspect-square p-1 ${isToday ? 'ring-2 ring-blue-500 rounded-lg' : ''}" data-date="${dateStr}">
                            <button class="flex flex-col items-center justify-start w-full h-full p-1 transition rounded-lg day-btn hover:bg-gray-50">
                                <span class="text-sm font-medium ${isToday ? 'text-blue-600' : 'text-gray-700'}">${day}</span>
                                <div class="event-indicators mt-1 flex flex-wrap gap-0.5 justify-center">
                                    ${this.getEventIndicators(dayEvents)}
                                </div>
                            </button>
                        </div>
                    `;
                }

                html += `</div>`;

                this.container.innerHTML = html;
                this.attachEventListeners();
            }

            renderEvents() {
                const days = this.container.querySelectorAll('.calendar-day');

                days.forEach(day => {
                    const date = day.getAttribute('data-date');
                    const dayEvents = this.getEventsForDate(date);
                    const indicatorsContainer = day.querySelector('.event-indicators');

                    if (indicatorsContainer) {
                        indicatorsContainer.innerHTML = this.getEventIndicators(dayEvents);
                    }
                });
            }

            getEventsForDate(dateStr) {
                return this.events.filter(event => {
                    const eventStart = event.start_date;
                    const eventEnd = event.end_date;

                    if (eventStart === eventEnd) {
                        return eventStart === dateStr;
                    }

                    return dateStr >= eventStart && dateStr <= eventEnd;
                });
            }

            getEventIndicators(events) {
                const colors = {
                    'blue': 'bg-blue-500',
                    'green': 'bg-green-500',
                    'yellow': 'bg-yellow-500',
                    'red': 'bg-red-500',
                    'purple': 'bg-purple-500'
                };

                const topEvents = events.slice(0, 3);

                return topEvents.map(event => {
                    const colorClass = colors[event.color] || 'bg-gray-500';
                    return `<div class="w-1.5 h-1.5 rounded-full ${colorClass}" title="${event.title}"></div>`;
                }).join('');
            }

            isToday(year, month, day) {
                const today = new Date();
                return today.getFullYear() === year &&
                    today.getMonth() === month &&
                    today.getDate() === day;
            }

            attachEventListeners() {
                // Prev button
                const prevBtn = this.container.querySelector('.calendar-prev');
                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                        this.render();
                        this.loadEvents();
                    });
                }

                // Next button
                const nextBtn = this.container.querySelector('.calendar-next');
                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                        this.render();
                        this.loadEvents();
                    });
                }

                // Day buttons
                const dayBtns = this.container.querySelectorAll('.day-btn');
                dayBtns.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const dayDiv = btn.closest('.calendar-day');
                        const date = dayDiv.getAttribute('data-date');
                        const events = this.getEventsForDate(date);
                        this.showDateEvents(date, events);
                    });
                });
            }

            showDateEvents(date, events) {
                if (events.length === 0) {
                    Swal.fire({
                        title: `No Events`,
                        text: `No events scheduled on ${date}`,
                        icon: 'info',
                        confirmButtonText: 'Close',
                        customClass: {
                            popup: 'rounded-2xl'
                        }
                    });
                    return;
                }

                let eventListHtml = '<div class="space-y-2 overflow-y-auto max-h-96">';

                events.forEach(event => {
                    const colorBg = {
                        'blue': 'bg-blue-100',
                        'green': 'bg-green-100',
                        'yellow': 'bg-yellow-100',
                        'red': 'bg-red-100',
                        'purple': 'bg-purple-100'
                    };

                    eventListHtml += `
                        <div class="p-3 transition border border-gray-100 cursor-pointer rounded-xl hover:bg-gray-50"
                             onclick="openEventDetailModal('${event.id}', '${event.type}'); Swal.close();">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full ${colorBg[event.color] || 'bg-gray-100'} flex items-center justify-center">
                                    <span class="text-lg">${event.icon}</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">${this.escapeHtml(event.title)}</p>
                                    <p class="text-xs text-gray-500">${this.escapeHtml(event.description.substring(0, 80))}${event.description.length > 80 ? '...' : ''}</p>
                                </div>
                            </div>
                        </div>
                    `;
                });

                eventListHtml += '</div>';

                Swal.fire({
                    title: `Events on ${date}`,
                    html: eventListHtml,
                    confirmButtonText: 'Close',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg'
                    }
                });
            }

            escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            refresh() {
                this.loadEvents();
            }
        }

        // Initialize calendar
        document.addEventListener('DOMContentLoaded', function() {
            const calendarContainer = document.getElementById('calendarContainer');
            if (calendarContainer) {
                window.calendar = new CalendarComponent('calendarContainer');
            }
        });
    </script>

    {{-- WORKING TIME & STATUS UPDATE WITH BREAKS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let syncInterval = null;
            let checkInTime = null;
            let breaksData = [];
            let isCurrentlyOnBreak = false;
            let displayUpdateInterval = null;

            @if ($absensi && !$absensi->jam_pulang)
                const checkInDate = "{{ $absensi->tanggal->format('Y-m-d') }}";
                const checkInTimeStr = "{{ $absensi->jam_masuk }}";
                checkInTime = new Date(`${checkInDate}T${checkInTimeStr}`);

                function calculateTotalBreakSeconds() {
                    let totalBreakSeconds = 0;
                    const now = new Date();

                    if (!breaksData || breaksData.length === 0) return 0;

                    breaksData.forEach(breakItem => {
                        if (breakItem.start) {
                            const breakStart = new Date(breakItem.start);
                            let breakEnd = null;

                            if (breakItem.end) {
                                breakEnd = new Date(breakItem.end);
                            } else if (breakItem.is_active) {
                                breakEnd = now;
                            }

                            if (breakEnd && breakEnd > breakStart) {
                                totalBreakSeconds += Math.floor((breakEnd - breakStart) / 1000);
                            }
                        }
                    });

                    return totalBreakSeconds;
                }

                function calculateWorkingHours() {
                    const now = new Date();
                    const totalSeconds = Math.floor((now - checkInTime) / 1000);

                    if (totalSeconds < 0) return {
                        hours: 0,
                        minutes: 0,
                        seconds: 0,
                        formatted: '00:00:00'
                    };

                    const breakSeconds = calculateTotalBreakSeconds();
                    const workingSeconds = Math.max(0, totalSeconds - breakSeconds);

                    const hours = Math.floor(workingSeconds / 3600);
                    const minutes = Math.floor((workingSeconds % 3600) / 60);
                    const seconds = workingSeconds % 60;

                    return {
                        hours: hours,
                        minutes: minutes,
                        seconds: seconds,
                        formatted: `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
                    };
                }

                function updateDisplay() {
                    const workingElement = document.getElementById('working');
                    const statusElement = document.getElementById('statusText');

                    if (workingElement) {
                        const working = calculateWorkingHours();
                        workingElement.innerText = working.formatted;
                    }

                    if (statusElement) {
                        const shouldShowOnBreak = isCurrentlyOnBreak;
                        const currentText = statusElement.textContent;

                        if (shouldShowOnBreak && currentText !== 'On Break') {
                            statusElement.textContent = 'On Break';
                            statusElement.className = 'text-xl md:text-2xl font-semibold text-blue-500';
                        } else if (!shouldShowOnBreak && currentText === 'On Break') {
                            statusElement.textContent = 'Working';
                            statusElement.className = 'text-xl md:text-2xl font-semibold text-yellow-600';
                        }
                    }
                }

                function fetchBreaksData() {
                    $.ajax({
                        url: "{{ route('attendance.status') }}",
                        type: "GET",
                        dataType: 'json',
                        success: function(response) {
                            if (response.absensi && !response.absensi.jam_pulang) {
                                if (response.breaks && Array.isArray(response.breaks)) {
                                    breaksData = response.breaks;
                                }
                                isCurrentlyOnBreak = response.isOnBreak || false;

                                if (response.absensi.jam_masuk && !checkInTime) {
                                    const serverCheckIn = new Date(
                                        `${response.absensi.tanggal}T${response.absensi.jam_masuk}`);
                                    if (!isNaN(serverCheckIn.getTime())) {
                                        checkInTime = serverCheckIn;
                                    }
                                }

                                updateDisplay();
                            } else if (response.absensi && response.absensi.jam_pulang) {
                                if (displayUpdateInterval) clearInterval(displayUpdateInterval);
                                if (syncInterval) clearInterval(syncInterval);
                            }
                        },
                        error: function(error) {
                            console.error('Error fetching status:', error);
                        }
                    });
                }

                fetchBreaksData();
                displayUpdateInterval = setInterval(updateDisplay, 1000);
                syncInterval = setInterval(fetchBreaksData, 10000);

                window.addEventListener('beforeunload', function() {
                    if (displayUpdateInterval) clearInterval(displayUpdateInterval);
                    if (syncInterval) clearInterval(syncInterval);
                });
            @endif

            $(document).on('submit', 'form[action*="break"]', function(e) {
                setTimeout(function() {
                    if (typeof fetchBreaksData === 'function') fetchBreaksData();
                }, 500);
            });
        });
    </script>

    {{-- ATTENDANCE TABLE --}}
    <script>
        $(document).ready(function() {
            loadAttendance($('#filterBulan').val());

            $('#filterBulan').on('change', function() {
                loadAttendance($(this).val());
            });

            function formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }

            function formatTime(timeString) {
                if (!timeString) return '-';
                let [hour, minute] = timeString.split(':');
                let date = new Date();
                date.setHours(hour);
                date.setMinutes(minute);
                return date.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }

            function statusBadge(status) {
                const badges = {
                    present: `<span class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Present</span>`,
                    permissions: `<span class="px-3 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">Permission</span>`,
                    sick: `<span class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-full">Sick</span>`,
                    pending: `<span class="px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-full">Pending</span>`
                };
                return badges[status] ?? badges['pending'];
            }

            function loadAttendance(bulan, page) {
                page = page || 1;
                $('#attendanceLoader').removeClass('hidden');
                $('#attendanceTable').html('');

                $.ajax({
                    url: "{{ route('attendance.filter') }}",
                    type: "GET",
                    data: {
                        bulan: bulan,
                        page: page
                    },
                    success: function(response) {
                        $('#presentCount').text(response.summary.present);
                        $('#permissionCount').text(response.summary.permission);
                        $('#sickCount').text(response.summary.sick);
                        $('#pendingCount').text(response.summary.pending);

                        let rows = '';

                        if (response.data.length > 0) {
                            response.data.forEach(item => {
                                rows += `
                                <tr class="transition hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-700">${formatDate(item.tanggal)}</td>
                                    <td class="px-6 py-4 text-gray-600">${formatTime(item.jam_masuk)}</td>
                                    <td class="px-6 py-4 text-gray-600">${formatTime(item.jam_pulang)}</td>
                                    <td class="px-6 py-4">${statusBadge(item.status_kehadiran)}</td>
                                </tr>`;
                            });
                        } else {
                            rows =
                                `<tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No attendance data found</td></tr>`;
                        }

                        $('#attendanceTable').html(rows);

                        let pagination = `
                            <div class="text-sm text-gray-500">Page ${response.pagination.current_page} of ${response.pagination.last_page}</div>
                            <div class="flex items-center gap-2">
                                <button ${!response.pagination.prev_page_url ? 'disabled' : ''}
                                    class="paginateBtn px-4 py-2 rounded-lg border text-sm ${!response.pagination.prev_page_url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}"
                                    data-page="${response.pagination.current_page - 1}">Previous</button>
                                <button ${!response.pagination.next_page_url ? 'disabled' : ''}
                                    class="paginateBtn px-4 py-2 rounded-lg border text-sm ${!response.pagination.next_page_url ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50'}"
                                    data-page="${response.pagination.current_page + 1}">Next</button>
                            </div>
                        `;
                        $('#attendancePagination').html(pagination);
                    },
                    complete: function() {
                        $('#attendanceLoader').addClass('hidden');
                    }
                });
            }

            $(document).on('click', '.paginateBtn:not([disabled])', function() {
                loadAttendance($('#filterBulan').val(), $(this).data('page'));
            });
        });
    </script>

    {{-- ANNOUNCEMENT MODAL --}}
    <script>
        function showDetail(id) {
            fetch(`/pengumuman/${id}`)
                .then(response => response.json())
                .then(data => {
                    const categoryClass = {
                        umum: 'bg-blue-100 text-blue-700',
                        kebijakan: 'bg-amber-100 text-amber-700',
                        pengumuman: 'bg-violet-100 text-violet-700',
                        event: 'bg-emerald-100 text-emerald-700',
                        penting: 'bg-red-100 text-red-700'
                    };

                    const badgeClass = categoryClass[data.kategori] || 'bg-gray-100 text-gray-700';
                    const publishDate = data.tanggal_terbit ? new Date(data.tanggal_terbit).toLocaleDateString(
                        'id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        }) : '-';

                    const content = `
                        <div class="space-y-6">
                            <div class="pb-4 border-b border-gray-100">
                                <h4 class="text-2xl font-bold leading-snug text-gray-800">${escapeHtml(data.judul)}</h4>
                                <div class="flex flex-wrap items-center gap-2 mt-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${badgeClass}">
                                        ${ ({ 'umum': 'General', 'kebijakan': 'Policy', 'pengumuman': 'Announcement', 'event': 'Event', 'penting': 'Important' })[data.kategori] || 'Uncategorized' }
                                    </span>
                                    <span class="text-xs text-gray-400">Published ${publishDate}</span>
                                </div>
                            </div>
                            <div class="p-5 border border-gray-100 bg-gray-50 rounded-2xl">
                                <p class="text-sm leading-7 text-gray-700 whitespace-pre-line">${escapeHtml(data.konten)}</p>
                            </div>
                            <!-- ATTACHMENT -->
                            ${data.lampiran ?
                                    `
                                                <div class="space-y-3">
                                                    <h5 class="text-sm font-semibold text-gray-700">
                                                        Attachment
                                                    </h5>

                                                    ${
                                                        /\.(jpg|jpeg|png|gif|webp)$/i.test(data.lampiran)
                                                        ? `
                                            <img
                                                src="/storage/${data.lampiran}"
                                                alt="Lampiran"
                                                class="w-full border border-gray-200 rounded-xl"
                                            >
                                        `
                                                        : /\.(pdf)$/i.test(data.lampiran)
                                                        ? `
                                            <iframe
                                                src="/storage/${data.lampiran}"
                                                class="w-full h-[600px] rounded-xl border border-gray-200"
                                            ></iframe>
                                        `
                                                        : `
                                            <div class="p-4 border border-blue-100 bg-blue-50 rounded-2xl">
                                                <a href="/storage/${data.lampiran}"
                                                    target="_blank"
                                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                                                    Download Attachment
                                                </a>
                                            </div>
                                        `
                                                    }
                                                </div>
                                            `
                                    : ''
                                }
                        </div>
                    `;

                    document.getElementById('detailContent').innerHTML = content;
                    document.getElementById('detailModal').classList.remove('hidden');
                })
                .catch(error => console.error('Error fetching announcement details:', error));
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
@endpush
