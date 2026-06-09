@extends('layouts.app')
@section('content')
    <div class="container mx-auto py-4 space-y-4">
        <!-- Header Card -->
        <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-blue-900 mb-1">Announcements</h1>
                <p class="text-gray-700/80 text-sm">Stay updated with the latest announcements.</p>
            </div>
            <!-- IMAGE / ILLUSTRATION -->
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6" style="border: 2px solid #e0eaff;">

            {{-- Pengumuman filter dan button tambah data --}}
            <div class="flex justify-between items-center mb-4">
                <span>Total announcements: {{ $pengumuman->count() }}</span>
                <button data-modal-target="announcement-modal" data-modal-toggle="announcement-modal"
                    class="bg-blue-600 text-white px-3 py-1 text-sm rounded-md hover:bg-blue-700 transition">Add
                    Announcement
                </button>
                @include('components.Announcements.create')
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-gray-400 font-medium text-xs uppercase tracking-wide border-b">
                            <th class="text-left pb-3">Title</th>
                            <th class="text-left pb-3">Category</th>
                            <th class="text-left pb-3">Target</th>
                            <th class="text-left pb-3">Status</th>
                            <th class="text-left pb-3">Publish Date</th>
                            <th class="text-left pb-3">Valid Until</th>
                            <th class="text-left pb-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengumuman as $item)
                            <tr class="leave-row border-b border-gray-100 hover:bg-blue-50/40 transition">
                                <td class="py-3 text-gray-700">{{ $item->judul }}</td>
                                <td class="py-3">
                                    @php
                                        $category = match ($item->kategori) {
                                            'umum' => 'bg-sky-100 text-sky-800',
                                            'kebijakan' => 'bg-amber-100 text-amber-800',
                                            'pengumuman' => 'bg-indigo-100 text-indigo-800',
                                            'event' => 'bg-emerald-100 text-emerald-800',
                                            'penting' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $category }}">
                                        {{ ucfirst($item->kategori ?? 'Uncategorized') }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @php
                                        $category = match ($item->target_role) {
                                            'all' => 'bg-gray-100 text-gray-800',
                                            'karyawan' => 'bg-blue-100 text-blue-800',
                                            'admin' => 'bg-red-100 text-red-800',
                                            'hr' => 'bg-emerald-100 text-emerald-800',
                                            default => 'bg-slate-100 text-slate-800',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $category }}">
                                        {{ $item->target_role && $item->target_role != 'all' ? ($item->target_role == 'karyawan' ? 'Employee' : ucfirst($item->target_role)) : 'All Role' }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @php
                                        $badge = 'bg-gray-200 text-gray-800';
                                        if ($item->status == 1) {
                                            $badge = 'bg-green-200 text-green-800';
                                        } elseif ($item->status == 0) {
                                            $badge = 'bg-yellow-200 text-yellow-800';
                                        }
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                        {{ ucfirst($item->status ? 'Published' : 'Draft') }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-700">
                                    {{ $item->tanggal_terbit ? $item->tanggal_terbit->format('d F Y, H:i') : '-' }}</td>
                                <td class="py-3 text-gray-700">
                                    {{ $item->tanggal_berlaku_hingga ? $item->tanggal_berlaku_hingga->format('d F Y') : '-' }}
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <a onclick="showDetail({{ $item->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-sm transition cursor-pointer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <button data-modal-target="announcement-modal-edit-{{ $item->id }}"
                                            data-modal-toggle="announcement-modal-edit-{{ $item->id }}"
                                            class="text-green-600 hover:text-green-800 text-sm transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        @include('components.Announcements.edit')

                                        <form action="{{ route('admin.pengumuman.destroy', $item->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-500 hover:text-red-700 btn-delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">

        <div class="relative w-full max-w-2xl">

            <!-- MODAL -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden animate-fadeIn">

                <!-- HEADER -->
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">

                    <div>
                        <h3 class="text-xl font-bold text-blue-900">
                            Announcement Detail
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Complete Announcement Information
                        </p>
                    </div>

                    <button onclick="closeDetailModal()"
                        class="w-10 h-10 rounded-xl hover:bg-gray-100 flex items-center justify-center transition">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- CONTENT -->
                <div id="detailContent" class="p-6 max-h-[75vh] overflow-y-auto custom-scrollbar">
                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showDetail(id) {
            fetch(`/admin/pengumuman/${id}`)
                .then(response => response.json())
                .then(data => {

                    const categoryClass = {
                        general: 'bg-blue-100 text-blue-700',
                        policy: 'bg-amber-100 text-amber-700',
                        announcement: 'bg-violet-100 text-violet-700',
                        event: 'bg-emerald-100 text-emerald-700',
                        important: 'bg-red-100 text-red-700'
                    };

                    const badgeClass = categoryClass[data.kategori] || 'bg-gray-100 text-gray-700';

                    const publishDate = data.tanggal_terbit ?
                        new Date(data.tanggal_terbit).toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'long',
                            year: 'numeric'
                        }) :
                        '-';

                    const content = `
                        <div class="space-y-6">

                            <!-- HEADER -->
                            <div class="border-b border-gray-100 pb-4">
                                <div class="flex items-start justify-between gap-3 flex-wrap">
                                    <div>
                                        <h4 class="text-2xl font-bold text-gray-800 leading-snug">
                                            ${data.judul}
                                        </h4>

                                        <div class="flex items-center gap-2 mt-3 flex-wrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${badgeClass}">
                                                ${data.kategori ? data.kategori.charAt(0).toUpperCase() + data.kategori.slice(1) : 'Uncategorized'}
                                            </span>

                                            <span class="text-xs text-gray-400">
                                                Published ${publishDate}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CONTENT -->
                            <div class="bg-gray-50 border border-gray-100 rounded-2xl px-5">
                                <p class="text-sm leading-7 text-gray-700 whitespace-pre-line">
                                    ${data.konten}
                                </p>
                            </div>

                            <!-- ATTACHMENT -->
                            ${
                                data.lampiran
                                    ? `
                                                                                                                                        <div class="border border-blue-100 bg-blue-50 rounded-2xl p-4">
                                                                                                                                            <div class="flex items-center justify-between flex-wrap gap-3">

                                                                                                                                                <div class="flex items-center gap-3">
                                                                                                                                                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                                                                                                                                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                                                                                                d="M12 16v-8m0 0l-3 3m3-3l3 3M5 20h14" />
                                                                                                                                                        </svg>
                                                                                                                                                    </div>

                                                                                                                                                    <div>
                                                                                                                                                        <p class="text-sm font-medium text-gray-800">
                                                                                                                                                            Attachment
                                                                                                                                                        </p>
                                                                                                                                                        <p class="text-xs text-gray-500">
                                                                                                                                                            Click to view file
                                                                                                                                                        </p>
                                                                                                                                                    </div>
                                                                                                                                                </div>

                                                                                                                                                <a href="/storage/${data.lampiran}"
                                                                                                                                                    target="_blank"
                                                                                                                                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm transition">
                                                                                                                                                    View File
                                                                                                                                                </a>

                                                                                                                                            </div>
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
    </script>
@endpush
