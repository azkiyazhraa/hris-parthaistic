@extends('layouts.app')
@section('content')
    <div class="container py-4 mx-auto space-y-4">

        <div class="flex items-center justify-between p-6 shadow-lg bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl">
            <div>
                <h1 class="mb-1 text-2xl font-bold text-blue-900">
                    Laporan Kehadiran Karyawan
                </h1>
                <p class="text-sm text-gray-700/80">
                    Ringkasan kehadiran, karyawan tanpa check-in/check-out, dan yang lupa checkout.
                </p>
            </div>
            <div class="hidden md:block">
                <a href="{{ route('admin.absensi.index') }}"
                    class="px-4 py-2 text-sm font-medium text-blue-900 bg-white/70 rounded-xl hover:bg-white transition">
                    &larr; Kembali
                </a>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="p-4 bg-white shadow rounded-2xl md:p-6">
            <form method="GET" action="{{ route('admin.absensi.laporan') }}">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="w-full sm:flex-1">
                        <label class="block mb-1 text-xs font-medium text-gray-500">Cari Karyawan</label>
                        <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Nama / email..."
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div class="w-full sm:w-44">
                        <label class="block mb-1 text-xs font-medium text-gray-500">Dari Tanggal</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div class="w-full sm:w-44">
                        <label class="block mb-1 text-xs font-medium text-gray-500">Sampai Tanggal</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit"
                            class="flex-1 sm:flex-none px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition">
                            Filter
                        </button>
                        <a href="{{ route('admin.absensi.laporan') }}"
                            class="flex-1 sm:flex-none px-5 py-2.5 text-sm text-center text-gray-500 border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- STATISTIK --}}
        <div class="overflow-hidden bg-white border border-gray-100 shadow-sm rounded-2xl">
            <div class="grid grid-cols-2 md:grid-cols-4">
                <div class="p-5 text-center border-b border-r border-gray-100 md:border-b-0">
                    <p class="mb-1 text-xs tracking-wide text-gray-400 uppercase">Total Data</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $total }}</p>
                </div>
                <div class="p-5 text-center border-b border-r border-gray-100 md:border-b-0">
                    <p class="mb-1 text-xs tracking-wide text-green-600 uppercase">Hadir Lengkap</p>
                    <p class="text-2xl font-semibold text-green-600">{{ $lengkap->count() }}</p>
                    <p class="text-xs text-gray-400">{{ $percentages['lengkap'] }}%</p>
                </div>
                <div class="p-5 text-center border-b border-r border-gray-100 md:border-b-0">
                    <p class="mb-1 text-xs tracking-wide text-yellow-600 uppercase">Lupa Check-Out</p>
                    <p class="text-2xl font-semibold text-yellow-600">{{ $lupaCheckout->count() }}</p>
                    <p class="text-xs text-gray-400">{{ $percentages['lupa_checkout'] }}%</p>
                </div>
                <div class="p-5 text-center border-b border-r border-gray-100 md:border-b-0 md:border-r-0">
                    <p class="mb-1 text-xs tracking-wide text-red-600 uppercase">Tanpa Check-In/Out</p>
                    <p class="text-2xl font-semibold text-red-600">{{ $tanpaCheckinCheckout->count() }}</p>
                    <p class="text-xs text-gray-400">{{ $percentages['tanpa_checkin'] }}%</p>
                </div>
            </div>
        </div>

        {{-- GRAFIK --}}
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="p-4 bg-white shadow rounded-2xl md:p-6">
                <h3 class="mb-3 text-base font-semibold text-gray-800">Persentase Kehadiran</h3>
                <div id="donutPersentase"></div>
            </div>
            <div class="p-4 bg-white shadow rounded-2xl md:p-6">
                <h3 class="mb-3 text-base font-semibold text-gray-800">Jumlah Kehadiran per Tanggal</h3>
                <div id="barTrend"></div>
            </div>
        </div>

        {{-- TABEL TANPA CHECK-IN & CHECK-OUT --}}
        <div class="p-4 bg-white shadow rounded-2xl md:p-6">
            <h3 class="mb-4 text-base font-semibold text-gray-800">
                Karyawan Tidak Check-In &amp; Tidak Check-Out
                <span class="ml-1 text-xs font-normal text-gray-400">({{ $tanpaCheckinCheckout->count() }} data · {{ $percentages['tanpa_checkin'] }}%)</span>
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-sm text-left">
                    <thead>
                        <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
                            <th class="pb-3 text-left">Nama</th>
                            <th class="pb-3 text-left">Tanggal</th>
                            <th class="pb-3 text-left">Check-In</th>
                            <th class="pb-3 text-left">Check-Out</th>
                            <th class="pb-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tanpaCheckinCheckout as $item)
                            <tr class="border-b border-gray-100 hover:bg-blue-50/40">
                                <td class="py-3 font-medium text-gray-800">{{ $item->karyawan->nama_lengkap ?? $item->nama_karyawan }}</td>
                                <td class="py-3">{{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}</td>
                                <td class="py-3">-</td>
                                <td class="py-3">-</td>
                                <td class="py-3">
                                    <span class="py-1 px-3 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                        {{ ucfirst($item->status_kehadiran) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-400">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL LUPA CHECKOUT --}}
        <div class="p-4 bg-white shadow rounded-2xl md:p-6">
            <h3 class="mb-4 text-base font-semibold text-gray-800">
                Karyawan Lupa Check-Out
                <span class="ml-1 text-xs font-normal text-gray-400">({{ $lupaCheckout->count() }} data · {{ $percentages['lupa_checkout'] }}%)</span>
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px] text-sm text-left">
                    <thead>
                        <tr class="text-xs font-medium tracking-wide text-gray-400 uppercase border-b">
                            <th class="pb-3 text-left">Nama</th>
                            <th class="pb-3 text-left">Tanggal</th>
                            <th class="pb-3 text-left">Check-In</th>
                            <th class="pb-3 text-left">Check-Out</th>
                            <th class="pb-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lupaCheckout as $item)
                            <tr class="border-b border-gray-100 hover:bg-blue-50/40">
                                <td class="py-3 font-medium text-gray-800">{{ $item->karyawan->nama_lengkap ?? $item->nama_karyawan }}</td>
                                <td class="py-3">{{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}</td>
                                <td class="py-3">{{ $item->jam_masuk ? \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') : '-' }}</td>
                                <td class="py-3">-</td>
                                <td class="py-3">
                                    <span class="py-1 px-3 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                        {{ ucfirst($item->status_kehadiran) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-400">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        var donutOptions = {
            chart: { type: 'donut', height: 280 },
            series: [{{ $lengkap->count() }}, {{ $lupaCheckout->count() }}, {{ $tanpaCheckinCheckout->count() }}],
            labels: ['Hadir Lengkap', 'Lupa Check-Out', 'Tanpa Check-In/Out'],
            colors: ['#10b981', '#f59e0b', '#ef4444'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function () { return '{{ $total }}'; }
                            }
                        }
                    }
                }
            }
        };
        new ApexCharts(document.querySelector('#donutPersentase'), donutOptions).render();

        var barOptions = {
            chart: { type: 'bar', height: 280, toolbar: { show: false } },
            series: [{
                name: 'Kehadiran',
                data: @json($trendValues)
            }],
            xaxis: { categories: @json($trendLabels) },
            colors: ['#2563eb'],
            plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
            dataLabels: { enabled: false }
        };
        new ApexCharts(document.querySelector('#barTrend'), barOptions).render();
    </script>
@endpush
