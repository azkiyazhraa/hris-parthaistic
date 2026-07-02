@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">
        <!-- Header Card -->
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">Announcements</h1>
                <p class="text-sm text-gray-700/80">Manage and publish announcements for employees.</p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="p-6 bg-white shadow-lg rounded-2xl" style="border: 2px solid #e0eaff;">

            <!-- Back -->
            <div class="mb-4">
                <a href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>

            <!-- Toolbar -->
            <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <!-- Search -->
                    <input type="text" id="searchAnnouncement" placeholder="Search title..."
                        class="w-full sm:w-64 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">

                    <!-- Filter Category -->
                    <select id="filterCategory"
                        class="w-full sm:w-44 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">All Categories</option>
                        <option value="umum">General</option>
                        <option value="kebijakan">Policy</option>
                        <option value="pengumuman">Announcement</option>
                        <option value="event">Event</option>
                        <option value="penting">Important</option>
                        <option value="crew_call">Crew Call</option>
                    </select>

                    <!-- Filter Status -->
                    <select id="filterStatus"
                        class="w-full sm:w-40 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="">All Status</option>
                        <option value="1">Published</option>
                        <option value="0">Draft</option>
                    </select>
                </div>

                <button data-modal-target="announcement-modal" data-modal-toggle="announcement-modal"
                    class="px-4 py-2.5 text-sm text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition whitespace-nowrap">
                    + Add Announcement
                </button>
                @include('components.Announcements.create')
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
                            <th class="pb-3 text-left">Title</th>
                            <th class="pb-3 text-left">Category</th>
                            <th class="pb-3 text-left">Target</th>
                            <th class="pb-3 text-left">Status</th>
                            <th class="pb-3 text-left">Publish Date</th>
                            <th class="pb-3 text-left">Valid Until</th>
                            <th class="pb-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengumuman as $item)
                            <tr class="announcement-row transition border-b border-gray-100 hover:bg-blue-50/40"
                                data-title="{{ strtolower($item->judul) }}"
                                data-category="{{ $item->kategori ?? '' }}"
                                data-status="{{ $item->status ? '1' : '0' }}">
                                <td class="py-3 text-gray-700 max-w-xs truncate">{{ $item->judul }}</td>
                                <td class="py-3">
                                    @php
                                        $catClass = match ($item->kategori) {
                                            'umum'       => 'bg-sky-100 text-sky-800',
                                            'kebijakan'  => 'bg-amber-100 text-amber-800',
                                            'pengumuman' => 'bg-indigo-100 text-indigo-800',
                                            'event'      => 'bg-emerald-100 text-emerald-800',
                                            'penting'    => 'bg-red-100 text-red-800',
                                            'crew_call'  => 'bg-cyan-100 text-cyan-800',
                                            default      => 'bg-gray-100 text-gray-800',
                                        };
                                        $catLabel = match ($item->kategori) {
                                            'umum'       => 'General',
                                            'kebijakan'  => 'Policy',
                                            'pengumuman' => 'Announcement',
                                            'event'      => 'Event',
                                            'penting'    => 'Important',
                                            'crew_call'  => 'Crew Call',
                                            default      => 'Uncategorized',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $catClass }}">
                                        {{ $catLabel }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @php
                                        $roleClass = match ($item->target_role) {
                                            'all'      => 'bg-gray-100 text-gray-800',
                                            'karyawan' => 'bg-blue-100 text-blue-800',
                                            'admin'    => 'bg-red-100 text-red-800',
                                            'hr'       => 'bg-emerald-100 text-emerald-800',
                                            default    => 'bg-slate-100 text-slate-800',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $roleClass }}">
                                        {{ $item->target_role === 'karyawan' ? 'Employee' : ($item->target_role === 'all' || !$item->target_role ? 'All Roles' : ucfirst($item->target_role)) }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $item->status ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $item->status ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-700">
                                    {{ $item->tanggal_terbit ? $item->tanggal_terbit->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="py-3 text-gray-700">
                                    {{ $item->tanggal_berlaku_hingga ? $item->tanggal_berlaku_hingga->format('d M Y') : '-' }}
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <!-- Detail -->
                                        <button onclick="showDetail({{ $item->id }})"
                                            class="text-blue-500 hover:text-blue-700 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <!-- Edit -->
                                        <button onclick="openEditModal({{ $item->id }})"
                                            class="text-green-500 hover:text-green-700 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        <!-- Delete -->
                                        <form action="{{ route('admin.pengumuman.destroy', $item->id) }}" method="POST"
                                            class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-500 hover:text-red-700 btn-delete transition">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="emptyRow" style="display:none;">
                            <td colspan="7" class="py-8 text-center text-gray-400">No announcements found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div id="paginationContainer" class="flex justify-end gap-1 mt-4"></div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
        <div class="relative w-full max-w-2xl">
            <div class="overflow-hidden bg-white shadow-2xl rounded-3xl">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
                    <div>
                        <h3 class="text-xl font-bold text-blue-900">Announcement Detail</h3>
                        <p class="mt-1 text-sm text-gray-500">Complete Announcement Information</p>
                    </div>
                    <button onclick="closeDetailModal()"
                        class="flex items-center justify-center w-10 h-10 transition rounded-xl hover:bg-gray-100">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="detailContent" class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar"></div>
            </div>
        </div>
    </div>

    <!-- Single Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="relative bg-white w-full max-w-xl rounded-xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b">
                <div>
                    <h2 class="text-xl font-semibold">Edit Announcement</h2>
                    <p class="text-gray-500 text-sm mt-1">Update announcement details</p>
                </div>
                <button onclick="closeEditModal()"
                    class="w-10 h-10 rounded-full flex items-center justify-center hover:bg-gray-100 transition">✕</button>
            </div>

            <div class="max-h-[80vh] overflow-y-auto px-6">
                <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-6 py-6">
                    @csrf
                    @method('PUT')

                    <div class="bg-gray-50 rounded-2xl border border-gray-100 p-5 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                            <input type="text" name="judul" id="edit_judul" required
                                placeholder="Enter the title of the announcement..."
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                            <textarea name="konten" id="edit_konten" rows="6" required
                                placeholder="Write the announcement content here..."
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl border border-gray-100 p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select name="kategori" id="edit_kategori"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Category</option>
                                    <option value="umum">General</option>
                                    <option value="kebijakan">Policy</option>
                                    <option value="pengumuman">Announcement</option>
                                    <option value="event">Event</option>
                                    <option value="penting">Important</option>
                                    <option value="crew_call">Crew Call</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Target Role</label>
                                <select name="target_role" id="edit_target_role"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="all">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="hr">HR</option>
                                    <option value="karyawan">Employee</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Publish Date</label>
                                <input type="datetime-local" name="tanggal_terbit" id="edit_tanggal_terbit"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Leave blank to keep the current publish date</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                                <input type="date" name="tanggal_berlaku_hingga" id="edit_tanggal_berlaku_hingga"
                                    class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl border border-gray-100 p-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attachment</label>
                        <div id="edit_current_attachment" class="mb-2 hidden">
                            <a id="edit_attachment_link" href="#" target="_blank"
                                class="text-blue-600 hover:text-blue-800 text-sm">View current attachment</a>
                        </div>
                        <input type="file" name="lampiran" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                            class="w-full rounded-xl border border-dashed border-gray-300 bg-white px-4 py-3 text-sm
                                file:mr-4 file:rounded-lg file:border-0 file:bg-blue-100 file:px-4 file:py-2
                                file:text-blue-700 hover:file:bg-blue-200">
                        <p class="text-xs text-gray-500 mt-2">Maximum 5MB • PDF, DOC, DOCX, JPG, JPEG, PNG</p>
                    </div>

                    <div class="flex items-center justify-between rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4">
                        <div>
                            <h4 class="text-sm font-semibold text-blue-900">Publish Announcement</h4>
                            <p class="text-xs text-blue-700 mt-1">Enable to publish the announcement</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="status" id="edit_status" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-blue-600
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                after:bg-white after:border-gray-300 after:border after:rounded-full
                                after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                            </div>
                        </label>
                    </div>

                    <div class="py-4 border-t flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ── Helpers ────────────────────────────────────────────────────────────
        function escapeHtml(str) {
            const d = document.createElement('div');
            d.appendChild(document.createTextNode(str || ''));
            return d.innerHTML;
        }

        // ── Detail Modal ───────────────────────────────────────────────────────
        function showDetail(id) {
            fetch(`/admin/pengumuman/${id}`)
                .then(r => r.json())
                .then(data => {
                    const catClass = {
                        umum: 'bg-sky-100 text-sky-700',
                        kebijakan: 'bg-amber-100 text-amber-700',
                        pengumuman: 'bg-indigo-100 text-indigo-700',
                        event: 'bg-emerald-100 text-emerald-700',
                        penting: 'bg-red-100 text-red-700',
                        crew_call: 'bg-cyan-100 text-cyan-700',
                    };
                    const catLabel = {
                        umum: 'General', kebijakan: 'Policy', pengumuman: 'Announcement',
                        event: 'Event', penting: 'Important', crew_call: 'Crew Call',
                    };
                    const badgeClass = catClass[data.kategori] || 'bg-gray-100 text-gray-700';
                    const publishDate = data.tanggal_terbit
                        ? new Date(data.tanggal_terbit).toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' })
                        : '-';

                    let attachmentHtml = '';
                    if (data.lampiran) {
                        if (/\.(jpg|jpeg|png|gif|webp)$/i.test(data.lampiran)) {
                            attachmentHtml = `<img src="/storage/${data.lampiran}" alt="Attachment" class="w-full border border-gray-200 rounded-xl">`;
                        } else if (/\.pdf$/i.test(data.lampiran)) {
                            attachmentHtml = `<iframe src="/storage/${data.lampiran}" class="w-full h-[500px] rounded-xl border border-gray-200"></iframe>`;
                        } else {
                            attachmentHtml = `<a href="/storage/${data.lampiran}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-sm text-white bg-blue-600 rounded-xl hover:bg-blue-700">Download Attachment</a>`;
                        }
                    }

                    document.getElementById('detailContent').innerHTML = `
                        <div class="space-y-5">
                            <div class="pb-4 border-b border-gray-100">
                                <h4 class="text-xl font-bold text-gray-800">${escapeHtml(data.judul)}</h4>
                                <div class="flex flex-wrap items-center gap-2 mt-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${badgeClass}">
                                        ${catLabel[data.kategori] || 'Uncategorized'}
                                    </span>
                                    <span class="text-xs text-gray-400">Published ${publishDate}</span>
                                </div>
                            </div>
                            <div class="px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl">
                                <p class="text-sm leading-7 text-gray-700 whitespace-pre-line">${escapeHtml(data.konten)}</p>
                            </div>
                            ${data.lampiran ? `<div class="space-y-2"><h5 class="text-sm font-semibold text-gray-700">Attachment</h5>${attachmentHtml}</div>` : ''}
                        </div>
                    `;
                    document.getElementById('detailModal').classList.remove('hidden');
                })
                .catch(err => console.error('Failed to load announcement:', err));
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // ── Edit Modal ─────────────────────────────────────────────────────────
        function openEditModal(id) {
            fetch(`/admin/pengumuman/${id}`)
                .then(r => r.json())
                .then(data => {
                    const form = document.getElementById('editForm');
                    form.action = `/admin/pengumuman/${id}`;

                    document.getElementById('edit_judul').value            = data.judul || '';
                    document.getElementById('edit_konten').value           = data.konten || '';
                    document.getElementById('edit_kategori').value         = data.kategori || '';
                    document.getElementById('edit_target_role').value      = data.target_role || 'all';
                    document.getElementById('edit_tanggal_berlaku_hingga').value =
                        data.tanggal_berlaku_hingga ? data.tanggal_berlaku_hingga.substring(0, 10) : '';
                    document.getElementById('edit_tanggal_terbit').value   =
                        data.tanggal_terbit ? data.tanggal_terbit.substring(0, 16) : '';
                    document.getElementById('edit_status').checked         = !!data.status;

                    const attachEl   = document.getElementById('edit_current_attachment');
                    const attachLink = document.getElementById('edit_attachment_link');
                    if (data.lampiran) {
                        attachLink.href = `/storage/${data.lampiran}`;
                        attachEl.classList.remove('hidden');
                    } else {
                        attachEl.classList.add('hidden');
                    }

                    document.getElementById('editModal').classList.remove('hidden');
                })
                .catch(err => console.error('Failed to load announcement for edit:', err));
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // ── Delete Confirm ─────────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', function () {
                    const form = this.closest('.delete-form');
                    Swal.fire({
                        title: 'Delete this announcement?',
                        text: 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg ml-2',
                            cancelButton: 'bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded-lg',
                        }
                    }).then(result => {
                        if (result.isConfirmed) form.submit();
                    });
                });
            });

            // ── Search / Filter / Pagination ───────────────────────────────────
            const searchInput   = document.getElementById('searchAnnouncement');
            const catFilter     = document.getElementById('filterCategory');
            const statusFilter  = document.getElementById('filterStatus');
            const allRows       = Array.from(document.querySelectorAll('.announcement-row'));
            const emptyRow      = document.getElementById('emptyRow');
            const pagination    = document.getElementById('paginationContainer');
            const perPage       = 10;
            let currentPage     = 1;
            let filteredRows    = [...allRows];

            function applyFilters() {
                const keyword = searchInput.value.toLowerCase().trim();
                const cat     = catFilter.value;
                const status  = statusFilter.value;

                filteredRows = allRows.filter(row => {
                    const matchTitle  = !keyword || row.dataset.title.includes(keyword);
                    const matchCat    = !cat    || row.dataset.category === cat;
                    const matchStatus = !status  || row.dataset.status === status;
                    return matchTitle && matchCat && matchStatus;
                });

                currentPage = 1;
                renderTable();
                renderPagination();
            }

            function renderTable() {
                allRows.forEach(r => (r.style.display = 'none'));
                const start = (currentPage - 1) * perPage;
                filteredRows.slice(start, start + perPage).forEach(r => (r.style.display = 'table-row'));
                emptyRow.style.display = filteredRows.length === 0 ? 'table-row' : 'none';
            }

            function renderPagination() {
                pagination.innerHTML = '';
                const total = Math.ceil(filteredRows.length / perPage);
                if (total <= 1) return;

                const prev = document.createElement('button');
                prev.innerHTML  = '&laquo;';
                prev.disabled   = currentPage === 1;
                prev.className  = 'px-3 py-1 rounded border text-sm disabled:opacity-40';
                prev.onclick    = () => { if (currentPage > 1) { currentPage--; renderTable(); renderPagination(); } };
                pagination.appendChild(prev);

                for (let i = 1; i <= total; i++) {
                    const btn = document.createElement('button');
                    btn.textContent = i;
                    btn.className   = `px-3 py-1 rounded border text-sm ${i === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'}`;
                    btn.onclick     = () => { currentPage = i; renderTable(); renderPagination(); };
                    pagination.appendChild(btn);
                }

                const next = document.createElement('button');
                next.innerHTML  = '&raquo;';
                next.disabled   = currentPage === total;
                next.className  = 'px-3 py-1 rounded border text-sm disabled:opacity-40';
                next.onclick    = () => { if (currentPage < total) { currentPage++; renderTable(); renderPagination(); } };
                pagination.appendChild(next);
            }

            searchInput.addEventListener('input', applyFilters);
            catFilter.addEventListener('change', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
            applyFilters();
        });
    </script>
@endpush
