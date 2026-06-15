    @extends('layouts.app')
    @section('content')
        <div class="container mx-auto py-4 space-y-4">

            {{-- CARD HEADER --}}
            <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
                <!-- TEXT -->
                <div>
                    <h1 class="text-2xl font-bold text-blue-900 mb-1">
                        Employee
                    </h1>
                    <p class="text-gray-700/80 text-sm">
                        Employee management system to manage employee data, attendance, and performance.
                    </p>
                </div>

                <!-- IMAGE / ILLUSTRATION -->
                <div class="hidden md:block">
                    <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
                </div>
            </div>

            {{-- CARD SUMMARY --}}
            <div class="bg-white rounded-2xl shadow p-4 md:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x">
                    <!-- ITEM 1 -->
                    <div class="flex flex-col gap-1 px-4 py-2">
                        <p class="text-gray-400 text-sm">Period</p>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl md:text-2xl font-semibold text-blue-900">
                                This year
                            </h2>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-900" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>

                    <!-- ITEM 2 -->
                    <div class="flex flex-col gap-1 px-4 py-2">
                        <p class="text-gray-400 text-sm">Total Employees</p>
                        <h2 class="text-xl md:text-2xl font-semibold text-blue-900">
                            {{ $karyawans->whereIn('status', ['Full-time', 'Contract', 'Internship'])->count() }}
                        </h2>
                    </div>

                    <!-- ITEM 3 -->
                    <div class="flex flex-col gap-1 px-4 py-2">
                        <p class="text-gray-400 text-sm">Active Employees</p>
                        <h2 class="text-xl md:text-2xl font-semibold text-green-600">
                            {{ $karyawans->whereIn('status', ['Full-time', 'Contract', 'Internship'])->count() }}
                        </h2>
                    </div>

                    <!-- ITEM 4 -->
                    <div class="flex flex-col gap-1 px-4 py-2">
                        <p class="text-gray-400 text-sm">Resigned/Terminated</p>
                        <h2 class="text-xl md:text-2xl font-semibold text-red-600">
                            {{ $karyawans->whereIn('status', ['Resigned', 'Contract Ended', 'Internship Completed', 'Terminated'])->count() }}
                        </h2>
                    </div>
                </div>
            </div>


            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow p-4 md:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
                    <!-- LEFT -->
                    <div>
                        <button data-modal-target="employee-modal" data-modal-toggle="employee-modal"
                            class="inline-flex items-center gap-2 bg-blue-800 hover:bg-blue-900 text-white px-4 py-2 rounded-xl text-sm font-medium shadow-sm transition duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Employee
                        </button>
                    </div>

                    <!-- RIGHT -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
                        <!-- SEARCH -->
                        <div class="relative w-full sm:w-72">
                            <input type="text" id="searchEmployee" placeholder="Search employee..."
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 pl-10 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>

                        <!-- FILTER -->
                        <div class="relative w-full sm:w-52">
                            <select id="filterStatus"
                                class="w-full appearance-none border border-gray-300 bg-white rounded-xl px-4 py-2.5 pr-10 text-sm text-gray-700 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                                <option value="">All Status</option>
                                <option value="Full-time">Full-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Internship">Internship</option>
                                <option value="Resigned">Resigned</option>
                                <option value="Contract Ended">Contract Ended</option>
                                <option value="Internship Completed">Internship Completed</option>
                                <option value="Terminated">Terminated</option>
                            </select>
                        </div>
                    </div>
                </div>

                @include('admin.karyawan.create')


                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] md:min-w-full text-sm text-left">
                        <thead>
                            <tr class="text-gray-400 font-medium text-xs uppercase tracking-wide border-b">
                                <th class="text-left pb-3 whitespace-nowrap">Name</th>
                                <th class="text-left pb-3 whitespace-nowrap">Position</th>
                                <th class="text-left pb-3 whitespace-nowrap">Join Date</th>
                                <th class="text-left pb-3 whitespace-nowrap">Employment Type</th>
                                <th class="text-left pb-3 whitespace-nowrap">Working Days</th>
                                <th class="text-left pb-3 whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($karyawans as $item)
                                {{-- HANYA TAMPILKAN JIKA BUKAN HR --}}
                                @if($item->role !== 'hr')
                                <tr class="employee-row border-b border-gray-100 hover:bg-blue-50/40 transition"
                                    data-search="{{ strtolower($item->nama_lengkap . ' ' . $item->email . ' ' . $item->nip . ' ' . ($item->jabatan_display ?? '')) }}"
                                    data-status="{{ strtolower($item->status) }}">
                                    <td class="py-3">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $fotoUrl = $item->foto_profil
                                                    ? Storage::url($item->foto_profil)
                                                    : 'https://ui-avatars.com/api/?background=2563EB&color=fff&size=100&name=' .
                                                        urlencode($item->nama_lengkap);
                                            @endphp

                                            <div
                                                class="w-9 h-9 rounded-full bg-blue-100 border border-blue-200 overflow-hidden shrink-0">
                                                <img src="{{ $fotoUrl }}" alt="{{ $item->nama_lengkap }}"
                                                    class="w-full h-full object-cover" loading="lazy"
                                                    onerror="this.src='https://ui-avatars.com/api/?background=2563EB&color=fff&size=100&name={{ urlencode($item->nama_lengkap) }}'">
                                            </div>
                                            <div>
                                                <span class="font-medium text-gray-800">{{ $item->nama_lengkap }}</span><br>
                                                <span class="text-xs text-gray-500">{{ $item->nip }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="bg-violet-100 text-violet-800 py-1 px-3 rounded-full text-xs font-medium">
                                            {{ $item->jabatan_display ?? ucfirst($item->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-gray-700">{{ $item->tanggal_bergabung ? date('d F Y', strtotime($item->tanggal_bergabung)) : '-' }}</td>
                                    <td class="py-3">
                                        <span class="{{ $item->status_class }} py-1 px-3 rounded-full text-xs font-medium">
                                            {{ $item->status_label }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-gray-700 text-xs">
                                        @if(!$item->isActive() && $item->total_hari_kerja > 0)
                                            <span class="font-medium">{{ number_format($item->total_hari_kerja) }} days</span>
                                            <br>
                                            <span class="text-gray-400">{{ $item->total_hari_kerja_formatted }}</span>
                                        @elseif($item->isActive() && $item->tanggal_bergabung)
                                            @php
                                                $activeDays = \Carbon\Carbon::parse($item->tanggal_bergabung)->diffInDays(now()) + 1;
                                            @endphp
                                            <span class="font-medium">{{ number_format($activeDays) }} days</span>
                                            <br>
                                            <span class="text-green-500 text-xs">Active</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="flex items-center gap-2">
                                            <a class="text-blue-500 hover:text-blue-700 cursor-pointer"
                                                onclick="showEmployeeDetail({{ $item->id }})"
                                                data-modal-target="default-modal" data-modal-toggle="default-modal">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>

                                            <a data-modal-target="employee-modal-edit-{{ $item->id }}"
                                                data-modal-toggle="employee-modal-edit-{{ $item->id }}"
                                                class="text-yellow-500 hover:text-yellow-700 cursor-pointer">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </a>
                                            {{-- DELETE BUTTON DIHAPUS --}}
                                        </div>
                                    </td>
                                </tr>
                                @include('admin.karyawan.edit')
                                @endif
                            @endforeach
                            <tr id="emptySearchRow" style="display:none;">
                                <td colspan="6" class="text-center py-4 text-gray-400">
                                    Data Not Found
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div id="paginationContainer" class="mt-4 flex justify-end gap-1"></div>

                </div>
            </div>

        </div>

        @include('components.Employees.detail')
    @endsection


    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('searchEmployee');
                const statusFilter = document.getElementById('filterStatus');
                const allRows = Array.from(document.querySelectorAll('.employee-row'));
                const emptyRow = document.getElementById('emptySearchRow');
                const paginationContainer = document.getElementById('paginationContainer');

                const perPage = 10;
                let currentPage = 1;
                let filteredRows = [...allRows];

                function applyFilters() {
                    const keyword = searchInput.value.toLowerCase().trim();
                    const status = statusFilter.value.toLowerCase().trim();

                    filteredRows = allRows.filter(row => {
                        const searchText = row.dataset.search || '';
                        const rowStatus = row.dataset.status || '';

                        const matchKeyword = searchText.includes(keyword);
                        const matchStatus = !status || rowStatus === status;

                        return matchKeyword && matchStatus;
                    });

                    currentPage = 1;
                    renderTable();
                    renderPagination();
                }

                function renderTable() {
                    allRows.forEach(row => row.style.display = 'none');

                    const start = (currentPage - 1) * perPage;
                    const end = start + perPage;

                    filteredRows.slice(start, end).forEach(row => {
                        row.style.display = 'table-row';
                    });

                    if (emptyRow) {
                        emptyRow.style.display = filteredRows.length === 0 ? 'table-row' : 'none';
                    }
                }

                function renderPagination() {
                    paginationContainer.innerHTML = '';

                    const totalPages = Math.ceil(filteredRows.length / perPage);
                    if (totalPages <= 1) return;

                    for (let i = 1; i <= totalPages; i++) {
                        const btn = document.createElement('button');

                        btn.textContent = i;
                        btn.className = `
                            px-3 py-1 rounded text-sm border
                            ${i === currentPage
                                ? 'bg-blue-600 text-white border-blue-600'
                                : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'}
                        `;

                        btn.addEventListener('click', function() {
                            currentPage = i;
                            renderTable();
                            renderPagination();
                        });

                        paginationContainer.appendChild(btn);
                    }
                }

                searchInput.addEventListener('input', applyFilters);
                statusFilter.addEventListener('change', applyFilters);

                applyFilters();
            });
        </script>
    @endpush
