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
                    Monitor employee data, attendance, and performance in one place
                </p>
            </div>

            <!-- IMAGE / ILLUSTRATION -->
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="py-4 rounded-base">
            <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-3">
                <!-- CARD 1 -->
                <div class="p-5 bg-white shadow rounded-2xl">
                    <p class="mb-2 text-sm text-gray-500">Total Employee</p>

                    <h2 class="text-3xl font-bold text-blue-900">{{ $totalKaryawan }}</h2>
                    <p class="mb-4 text-sm text-gray-500">Employees</p>

                    <!-- PROGRESS -->
                    <div class="space-y-2 text-xs">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Full-time</span>
                                <span>{{ number_format($fulltimePercent, 1) }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-blue-500 rounded-full" style="width: {{ round($fulltimePercent) }}%">
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Contract</span>
                                <span>{{ number_format($contractPercent, 1) }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-green-500 rounded-full" style="width: {{ round($contractPercent) }}%">
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Internship</span>
                                <span>{{ number_format($internshipPercent, 1) }}%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-purple-500 rounded-full" style="width: {{ round($internshipPercent) }}%">
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Resigned/Terminated</span>
                                <span>{{ $resignedEmployees }}</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full">
                                <div class="h-2 bg-red-500 rounded-full"
                                    style="width: {{ $totalKaryawan > 0 ? round(($resignedEmployees / $totalKaryawan) * 100) : 0 }}%">
                                </div>
                            </div>
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
                        <button data-modal-target="announcement-modal" data-modal-toggle="announcement-modal"
                            class="text-xl font-bold hover:text-blue-600">
                            +
                        </button>
                        @include('components.Announcements.create')
                    </div>

                    @if ($attachment->count() > 1)
                        <div class="space-y-3 text-sm text-gray-600">
                            @foreach ($attachment as $item)
                                <div>
                                    <a onclick="showDetail({{ $item->id }})" href="javascript:void(0)"
                                        class="cursor-pointer">

                                        <div class="flex justify-between text-sm">
                                            <span class="font-medium">
                                                {{ \Illuminate\Support\Str::limit($item->judul, 20, '...') }}
                                            </span>

                                            <span class="text-{{ $item->status == 1 ? 'green' : 'yellow' }}-500 text-xs">
                                                {{ $item->tanggal_terbit->format('d M Y') }}
                                            </span>
                                        </div>

                                        <p class="text-xs text-gray-400">
                                            {{ \Illuminate\Support\Str::words($item->konten, 10, '...') }}
                                        </p>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3 text-right">
                            <a href="{{ route('admin.pengumuman.index') }}" class="text-xs text-cyan-500 hover:underline">
                                View All
                            </a>
                        </div>
                    @elseif($attachment->count() === 1)
                        @php
                            $item = $attachment->first();
                        @endphp

                        <div class="space-y-3">
                            @if ($item->lampiran && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $item->lampiran))
                                <div class="w-full aspect-video overflow-hidden rounded-xl border border-gray-200">
                                    <img src="{{ asset('storage/' . $item->lampiran) }}" alt="Lampiran"
                                        class="w-full h-full object-cover">
                                </div>
                            @elseif ($item->lampiran)
                                <a href="{{ asset('storage/' . $item->lampiran) }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs text-blue-600 border border-blue-200 rounded-lg hover:bg-blue-50">
                                    View Attachment
                                </a>
                            @endif

                            <div>
                                <h4 class="font-semibold text-gray-800">
                                    {{ $item->judul }}
                                </h4>

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $item->tanggal_terbit->format('d F Y') }}
                                </p>

                                @php
                                    $contentText = strip_tags($item->konten);
                                @endphp

                                <div class="mt-3 text-xs text-gray-600">
                                    {{ \Illuminate\Support\Str::limit($contentText, 100, '...') }}
                                </div>

                                <div class="mt-4 flex items-center justify-between">
                                    <a href="javascript:void(0)" onclick="showDetail({{ $item->id }})"
                                        class="text-xs text-cyan-500 hover:underline">
                                        Read More
                                    </a>
                                    <a href="{{ route('admin.pengumuman.index') }}"
                                        class="text-xs text-cyan-500 hover:underline">
                                        Manage →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="rounded-base">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- LEFT: TABLE -->
                <div class="col-span-2 p-6 bg-white shadow rounded-2xl">
                    <!-- HEADER -->
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-lg font-semibold text-gray-800">Today Attendance</h2>
                        <span>
                            {{ \Carbon\Carbon::now()->locale('en')->isoFormat('dddd, D MMMM YYYY') }}
                        </span>
                    </div>

                    {{-- STATISTICS --}}
                    <div class="flex justify-between mb-4">
                        <div class="grid grid-cols-4 gap-4 text-center">
                            <div class="flex items-end justify-center">
                                <p class="text-2xl font-bold leading-none text-blue-900">{{ $statistics['present'] }}</p>
                                <p class="ml-1 text-xs text-gray-500">Present</p>
                            </div>
                            <div class="flex items-end justify-center">
                                <p class="text-2xl font-bold leading-none text-blue-900">{{ $statistics['permit'] }}</p>
                                <p class="ml-1 text-xs text-gray-500">Permission</p>
                            </div>
                            <div class="flex items-end justify-center">
                                <p class="text-2xl font-bold leading-none text-blue-900">{{ $statistics['sick'] }}</p>
                                <p class="ml-1 text-xs text-gray-500">Sick</p>
                            </div>
                            <div class="flex items-end justify-center">
                                <p class="text-2xl font-bold leading-none text-blue-900">{{ $statistics['pending'] }}</p>
                                <p class="ml-1 text-xs text-gray-500">Pending</p>
                            </div>
                        </div>
                    </div>

                    <!-- TABLE -->
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 bg-gray-100">
                                <tr>
                                    <th class="p-4 whitespace-nowrap">Name</th>
                                    <th class="p-4 whitespace-nowrap">Date</th>
                                    <th class="p-4 whitespace-nowrap">Check-In</th>
                                    <th class="p-4 whitespace-nowrap">Check-Out</th>
                                    <th class="p-4 whitespace-nowrap">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @forelse($absensi as $item)
                                    <tr>
                                        <td class="p-3">
                                            <div class="flex items-center gap-3">
                                                @php
                                                    $fotoUrl = $item->karyawan->foto_profil
                                                        ? Storage::url($item->karyawan->foto_profil)
                                                        : 'https://ui-avatars.com/api/?background=2563EB&color=fff&size=100&name=' .
                                                            urlencode($item->karyawan->nama_lengkap);
                                                @endphp
                                                <div
                                                    class="overflow-hidden bg-blue-100 border border-blue-200 rounded-full w-9 h-9 shrink-0">
                                                    <img src="{{ $fotoUrl }}"
                                                        alt="{{ $item->karyawan->nama_lengkap }}"
                                                        class="object-cover w-full h-full" loading="lazy">
                                                </div>
                                                <span
                                                    class="font-medium text-gray-800">{{ $item->karyawan->nama_lengkap }}</span>
                                            </div>
                                        </td>
                                        <td class="p-3">{{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                                        </td>
                                        <td class="p-3">
                                            {{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '-' }}
                                        </td>
                                        <td class="p-3">
                                            {{ $item->jam_pulang ? \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') : '-' }}
                                        </td>
                                        <td class="p-3">
                                            @php
                                                $status = $item->status_kehadiran;
                                                $statusMap = [
                                                    'pending' => ['Pending', 'bg-yellow-100 text-yellow-800'],
                                                    'present' => ['Present', 'bg-green-100 text-green-800'],
                                                    'permit' => ['Permit', 'bg-blue-100 text-blue-800'],
                                                    'sick' => ['Sick', 'bg-purple-100 text-purple-800'],
                                                    'absent' => ['Absent', 'bg-red-100 text-red-800'],
                                                ];
                                                [$label, $class] = $statusMap[$status] ?? [
                                                    'Unknown',
                                                    'bg-gray-100 text-gray-800',
                                                ];
                                            @endphp
                                            <span
                                                class="inline-flex px-3 py-1 rounded-full text-xs font-medium {{ $class }}">
                                                {{ $label }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-3 text-center text-gray-400">No attendance records for
                                            today.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
                                    return '80%';
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
                if (this.container) this.init();
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
                        <h2 class="text-xl font-semibold text-gray-800">${this.currentDate.toLocaleString('default', { month: 'long', year: 'numeric' })}</h2>
                        <div class="flex gap-2">
                            <button class="flex items-center justify-center w-8 h-8 transition bg-gray-100 rounded-lg calendar-prev hover:bg-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button class="flex items-center justify-center w-8 h-8 transition bg-gray-100 rounded-lg calendar-next hover:bg-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
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

                for (let i = 0; i < firstDay; i++) html += `<div class="aspect-square"></div>`;

                for (let day = 1; day <= lastDate; day++) {
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const isToday = this.isToday(year, month, day);
                    const dayEvents = this.getEventsForDate(dateStr);
                    html += `
                        <div class="calendar-day aspect-square p-1 ${isToday ? 'ring-2 ring-blue-500 rounded-lg' : ''}" data-date="${dateStr}">
                            <button class="flex flex-col items-center justify-start w-full h-full p-1 transition rounded-lg day-btn hover:bg-gray-50">
                                <span class="text-sm font-medium ${isToday ? 'text-blue-600' : 'text-gray-700'}">${day}</span>
                                <div class="event-indicators mt-1 flex flex-wrap gap-0.5 justify-center">${this.getEventIndicators(dayEvents)}</div>
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
                    if (indicatorsContainer) indicatorsContainer.innerHTML = this.getEventIndicators(dayEvents);
                });
            }

            getEventsForDate(dateStr) {
                return this.events.filter(event => {
                    const eventStart = event.start_date;
                    const eventEnd = event.end_date;
                    if (eventStart === eventEnd) return eventStart === dateStr;
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
                return topEvents.map(event =>
                    `<div class="w-1.5 h-1.5 rounded-full ${colors[event.color] || 'bg-gray-500'}" title="${event.title}"></div>`
                ).join('');
            }

            isToday(year, month, day) {
                const today = new Date();
                return today.getFullYear() === year && today.getMonth() === month && today.getDate() === day;
            }

            attachEventListeners() {
                const prevBtn = this.container.querySelector('.calendar-prev');
                if (prevBtn) prevBtn.addEventListener('click', () => {
                    this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                    this.render();
                    this.loadEvents();
                });

                const nextBtn = this.container.querySelector('.calendar-next');
                if (nextBtn) nextBtn.addEventListener('click', () => {
                    this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                    this.render();
                    this.loadEvents();
                });

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
                const colorBg = {
                    'blue': 'bg-blue-100',
                    'green': 'bg-green-100',
                    'yellow': 'bg-yellow-100',
                    'red': 'bg-red-100',
                    'purple': 'bg-purple-100'
                };

                events.forEach(event => {
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

        document.addEventListener('DOMContentLoaded', function() {
            const calendarContainer = document.getElementById('calendarContainer');
            if (calendarContainer) window.calendar = new CalendarComponent('calendarContainer');
        });
    </script>

    {{-- ANNOUNCEMENT MODAL --}}
    <script>
        function showDetail(id) {
            fetch(`/admin/pengumuman/${id}`)
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
