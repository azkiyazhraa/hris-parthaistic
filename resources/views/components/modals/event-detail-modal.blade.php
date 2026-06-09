{{-- Event Detail Modal --}}
<div id="eventDetailModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

    <div class="relative w-full max-w-2xl">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-fadeIn">

            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                <div>
                    <h3 id="modalTitle" class="text-xl font-bold text-gray-800">
                        Event Details
                    </h3>
                    <p id="modalSubtitle" class="text-sm text-gray-500 mt-1">
                        Detail information event
                    </p>
                </div>
                <button onclick="closeEventDetailModal()"
                    class="w-10 h-10 rounded-xl hover:bg-gray-100 flex items-center justify-center transition">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div id="eventDetailContent" class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar">
                <div class="text-center py-8 text-gray-400">
                    Loading...
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .animate-fadeIn {
        animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>

<script>
    function openEventDetailModal(eventId, eventType) {
        const modal = document.getElementById('eventDetailModal');
        const contentDiv = document.getElementById('eventDetailContent');

        if (!modal || !contentDiv) return;

        // Show loading
        contentDiv.innerHTML = `
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                <p class="text-gray-500 mt-3">Loading details...</p>
            </div>
        `;

        modal.classList.remove('hidden');

        // Fetch event details
        fetch(`/calendar/event-detail?type=${eventType}&id=${eventId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    renderEventDetail(data.data);
                } else {
                    contentDiv.innerHTML = `
                        <div class="text-center py-8 text-red-500">
                            Failed to load event details
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error fetching event detail:', error);
                contentDiv.innerHTML = `
                    <div class="text-center py-8 text-red-500">
                        Failed to load event details
                    </div>
                `;
            });
    }

    function renderEventDetail(data) {
        const contentDiv = document.getElementById('eventDetailContent');
        const modalTitle = document.getElementById('modalTitle');
        const modalSubtitle = document.getElementById('modalSubtitle');

        if (!contentDiv) return;

        // Set modal title based on type
        const typeLabels = {
            'pengumuman': 'Pengumuman',
            'penggajian': 'Penggajian',
            'cuti': 'Cuti',
            'absensi': 'Absensi'
        };

        if (modalTitle) {
            modalTitle.textContent = data.title || data.judul || typeLabels[data.type] + ' Details';
        }
        if (modalSubtitle) {
            modalSubtitle.textContent = typeLabels[data.type] + ' Information';
        }

        let html = '';

        switch (data.type) {
            case 'pengumuman':
                html = renderPengumumanDetail(data);
                break;
            case 'penggajian':
                html = renderPenggajianDetail(data);
                break;
            case 'cuti':
                html = renderCutiDetail(data);
                break;
            case 'absensi':
                html = renderAbsensiDetail(data);
                break;
            default:
                html = renderDefaultDetail(data);
        }

        contentDiv.innerHTML = html;
    }

    function renderPengumumanDetail(data) {
        const publishDate = data.tanggal_terbit ? new Date(data.tanggal_terbit).toLocaleDateString('id-ID', {
            day: '2-digit', month: 'long', year: 'numeric'
        }) : '-';

        const kategoriClass = {
            umum: 'bg-blue-100 text-blue-700',
            kebijakan: 'bg-amber-100 text-amber-700',
            pengumuman: 'bg-violet-100 text-violet-700',
            event: 'bg-emerald-100 text-emerald-700',
            penting: 'bg-red-100 text-red-700'
        };

        const badgeClass = kategoriClass[data.kategori] || 'bg-gray-100 text-gray-700';

        return `
            <div class="space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <div class="flex items-start justify-between gap-3 flex-wrap">
                        <div>
                            <h4 class="text-2xl font-bold text-gray-800 leading-snug">${escapeHtml(data.judul)}</h4>
                            <div class="flex items-center gap-2 mt-3 flex-wrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${badgeClass}">
                                    ${data.kategori ? data.kategori.charAt(0).toUpperCase() + data.kategori.slice(1) : 'Uncategorized'}
                                </span>
                                <span class="text-xs text-gray-400">Published ${publishDate}</span>
                            </div>
                        </div>
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
                                    <p class="text-sm font-medium text-gray-800">Lampiran</p>
                                    <p class="text-xs text-gray-500">Klik untuk melihat file</p>
                                </div>
                            </div>
                            <a href="/storage/${data.lampiran}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm transition">
                                Lihat File
                            </a>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    }

    function renderPenggajianDetail(data) {
        const bulanText = data.bulan_text || (data.bulan ? getBulanName(data.bulan) : '-');
        const statusClass = {
            'draft': 'bg-gray-100 text-gray-700',
            'pending': 'bg-yellow-100 text-yellow-700',
            'approved': 'bg-blue-100 text-blue-700',
            'paid': 'bg-green-100 text-green-700'
        };

        return `
            <div class="space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <h4 class="text-2xl font-bold text-gray-800">Salary - ${bulanText} ${data.tahun}</h4>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${statusClass[data.status] || 'bg-gray-100'}">
                            ${data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'Unknown'}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <p class="text-xs text-gray-500 mb-1">Total Earnings</p>
                        <p class="text-xl font-bold text-green-600">Rp ${formatNumber(data.total_earnings || 0)}</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <p class="text-xs text-gray-500 mb-1">Total Deductions</p>
                        <p class="text-xl font-bold text-red-600">Rp ${formatNumber(data.total_deductions || 0)}</p>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-2xl p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Net Salary</p>
                            <p class="text-2xl font-bold text-blue-600">Rp ${formatNumber(data.net_salary || 0)}</p>
                        </div>
                        ${data.payslip_sent_at ? `
                            <a href="/penggajian/${data.id}/download" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-sm hover:bg-blue-700 transition">
                                Download Payslip
                            </a>
                        ` : ''}
                    </div>
                </div>

                ${data.catatan ? `
                    <div class="border-t pt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Catatan:</p>
                        <p class="text-sm text-gray-600">${escapeHtml(data.catatan)}</p>
                    </div>
                ` : ''}
            </div>
        `;
    }

    function renderCutiDetail(data) {
        const startDate = data.tanggal_mulai ? new Date(data.tanggal_mulai).toLocaleDateString('id-ID') : '-';
        const endDate = data.tanggal_selesai ? new Date(data.tanggal_selesai).toLocaleDateString('id-ID') : '-';

        const statusClass = {
            'pending': 'bg-yellow-100 text-yellow-700',
            'approved': 'bg-green-100 text-green-700',
            'disetujui': 'bg-green-100 text-green-700',
            'rejected': 'bg-red-100 text-red-700',
            'ditolak': 'bg-red-100 text-red-700'
        };

        const statusText = data.status_text || (data.status === 'approved' ? 'Approved' : data.status);

        return `
            <div class="space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <h4 class="text-2xl font-bold text-gray-800">${data.jenis_cuti_label || 'Leave Request'}</h4>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${statusClass[data.status] || 'bg-gray-100'}">
                            ${statusText}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <p class="text-xs text-gray-500 mb-1">Start Date</p>
                        <p class="text-lg font-semibold">${startDate}</p>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-4">
                        <p class="text-xs text-gray-500 mb-1">End Date</p>
                        <p class="text-lg font-semibold">${endDate}</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-2xl p-4 flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Total Days</p>
                        <p class="text-2xl font-bold">${data.total_hari || 0} days</p>
                    </div>
                </div>

                ${data.alasan ? `
                    <div class="border-t pt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Reason:</p>
                        <p class="text-sm text-gray-600">${escapeHtml(data.alasan)}</p>
                    </div>
                ` : ''}

                ${data.catatan ? `
                    <div class="border-t pt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Admin Note:</p>
                        <p class="text-sm text-gray-600">${escapeHtml(data.catatan)}</p>
                    </div>
                ` : ''}
            </div>
        `;
    }

    function renderAbsensiDetail(data) {
        const checkIn = data.jam_masuk ? new Date(`2000-01-01T${data.jam_masuk}`).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';
        const checkOut = data.jam_pulang ? new Date(`2000-01-01T${data.jam_pulang}`).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-';

        const statusClass = {
            'hadir': 'bg-green-100 text-green-700',
            'masuk': 'bg-green-100 text-green-700',
            'izin': 'bg-blue-100 text-blue-700',
            'sakit': 'bg-purple-100 text-purple-700',
            'alpha': 'bg-red-100 text-red-700'
        };

        const statusText = {
            'hadir': 'Present',
            'masuk': 'Present',
            'izin': 'Permission',
            'sakit': 'Sick',
            'alpha': 'Absent'
        };

        return `
            <div class="space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <h4 class="text-2xl font-bold text-gray-800">Attendance Record</h4>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${statusClass[data.status_kehadiran] || 'bg-gray-100'}">
                            ${statusText[data.status_kehadiran] || data.status_kehadiran}
                        </span>
                        <span class="text-xs text-gray-400">${data.tanggal ? new Date(data.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-'}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-green-50 rounded-2xl p-4">
                        <p class="text-xs text-gray-500 mb-1">Check In</p>
                        <p class="text-xl font-bold text-green-600">${checkIn}</p>
                    </div>
                    <div class="bg-orange-50 rounded-2xl p-4">
                        <p class="text-xs text-gray-500 mb-1">Check Out</p>
                        <p class="text-xl font-bold text-orange-600">${checkOut}</p>
                    </div>
                </div>

                ${data.keterangan ? `
                    <div class="border-t pt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Keterangan:</p>
                        <p class="text-sm text-gray-600">${escapeHtml(data.keterangan)}</p>
                    </div>
                ` : ''}

                ${data.location ? `
                    <div class="border-t pt-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Location:</p>
                        <p class="text-sm text-gray-600">${escapeHtml(data.location)}</p>
                    </div>
                ` : ''}
            </div>
        `;
    }

    function renderDefaultDetail(data) {
        return `
            <div class="space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <h4 class="text-2xl font-bold text-gray-800">${data.title || data.judul || 'Event Details'}</h4>
                </div>
                <div class="bg-gray-50 rounded-2xl p-5">
                    <p class="text-sm text-gray-700">${data.description || data.pesan || 'No additional information available.'}</p>
                </div>
            </div>
        `;
    }

    function closeEventDetailModal() {
        const modal = document.getElementById('eventDetailModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function getBulanName(month) {
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        return months[month - 1] || '-';
    }
</script>
