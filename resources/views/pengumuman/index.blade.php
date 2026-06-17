@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">
        <!-- Header Card -->
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">Announcements</h1>
                <p class="text-sm text-gray-700/80">Stay updated with the latest announcements.</p>
            </div>
            <!-- IMAGE / ILLUSTRATION -->
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="p-6 bg-white shadow-lg rounded-2xl" style="border: 2px solid #e0eaff;">

            {{-- Pengumuman filter dan button tambah data --}}
            <div class="flex items-center justify-between mb-4">
                <span>Total announcements: {{ $pengumuman->count() }}</span>
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
                            <tr class="transition border-b border-gray-100 leave-row hover:bg-blue-50/40">
                                <td class="py-3 text-gray-700">{{ $item->judul }}</td>
                                <td class="py-3">
                                    @php
                                        $category = match ($item->kategori) {
                                            'general' => 'bg-sky-100 text-sky-800',
                                            'policy' => 'bg-amber-100 text-amber-800',
                                            'announcement' => 'bg-indigo-100 text-indigo-800',
                                            'event' => 'bg-emerald-100 text-emerald-800',
                                            'important' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $category }}">
                                        {{ match ($item->kategori) {
                                            'umum' => 'General',
                                            'kebijakan' => 'Policy',
                                            'pengumuman' => 'Announcement',
                                            'event' => 'Event',
                                            'penting' => 'Important',
                                            default => 'Uncategorized',
                                        } }}
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
                                        {{ $item->target_role && $item->target_role != 'all' ? ucfirst($item->target_role) : 'All Roles' }}
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
                                    {{ $item->tanggal_terbit ? $item->tanggal_terbit->format('d F Y, H:i') : '-' }}
                                </td>
                                <td class="py-3 text-gray-700">
                                    {{ $item->tanggal_berlaku_hingga ? $item->tanggal_berlaku_hingga->format('d F Y') : '-' }}
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <a onclick="showDetail({{ $item->id }})"
                                            class="text-sm text-blue-600 transition cursor-pointer hover:text-blue-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
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
        class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">

        <div class="relative w-full max-w-2xl">

            <!-- MODAL -->
            <div class="overflow-hidden bg-white shadow-2xl rounded-3xl animate-fadeIn">

                <!-- HEADER -->
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">

                    <div>
                        <h3 class="text-xl font-bold text-blue-900">
                            Announcements Detail
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Complete Announcements Information
                        </p>
                    </div>

                    <button onclick="closeDetailModal()"
                        class="flex items-center justify-center w-10 h-10 transition rounded-xl hover:bg-gray-100">
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
            fetch(`/pengumuman/${id}`)
                .then(response => response.json())
                .then(data => {

                    const categoryClass = {
                        'umum': 'bg-blue-100 text-blue-700',
                        'kebijakan': 'bg-amber-100 text-amber-700',
                        'pengumuman': 'bg-violet-100 text-violet-700',
                        'event': 'bg-emerald-100 text-emerald-700',
                        'penting': 'bg-red-100 text-red-700'
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
                                        <div class="pb-4 border-b border-gray-100">
                                            <div class="flex flex-wrap items-start justify-between gap-3">
                                                <div>
                                                    <h4 class="text-2xl font-bold leading-snug text-gray-800">
                                                        ${data.judul}
                                                    </h4>

                                                    <div class="flex flex-wrap items-center gap-2 mt-3">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${badgeClass}">
                                                            ${ ({ 'umum': 'General', 'kebijakan': 'Policy', 'pengumuman': 'Announcement', 'event': 'Event', 'penting': 'Important' })[data.kategori] || 'Uncategorized' }
                                                        </span>

                                                        <span class="text-xs text-gray-400">
                                                            Published ${publishDate}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- CONTENT -->
                                        <div class="px-5 border border-gray-100 bg-gray-50 rounded-2xl">
                                            <p class="text-sm leading-7 text-gray-700 whitespace-pre-line">
                                                ${data.konten}
                                            </p>
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
    </script>
@endpush
