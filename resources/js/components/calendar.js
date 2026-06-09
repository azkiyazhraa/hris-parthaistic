// Calendar Component
class CalendarComponent {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.currentDate = new Date();
        this.selectedDate = null;
        this.events = [];
        this.onDateClick = options.onDateClick || null;
        this.onEventClick = options.onEventClick || null;

        this.init();
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
                <h2 class="text-xl font-semibold text-gray-800">
                    ${this.currentDate.toLocaleString('default', { month: 'long', year: 'numeric' })}
                </h2>
                <div class="flex gap-2">
                    <button class="calendar-prev w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button class="calendar-next w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 transition">
                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
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
                <div class="calendar-day aspect-square p-1 ${isToday ? 'ring-2 ring-blue-500' : ''}" data-date="${dateStr}">
                    <button class="day-btn w-full h-full rounded-lg hover:bg-gray-50 transition flex flex-col items-center justify-start p-1">
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

        // Attach event listeners
        this.attachEventListeners();
    }

    renderEvents() {
        // Update existing calendar with event indicators
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

        // Show up to 3 indicators
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
                
                if (this.onDateClick) {
                    this.onDateClick(date, events);
                }
            });
        });
    }

    refresh() {
        this.loadEvents();
    }
}

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CalendarComponent;
}
