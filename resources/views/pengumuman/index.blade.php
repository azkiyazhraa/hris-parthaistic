@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">Announcements</h1>
                <p class="text-sm text-gray-700/80">Stay updated with the latest announcements.</p>
            </div>
            <div class="hidden md:block">
                <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
            </div>
        </div>

        <div class="p-6 bg-white shadow-lg rounded-2xl" style="border: 2px solid #e0eaff;">

            <!-- Search + filter -->
            <div class="flex flex-col sm:flex-row gap-3 mb-6">
                <input type="text" id="searchAnnouncement" placeholder="Search announcements..."
                    class="w-full sm:w-72 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <select id="filterCategory"
                    class="w-full sm:w-44 border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">All Categories</option>
                    <option value="umum">General</option>
                    <option value="kebijakan">Policy</option>
                    <option value="pengumuman">Announcement</option>
                    <option value="event">Event</option>
                    <option value="penting">Important</option>
                </select>
            </div>

            <!-- Cards grid -->
            <div id="cardsContainer" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse ($pengumuman as $item)
                    @php
                        $catClass = match ($item->kategori) {
                            'umum'       => 'bg-sky-100 text-sky-800',
                            'kebijakan'  => 'bg-amber-100 text-amber-800',
                            'pengumuman' => 'bg-indigo-100 text-indigo-800',
                            'event'      => 'bg-emerald-100 text-emerald-800',
                            'penting'    => 'bg-red-100 text-red-800',
                            default      => 'bg-gray-100 text-gray-800',
                        };
                        $catLabel = match ($item->kategori) {
                            'umum'       => 'General',
                            'kebijakan'  => 'Policy',
                            'pengumuman' => 'Announcement',
                            'event'      => 'Event',
                            'penting'    => 'Important',
                            default      => 'General',
                        };
                        $borderAccent = match ($item->kategori) {
                            'penting'    => 'border-l-red-400',
                            'kebijakan'  => 'border-l-amber-400',
                            'event'      => 'border-l-emerald-400',
                            'pengumuman' => 'border-l-indigo-400',
                            default      => 'border-l-blue-400',
                        };
                    @endphp
                    <div class="announcement-card border border-gray-100 border-l-4 {{ $borderAccent }} rounded-2xl p-5 hover:shadow-md transition cursor-pointer"
                        data-title="{{ strtolower($item->judul) }}"
                        data-category="{{ $item->kategori ?? '' }}"
                        onclick="showDetail({{ $item->id }})">

                        <div class="flex items-start justify-between gap-2 mb-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $catClass }}">
                                {{ $catLabel }}
                            </span>
                            <span class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $item->tanggal_terbit ? $item->tanggal_terbit->format('d M Y') : '-' }}
                            </span>
                        </div>

                        <h3 class="font-semibold text-gray-800 text-sm mb-2 line-clamp-2">{{ $item->judul }}</h3>

                        <p class="text-xs text-gray-500 leading-relaxed line-clamp-3">
                            {{ $item->konten }}
                        </p>

                        @if ($item->tanggal_berlaku_hingga)
                            <p class="text-xs text-gray-400 mt-3">Valid until {{ $item->tanggal_berlaku_hingga->format('d M Y') }}</p>
                        @endif

                        <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-400">
                                By {{ $item->pembuat->nama_lengkap ?? 'Admin' }}
                            </span>
                            <span class="text-xs text-blue-600 font-medium">Read more →</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 py-12 text-center text-gray-400">
                        <p class="text-sm">No announcements available.</p>
                    </div>
                @endforelse
            </div>

            <p id="emptySearch" class="hidden py-8 text-center text-sm text-gray-400">No announcements match your search.</p>
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
@endsection

@push('scripts')
    <script>
        function escapeHtml(str) {
            const d = document.createElement('div');
            d.appendChild(document.createTextNode(str || ''));
            return d.innerHTML;
        }

        function showDetail(id) {
            fetch(`/pengumuman/${id}`)
                .then(r => r.json())
                .then(data => {
                    const catClass = {
                        umum: 'bg-sky-100 text-sky-700', kebijakan: 'bg-amber-100 text-amber-700',
                        pengumuman: 'bg-indigo-100 text-indigo-700', event: 'bg-emerald-100 text-emerald-700',
                        penting: 'bg-red-100 text-red-700',
                    };
                    const catLabel = {
                        umum: 'General', kebijakan: 'Policy', pengumuman: 'Announcement',
                        event: 'Event', penting: 'Important',
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
                                        ${catLabel[data.kategori] || 'General'}
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

        // Search & filter
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchAnnouncement');
            const catFilter   = document.getElementById('filterCategory');
            const allCards    = Array.from(document.querySelectorAll('.announcement-card'));
            const emptyMsg    = document.getElementById('emptySearch');

            function applyFilters() {
                const keyword = searchInput.value.toLowerCase().trim();
                const cat     = catFilter.value;
                let visible   = 0;

                allCards.forEach(card => {
                    const matchTitle = !keyword || card.dataset.title.includes(keyword);
                    const matchCat   = !cat    || card.dataset.category === cat;
                    const show       = matchTitle && matchCat;
                    card.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                emptyMsg.classList.toggle('hidden', visible > 0);
            }

            searchInput.addEventListener('input', applyFilters);
            catFilter.addEventListener('change', applyFilters);
        });
    </script>
@endpush
