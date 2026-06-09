@extends('layouts.app')

@section('content')
<div class="container mx-auto py-4 space-y-4">
    <div class="bg-gradient-to-r from-blue-200 to-cyan-400 rounded-2xl p-6 shadow-lg flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-blue-900 mb-1">Detail Payroll</h1>
            <p class="text-gray-700/80 text-sm">{{ $penggajian->bulan_text }} {{ $penggajian->tahun }}</p>
        </div>
        <div class="hidden md:block">
            <img src="https://illustrations.popsy.co/blue/work-from-home.svg" alt="illustration" class="w-20">
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6" style="border: 2px solid #e0eaff; max-width: 800px; margin: 0 auto;">
        
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-sm font-semibold text-gray-700">Period</span>
                <span class="text-sm text-gray-500">{{ $penggajian->bulan_text }} {{ $penggajian->tahun }}</span>
            </div>
            <a href="{{ route('penggajian.download', $penggajian->id) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </a>
        </div>

        <div class="flex justify-between items-start mb-6">
            <div class="flex-1">
                <h3 class="text-base font-bold text-gray-900 mb-4">Employee's Info</h3>
                <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Name</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $penggajian->nama_karyawan }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Employee's ID</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $penggajian->karyawan->nip ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Department</p>
                        <p class="text-sm font-semibold text-gray-800">{{ ucfirst($penggajian->karyawan->role ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Position</p>
                        <p class="text-sm font-semibold text-gray-800">{{ ucfirst($penggajian->karyawan->role ?? '-') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Status</p>
                        {!! $penggajian->status_badge !!}
                    </div>
                </div>
            </div>
            <div class="ml-6 flex-shrink-0">
                <div class="w-20 h-20 rounded-full border-4 border-blue-900 overflow-hidden bg-gray-100">
                    @php
                        $fotoUrl = $penggajian->karyawan && $penggajian->karyawan->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($penggajian->karyawan->foto_profil) 
                            ? \Illuminate\Support\Facades\Storage::url($penggajian->karyawan->foto_profil) 
                            : 'https://ui-avatars.com/api/?background=0D8F81&color=fff&size=100&name=' . urlencode($penggajian->nama_karyawan);
                    @endphp
                    <img src="{{ $fotoUrl }}" class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 my-5"></div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-base font-bold text-gray-900 mb-3">Earnings</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Base Salary</span>
                        <span class="text-gray-800">Rp {{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <p class="text-gray-600 font-semibold mb-1">Allowance:</p>
                        <div class="pl-3 space-y-1">
                            <div class="flex justify-between"><span class="text-gray-500">Transport</span><span class="text-gray-700">Rp {{ number_format($penggajian->transport_allowance, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Meal</span><span class="text-gray-700">Rp {{ number_format($penggajian->meal_allowance, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Internet</span><span class="text-gray-700">Rp {{ number_format($penggajian->internet_allowance, 0, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Position</span><span class="text-gray-700">Rp {{ number_format($penggajian->position_allowance, 0, ',', '.') }}</span></div>
                        </div>
                    </div>
                    <div class="flex justify-between pt-1">
                        <span class="text-gray-600">Incentive</span>
                        <span class="text-gray-800">Rp {{ number_format($penggajian->incentive, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-gray-100 font-semibold text-gray-800">
                        <span>Total earnings</span>
                        <span>Rp {{ number_format($penggajian->total_earnings, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-base font-bold text-gray-900 mb-3">Deduction</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">Tax (PPh 21)</span><span class="text-gray-800">Rp {{ number_format($penggajian->tax, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">BPJS Health</span><span class="text-gray-800">Rp {{ number_format($penggajian->bpjs_kesehatan, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">BPJS Employment</span><span class="text-gray-800">Rp {{ number_format($penggajian->bpjs_ketenagakerjaan, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Late/Absent</span><span class="text-gray-800">Rp {{ number_format($penggajian->late_absent_deduction, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-600">Loan</span><span class="text-gray-800">Rp {{ number_format($penggajian->loan_deduction, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between pt-2 border-t border-gray-100 font-semibold text-gray-800">
                        <span>Total deduction</span>
                        <span>Rp {{ number_format($penggajian->total_deductions, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 my-4"></div>

        <div class="flex items-center gap-4 mb-6">
            <span class="text-base font-bold text-gray-900">Net Salary</span>
            <span class="text-xl font-bold text-blue-900">Rp {{ number_format($penggajian->net_salary, 0, ',', '.') }}</span>
        </div>

        @if($penggajian->catatan)
        <div class="mb-6 p-3 bg-yellow-50 border-l-4 border-yellow-500 rounded">
            <p class="text-sm font-semibold text-yellow-800">Catatan:</p>
            <p class="text-sm text-yellow-700">{{ $penggajian->catatan }}</p>
        </div>
        @endif

        <div class="flex gap-3 mt-6 pt-4 border-t">
            <a href="{{ route('penggajian.download', $penggajian->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download Payslip
            </a>
            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border-2 border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <a href="{{ route('penggajian.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg border-2 border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </div>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        .bg-white, .bg-white * { visibility: visible; }
        .bg-white { position: absolute; top: 0; left: 0; width: 100%; margin: 0; padding: 20px; box-shadow: none; }
        .bg-gradient-to-r, .flex.gap-3, button, .bg-blue-600, .border-2 { display: none !important; }
    }
</style>
@endsection