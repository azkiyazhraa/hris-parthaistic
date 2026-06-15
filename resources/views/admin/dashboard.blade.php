@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4">
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <!-- TEXT -->
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">
                    Dashboard
                </h1>
                <p class="text-gray-700/80 text-sm">
                    Monitor employee data, attendance, and performance in one place
                </p>
            </div>

            <!-- IMAGE / ILLUSTRATION -->
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="py-4 rounded-base">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <!-- CARD 1 -->
                <div class="bg-white p-5 rounded-2xl shadow">
                    <p class="text-gray-500 text-sm mb-2">Total Employee</p>

                    <h2 class="text-3xl font-bold text-blue-900">{{ $totalKaryawan }}</h2>
                    <p class="text-sm text-gray-500 mb-4">Employees</p>

                    <!-- PROGRESS -->
                    <div class="space-y-2 text-xs">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Full-time</span>
                                <span>{{ number_format($fulltimePercent, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ round($fulltimePercent) }}%">
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Contract</span>
                                <span>{{ number_format($contractPercent, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ round($contractPercent) }}%">
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Internship</span>
                                <span>{{ number_format($internshipPercent, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ round($internshipPercent) }}%">
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between mb-1">
                                <span>Resigned/Terminated</span>
                                <span>{{ $resignedEmployees }}</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full">
                                <div class="bg-red-500 h-2 rounded-full"
                                    style="width: {{ $totalKaryawan > 0 ? round(($resignedEmployees / $totalKaryawan) * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2 (DONUT) -->
                <div class="bg-white p-5 rounded-2xl shadow flex flex-col items-center justify-center">
                    <p class="text-gray-500 text-sm mb-4">Employee's Task Record</p>
                    <div id="donutChart"></div>
                </div>

                <!-- CARD 3 -->
                <div class="bg-white p-5 rounded-2xl shadow">
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-gray-500 text-sm">Announcements</p>
                        <button data-modal-target="announcement-modal" data-modal-toggle="announcement-modal"
                            class="text-xl font-bold hover:text-blue-600">
                            +
                        </button>
                        @include('components.Announcements.create')
                    </div>

                    <div class="space-y-3 text-sm text-gray-600">
                        @foreach ($attachment as $item)
                            <div>
                                <a onclick="showDetail({{ $item->id }})" href="javascript:void(0)"
                                    class="cursor-pointer">
                                    <div class="flex justify-between text-sm">
                                        <span
                                            class="font-medium">{{ \Illuminate\Support\Str::limit($item->judul, 20, '...') }}</span>
                                        <span
                                            class="text-{{ $item->status == 1 ? 'green' : 'yellow' }}-500 text-xs">{{ $item->tanggal_terbit->format('d M Y') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-400">
                                        {{ \Illuminate\Support\Str::words($item->konten, 10, '...') }}
                                    </p>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-right mt-3">
                        <a href="{{ route('admin.pengumuman.index') }}" class="text-cyan-500 text-xs hover:underline">View
                            All</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-base">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- LEFT: TABLE -->
                <div class="col-span-2 bg-white p-6 rounded-2xl shadow">
                    <!-- HEADER -->
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-lg font-semibold text-gray-800">Today Attendance</h2>
                        <span>
                            {{ \Carbon\Carbon::now()->locale('en')->isoFormat('dddd, D MMMM YYYY') }}
                        </span>
                    </div>

                    {{-- STATISTICS --}}
                    <div class="flex justify-between mb-4">
                        <div class="grid grid-cols-4 text-center gap-4">
                            <div class="flex items-end justify-center">
                                <p class="text-2xl font-bold text-blue-900 leading-none">{{ $statistics['present'] }}</p>
                                <p class="text-xs text-gray-500 ml-1">Present</p>
                            </div>
                            <div class="flex items-end justify-center">
                                <p class="text-2xl font-bold text-blue-900 leading-none">{{ $statistics['permit'] }}</p>
                                <p class="text-xs text-gray-500 ml-1">Permission</p>
                            </div>
                            <div class="flex items-end justify-center">
                                <p class="text-2xl font-bold text-blue-900 leading-none">{{ $statistics['sick'] }}</p>
                                <p class="text-xs text-gray-500 ml-1">Sick</p>
                            </div>
                            <div class="flex items-end justify-center">
                                <p class="text-2xl font-bold text-blue-900 leading-none">{{ $statistics['pending'] }}</p>
                                <p class="text-xs text-gray-500 ml-1">Pending</p>
                            </div>
                        </div>
                    </div>

                    <!-- TABLE -->
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[800px] md:min-w-full text-sm text-left">
                            <thead class="bg-gray-100 text-gray-500 text-xs">
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
                                                    class="w-9 h-9 rounded-full bg-blue-100 border border-blue-200 overflow-hidden shrink-0">
                                                    <img src="{{ $fotoUrl }}"
                                                        alt="{{ $item->karyawan->nama_lengkap }}"
                                                        class="w-full h-full object-cover" loading="lazy">
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
                <div class="bg-white rounded-3xl shadow p-6">
                    <div id="calendarContainer"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL PENGUMUMAN --}}
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="relative w-full max-w-2xl">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-fadeIn">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                    <div>
                        <h3 class="text-xl font-bold text-blue-900">Announcements Detail</h3>
                        <p class="text-sm text-gray-500 mt-1">Complete Announcements Information</p>
                    </div>
                    <button onclick="closeDetailModal()"
                        class="w-10 h-10 rounded-xl hover:bg-gray-100 flex items-center justify-center transition">
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
                    <div class="calendar-header flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">${this.currentDate.toLocaleString('default', { month: 'long', year: 'numeric' })}</h2>
                        <div class="flex gap-2">
                            <button class="calendar-prev w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button class="calendar-next w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="calendar-weekdays grid grid-cols-7 gap-1 mb-2">
                        <div class="text-center text-xs font-medium text-gray-400 py-2">Sun</div>
                        <div class="text-center text-xs font-medium text-gray-400 py-2">Mon</div>
                        <div class="text-center text-xs font-medium text-gray-400 py-2">Tue</div>
                        <div class="text-center text-xs font-medium text-gray-400 py-2">Wed</div>
                        <div class="text-center text-xs font-medium text-gray-400 py-2">Thu</div>
                        <div class="text-center text-xs font-medium text-gray-400 py-2">Fri</div>
                        <div class="text-center text-xs font-medium text-gray-400 py-2">Sat</div>
                    </div>
                    <div class="calendar-days grid grid-cols-7 gap-1">
                `;

                for (let i = 0; i < firstDay; i++) html += `<div class="aspect-square"></div>`;

                for (let day = 1; day <= lastDate; day++) {
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const isToday = this.isToday(year, month, day);
                    const dayEvents = this.getEventsForDate(dateStr);
                    html += `
                        <div class="calendar-day aspect-square p-1 ${isToday ? 'ring-2 ring-blue-500 rounded-lg' : ''}" data-date="${dateStr}">
                            <button class="day-btn w-full h-full rounded-lg hover:bg-gray-50 transition flex flex-col items-center justify-start p-1">
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

                let eventListHtml = '<div class="space-y-2 max-h-96 overflow-y-auto">';
                const colorBg = {
                    'blue': 'bg-blue-100',
                    'green': 'bg-green-100',
                    'yellow': 'bg-yellow-100',
                    'red': 'bg-red-100',
                    'purple': 'bg-purple-100'
                };

                events.forEach(event => {
                    eventListHtml += `
                        <div class="p-3 rounded-xl border border-gray-100 hover:bg-gray-50 cursor-pointer transition"
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
                            <div class="border-b border-gray-100 pb-4">
                                <h4 class="text-2xl font-bold text-gray-800 leading-snug">${escapeHtml(data.judul)}</h4>
                                <div class="flex items-center gap-2 mt-3 flex-wrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${badgeClass}">${data.kategori ? data.kategori.charAt(0).toUpperCase() + data.kategori.slice(1) : 'Uncategorized'}</span>
                                    <span class="text-xs text-gray-400">Published ${publishDate}</span>
                                </div>
                            </div>
                            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5">
                                <p class="text-sm leading-7 text-gray-700 whitespace-pre-line">${escapeHtml(data.konten)}</p>
                            </div>
                            ${data.lampiran ? `
                                    <div class="border border-blue-100 bg-blue-50 rounded-2xl p-4">
                                        <div class="flex items-center justify-between flex-wrap gap-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16v-8m0 0l-3 3m3-3l3 3M5 20h14"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-800">Attachment</p>
                                                    <p class="text-xs text-gray-500">Click to ciew file</p>
                                                </div>
                                            </div>
                                            <a href="/storage/${data.lampiran}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm transition">View File</a>
                                        </div>
                                    </div>
                                ` : ''}
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
